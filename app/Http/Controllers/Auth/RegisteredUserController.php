<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        $this->ensureValidInvite($request);

        return Inertia::render('Auth/Register', [
            'classrooms' => Classroom::where('is_active', true)
                ->orderBy('display_order')
                ->orderBy('name')
                ->get(['id', 'name']),
            'invite' => $request->query('invite'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->ensureValidInvite($request);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'classrooms' => 'required|array|min:1',
            'classrooms.*' => 'integer|exists:classrooms,id',
        ], [
            'classrooms.required' => 'Please select at least one class.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Only attach active classrooms the parent selected.
        $classroomIds = Classroom::where('is_active', true)
            ->whereIn('id', $request->input('classrooms', []))
            ->pluck('id');
        $user->classrooms()->sync($classroomIds);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('order.start', absolute: false));
    }

    /**
     * Refuse registration unless the shared invite code matches (when set).
     */
    private function ensureValidInvite(Request $request): void
    {
        $expected = config('order.invite_code');

        if (empty($expected)) {
            return;
        }

        $provided = (string) ($request->query('invite') ?? $request->input('invite'));

        abort_unless(hash_equals($expected, $provided), 403, 'This registration link is invalid or has expired.');
    }
}
