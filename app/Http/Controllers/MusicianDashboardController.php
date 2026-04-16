<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MusicianDashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();
        
        // Get primary instrument
        $primaryInstrument = $user->primaryInstrument();
        
        // Get next concert
        $nextConcert = Concert::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->with(['scores.parts.instrument'])
            ->first();
        
        // Get my parts for next concert (all assigned instruments)
        $myParts = [];
        $instrumentIds = $user->instruments()->pluck('instruments.id')->toArray();

        if ($nextConcert && !empty($instrumentIds)) {
            foreach ($nextConcert->scores as $score) {
                $matchedParts = $score->parts
                    ->whereIn('instrument_id', $instrumentIds);
                
                foreach ($matchedParts as $myPart) {
                    $myParts[] = [
                        'score_id' => $score->id,
                        'score_title' => $score->title,
                        'score_composer' => $score->composer,
                        'part_id' => $myPart->id,
                        'part_number' => $myPart->part_number,
                        'instrument' => $myPart->instrument?->name ?? $myPart->instrument,
                    ];
                }
            }
        }
        
        // Get recent activity stats
        $recentStats = [
            'newScores' => \App\Models\Score::where('created_at', '>=', now()->subMonth())
                ->count(),
            'upcomingConcerts' => Concert::where('date', '>=', now())->count(),
        ];
        
        return Inertia::render('Dashboard', [
            'primaryInstrument' => $primaryInstrument,
            'nextConcert' => $nextConcert,
            'myParts' => $myParts,
            'recentStats' => $recentStats,
            'allInstruments' => $user->instruments()->orderBy('display_order')->get(),
        ]);
    }
}
