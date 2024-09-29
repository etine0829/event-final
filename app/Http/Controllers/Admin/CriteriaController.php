<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use \App\Models\Admin\Criteria;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Storage;
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
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'category_id' => [
                    'required',
                    'exists:category,id',
                ],
                'criteria_id' => [
                    'required',
                    'string',
                    'max:255',
                ],
                // 'course_id' => 'required|string|max:255',
                'criteria_name' => 'required|string|max:255',
                'criteria_score' => 'required|string|max:255',
                // 'course_logo' => 'image|max:2048', // Example: validation for image upload
            ]);

            // Handle file upload if 'course_photo' is present
            // if ($request->hasFile('course_logo')) {
            //     $fileNameWithExt = $request->file('course_logo')->getClientOriginalName();
            //     $filename = pathinfo($fileNameWithExt, PATHINFO_FILENAME);
            //     $extension = $request->file('course_logo')->getClientOriginalExtension();
            //     $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            //     $path = $request->file('course_logo')->storeAs('public/course_logo', $fileNameToStore);
            // } else {
            //     $fileNameToStore = 'user.png'; // Default file if no photo is uploaded
            // }

            // Check if an course with the same course_id or course_rfid already exists
            $existingCriteriaById = Criteria::where('criteria_id', $request->input('criteria_id'))->first();

            if (!$existingCriteriaById) {
                $criteria = new Criteria();
                $criteria->event_id = $request->input('event_id');
                $criteria->category_id = $request->input('category_id');
                $criteria->criteria_id = $request->input('criteria_id');
                $criteria->criteria_name = $request->input('criteria_name');
                $criteria->criteria_score = $request->input('criteria_score');
                $criteria->save();

                if (Auth::user()->hasRole('admin')) {
                    return redirect()->route('admin.criteria.index')
                        ->with('success', 'Criteria created successfully.');
                }
                else{
                    return redirect()->route('event_manager.criteria.index')
                        ->with('success', 'Criteria created successfully.');
                }
                    

            } else {
                $errorMessage = '';
                if ($existingCriteriaById) {
                    $criteriaName = $existingCriteriaById->criteria_name . ' ' . $existingCriteriaById->criteria_score;
                    $errorMessage .= 'Criteria ID ' . $request->input('criteria_id') . ' is already taken by ' . $criteriaName . '. ';
                }

                
                if (Auth::user()->hasRole('admin')) {
                    return redirect()->route('admin.criteria.index')
                    ->with('error', $errorMessage . 'Try again.');
                }
                else{
                    return redirect()->route('event_manager.criteria.index')
                    ->with('error', $errorMessage . 'Try again.');
                }
            }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'category_id' => [
                'required',
                'exists:category,id',
            ],
            'criteria_id' => [
                'required',
                'string',
                'max:255',
            ],
            'criteria_name' => 'required|string|max:255',
            'criteria_score' => 'required|string|max:255',
        ]);


        
        // Find the existing criteria record
        $criteria = Criteria::findOrFail($id);

        // Check if an course with the same course_id or course_rfid already exists, excluding the current course
        $existingCriteriaById = criteria::where('criteria_id', $request->input('criteria_id'))->where('id', '!=', $id)->first();

        if (!$existingCriteriaById) {
            // Update course attributes
            
            $criteria->event_id = $request->input('event_id');
            $criteria->category_id = $request->input('category_id');
            $criteria->criteria_id = $request->input('criteria_id');
            $criteria->criteria_name = $request->input('criteria_name');
            $criteria->criteria_score = $request->input('criteria_score');
            $criteria->save();

            if (Auth::user()->hasRole('admin')){
                return redirect()->route('admin.criteria.index')
                ->with('success', 'Criteria updated successfully.');
            } else {
                return redirect()->route('event_manager.criteria.index')
                ->with('success', 'Criteria updated successfully.');
            }
            
        } else {
            $errorMessage = '';
            if ($existingCriteriaById) {
                $criteriaName = $existingCriteriaById->criteria_id . ' ' . $existingCriteriaById->criteria_name;
                $errorMessage .= 'Criteria ID ' . $request->input('criteria_id') . ' is already taken by ' . $criteriaName . '. ';
            }

            if (Auth::user()->hasRole('admin'))
            {
                return redirect()->route('admin.criteria.index')
                ->with('error', $errorMessage . 'Try again.');
            } else {
                return redirect()->route('event.ccriteria.index')
                ->with('error', $errorMessage . 'Try again.');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
        $criteria = Criteria::findOrFail($id);
        $criteria->delete();

        if (Auth::user()->hasRole('admin'))
        {
            return redirect()->route('admin.criteria.index')->with('success', 'Criteria deleted successfully.');
        }
        else {
            return redirect()->route('event_manager.criteria.index')->with('success', 'Criteria deleted successfully.');
        }
    }

}