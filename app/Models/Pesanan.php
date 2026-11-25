<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanan';

    protected $fillable = [
        'kode_pesanan',
        'meja_id',
        'pengguna_id',
        'sumber',
        'status',
        'nama_pelanggan',
        'total_harga',
        'catatan',
        'uuid_klien',
        'waktu_pesan',
    ];

    protected function casts(): array
    {
        return [
            'total_harga' => 'decimal:2',
            'waktu_pesan' => 'datetime',
        ];
    }

    public function meja(): BelongsTo
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function detailPesanan(): HasMany
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id');
    }

    public function pembayaran(): HasOne
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id');
    }
}
