<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstrumentController extends Controller
{
    /**
     * Store a newly created instrument (API endpoint for inline creation).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instruments,name',
        ]);

        $instrument = Instrument::create([
            'name' => $validated['name'],
            'display_order' => Instrument::max('display_order') + 1,
        ]);

        return response()->json([
            'id' => $instrument->id,
            'name' => $instrument->name,
            'display_order' => $instrument->display_order,
        ], 201);
    }
}
