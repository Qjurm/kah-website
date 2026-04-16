<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class InstrumentController extends Controller
{
    /**
     * Display a listing of instruments for the admin dashboard.
     */
    public function index(): Response
    {
        $instruments = Instrument::withCount(['users', 'parts'])
            ->with(['users', 'parts.score'])
            ->orderBy('name')
            ->get();

        return Inertia::render('Admin/Instruments/Index', [
            'instruments' => $instruments->map(fn ($instrument) => [
                'id' => $instrument->id,
                'name' => $instrument->name,
                'users_count' => $instrument->users_count,
                'parts_count' => $instrument->parts_count,
                'members' => $instrument->users->map(fn ($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                ]),
                'repertoire' => $instrument->parts->map(fn ($p) => [
                    'id' => $p->id,
                    'score_id' => $p->score_id,
                    'title' => $p->score?->title ?? 'Onbekend',
                ])->unique('title')->values(),
            ])
        ]);
    }

    /**
     * Store a newly created instrument (Web form).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instruments,name',
        ]);

        Instrument::create([
            'name' => $validated['name'],
            'display_order' => Instrument::max('display_order') + 1,
        ]);

        return redirect()->back()->with('success', 'Instrument toegevoegd.');
    }

    /**
     * Update an instrument name.
     */
    public function update(Request $request, Instrument $instrument): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instruments,name,' . $instrument->id,
        ]);

        $instrument->update(['name' => $validated['name']]);

        return redirect()->back()->with('success', 'Instrument bijgewerkt.');
    }

    /**
     * Remove an instrument.
     */
    public function destroy(Instrument $instrument): RedirectResponse
    {
        $instrument->delete();
        return redirect()->back()->with('success', 'Instrument verwijderd.');
    }

    /**
     * Store a newly created instrument (API endpoint for inline creation).
     */
    public function storeApi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instruments,name',
        ]);

        $instrument = Instrument::create([
            'name' => $validated['name'],
            'display_order' => Instrument::max('display_order') + 1,
        ]);

        return response()->json([
            'id' => $instrument->id,
            'name' => $instrument->name,
            'display_order' => $instrument->display_order,
        ], 201);
    }
}
