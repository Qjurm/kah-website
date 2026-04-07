<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Get pending users
        $pendingUsers = User::where('approved', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get recent scores
        $recentScores = Score::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get upcoming concerts
        $upcomingConcerts = Concert::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->limit(3)
            ->get();

        // Instrument breakdown by category
        $caseStatement = "CASE 
            WHEN name IN ('Piccolo', 'Dwarsfluit', 'Hobo', 'Klarinet', 'Basklarinet', 'Fagot', 'Altsaxofoon', 'Tenorsaxofoon', 'Baritonsaxofoon') THEN 'woodwinds'
            WHEN name IN ('Trompet', 'Hoorn', 'Trombone', 'Bastrombone', 'Euphonium', 'Tuba', 'Contrabas') THEN 'brass'
            ELSE 'percussion'
        END";
        
        $instrumentBreakdown = DB::table('instruments')
            ->selectRaw("$caseStatement as category, COUNT(*) as count")
            ->groupBy(DB::raw($caseStatement))
            ->pluck('count', 'category')
            ->toArray();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'scores' => Score::count(),
                'concerts' => Concert::count(),
                'users' => User::count(),
                'pendingUsers' => User::where('approved', false)->count(),
                'currentConcert' => Concert::where('is_current', true)->first()?->title,
            ],
            'pendingUsers' => $pendingUsers,
            'recentScores' => $recentScores,
            'upcomingConcerts' => $upcomingConcerts,
            'instrumentBreakdown' => $instrumentBreakdown,
        ]);
    }
}
