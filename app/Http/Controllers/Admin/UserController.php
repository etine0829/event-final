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
       
        $roleRedirect = Auth::user()->hasRole('admin') ? 'admin.user.index' : 'event_manager.user.index';

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'picture' => 'nullable|string|max:255', 
            'email' => 'required|string|email|max:255|unique:users,email', 
            'password' => 'required|string|min:8|confirmed', 
            'role' => 'required|string|in:event_manager,judge,judge_chairman,staff',
        ]);

        try {
        
            $validatedData['password'] = Hash::make($validatedData['password']);

            $user = User::create([
                'name' => $validatedData['name'],
                'picture' => $validatedData['picture'] ?? null, 
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'role' => $validatedData['role'],
            ]);

            $user->assignRole($validatedData['role']);

            event(new Registered($user));

            return redirect()->route($roleRedirect)
                ->with('success', 'User created successfully.');
        } catch (\Exception $e) {

            Log::error('User creation failed: ' . $e->getMessage());

            return redirect()->route($roleRedirect)
                ->with('error', 'Failed to create user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, User $user)
    {

        $roleRedirect = Auth::user()->hasRole('admin') ? 'admin.user.index' : 'event_manager.user.index';
    
        try {

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email|unique:users,email,' . $user->id, // Unique email, excluding the current user
                'role' => 'required|string|in:event_manager,judge,judge_chairman,staff',
            ]);

            $hasChanges = false;
            

            if ($request->name !== $user->name || 
                $request->email !== $user->email || 
                $request->role !== $user->role) {
                $hasChanges = true;
            }
    
            if ($request->has('password') && !empty($request->password)) {
                if ($request->password !== $user->password) {
                    $hasChanges = true;
                }
            }

            if (!$hasChanges) {
                return redirect()->route($roleRedirect)->with('info', 'No changes were made.');
            }
    

            if ($request->has('password') && !empty($request->password)) {
                $validatedData['password'] = Hash::make($request->password);
            }

            $user->update($validatedData);

            return redirect()->route($roleRedirect)->with('success', 'User updated successfully.');
    
        } catch (ValidationException $e) {

            $errors = $e->errors();
            return redirect()->back()->withErrors($errors)->with('error', $errors['email'][0] ?? 'Validation error');
        } catch (\Exception $e) {

            Log::error('Failed to update user: ' . $e->getMessage());
            return redirect()->route($roleRedirect)->with('error', 'Failed to update user.');
        }
    }
    
    public function destroy(User $user)
    {
        $redirectRoute = Auth::user()->hasRole('admin') 
            ? 'admin.user.index' 
            : 'event_manager.user.index';

        if ($user->event()->exists()) {
            return redirect()->route($redirectRoute)->with('error', 'Cannot delete user because it has associated data.');
        }

        $user->delete();

        return redirect()->route($redirectRoute)->with('success', 'User deleted successfully.');
    }


    public function deleteAll(Request $request)
    {
          
         $count = User::count();

        if ($count === 0) {
            return redirect()->route('admin.user.index')->with('info', 'There are no user to delete.');
        }

        try {

            \DB::beginTransaction();

            User::whereHas('user')->delete();

            User::truncate();

            \DB::commit();

            return redirect()->route('admin.user.index')->with('success', 'All users deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollback();

            return redirect()->route('admin.user.index')->with('error', 'Cannot delete users because they have associated events.');
        }

        
    }
}
