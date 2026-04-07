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
        $scores = Score::with(['parts.instrument'])->orderBy('number')->get();

        $currentConcert = Concert::where('is_current', true)
            ->with(['scores' => function ($query) {
                $query->orderBy('number')->with('parts.instrument');
            }])
            ->first();

        $userInstruments = $user ? $user->instruments()->orderBy('display_order')->get() : [];
        $primaryInstrument = $user ? $user->primaryInstrument() : null;

        return Inertia::render('Muziek/Index', [
            'scores' => $scores,
            'currentConcert' => $currentConcert,
            'userInstruments' => $userInstruments,
            'primaryInstrument' => $primaryInstrument,
        ]);
    }

    public function download(Score $score, ScorePart $part): StreamedResponse
    {
        abort_if($part->score_id !== $score->id, 404);

        $filename = $part->instrument?->name ?? $part->instrument;
        if ($part->part_number && $part->part_number > 1) {
            $filename .= ' ' . $part->part_number;
        }

        return Storage::disk('public')->download($part->pdf_path, $filename . '.pdf');
    }
}
