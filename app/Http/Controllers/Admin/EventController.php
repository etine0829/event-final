<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Event;
use App\Models\Admin\Category;
use App\Models\User;
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

        } else if (Auth::user()->hasRole('event_manager')) {

            $validatedData = $request->validate([
                'event_name' => 'required|string',
                // 'event_date' => 'required|date_format:Y-m-d\TH:i',
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

        } else if (Auth::user()->hasRole('event_manager')) {
            
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
        }
    }

    public function destroy(Event $event)
    {
        if (Auth::user()->hasRole('admin')) {

            // Check if there are any associated records
            if ($event->user()->exists() || $event->categories()->exists() || $event->participant()->exists()) {
                return redirect()->route('admin.event.index')->with('error', 'Cannot delete event because it has associated data.');
            }

            // If no associated records, proceed with deletion
            $event->delete();

            return redirect()->route('admin.event.index')->with('success', 'Event deleted successfully.');

        } else if (Auth::user()->hasRole('event_manager')) {
            
            // Check if there are any associated records
            if ($event->user()->exists() || $event->participant()->exists()) {
                return redirect()->route('event_manager.event.index')->with('error', 'Cannot delete event because it has associated data.');
            }

            // If no associated records, proceed with deletion
            $event->delete();

            return redirect()->route('event_manager.event.index')->with('success', 'Event deleted successfully.');

        }
    }

    public function deleteAll(Request $request)
    {
        
        
         $count = Event::count();

        if ($count === 0) {
            return redirect()->route('admin.event.index')->with('info', 'There are no events to delete.');
        }

        try {
            // Use a transaction to ensure data integrity
            \DB::beginTransaction();

            // Delete related data in other tables first (e.g., staff)
            Event::whereHas('event')->delete();

            // Now you can delete the event
            Event::truncate();

            \DB::commit();

            return redirect()->route('admin.event.index')->with('success', 'All events deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollback();

            // Log the error or handle it appropriately
            return redirect()->route('admin.event.index')->with('error', 'Cannot delete events because they have associated categories.');
        }

        
    }


}
