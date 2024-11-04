<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Admin\Event;
use App\Models\Admin\Category;
use App\Models\Admin\Criteria;
use App\Models\Admin\Participant;

class JudgePortal extends Component
{
    public $events;
    public $categories;
    public $participants;
    public $selectedCategory = null;

    public function mount()
    {
        // Fetch all categories (assuming a specific event for simplicity)
        $this->categories = Category::with(['criteria'])->get();
    }

    public function selectCategory($categoryId)
    {
        // Set the selected category based on the clicked category ID
        $this->selectedCategory = Category::with(['criteria'])->find($categoryId);
    }

    public function render()
    {
        return view('livewire.judge-portal', [
            'categories' => $this->categories,
            'selectedCategory' => $this->selectedCategory,
        ]);
    }
}
