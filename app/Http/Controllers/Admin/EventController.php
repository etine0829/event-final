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
        // Define common validation rules
        $validatedData = $request->validate([
            'event_name' => 'required|string',
            'venue' => 'required|string',
            'type_of_scoring' => 'required|string',
        ]);

        $event = Event::create($validatedData);

        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.event.index')->with('success', 'Event created successfully.');
        } else if (Auth::user()->hasRole('event_manager')) {
            return redirect()->route('event_manager.event.index')
                ->with('success', 'Event created successfully.');
        }
    }


    public function update(Request $request, Event $event)
    {

        $validatedData = $request->validate([
            'event_name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'type_of_scoring' => 'required|in:points,ranking(H-L),ranking(L-H)',
        ]);

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

        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.event.index')->with('success', 'Event updated successfully.');
        } else if (Auth::user()->hasRole('event_manager')) {
            return redirect()->route('event_manager.event.index')->with('success', 'Event updated successfully.');
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
        // Get the count of events
        $count = Event::count();

        // Check the user's role and handle the event count logic
        if ($count === 0) {
            if (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.event.index')->with('info', 'There are no events to delete.');
            } elseif (Auth::user()->hasRole('event_manager')) {
                return redirect()->route('event_manager.event.index')->with('info', 'There are no events to delete.');
            }
        }

        try {
            // Use a transaction to ensure data integrity
            \DB::beginTransaction();

            // Delete related data in other tables first (e.g., staff)
            Event::whereHas('event')->delete();

            // Now you can delete the event
            Event::truncate();

            \DB::commit();

            // Redirect based on the user's role after successful deletion
            if (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.event.index')->with('success', 'All events deleted successfully.');
            } elseif (Auth::user()->hasRole('event_manager')) {
                return redirect()->route('event_manager.event.index')->with('success', 'All events deleted successfully.');
            }

        } catch (\Exception $e) {
            \DB::rollback();

            // Log the error or handle it appropriately
            if (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.event.index')->with('error', 'Cannot delete events because they have associated categories.');
            } elseif (Auth::user()->hasRole('event_manager')) {
                return redirect()->route('event_manager.event.index')->with('error', 'Cannot delete events because they have associated categories.');
            }
        }
    }
}
