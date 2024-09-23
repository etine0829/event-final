<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Event;
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
                // 'event_date' => 'required|date_format:Y-m-d\TH:i',
                'venue' => 'required|string',
                'type_of_scoring' => 'required|string',
            ]);

            // Event::create($validatedData);
            $event = Event::create($validatedData);

            return redirect()->route('admin.event.index')->with('success', 'Event created successfully.');

        // } else if (Auth::user()->hasRole('event_manager')) {

        //     $validatedData = $request->validate([
        //         'event_name' => 'required|string',
        //         // 'event_date' => 'required|date_format:Y-m-d\TH:i',
        //         'venue' => 'required|string',
        //         'type_of_scoring' => 'required|string',
        //     ]);

        //     $event = Event::create($validatedData);


        //     return redirect()->route('event_manager.event.index')
        //         ->with('success', 'School created successfully.');
        // }
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
        if (Auth::user()->hasRole('admin')) {

            $validatedData = $request->validate([
                'event_name' => 'required|string|max:255',
                // 'event_date' => 'required|date_format:Y-m-d\TH:i',
                'venue' => 'required|string|max:255',
                'type_of_scoring' => 'required|in:points,ranking',
            ]);

            // $event->update($validated);

            // Check for changes
            $changes = false;
            foreach ($validatedData as $key => $value) {
                if ($event->$key !== $value) {
                    $changes = true;
                    break;
                }
            }

            if (!$changes) {
                return redirect()->route('admin.event.index')->with('info', 'No changes were made.');
            }

            $event->update($validatedData);

            return redirect()->route('admin.event.index')->with('success', 'Event updated successfully.');

        // } else if (Auth::user()->hasRole('event_manager')) {
            
        //     $validated = $request->validate([
        //         'event_name' => 'required|string|max:255|events,event_name,' . $event->id,
        //         // 'event_date' => 'required|date_format:Y-m-d\TH:i',
        //         'venue' => 'required|string|max:255',
        //         'type_of_scoring' => 'required|string|max:255|in:points,rankingHL,rankingLH',
        //     ]);

        //     // $event->update($validated);

        //     // Check for changes
        //     $changes = false;
        //     foreach ($validatedData as $key => $value) {
        //         if ($event->$key !== $value) {
        //             $changes = true;
        //             break;
        //         }
        //     }

        //     if (!$changes) {
        //         return redirect()->route('event_manager.event.index')->with('info', 'No changes were made.');
        //     }

        //     $school->update($validatedData);

        //     return redirect()->route('event_manager.event.index')->with('success', 'School updated successfully.');
        // }
        }
    }


    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
