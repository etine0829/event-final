<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;

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

            // Validate the incoming request data
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'picture' => 'nullable|string|max:255', // Allow picture to be optional
                'email' => 'required|string|email|max:255|unique:users,email', // Removed unnecessary 'lowercase'
                'password' => 'required|string|min:8|confirmed', // Enforce password confirmation
                'role' => 'required|string|in:event_manager,judge,staff',
                'event_id' => 'required|exists:events,id',
            ]);

            try {
                // Hash the password before saving
                $validatedData['password'] = Hash::make($validatedData['password']);

                // Create the new user
                $user = User::create([
                    'name' => $validatedData['name'],
                    'picture' => $validatedData['picture'] ?? null, // Handle null picture gracefully
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                    'event_id' => $validatedData['event_id'],
                ]);

                // Assign the selected role to the user
                $user->assignRole($validatedData['role']);

                // Fire the Registered event
                event(new Registered($user));

                // Redirect with success message
                return redirect()->route('admin.user.index')
                    ->with('success', 'User created successfully.');
            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('User creation failed: ' . $e->getMessage());

                // Redirect with error message
                return redirect()->route('admin.user.index')
                    ->with('error', 'Failed to create user: ' . $e->getMessage());
            }
        }

        
    // // Ensure the authenticated user has the admin role
    // if (Auth::user()->hasRole('admin')) {
        
    //     // dd($request->all());
    //     // Validate the incoming request data
    //     $validatedData = $request->validate([
    //         'name' => 'required|string|max:255',
    //         'picture' => 'string|max:255',
    //         'email' => 'required|string|lowercase|email|max:255|unique:users,email', 
    //         'password' => ['required'],
    //         'role' => ['required', 'string', 'in:event_manager,judge,staff'],
    //         'event_id' => 'required|exists:events,id',
    //     ]);

    //     try {
    //         // Hash the password before saving
    //         $validatedData['password'] = Hash::make($validatedData['password']);

    //         // Create the new user  
    //         $user = User::create($validatedData);

    //         $user->assignRole($request->role);

    //         event(new Registered($user));
    
    //         Auth::login($user);

    //         return redirect()->route('admin.user.index')
    //             ->with('success', 'User created successfully.');
    //     } catch (\Exception $e) {
    //         return redirect()->route('admin.user.index')->with('error', 'Failed to create user: ' . $e->getMessage());
    //     }
    // }
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
