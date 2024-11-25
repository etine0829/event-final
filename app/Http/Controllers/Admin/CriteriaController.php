<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use App\Models\Admin\Criteria;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class CriteriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('Admin.criteria.index');
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
    // Validate input data
    $validatedData = $request->validate([
        'event_id' => 'required|exists:events,id',
        'category_id' => 'required|exists:category,id',
        'criteria_name' => 'required|string|max:255',
        'criteria_score' => 'nullable|numeric|max:100', // Ensure score is numeric and <= 100
    ]);

    // Calculate the total score for the given category
    $totalScore = Criteria::where('category_id', $request->input('category_id'))->sum('criteria_score');
    $newScore = $request->input('criteria_score') ?? 0;

    if (($totalScore + $newScore) > 100) {
        return redirect()->route('admin.criteria.index')
            ->with('error', 'The total criteria scores for this category cannot exceed 100.');
    }

    // Check if a criteria with the same name already exists
    $existingCriteriaByNameAndCategory = Criteria::where('criteria_name', $request->input('criteria_name'))
        ->where('category_id', $request->input('category_id'))
        ->first();

    if (!$existingCriteriaByNameAndCategory) {
        $criteria = new Criteria();
        $criteria->event_id = $request->input('event_id');
        $criteria->category_id = $request->input('category_id');
        $criteria->criteria_name = $request->input('criteria_name');
        $criteria->criteria_score = $newScore;
        $criteria->save();

        return redirect()->route('admin.criteria.index')
            ->with('success', 'Criteria created successfully.');
    } else {
        return redirect()->route('admin.criteria.index')
            ->with('error', 'Criteria name is already taken for this category. Try again.');
    }
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    // Validate input data
    $validatedData = $request->validate([
        'event_id' => 'required|exists:events,id',
        'category_id' => 'required|exists:category,id',
        'criteria_name' => 'required|string|max:255',
        'criteria_score' => 'nullable|numeric|max:100', // Ensure score is numeric and <= 100
    ]);

    // Find the existing criteria record
    $criteria = Criteria::findOrFail($id);

    // Calculate the total score for the category, excluding the current criteria
    $currentScore = $criteria->criteria_score ?? 0; // The current score of the criteria
    $newScore = $request->input('criteria_score') ?? 0;
    $totalScore = Criteria::where('category_id', $request->input('category_id'))
        ->where('id', '!=', $id) // Exclude the current criteria being updated
        ->sum('criteria_score');

    if (($totalScore + $newScore) > 100) {
        return redirect()->route('admin.criteria.index')
            ->with('error', 'The total criteria scores for this category cannot exceed 100.');
    }

    // Check if a criteria with the same name already exists
    $existingCriteriaByName = Criteria::where('criteria_name', $request->input('criteria_name'))
        ->where('id', '!=', $id)
        ->first();

    if (!$existingCriteriaByName) {
        $criteria->event_id = $request->input('event_id');
        $criteria->category_id = $request->input('category_id');
        $criteria->criteria_name = $request->input('criteria_name');
        $criteria->criteria_score = $newScore;
        $criteria->save();

        return redirect()->route('admin.criteria.index')
            ->with('success', 'Criteria updated successfully.');
    } else {
        return redirect()->route('admin.criteria.index')
            ->with('error', 'Criteria name is already taken. Try again.');
    }
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Criteria $criteria)
{
    if (Auth::user()->hasAnyRole(['admin', 'event_manager'])) {
        // Check if there are any associated records
        if ($criteria->exists()) {
            return redirect()->route('admin.criteria.index')->with('error', 'Cannot delete criteria because it has associated data.');
        }

        // If no associated records, proceed with deletion
        $criteria->delete();

        return redirect()->route('admin.criteria.index')->with('success', 'criteria deleted successfully.');
    }

    return redirect()->route('admin.criteria.index')->with('error', 'Unauthorized access.');
}

    public function deleteAll(Request $request)
    {       
         $count = Criteria::count();

        if ($count === 0) {
            return redirect()->route('admin.criteria.index')->with('info', 'There are no criteria to delete.');
        }

        try {
            // Use a transaction to ensure data integrity
            \DB::beginTransaction();

            // Delete related data in other tables first (e.g., staff)
            Criteria::whereHas('criteria')->delete();

            // Now you can delete the event
            Criteria::truncate();

            \DB::commit();

            return redirect()->route('admin.criteria.index')->with('success', 'All criteria deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollback();

            // Log the error or handle it appropriately
            return redirect()->route('admin.criteria.index')->with('error', 'Cannot delete criteria because they have associated data.');
        }
    }
}
