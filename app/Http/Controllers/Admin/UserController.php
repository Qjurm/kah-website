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
        $pendingUsers = User::where('approved', false)->orderBy('created_at', 'desc')->get();
        $users = User::where('approved', true)->orderBy('name')->paginate(20);

        return Inertia::render('Admin/Users/Index', [
            'pendingUsers' => $pendingUsers->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'created_at' => $u->created_at,
                'approved' => $u->approved,
            ])->toArray(),
            'users' => $users->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'created_at' => $u->created_at,
                'approved' => $u->approved,
            ])->toArray(),
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
