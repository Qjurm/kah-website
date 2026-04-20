<?php

namespace App\Http\Controllers\Music;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use App\Models\Score;
use App\Models\ScorePart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MusicController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $scores = Score::with(['parts.instrument'])->orderBy('title')->get();

        $currentConcert = Concert::where('is_current', true)
            ->with(['scores' => function ($query) {
                $query->orderBy('title')->with('parts.instrument');
            }])
            ->first();

        $userInstruments = $user ? $user->instruments()->orderBy('display_order')->get() : [];
        $primaryInstrument = $user ? $user->primaryInstrument() : null;
        
        $myRelevantParts = [];
        if ($currentConcert && $user) {
            $instrumentIds = $userInstruments->pluck('id')->toArray();
            foreach ($currentConcert->scores as $score) {
                foreach ($score->parts as $part) {
                    if (in_array($part->instrument_id, $instrumentIds)) {
                        $myRelevantParts[$score->id][] = [
                            'id' => $part->id,
                            'instrument_id' => $part->instrument_id,
                            'instrument' => $part->instrument,
                            'part_number' => $part->part_number,
                            'original_filename' => $part->original_filename,
                        ];
                    }
                }
            }
        }

        return Inertia::render('Muziek/Index', [
            'scores' => $scores,
            'currentConcert' => $currentConcert,
            'myRelevantParts' => $myRelevantParts,
            'userInstruments' => $userInstruments,
            'primaryInstrument' => $primaryInstrument,
        ]);
    }

    public function download(Score $score, ScorePart $part, \App\Actions\DownloadScorePartAction $downloadAction): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return $downloadAction->execute($score, $part);
    }

    public function view(Score $score, ScorePart $part)
    {
        abort_if($part->score_id !== $score->id, 404);
        $disk = config('filesystems.scores.driver') === 'local' ? 'public' : 'scores';
        return Storage::disk($disk)->response($part->pdf_path);
    }
}
