<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bahan extends Model
{
    use HasFactory;

    protected $table = 'bahan';

    protected $fillable = [
        'nama',
        'satuan',
        'stok',
        'stok_minimum',
    ];

    protected function casts(): array
    {
        return [
            'stok' => 'decimal:2',
            'stok_minimum' => 'decimal:2',
        ];
    }

    public function menu(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'resep_menu', 'bahan_id', 'menu_id')
            ->withPivot('jumlah_pakai');
    }

    public function mutasiStok(): HasMany
    {
        return $this->hasMany(MutasiStok::class, 'bahan_id');
    }
}
