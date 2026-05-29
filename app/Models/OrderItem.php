<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'classroom_photo_id',
        'print_option_id',
        'quantity',
        'included_count',
        'extra_count',
        'photo_title',
        'photo_thumbnail_path',
        'print_option_name',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'included_count' => 'integer',
        'extra_count' => 'integer',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(ClassroomPhoto::class, 'classroom_photo_id');
    }

    public function printOption(): BelongsTo
    {
        return $this->belongsTo(PrintOption::class);
    }
}
