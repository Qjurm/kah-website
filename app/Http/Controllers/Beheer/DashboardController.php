<?php

namespace App\Http\Controllers\Beheer;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use App\Models\Score;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'scores' => Score::count(),
                'concerts' => Concert::count(),
                'users' => User::count(),
                'currentConcert' => Concert::where('is_current', true)->first()?->title,
            ],
        ]);
    }
}
