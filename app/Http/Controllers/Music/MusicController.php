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
        $scores = Score::with('parts')->orderBy('number')->get();

        $currentConcert = Concert::where('is_current', true)
            ->with(['scores' => function ($query) {
                $query->orderBy('number')->with('parts');
            }])
            ->first();

        return Inertia::render('Muziek/Index', [
            'scores' => $scores,
            'currentConcert' => $currentConcert,
        ]);
    }

    public function download(Score $score, ScorePart $part): StreamedResponse
    {
        abort_if($part->score_id !== $score->id, 404);

        return Storage::disk('public')->download($part->pdf_path, $part->instrument . '.pdf');
    }
}
