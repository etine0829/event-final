<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\User; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class JudgesController extends Controller
{
    public function index()
    {
        return view('Admin.judge.index');
    }

    public function store(Request $request)
    {
        // Ensure the authenticated user has the admin role
        if (Auth::user()->hasRole('admin')) {

            // Validate the incoming request data
            $validatedData = $request->validate([
                'event_id' => 'required|string',
                'name' => ['required', 'string', 'max:255'],
                'picture' => ['required', 'string'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => ['required', 'string', 'in:judges'],
            ]);

            // Create the new user
            $user = User::create([
                'name' => $validatedData['name'],
                'picture' => $validatedData['picture'], // Assuming 'picture' is a valid string (e.g., file path)
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']), // Hash the password before storing
            ]);

            // Assign the "judges" role to the user
            $user->assignRole($validatedData['role']);

            // Redirect back with success message
            return redirect()->route('admin.judge.index')->with('success', 'Judge created successfully.');
        }

        // If the user is not an admin, return a forbidden response or redirect
        return redirect()->back()->with('error', 'Unauthorized action.');
    }
}
