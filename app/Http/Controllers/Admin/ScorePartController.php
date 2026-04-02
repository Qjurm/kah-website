<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Score;
use App\Models\ScorePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ScorePartController extends Controller
{
    public function store(Request $request, Score $score): RedirectResponse
    {
        $validated = $request->validate([
            'instrument' => 'required|string|max:255',
            'pdf' => 'required|file|mimes:pdf|max:20480',
        ]);

        $path = $request->file('pdf')->store("scores/{$score->id}", 'public');

        $score->parts()->create([
            'instrument' => $validated['instrument'],
            'pdf_path' => $path,
        ]);

        return redirect()->route('admin.scores.edit', $score)->with('success', 'Part toegevoegd.');
    }

    public function destroy(ScorePart $part): RedirectResponse
    {
        Storage::disk('public')->delete($part->pdf_path);
        $scoreId = $part->score_id;
        $part->delete();

        return redirect()->route('admin.scores.edit', $scoreId)->with('success', 'Part verwijderd.');
    }
}
