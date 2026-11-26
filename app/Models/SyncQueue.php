<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'local_id',
        'short_id',
        'order_id',
        'order_payload',
        'total_amount',
        'description',
        'status',
        'attempts',
        'last_attempt_at',
        'synced_at',
    ];

    protected $casts = [
        'order_payload' => 'array',
        'total_amount' => 'integer',
        'attempts' => 'integer',
        'last_attempt_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp '.number_format($this->total_amount, 0, ',', '.');
    }

    public function getDisplayIdAttribute(): string
    {
        return $this->short_id ?? (strlen($this->local_id) > 10 ? substr($this->local_id, 0, 4).'...'.substr($this->local_id, -4) : $this->local_id);
    }
}
