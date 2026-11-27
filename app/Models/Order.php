<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_code',
        'table_number',
        'customer_name',
        'source',
        'notes',
        'customer_notes',
        'subtotal',
        'total_price',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'midtrans_order_id',
        'snap_token',
        'snap_redirect_url',
        'midtrans_transaction_id',
        'midtrans_transaction_status',
        'amount_paid',
        'change_amount',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'total_price' => 'integer',
        'total_amount' => 'integer',
        'amount_paid' => 'integer',
        'change_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function getFormattedTotalAttribute(): string
    {
        $total = $this->total_amount ?? $this->total_price ?? 0;

        return 'Rp '.number_format((int) $total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp '.number_format((int) ($this->subtotal ?? 0), 0, ',', '.');
    }

    public function getFormattedPaidAttribute(): ?string
    {
        return $this->amount_paid ? 'Rp '.number_format((int) $this->amount_paid, 0, ',', '.') : null;
    }

    public function getFormattedChangeAttribute(): ?string
    {
        return $this->change_amount ? 'Rp '.number_format((int) $this->change_amount, 0, ',', '.') : null;
    }

    public static function generateOrderCode(): string
    {
        $prefix = 'DB-';
        do {
            $code = $prefix.str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('order_code', $code)->exists());

        return $code;
    }
}
