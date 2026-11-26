<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Menu
 *
 * @property int $id
 * @property int $category_id
 * @property string $name
 * @property string|null $description
 * @property int $price
 * @property string|null $image
 * @property bool $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Category $category
 */
class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'icon',
        'category',
        'is_available',
        'kategori_id',
        'nama',
        'deskripsi',
        'harga',
        'foto',
        'status_tersedia',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_available' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
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

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'menu_id');
    }

    public function getFormattedPriceAttribute(): string
    {
        $amount = $this->price ?? $this->attributes['harga'] ?? 0;
        return 'Rp ' . number_format((int) $amount, 0, ',', '.');
    }

    public function getNamaAttribute(): ?string
    {
        return $this->attributes['nama'] ?? $this->attributes['name'] ?? null;
    }

    public function getDeskripsiAttribute(): ?string
    {
        return $this->attributes['deskripsi'] ?? $this->attributes['description'] ?? null;
    }

    public function getHargaAttribute(): mixed
    {
        return $this->attributes['harga'] ?? $this->attributes['price'] ?? 0;
    }

    public function getStatusTersediaAttribute(): bool
    {
        return (bool) ($this->attributes['status_tersedia'] ?? $this->attributes['is_available'] ?? true);
    }
}
