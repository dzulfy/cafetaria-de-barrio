<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ResepMenu extends Pivot
{
    public $timestamps = false;

    protected $table = 'resep_menu';

    protected $fillable = [
        'menu_id',
        'bahan_id',
        'jumlah_pakai',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_pakai' => 'decimal:2',
        ];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }
}
