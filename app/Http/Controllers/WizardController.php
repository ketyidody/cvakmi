<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksClassroomAccess;
use App\Models\Classroom;
use App\Models\ClassroomPhoto;
use App\Models\Order;
use App\Models\Package;
use App\Models\PrintOption;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

/**
 * Drives the step-by-step parent ordering flow:
 *   1. (Registration — handled by RegisteredUserController)
 *   2. Packages — one picker per classroom the parent belongs to (classes
 *      with no selection are skipped from later steps)
 *   3+. Photos — one step per classroom that has a selected package
 *   N. Summary + submit
 */
class WizardController extends Controller
{
    use ChecksClassroomAccess;

    /**
     * Dispatch to whichever step matches the parent's current draft state.
     */
    public function start(Request $request)
    {
        $cart = $request->user()->currentCart();
        $cart->load('classroomPackages.classroom');

        abort_if(
            $this->parentClassrooms($request->user())->isEmpty(),
            403,
            'Nie ste priradený k žiadnej triede.'
        );

        $first = $this->selectedClassrooms($cart)->first();
        if (! $first) {
            return redirect()->route('order.package');
        }

        return redirect()->route('order.photos', $first->slug);
    }

    // ---------- Step 2: packages (one per classroom) -----------------------

    public function package(Request $request)
    {
        $cart = $request->user()->currentCart();
        $cart->load('classroomPackages');

        // Current selection per classroom_id (null when no package chosen).
        $current = $cart->classroomPackages
            ->mapWithKeys(fn ($cp) => [$cp->classroom_id => $cp->package_id])
            ->all();

        return Inertia::render('Order/Wizard/Package', [
            'classrooms' => $this->parentClassrooms($request->user())
                ->map(fn (Classroom $c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'slug' => $c->slug,
                ])->values(),
            'currentSelections' => $current,
            'packages' => Package::with('printOptions')
                ->where('is_active', true)
                ->orderBy('display_order')->orderBy('name')
                ->get()
                ->map(fn ($p) => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'description' => $p->description,
                    'price' => (float) $p->price,
                    'allowances' => $p->printOptions->map(fn ($o) => [
                        'print_option_id' => $o->id,
                        'print_option_name' => $o->name,
                        'included_quantity' => (int) $o->pivot->included_quantity,
                    ])->values(),
                ]),
            'steps' => $this->stepList($request->user(), 'package'),
        ]);
    }

    /**
     * Persist per-classroom package selections. Selections come as
     * { classroom_id => package_id|null }. Null means "skip this class" —
     * any existing items for that class are removed.
     */
    public function savePackages(Request $request)
    {
        $validated = $request->validate([
            'selections' => 'required|array|min:1',
            'selections.*.classroom_id' => 'required|integer|exists:classrooms,id',
            'selections.*.package_id' => 'nullable|integer|exists:packages,id',
        ]);

        $user = $request->user();
        $allowedClassroomIds = $this->parentClassrooms($user)->pluck('id')->all();
        $cart = $user->currentCart();

        DB::transaction(function () use ($cart, $validated, $allowedClassroomIds) {
            $touchedClassroomIds = [];
            $deselectedClassroomIds = [];

            foreach ($validated['selections'] as $row) {
                $classroomId = (int) $row['classroom_id'];
                if (! in_array($classroomId, $allowedClassroomIds, true)) {
                    continue;  // Ignore classes the parent doesn't belong to.
                }

                if (empty($row['package_id'])) {
                    // Deselected: remove pivot if it exists.
                    $cart->classroomPackages()->where('classroom_id', $classroomId)->delete();
                    $deselectedClassroomIds[] = $classroomId;
                } else {
                    $cart->classroomPackages()->updateOrCreate(
                        ['classroom_id' => $classroomId],
                        ['package_id' => (int) $row['package_id']],
                    );
                    $touchedClassroomIds[] = $classroomId;
                }
            }

            // Drop items belonging to deselected classes so we don't leave
            // orphaned (now uncovered) selections in the cart.
            if (! empty($deselectedClassroomIds)) {
                $cart->items()
                    ->whereHas('photo', fn ($q) => $q->whereIn('classroom_id', $deselectedClassroomIds))
                    ->delete();
            }
        });

        $cart->refresh();
        $cart->recalculate();

        // Require at least one selection to advance.
        $first = $this->selectedClassrooms($cart)->first();
        if (! $first) {
            return back()->withErrors([
                'selections' => 'Vyberte balík aspoň pre jednu triedu.',
            ]);
        }

        return redirect()->route('order.photos', $first->slug);
    }

    // ---------- Step 3.x: photos per classroom -----------------------------

    public function photos(Request $request, Classroom $classroom)
    {
        $this->authorizeClassroomAccess($request->user(), $classroom);

        $cart = $request->user()->currentCart();
        $cart->load([
            'classroomPackages.package.printOptions',
            'items.printOption',
            'items.photo',
        ]);

        $myPackage = $cart->classroomPackages->firstWhere('classroom_id', $classroom->id);
        if (! $myPackage) {
            // No package picked for this class — bounce back to the package step.
            return redirect()->route('order.package');
        }

        $classroom->load('photos');

        // Items in THIS classroom — used both for the allowance usage strip and
        // for the per-photo "already picked" breakdown.
        $itemsHere = $cart->items->filter(fn ($i) => $i->photo?->classroom_id === $classroom->id);

        $printOptions = PrintOption::where('is_active', true)
            ->orderBy('display_order')->orderBy('name')
            ->get(['id', 'name', 'description', 'price']);

        // Allowance vs usage per print option for this classroom's package.
        // `used` is the sum of `included_count` (allowance actually consumed),
        // so it never exceeds the allowance — anything past it shows as extra.
        $usedByOption = $itemsHere
            ->groupBy('print_option_id')
            ->map(fn ($items) => (int) $items->sum('included_count'));

        $allowanceRows = collect($myPackage->package?->allowanceMap() ?? [])
            ->map(function ($qty, $printOptionId) use ($usedByOption, $printOptions) {
                $opt = $printOptions->firstWhere('id', $printOptionId);

                return [
                    'print_option_id' => (int) $printOptionId,
                    'name' => $opt?->name,
                    'allowance' => (int) $qty,
                    'used' => (int) ($usedByOption[$printOptionId] ?? 0),
                ];
            })
            ->values();

        // Per-photo selections include the breakdown so the photo card can
        // label each line as "in package" or "extra".
        $selections = $itemsHere
            ->groupBy('classroom_photo_id')
            ->map(fn ($items) => $items->map(fn ($i) => [
                'item_id' => $i->id,
                'print_option_id' => $i->print_option_id,
                'print_option_name' => $i->printOption?->name ?? $i->print_option_name,
                'quantity' => $i->quantity,
                'included_count' => $i->included_count,
                'extra_count' => $i->extra_count,
                'unit_price' => (float) ($i->printOption?->price ?? $i->unit_price),
            ])->values());

        $selectedClasses = $this->selectedClassrooms($cart);
        $idx = $selectedClasses->search(fn ($c) => $c->id === $classroom->id);
        $prev = $idx > 0 ? $selectedClasses[$idx - 1] : null;
        $next = $idx < $selectedClasses->count() - 1 ? $selectedClasses[$idx + 1] : null;

        return Inertia::render('Order/Wizard/Photos', [
            'classroom' => $classroom->only(['id', 'name', 'slug', 'description']),
            'package' => $myPackage->package ? [
                'id' => $myPackage->package->id,
                'name' => $myPackage->package->name,
                'description' => $myPackage->package->description,
                'price' => (float) $myPackage->package->price,
            ] : null,
            'allowanceRows' => $allowanceRows,
            'photos' => $classroom->photos->map(fn (ClassroomPhoto $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'thumbnail_url' => route('order.photo', ['classroomPhoto' => $p->id, 'size' => 'thumbnail']),
                'medium_url' => route('order.photo', ['classroomPhoto' => $p->id, 'size' => 'medium']),
            ]),
            'printOptions' => $printOptions,
            'selections' => $selections,
            'cartItemCount' => $cart->items->count(),
            'prevClassroom' => $prev?->only(['slug', 'name']),
            'nextClassroom' => $next?->only(['slug', 'name']),
            'steps' => $this->stepList($request->user(), $classroom),
        ]);
    }

    // ---------- Step N: summary + submit -----------------------------------

    public function summary(Request $request)
    {
        $cart = $request->user()->currentCart();
        $cart->load([
            'items.photo.classroom',
            'items.printOption',
            'classroomPackages.package',
            'classroomPackages.classroom',
        ]);

        if ($this->selectedClassrooms($cart)->isEmpty()) {
            return redirect()->route('order.package');
        }

        $items = $cart->items->filter(fn ($i) => $i->photo !== null);

        // Group items by their photo's classroom; each group also carries the
        // classroom's selected package for context.
        $groups = $items
            ->groupBy(fn ($i) => $i->photo->classroom_id)
            ->map(function ($g, $classroomId) use ($cart) {
                $pivot = $cart->classroomPackages->firstWhere('classroom_id', $classroomId);
                $classroom = $pivot?->classroom ?? $g->first()->photo->classroom;

                return [
                    'classroom_id' => $classroomId,
                    'classroom_name' => $classroom?->name ?? '—',
                    'package_name' => $pivot?->package?->name,
                    'package_price' => $pivot?->package ? (float) $pivot->package->price : 0,
                    'items' => $g->values()->map(fn ($i) => [
                        'id' => $i->id,
                        'photo_title' => $i->photo?->title ?? $i->photo_title,
                        'print_option_name' => $i->printOption?->name ?? $i->print_option_name,
                        'quantity' => $i->quantity,
                        'included_count' => $i->included_count,
                        'extra_count' => $i->extra_count,
                        'unit_price' => (float) $i->unit_price,
                        'line_total' => (float) $i->line_total,
                        'thumbnail_url' => $i->classroom_photo_id
                            ? route('order.photo', ['classroomPhoto' => $i->classroom_photo_id, 'size' => 'thumbnail'])
                            : null,
                    ]),
                ];
            })
            ->values();

        // Classrooms with a package but no items — shown so the parent can either
        // pick photos or skip the class.
        $emptySelectedClassrooms = $cart->classroomPackages
            ->filter(fn ($cp) => ! $items->contains(fn ($i) => $i->photo?->classroom_id === $cp->classroom_id))
            ->map(fn ($cp) => [
                'classroom_slug' => $cp->classroom?->slug,
                'classroom_name' => $cp->classroom?->name,
                'package_name' => $cp->package?->name,
            ])
            ->values();

        $packagesTotal = (float) $groups->sum('package_price');
        $extras = (float) round($items->sum(fn ($i) => (float) $i->line_total), 2);

        return Inertia::render('Order/Wizard/Summary', [
            'groups' => $groups,
            'emptySelectedClassrooms' => $emptySelectedClassrooms,
            'totals' => [
                'packages' => $packagesTotal,
                'extras' => $extras,
                'total' => round($packagesTotal + $extras, 2),
            ],
            'hasItems' => $items->isNotEmpty(),
            'steps' => $this->stepList($request->user(), 'summary'),
        ]);
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'note' => 'nullable|string|max:2000',
        ]);

        $cart = $request->user()->currentCart();
        $cart->load(['items.photo', 'classroomPackages.package']);

        if ($cart->classroomPackages->isEmpty()) {
            return back()->withErrors([
                'selections' => 'Najprv si vyberte balík aspoň pre jednu triedu.',
            ]);
        }

        $items = $cart->items->filter(fn ($i) => $i->photo !== null);
        if ($items->isEmpty()) {
            return back()->withErrors(['cart' => 'Váš výber je prázdny.']);
        }

        DB::transaction(function () use ($cart, $validated) {
            $cart->recalculate();

            // Snapshot package name + price on each classroom-package row.
            foreach ($cart->classroomPackages as $cp) {
                $cp->update([
                    'package_name' => $cp->package?->name,
                    'package_price' => $cp->package?->price ?? 0,
                ]);
            }

            $cart->update([
                'status' => Order::STATUS_PENDING,
                'order_number' => Order::generateOrderNumber(),
                'note' => $validated['note'] ?? null,
                'submitted_at' => now(),
            ]);

            // Drop orphan items whose photo got deleted while in the cart.
            $cart->items()->whereNull('classroom_photo_id')->delete();
        });

        return redirect()->route('order.show', $cart)
            ->with('success', 'Objednávka odoslaná.');
    }

    // ---------- Helpers ----------------------------------------------------

    /**
     * Steps: Balíky → (one per selected classroom) → Sumár. Skipped classes
     * don't appear; we only show what the parent is actually ordering from.
     */
    private function stepList(User $user, string|Classroom $current): array
    {
        $cart = $user->currentCart();
        $cart->load('classroomPackages.classroom');

        $currentKey = $current instanceof Classroom ? 'photos:'.$current->slug : $current;

        $steps = [[
            'key' => 'package',
            'label' => 'Balíky',
            'url' => route('order.package'),
        ]];
        foreach ($this->selectedClassrooms($cart) as $c) {
            $steps[] = [
                'key' => 'photos:'.$c->slug,
                'label' => $c->name,
                'url' => route('order.photos', $c->slug),
            ];
        }
        $steps[] = [
            'key' => 'summary',
            'label' => 'Sumár',
            'url' => route('order.summary'),
        ];

        foreach ($steps as &$step) {
            $step['current'] = $step['key'] === $currentKey;
        }

        return $steps;
    }

    /**
     * Classrooms the parent has chosen a package for, in display order.
     */
    private function selectedClassrooms(Order $cart)
    {
        return $cart->classroomPackages
            ->map(fn ($cp) => $cp->classroom)
            ->filter()
            ->sortBy(fn ($c) => [$c->display_order, $c->name])
            ->values();
    }

    /**
     * The active classrooms for this parent in display order.
     */
    private function parentClassrooms(User $user)
    {
        $query = $user->is_admin ? Classroom::query() : $user->classrooms();

        return $query->where('is_active', true)
            ->orderBy('display_order')->orderBy('name')
            ->get();
    }
}
