<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;  
use \App\Models\Admin\Event; 
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class ShowJudgesTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortDirection = 'asc';
    public $selectedEvent = 0;
    public $sortField = 'id';
    public $judgeToShow;
    public $eventToShow;

    protected $listeners = ['updateCategory'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->judgeToShow = collect([]); // Initialize as an empty collection
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
        // Query to fetch judges
        $query = User::with('event');

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply event filter
        if($this->selectedEvent === '0'){
            $this->eventToShow = null;
            $this->judgeToShow = null;
        }
        else if ($this->selectedEvent) {
            $query->where('event_id', $this->selectedEvent);
            $this->eventToShow = Event::findOrFail($this->selectedEvent);
            $this->judgeToShow = User::where('event_id', $this->selectedEvent)
            ->get();


            
        } else {
            $this->eventToShow = null;
            $this->judgeToShow = null;
        }

        
        // Fetch sorted judges with pagination
        $judges = $query->orderBy($this->sortField, $this->sortDirection)
                        ->paginate(25);



        $events = Event::all();

        // Count the number of judges for each event
        $judgeCounts = User::select('event_id', \DB::raw('count(*) as judge_count'))
                           ->groupBy('event_id')
                           ->get()
                           ->keyBy('event_id');
  

        return view('livewire.admin.show-judges-table', [
            'judges' => $judges,
            'events' => $events,
            'judgeCounts' => $judgeCounts,
            'judgeToShow' => $this->judgeToShow,
        ]);
    }

    public function updateCategory()
    {
        // Update judgeToShow based on selected event
        if ($this->selectedEvent) {
            $this->judgeToShow = User::where('event_id', $this->selectedEvent)
                                     ->get();
        } else {
            $this->judgeToShow = collect(); // Reset to empty collection if no event is selected
        }
    }

    protected function applySearchFilters($query)
    {
        return $query->where(function (Builder $query) {
            $query->where('id', 'like', '%' . $this->search . '%')        
                  ->orWhere('name', 'like', '%' . $this->search . '%')
                  ->orWhere('picture', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('event', function (Builder $query) {
                      $query->where('event_name', 'like', '%' . $this->search . '%')
                            ->orWhere('venue', 'like', '%' . $this->search . '%')
                            ->orWhere('type_of_scoring', 'like', '%' . $this->search . '%');
                  });
        });
    }
}
