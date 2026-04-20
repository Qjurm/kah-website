<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstrumentSection;
use Illuminate\Http\Request;

class InstrumentSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sections = InstrumentSection::withCount('instruments')->orderBy('display_order')->get();
        return \Inertia\Inertia::render('Admin/InstrumentSections/Index', [
            'sections' => $sections
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instrument_sections,name',
            'display_order' => 'nullable|integer',
        ]);

        InstrumentSection::create($validated);

        return redirect()->back()->with('success', 'Sectie aangemaakt.');
    }

    public function update(Request $request, InstrumentSection $instrumentSection)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:instrument_sections,name,' . $instrumentSection->id,
            'display_order' => 'nullable|integer',
        ]);

        $instrumentSection->update($validated);

        return redirect()->back()->with('success', 'Sectie bijgewerkt.');
    }

    public function destroy(InstrumentSection $instrumentSection)
    {
        if ($instrumentSection->instruments()->exists()) {
            return redirect()->back()->with('error', 'Deze sectie kan niet verwijderd worden omdat er nog instrumenten aan gekoppeld zijn.');
        }

        $instrumentSection->delete();

        return redirect()->back()->with('success', 'Sectie verwijderd.');
    }
}
