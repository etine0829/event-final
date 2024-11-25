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
    public $judgeToShow = [];
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
        $this->judgeToShow = [];    
        $this->eventToShow = null;
    }

    public function assignJudgeToEvent()
    {
        if (!$this->selectedEvent || !$this->selectedJudge) {
            session()->flash('error', 'Please select both an event and a judge.');
            return;
        }

        $event = Event::find($this->selectedEvent);
        $judge = User::find($this->selectedJudge);

        if (!$event || !$judge) {
            session()->flash('error', 'Invalid event or judge.');
            return;
        }

        if ($judge->event_id == $this->selectedEvent) {
            return redirect()->route('admin.judge.index')->with('error', 'Judge is already assigned to this event.');
        }

        $isJudgeChairman = $judge->roles->contains('name', 'judge_chairman');

        if ($isJudgeChairman) {
            $existingChairman = User::where('event_id', $this->selectedEvent)
                                    ->whereHas('roles', function ($query) {
                                        $query->where('name', 'judge_chairman');
                                    })
                                    ->exists();

            if ($existingChairman) {
                return redirect()->route('admin.judge.index')->with('error', 'Only one Chairman Judge');
            }
        }

        $judge->event_id = $this->selectedEvent;
        $judge->save();

        return redirect()->route('admin.judge.index')->with('success', 'Judge updated successfully.');
    }

    public function updateCategory()
    {
        if ($this->selectedEvent) {
            $this->judgeToShow = User::where('event_id', $this->selectedEvent)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['judge', 'judge_chairman']);
                })
                ->get();
        } else {
            $this->judgeToShow = [];
        }
    }

    public function render()
    {
        $query = User::with('event')
            ->whereNull('event_id')
            ->whereHas('roles', function ($query) {
                $query->whereIn('name', ['judge', 'judge_chairman']);
            });

        $query = $this->applySearchFilters($query);

        if ($this->selectedEvent) {
            $this->eventToShow = Event::find($this->selectedEvent);
            $this->judgeToShow = User::where('event_id', $this->selectedEvent)
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['judge', 'judge_chairman']);
                })
                ->get();
        } else {
            $this->eventToShow = null;
            $this->judgeToShow = [];
        }

        $judges = $query->orderBy($this->sortField, $this->sortDirection)->paginate(25);

        $events = Event::all();

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