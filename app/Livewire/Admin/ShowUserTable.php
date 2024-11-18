<?php

namespace App\Livewire\Admin;

use \App\Models\User;
use \App\Models\Admin\Event; 
use Livewire\Component;
use Livewire\WithPagination;

class ShowUserTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'desc';

    public function updatingSearch()
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
        $users = User::where('role', '!=', 'admin') // Exclude admins
            ->where(function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                    ->orWhere('name', 'like', '%' . $this->search . '%')
                    ->orWhere('picture', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('role', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(20);

            $events = Event::all();

        return view('livewire.admin.show-user-table', [
            'users' => $users,
            'events' => $events,
        ]);
    }
}

