<?php

namespace App\Livewire\Admin;


use \App\Models\Admin\Criteria; 
use \App\Models\Admin\Event; 
use \App\Models\Admin\Category; 
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;


class ShowCriteriaTable extends Component
{
    use WithPagination; 
    public $search = '';
    public $sortField = 'criteria_id';
    public $sortDirection = 'asc';
    public $selectedEvent = null;
    public $selectedCategory1 = null;
    public $categoriesToShow;
    public $eventToShow;
    public $categoryToShow;

    // protected $listeners = ['updateEmployees', 'updateEmployeesByDepartment'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->selectedEvent = session('selectedEvent', null);
        $this->selectedCategory1 = session('selectedCategory1', null);
        $this->categoriesToShow = collect([]); // Initialize as an empty collection
        $this->eventToShow = collect([]); // Initialize as an empty collection
        $this->categoryToShow = collect([]);
    }

    public function updatingSelectedEvent()
    {
        $this->resetPage();
        // $this->updateEmployees();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
        // $this->updateEmployeesByDepartment();
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
        $query = Criteria::with('category')->with('event');

        // Apply search filters
        $query = $this->applySearchFilters($query);

        // Apply selected event filter
        if ($this->selectedEvent) {
            $query->where('event_id', $this->selectedEvent);
            $this->eventToShow = Event::find($this->selectedEvent);
        } else {
            $this->eventToShow = null; // Reset eventToShow if no school is selected
        }

        // Apply selected category filter
        if ($this->selectedCategory1) {
            $query->where('category_id', $this->selectedCategory1);
            $this->categoryToShow = Category::find($this->selectedCategory1);
        } else {
            $this->categoryToShow = null; // Reset departmentToShow if no department is selected
        }

        $criterion = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $events = Event::all();
        // $departments = Department::where('school_id', $this->selectedEvent)->get();
        $categories = Category::where('event_id', $this->selectedEvent)
        ->get(['category_name']);



        // Count employees by department
        $categoryCounts = Criteria::select('category_id', \DB::raw('count(*) as criteria_count'))
            ->groupBy('category_id')
            ->get()
            ->keyBy('category_id');

        return view('livewire.admin.show-criteria-table', [
            'criterion' => $criterion,
            'events' => $events,
            'categories' => $categories,
        
            'categoryCounts' => $categoryCounts,
        ]);
    }

    public function updateEmployees()
    {
        // Update categoryToShow based on selected school
        if ($this->selectedEvent) {
            $this->categoryToShow = Category::where('event_id', $this->selectedEvent)->get();
        } else {
            $this->categoryToShow = collect(); // Reset to empty collection if no school is selected
        }

        // Ensure departmentToShow is reset if the selected school changes
        $this->selectedCategory1 = null;
        $this->CategoryToShow = null;
    }

public function updateEmployeesByDepartment()
{
    if ($this->selectedCategory1 && $this->selectedEvent) {
        $this->categoryToShow = Category::where('category_id', $this->selectedCategory1)
                                            ->where('event_id', $this->selectedEvent)
                                            ->first();
    } else {
        $this->categoryToShow = null;
    }
}



    protected function applySearchFilters($query)
    {
        return $query->where(function (Builder $query) {
            $query->where('criteria_id', 'like', '%' . $this->search . '%')
                ->orWhere('criteria_name', 'like', '%' . $this->search . '%')
                ->orWhere('criteria_score', 'like', '%' . $this->search . '%')
                ->orWhereHas('category', function (Builder $query) {
                $query->where('category_name', 'like', '%' . $this->search . '%')
                    ->orWhere('score', 'like', '%' . $this->search . '%');
            });
        });
    }
    
}