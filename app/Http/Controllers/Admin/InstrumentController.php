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
            ->with(['users', 'parts.score', 'section'])
            ->orderBy('name')
            ->get();

        $sections = \App\Models\InstrumentSection::orderBy('display_order')->get();

        return Inertia::render('Admin/Instruments/Index', [
            'instruments' => $instruments->map(fn ($instrument) => [
                'id' => $instrument->id,
                'name' => $instrument->name,
                'section_id' => $instrument->section_id,
                'section_name' => $instrument->section?->name ?? 'Geen sectie',
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
            ]),
            'sections' => $sections
        ]);
    }

    /**
     * Store a newly created instrument (Web form).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instruments,name',
            'section_id' => 'nullable|exists:instrument_sections,id',
        ]);

        Instrument::create([
            'name' => $validated['name'],
            'section_id' => $validated['section_id'] ?? null,
            'display_order' => Instrument::max('display_order') + 1,
        ]);

        return redirect()->back()->with('success', 'Instrument toegevoegd.');
    }

    /**
     * Update an instrument name and section.
     */
    public function update(Request $request, Instrument $instrument): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instruments,name,' . $instrument->id,
            'section_id' => 'nullable|exists:instrument_sections,id',
        ]);

        $instrument->update([
            'name' => $validated['name'],
            'section_id' => $validated['section_id'] ?? null,
        ]);

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
            'section_id' => 'nullable|exists:instrument_sections,id',
        ]);

        $instrument = Instrument::create([
            'name' => $validated['name'],
            'section_id' => $validated['section_id'] ?? null,
            'display_order' => Instrument::max('display_order') + 1,
        ]);

        return response()->json([
            'id' => $instrument->id,
            'name' => $instrument->name,
            'section_id' => $instrument->section_id,
            'display_order' => $instrument->display_order,
        ], 201);
    }
}
