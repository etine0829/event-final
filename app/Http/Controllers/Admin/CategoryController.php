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

    public function store(Request $request)
    {
        // Check if the user has the 'admin' role
        if (Auth::user()->hasRole('admin')) {
            $validatedData = $request->validate([
                'event_id' => 'required|exists:events,id',
                'category_name' => 'required|string|max:255',
                'score' => 'nullable|numeric|max:100', // Individual score validation
            ]);
    
            // Check if the total score exceeds 100
            $totalScore = Category::where('event_id', $request->input('event_id'))->sum('score');
            $newScore = $request->input('score') ?? 0;
    
            if (($totalScore + $newScore) > 100) {
                return redirect()->route('admin.category.index')
                    ->with('error', 'The total category scores for this event cannot exceed 100.');
            }
    
            // Check if a category with the same name already exists
            $existingCategoryName = Category::where('category_name', $request->input('category_name'))
                ->where('event_id', $request->input('event_id'))
                ->first();
    
            if (!$existingCategoryName) {
                $category = new Category();
                $category->event_id = $request->input('event_id');
                $category->category_name = $request->input('category_name');
                $category->score = $request->input('score');
                $category->save();
    
                return redirect()->route('admin.category.index')
                    ->with('success', 'Category created successfully.');
            } else {
                return redirect()->route('admin.category.index')
                    ->with('error', 'Category name is already taken for this event. Try again.');
            }
        }
    
        // Handle event_manager role
        elseif (Auth::user()->hasRole('event_manager')) {
            $validatedData = $request->validate([
                'event_id' => 'required|exists:events,id',
                'category_name' => 'required|string|max:255',
                'score' => 'nullable|numeric|max:100', // Individual score validation
            ]);
    
            // Check if the total score exceeds 100
            $totalScore = Category::where('event_id', $request->input('event_id'))->sum('score');
            $newScore = $request->input('score') ?? 0;
    
            if (($totalScore + $newScore) > 100) {
                return redirect()->route('event_manager.category.index')
                    ->with('error', 'The total category scores for this event cannot exceed 100.');
            }
    
            // Check if a category with the same name already exists
            $existingCategoryName = Category::where('category_name', $request->input('category_name'))
                ->where('event_id', $request->input('event_id'))
                ->first();
    
            if (!$existingCategoryName) {
                $category = new Category();
                $category->event_id = $request->input('event_id');
                $category->category_name = $request->input('category_name');
                $category->score = $request->input('score');
                $category->save();
    
                return redirect()->route('event_manager.category.index')
                    ->with('success', 'Category created successfully.');
            } else {
                return redirect()->route('event_manager.category.index')
                    ->with('error', 'Category name is already taken for this event. Try again.');
            }
        }
    
        // If neither 'admin' nor 'event_manager', return unauthorized access
        return redirect()->route('event_manager.category.index')->with('error', 'Unauthorized access.');
    }
    
    public function update(Request $request, Category $category)
    {
        // Check if the user has the 'admin' role
        if (Auth::user()->hasRole('admin')) {
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
                'score' => 'nullable|numeric|max:100', // Individual score validation
            ]);
    
            // Check if the total score exceeds 100 after the update
            $currentScore = $category->score ?? 0; // The current score of the category
            $newScore = $request->input('score') ?? 0;
            $totalScore = Category::where('event_id', $request->input('event_id'))
                ->where('id', '<>', $category->id) // Exclude the current category being updated
                ->sum('score');
    
            if (($totalScore + $newScore) > 100) {
                return redirect()->route('admin.category.index')
                    ->with('error', 'The total category scores for this event cannot exceed 100.');
            }
    
            // Update the category record
            $category->update($validatedData);
    
            return redirect()->route('admin.category.index')
                ->with('success', 'Category updated successfully.');
        }
    
        // Check if the user has the 'event_manager' role
        elseif (Auth::user()->hasRole('event_manager')) {
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
                'score' => 'nullable|numeric|max:100', // Individual score validation
            ]);
    
            // Check if the total score exceeds 100 after the update
            $currentScore = $category->score ?? 0; // The current score of the category
            $newScore = $request->input('score') ?? 0;
            $totalScore = Category::where('event_id', $request->input('event_id'))
                ->where('id', '<>', $category->id) // Exclude the current category being updated
                ->sum('score');
    
            if (($totalScore + $newScore) > 100) {
                return redirect()->route('event_manager.category.index')
                    ->with('error', 'The total category scores for this event cannot exceed 100.');
            }
    
            // Update the category record
            $category->update($validatedData);
    
            return redirect()->route('event_manager.category.index')
                ->with('success', 'Category updated successfully.');
        }
    
        // If neither 'admin' nor 'event_manager', return unauthorized access
        return redirect()->route('event_manager.category.index')->with('error', 'Unauthorized action.');
    }

    
    public function destroy(Category $category)
    {
        // Check if the user has the required role (either 'admin' or 'event_manager')
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('event_manager')) {
            return redirect()->route('admin.category.index')->with('error', 'Unauthorized access.');
        }
    
        // Check if the category has any associated records in the criteria relationship
        if ($category->criteria()->exists()) {
            // If the category has associated data, return with an error message
            if (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.category.index')->with('error', 'Cannot delete category because it has associated data.');
            } else {
                return redirect()->route('event_manager.category.index')->with('error', 'Cannot delete category because it has associated data.');
            }
        }
    
        // If no associated records, proceed with the category deletion
        $category->delete();
    
        // Redirect back to the appropriate index route based on the role with a success message
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.category.index')->with('success', 'Category deleted successfully.');
        } else {
            return redirect()->route('event_manager.category.index')->with('success', 'Category deleted successfully.');
        }
    }
    
    
     
    
    public function deleteAll(Request $request)
    {
        // Get the count of categories
        $count = Category::count();

        if ($count === 0) {
            // No categories to delete, show info message
            return redirect()->route('admin.category.index')->with('info', 'There are no categories to delete.');
        }

        try {
            // Start the transaction to ensure data integrity
            \DB::beginTransaction();

            // Check if the user has the 'admin' role
            if (Auth::user()->hasRole('admin')) {
                // Admin can delete all categories
                Category::truncate();  // Delete all categories

            } elseif (Auth::user()->hasRole('event_manager')) {
                // Event managers can only delete categories related to events they manage
                // For example, assuming there's a 'managers' relationship on the Event model
                $eventManagerEventIds = Auth::user()->events->pluck('id');  // Get the events managed by the current user
                Category::whereIn('event_id', $eventManagerEventIds)->delete();  // Delete categories for those events
            }

            // Commit the transaction
            \DB::commit();

            // Return a success message
            return redirect()->route('admin.category.index')->with('success', 'Categories deleted successfully.');

        } catch (\Exception $e) {
            // Rollback in case of error
            \DB::rollback();

            // Log the error or handle it accordingly
            return redirect()->route('admin.category.index')->with('error', 'Cannot delete categories due to associated data.');
        }
    }

}

