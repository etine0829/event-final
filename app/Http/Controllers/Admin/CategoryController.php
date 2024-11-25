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

    return redirect()->route('admin.category.index')->with('error', 'Unauthorized access.');
}

public function update(Request $request, Category $category)
{
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

