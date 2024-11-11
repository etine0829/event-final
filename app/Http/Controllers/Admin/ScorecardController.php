<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Scorecard;
use App\Models\Admin\Participant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScorecardController extends Controller
{
    public function index()
    {
        // Fetch all scorecard records with related data
        $scorecards = Scorecard::all();

        return view('scorecards.index', compact('scorecards'));
    }
    
    public function store(Request $request)
    {
        $category_id = $request->input('category_id');
        
        foreach ($request->input('scores') as $participant_id => $criteriaScores) {
            $totalScore = 0;
            $criteriaCount = count($criteriaScores['criteria_scores']);
            
            // Calculate the total score
            foreach ($criteriaScores['criteria_scores'] as $criteriaId => $score) {
                $totalScore += $score;
            }

            // Calculate the average score for this participant
            $averageScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;

            // Save each individual criterion score with avg_score
            foreach ($criteriaScores['criteria_scores'] as $criteriaId => $score) {
                Scorecard::create([
                    'participant_id' => $participant_id,
                    'criteria_id' => $criteriaId,
                    'score' => $score,
                    'avg_score' => $averageScore,  // Store the calculated average score here
                    'category_id' => $category_id,
                ]);
            }
        }

        return redirect()->route('judge.dashboard')->with('success', 'Scores submitted successfully.');
    }
}
