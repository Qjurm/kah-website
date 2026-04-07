<?php

namespace App\Http\Controllers\Beheer;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use App\Models\Score;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ConcertController extends Controller
{
    public function index(): Response
    {
        $concerts = Concert::orderByDesc('date')->paginate(20);

        return Inertia::render('Admin/Concerts/Index', [
            'concerts' => $concerts,
        ]);
    }

    public function create(): Response
    {
        $scores = Score::orderBy('number')->get(['id', 'title', 'composer', 'number']);

        return Inertia::render('Admin/Concerts/Create', [
            'scores' => $scores,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'      => 'required|string|max:255',
            'date'       => 'required|date',
            'location'   => 'nullable|string|max:255',
            'photo'      => 'nullable|image|max:5120',
            'is_current' => 'boolean',
            'is_public'  => 'boolean',
            'score_ids'  => 'nullable|array',
            'score_ids.*' => 'exists:scores,id',
        ]);

        if (!empty($validated['is_current'])) {
            Concert::where('is_current', true)->update(['is_current' => false]);
        }

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('concert-photos', 'public');
        }

        $concert = Concert::create([
            'title'      => $validated['title'],
            'date'       => $validated['date'],
            'location'   => $validated['location'] ?? null,
            'photo_path' => $photoPath,
            'is_current' => $validated['is_current'] ?? false,
            'is_public'  => $validated['is_public'] ?? false,
        ]);

        $concert->scores()->sync($validated['score_ids'] ?? []);

        return redirect()->route('beheer.concerten.index')->with('success', 'Concert aangemaakt.');
    }

    public function edit(Concert $concert): Response
    {
        $concert->load('scores');
        $scores = Score::orderBy('number')->get(['id', 'title', 'composer', 'number']);

        return Inertia::render('Admin/Concerts/Edit', [
            'concert' => $concert,
            'scores'  => $scores,
        ]);
    }

    public function update(Request $request, Concert $concert): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'date'        => 'required|date',
            'location'    => 'nullable|string|max:255',
            'photo'       => 'nullable|image|max:5120',
            'is_current'  => 'boolean',
            'is_public'   => 'boolean',
            'score_ids'   => 'nullable|array',
            'score_ids.*' => 'exists:scores,id',
        ]);

        if (!empty($validated['is_current']) && !$concert->is_current) {
            Concert::where('is_current', true)->update(['is_current' => false]);
        }

        $photoPath = $concert->photo_path;
        if ($request->hasFile('photo')) {
            if ($concert->photo_path) {
                Storage::disk('public')->delete($concert->photo_path);
            }
            $photoPath = $request->file('photo')->store('concert-photos', 'public');
        }

        $concert->update([
            'title'      => $validated['title'],
            'date'       => $validated['date'],
            'location'   => $validated['location'] ?? null,
            'photo_path' => $photoPath,
            'is_current' => $validated['is_current'] ?? false,
            'is_public'  => $validated['is_public'] ?? false,
        ]);

        $concert->scores()->sync($validated['score_ids'] ?? []);

        return redirect()->route('beheer.concerten.index')->with('success', 'Concert bijgewerkt.');
    }

    public function destroy(Concert $concert): RedirectResponse
    {
        if ($concert->photo_path) {
            Storage::disk('public')->delete($concert->photo_path);
        }

        $concert->delete();

        return redirect()->route('beheer.concerten.index')->with('success', 'Concert verwijderd.');
    }
}
