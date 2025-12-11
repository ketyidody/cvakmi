<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'thumbnail_image',
        'full_name',
        'review_date',
        'source',
        'content',
        'rating',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'review_date' => 'date',
        'is_active' => 'boolean',
    ];
}
