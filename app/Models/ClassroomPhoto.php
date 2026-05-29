<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassroomPhoto extends Model
{
    protected $fillable = [
        'classroom_id',
        'title',
        'image_path',
        'medium_path',
        'thumbnail_path',
        'width',
        'height',
        'file_size',
        'display_order',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }
}
