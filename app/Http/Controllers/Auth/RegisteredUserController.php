<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Instrument;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        $instruments = Instrument::orderBy('display_order')->get(['id', 'name']);
        
        return Inertia::render('Auth/Register', [
            'instruments' => $instruments,
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'instruments' => 'array|required_if:role,musician',
            'instruments.*' => 'integer|exists:instruments,id',
            'primary_instrument' => 'nullable|integer|exists:instruments,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->input('role', 'musician'),
        ]);

        // Attach instruments if provided
        if ($request->has('instruments') && is_array($request->instruments)) {
            foreach ($request->instruments as $instrumentId) {
                $isPrimary = $request->primary_instrument == $instrumentId;
                $user->instruments()->attach($instrumentId, ['is_primary' => $isPrimary]);
            }
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on user role
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->intended(route('beheer.dashboard', absolute: false));
        }

        return redirect()->intended(route('muziek.index', absolute: false));
    }
}
