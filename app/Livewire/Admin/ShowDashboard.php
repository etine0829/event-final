<?php

namespace App\Livewire\Admin;
use App\Models\User;
use App\Models\Admin\Criteria;
use Livewire\Component;
use App\Models\Admin\Event;
use App\Models\Admin\Category;

class ShowDashboard extends Component
{
    public $judgeCount; // Property to store the judge count
    public $criteriaCount;
    public $eventCount;
    public $categoryCount;

    public function mount()
    {
        // Fetch the count of judges when the component is initialized
        $this->judgeCount = $this->fetchJudgeCount();
        $this->criteriaCount = $this->countCriteria();
        $this->eventCount = $this->countEvents();
        $this->categoryCount = $this->countCategories();
    }

    public function fetchJudgeCount()
    {
        // Query to count all users with the role 'judge'
        return User::whereHas('roles', function ($query) {
            $query->where('name', 'judge'); // Filter only users with the 'judge' role
        })->count();
    }
    public function countCategories()
    {
        // Query the categories table to count all rows
        return Category::count();
    }
    public function countCriteria()
    {
        // Query the Criteria table to count all rows
        return Criteria::count();
    }
    public function countEvents()
    {
        // Query the events table to count all rows
        return Event::count();
    }
    public function render()
    {
        return view('livewire.admin.show-dashboard', [
            'judgeCount' => $this->judgeCount, // Pass the count to the view
            'criteriaCount' => $this->criteriaCount,
            'eventCount' => $this->eventCount,
            'categoryCount' => $this->categoryCount,
        ]);
    }
}
