<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Event;
use App\Models\Admin\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class GroupController extends Controller
{
     //
     public function index()
     {
         return view('Admin.group.index');
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
 
         $validatedData = $request->validate([
             'event_id' => 'required|exists:events,id',
             'group_name' => 'required|string|max:255',
     
         ]);

         // Check if the group_name already exists for the given event_id
        $existingGroup = Group::where('event_id', $request->event_id)
        ->where('group_name', $request->group_name)
        ->exists();

        if ($existingGroup) {
        return redirect()->route('admin.group.index')
        ->with('error', 'The group name already exists for this event.');
        }
    
         // Attempt to create the Category record
         try {
             Group::create($validatedData);
             return redirect()->route('admin.group.index')
                 ->with('success', 'Group created successfully.');
         } catch (\Exception $e) {
             return redirect()->route('admin.group.index')->with('error', 'Failed to create group: ' . $e->getMessage());
         }
     }
     else if (Auth::user()->hasRole('event_manager')) {
 
        $validatedData = $request->validate([
            'event_id' => 'required|exists:events,id',
            'group_name' => 'required|string|max:255',
    
        ]);

        // Attempt to create the Group record
        try {
            Group::create($validatedData);
            return redirect()->route('admin.group.index')
                ->with('success', 'Group created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.group.index')->with('error', 'Failed to create group: ' . $e->getMessage());
        }
     }
 }
 
     public function update(Request $request, Group $group)
     {
         
         if (Auth::user()->hasRole('admin')) {
 
             try {
                 $validatedData = $request->validate([
                     'event_id' => 'required|exists:events,id',
                     'group_name' => [
                         'required',
                         'string',
                         'max:255',
                         Rule::unique('group')->where(function ($query) use ($request, $group) {
                             return $query->where('event_id', $request->event_id)
                                         ->where('id', '<>', $group->id);
                         }),
                     ]  
                 ]);
                 
                  // Check if any changes were made
             $hasChanges = $request->event_id !== $group->event_id ||
             $request->group_name !== $group->group_name;
 
                 if (!$hasChanges) {
                 return redirect()->route('admin.group.index')->with('info', 'No changes were made.');
                 }
 
                 // Update the group record
                 $group->update($validatedData);
 
                 return redirect()->route('admin.group.index')->with('success', 'Group updated successfully.');
                 } catch (ValidationException $e) {
                 // Return all validation errors to the user
                 return redirect()->back()->withErrors($e->errors())->with('error', 'Validation error occurred.');
                 } catch (\Exception $e) {
                 // Catch any other errors
                 return redirect()->route('admin.group.index')->with('error', 'An error occurred: ' . $e->getMessage());
                 }
                 }
 
                 // Handle unauthorized access
                 return redirect()->route('admin.group.index')->with('error', 'Unauthorized action.');
     }
 
     public function destroy(Group $group)
     {
         if (Auth::user()->hasRole('admin')) {
 
             // Check if there are any associated records
             if ($group->participant()->exists()) {
                 return redirect()->route('admin.group.index')->with('error', 'Cannot delete group because it has associated data.');
             }
 
             // If no associated records, proceed with deletion
             $group->delete();
 
             return redirect()->route('admin.group.index')->with('success', 'group deleted successfully.');
 
         } else if (Auth::user()->hasRole('event_manager')) {
             
             // Check if there are any associated records
             if ($group->participant()->exists()) {
                return redirect()->route('admin.group.index')->with('error', 'Cannot delete group because it has associated data.');
            }

            // If no associated records, proceed with deletion
            $group->delete();

            return redirect()->route('admin.group.index')->with('success', 'Group deleted successfully.');
         }
     }
 
     public function deleteAll(Request $request)
     {
         
         
         $count = Group::count();
 
         if ($count === 0) {
             return redirect()->route('admin.group.index')->with('info', 'There are no group to delete.');
         }
 
         try {
             // Use a transaction to ensure data integrity
             \DB::beginTransaction();
 
             // Delete related data in other tables first (e.g., staff)
             Event::whereHas('group')->delete();
 
             // Now you can delete the events
             Event::truncate();
 
             \DB::commit();
 
             return redirect()->route('admin.group.index')->with('success', 'All groups deleted successfully.');
         } catch (\Exception $e) {
             \DB::rollback();
 
             // Log the error or handle it appropriately
             return redirect()->route('admin.group.index')->with('error', 'Cannot delete groups because they have associated criterion.');
         }
 
         
     }
         
}
