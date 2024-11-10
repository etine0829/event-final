<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Scorecard; // Ensure Scorecard model is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScorecardController extends Controller
{
    
    public function store(Request $request)
{
    foreach ($request->input('scores') as $participantId => $scoreData) {
        foreach ($scoreData['criteria_scores'] as $criteriaId => $score) {
            Scorecard::create([
                'category_id' => $request->input('category_id'), // pass category ID from the form
                'criteria_id' => $criteriaId,
                'participant_id' => $participantId,
                'score' => $score,
            ]);
        }
    }

    return redirect()->route('judge.dashboard')->with('success', 'Scores submitted successfully!');
}

}
