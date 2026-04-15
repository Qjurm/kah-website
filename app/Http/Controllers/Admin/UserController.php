<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $pendingUsers = User::with('instruments')->where('approved', false)->orderBy('created_at', 'desc')->get();
        $users = User::with('instruments')->where('approved', true)->orderBy('name')->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'pendingUsers' => $pendingUsers->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'created_at' => $u->created_at,
                'approved' => $u->approved,
                'instruments' => $u->instruments->pluck('name')->toArray(),
            ])->toArray(),
            'users' => [
                'data' => $users->map(fn ($u) => [
                    'id' => $u->id, 
                    'name' => $u->name, 
                    'email' => $u->email, 
                    'role' => $u->role, 
                    'created_at' => $u->created_at, 
                    'approved' => $u->approved,
                    'instruments' => $u->instruments->pluck('name')->toArray(),
                ])->toArray(),
                'from' => $users->firstItem(),
                'to' => $users->lastItem(),
                'total' => $users->total(),
                'last_page' => $users->lastPage(),
                'current_page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'path' => $users->path(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,musician',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('beheer.gebruikers.index')->with('success', 'Gebruiker aangemaakt.');
    }

    public function edit(User $user): Response
    {
        $user->load('instruments');
        $allInstruments = \App\Models\Instrument::orderBy('display_order')->get();

        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'approved' => $user->approved,
                'instrument_ids' => $user->instruments->pluck('id')->toArray(),
                'primary_instrument_id' => $user->instruments->where('pivot.is_primary', true)->first()?->id,
            ],
            'allInstruments' => $allInstruments,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,musician',
            'instrument_ids' => 'nullable|array',
            'instrument_ids.*' => 'exists:instruments,id',
            'primary_instrument_id' => 'nullable|exists:instruments,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (isset($validated['instrument_ids'])) {
            $syncData = [];
            foreach ($validated['instrument_ids'] as $id) {
                $syncData[$id] = ['is_primary' => ($id == ($validated['primary_instrument_id'] ?? null))];
            }
            $user->instruments()->sync($syncData);
        }

        return redirect()->route('beheer.gebruikers.index')->with('success', 'Gebruiker bijgewerkt.');
    }

    public function approve(User $user): RedirectResponse
    {
        $user->update(['approved' => true]);
        return redirect()->route('beheer.gebruikers.index')->with('success', $user->name . ' goedgekeurd.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('beheer.gebruikers.index')->with('success', 'Gebruiker verwijderd.');
    }
}
