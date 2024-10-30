<?php

namespace App\Livewire\Admin;

use \App\Models\Admin\Event; 
use \App\Models\Admin\Group; 
use \App\Models\Admin\Participant; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;


class ShowParticipantTable extends Component
{
    use WithPagination; 
    public $search = '';
    public $sortField = 'id';
    public $sortDirection = 'asc';
    public $selectedEvent = null;
    public $selectedGroup = null;
    public $groupsToShow;
    public $eventToShow;
    public $groupToShow;

    protected $listeners = ['updateEmployees', 'updateEmployeesByDepartment'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->selectedGroup = session('selectedGroup', null);
        $this->groupsToShow = collect([]); // Initialize as an empty collection
        $this->eventToShow = collect([]); // Initialize as an empty collection
        $this->groupToShow = collect([]);
    }

    public function updatingSelectedEvent()
    {
        $this->resetPage();
        $this->updateEmployees();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
        $this->updateEmployeesByDepartment();
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
        $query = Participant::with('group')->with('event');

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply selected event filter
        if ($this->selectedEvent) {
            $query->where('event_id', $this->selectedEvent);
            $this->eventToShow = Event::findOrFail($this->selectedEvent);
    
            // Set the type_of_scoring based on the selected event
            $this->type_of_scoring = $this->eventToShow->type_of_scoring;
        } else {
            $this->eventToShow = null;
            $this->type_of_scoring = null; // Reset if no event is selected
        }

        // Apply selected category filter
        if ($this->selectedGroup) {
            $query->where('group_id', $this->selectedGroup);
            $this->groupToShow = Group::find($this->selectedGroup);
        } else {
            $this->groupToShow = null; // Reset departmentToShow if no department is selected
        }

        $participants = $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);


        $events = Event::all();
        // $departments = Department::where('school_id', $this->selectedEvent)->get();
        $groups = Group::where('event_id', $this->selectedEvent)
        ->get(['id','group_name']);



        // Count employees by department
        $groupCounts = Participant::select('event_id', \DB::raw('count(*) as group_count'))
            ->groupBy('event_id')
            ->get()
            ->keyBy('event_id');

        return view('livewire.admin.show-participant-table', [
            'participants' => $participants,
            'events' => $events,
            'groups' => $groups,
            'groupCounts' => $groupCounts,
            'type_of_scoring' => $this->type_of_scoring,
        ]);
    }

    public function updateEmployees()
    {
        // Update groupToShow based on selected school
        if ($this->selectedEvent) {
            $this->groupsToShow = Group::where('event_id', $this->selectedEvent)->get();
        } else {
            $this->groupsToShow = collect(); // Reset to empty collection if no school is selected
        }

        // Ensure departmentToShow is reset if the selected school changes
        $this->selectedGroup = null;
        $this->groupToShow = null;
    }

public function updateEmployeesByDepartment()
{
    if ($this->selectedGroup && $this->selectedEvent) {
        $this->groupToShow = Group::where('id', $this->selectedGroup)
                                            ->where('event_id', $this->selectedEvent)
                                            ->first();
    } else {
        $this->groupToShow = null;
        
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
                ->orWhereHas('group', function (Builder $query) {
                $query->where('group_name', 'like', '%' . $this->search . '%');
            });
        });
    }
    
}