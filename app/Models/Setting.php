<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'full_name',
        'address',
        'email',
        'iban',
        'watermark_text',
        'orders_enabled',
    ];

    protected $casts = [
        'orders_enabled' => 'boolean',
    ];

    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    public static function ordersEnabled(): bool
    {
        return (bool) static::current()->orders_enabled;
    }
}
