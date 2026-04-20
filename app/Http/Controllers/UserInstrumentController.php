<?php

namespace App\Http\Controllers;

use App\Models\Instrument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserInstrumentController extends Controller
{
    public function edit(): Response
    {
        $user = Auth::user();
        $user->load('instruments');
        $allInstruments = Instrument::orderBy('display_order')->get();
        $instrumentSections = \App\Models\InstrumentSection::with('instruments')->orderBy('display_order')->get();
        $unsortedInstruments = Instrument::whereNull('section_id')->orderBy('display_order')->get();

        if ($unsortedInstruments->isNotEmpty()) {
            $overigSection = $instrumentSections->where('name', 'Overig')->first();
            
            if ($overigSection) {
                // Merge with existing Overig section
                $overigSection->setRelation('instruments', $overigSection->instruments->concat($unsortedInstruments));
            } else {
                // Create a virtual Overig section if it doesn't exist in DB
                $instrumentSections->push((object)[
                    'id' => 0,
                    'name' => 'Overig',
                    'instruments' => $unsortedInstruments
                ]);
            }
        }
        
        // Get next concert
        $nextConcert = \App\Models\Concert::where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->with(['scores.parts.instrument'])
            ->first();
            
        // Get parts for user's currently selected instruments
        $myParts = [];
        $selectedIds = $user->instruments->pluck('id')->toArray();
        
        if ($nextConcert && !empty($selectedIds)) {
            foreach ($nextConcert->scores as $score) {
                $parts = $score->parts->whereIn('instrument_id', $selectedIds);
                foreach ($parts as $part) {
                    $myParts[] = [
                        'score_id' => $score->id,
                        'score_title' => $score->title,
                        'score_composer' => $score->composer,
                        'part_id' => $part->id,
                        'instrument' => $part->instrument?->name ?? $part->instrument,
                    ];
                }
            }
        }

        return Inertia::render('Instruments/Edit', [
            'userInstruments' => $selectedIds,
            'primaryInstrumentId' => $user->instruments->where('pivot.is_primary', true)->first()?->id,
            'allInstruments' => $allInstruments,
            'instrumentSections' => $instrumentSections,
            'nextConcert' => $nextConcert ? [
                'id' => $nextConcert->id,
                'title' => $nextConcert->title,
                'date' => $nextConcert->date?->format('Y-m-d'),
                'location' => $nextConcert->location,
            ] : null,
            'myParts' => $myParts,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'instrument_ids' => 'required|array|min:1',
            'instrument_ids.*' => 'exists:instruments,id',
            'primary_instrument_id' => 'nullable|exists:instruments,id',
        ], [
            'instrument_ids.required' => 'Kies ten minste één instrument.',
            'instrument_ids.min' => 'Kies ten minste één instrument.',
        ]);

        $syncData = [];
        foreach ($validated['instrument_ids'] as $id) {
            $syncData[$id] = ['is_primary' => ($id == ($validated['primary_instrument_id'] ?? null))];
        }

        $user->instruments()->sync($syncData);

        return redirect()->route('mijn-instrumenten.edit')->with('success', 'Je instrumenten zijn bijgewerkt.');
    }
}
