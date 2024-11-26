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
use Illuminate\Support\Facades\Storage;



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
        // Custom validation for participant_name
        if (!preg_match('/^[a-zA-Z\s]+$/', $request->input('participant_name'))) {
            return redirect()->back()->withErrors(['participant_name' => 'The participant name must only contain letters and spaces, and cannot have any numbers or special characters.']);
        }




        // Check if the participant already exists for this event
        $existingParticipant = Participant::where('event_id', $request->input('event_id'))
            ->where('group_id', $request->input('group_id'))
            ->where('participant_name', $request->input('participant_name'))
            ->first();




        if ($existingParticipant) {
            return redirect()->back()->with('error', 'This participant is already registered in the selected group and event.');
        }




        $validatedData = $request->validate([
            'event_id' => 'required|exists:events,id',
            'group_id' => 'nullable|string|max:255',
            'participant_photo' => 'nullable|image|max:2048',
            'participant_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'participant_gender' => 'required|string|max:255',
            'participant_comment' => 'nullable|string|max:255',
        ]);




        // Handle the file upload
        if ($request->hasFile('participant_photo')) {
            $fileNameWithExt = $request->file('participant_photo')->getClientOriginalName();
            $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            $extension = $request->file('participant_photo')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;




            // Save the file to 'public/participant_photo' and store the path in $fileNameToStore
            $path = $request->file('participant_photo')->storeAs('public/participant_photo', $fileNameToStore);
            $validatedData['participant_photo'] = $fileNameToStore;
        } else {
            $validatedData['participant_photo'] = 'user.png'; // Default image
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
        'participant_name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
        'participant_gender' => 'required|in:male,female',
        'participant_comment' => 'nullable|string|max:255',
        'participant_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);




    // Check if there's a new photo uploaded
    if ($request->hasFile('participant_photo')) {
        // Delete old photo if exists
        // Delete old photo if exists
        if ($participant->participant_photo && Storage::exists('public/participant_photo/' . $participant->participant_photo)) {
            Storage::delete('public/participant_photo/' . $participant->participant_photo);
        }
   
        // Save the new photo
        $fileNameWithExt = $request->file('participant_photo')->getClientOriginalName();
        $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('participant_photo')->getClientOriginalExtension();
        $fileNameToStore = $filename . '_' . time() . '.' . $extension;
   
        $path = $request->file('participant_photo')->storeAs('public/participant_photo', $fileNameToStore);
        $validatedData['participant_photo'] = $fileNameToStore;
    } else {
        // Keep old photo if no new one is uploaded
        // Keep old photo if no new one is uploaded
        $validatedData['participant_photo'] = $participant->participant_photo;
    }
   




    // Update participant data
    try {
        $participant->update($validatedData);
        return redirect()->route('admin.participant.index')
            ->with('success', 'Participant updated successfully.');
    } catch (\Exception $e) {
        Log::error('Failed to update participant: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Failed to update participant.');
    }




    return redirect()->route('admin.participant.index')->with('success', 'Participant updated successfully.');
}








public function destroy(Participant $participant)
{
    if (Auth::user()->hasRole('admin')) {
        // Check for associated records (e.g., scores, events, etc.)
        if ($participant->scorecards()->exists()) { // Example: Replace 'scores' with the actual relationship
            return redirect()->route('admin.participant.index')
                ->with('error', 'Participant cannot be deleted because they have associated records.');
        }




        // Proceed with deletion if no associated records
        $participant->delete();




        return redirect()->route('admin.participant.index')
            ->with('success', 'Participant deleted successfully.');
    }




    // Optional: Handle unauthorized access
    return redirect()->route('admin.participant.index')
        ->with('error', 'You do not have permission to perform this action.');
}




    public function deleteAll(Request $request)
    {
        try {
            \DB::transaction(function () {
                Participant::all()->each(function ($participant) {
                    $participant->scorecard()->delete();
                    $participant->delete();
                });
            });




            return redirect()->route('admin.participant.index')->with('success', 'All participants deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete participants: ' . $e->getMessage());
        }
    }




 
  }
 
 





