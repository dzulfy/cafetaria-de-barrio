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
use Illuminate\Validation\Rule;
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
        $request->merge([
            'table_number' => $this->normalizeTableNumber($request->input('table_number')),
        ]);

        $validated = $request->validate([
            'table_number' => ['required', 'string', 'max:10', 'unique:cafe_tables,table_number'],
            'name' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        $tableNumber = $validated['table_number'];
        $name = trim((string) ($validated['name'] ?? ''));

        CafeTable::create([
            'table_number' => $tableNumber,
            'name' => $name !== '' ? $name : 'Meja '.$tableNumber,
            'token' => CafeTable::generateUniqueToken(),
            'status' => $validated['status'] ?? 'aktif',
        ]);

        return redirect()->route('admin.tables.index')->with('success', 'Meja baru berhasil ditambahkan!');
    }

    /**
     * Update the specified table and optionally regenerate its QR token.
     */
    public function update(Request $request, CafeTable $table): RedirectResponse|JsonResponse
    {
        $request->merge([
            'table_number' => $this->normalizeTableNumber($request->input('table_number')),
        ]);

        $validated = $request->validate([
            'table_number' => [
                'required',
                'string',
                'max:10',
                Rule::unique('cafe_tables', 'table_number')->ignore($table->id),
            ],
            'name' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'regenerate_token' => ['nullable', 'boolean'],
        ]);

        $name = trim((string) ($validated['name'] ?? ''));

        $data = [
            'table_number' => $validated['table_number'],
            'name' => $name !== '' ? $name : 'Meja '.$validated['table_number'],
            'status' => $validated['status'],
        ];

        if ($request->boolean('regenerate_token')) {
            $data['token'] = CafeTable::generateUniqueToken();
        }

        $table->update($data);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$table->name} berhasil diperbarui!",
                'table' => $table,
            ]);
        }

        return redirect()->route('admin.tables.index')->with('success', "{$table->name} berhasil diperbarui!");
    }

    /**
     * Remove the specified table from storage.
     */
    public function destroy(Request $request, CafeTable $table): RedirectResponse|JsonResponse
    {
        $tableName = $table->name;
        $table->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$tableName} berhasil dihapus!",
            ]);
        }

        return redirect()->route('admin.tables.index')->with('success', "{$tableName} berhasil dihapus!");
    }

    /**
     * Normalize table numbers (e.g. '1' -> '01') consistently before validation and storage.
     */
    private function normalizeTableNumber(mixed $value): string
    {
        $trimmed = trim((string) $value);
        if ($trimmed === '') {
            return '';
        }

        return str_pad($trimmed, 2, '0', STR_PAD_LEFT);
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
        $url = $table->qr_url;
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
        $qrUrl = $table->qr_url;
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
