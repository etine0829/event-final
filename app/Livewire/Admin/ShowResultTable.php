<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Scorecard;
use App\Models\Admin\Participant;
use App\Models\Admin\Criteria;
use App\Models\Admin\Category;
use App\Models\Admin\Event;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ShowResultTable extends Component
{
    public $selectedEvent; // The selected event ID
    public $categories = []; // Holds categories with scores and participants
    public $judges = []; // Holds the list of judges
    public $participants = [];
    public $scores = []; // Declare the scores property to store individual score data

    protected $listeners = ['updateResult'];

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null); // Get selected event from session
        $this->loadCategories(); // Load categories and related data
    }

    public function updatedSelectedEvent()
    {
        session(['selectedEvent' => $this->selectedEvent]);
        $this->loadCategories(); // Reload categories and scorecards on event selection
    }

    public function updateCategory()
    {
        // Logic for updating categories or refreshing data
        $this->loadCategories(); // Call the method to refresh categories
    }

    private function loadCategories()
    {
        $this->categories = []; // Reset categories array
        

        // Fetch the selected event and get users who are assigned as 'judge' for this event
        $this->judges = User::where('event_id', $this->selectedEvent)
                            ->where('role', 'judge')
                            ->get();

        
        // Fetch categories for the selected event
        $categories = Category::where('event_id', $this->selectedEvent)
            ->with([
                'criteria',
                'criteria.scorecards',
                'criteria.scorecards.participant'
            ])
            ->get();

        foreach ($categories as $category) {
            $categoryScore = $category->score ?? 100; // Default to 100 if null

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

            // Fetch participants and their scorecards
            $participants = $category->criteria
            ->flatMap(fn($criteria) => $criteria->scorecards->load('participant'))  // Eager load 'participant' here
            ->groupBy(fn($scorecard) => $scorecard->participant->id ?? null);

            foreach ($participants as $participantId => $scorecards) {
            if ($participantId === null) continue;

            $participant = $scorecards->first()->participant;  // Ensure participant is available

            // Check if the participant is not null
            if ($participant) {
                $participantData = [
                    'id' => $participant->id,
                    'participant_no' => $participant->participant_number,  // Access the participant_number field
                    'name' => $participant->participant_name,
                    'scores' => [],
                    'deduction' => $participant->deduction ?? 0,
                    'rank' => 0,
                ];
            }

                // Populate scores per criteria if needed
                foreach ($category->criteria as $criteria) {
                    $score = $scorecards->firstWhere('criteria_id', $criteria->id)?->score ?? null;
                    $participantData['scores'][$criteria->id] = $score;

                    // Store scores in the $scores property for later use or editing
                    $this->scores[$category->id][$participant->id][$criteria->id] = $score;
                }

                $categoryData['participants'][] = $participantData;
            }

            // Rank participants based on their total rank or score (lower score/rank is better)
            $ranks = collect($categoryData['participants'])
                ->sortBy('total_score') // Sort participants based on total_score (ascending order)
                ->values(); // Reindex the collection

            // Assign ranks based on sorted order but preserve original order
            foreach ($categoryData['participants'] as $key => &$participant) {
                $participant['rank'] = $ranks->search(function ($rank) use ($participant) {
                    return $rank['id'] === $participant['id'];
                }) + 1; // Rank starts from 1
            }

            // Add the category data to the overall categories array
            $this->categories[] = $categoryData;
        }
    }

    public function updatedScores($value, $key)
    {
        // The key will be like: "categoryId.participantId.criteriaId"
        list($categoryId, $participantId, $criteriaId) = explode('.', $key);

        // Update the score in the array
        $this->scores[$categoryId][$participantId][$criteriaId] = $value;

        // Store the updated scores in the session
        session()->put('scores', $this->scores);

        // Recalculate ranks for the category after the score has been updated
        $this->recalculateRanks($categoryId);
    }


    private function recalculateRanks($categoryId)
    {
    // Fetch the category data again
    $category = Category::find($categoryId);
    $categoryData = [];

    // Fetch participants and their scorecards for the category
    $participants = $category->criteria
        ->flatMap(fn($criteria) => $criteria->scorecards)
        ->groupBy(fn($scorecard) => $scorecard->participant->id ?? null);

    foreach ($participants as $participantId => $scorecards) {
        if ($participantId === null) continue;

        $participant = $scorecards->first()->participant;

        // Initialize total score and rank
        $totalScore = 0;
        $totalRank = 0;
        $judgeCount = 0;

        // Calculate the total score based on the ranks (or scores)
        foreach ($scorecards as $scorecard) {
            $totalRank += $scorecard->rank; // Use rank or score here, depending on your logic
            $judgeCount++;
        }

        // Calculate total_score as the sum of ranks or other relevant data
        $avgRank = $judgeCount > 0 ? $totalRank / $judgeCount : 0;

        // Initialize the participant data with total_score or total_rank
        $participantData = [
            'id' => $participant->id,
            'participant_no' => $participant->participant_number,
            'name' => $participant->participant_name,
            'scores' => [], // Store individual scores per criteria
            'avg_rank' => number_format($avgRank, 2), // Store the average rank (lower is better)
            'deduction' => $participant->deduction ?? 0, // Deduction if any
            'total_score' => $totalRank, // Use total_score instead of total_rank if needed
            'rank' => 0, // Placeholder rank, will be calculated later
        ];

        // Populate scores per criteria if needed
        foreach ($category->criteria as $criteria) {
            $score = $scorecards->firstWhere('criteria_id', $criteria->id)?->score ?? null;
            $participantData['scores'][$criteria->id] = $score;

            // Store scores in the $scores property for later use or editing
            $this->scores[$category->id][$participant->id][$criteria->id] = $score;
        }

        $categoryData['participants'][] = $participantData;
    }

    // Rank participants based on their total rank or score (lower score/rank is better)
    $ranks = collect($categoryData['participants'])
        ->sortBy('total_score') // Sort participants based on total_score (ascending order)
        ->values(); // Reindex the collection

    // Assign ranks based on sorted order but preserve original order
    foreach ($categoryData['participants'] as $key => &$participant) {
        $participant['rank'] = $ranks->search(function ($rank) use ($participant) {
            return $rank['id'] === $participant['id'];
        }) + 1; // Rank starts from 1
    }

    // Update the category's participants with the new ranks
    $this->categories = array_map(function ($category) use ($categoryData) {
        if ($category['id'] === $categoryId) {
            $category['participants'] = $categoryData['participants'];
        }
        return $category;
    }, $this->categories);

    // Trigger update of the category list to reflect the new ranks
    $this->loadCategories();
}



    public function render()
    {
        // Fetch events to display in the dropdown
        $events = Event::all();

        return view('livewire.admin.show-result-table', [
            'events' => $events,
            'categories' => $this->categories,
        ])->layout('layouts.portal');
    }
}
