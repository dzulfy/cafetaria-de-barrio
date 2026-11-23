<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_code',
        'table_number',
        'customer_name',
        'source',
        'status',
        'customer_notes',
        'total_amount',
        'payment_method',
        'amount_paid',
        'change_amount',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'integer',
        'amount_paid' => 'integer',
        'change_amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedPaidAttribute(): ?string
    {
        return $this->amount_paid ? 'Rp '.number_format($this->amount_paid, 0, ',', '.') : null;
    }

    public function getFormattedChangeAttribute(): ?string
    {
        return $this->change_amount ? 'Rp '.number_format($this->change_amount, 0, ',', '.') : null;
    }
}
