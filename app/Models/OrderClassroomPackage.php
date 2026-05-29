<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One package selection for one classroom within an order. An order can have
 * multiple of these (one per classroom the parent is ordering from).
 */
class OrderClassroomPackage extends Model
{
    protected $table = 'order_classroom_packages';

    protected $fillable = [
        'order_id',
        'classroom_id',
        'package_id',
        'package_name',
        'package_price',
    ];

    protected $casts = [
        'package_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
