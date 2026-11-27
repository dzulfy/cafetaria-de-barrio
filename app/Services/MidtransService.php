<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MidtransService
{
    public function isProduction(): bool
    {
        return (bool) config('services.midtrans.is_production', false);
    }

    public function serverKey(): string
    {
        return (string) config('services.midtrans.server_key', '');
    }

    public function clientKey(): string
    {
        return (string) config('services.midtrans.client_key', '');
    }

    public function signatureKey(): string
    {
        $configured = (string) config('services.midtrans.signature_key', '');

        return $configured !== '' ? $configured : $this->serverKey();
    }

    public function isConfigured(): bool
    {
        return $this->serverKey() !== '' && $this->clientKey() !== '';
    }

    public function snapJsUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    public function snapApiUrl(): string
    {
        return $this->isProduction()
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function coreApiUrl(): string
    {
        return $this->isProduction()
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Create or reuse a Midtrans Snap transaction for the given order.
     *
     * @return array{token: string, redirect_url: string|null, midtrans_order_id: string}
     */
    public function createSnapTransaction(Order $order, bool $forceNew = false): array
    {
        if (
            ! $forceNew
            && ! empty($order->snap_token)
            && ! in_array($order->payment_status, ['failed', 'expired'], true)
        ) {
            return [
                'token' => (string) $order->snap_token,
                'redirect_url' => $order->snap_redirect_url,
                'midtrans_order_id' => $order->midtrans_order_id ?? $order->order_code,
            ];
        }

        if (! $this->isConfigured()) {
            throw new RuntimeException('Konfigurasi Midtrans Server Key / Client Key belum lengkap.');
        }

        $order->loadMissing('items');

        $itemDetails = [];
        $computedGross = 0;

        foreach ($order->items as $item) {
            $price = (int) $item->price;
            $qty = (int) $item->quantity;

            if ($price <= 0 || $qty <= 0) {
                continue;
            }

            $itemDetails[] = [
                'id' => (string) ($item->menu_id ?? $item->menu_item_id ?? $item->id),
                'price' => $price,
                'quantity' => $qty,
                'name' => Str::limit((string) $item->menu_name, 50, ''),
            ];

            $computedGross += $price * $qty;
        }

        $grossAmount = $computedGross > 0
            ? $computedGross
            : (int) ($order->total_amount ?: $order->total_price ?: 0);

        if ($grossAmount <= 0) {
            throw new RuntimeException('Total tagihan pesanan harus lebih dari Rp 0 untuk membuat transaksi Midtrans.');
        }

        $needsFreshOrderId = $forceNew
            || in_array($order->payment_status, ['failed', 'expired'], true)
            || (! empty($order->midtrans_order_id) && $order->midtrans_order_id !== $order->order_code);

        $midtransOrderId = $needsFreshOrderId
            ? $order->order_code.'-'.now()->timestamp
            : $order->order_code;

        $payload = [
            'transaction_details' => [
                'order_id' => $midtransOrderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name ?: 'Pelanggan Meja '.$order->table_number,
            ],
            'callbacks' => [
                'finish' => route('orders.status', ['order_code' => $order->order_code]),
            ],
        ];

        if ($itemDetails !== []) {
            $payload['item_details'] = $itemDetails;
        }

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->asJson()
            ->timeout(15)
            ->post($this->snapApiUrl(), $payload);

        if (! $response->successful()) {
            $errorBody = $response->json();
            $errorMessage = is_array($errorBody) && isset($errorBody['error_messages'])
                ? implode(', ', (array) $errorBody['error_messages'])
                : ($response->body() ?: 'HTTP '.$response->status());

            throw new RuntimeException('Gagal membuat transaksi Midtrans: '.$errorMessage);
        }

        $data = $response->json();
        $token = (string) ($data['token'] ?? '');
        $redirectUrl = $data['redirect_url'] ?? null;

        if ($token === '') {
            throw new RuntimeException('Midtrans tidak mengembalikan Snap token.');
        }

        $updates = [
            'midtrans_order_id' => $midtransOrderId,
            'snap_token' => $token,
            'snap_redirect_url' => $redirectUrl,
        ];

        if (in_array($order->payment_status, ['failed', 'expired', null, ''], true)) {
            $updates['payment_status'] = 'unpaid';
        }

        $order->update($updates);

        return [
            'token' => $token,
            'redirect_url' => $redirectUrl,
            'midtrans_order_id' => $midtransOrderId,
        ];
    }

    /**
     * Fetch transaction status directly from Midtrans Core API.
     *
     * @return array<string, mixed>
     */
    public function fetchTransactionStatus(string $midtransOrderId): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Konfigurasi Midtrans Server Key / Client Key belum lengkap.');
        }

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->timeout(15)
            ->get("{$this->coreApiUrl()}/{$midtransOrderId}/status");

        if (! $response->successful()) {
            throw new RuntimeException('Status transaksi Midtrans belum tersedia (HTTP '.$response->status().').', $response->status());
        }

        return (array) $response->json();
    }

    /**
     * Verify SHA-512 webhook signature from Midtrans.
     *
     * @param  array<string, mixed>  $payload
     */
    public function verifySignature(array $payload): bool
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || $statusCode === '' || $grossAmount === '' || $signatureKey === '') {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$this->signatureKey());

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Map Midtrans transaction status payload onto the local Order record.
     *
     * @param  array<string, mixed>  $payload
     */
    public function applyTransactionStatus(Order $order, array $payload): Order
    {
        if ($order->isPaid()) {
            return $order->fresh(['items.menuItem']);
        }

        $txStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');
        $paymentType = (string) ($payload['payment_type'] ?? '');
        $txId = (string) ($payload['transaction_id'] ?? '');
        $grossAmount = $payload['gross_amount'] ?? null;

        if ($txStatus === 'settlement' || ($txStatus === 'capture' && ($fraudStatus === '' || $fraudStatus === 'accept'))) {
            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $paymentType !== '' ? $paymentType : 'midtrans',
                'midtrans_transaction_id' => $txId !== '' ? $txId : $order->midtrans_transaction_id,
                'midtrans_transaction_status' => $txStatus,
                'amount_paid' => (int) round((float) ($grossAmount ?? ($order->total_amount ?: $order->total_price ?: 0))),
                'change_amount' => 0,
                'paid_at' => $order->paid_at ?? now(),
            ]);
        } elseif ($txStatus === 'pending' || ($txStatus === 'capture' && $fraudStatus === 'challenge')) {
            $order->update([
                'payment_status' => 'pending',
                'payment_method' => $paymentType !== '' ? $paymentType : $order->payment_method,
                'midtrans_transaction_id' => $txId !== '' ? $txId : $order->midtrans_transaction_id,
                'midtrans_transaction_status' => $txStatus,
            ]);
        } elseif ($txStatus === 'expire') {
            $order->update([
                'payment_status' => 'expired',
                'midtrans_transaction_status' => 'expire',
                'midtrans_transaction_id' => $txId !== '' ? $txId : $order->midtrans_transaction_id,
            ]);
        } elseif (in_array($txStatus, ['deny', 'cancel', 'failure'], true)) {
            $order->update([
                'payment_status' => 'failed',
                'midtrans_transaction_status' => $txStatus,
                'midtrans_transaction_id' => $txId !== '' ? $txId : $order->midtrans_transaction_id,
            ]);
        }

        return $order->fresh(['items.menuItem']);
    }
}
