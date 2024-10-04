<?php

namespace App\Livewire\Admin;

use \App\Models\Admin\Event; 
use \App\Models\Admin\Participant; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
class ShowParticipantTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortDirection = 'asc';
    public $selectedEvent = null;
    public $selectedParticipant = null;
    public $sortField = 'id';
    public $participantToShow;
    public $eventToShow;

    protected $listeners = ['updateCategory'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->participantToShow = collect([]); // Initialize as an empty collection
        $this->eventToShow = collect([]); // Initialize as an empty collection
    }

    public function updatingSelectedEvent()
    {
        $this->resetPage();
    }
    


    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

public function render()
    {
        $query = Participant::with('event');

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply selected school filter
        if ($this->selectedEvent) {
            $query->where('event_id', $this->selectedEvent);
            $this->eventToShow = Event::findOrFail($this->selectedEvent);
        } else {
            $this->eventToShow = null; // Reset schoolToShow if no school is selected
        }

        $participants = $query->orderBy($this->sortField, $this->sortDirection)
                             ->paginate(25);

        $events = Event::all();


         $participantCounts = Participant::select('event_id', \DB::raw('count(*) as participant_count'))
                                  ->groupBy('event_id')
                                  ->get()
                                  ->keyBy('event_id');

        return view('livewire.admin.show-participant-table', [
            'participants' => $participants,
            'events' => $events,
            'participantCounts' => $participantCounts,
        ]);
    }

    public function updateCategory()
    {
        // Update participantToShow based on selected school
        if ($this->selectedEvent) {
            $this->participantToShow = Participant::where('event_id', $this->selectedEvent)
                ->get(); // Ensure this returns a collection
        } else {
            $this->participantToShow = collect(); // Reset to empty collection if no school is selected
        }

        
    }

    protected function applySearchFilters($query)
{
    return $query->where(function (Builder $query) {
        $query->where('id', 'like', '%' . $this->search . '%')
            ->orWhere('participant_photo', 'like', '%' . $this->search . '%')        
            ->orWhere('participant_name', 'like', '%' . $this->search . '%')
            ->orWhere('participant_gender', 'like', '%' . $this->search . '%')
            ->orWhere('participant_comment', 'like', '%' . $this->search . '%')
            ->orWhere('participant_department', 'like', '%' . $this->search . '%')
            ->orWhereHas('event', function (Builder $query) {
                $query->where('event_name', 'like', '%' . $this->search . '%')
                    ->orWhere('venue', 'like', '%' . $this->search . '%')
                    ->orWhere('type_of_scoring', 'like', '%' . $this->search . '%');
            });
    });
}

    
}	
