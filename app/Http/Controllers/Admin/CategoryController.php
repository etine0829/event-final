<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;    
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\Admin\Event;
use App\Models\Admin\Category;

class CategoryController extends Controller
{
    //
    public function index()
    {
        return view('Admin.category.index');
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

        // // Log the incoming request data for debugging
        // \Log::info('Category Store Request Data:', $request->all());

        $validatedData = $request->validate([
            'event_id' => 'required|exists:events,id',
            'category_name' => 'required|string|max:255',
            'score' => 'nullable|string|max:255',
        ]);

        // Attempt to create the Category record
        try {
            Category::create($validatedData);
            return redirect()->route('admin.category.index')
                ->with('success', 'Category created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.category.index')->with('error', 'Failed to create category: ' . $e->getMessage());
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
    public function update(Request $request, Category $category)
    {
        
        if (Auth::user()->hasRole('admin')) {

            try {
                $validatedData = $request->validate([
                    'event_id' => 'required|exists:events,id',
                    'category_name' => [
                        'required',
                        'string',
                        'max:255',
                        Rule::unique('category')->where(function ($query) use ($request, $category) {
                            return $query->where('event_id', $request->event_id)
                                        ->where('id', '<>', $category->id);
                        }),
                    ],
                    'score' => 'nullable|string|max:255',
                    
                ]);
                
                 // Check if any changes were made
            $hasChanges = $request->event_id !== $category->event_id ||
            $request->category_name !== $category->category_name ||
            $request->score !== $category->score;

                if (!$hasChanges) {
                return redirect()->route('admin.category.index')->with('info', 'No changes were made.');
                }

                // Update the category record
                $category->update($validatedData);

                return redirect()->route('admin.category.index')->with('success', 'Category updated successfully.');
                } catch (ValidationException $e) {
                // Return all validation errors to the user
                return redirect()->back()->withErrors($e->errors())->with('error', 'Validation error occurred.');
                } catch (\Exception $e) {
                // Catch any other errors
                return redirect()->route('admin.category.index')->with('error', 'An error occurred: ' . $e->getMessage());
                }
                }

                // Handle unauthorized access
                return redirect()->route('admin.category.index')->with('error', 'Unauthorized action.');
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

