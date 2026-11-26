<?php

declare(strict_types=1);

namespace App\Services;

class QrCodeService
{
    /**
     * Galois Field 256 tables
     *
     * @var array<int, int>
     */
    private static array $exp = [];

    /**
     * @var array<int, int>
     */
    private static array $log = [];

    private static bool $initialized = false;

    private static function initGf(): void
    {
        if (self::$initialized) {
            return;
        }

        $val = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$exp[$i] = $val;
            self::$log[$val] = $i;
            $val <<= 1;
            if ($val & 256) {
                $val ^= 0x11D;
            }
        }
        for ($i = 255; $i < 512; $i++) {
            self::$exp[$i] = self::$exp[$i - 255];
        }

        self::$initialized = true;
    }

    private static function gMul(int $x, int $y): int
    {
        if ($x === 0 || $y === 0) {
            return 0;
        }

        return self::$exp[self::$log[$x] + self::$log[$y]];
    }

    /**
     * Generate Reed-Solomon error correction codewords
     *
     * @param  array<int, int>  $data
     * @return array<int, int>
     */
    private static function rsEncode(array $data, int $ecCount): array
    {
        self::initGf();

        // Generator polynomial
        $gen = [1];
        for ($i = 0; $i < $ecCount; $i++) {
            $next = array_fill(0, count($gen) + 1, 0);
            $factor = self::$exp[$i];
            for ($j = 0; $j < count($gen); $j++) {
                $next[$j] ^= $gen[$j];
                $next[$j + 1] ^= self::gMul($gen[$j], $factor);
            }
            $gen = $next;
        }

        $msg = array_merge($data, array_fill(0, $ecCount, 0));
        for ($i = 0; $i < count($data); $i++) {
            $coef = $msg[$i];
            if ($coef !== 0) {
                for ($j = 0; $j < count($gen); $j++) {
                    $msg[$i + $j] ^= self::gMul($gen[$j], $coef);
                }
            }
        }

        return array_slice($msg, count($data));
    }

    /**
     * Generate an SVG string representing the QR code.
     */
    public static function generateSvg(string $text, int $size = 200, string $color = '#1B2B24'): string
    {
        $matrix = self::encodeToMatrix($text);
        $count = count($matrix);
        $quietZone = 4;
        $totalSize = $count + ($quietZone * 2);

        $rects = [];
        for ($r = 0; $r < $count; $r++) {
            for ($c = 0; $c < $count; $c++) {
                if ($matrix[$r][$c]) {
                    $x = $c + $quietZone;
                    $y = $r + $quietZone;
                    $rects[] = sprintf('<rect x="%d" y="%d" width="1" height="1" />', $x, $y);
                }
            }
        }

        $rectContent = implode('', $rects);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$totalSize} {$totalSize}" class="w-full h-full" style="display:block;max-width:100%;height:auto;" shape-rendering="crispEdges">
  <rect width="100%" height="100%" fill="transparent" />
  <g fill="{$color}">
    {$rectContent}
  </g>
