<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use App\Models\Score;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $scores = Score::orderBy('number')->get(['id', 'title', 'composer']);

        return Inertia::render('Admin/Concerts/Create', [
            'scores' => $scores,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'is_current' => 'boolean',
            'score_ids' => 'nullable|array',
            'score_ids.*' => 'exists:scores,id',
        ]);

        if (!empty($validated['is_current'])) {
            Concert::where('is_current', true)->update(['is_current' => false]);
        }

        $concert = Concert::create([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'location' => $validated['location'] ?? null,
            'is_current' => $validated['is_current'] ?? false,
        ]);

        if (!empty($validated['score_ids'])) {
            $concert->scores()->sync($validated['score_ids']);
        }

        return redirect()->route('admin.concerts.index')->with('success', 'Concert aangemaakt.');
    }

    public function edit(Concert $concert): Response
    {
        $concert->load('scores');
        $scores = Score::orderBy('number')->get(['id', 'title', 'composer']);

        return Inertia::render('Admin/Concerts/Edit', [
            'concert' => $concert,
            'scores' => $scores,
        ]);
    }

    public function update(Request $request, Concert $concert): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'is_current' => 'boolean',
            'score_ids' => 'nullable|array',
            'score_ids.*' => 'exists:scores,id',
        ]);

        if (!empty($validated['is_current']) && !$concert->is_current) {
            Concert::where('is_current', true)->update(['is_current' => false]);
        }

        $concert->update([
            'title' => $validated['title'],
            'date' => $validated['date'],
            'location' => $validated['location'] ?? null,
            'is_current' => $validated['is_current'] ?? false,
        ]);

        $concert->scores()->sync($validated['score_ids'] ?? []);

        return redirect()->route('admin.concerts.index')->with('success', 'Concert bijgewerkt.');
    }

    public function destroy(Concert $concert): RedirectResponse
    {
        $concert->delete();

        return redirect()->route('admin.concerts.index')->with('success', 'Concert verwijderd.');
    }
}
