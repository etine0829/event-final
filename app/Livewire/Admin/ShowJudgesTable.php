<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;  
use App\Models\Admin\Event; 
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

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

    public function updatedSelectedEvent()
    {
        $this->updateCategory();
    }

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->judgeToShow = []; // Initialize as an empty array for simplicity
        $this->eventToShow = null;
    }

    public function assignJudgeToEvent()
{
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

    // Update the judge's event_id field with the selected event ID
    $judge->event_id = $this->selectedEvent;
    $judge->save();

    // Flash success message
    session()->flash('success', 'Judge successfully assigned to the event.');

    // Optionally reset the selected judge after assignment
    $this->reset(['selectedJudge']);
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
    $judges = $query->orderBy($this->sortField, $this->sortDirection)->paginate(100);

    // Fetch all events
    $events = Event::all();

    $judgeCounts = User::select('event_id', \DB::raw('count(*) as judge_count'))
        ->groupBy('event_id')
        ->get()
        ->keyBy('event_id');

    return view('livewire.admin.show-judges-table', [
        'judges' => $judges,
        'events' => $events,
        'judgeCounts' => $judgeCounts,
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