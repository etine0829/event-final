<?php

namespace App\Livewire\Admin;

use \App\Models\Admin\Event; 
use \App\Models\Admin\Category; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class ShowCategoryTable extends Component
{
    
    use WithPagination;

    public $search = '';
    public $sortDirection = 'asc';
    public $selectedEvent = null;
    public $selectedCategory = null;
    public $sortField = 'id';
    public $categoryToShow;
    public $eventToShow = null;

    protected $listeners = ['updateCategory'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->categoryToShow = collect([]); // Initialize as an empty collection
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
        $query = Category::with('event');

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply selected school filter
        if ($this->selectedEvent) {
            $query->where('event_id', $this->selectedEvent);
            $this->eventToShow = Event::findOrFail($this->selectedEvent);
        } else {
            $this->eventToShow = null; // Reset schoolToShow if no school is selected
        }

        $categories = $query->orderBy($this->sortField, $this->sortDirection)
                             ->paginate(25);

        $events = Event::all();


         $categoryCounts = Category::select('event_id', \DB::raw('count(*) as category_count'))
                                  ->groupBy('event_id')
                                  ->get()
                                  ->keyBy('event_id');

        return view('livewire.admin.show-category-table', [
            'categories' => $categories,
            'events' => $events,
            'categoryCounts' => $categoryCounts,
        ]);
    }

    public function updateCategory()
    {
        // Update categoryToShow based on selected school
        if ($this->selectedEvent) {
            $this->categoryToShow = Category::where('event_id', $this->selectedEvent)
                ->get(); // Ensure this returns a collection
        } else {
            $this->categoryToShow = collect(); // Reset to empty collection if no school is selected
        }

        
    }

    protected function applySearchFilters($query)
{
    return $query->where(function (Builder $query) {
        $query->where('id', 'like', '%' . $this->search . '%')        
            ->orWhere('category_name', 'like', '%' . $this->search . '%')
            ->orWhere('score', 'like', '%' . $this->search . '%')
            ->orWhereHas('event', function (Builder $query) {
                $query->where('event_name', 'like', '%' . $this->search . '%')
                    ->orWhere('venue', 'like', '%' . $this->search . '%')
                    ->orWhere('type_of_scoring', 'like', '%' . $this->search . '%');
            });
    });
}

    
}