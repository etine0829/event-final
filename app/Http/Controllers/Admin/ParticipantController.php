<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Admin\Event;
use App\Models\Admin\Participant;


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
 
        //  // // Log the incoming request data for debugging
        //  \Log::info('Category Store Request Data:', $request->all());
 
         $validatedData = $request->validate([
            'event_id' => 'required|exists:events,id',
            'participant_photo' => 'image|max:2048',
            'participant_name' => 'required|string|max:255',
            'participant_gender' => 'required|string|max:255',
            'participant_group' => 'nullable|string|max:255',
            'participant_comment' => 'nullable|string|max:255',
             
         ]);

         // Handle file upload if 'course_photo' is present
         
         // Attempt to create the participant record
         try {
             Participant::create($validatedData);
             return redirect()->route('admin.participant.index')
                 ->with('success', 'Participant created successfully.');
         } catch (\Exception $e) {
             return redirect()->route('admin.participant.index')->with('error', 'Failed to create participant: ' . $e->getMessage());
         }
     }
 }
 
            
         //     $validatedData = $request->validate([
         //         'school_id' => 'required|exists:schools,id',
         //         'department_id' => [
         //             'required',
         //             'string',
         //             'max:255',
         //             Rule::unique('departments')->where(function ($query) use ($request) {
         //                 return $query->where('school_id', $request->school_id);
         //             })->ignore($request->department_id, 'department_id'), // Ensure to ignore by 'department_id'
         //         ],
         //         'department_abbreviation' => 'required|string|max:255',
         //         'department_name' => 'required|string|max:255',
         //         'dept_identifier' => 'required|string|max:255',
         //     ], [
         //         'department_id.unique' => 'The department ID is not valid.',
         //     ]);
 
         //     // Attempt to create the Department record
         //     try {
         //         Department::create($validatedData);
                 
         //         // If creation succeeds, redirect with success message
         //         return redirect()->route('staff.department.index')
         //             ->with('success', 'Department created successfully.');
         //     } catch (\Exception $e) {
         //         // If an exception occurs (unlikely in normal validation flow)
         //         // Handle any specific errors or logging as needed
         //         // You can redirect back with an error message or do other error handling
         //         return  redirect()->route('staff.department.index')->with('error','The department ID is already taken in this school.');
         //     }
     
 //     /**
 //      * Display the specified resource.
 //      */
 //     public function show(string $id)
 //     {
 //         //
 //     }
 
 //     /**
 //      * Show the form for editing the specified resource.
 //      */
 //     public function edit(string $id)
 //     {
 //         //
 //     }
 
 //     /**
 //      * Update the specified resource in storage.
 //      */
    public function update(Request $request, Participant $participant)
    {
        if (Auth::user()->hasRole('admin')) {
    
            try {
                // Check if changes exist before validation
                $hasChanges = false;
                if ($request->event_id !== $participant->event_id ||
                    $request->participant_photo !== $participant->participant_photo ||
                    $request->participant_name !== $participant->participant_name ||
                    $request->participant_gender !== $participant->participant_gender ||
                    $request->participant_group !== $participant->participant_group ||
                    $request->participant_comment !== $participant->participant_comment
                    )
                {
                    $hasChanges = true;
                }
    
                // If no changes detected, return with info message
                if (!$hasChanges) {
                    return redirect()->route('admin.participant.index')->with('info', 'No changes were made.');
                }
    
                // If changes exist, then validate the input
                $validatedData = $request->validate([
                    'event_id' => 'required|exists:events,id',
                    'participant_photo' => 'nullable|string|max:255',
                    'participant_name' => 'required|string|max:255',
                    'participant_gender' => 'required|in:male,female',
                    'participant_group' => 'nullable|string|max:255',
                    'participant_comment' => 'nullable|string|max:255',
                   
                ]);
    
                // Update the participant record
                $participant->update($validatedData);
    
                return redirect()->route('admin.participant.index')->with('success', 'Participant updated successfully.');
            } catch (ValidationException $e) {
                $errors = $e->errors();
                return redirect()->back()->withErrors($errors)->with('error', $errors['participant_id'][0] ?? 'Validation error');
            }
        }     
    }

    public function destroy(string $id)
    {
        
        $participant = Participant::findOrFail($id);
        $participant->delete();

        if (Auth::user()->hasRole('admin'))
        {
            return redirect()->route('admin.participant.index')->with('success', 'Participant deleted successfully.');
        }
        else {
            return redirect()->route('event_manager.participant.index')->with('success', 'Participant deleted successfully.');
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
 
 