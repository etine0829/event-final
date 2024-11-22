<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;




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
       
        // dd($request->all());
        // Validate the incoming request data
        $validatedData = $request->validate([
            'event_id' => 'required|exists:events,id',
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required'],
        ]);


        try {
            // Hash the password before saving
            $validatedData['password'] = Hash::make($validatedData['password']);


            // Create the new user  
            $user = User::create($validatedData);


            // Assign the 'judge' role to the user
            $user->assignRole('judge');


            return redirect()->route('admin.judge.index')
                ->with('success', 'Judge created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.judge.index')->with('error', 'Failed to create judge: ' . $e->getMessage());
        }
    }
}


public function update(Request $request, User $judge)
{
    if (Auth::user()->hasRole('admin')) {
        try {
            $validatedData = $request->validate([
                'event_id' => 'required|exists:events,id',
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('users')->where(function ($query) use ($request, $judge) {
                        return $query->where('event_id', $request->event_id)
                                     ->where('id', '<>', $judge->id); // Ignore current judge in uniqueness check
                    }),
                ],
                'email' => 'required|string|email|max:255|unique:users,email,' . $judge->id, // Allow the email to stay unique except for current judge
                'password' => 'nullable|string|min:8|confirmed', // Password is optional, but should be validated if filled
            ]);

            $hasChanges = false;

            // Check if any fields have changed
            if ($request->event_id !== $judge->event_id ||
                $request->name !== $judge->name ||
                $request->email !== $judge->email) {
                $hasChanges = true;
            }

            // If password is filled, it should be hashed and included
            if ($request->filled('password')) {
                $hasChanges = true;
                $validatedData['password'] = Hash::make($request->password);
            } else {
                // Ensure password field is excluded if not updated
                unset($validatedData['password']);
            }

            // If no changes, return with an info message
            if (!$hasChanges) {
                return redirect()->route('admin.judge.index')->with('info', 'No changes were made.');
            }

            // Update the judge with validated data
            $judge->update($validatedData);

            return redirect()->route('admin.judge.index')->with('success', 'Judge updated successfully.');
        } catch (ValidationException $e) {
            // Handle validation errors
            $errors = $e->errors();
            \Log::error('Validation error during judge update:', $errors); // Log the full error for debugging
            return redirect()->back()->withErrors($errors)->with('error', 'Validation error occurred.');
        } catch (\Exception $e) {
            // Log any other exception that occurs
            \Log::error('Error during judge update:', ['error' => $e->getMessage()]);
            return redirect()->route('admin.judge.index')->with('error', 'An unexpected error occurred.');
        }
    }
}



    public function destroy(User $judge)
    {
        // Ensure the authenticated user has the admin role
        if (Auth::user()->hasRole('admin')) {
            try {
                // Delete the judge record
                $judge->delete();


                return redirect()->route('admin.judge.index')
                    ->with('success', 'Judge deleted successfully.');
            } catch (\Exception $e) {
                return redirect()->route('admin.judge.index')
                    ->with('error', 'Failed to delete judge: ' . $e->getMessage());
            }
        }


        // Return an error if the user is not authorized
        return redirect()->route('admin.judge.index')
            ->with('error', 'You do not have permission to perform this action.');
    }


}




