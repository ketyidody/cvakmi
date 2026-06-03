<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_CART = 'cart';

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'paid',
        'note',
        'total_estimate',
        'submitted_at',
    ];

    protected $casts = [
        'paid' => 'boolean',
        'total_estimate' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Per-classroom package selections (zero or more per order).
     */
    public function classroomPackages(): HasMany
    {
        return $this->hasMany(OrderClassroomPackage::class);
    }

    /**
     * Recompute snapshot fields, package-allowance distribution, and totals.
     *
     * Each classroom in the order has its own package selection (in
     * `classroomPackages`); allowances are distributed PER classroom. For each
     * item: its photo's classroom determines which allowance map applies; that
     * classroom's `included_quantity` for the item's print option is consumed
     * first (free), the remainder becomes `extra_count` and is charged at the
     * snapshotted unit price. Items in classrooms without a selected package
     * are entirely "extra" (full price). The order's total is the sum of
     * package prices for classrooms with at least one item, plus all extras.
     */
    public function recalculate(): void
    {
        $this->load([
            'items.photo.classroom',
            'items.printOption',
            'classroomPackages.package.printOptions',
        ]);

        // Per-classroom remaining allowance: classroom_id => [print_option_id => qty]
        $remaining = [];
        foreach ($this->classroomPackages as $cp) {
            $remaining[$cp->classroom_id] = $cp->package
                ? $cp->package->allowanceMap()
                : [];
        }

        $extrasTotal = 0;

        // Process items in id order so distribution is deterministic.
        $items = $this->items->sortBy('id')->values();

        foreach ($items as $item) {
            $printOption = $item->printOption;
            $unitPrice = $printOption?->price ?? $item->unit_price;
            $quantity = (int) $item->quantity;

            $classroomId = $item->photo?->classroom_id;
            $optionId = $printOption?->id;

            $available = ($classroomId !== null && $optionId !== null && isset($remaining[$classroomId]))
                ? ($remaining[$classroomId][$optionId] ?? 0)
                : 0;

            $included = min($quantity, $available);
            $extra = $quantity - $included;

            if ($included > 0) {
                $remaining[$classroomId][$optionId] = $available - $included;
            }

            $item->fill([
                'included_count' => $included,
                'extra_count' => $extra,
                'unit_price' => $unitPrice,
                'line_total' => $extra * $unitPrice,
                'photo_title' => $item->photo?->title ?? $item->photo_title,
                'photo_thumbnail_path' => $item->photo?->thumbnail_path ?? $item->photo_thumbnail_path,
                'print_option_name' => $printOption?->name ?? $item->print_option_name,
            ])->save();

            $extrasTotal += $extra * $unitPrice;
        }

        // Charge for each classroom-package only if that classroom actually has items.
        $classroomsWithItems = $this->items
            ->pluck('photo.classroom_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $packageTotal = $this->classroomPackages
            ->whereIn('classroom_id', $classroomsWithItems)
            ->sum(fn ($cp) => (float) ($cp->package?->price ?? 0));

        $this->total_estimate = $packageTotal + $extrasTotal;
        $this->save();
    }

    /**
     * Build the human-readable order number for this row in the form
     * YYMMDDOOOO — submission date (2-digit year) concatenated with the
     * order's primary key zero-padded to four digits. Kept at 10 digits so
     * it fits the PAY by square variable-symbol spec.
     */
    public function makeOrderNumber(): string
    {
        return now()->format('ymd').sprintf('%04d', $this->id);
    }
}
