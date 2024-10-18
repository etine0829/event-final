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
            'participant_photo' => 'nullable|string|max:255',
            'participant_name' => 'required|string|max:255',
            'participant_gender' => 'required|string|max:255',
            'participant_comment' => 'nullable|string|max:255',
            'custom_label_1' => 'nullable|string|max:255',
            'custom_label_2' => 'nullable|string|max:255',
            'custom_value_1' => 'nullable|string|max:255',
            'custom_value_2' => 'nullable|string|max:255', 
         ]);
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
                    $request->participant_comment !== $participant->participant_comment ||
                    $request->custom_label_1 !== $participant->custom_label_1 ||
                    $request->custom_value_1 !== $participant->custom_value_1 ||
                    $request->custom_label_2 !== $participant->custom_label_2 ||
                    $request->custom_value_2 !== $participant->custom_value_2)
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
                    'participant_gender' => 'required|in:Male,Female',
                    'participant_comment' => 'nullable|string|max:255',
                    'custom_label_1' => 'nullable|string|max:255',
                    'custom_label_2' => 'nullable|string|max:255',
                    'custom_value_1' => 'nullable|string|max:255',
                    'custom_value_2' => 'nullable|string|max:255',
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
    
 //         } else if (Auth::user()->hasRole('admin_staff')) {
         
 //             try {
 //                 $validatedData = $request->validate([
 //                     'school_id' => 'required|exists:schools,id',
 //                     'department_id' => [
 //                         'required',
 //                         'string',
 //                         'max:255',
 //                         Rule::unique('departments')->where(function ($query) use ($request, $department) {
 //                             return $query->where('school_id', $request->school_id)
 //                                         ->where('id', '<>', $department->id);
 //                         }),
 //                     ],
 //                     'department_abbreviation' => [
 //                         'required',
 //                         'string',
 //                         'max:255',
 //                         Rule::unique('departments')->where(function ($query) use ($request, $department) {
 //                             return $query->where('school_id', $request->school_id)
 //                                         ->where('id', '<>', $department->id);
 //                         }),
 //                     ],
 //                     'department_name' => 'required|string|max:255',
 //                     'dept_identifier' => 'required|string|max:255',
 //                 ]);
                 
 //                 $hasChanges = false;
 //                 if ($request->school_id !== $department->school_id ||
 //                     $request->department_id !== $department->department_id ||
 //                     $request->department_abbreviation !== $department->department_abbreviation ||
 //                     $request->department_name !== $department->department_name ||
 //                     $request->dept_identifier !== $department->dept_identifier) 
 //                 {
 //                     $hasChanges = true;
 //                 }
 
 //                 if (!$hasChanges) {
 //                     return redirect()->route('staff.department.index')->with('info', 'No changes were made.');
 //                 }
 
 //                 // Update the department record
 //                 $department->update($validatedData);
 
 //                 return redirect()->route('staff.department.index')->with('success', 'Department updated successfully.');
 //             } catch (ValidationException $e) {
 //                 $errors = $e->errors();
 //                 return redirect()->back()->withErrors($errors)->with('error', $errors['department_id'][0] ?? 'Validation error');
 //             }
 
 //         }
 
 //     }
 
 //     /**
 //      * Remove the specified resource from storage.
 //      */
 //     // public function destroy(Department $department)
 //     // {
 //     //     if (Auth::user()->hasRole('admin')) {
 
 //     //         if ($department->employees()->exists()) {
 //     //             return redirect()->route('admin.department.index')->with('error', 'Cannot delete department because it has associated data.');
 //     //         }
 
 //     //         $department->delete();
 
 //     //         return redirect()->route('admin.department.index')->with('success', 'Department/s deleted successfully.');
 
 //     //     } else if (Auth::user()->hasRole('admin_staff')) {
 
 //     //         if ($department->employees()->exists()) {
 //     //             return redirect()->route('staff.department.index')->with('error', 'Cannot delete department because it has associated data.');
 //     //         }
 
 //     //         $department->delete();
 
 //     //         return redirect()->route('staff.department.index')->with('success', 'Department/s deleted successfully.');
 
 //     //     }
 
 //     // }
 
 //     public function destroy(Department $department)
 //     {
 //         // Determine the route and role-based message
 //         $route = Auth::user()->hasRole('admin') ? 'admin.department.index' : 'staff.department.index';
 //         $role = Auth::user()->hasRole('admin') ? 'admin' : 'admin_staff';
 
 //         try {
 //             // Check if there are associated employees
 //             if ($department->employees()->exists()) {
 //                 return redirect()->route($route)->with('error', 'Cannot delete department because it has associated data.');
 //             }
 
 //             // Attempt to delete the department
 //             $department->delete();
 
 //             return redirect()->route($route)->with('success', 'Department/s deleted successfully.');
 
 //         } catch (\Illuminate\Database\QueryException $e) {
 //             // Check for foreign key constraint violation
 //             if ($e->getCode() == '23000') {
 //                 return redirect()->route($route)->with('error', 'Cannot delete department due to a foreign key constraint violation.');
 //             }
 
 //             // Handle other types of SQL exceptions
 //             return redirect()->route($route)->with('error', 'An unexpected error occurred while trying to delete the department.');
 //         }
 //     }
 
 //     public function deleteAll(Request $request)
 //     {
 
 //         $schoolId = $request->input('school_id');
 
 //         if (!$schoolId) {
 //             return redirect()->back()->with('error', 'No school selected.');
 //         }
 
 //         // Check if there are any departments associated with this school
 //         $departmentsWithEmployees = Department::where('school_id', $schoolId)->whereHas('employees')->exists();
 
 //         if ($departmentsWithEmployees) {
 //             return redirect()->route('admin.department.index')->with('error', 'Cannot delete departments because they have associated employees.');
 //         }
 
 //         // If no departments have associated employees, proceed with deletion
 //         Department::where('school_id', $schoolId)->delete();
 
 //         return redirect()->back()->with('success', 'All departments for the selected school have been deleted.');
 
         
 //     }
 
  }
 
 