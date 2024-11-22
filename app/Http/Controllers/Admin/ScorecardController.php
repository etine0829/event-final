<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Scorecard; // Ensure Scorecard model is imported
use Illuminate\Http\Request;

class ScorecardController extends Controller
{

    public function store(Request $request)
    {
        $participantScores = [];

        foreach ($request->input('scores') as $participantId => $scoreData) {
            $totalScore = 0;
            $criteriaCount = 0;

            foreach ($scoreData['criteria_scores'] as $criteriaId => $score) {
                // Check if the score already exists
                $existingScore = Scorecard::where('category_id', $request->input('category_id'))
                    ->where('participant_id', $participantId)
                    ->where('criteria_id', $criteriaId)
                    ->first();

                if ($existingScore) {
                    // Update existing score
                    $existingScore->update(['score' => $score]);
                } else {
                    // Create new score
                    Scorecard::create([
                        'category_id' => $request->input('category_id'),
                        'criteria_id' => $criteriaId,
                        'participant_id' => $participantId,
                        'score' => $score,
                    ]);
                }

                $totalScore += $score;
                $criteriaCount++;
            }

            $avgScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;

            // Update average score
            $scorecard = Scorecard::where('participant_id', $participantId)
                ->where('category_id', $request->input('category_id'))
                ->first();

            if ($scorecard) {
                $scorecard->avg_score = $avgScore;
                $scorecard->save();
            }

            $participantScores[] = [
                'participant_id' => $participantId,
                'avg_score' => $avgScore,
            ];
        }

        // Rank participants based on avg_score
        usort($participantScores, function ($a, $b) {
            return $b['avg_score'] <=> $a['avg_score'];
        });

        $rank = 1;
        foreach ($participantScores as $participantScore) {
            Scorecard::where('participant_id', $participantScore['participant_id'])
                ->where('category_id', $request->input('category_id'))
                ->update(['rank' => $rank]);
            $rank++;
        }

        return redirect()->route('judge.dashboard')->with('success', 'Scores saved successfully!');
    }

    public function update(Request $request)
    {
        $participantScores = [];

        foreach ($request->input('scores') as $participantId => $scoreData) {
            $totalScore = 0;
            $criteriaCount = 0;

            foreach ($scoreData['criteria_scores'] as $criteriaId => $score) {
                $existingScore = Scorecard::where('category_id', $request->input('category_id'))
                    ->where('participant_id', $participantId)
                    ->where('criteria_id', $criteriaId)
                    ->first();

                if ($existingScore) {
                    // Update score
                    $existingScore->update(['score' => $score]);
                }

                $totalScore += $score;
                $criteriaCount++;
            }

            $avgScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;

            $scorecard = Scorecard::where('participant_id', $participantId)
                ->where('category_id', $request->input('category_id'))
                ->first();

            if ($scorecard) {
                $scorecard->avg_score = $avgScore;
                $scorecard->save();
            }

            $participantScores[] = [
                'participant_id' => $participantId,
                'avg_score' => $avgScore,
            ];
        }

        usort($participantScores, function ($a, $b) {
            return $b['avg_score'] <=> $a['avg_score'];
        });

        $rank = 1;
        foreach ($participantScores as $participantScore) {
            Scorecard::where('participant_id', $participantScore['participant_id'])
                ->where('category_id', $request->input('category_id'))
                ->update(['rank' => $rank]);
            $rank++;
        }

        return redirect()->route('judge.dashboard')->with('success', 'Scores updated successfully!');
    }
}