</svg>
SVG;
    }

    /**
     * Encode text into a boolean QR matrix
     *
     * @return array<int, array<int, bool>>
     */
    public static function encodeToMatrix(string $text): array
    {
        self::initGf();

        $bytes = array_values(unpack('C*', $text) ?: []);
        $len = count($bytes);

        // Version capacity table (Version => [TotalDataBytes, EcBytes, Dimension, AlignPos])
        // Using Error Correction Level L
        $versions = [
            1 => ['data' => 19, 'ec' => 7, 'dim' => 21, 'align' => []],
            2 => ['data' => 34, 'ec' => 10, 'dim' => 25, 'align' => [6, 18]],
            3 => ['data' => 55, 'ec' => 15, 'dim' => 29, 'align' => [6, 22]],
            4 => ['data' => 80, 'ec' => 20, 'dim' => 33, 'align' => [6, 26]],
            5 => ['data' => 108, 'ec' => 26, 'dim' => 37, 'align' => [6, 30]],
        ];

        $selectedVersion = 1;
        foreach ($versions as $ver => $info) {
            if ($len + 2 <= $info['data']) { // 4 bits mode + 8 bits length + data
                $selectedVersion = $ver;
                break;
            }
            $selectedVersion = $ver;
        }

        $vInfo = $versions[$selectedVersion];
        $totalDataBytes = $vInfo['data'];
        $ecBytes = $vInfo['ec'];
        $dim = $vInfo['dim'];

        // Bit stream construction (Mode 0100 = 8-bit byte mode)
        $bits = [0, 1, 0, 0];
        // 8-bit character count indicator (for Version 1-9)
        for ($i = 7; $i >= 0; $i--) {
            $bits[] = ($len >> $i) & 1;
        }
        // Data bytes
        foreach ($bytes as $byte) {
            for ($i = 7; $i >= 0; $i--) {
                $bits[] = ($byte >> $i) & 1;
            }
        }

        // Terminator (up to 4 zeroes)
        $maxBits = $totalDataBytes * 8;
        $termLen = min(4, $maxBits - count($bits));
        for ($i = 0; $i < $termLen; $i++) {
            $bits[] = 0;
        }

        // Pad to byte boundary
        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }

        // Pad bytes (0xEC, 0x11)
        $padBytes = [0xEC, 0x11];
        $padIdx = 0;
        while (count($bits) < $maxBits) {
            $pByte = $padBytes[$padIdx % 2];
            for ($i = 7; $i >= 0; $i--) {
                $bits[] = ($pByte >> $i) & 1;
            }
            $padIdx++;
        }

        // Convert bits to data codewords
        $dataCodewords = [];
        for ($i = 0; $i < count($bits); $i += 8) {
            $val = 0;
            for ($j = 0; $j < 8; $j++) {
                $val = ($val << 1) | $bits[$i + $j];
            }
            $dataCodewords[] = $val;
        }

        // Generate Error Correction Codewords
        $ecCodewords = self::rsEncode($dataCodewords, $ecBytes);
        $finalSequence = array_merge($dataCodewords, $ecCodewords);

        // Convert final sequence to bit array
        $allBits = [];
        foreach ($finalSequence as $byte) {
            for ($i = 7; $i >= 0; $i--) {
                $allBits[] = ($byte >> $i) & 1;
            }
        }

        // Initialize Matrix and Reserved modules mask
        $matrix = array_fill(0, $dim, array_fill(0, $dim, false));
        $reserved = array_fill(0, $dim, array_fill(0, $dim, false));

        // 1. Finder patterns (Top-left, Top-right, Bottom-left)
        self::placeFinder($matrix, $reserved, 0, 0);
        self::placeFinder($matrix, $reserved, $dim - 7, 0);
        self::placeFinder($matrix, $reserved, 0, $dim - 7);

        // Separators around finders
        self::placeSeparators($reserved, $dim);

        // 2. Alignment patterns for Version >= 2
        if (! empty($vInfo['align'])) {
            $coords = $vInfo['align'];
            foreach ($coords as $r) {
                foreach ($coords as $c) {
                    if (! $reserved[$r][$c]) {
                        self::placeAlignment($matrix, $reserved, $r, $c);
                    }
                }
            }
        }

        // 3. Timing patterns (Row 6 and Column 6)
        for ($i = 8; $i < $dim - 8; $i++) {
            if (! $reserved[6][$i]) {
                $matrix[6][$i] = ($i % 2 === 0);
                $reserved[6][$i] = true;
            }
            if (! $reserved[$i][6]) {
                $matrix[$i][6] = ($i % 2 === 0);
                $reserved[$i][6] = true;
            }
        }

        // Dark module
        $matrix[$dim - 8][8] = true;
        $reserved[$dim - 8][8] = true;

        // Reserve format information areas
        for ($i = 0; $i < 9; $i++) {
            $reserved[8][$i] = true;
            $reserved[$i][8] = true;
        }
        for ($i = 0; $i < 8; $i++) {
            $reserved[8][$dim - 1 - $i] = true;
            $reserved[$dim - 1 - $i][8] = true;
        }

        // 4. Place Data bits (up-down snake traversal)
        $bitIdx = 0;
        $totalBitLen = count($allBits);
        $col = $dim - 1;
        $dir = -1; // -1 = up, 1 = down

        while ($col > 0) {
            if ($col === 6) {
                $col--; // skip vertical timing line
            }

            $rowStart = ($dir === -1) ? ($dim - 1) : 0;
            $rowEnd = ($dir === -1) ? -1 : $dim;

            for ($row = $rowStart; $row !== $rowEnd; $row += $dir) {
                for ($cOffset = 0; $cOffset < 2; $cOffset++) {
                    $c = $col - $cOffset;
                    if (! $reserved[$row][$c]) {
                        $bit = ($bitIdx < $totalBitLen) ? $allBits[$bitIdx] : 0;
                        $bitIdx++;
                        // Apply Mask Pattern 0: (row + col) % 2 === 0
                        $mask = (($row + $c) % 2 === 0);
                        $matrix[$row][$c] = ($bit === 1) ^ $mask;
                    }
                }
            }

            $dir = -$dir;
            $col -= 2;
        }

        // 5. Place Format Info (Mask 0, Level L = 01 000 = 01000 => format bits: 111011111000100)
        // Pre-computed BCH code for Level L + Mask 0 is 0x77C4 (binary: 111011111000100)
        $formatBits = [1, 1, 1, 0, 1, 1, 1, 1, 1, 0, 0, 0, 1, 0, 0];
        self::placeFormatInfo($matrix, $dim, $formatBits);

        return $matrix;
    }

    /**
     * @param  array<int, array<int, bool>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     */
    private static function placeFinder(array &$matrix, array &$reserved, int $r, int $c): void
    {
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $isBlack = ($i === 0 || $i === 6 || $j === 0 || $j === 6 || ($i >= 2 && $i <= 4 && $j >= 2 && $j <= 4));
                $matrix[$r + $i][$c + $j] = $isBlack;
                $reserved[$r + $i][$c + $j] = true;
            }
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $reserved
     */
    private static function placeSeparators(array &$reserved, int $dim): void
    {
        for ($i = 0; $i < 8; $i++) {
            if ($i < $dim) {
                $reserved[7][$i] = true;
                $reserved[$i][7] = true;
                $reserved[7][$dim - 1 - $i] = true;
                $reserved[$i][$dim - 8] = true;
                $reserved[$dim - 8][$i] = true;
                $reserved[$dim - 1 - $i][7] = true;
            }
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $matrix
     * @param  array<int, array<int, bool>>  $reserved
     */
    private static function placeAlignment(array &$matrix, array &$reserved, int $r, int $c): void
    {
        for ($i = -2; $i <= 2; $i++) {
            for ($j = -2; $j <= 2; $j++) {
                $isBlack = (abs($i) === 2 || abs($j) === 2 || ($i === 0 && $j === 0));
                $matrix[$r + $i][$c + $j] = $isBlack;
                $reserved[$r + $i][$c + $j] = true;
            }
        }
    }

    /**
     * @param  array<int, array<int, bool>>  $matrix
     * @param  array<int, int>  $bits
     */
    private static function placeFormatInfo(array &$matrix, int $dim, array $bits): void
    {
        // Top-left finder
        $coordsTopLeft = [
            [8, 0], [8, 1], [8, 2], [8, 3], [8, 4], [8, 5],
            [8, 7], [8, 8], [7, 8], [5, 8], [4, 8], [3, 8], [2, 8], [1, 8], [0, 8],
        ];
        for ($i = 0; $i < 15; $i++) {
            [$r, $c] = $coordsTopLeft[$i];
            $matrix[$r][$c] = ($bits[$i] === 1);
        }

        // Top-right and bottom-left finders
        $coordsSplit = [
            [$dim - 1, 8], [$dim - 2, 8], [$dim - 3, 8], [$dim - 4, 8],
            [$dim - 5, 8], [$dim - 6, 8], [$dim - 7, 8],
            [8, $dim - 8], [8, $dim - 7], [8, $dim - 6], [8, $dim - 5],
            [8, $dim - 4], [8, $dim - 3], [8, $dim - 2], [8, $dim - 1],
        ];
        for ($i = 0; $i < 15; $i++) {
            [$r, $c] = $coordsSplit[$i];
            $matrix[$r][$c] = ($bits[$i] === 1);
        }
    }
}
