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
    
        $loggedInJudgeId = Auth::id(); // Get the logged-in judge's ID
    
        // Fetch categories for the selected event along with criteria and scorecards
        $categories = Category::where('event_id', $this->eventId)
            ->with(['criteria', 'criteria.scorecards' => function ($query) use ($loggedInJudgeId) {
                $query->where('user_id', $loggedInJudgeId); // Filter scores by logged-in judge
            }, 'criteria.scorecards.participant'])
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
    


    public function render()
    {
        return view('livewire.admin.show-scores', [
            'categories' => $this->categories,
        ])->layout('layouts.portal');
    }
}