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
    // Array to accumulate total scores for each participant
    $participantScores = [];
   
    // Loop through the scores provided in the request
    foreach ($request->input('scores') as $participantId => $scoreData) {
        $totalScore = 0;
        $criteriaCount = 0;


        // Loop through each criterion score for the participant
        foreach ($scoreData['criteria_scores'] as $criteriaId => $score) {
            // Store the individual score record
            Scorecard::create([
                'category_id' => $request->input('category_id'), // pass category ID from the form
                'criteria_id' => $criteriaId,
                'participant_id' => $participantId,
                'score' => $score,
            ]);


            // Accumulate total score and count the criteria
            $totalScore += $score;
            $criteriaCount++;
        }


        // Calculate the average score
        $avgScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;


        // Store the average score
        $scorecard = Scorecard::where('participant_id', $participantId)
            ->where('category_id', $request->input('category_id'))
            ->first();


        // Only update the average score if the record exists
        if ($scorecard) {
            $scorecard->avg_score = $avgScore;
            $scorecard->save();
        }


        // Add the participant's score to the array for ranking
        $participantScores[] = [
            'participant_id' => $participantId,
            'avg_score' => $avgScore,
        ];
    }


    // Rank participants based on avg_score in descending order
    usort($participantScores, function ($a, $b) {
        return $b['avg_score'] <=> $a['avg_score'];  // Sort in descending order of avg_score
    });


    // Assign ranks based on sorted scores
    $rank = 1;
    foreach ($participantScores as $participantScore) {
        // Update the participant's rank in the Scorecard table
        Scorecard::where('participant_id', $participantScore['participant_id'])
            ->where('category_id', $request->input('category_id'))
            ->update(['rank' => $rank]);


        // Increment the rank for the next participant
        $rank++;
    }


    // Redirect with a success message
    return redirect()->route('judge.dashboard')->with('success', 'Scores submitted successfully!');
}


   


}



