<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class EventController extends Controller
{
    public function index()
    {
        // $events = Event::all();
        return view('Admin.event.index');
    }

    public function create()
    {

    }

    public function store(Request $request)
    {
        if (Auth::user()->hasRole('admin')) {


            $validatedData = $request->validate([
                'event_name' => 'required|string',
                'event_date' => 'required|date_format:Y-m-d\TH:i',
                'venue' => 'required|string',
                'type_of_scoring' => 'required|string',
            ]);

            // Event::create($validatedData);
            $event = Event::create($validatedData);

            return redirect()->route('admin.event.index')->with('success', 'Event created successfully.');

        } else if (Auth::user()->hasRole('event_manager')) {

            $validatedData = $request->validate([
                'event_name' => 'required|string',
                'event_date' => 'required|date_format:Y-m-d\TH:i',
                'venue' => 'required|string',
                'type_of_scoring' => 'required|string',
            ]);

            $event = Event::create($validatedData);


            return redirect()->route('event_manager.event.index')
                ->with('success', 'School created successfully.');
        }

    }
    public function show(Event $event)
    {
        // return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        // return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'event_name' => 'required|string',
            'event_date' => 'required|date_format:Y-m-d\TH:i',
            'venue' => 'required|string',
            'type_of_scoring' => 'required|string',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
