<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Event;
use App\Models\Admin\Category;

class JudgePortal extends Component
{
    public $events;
    public $categories;
    public $scoringType;  // To hold the event's scoring type

    public function mount()
    {
        $this->AssignedEvents();
        $this->AssignedCategories();
    }

    public function AssignedEvents()
    {
        $user = Auth::user();

        if ($user->hasRole('judge')) {
            $this->events = Event::where('id', $user->event_id)->get();
            // Assuming you have only one event assigned to a judge
            if ($this->events->isNotEmpty()) {
                $this->scoringType = $this->events->first()->type_of_scoring; // Get the scoring type
            }
        } else {
            $this->events = collect(); 
            $this->scoringType = null;
        }
    }

    public function AssignedCategories()
    {
        $user = Auth::user();

        if ($user && $user->hasRole('judge') && $user->event) {
            $this->categories = $user->event->categories;

            // Decode criteria JSON if it exists
            foreach ($this->categories as $category) {
                // Assuming 'criteria' is a JSON field in the Category model
                $category->criteria = json_decode($category->criteria, true);
            }
        } else {
            $this->categories = collect(); 
        }
    }

    public function goToCategoryDetails($categoryId)
    {
        // Redirect to the category details page with the selected category ID
        return redirect()->route('category.details', ['categoryId' => $categoryId]);
    }

    public function render()
    {
        return view('livewire.judge-portal', [
            'events' => $this->events,
            'categories' => $this->categories,
            'scoringType' => $this->scoringType,  // Pass scoring type to the view
        ]);
    }
}
