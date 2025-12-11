<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PricingType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pricingType) {
            if (empty($pricingType->slug)) {
                $pricingType->slug = Str::slug($pricingType->name);
            }
        });
    }

    public function packages(): HasMany
    {
        return $this->hasMany(PricingPackage::class);
    }
}
