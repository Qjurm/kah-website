<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScoreResource;
use App\Http\Resources\InstrumentResource;
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
            'scores' => ScoreResource::collection($scores),
        ]);
    }

    public function create(): Response
    {
        $instruments = Instrument::orderBy('display_order')->get();

        return Inertia::render('Admin/Scores/Create', [
            'instruments' => InstrumentResource::collection($instruments),
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
            $path = null;
            if ($pdfFile) {
                $path = $pdfFile->store("scores/{$score->id}", 'public');
            }
            // Create part even if no PDF uploaded yet
            $score->parts()->create([
                'instrument' => $part['instrument'],
                'pdf_path'   => $path,
            ]);
        }

        return redirect()->route('beheer.bladmuziek.index')->with('success', 'Bladmuziek aangemaakt.');
    }

    public function edit(Score $score): Response
    {
        $score->load('parts');
        $instruments = Instrument::orderBy('display_order')->get();

        $data = [
            'score' => [
                'id'         => $score->id,
                'number'     => $score->number,
                'title'      => $score->title,
                'composer'   => $score->composer,
                'arranger'   => $score->arranger,
                'created_at' => $score->created_at,
                'updated_at' => $score->updated_at,
                'parts'      => $score->parts->map(fn ($p) => [
                    'id'         => $p->id,
                    'score_id'   => $p->score_id,
                    'instrument' => $p->instrument,
                    'pdf_path'   => $p->pdf_path,
                    'created_at' => $p->created_at,
                ])->toArray(),
            ],
            'instruments' => $instruments->map(fn ($i) => [
                'id'            => $i->id,
                'name'          => $i->name,
                'display_order' => $i->display_order,
            ])->toArray(),
        ];

        ray('Score Edit Data', $data);

        return Inertia::render('Admin/Scores/Edit', $data);
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
            $path = null;
            if ($pdfFile) {
                $path = $pdfFile->store("scores/{$score->id}", 'public');
            }
            // Create part even if no PDF uploaded yet
            $score->parts()->create([
                'instrument' => $part['instrument'],
                'pdf_path'   => $path,
            ]);
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
