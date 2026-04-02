<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Score;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ScoreController extends Controller
{
    public function index(): Response
    {
        $scores = Score::withCount('parts')->orderBy('number')->paginate(20);

        return Inertia::render('Admin/Scores/Index', [
            'scores' => $scores,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Scores/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'composer' => 'required|string|max:255',
            'arranger' => 'nullable|string|max:255',
            'number' => 'required|integer',
        ]);

        Score::create($validated);

        return redirect()->route('admin.scores.index')->with('success', 'Score aangemaakt.');
    }

    public function edit(Score $score): Response
    {
        $score->load('parts');

        return Inertia::render('Admin/Scores/Edit', [
            'score' => $score,
        ]);
    }

    public function update(Request $request, Score $score): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'composer' => 'required|string|max:255',
            'arranger' => 'nullable|string|max:255',
            'number' => 'required|integer',
        ]);

        $score->update($validated);

        return redirect()->route('admin.scores.index')->with('success', 'Score bijgewerkt.');
    }

    public function destroy(Score $score): RedirectResponse
    {
        $score->delete();

        return redirect()->route('admin.scores.index')->with('success', 'Score verwijderd.');
    }
}
