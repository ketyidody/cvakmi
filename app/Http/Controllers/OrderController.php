<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Parent-facing order history (post-submission). Submission itself lives in
 * the wizard; this controller only reads.
 */
class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = $request->user()->orders()
            ->with('classroomPackages:id,order_id,package_name')
            ->where('status', '!=', Order::STATUS_CART)
            ->withCount('items')
            ->orderByDesc('submitted_at')
            ->get(['id', 'order_number', 'status', 'total_estimate', 'submitted_at']);

        return Inertia::render('Order/History', [
            'orders' => $orders->map(fn ($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'status' => $o->status,
                'total_estimate' => (float) $o->total_estimate,
                'submitted_at' => $o->submitted_at,
                'items_count' => $o->items_count,
                'package_names' => $o->classroomPackages
                    ->pluck('package_name')
                    ->filter()
                    ->unique()
                    ->values(),
            ]),
        ]);
    }

    public function show(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_if($order->status === Order::STATUS_CART, 404);

        $order->load(['items.photo.classroom', 'classroomPackages.classroom']);

        // Group items by their photo's classroom, attaching the per-classroom
        // package snapshot for that group.
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
                        'photo_title' => $i->photo_title,
                        'print_option_name' => $i->print_option_name,
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

        // Total of all per-classroom packages that produced items.
        $packagesTotal = (float) $groups->sum('package_price');

        $settings = Setting::current();
        $iban = preg_replace('/\s+/', '', (string) $settings->iban);

        return Inertia::render('Order/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'note' => $order->note,
                'total_estimate' => (float) $order->total_estimate,
                'packages_total' => $packagesTotal,
                'submitted_at' => $order->submitted_at,
                'groups' => $groups,
            ],
            'payment' => $iban === '' ? null : [
                'iban' => $iban,
                'beneficiary_name' => $settings->full_name,
                'beneficiary_address' => $settings->address,
                'beneficiary_email' => $settings->email,
                'amount' => (float) $order->total_estimate,
                'currency' => 'EUR',
                'variable_symbol' => (string) ($order->order_number ?: $order->id),
            ],
        ]);
    }
}
