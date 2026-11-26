<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class TableController extends Controller
{
    /**
     * Display A7 Meja & QR Code view.
     */
    public function index(Request $request): View
    {
        $tables = CafeTable::query()
            ->orderBy('table_number')
            ->paginate(8)
            ->withQueryString();

        $totalTables = CafeTable::count();

        return view('admin.tables.index', [
            'tables' => $tables,
            'totalTables' => $totalTables,
        ]);
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:10', 'unique:cafe_tables,table_number'],
            'name' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        $tableNumber = str_pad($validated['table_number'], 2, '0', STR_PAD_LEFT);

        CafeTable::create([
            'table_number' => $tableNumber,
            'name' => $validated['name'] ?: 'Meja '.$tableNumber,
            'token' => CafeTable::generateUniqueToken(),
            'status' => $validated['status'] ?? 'aktif',
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Meja baru berhasil ditambahkan!');
    }

    /**
     * Toggle table active status.
     */
    public function toggleStatus(CafeTable $table): JsonResponse|RedirectResponse
    {
        $table->update([
            'status' => $table->status === 'aktif' ? 'nonaktif' : 'aktif',
        ]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $table->status,
            ]);
        }

        return redirect()->back()->with('success', "Status {$table->name} diperbarui menjadi {$table->status}.");
    }

    /**
     * Download pure SVG QR Code for the table.
     */
    public function downloadQr(CafeTable $table): Response
    {
        $url = route('cart', ['table' => $table->table_number, 'token' => $table->token]);
        $svg = self::generateQrSvg($table->table_number, $table->name, $url);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="QR_'.$table->name.'.svg"',
        ]);
    }

    /**
     * Display printable QR Card for table tent / acrylic.
     */
    public function print(CafeTable $table): View
    {
        $qrUrl = route('cart', ['table' => $table->table_number, 'token' => $table->token]);
        $qrSvg = self::generateQrSvg($table->table_number, $table->name, $qrUrl);

        return view('admin.tables.print', [
            'table' => $table,
            'qrUrl' => $qrUrl,
            'qrSvg' => $qrSvg,
        ]);
    }

    /**
     * Generate clean, crisp SVG QR Code (viewBox-based, no overlapping background).
     */
    public static function generateQrSvg(string $tableNumber, string $name, string $url): string
    {
        return QrCodeService::generateSvg($url, 200, '#1B2B24');
    }
}
