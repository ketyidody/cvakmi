<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Package extends Model
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

    /**
     * Print formats the package covers, with the per-format quantity included.
     */
    public function printOptions(): BelongsToMany
    {
        return $this->belongsToMany(PrintOption::class)
            ->withPivot('included_quantity')
            ->withTimestamps();
    }

    /**
     * Map of print_option_id => included_quantity for this package.
     *
     * @return array<int, int>
     */
    public function allowanceMap(): array
    {
        return $this->printOptions
            ->mapWithKeys(fn ($opt) => [$opt->id => (int) $opt->pivot->included_quantity])
            ->all();
    }
}
