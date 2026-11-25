<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MutasiStok extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'mutasi_stok';

    protected $fillable = [
        'bahan_id',
        'pengguna_id',
        'jenis',
        'jumlah',
        'catatan',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function bahan(): BelongsTo
    {
        return $this->belongsTo(Bahan::class, 'bahan_id');
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }
}
