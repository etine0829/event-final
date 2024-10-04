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
                'event_id' => 'required|exists:events,id',
                'name' => 'required|string|max:255',
                'picture' => 'string|max:255',
                'email' => 'require|string|lowercase|email|max:255|unique:users,email',
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
                'role' => 'required|string|in:judges',
                // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
                // 'role' => ['required', 'string', 'in:judges'],
            ]);

            try {
                User::create($validatedData);
                return redirect()->route('admin.judge.index')
                    ->with('success', 'Judge created successfully.');
            } catch (\Exception $e) {
                return redirect()->route('admin.judge.index')->with('error', 'Failed to create judge: ' . $e->getMessage());
            }
        }
    }


    public function update(Request $request, Category $category)
    {
        
        if (Auth::user()->hasRole('admin')) {

            try {
                $validatedData = $request->validate([
                    'event_id' => 'required|exists:events,id',
                    'name' => 'required|string|max:255',
                    'picture' => 'string|max:255',
                    'email' => 'require|string|lowercase|email|max:255|unique:users,email',
                    'password' => ['required', 'confirmed', Rules\Password::defaults()],
                    'role' => 'required|string|in:judges',
                    // 'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
                    // 'password' => ['required', 'confirmed', Rules\Password::defaults()],
                    // 'role' => ['required', 'string', 'in:judges'],
                ]);
                
                $hasChanges = false;
                if ($request->event_id !== $judge->event_id ||
                    $request->judge_id !== $judge->judge_id ||
                    $request->name !== $judge->name ||
                    $request->picture !== $judge->picture ||
                    $request->email !== $judge->email ||
                    $request->password !== $judge->password ) 
                {
                    $hasChanges = true;
                }

                if (!$hasChanges) {
                    return redirect()->route('admin.judge.index')->with('info', 'No changes were made.');
                }

                // Update the category record
                $judge->update($validatedData);

                return redirect()->route('admin.judge.index')->with('success', 'judge updated successfully.');
            } catch (ValidationException $e) {
                $errors = $e->errors(); 
                return redirect()->back()->withErrors($errors)->with('error', $errors['judge_id'][0] ?? 'Validation error');
            }
        }
    }
}