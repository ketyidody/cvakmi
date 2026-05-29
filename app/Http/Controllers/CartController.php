<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ChecksClassroomAccess;
use App\Models\ClassroomPhoto;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PrintOption;
use Illuminate\Http\Request;

/**
 * Item-level cart operations during the wizard's photo steps. The cart itself
 * (the in-progress Order with status='cart') is owned by the wizard; this
 * controller just edits its items.
 */
class CartController extends Controller
{
    use ChecksClassroomAccess;

    public function addItem(Request $request)
    {
        $validated = $request->validate([
            'classroom_photo_id' => 'required|exists:classroom_photos,id',
            'print_option_id' => 'required|exists:print_options,id',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $photo = ClassroomPhoto::with('classroom')->findOrFail($validated['classroom_photo_id']);
        $this->authorizeClassroomAccess($request->user(), $photo->classroom);

        $printOption = PrintOption::where('is_active', true)->findOrFail($validated['print_option_id']);

        $cart = $request->user()->currentCart();

        // Same photo + same print option folds into one line.
        $item = $cart->items()->firstOrNew([
            'classroom_photo_id' => $photo->id,
            'print_option_id' => $printOption->id,
        ]);
        $item->quantity = ($item->quantity ?? 0) + $validated['quantity'];
        $item->save();

        $cart->recalculate();

        return back()->with('success', 'Pridané do výberu.');
    }

    public function updateItem(Request $request, OrderItem $item)
    {
        $this->authorizeItem($request, $item);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:0|max:99',
            'print_option_id' => 'nullable|exists:print_options,id',
        ]);

        $cart = $item->order;

        if ($validated['quantity'] === 0) {
            $item->delete();
            $cart->recalculate();

            return back()->with('success', 'Položka odstránená.');
        }

        if (! empty($validated['print_option_id'])) {
            $item->print_option_id = $validated['print_option_id'];
        }
        $item->quantity = $validated['quantity'];
        $item->save();

        $cart->recalculate();

        return back()->with('success', 'Výber upravený.');
    }

    public function removeItem(Request $request, OrderItem $item)
    {
        $this->authorizeItem($request, $item);
        $cart = $item->order;
        $item->delete();
        $cart->recalculate();

        return back()->with('success', 'Položka odstránená.');
    }

    /**
     * Ensure the item belongs to the current user's active cart.
     */
    private function authorizeItem(Request $request, OrderItem $item): void
    {
        abort_unless(
            $item->order->user_id === $request->user()->id
                && $item->order->status === Order::STATUS_CART,
            403
        );
    }
}
