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

        // Load participants along with their group and event details for the category's event
        $this->participants = Participant::where('event_id', $this->category->event_id)
                                          ->with(['group', 'event']) // Eager load the group and event relationships
                                          ->get();

        // Load groups for the event associated with the category
        $this->groups = Group::where('event_id', $this->category->event_id)->get();

        // Load events (if necessary) for the category
        $this->events = Event::where('id', $this->category->event_id)->get();

        // Load criteria for the specific category and event
        $this->criteria = Criteria::where('event_id', $this->category->event_id)
                                  ->where('category_id', $categoryId)
                                  ->get();
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
