<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PricingPackage extends Model
{
    protected $fillable = [
        'pricing_type_id',
        'name',
        'description',
        'price',
        'duration',
        'display_order',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function pricingType(): BelongsTo
    {
        return $this->belongsTo(PricingType::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'package_service')
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
