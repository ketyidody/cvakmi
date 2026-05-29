<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'order' => [
                // Total in-progress lines across all the user's per-class carts.
                'cartCount' => fn () => $user
                    ? (int) \App\Models\OrderItem::whereIn(
                        'order_id',
                        $user->orders()->where('status', \App\Models\Order::STATUS_CART)->pluck('id')
                    )->count()
                    : 0,
            ],
        ];
    }
}
