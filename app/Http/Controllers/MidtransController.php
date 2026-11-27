<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\GuestSessionService;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MidtransController extends Controller
{
    public function __construct(
        public MidtransService $midtrans,
        public GuestSessionService $guestSessions,
    ) {}

    /**
     * Handle incoming Midtrans payment notification webhook.
     */
    public function notification(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->midtrans->verifySignature($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Signature tidak valid.',
            ], 403);
        }

        $midtransOrderId = (string) ($payload['order_id'] ?? '');
        $order = $this->findOrderByMidtransId($midtransOrderId);

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        $order = $this->midtrans->applyTransactionStatus($order, $payload);

        return response()->json([
            'success' => true,
            'order_code' => $order->order_code,
            'payment_status' => $order->payment_status,
        ]);
    }

    /**
     * Create or return a Midtrans Snap token for an order.
     */
    public function token(Request $request, string $order_code): JsonResponse
    {
        $order = Order::with('items')->where('order_code', $order_code)->firstOrFail();
        $this->guestSessions->authorizeOrder($request, $order);

        if ($order->isPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan ini sudah dibayar.',
            ], 422);
        }

        if ($order->status === 'dibatalkan') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan sudah dibatalkan.',
            ], 422);
        }

        try {
            $snap = $this->midtrans->createSnapTransaction($order, $request->boolean('force_new'));
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 502);
        }

        return response()->json([
            'success' => true,
            'snap_token' => $snap['token'],
            'redirect_url' => $snap['redirect_url'],
            'client_key' => $this->midtrans->clientKey(),
            'order' => $order->fresh(['items.menuItem']),
        ]);
    }

    /**
     * Actively query Midtrans Core API to verify payment status.
     */
    public function verify(Request $request, string $order_code): JsonResponse
    {
        $order = Order::with('items')->where('order_code', $order_code)->firstOrFail();
        $this->guestSessions->authorizeOrder($request, $order);

        if ($order->isPaid()) {
            return response()->json([
                'success' => true,
                'paid' => true,
                'payment_status' => 'paid',
                'message' => 'Pembayaran sudah lunas.',
                'order' => $order->fresh(['items.menuItem']),
            ]);
        }

        $lookupId = $order->midtrans_order_id ?: $order->order_code;

        try {
            $statusPayload = $this->midtrans->fetchTransactionStatus($lookupId);
        } catch (\Throwable $e) {
            if ($e->getCode() === 404) {
                return response()->json([
                    'success' => true,
                    'paid' => false,
                    'payment_status' => $order->payment_status,
                    'message' => 'Pembayaran QRIS / Transfer belum diselesaikan.',
                    'order' => $order,
                ]);
            }

            return response()->json([
                'success' => false,
                'paid' => false,
                'message' => $e->getMessage(),
            ], 502);
        }

        $order = $this->midtrans->applyTransactionStatus($order, $statusPayload);

        return response()->json([
            'success' => true,
            'paid' => $order->isPaid(),
            'payment_status' => $order->payment_status,
            'midtrans_transaction_status' => $order->midtrans_transaction_status,
            'message' => $order->isPaid()
                ? 'Pembayaran QRIS / Transfer berhasil dikonfirmasi!'
                : 'Status pembayaran diperbarui.',
            'order' => $order,
        ]);
    }

    private function findOrderByMidtransId(string $midtransOrderId): ?Order
    {
        if ($midtransOrderId === '') {
            return null;
        }

        $order = Order::where('midtrans_order_id', $midtransOrderId)
            ->orWhere('order_code', $midtransOrderId)
            ->first();

        if ($order) {
            return $order;
        }

        $baseOrderCode = preg_replace('/-\d+$/', '', $midtransOrderId);
        if ($baseOrderCode && $baseOrderCode !== $midtransOrderId) {
            return Order::where('order_code', $baseOrderCode)->first();
        }

        return null;
    }
}
