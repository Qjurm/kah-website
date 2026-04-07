<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use App\Models\Score;
use App\Models\ScorePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $instruments = Instrument::orderBy('display_order')->pluck('name');

        return Inertia::render('Admin/Scores/Create', [
            'instruments' => $instruments,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'composer'           => 'required|string|max:255',
            'arranger'           => 'nullable|string|max:255',
            'number'             => 'required|integer',
            'parts'              => 'nullable|array',
            'parts.*.instrument' => 'required_with:parts|string|max:255',
            'parts.*.pdf'        => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $score = Score::create([
            'title'    => $validated['title'],
            'composer' => $validated['composer'],
            'arranger' => $validated['arranger'] ?? null,
            'number'   => $validated['number'],
        ]);

        foreach ($validated['parts'] ?? [] as $i => $part) {
            $pdfFile = $request->file("parts.{$i}.pdf");
            if ($pdfFile) {
                $path = $pdfFile->store("scores/{$score->id}", 'public');
                $score->parts()->create([
                    'instrument' => $part['instrument'],
                    'pdf_path'   => $path,
                ]);
            }
        }

        return redirect()->route('beheer.bladmuziek.index')->with('success', 'Bladmuziek aangemaakt.');
    }

    public function edit(Score $score): Response
    {
        $score->load('parts');
        $instruments = Instrument::orderBy('display_order')->pluck('name');

        return Inertia::render('Admin/Scores/Edit', [
            'score'       => $score,
            'instruments' => $instruments,
        ]);
    }

    public function update(Request $request, Score $score): RedirectResponse
    {
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'composer'               => 'required|string|max:255',
            'arranger'               => 'nullable|string|max:255',
            'number'                 => 'required|integer',
            'removed_part_ids'       => 'nullable|array',
            'removed_part_ids.*'     => 'integer|exists:score_parts,id',
            'new_parts'              => 'nullable|array',
            'new_parts.*.instrument' => 'required_with:new_parts|string|max:255',
            'new_parts.*.pdf'        => 'nullable|file|mimes:pdf|max:20480',
        ]);

        $score->update([
            'title'    => $validated['title'],
            'composer' => $validated['composer'],
            'arranger' => $validated['arranger'] ?? null,
            'number'   => $validated['number'],
        ]);

        if (!empty($validated['removed_part_ids'])) {
            $partsToDelete = ScorePart::whereIn('id', $validated['removed_part_ids'])
                ->where('score_id', $score->id)
                ->get();
            foreach ($partsToDelete as $part) {
                Storage::disk('public')->delete($part->pdf_path);
                $part->delete();
            }
        }

        foreach ($validated['new_parts'] ?? [] as $i => $part) {
            $pdfFile = $request->file("new_parts.{$i}.pdf");
            if ($pdfFile) {
                $path = $pdfFile->store("scores/{$score->id}", 'public');
                $score->parts()->create([
                    'instrument' => $part['instrument'],
                    'pdf_path'   => $path,
                ]);
            }
        }

        return redirect()->route('beheer.bladmuziek.index')->with('success', 'Bladmuziek bijgewerkt.');
    }

    public function destroy(Score $score): RedirectResponse
    {
        $score->load('parts');
        foreach ($score->parts as $part) {
            Storage::disk('public')->delete($part->pdf_path);
        }
        $score->delete();

        return redirect()->route('beheer.bladmuziek.index')->with('success', 'Bladmuziek verwijderd.');
    }
}
