<?php

namespace App\Livewire\Admin;

use App\Models\Admin\Event;
use App\Models\Admin\Category;
use App\Models\Admin\Result;
use Livewire\Component;
use Livewire\WithPagination;

class ShowResultTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortDirection = 'asc';
    public $selectedEvent = null;
    public $selectedResult = null;
    public $sortField = 'id';
    public $resultToShow;
    public $eventToShow;
    public $type_of_scoring;
    public $categories = []; // Categories property
    public $criteria = [];   // Criteria property

    protected $listeners = ['updateResult'];

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->resultToShow = collect([]);
        $this->eventToShow = collect([]);
        $this->type_of_scoring = null;
        $this->categories = collect([]);
        $this->criteria = collect([]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedEvent()
    {
        $this->resetPage();
        $this->loadCategoriesAndCriteria();
    }

    public function loadCategoriesAndCriteria()
{
    // Check if event is selected
    if ($this->selectedEvent) {
        // Load the event
        $this->eventToShow = Event::findOrFail($this->selectedEvent);

        // Check if categories are being loaded
        $this->categories = $this->eventToShow->categories;
        
        // Check loaded categories
        dd($this->categories);  // Debugging categories

        // Set the scoring type
        $this->type_of_scoring = $this->eventToShow->type_of_scoring;
    } else {
        $this->eventToShow = null;
        $this->categories = collect([]);
        $this->type_of_scoring = null;
    }
}


    public function render()
    {
        $query = Result::with('event');

        if ($this->selectedEvent) {
            $query->where('event_id', $this->selectedEvent);
        }

        $results = $query->orderBy($this->sortField, $this->sortDirection)
                         ->paginate(25);

        $events = Event::all();

        return view('livewire.admin.show-result-table', [
            'results' => $results,
            'events' => $events,
            'type_of_scoring' => $this->type_of_scoring,
            'categories' => $this->categories, // Pass categories directly to the view
        ]);
    }

    public function updateResult()
    {
        if ($this->selectedEvent) {
            $this->resultToShow = Result::where('event_id', $this->selectedEvent)
                                        ->get();
        } else {
            $this->resultToShow = collect();
        }
    }
}
