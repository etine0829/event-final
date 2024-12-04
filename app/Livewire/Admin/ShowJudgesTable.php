<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;  
use App\Models\Admin\Event; 
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ShowJudgesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortDirection = 'asc';
    public $selectedEvent = 0;
    public $sortField = 'id';
    public $judgeToShow = [];  // Initialize as an array for simplicity
    public $eventToShow;
    public $selectedJudge;

    protected $listeners = ['updateCategory'];

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->judgeToShow = [];    
        $this->eventToShow = null;
    }

    public function assignJudgeToEvent()
{
    if(Auth::user()->hasRole('admin')){
        // Ensure we have a valid event and judge selected
        if (!$this->selectedEvent || !$this->selectedJudge) {
            session()->flash('error', 'Please select both an event and a judge.');
            return;
        }

        // Find the event and judge by their IDs
        $event = Event::find($this->selectedEvent);
        $judge = User::find($this->selectedJudge);  // Assuming judge is a User model

        // Check if event and judge exist
        if (!$event || !$judge) {
            session()->flash('error', 'Invalid event or judge.');
            return;
        }

        // Check if the judge is already assigned to the selected event
        if ($judge->event_id == $this->selectedEvent) {
            return redirect()->route('admin.judge.index')->with('error', 'Judge is already assign to this event   .');
            return;
        }

        if ($judge->event_id == $this->selectedEvent) {
            return redirect()->route('admin.judge.index')->with('error', 'Judge is already assign to this event   .');
            return;
        }

        // Update the judge's event_id field with the selected event ID
        $judge->event_id = $this->selectedEvent;
        $judge->save();

        // Flash success message
        return redirect()->route('admin.judge.index')->with('success', 'Judge added successfully.');
    }else{
        // Ensure we have a valid event and judge selected
        if (!$this->selectedEvent || !$this->selectedJudge) {
            session()->flash('error', 'Please select both an event and a judge.');
            return;
        }

        // Find the event and judge by their IDs
        $event = Event::find($this->selectedEvent);
        $judge = User::find($this->selectedJudge);  // Assuming judge is a User model

        // Check if event and judge exist
        if (!$event || !$judge) {
            session()->flash('error', 'Invalid event or judge.');
            return;
        }

        // Check if the judge is already assigned to the selected event
        if ($judge->event_id == $this->selectedEvent) {
            return redirect()->route('event_manager.judge.index')->with('error', 'Judge is already assign to this event   .');
            return;
        }

        if ($judge->event_id == $this->selectedEvent) {
            return redirect()->route('event_manager.judge.index')->with('error', 'Judge is already assign to this event   .');
            return;
        }

        // Update the judge's event_id field with the selected event ID
        $judge->event_id = $this->selectedEvent;
        $judge->save();

        // Flash success message
        return redirect()->route('event_manager.judge.index')->with('success', 'Judge added successfully.');
    }
}


public function render()
{
    // Query to fetch judges
    $query = User::with('event')->whereHas('roles', function ($query) {
        $query->where('name', 'judge'); // Filter by 'judge' role
    });

    // Apply search filters
    $query = $this->applySearchFilters($query);

    // Apply event filter if selected
    if ($this->selectedEvent) {
        $this->eventToShow = Event::find($this->selectedEvent);
        $this->judgeToShow = User::where('event_id', $this->selectedEvent)
                                 ->whereHas('roles', function ($query) {
                                     $query->where('name', 'judge'); // Filter by 'judge' role
                                 })
                                 ->get();
    } else {
        $this->eventToShow = null;
        $this->judgeToShow = [];
    }

    // Fetch sorted judges with pagination
    $judges = $query->orderBy($this->sortField, $this->sortDirection)->paginate(25);

    // Fetch all events
    $events = Event::all();

    return view('livewire.admin.show-judges-table', [
        'judges' => $judges,
        'events' => $events,
        'judgeToShow' => $this->judgeToShow, // Pass the updated judges list
    ]);
}

    public function updateCategory()
    {
        if ($this->selectedEvent) {
            $this->judgeToShow = User::where('event_id', $this->selectedEvent)->get();
        } else {
            $this->judgeToShow = []; // Reset to empty collection if no event is selected
        }
    }

    protected function applySearchFilters($query)
    {
        return $query->where(function (Builder $query) {
            $query->where('id', 'like', '%' . $this->search . '%')        
                  ->orWhere('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('event', function (Builder $query) {
                      $query->where('event_name', 'like', '%' . $this->search . '%')
                            ->orWhere('venue', 'like', '%' . $this->search . '%')
                            ->orWhere('type_of_scoring', 'like', '%' . $this->search . '%');
                  });
        });
    }
}