<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Admin\Event;
use App\Models\Admin\Category;
use App\Models\Admin\Criteria;
use App\Models\Admin\Participant;
use App\Models\User;

class JudgePortal extends Component
{
    public $events;
    public $categories;
    public $judges;
    
    public $participants = [];
    public $criteria = [];
    public $selectedCategoryToShow;
    public $isLoading = false;

    public $selectedEvent = null;
    public $selectedCategory = null;
    public $showSelectedCateegory;
    public $categoriesToShow;
    public $eventToShow;

    protected $listeners = ['updateCategory', 'showDepartmentSchedule'];

    public function mount()
    {
        $this->events = collect();
        $this->categories = collect();
        $this->selectedCategory = null;
        $this->participants = [];
        $this->criteria = [];

        $this->selectedEvent = session('selectedEvent', null);
        $this->selectedCategory = session('selectedCategory', null);
        $this->showSelectedCategory = session('showSelectedCategory', null);

        $this->categoriesToShow = collect([]); // Initialize as an empty collection
        $this->eventToShow = collect([]);

    }

    public function loadCategoryDetails($categoryId)
    {
        // Clear previous data
        $this->criteria = collect();
        $this->participants = collect();

        // Load selected category with criteria
        $this->selectedCategory = Category::with('criteria')->find($categoryId);

        if ($this->selectedCategory) {
            $eventId = $this->selectedCategory->event_id;

            // Load participants for the event
            $this->participants = Participant::where('event_id', $eventId)
                ->with('group') // Ensure the group relationship is defined in Participant model
                ->get();

            // Load criteria for the selected category
            $this->criteria = $this->selectedCategory->criteria;
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Load events and categories for judges
        if ($user && $user->hasRole('judge')) {
            $this->events = Event::where('id', $user->event_id)->get();
            $this->categories = $user->event ? $user->event->categories : collect();
        }

        // Prepare selected category details if available
        $this->selectedCategoryToShow = $this->selectedCategory
            ? Category::with('criteria')->find($this->selectedCategory->id)
            : null;

        return view('livewire.judge-portal', [
            'events' => $this->events,
            'categories' => $this->categories,
            'selectedCategory' => $this->selectedCategoryToShow,
            'participants' => $this->participants,
            'criteria' => $this->criteria,
        ]);
    }
}
