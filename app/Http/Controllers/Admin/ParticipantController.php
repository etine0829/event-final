<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Models\Admin\Participant;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;


class ParticipantController extends Controller
{
     //
     public function index()
     {
         return view('Admin.participant.index');
     }
 
     /**
      * Show the form for creating a new resource.
      */
     public function create()
     {
         //
        
     }
 
     /**
      * Store a newly created resource in storage.
      */
      public function store(Request $request)
      {
          if (Auth::user()->hasRole('admin')) {
             

            // Custom validation for participant_name to ensure no numbers or special characters
            if (!preg_match('/^[a-zA-Z\s]+$/', $request->input('participant_name'))) {
                return redirect()->back()->withErrors(['participant_name' => 'The participant name must only contain letters and spaces, and cannot have any numbers or special characters.']);
            }
            // Check if the participant already exists for this event
            $existingParticipant = Participant::where('event_id', $request->input('event_id'))
            ->where('group_id', $request->input('group_id'))
            ->where('participant_name', $request->input('participant_name'))
            ->first();
    
            if ($existingParticipant) {
                // Redirect back with an error message if the participant already exists in the same group and event
                return redirect()->back()->with('error', 'This participant is already registered in the selected group and event.');
            }
           
      
            $validatedData = $request->validate([
                'event_id' => 'required|exists:events,id',
                'group_id' => 'nullable|string|max:255',
                'participant_photo' => 'nullable|image|max:2048',
                'participant_name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/', // Ensure no numbers or special characters are allowed
                ],
                'participant_gender' => 'required|string|max:255',
                'participant_comment' => 'nullable|string|max:255',
              ]);
              
              if ($request->hasFile('participant_photo')) {
                  $fileNameWithExt = $request->file('participant_photo')->getClientOriginalName();
                  $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
                  $extension = $request->file('participant_photo')->getClientOriginalExtension();
                  $fileNameToStore = $filename . '_' . time() . '.' . $extension;
                  $path = $request->file('participant_photo')->storeAs('public/participant_photo', $fileNameToStore);
                  $validatedData['participant_photo'] = $fileNameToStore;
              } else {
                  $validatedData['participant_photo'] = 'user.png';
              }
      
              try {
                  Participant::create($validatedData);
                  return redirect()->route('admin.participant.index')
                      ->with('success', 'Participant created successfully.');
              } catch (Exception $e) {
                  Log::error('Participant creation failed: ' . $e->getMessage());
                  return redirect()->back()->with('error', 'Failed to create participant.');
              }
        } else {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
    }
      
    public function update(Request $request, Participant $participant)
{
    $validatedData = $request->validate([
        'group_id' => 'nullable|exists:group,id',
        'participant_name' => [
            'required',
            'string',
            'max:255',
            'regex:/^[a-zA-Z\s]+$/',
        ],
        'participant_gender' => 'required|in:male,female',
        'participant_comment' => 'nullable|string|max:255',
        'participant_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Check if there's a new photo uploaded
    if ($request->hasFile('participant_photo')) {
        // If a new photo is uploaded, delete the old one if it exists
        if ($participant->participant_photo && Storage::exists('public/participant_photo/' . $participant->participant_photo)) {
            Storage::delete('public/participant_photo/' . $participant->participant_photo);
        }

        // Store the new photo and update the validated data
        $validatedData['participant_photo'] = $request->file('participant_photo')->store('participant_photo', 'public');
    } else {
        // If no new photo, keep the old photo
        $validatedData['participant_photo'] = $participant->participant_photo;
    }

    // Update participant data
    $participant->update($validatedData);

    return redirect()->route('admin.participant.index')->with('success', 'Participant updated successfully.');
}

public function destroy(Participant $participant)
    {
        if (Auth::user()->hasRole('admin')) {


            // If no associated records, proceed with deletion
            $participant->delete();

            return redirect()->route('admin.participant.index')->with('success', 'Participant deleted successfully.');
        }
    }


    public function deleteAll(Request $request)
    {       
         $count = Participant::count();

        if ($count === 0) {
            return redirect()->route('admin.participant.index')->with('info', 'There are no participant to delete.');
        }

        try {
            // Use a transaction to ensure data integrity
            \DB::beginTransaction();

            // Delete related data in other tables first (e.g., staff)
            Participant::whereHas('participant')->delete();

            // Now you can delete the event
            Participant::truncate();

            \DB::commit();

            return redirect()->route('admin.participant.index')->with('success', 'All participant deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollback();

            // Log the error or handle it appropriately
            return redirect()->route('admin.participant.index')->with('error', 'Cannot delete participant because they have associated data.');
        }
    }
 
  }
 
 