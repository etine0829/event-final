<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Scorecard; // Ensure Scorecard model is imported
use Illuminate\Http\Request;

class ScorecardController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'category_id' => 'required|integer',
        'scores' => 'required|array',
        'scores.*.criteria_scores' => 'required|array',
    ]);

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
                ],
                ['score' => $score]
            );
        }

        // Update average score for the participant
        Scorecard::where('participant_id', $participantId)
            ->where('category_id', $request->input('category_id'))
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
                ],
                ['score' => $score]
            );
        }

        // Update average score for the participant
        Scorecard::where('participant_id', $participantId)
            ->where('category_id', $request->input('category_id'))
            ->update(['avg_score' => $avgScore]);
    }

    return redirect()->route('judge.dashboard')->with('success', 'Scores updated successfully!');
}

}
