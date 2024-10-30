<?php

namespace App\Livewire\Admin;

use \App\Models\Admin\Event; 
use \App\Models\Admin\Group; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;


class ShowGroupTable extends Component
{
    
    use WithPagination;

    public $search = '';
    public $sortDirection = 'asc';
    public $selectedEvent = null;
    public $selectedGroup = null;
    public $sortField = 'id';
    public $groupToShow;
    public $eventToShow;

    protected $listeners = ['updateCategory'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->groupToShow = collect([]); // Initialize as an empty collection
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
        $query = Group::with('event');

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply selected school filter
        if ($this->selectedEvent) {
            $query->where('event_id', $this->selectedEvent);
            $this->eventToShow = Event::findOrFail($this->selectedEvent);
    
            // Set the type_of_scoring based on the selected event
            $this->type_of_scoring = $this->eventToShow->type_of_scoring;
        } else {
            $this->eventToShow = null;
            $this->type_of_scoring = null; // Reset if no event is selected
        }

        $groups = $query->orderBy($this->sortField, $this->sortDirection)
                             ->paginate(25);

        $events = Event::all();


        $groupCounts = Group::select('event_id', \DB::raw('count(*) as group_count'))
                                  ->groupBy('event_id')
                                  ->get()
                                  ->keyBy('event_id');

        return view('livewire.admin.show-group-table', [
            'groups' => $groups,
            'events' => $events,
            'groupCounts' => $groupCounts,
            'type_of_scoring' => $this->type_of_scoring,
        ]);
    }

    public function updateCategory()
    {
        // Update groupToShow based on selected school
        if ($this->selectedEvent) {
            $this->groupToShow = Group::where('event_id', $this->selectedEvent)
                ->get(); // Ensure this returns a collection
        } else {
            $this->groupToShow = collect(); // Reset to empty collection if no school is selected
        }

        
    }

    protected function applySearchFilters($query)
{
    return $query->where(function (Builder $query) {
        $query->where('id', 'like', '%' . $this->search . '%')        
            ->orWhere('group_name', 'like', '%' . $this->search . '%')
            ->orWhereHas('event', function (Builder $query) {
                $query->where('event_name', 'like', '%' . $this->search . '%')
                    ->orWhere('venue', 'like', '%' . $this->search . '%')
                    ->orWhere('type_of_scoring', 'like', '%' . $this->search . '%');
            });
    });
}

    
}