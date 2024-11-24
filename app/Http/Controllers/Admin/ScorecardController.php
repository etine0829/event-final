<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Scorecard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Import Auth facade
use Spatie\Permission\Models\Role; // Import Role if using Spatie

class ScorecardController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'scores' => 'required|array',
            'scores.*.criteria_scores' => 'required|array',
        ]);

        // Get the currently logged-in judge's user ID
        $loggedInUserId = Auth::id();

        // Ensure the logged-in user has the 'judge' role
        $judgeRole = Role::where('name', 'judge')->first(); // Using Spatie
        if (!$judgeRole || !Auth::user()->hasRole('judge')) {
            return redirect()->route('judge.dashboard')->withErrors('Unauthorized action.');
        }

        foreach ($request->input('scores') as $participantId => $scoreData) {
            $totalScore = array_sum($scoreData['criteria_scores']);
            $criteriaCount = count($scoreData['criteria_scores']);
            $avgScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;

            foreach ($scoreData['criteria_scores'] as $criteriaId => $score) {
                Scorecard::updateOrCreate(
                    [
                        'category_id' => $request->input('category_id'),
                        'participant_id' => $participantId,
                        'criteria_id' => $criteriaId,
                        'user_id' => $loggedInUserId, // Use judge's user ID
                    ],
                    ['score' => $score]
                );
            }

            // Update average score for the participant (for the specific judge)
            Scorecard::where('participant_id', $participantId)
                ->where('category_id', $request->input('category_id'))
                ->where('user_id', $loggedInUserId) // Update only for the current judge
                ->update(['avg_score' => $avgScore]);
        }

        return redirect()->route('judge.dashboard')->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'scores' => 'required|array',
            'scores.*.criteria_scores' => 'required|array',
        ]);

        // Get the currently logged-in judge's user ID
        $loggedInUserId = Auth::id();

        // Ensure the logged-in user has the 'judge' role
        $judgeRole = Role::where('name', 'judge')->first(); // Using Spatie
        if (!$judgeRole || !Auth::user()->hasRole('judge')) {
            return redirect()->route('judge.dashboard')->withErrors('Unauthorized action.');
        }

        foreach ($request->input('scores') as $participantId => $scoreData) {
            $totalScore = array_sum($scoreData['criteria_scores']);
            $criteriaCount = count($scoreData['criteria_scores']);
            $avgScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;

            foreach ($scoreData['criteria_scores'] as $criteriaId => $score) {
                Scorecard::updateOrCreate(
                    [
                        'category_id' => $request->input('category_id'),
                        'participant_id' => $participantId,
                        'criteria_id' => $criteriaId,
                        'user_id' => $loggedInUserId, // Use judge's user ID
                    ],
                    ['score' => $score]
                );
            }

            // Update average score for the participant (for the specific judge)
            Scorecard::where('participant_id', $participantId)
                ->where('category_id', $request->input('category_id'))
                ->where('user_id', $loggedInUserId) // Update only for the current judge
                ->update(['avg_score' => $avgScore]);
        }

        return redirect()->route('judge.dashboard')->with('success', 'Scores updated successfully!');
    }
}
