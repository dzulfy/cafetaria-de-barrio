<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CafeTableFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CafeTable extends Model
{
    /** @use HasFactory<CafeTableFactory> */
    use HasFactory;

    protected $fillable = [
        'table_number',
        'name',
        'token',
        'status',
    ];

    public static function generateUniqueToken(): string
    {
        do {
            $token = Str::random(10);
        } while (static::where('token', $token)->exists());

        return $token;
    }

    protected function qrUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => route('landing', ['table' => $this->table_number, 'token' => $this->token])
        );
    }
}
