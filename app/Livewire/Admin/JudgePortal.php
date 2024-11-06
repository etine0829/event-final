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
    public $selectedCategory;
    public $participants = [];
    public $criteria = [];
    

    public function mount()
    {
        $this->categories = Category::with(['criteria'])->get();
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
            $this->categories = $user->event->categories()->with(['criteria'])->get();
        } else {
            $this->categories = collect(); 
        }
    }

    public function loadCategoryDetails($categoryId)
    {
        $this->selectedCategory = Category::find($categoryId);

        if ($this->selectedCategory) {
           
            $this->participants = $this->selectedCategory->participants;
            $this->criteria = $this->selectedCategory->criteria;
        }
    }

    public function selectCategory($categoryId)
    {
        $this->selectedCategory = Category::with(['criteria'])->find($categoryId);
    }

    

    public function render()
    {
        return view('livewire.judge-portal', [
            'judges' => $this->judges,
            'events' => $this->events,
            'categories' => $this->categories,
            'selectedCategory' => $this->selectedCategory,
            'participants' => $this->participants,
            'criteria' => $this->criteria,
        ]);
    }
}
