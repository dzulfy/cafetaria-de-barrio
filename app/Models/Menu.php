<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menu';

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'harga',
        'foto',
        'status_tersedia',
    ];

    protected function casts(): array
    {
        return [
            'harga' => 'decimal:2',
            'status_tersedia' => 'boolean',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'menu_id');
    }

    public function bahan(): BelongsToMany
    {
        return $this->belongsToMany(Bahan::class, 'resep_menu', 'menu_id', 'bahan_id')
            ->withPivot('jumlah_pakai');
    }
}
