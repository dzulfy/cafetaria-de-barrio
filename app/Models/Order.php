<?php

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use HasFactory;

    protected $fillable = [
        'order_code',
        'table_number',
        'customer_name',
        'notes',
        'subtotal',
        'total_price',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'total_price' => 'integer',
    ];

    protected function formattedTotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => 'Rp '.number_format($this->total_price, 0, ',', '.')
        );
    }

    protected function formattedSubtotal(): Attribute
    {
        return Attribute::make(
            get: fn (): string => 'Rp '.number_format($this->subtotal, 0, ',', '.')
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
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
