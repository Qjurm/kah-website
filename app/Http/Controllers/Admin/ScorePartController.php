<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Score;
use App\Models\ScorePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Instrument;
use App\Models\InstrumentAlias;

class ScorePartController extends Controller
{
    public function store(Request $request, Score $score)
    {
        $validated = $request->validate([
            'instrument' => 'required|string',
            'pdf' => 'required', // Relaxed for debugging: removed 'file' and 'mimes'
        ]);

        $file = $request->file('pdf');
        $originalName = $file->getClientOriginalName();
        $disk = config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores';
        $path = $file->store("scores/{$score->id}", $disk);

        // Resolve instrument_id (handle comma-separated names)
        $matchedInstrumentId = null;
        $instrumentNames = array_map('trim', explode(', ', $validated['instrument']));
        $firstInstrument = array_shift($instrumentNames);
        
        if ($firstInstrument) {
            $foundInstrument = Instrument::where('name', $firstInstrument)->first();
            if ($foundInstrument) {
                $matchedInstrumentId = $foundInstrument->id;
            }
        }

        $part = $score->parts()->create([
            'instrument'        => $validated['instrument'],
            'instrument_id'     => $matchedInstrumentId,
            'pdf_path'          => $path,
            'original_filename' => $originalName,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'part'    => $part
            ]);
        }

        return redirect()->route('beheer.bladmuziek.edit', $score)->with('success', 'Partij toegevoegd.');
    }

    public function destroy(ScorePart $part)
    {
        $disk = config('filesystems.disks.scores.driver') === 'local' ? 'public' : 'scores';
        Storage::disk($disk)->delete($part->pdf_path);
        
        $scoreId = $part->score_id;
        $part->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('beheer.bladmuziek.edit', $scoreId)->with('success', 'Partij verwijderd.');
    }
}
