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
    public $userToShow = [];  // Changed from $judgeToShow to $userToShow to include both judges and staff
    public $eventToShow;
    public $selectedUser;  // Changed from selectedJudge to selectedUser for flexibility
    public $role = 'judge';  // Added a role selector to filter between judge or staff

    protected $listeners = ['updateCategory'];

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->userToShow = [];    
        $this->eventToShow = null;
    }

    public function assignUserToEvent()
    {
        // Ensure admin privileges
        if (Auth::user()->hasRole('admin')) {
            // Ensure we have a valid event and user selected
            if (!$this->selectedEvent || !$this->selectedUser) {
                session()->flash('error', 'Please select both an event and a user.');
                return;
            }

            // Find the event and user by their IDs
            $event = Event::find($this->selectedEvent);
            $user = User::find($this->selectedUser);  // User can be either a judge or staff

            // Check if event and user exist
            if (!$event || !$user) {
                session()->flash('error', 'Invalid event or user.');
                return;
            }

            // Check if the user is already assigned to the selected event
            if ($user->event_id == $this->selectedEvent) {
                return redirect()->route('admin.judge.index')->with('error', 'User is already assigned to this event.');
            }

            // Update the user's event_id field with the selected event ID
            $user->event_id = $this->selectedEvent;
            $user->save();

            // Flash success message
            return redirect()->route('admin.judge.index')->with('success', 'User added successfully.');
        }else{
            // Ensure we have a valid event and user selected
            if (!$this->selectedEvent || !$this->selectedUser) {
                session()->flash('error', 'Please select both an event and a user.');
                return;
            }

            // Find the event and user by their IDs
            $event = Event::find($this->selectedEvent);
            $user = User::find($this->selectedUser);  // User can be either a judge or staff

            // Check if event and user exist
            if (!$event || !$user) {
                session()->flash('error', 'Invalid event or user.');
                return;
            }

            // Check if the user is already assigned to the selected event
            if ($user->event_id == $this->selectedEvent) {
                return redirect()->route('event_manager.judge.index')->with('error', 'User is already assigned to this event.');
            }

            // Update the user's event_id field with the selected event ID
            $user->event_id = $this->selectedEvent;
            $user->save();

            // Flash success message
            return redirect()->route('event_manager.judge.index')->with('success', 'User added successfully.');
        }
    }

    public function deleteJudge($judgeId)
    {
        // Remove the judge from the $userToShow array
        $this->userToShow = $this->userToShow->filter(function ($judge) use ($judgeId) {
            return $judge->id !== $judgeId;
        });

        // Flash success message to the session
        session()->flash('success', 'User removed from the table.');
    }


    public function render()
    {
        // Query to fetch users (both judges and staff)
        $query = User::with('event')->whereHas('roles', function ($query) {
            $query->whereIn('name', ['judge', 'staff']);  // Fetch both judge and staff roles
        });

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply event filter if selected
        if ($this->selectedEvent) {
            $this->eventToShow = Event::find($this->selectedEvent);
            $this->userToShow = User::where('event_id', $this->selectedEvent)
                                    ->whereHas('roles', function ($query) {
                                        $query->whereIn('name', ['judge', 'staff']);  // Filter by both judge and staff roles
                                    })
                                    ->get();
        } else {
            $this->eventToShow = null;
            $this->userToShow = [];
        }

        // Fetch sorted users with pagination
        $users = $query->orderBy($this->sortField, $this->sortDirection)->paginate(25);

        // Fetch all events
        $events = Event::all();

        return view('livewire.admin.show-judges-table', [
            'users' => $users,
            'events' => $events,
            'userToShow' => $this->userToShow, // Pass the updated users list
        ]);
    }

    public function updateCategory()
    {
        if ($this->selectedEvent) {
            $this->userToShow = User::where('event_id', $this->selectedEvent)
                ->whereIn('name', ['judge', 'staff'])  // Include both roles
                ->get();

        } else {
            $this->userToShow = []; // Reset to empty collection if no event is selected
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
