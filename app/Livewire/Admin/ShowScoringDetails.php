<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Category;
use App\Models\Admin\Participant;
use App\Models\Admin\Criteria;
use App\Models\Admin\Group;  // Ensure Group model is imported
use App\Models\Admin\Event;  // Ensure Event model is imported
use App\Models\Admin\Scorecard; // Add the Score model
use Illuminate\Support\Facades\DB; // Use for transaction handling


class ShowScoringDetails extends Component
{
    public $category;
    public $participants = [];
    public $criteria = [];
    public $groups = [];
    public $events = [];
    public $genderFilter = 'all'; // Default filter
    public $scores = [];

    public function mount($categoryId)
    {
        $this->category = Category::findOrFail($categoryId);

        $this->participants = Participant::where('event_id', $this->category->event_id)
                                        ->with(['group', 'event'])
                                        ->get();

        $this->criteria = Criteria::where('event_id', $this->category->event_id)
                                ->where('category_id', $categoryId)
                                ->get();

        // Load existing scores for participants
        foreach ($this->participants as $participant) {
            foreach ($this->criteria as $criterion) {
                $existingScore = Scorecard::where('category_id', $categoryId)
                                        ->where('participant_id', $participant->id)
                                        ->where('criteria_id', $criterion->id)
                                        ->first();
                $this->scores[$participant->id][$criterion->id] = $existingScore ? $existingScore->score : null;
            }
        }
    }

    public function updatedScores($value, $key)
    {
        // The key will be like: "participantId.criteriaId"
        list($participantId, $criteriaId) = explode('.', $key);

        $this->scores[$participantId][$criteriaId] = $value;

        
    }


    public function updatedGenderFilter()
    {
        // When the gender filter changes, update the participants
        $this->participants = Participant::when($this->genderFilter != 'all', function($query) {
            $query->where('participant_gender', $this->genderFilter);
        })
        ->where('event_id', $this->category->event_id)
        ->with(['group', 'event']) // Ensure to load group and event relations
        ->get();
    }

        public function saveScores()
    {
        foreach ($this->scores as $participantId => $criteriaScores) {
            foreach ($criteriaScores as $criteriaId => $score) {
                Scorecard::updateOrCreate(
                    [
                        'category_id' => $this->category->id,
                        'participant_id' => $participantId,
                        'criteria_id' => $criteriaId,
                    ],
                    ['score' => $score]
                );
            }
        }
        session()->flash('success', 'Scores saved successfully!');
    }


    public function render()
    {
        return view('livewire.admin.show-scoring-details', [
            'category' => $this->category,
            'participants' => $this->participants,
            'criteria' => $this->criteria,
            'groups' => $this->groups, // Pass groups to the view
            'events' => $this->events, // Pass events to the view
        ])->layout('layouts.portal'); // Chain layout method correctly here
    }
}
