<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\SyncQueue;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SyncController extends Controller
{
    /**
     * Display the Sinkronisasi Data view or return JSON stats.
     */
    public function index(Request $request): View|JsonResponse
    {
        $queueItems = SyncQueue::whereIn('status', ['pending', 'failed'])
            ->orderBy('created_at', 'asc')
            ->get();

        $menungguCount = SyncQueue::where('status', 'pending')->count();
        $gagalCount = SyncQueue::where('status', 'failed')->count();
        $syncedTodayCount = SyncQueue::where('status', 'synced')
            ->whereDate('synced_at', Carbon::today())
            ->count();

        $lastSyncedRecord = SyncQueue::where('status', 'synced')
            ->whereNotNull('synced_at')
            ->orderBy('synced_at', 'desc')
            ->first();

        $terakhirSinkron = $lastSyncedRecord ? $lastSyncedRecord->synced_at->format('H.i') : '19.12';

        $totalMenunggu = $menungguCount + $gagalCount;
        $isOffline = session()->get('is_offline', true);

        $stats = [
            'menunggu' => $menungguCount,
            'gagal' => $gagalCount,
            'synced_today' => $syncedTodayCount,
            'terakhir_sinkron' => $terakhirSinkron,
            'total_menunggu' => $totalMenunggu,
            'is_offline' => $isOffline,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'stats' => $stats,
                'queue' => $queueItems,
            ]);
        }

        // Also fetch orders for sidebar counts
        $ordersCount = Order::where('status', 'menunggu')->count();

        return view('kasir.sinkron', compact('queueItems', 'stats', 'ordersCount'));
    }

    /**
     * Synchronize all pending and failed records.
     */
    public function syncNow(Request $request): JsonResponse
    {
        $items = SyncQueue::whereIn('status', ['pending', 'failed'])->get();

        $syncedCount = 0;
        foreach ($items as $item) {
            // If it has order payload and no order created yet, create real order
            if ($item->order_payload && ! $item->order_id) {
                $payload = $item->order_payload;
                $latestOrder = Order::orderBy('id', 'desc')->first();
                $nextNumber = 130;
                if ($latestOrder && preg_match('/DB-(\d+)/', $latestOrder->order_code, $matches)) {
                    $nextNumber = max((int) $matches[1] + 1, $nextNumber);
                }
                $orderCode = sprintf('DB-%04d', $nextNumber);

                $order = Order::create([
                    'order_code' => $orderCode,
                    'table_number' => $payload['table_number'] ?? 'Meja 01',
                    'customer_name' => $payload['customer_name'] ?? 'Offline Customer',
                    'source' => 'Offline POS',
                    'status' => 'menunggu',
                    'total_amount' => $item->total_amount,
                ]);

                if (! empty($payload['items'])) {
                    foreach ($payload['items'] as $it) {
                        $order->items()->create([
                            'menu_name' => $it['name'] ?? 'Menu Item',
                            'price' => $it['price'] ?? 15000,
                            'quantity' => $it['quantity'] ?? 1,
                            'subtotal' => ($it['price'] ?? 15000) * ($it['quantity'] ?? 1),
                        ]);
                    }
                }

                $item->order_id = $order->id;
            }

            $item->update([
                'status' => 'synced',
                'description' => 'Tersinkron otomatis ke server',
                'synced_at' => Carbon::now(),
                'attempts' => $item->attempts + 1,
                'last_attempt_at' => Carbon::now(),
            ]);

            $syncedCount++;
        }

        session()->put('is_offline', false);

        return response()->json([
            'success' => true,
            'message' => "Berhasil menyinkronkan {$syncedCount} data pesanan ke database server!",
            'synced_count' => $syncedCount,
        ]);
    }

    /**
     * Retry failed sync items only.
     */
    public function retryFailed(Request $request): JsonResponse
    {
        $failedItems = SyncQueue::where('status', 'failed')->get();

        $retriedCount = 0;
        foreach ($failedItems as $item) {
            $item->update([
                'status' => 'synced',
                'description' => 'Berhasil disinkronkan setelah percobaan ke-'.($item->attempts + 1),
                'attempts' => $item->attempts + 1,
                'last_attempt_at' => Carbon::now(),
                'synced_at' => Carbon::now(),
            ]);
            $retriedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Berhasil memproses {$retriedCount} data yang gagal sebelumnya.",
            'retried_count' => $retriedCount,
        ]);
    }

    /**
     * Toggle network connection state simulation (Offline <-> Online).
     */
    public function toggleConnection(Request $request): JsonResponse
    {
        $current = session()->get('is_offline', true);
        $new = ! $current;
        session()->put('is_offline', $new);

        return response()->json([
            'success' => true,
            'is_offline' => $new,
            'status_label' => $new ? 'Offline' : 'Online',
        ]);
    }

    /**
     * Add a sample offline transaction to test the queue.
     */
    public function addOfflineItem(Request $request): JsonResponse
    {
        $uuid = Str::uuid()->toString();
        $shortId = substr($uuid, 0, 4).'...'.substr($uuid, -4);
        $total = $request->input('total_amount', 25000);

        $item = SyncQueue::create([
            'local_id' => $uuid,
            'short_id' => $shortId,
            'total_amount' => $total,
            'description' => 'Menunggu koneksi',
            'status' => 'pending',
            'attempts' => 0,
            'created_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan offline baru berhasil disimpan di perangkat.',
            'item' => $item,
        ]);
    }
}
