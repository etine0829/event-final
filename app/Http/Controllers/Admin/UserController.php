<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('Admin.user.index');
    }

    public function store(Request $request)
    {
        
    // Ensure the authenticated user has the admin role
    if (Auth::user()->hasRole('admin')) {
        
        // dd($request->all());
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'picture' => 'string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email', 
            'password' => ['required'],
            'role' => ['required', 'string', 'in:event_manager,judge,staff'],
        ]);

        try {
            // Hash the password before saving
            $validatedData['password'] = Hash::make($validatedData['password']);

            // Create the new user  
            $user = User::create($validatedData);

            return redirect()->route('admin.user.index')
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.user.index')->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }
}

    public function update(Request $request, User $user)
    {
        // if (Auth::user()->hasRole('admin')) {

        //     try {
        //         $validatedData = $request->validate([
        //             'event_id' => 'required|exists:events,id',
        //             'name' => 'required|string|max:255',
        //             'picture' => 'string|max:255',
        //             'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $judge->id, // Allow updating the email but keep uniqueness
        //             'password' => ['nullable', 'confirmed', Rules\Password::defaults()], // Password can be nullable if not changing
        //         ]);

        //         if ($request->filled('password')) {
        //             // Hash the password if it is updated
        //             $validatedData['password'] = Hash::make($validatedData['password']);
        //         } else {
        //             unset($validatedData['password']); // Remove password from the update if not provided
        //         }

        //         // Check if any data has changed
        //         if (!$judge->isDirty()) {
        //             return redirect()->route('admin.judge.index')->with('info', 'No changes were made.');
        //         }

        //         // Update the judge's data
        //         $judge->update($validatedData);

        //         return redirect()->route('admin.judge.index')->with('success', 'Judge updated successfully.');
        //     } catch (ValidationException $e) {
        //         return redirect()->back()->withErrors($e->errors())->with('error', 'Validation error');
        //     }
        // }

        if (Auth::user()->hasRole('admin')) {

            try {
                $validatedData = $request->validate([
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                    ],
                    'email' => 'required|string|max:255',
                    'role' => 'required|in:event_manager,staff,judge', 
                ]);
                
                $hasChanges = false;
                if ($request->name !== $user->name ||
                    $request->email !== $user->email ||
                    $request->password !== $user->password ||
                    $request->role !== $user->role
                     ) 
                {
                    $hasChanges = true;
                }

                if (!$hasChanges) {
                    return redirect()->route('admin.user.index')->with('info', 'No changes were made.');
                }

                // Update the category record
                $user->update($validatedData);

                return redirect()->route('admin.user.index')->with('success', 'User updated successfully.');
            } catch (ValidationException $e) {
                $errors = $e->errors();
                return redirect()->back()->withErrors($errors)->with('error', $errors['id'][0] ?? 'Validation error');
            }
        }
    }

    public function destroy(User $user)
    {
        if (Auth::user()->hasRole('admin')) {

            // Check if there are any associated records
            if ($user->event()->exists()) {
                return redirect()->route('admin.user.index')->with('error', 'Cannot delete user because it has associated data.');
            }

            // If no associated records, proceed with deletion
            $user->delete();

            return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');

        } else if (Auth::user()->hasRole('event_manager')) {
            
            // Check if there are any associated records
            if ($user->event()->exists()) {
                return redirect()->route('admin.user.index')->with('error', 'Cannot delete user because it has associated data.');
            }

            // If no associated records, proceed with deletion
            $user->delete();

            return redirect()->route('admin.user.index')->with('success', 'User deleted successfully.');


        }
    }

    public function deleteAll(Request $request)
    {
        
        
         $count = User::count();

        if ($count === 0) {
            return redirect()->route('admin.user.index')->with('info', 'There are no user to delete.');
        }

        try {
            // Use a transaction to ensure data integrity
            \DB::beginTransaction();

            // Delete related data in other tables first (e.g., staff)
            User::whereHas('user')->delete();

            // Now you can delete the event
            User::truncate();

            \DB::commit();

            return redirect()->route('admin.user.index')->with('success', 'All users deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollback();

            // Log the error or handle it appropriately
            return redirect()->route('admin.user.index')->with('error', 'Cannot delete users because they have associated events.');
        }

        
    }
}
