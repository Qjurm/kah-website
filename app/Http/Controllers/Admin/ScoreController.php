<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScoreResource;
use App\Http\Resources\InstrumentResource;
use App\Models\Instrument;
use App\Models\InstrumentAlias;
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
        $scores = Score::withCount('parts')->orderBy('title')->paginate(20);

        return Inertia::render('Admin/Scores/Index', [
            'scores' => ScoreResource::collection($scores),
        ]);
    }

    public function create(): Response
    {
        $instruments = Instrument::with('aliases')->orderBy('display_order')->get();

        return Inertia::render('Admin/Scores/Create', [
            'instruments' => $instruments->map(fn ($i) => [
                'id'            => $i->id,
                'name'          => $i->name,
                'display_order' => $i->display_order,
                'aliases'       => $i->aliases->pluck('alias')->toArray(),
            ])->toArray(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'composer'           => 'required|string|max:255',
            'arranger'           => 'nullable|string|max:255',
            'parts'                              => 'nullable|array',
            'parts.*.instrument'                 => 'required|string|max:255',
            'parts.*.original_parsed_instrument' => 'nullable|string|max:255',
            'parts.*.pdf'                        => 'nullable|file|mimes:pdf|max:20480',
        ], [
            'parts.*.instrument.required' => 'Kies een instrument voor elke bijgevoegde PDF.',
        ]);

        $score = Score::create([
            'title'    => $validated['title'],
            'composer' => $validated['composer'],
            'arranger' => $validated['arranger'] ?? null,
        ]);

        foreach ($validated['parts'] ?? [] as $i => $part) {
            $pdfFile = $request->file("parts.{$i}.pdf");
            $path = null;
            if ($pdfFile) {
                $path = $pdfFile->store("scores/{$score->id}", config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores');
            }

            $matchedInstrumentId = null;
            $foundInstrument = \App\Models\Instrument::where('name', $part['instrument'])->first();
            if ($foundInstrument) {
                $matchedInstrumentId = $foundInstrument->id;
            }

            if (!empty($part['instrument']) && !empty($part['original_parsed_instrument'])) {
                $chosenName = strtolower(trim($part['instrument']));
                $parsedName = strtolower(trim($part['original_parsed_instrument']));
                $parsedName = preg_replace('/\s*\d+$/', '', $parsedName); // strip trailing numbers for general mapping e.g "percussie 1" -> "percussie"

                if (!empty($parsedName) && $chosenName !== $parsedName) {
                    if ($foundInstrument) {
                        InstrumentAlias::updateOrCreate([
                            'alias' => $parsedName
                        ], [
                            'instrument_id' => $foundInstrument->id
                        ]);
                    }
                }
            }

            // Create part even if no PDF uploaded yet
            $score->parts()->create([
                'instrument'      => $part['instrument'],
                'instrument_id'   => $matchedInstrumentId,
                'pdf_path'        => $path,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'score'   => $score
            ]);
        }

        return redirect()->route('beheer.bladmuziek.index')->with('success', 'Bladmuziek aangemaakt.');
    }

    public function edit($id): Response
    {
        $score = Score::findOrFail($id);
        
        $score->load('parts');
        $instruments = Instrument::with('aliases')->orderBy('display_order')->get();

        $data = [
            'score' => [
                'id'         => $score->id,
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
                'aliases'       => $i->aliases->pluck('alias')->toArray(),
            ])->toArray(),
        ];


        return Inertia::render('Admin/Scores/Edit', $data);
    }

    public function update(Request $request, $id)
    {
        $score = Score::findOrFail($id);
        
        $validated = $request->validate([
            'title'                  => 'required|string|max:255',
            'composer'               => 'required|string|max:255',
            'arranger'               => 'nullable|string|max:255',
            'removed_part_ids'       => 'nullable|array',
            'removed_part_ids.*'                 => 'integer|exists:score_parts,id',
            'new_parts'                          => 'nullable|array',
            'new_parts.*.instrument'             => 'required|string|max:255',
            'new_parts.*.original_parsed_instrument' => 'nullable|string|max:255',
            'new_parts.*.pdf'                    => 'nullable|file|mimes:pdf|max:20480',
        ], [
            'new_parts.*.instrument.required' => 'Kies een instrument voor elke bijgevoegde PDF.',
        ]);

        $score->update([
            'title'    => $validated['title'],
            'composer' => $validated['composer'],
            'arranger' => $validated['arranger'] ?? null,
        ]);

        if (!empty($validated['removed_part_ids'])) {
            $partsToDelete = ScorePart::whereIn('id', $validated['removed_part_ids'])
                ->where('score_id', $score->id)
                ->get();
            $disk = config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores';
            foreach ($partsToDelete as $part) {
                Storage::disk($disk)->delete($part->pdf_path);
                $part->delete();
            }
        }

        foreach ($validated['new_parts'] ?? [] as $i => $part) {
            $pdfFile = $request->file("new_parts.{$i}.pdf");
            $path = null;
            if ($pdfFile) {
                $path = $pdfFile->store("scores/{$score->id}", config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores');
            }

            $matchedInstrumentId = null;
            $foundInstrument = \App\Models\Instrument::where('name', $part['instrument'])->first();
            if ($foundInstrument) {
                $matchedInstrumentId = $foundInstrument->id;
            }

            if (!empty($part['instrument']) && !empty($part['original_parsed_instrument'])) {
                $chosenName = strtolower(trim($part['instrument']));
                $parsedName = strtolower(trim($part['original_parsed_instrument']));
                $parsedName = preg_replace('/\s*\d+$/', '', $parsedName); // strip trailing numbers

                if (!empty($parsedName) && $chosenName !== $parsedName) {
                    $foundInstrument = \App\Models\Instrument::where('name', $part['instrument'])->first();
                    if ($foundInstrument) {
                        InstrumentAlias::updateOrCreate([
                            'alias' => $parsedName
                        ], [
                            'instrument_id' => $foundInstrument->id
                        ]);
                    }
                }
            }

            $score->parts()->create([
                'instrument'      => $part['instrument'],
                'instrument_id'   => $matchedInstrumentId,
                'pdf_path'        => $path,
            ]);
        }

        return redirect()->route('beheer.bladmuziek.index')->with('success', 'Bladmuziek bijgewerkt.');
    }

    public function destroy(Score $score): RedirectResponse
    {
        $score->load('parts');
        $disk = config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores';
        foreach ($score->parts as $part) {
            Storage::disk($disk)->delete($part->pdf_path);
        }
        $score->delete();

        return redirect()->route('beheer.bladmuziek.index')->with('success', 'Bladmuziek verwijderd.');
    }

    public function download(Score $score, \App\Models\ScorePart $part)
    {
        abort_if($part->score_id !== $score->id, 404);

        $filename = $part->original_filename ? pathinfo($part->original_filename, PATHINFO_FILENAME) : ($score->title . ' - ' . ($part->instrument?->name ?? $part->instrument));
        
        if (!$part->original_filename && $part->part_number && $part->part_number > 1) {
            $filename .= ' ' . $part->part_number;
        }

        $disk = config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores';
        return Storage::disk($disk)->download($part->pdf_path, $filename . '.pdf');
    }

    public function view(Score $score, \App\Models\ScorePart $part)
    {
        abort_if($part->score_id !== $score->id, 404);
        $disk = config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores';
        return Storage::disk($disk)->response($part->pdf_path);
    }
}
