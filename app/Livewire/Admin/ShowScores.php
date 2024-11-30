<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Scorecard;
use App\Models\Admin\Participant;
use App\Models\Admin\Criteria;
use App\Models\Admin\Category;
use Illuminate\Support\Facades\Auth;

class ShowScores extends Component
{
    public $categories = []; // Holds categories with scores and participants
    public $eventId; // The selected event ID
    public $scores = []; // Holds scores to be displayed and updated
    public $genderFilter = 'all';

    public function mount($eventId)
    {
        $this->eventId = $eventId; // Set the event ID
        $this->loadCategories(); // Load categories and related data
    }

    public function loadCategories()
{
    $this->categories = []; // Reset categories array
    $loggedInJudgeId = Auth::id(); // Get the logged-in judge's ID

    // Fetch categories for the selected event
    $categories = Category::where('event_id', $this->eventId)
        ->with([
            'criteria',
            'criteria.scorecards' => function ($query) use ($loggedInJudgeId) {
                $query->where('user_id', $loggedInJudgeId); // Filter scores by logged-in judge
            },
            'criteria.scorecards.participant',  // Fetch participants related to scorecards
            'criteria.scorecards.participant.group'  // Eager load the group for each participant
        ])
        ->get();

    foreach ($categories as $category) {
        $categoryScore = $category->score ?? 100; // Fetch category_score, default to 100 if null

        $categoryData = [
            'id' => $category->id,
            'name' => $category->category_name,
            'criteria' => [],
            'participants' => [],
        ];

        // Fetch criteria
        foreach ($category->criteria as $criteria) {
            $categoryData['criteria'][] = [
                'id' => $criteria->id,
                'name' => $criteria->criteria_name,
            ];
        }

        // Fetch participants and their scores
        $participants = $category->criteria
            ->flatMap(fn($criteria) => $criteria->scorecards)
            ->groupBy(fn($scorecard) => $scorecard->participant->id ?? null);

        // Determine the scoring type for the event
        $event = $category->event;
        $isPointsScoring = $event->type_of_scoring === 'points';

        foreach ($participants as $participantId => $scorecards) {
            if ($participantId === null) continue;

            $participant = $scorecards->first()->participant;

            // Apply gender filter: only include male/female participants based on the filter
            if ($this->genderFilter !== 'all' && $participant->gender !== $this->genderFilter) {
                continue; // Skip participant if they don't match the filter
            }

            // Initialize the total score
            $totalScore = 0;
            $totalCriteriaScore = 0; // For ranking calculation

            // Calculate the total score for all criteria
            foreach ($category->criteria as $criteria) {
                $score = $scorecards->firstWhere('criteria_id', $criteria->id)?->score ?? 0;
                $totalScore += $score;
                $totalCriteriaScore += $score;
            }

            // Calculate avg_score based on scoring type
            $avgScore = 0;
            if ($isPointsScoring) {
                $avgScore = ($totalScore) * ($categoryScore / 100);
            } else {
                $avgScore = $totalCriteriaScore;
            }

            // Format the avg_score for consistency
            $participantData = [
                'id' => $participant->id,
                'participant_no' => $participant->participant_number,
                'name' => $participant->participant_name,
                'gender' => $participant->gender,
                'scores' => [],
                'avg_score' => number_format($avgScore, 2),
                'group' => $participant->group,  // Add group data here
            ];

            // Fetch individual scores for each criteria
            foreach ($category->criteria as $criteria) {
                $score = $scorecards->firstWhere('criteria_id', $criteria->id)?->score ?? null;
                $participantData['scores'][$criteria->id] = $score;

                $this->scores[$category->id][$participant->id][$criteria->id] = $score;
            }

            $categoryData['participants'][] = $participantData;
        }

        $this->categories[] = $categoryData;
    }
}


    public function render()
    {
        return view('livewire.admin.show-scores', [
            'categories' => $this->categories,
        ])->layout('layouts.portal');
    }
}
