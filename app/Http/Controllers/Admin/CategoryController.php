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

        // Check if a criteria with the same criteria_name already exists
        $existingCategoryName = Category::where('category_name', $request->input('category_name'))
        ->where('event_id',$request->input('event_id'))
        ->first();

        if (!$existingCategoryName) {
            $category = new Category();
            $category->event_id = $request->input('event_id');;
            $category->category_name = $request->input('category_name');
            $category->score = $request->input('score');
            $category->save();

            return redirect()->route(Auth::user()->hasRole('admin') ? 'admin.category.index' : 'event_manager.category.index')
                ->with('success', 'Category created successfully.');
        } else {
            $errorMessage = 'Category name ' . $request->input('category_name') . ' is already taken for this category.';
            return redirect()->route(Auth::user()->hasRole('admin') ? 'admin.category.index' : 'event_manager.category.index')
                ->with('error', $errorMessage . ' Try again.');
        }
    }
    else if (Auth::user()->hasRole('event_manager')) {

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

    public function destroy(Category $category)
{
    if (Auth::user()->hasAnyRole(['admin', 'event_manager'])) {
        // Check if there are any associated records
        if ($category->criteria()->exists()) {
            return redirect()->route('admin.category.index')->with('error', 'Cannot delete category because it has associated data.');
        }

        // If no associated records, proceed with deletion
        $category->delete();

        return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully.');
    }

    return redirect()->route('admin.category.index')->with('error', 'Unauthorized access.');
}


    public function deleteAll(Request $request)
    {
        
        
        $count = Category::count();

        if ($count === 0) {
            return redirect()->route('admin.category.index')->with('info', 'There are no categories to delete.');
        }

        try {
            // Use a transaction to ensure data integrity
            \DB::beginTransaction();

            // Delete related data in other tables first (e.g., staff)
            Event::whereHas('category')->delete();

            // Now you can delete the events
            Event::truncate();

            \DB::commit();

            return redirect()->route('admin.category.index')->with('success', 'All categories deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollback();

            // Log the error or handle it appropriately
            return redirect()->route('admin.category.index')->with('error', 'Cannot delete categories because they have associated criterion.');
        }

        
    }
        
    

 }

