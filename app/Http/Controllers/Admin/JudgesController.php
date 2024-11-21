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
        // if (Auth::user()->hasRole('admin')) {


        //     try {
        //         $validatedData = $request->validate([
        //             'event_id' => 'required|exists:events,id',
        //             'name' => 'required|string|max:255',
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
                    'event_id' => 'required|exists:events,id',
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('users')->where(function ($query) use ($request, $judge) {
                            return $query->where('event_id', $request->event_id)
                                        ->where('id', '<>', $judge->id);
                        }),
                    ],
                    'email' => 'required|string|max:255',
                   
                   
                ]);
               
                $hasChanges = false;
                if ($request->event_id !== $judge->event_id ||
                    $request->name !== $judge->name ||
                    $request->email !== $judge->email ||
                    $request->password !== $judge->password
                     )
                {
                    $hasChanges = true;
                }


                if (!$hasChanges) {
                    return redirect()->route('admin.judge.index')->with('info', 'No changes were made.');
                }


                // Update the category record
                $judge->update($validatedData);


                return redirect()->route('admin.judge.index')->with('success', 'Judge updated successfully.');
            } catch (ValidationException $e) {
                $errors = $e->errors();
                return redirect()->back()->withErrors($errors)->with('error', $errors['id'][0] ?? 'Validation error');
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




