<?php

namespace App\Http\Controllers\Beheer;

use App\Http\Controllers\Controller;
use App\Models\AgendaItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AgendaController extends Controller
{
    public function index(): Response
    {
        $items = AgendaItem::orderBy('start_date', 'desc')->get();

        return Inertia::render('Admin/Agenda/Index', [
            'items' => $items,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Agenda/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'type'        => 'required|in:concert,rehearsal,meeting,other',
            'location'    => 'nullable|string|max:255',
        ]);

        $data['is_public'] = false;

        AgendaItem::create($data);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda-item aangemaakt.');
    }

    public function edit(AgendaItem $agenda): Response
    {
        return Inertia::render('Admin/Agenda/Edit', [
            'item' => $agenda,
        ]);
    }

    public function update(Request $request, AgendaItem $agenda): RedirectResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'type'        => 'required|in:concert,rehearsal,meeting,other',
            'location'    => 'nullable|string|max:255',
        ]);

        $data['is_public'] = false;

        $agenda->update($data);

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda-item bijgewerkt.');
    }

    public function destroy(AgendaItem $agenda): RedirectResponse
    {
        $agenda->delete();

        return redirect()->route('admin.agenda.index')->with('success', 'Agenda-item verwijderd.');
    }
}
