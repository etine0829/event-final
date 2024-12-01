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
    public $search = '';
    public $selectedEvent; // The selected event ID
    public $categories = []; // Holds categories with scores and participants
    public $judges = []; // Holds the list of judges
    public $participants = [];
    public $scores = []; // Declare the scores property to store individual score data
    public $eventToShow;

    protected $listeners = ['updateResult'];

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null); // Get selected event from session
        $this->loadCategories(); // Load categories and related data
        $this->eventToShow = collect([]);
    }

    public function updatedSelectedEvent()
    {
        session(['selectedEvent' => $this->selectedEvent]);
        $this->loadCategories(); // Reload categories and scorecards on event selection
    }

    public function updateCategory()
    {
            // Update categoryToShow based on selected school
        if ($this->selectedEvent) {
            $this->categoryToShow = Category::where('event_id', $this->selectedEvent)
                ->get(); // Ensure this returns a collection
        } else {
            $this->categoryToShow = collect(); // Reset to empty collection if no school is selected
        }
    
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
                'category_score' => $category->score,
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

            // Add the category data to the overall categories array
            $this->categories[] = $categoryData;
        }
    }

    public function render()
    {
        // Fetch events to display in the dropdown
        $events = Event::all();

        if ($this->selectedEvent) {
            // Instead of using $query, just fetch the selected event directly
            $this->eventToShow = Event::findOrFail($this->selectedEvent);
            $this->type_of_scoring = $this->eventToShow->type_of_scoring;
        } else {
            $this->eventToShow = null;
            $this->type_of_scoring = null; // Reset if no event is selected
        }

        return view('livewire.admin.show-result-table', [
            'events' => $events,
            'results' => $this->categories,
            'categories' => $this->categories,
            'type_of_scoring' => $this->type_of_scoring,
        ])->layout('layouts.portal');
    }
}
