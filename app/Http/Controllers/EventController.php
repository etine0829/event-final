<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    //
    public function index()
    {
        $event = Event::all();
        return view('event', ['events' => $event]);

    }
    public function addEvent(Request $request)
    {
        // Validate incoming request data
        $validatedData = $request->validate([
            'event_name' => 'required|string',
            'event_date' => 'required|date_format:Y-m-d\TH:i',
            'venue' => 'required|string',
            'type_of_scoring' => 'required|string',
        ]);

        // Attempt to create the event
        Event::create($validatedData);

        return redirect()->back()->with('success', 'Event added successfully!');

    }
        
    // public function manageEvent($id)
    //     {
    //         $event = Event::find($id);  // Fetch the event by its ID
    //         if (!$event) {
    //             return redirect()->back()->with('error', 'Event not found.');
    //         }
            
    //         return view('manage_event', ['event' => $event]);
    //     }


}
