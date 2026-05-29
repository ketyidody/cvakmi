<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintOption extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
