<?php


namespace App\Livewire\Admin;


use Livewire\Component;
use App\Models\Admin\Scorecard;
use App\Models\Admin\Participant;
use App\Models\Admin\Criteria;
use App\Models\Admin\Category;


class ShowScores extends Component
{
    public $categories = [];
    public $eventId; // The selected event ID


    public $scores = []; // Holds scores to be displayed and updated


    public function mount($eventId)
    {
        $this->eventId = $eventId;
        $this->loadCategories();
    }


    public function loadCategories()
{
    $this->categories = []; // Reset categories array

    // Fetch categories for the selected event along with criteria and scorecards
    $categories = Category::where('event_id', $this->eventId)
        ->with(['criteria', 'criteria.scorecards.participant'])
        ->get();

    foreach ($categories as $category) {
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

        // Fetch participants and their scores for each criterion
        $participants = $category->criteria
            ->flatMap(fn($criteria) => $criteria->scorecards)
            ->groupBy(fn($scorecard) => $scorecard->participant->id ?? null);

        foreach ($participants as $participantId => $scorecards) {
            if ($participantId === null) continue;

            $participant = $scorecards->first()->participant;

            $participantData = [
                'id' => $participant->id,
                'name' => $participant->participant_name,
                'scores' => [],
                'avg_score' => $scorecards->avg('score'),
                'rank' => $scorecards->first()->rank, // Include rank from the scorecard
            ];

            foreach ($category->criteria as $criteria) {
                $score = $scorecards->firstWhere('criteria_id', $criteria->id)?->score ?? null;
                $participantData['scores'][$criteria->id] = $score;

                // Initialize scores array for editing
                $this->scores[$category->id][$participant->id][$criteria->id] = $score;
            }

            $categoryData['participants'][] = $participantData;
        }

        $this->categories[] = $categoryData;
    }
}


    public function updateScores($categoryId)
{
    if (!isset($this->scores[$categoryId])) return;

    // Loop through the scores for the category
    foreach ($this->scores[$categoryId] as $participantId => $criteriaScores) {
        $totalScore = 0;
        $criteriaCount = 0;

        // Loop through each criterion score for the participant
        foreach ($criteriaScores as $criteriaId => $score) {
            // Update or create the score for the participant and criterion
            Scorecard::updateOrCreate(
                [
                    'participant_id' => $participantId,
                    'criteria_id' => $criteriaId,
                    'category_id' => $categoryId, // Ensure the category_id is also considered
                ],
                ['score' => $score]  // Updating the individual score
            );

            // Accumulate the total score and count the criteria
            $totalScore += $score;
            $criteriaCount++;
        }

        // Calculate the average score for the participant
        $avgScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;

        // Update the avg_score for the participant in the category
        // We need to make sure we are updating the right participant and category combination
        Scorecard::where('participant_id', $participantId)
            ->where('category_id', $categoryId)
            ->update(['avg_score' => $avgScore]);
    }

    // Now, update the rank based on the average score
    $this->updateRanks($categoryId);

    // Flash success message
    session()->flash('sweetalert', [
        'type' => 'success', 
        'message' => 'Scores updated successfully!'
    ]);

    // Reload categories to reflect updated scores in the table
    $this->loadCategories();
}

public function updateRanks($categoryId)
{
    // Fetch the scorecards for the category, ordered by average score in descending order
    $scorecards = Scorecard::where('category_id', $categoryId)
        ->with('participant')  // Load the participant details
        ->orderByDesc('avg_score')  // Order by average score, descending
        ->get();

    $rank = 1;  // Start ranking from 1
    $previousAvgScore = null;  // Keep track of the previous participant's average score
    $previousRank = null;  // Initialize the rank for the first participant

    foreach ($scorecards as $scorecard) {
        // If the current participant's avg_score is the same as the previous one, they get the same rank
        if ($scorecard->avg_score === $previousAvgScore) {
            $rank = $previousRank;
        } else {
            // Otherwise, increment the rank
            $rank = $previousRank + 1;
        }

        // Update the rank for this scorecard
        $scorecard->update(['rank' => $rank]);

        // Update the previous score and rank for the next iteration
        $previousAvgScore = $scorecard->avg_score;
        $previousRank = $rank;
    }
}


    public function render()
    {
        return view('livewire.admin.show-scores', [
            'categories' => $this->categories,
        ])->layout('layouts.portal');
    }
}

