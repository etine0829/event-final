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
        } else {
            $this->events = collect(); 
        }
    }

    public function AssignedCategories()
    {
        $user = Auth::user();

        if ($user && $user->hasRole('judge') && $user->event) {
            $this->categories = $user->event->categories;
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
        ]);
    }
}

