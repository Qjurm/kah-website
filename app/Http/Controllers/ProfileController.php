<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Instrument;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();
        $userInstruments = $user->instruments()->get();
        $allInstruments = Instrument::orderBy('display_order')->get();

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'userInstruments' => $userInstruments,
            'availableInstruments' => $allInstruments,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Update user's instruments.
     */
    public function updateInstruments(Request $request): RedirectResponse
    {
        $request->validate([
            'instruments' => 'array|required',
            'instruments.*' => 'integer|exists:instruments,id',
            'primary_instrument' => 'nullable|integer|exists:instruments,id',
        ]);

        $user = $request->user();

        // Sync instruments with is_primary flag
        $syncData = [];
        foreach ($request->instruments as $instrumentId) {
            $syncData[$instrumentId] = [
                'is_primary' => $request->primary_instrument == $instrumentId,
            ];
        }

        $user->instruments()->sync($syncData);

        return Redirect::route('profile.edit')->with('status', 'Instrumenten bijgewerkt.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
