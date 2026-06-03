<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with([
            'user',
            'items.photo.classroom:id,name',
            'classroomPackages:id,order_id,package_name',
        ])
            ->withCount('items')
            ->where('status', '!=', Order::STATUS_CART);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('submitted_at')
            ->paginate(20)
            ->withQueryString();

        // Expose derived per-order summaries the index needs in one place.
        $orders->getCollection()->transform(function ($order) {
            $classroomNames = $order->items
                ->map(fn ($i) => $i->photo?->classroom?->name)
                ->filter()->unique()->values();

            $packageNames = $order->classroomPackages
                ->pluck('package_name')->filter()->unique()->values();

            $order->setAttribute('classroom_names', $classroomNames);
            $order->setAttribute('package_names', $packageNames);

            return $order;
        });

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
            'filters' => $request->only('status'),
        ]);
    }

    public function show(Order $order)
    {
        abort_if($order->status === Order::STATUS_CART, 404);

        $order->load([
            'user.classrooms',
            'items.photo.classroom',
            'classroomPackages.classroom',
        ]);

        // Same grouping the parent's Show uses, so the admin sees the same
        // per-classroom breakdown.
        $groups = $order->items
            ->groupBy(fn ($i) => $i->photo?->classroom_id)
            ->map(function ($g, $classroomId) use ($order) {
                $pivot = $order->classroomPackages->firstWhere('classroom_id', $classroomId);
                $classroom = $pivot?->classroom ?? $g->first()->photo?->classroom;

                return [
                    'classroom_name' => $classroom?->name ?? '—',
                    'package_name' => $pivot?->package_name,
                    'package_price' => (float) ($pivot?->package_price ?? 0),
                    'items' => $g->values()->map(fn ($i) => [
                        'id' => $i->id,
                        'classroom_photo_id' => $i->classroom_photo_id,
                        'photo_title' => $i->photo_title,
                        'print_option_name' => $i->print_option_name,
                        'quantity' => $i->quantity,
                        'included_count' => $i->included_count,
                        'extra_count' => $i->extra_count,
                        'unit_price' => (float) $i->unit_price,
                        'line_total' => (float) $i->line_total,
                    ]),
                ];
            })
            ->values();

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'paid' => (bool) $order->paid,
                'note' => $order->note,
                'submitted_at' => $order->submitted_at,
                'total_estimate' => (float) $order->total_estimate,
                'user' => $order->user,
                'groups' => $groups,
            ],
            'statuses' => Order::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order)
    {
        abort_if($order->status === Order::STATUS_CART, 404);

        $validated = $request->validate([
            'status' => ['sometimes', 'required', Rule::in(Order::STATUSES)],
            'paid' => ['sometimes', 'boolean'],
        ]);

        $order->update($validated);

        return back()->with('success', 'Order updated.');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')
            ->with('success', 'Order deleted.');
    }
}
