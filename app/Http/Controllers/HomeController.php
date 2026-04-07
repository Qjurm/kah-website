<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Concert;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $upcomingConcerts = Concert::where('date', '>=', now()->toDateString())
            ->orderBy('date', 'asc')
            ->limit(6)
            ->get(['id', 'title', 'date', 'location', 'photo_path', 'is_current']);

        return Inertia::render('Home', [
            'upcomingConcerts' => $upcomingConcerts,
        ]);
    }
}
