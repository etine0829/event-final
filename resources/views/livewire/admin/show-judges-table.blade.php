<div>
@php
    session(['selectedEvent' => $selectedEvent]);
@endphp

@if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('event_manager'))
    <div>
        @if (session('success'))
            <x-sweetalert type="success" :message="session('success')" />
        @endif

        @if (session('info'))
            <x-sweetalert type="info" :message="session('info')" />
        @endif

        @if (session('error'))
            <x-sweetalert type="error" :message="session('error')" />
        @endif

        @if (Auth::user()->hasRole('admin'))
            <div class="flex justify-between mb-4 sm:-mt-4">
                <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Admin / Manage Judges</div>
            </div>
        @else
            <div class="flex justify-between mb-4 sm:-mt-4">
                <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Event Manager / Manage Judges</div>
            </div>
        @endif
        
        <div class="flex flex-col md:flex-row items-start md:items-center md:justify-start">
        <div class="flex items-center w-full md:w-auto">
            <label for="event_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Event:</label>
            <select wire:model="selectedEvent" id="event_id" name="event_id" wire:change="updateCategory"
                    class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror md:w-auto"
                    required>
                <option value="0">Select Event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                @endforeach
            </select>
        </div>

            <div class="w-full flex justify-end mt-4 md:mt-0 md:ml-4">
                @if($selectedEvent)
                    <input wire:model.debounce.500ms="search" type="text" class="text-sm border text-black border-gray-300 rounded-md px-3 py-1.5 w-64" placeholder="Search..." autofocus>
                @endif
            </div>
        </div>
        
        @if($eventToShow)
            <div class="flex justify-between">
                <p class="text-black mt-2 text-sm mb-4">Selected Event: <text class="uppercase text-red-500">{{ $eventToShow->event_name }}</text></p>
                
                <div x-data="{ open: false }">
                    <button @click="open = true" class="mt-2 bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                        <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Judges
                    </button>

                    <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div @click.away="open = true" class="w-[30%] max-h-[90%] bg-white p-6 rounded-lg shadow-lg mx-auto overflow-y-auto">
                            <div class="flex justify-between items-center pb-3">
                                <p class="text-xl font-bold">Add Judges</p>
                                <button @click="open = false" class="text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                            </div>

                            @if (Auth::user()->hasRole('admin'))
                                <form wire:submit.prevent="assignUserToEvent" class="mb-4">
                            @else
                                <form wire:submit.prevent="assignUserToEvent" class="mb-4">
                            @endif    
                                <div class="mb-2">
                                    <label for="judge_id">Judge:</label>
                                    <select wire:model="selectedUser" id="judge_id" class="form-control" required>
                                        <option value="">Select Judge</option>
                                        @foreach($users as $judge)
                                            <option value="{{ $judge->id }}">{{ $judge->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="flex mb-4 mt-5 justify-center">
                                    <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">Save</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        @endif

        <hr class="border-gray-200 my-4">
        
        @if($search && $users->isEmpty())
            <p class="text-black mt-8 text-center">No judge found in <text class="text-red-500">{{ $eventToShow->event_name }}</text> for matching "{{ $search }}"</p>  
            <div class="flex justify-center mt-2">
                @if($search)
                    <p><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
                @endif
            </div>
        @elseif(!$search && $users->isEmpty())
            <p class="text-black mt-8 text-center uppercase">No data available in judge<text class="text-red-500">
                @if($eventToShow)
                    {{ $eventToShow->event_name}}
                @endif</text>
            </p>
        @else

            @if($eventToShow)

                @if($userToShow->isEmpty())
                    <!-- Display message if no judges are associated with the selected event -->
                    <tr>
                        <td colspan="3" class="text-center text-red-500 font-bold">No judges and staff assigned to this event.</td>
                    </tr>
                @else
                    <div class="overflow-x-auto">
                        <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                            <thead class="bg-gray-200 text-black">
                                <tr>
                                    <th class="border border-gray-400 px-3 py-2">
                                        <button wire:click="sortBy('name')" class="w-full h-full flex items-center justify-center">
                                            Name
                                            @if ($sortField == 'name')
                                                @if ($sortDirection == 'asc')
                                                    &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                                @else
                                                    &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                                @endif
                                            @endif
                                        </button>
                                    </th>

                                    <th class="border border-gray-400 px-3 py-2">
                                        <button wire:click="sortBy('email')" class="w-full h-full flex items-center justify-center">
                                            Email
                                            @if ($sortField == 'email')
                                                @if ($sortDirection == 'asc')
                                                    &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                                @else
                                                    &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                                @endif
                                            @endif
                                        </button>
                                    </th>

                                    <th class="border border-gray-400 px-3 py-2">
                                        <button wire:click="sortBy('role')" class="w-full h-full flex items-center justify-center">
                                            Role
                                            @if ($sortField == 'role')
                                                @if ($sortDirection == 'asc')
                                                    &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                                @else
                                                    &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                                @endif
                                            @endif
                                        </button>
                                    </th>

                                    <th class="border border-gray-400 px-3 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody >
                            
                                @foreach ($userToShow as $judge)
                                    <tr class="hover:bg-gray-100" wire:model="selectedCategory">
                                        <td class="text-black border border-gray-400">{{ $judge->name }}</td>
                                        <td class="text-black border border-gray-400">{{ $judge->email }}</td>
                                        <td class="text-black border border-gray-400">{{ $judge->role }}</td>
                                        <td class="text-black border border-gray-400 px-1 py-1">
                                            <div class="flex justify-center items-center space-x-2">
                                                @if($eventToShow && $judge)
                                                    <div x-data="{ open: false, 
                                                        id: {{ json_encode($judge->id) }},
                                                        name: {{ json_encode($judge->name) }},
                                                        email: {{ json_encode($judge->email) }},
                                                        password: {{ json_encode($judge->password) }},
                                                    }">
                                                        <!-- You can add the edit or action buttons here -->
                                                        
                                                    </div>
                                                    @if(Auth::user()->hasRole('admin'))
                                                        <form id="deleteSelected" action="{{ route('admin.judge.destroy', [':id', ':judge_id']) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $judge->id }}', '{{ $judge->judge_id }}', '{{ $judge->judge_name }}', '{{ $judge->score }}');">
                                                    @else
                                                        <form id="deleteSelected" action="{{ route('event_manager.judge.destroy', [':id', ':judge_id']) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $judge->id }}', '{{ $judge->judge_id }}', '{{ $judge->judge_name }}', '{{ $judge->score }}');">
                                                    @endif
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="bg-red-500 text-white text-sm px-3 py-2 rounded hover:bg-red-700">
                                                                <i class="fa-solid fa-trash fa-xs" style="color: #ffffff;"></i>
                                                            </button>
                                                        </form>
                                                @else
                                                    <p>No judges and staff assigned to this event.</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                        @if($eventToShow)
                            <tr>
                                <td colspan="2">
                                    <div class="flex justify-between">
                                        <div class="uppercase text-black mt-2 text-sm mb-4">
                                            @if($search)
                                                {{ $users->total() }} Search results 
                                            @endif                                    
                                        </div>
                                    </div> 
                                </td>
                                <td>
                                    {{ $users->links() }}
                                </td>
                                <div class="flex justify-center mt-2">
                                    @if($search)
                                        <p><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
                                    @endif
                                </div>
                            </tr>
                        @endif
                    </div>
                @else
                    <p class="text-black mt-10  text-center">Select table to show data</p>
                @endif
        @endif
    </div>
@endif
</div>
    <script>
        function confirmDeleteAll(event) {
            event.preventDefault(); // Prevent form submission initially

            Swal.fire({
                title: 'Select event to Delete All Records',
                html: `
                    <select id="event_id_select" class="cursor-pointer hover:border-red-500 swal2-select">
                        <option value="">Select event</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}">{{ $event->name }} - {{ $event->venue }} - {{ $event->type_of_scoring }}</option>
                        @endforeach
                    </select>
                `,
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete all!',
                preConfirm: () => {
                    const eventId = Swal.getPopup().querySelector('#event_id_select').value;
                    if (!eventId) {
                        Swal.showValidationMessage(`Please select a event`);
                    }
                    return { eventId: eventId };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const eventId = result.value.eventId;
                    document.getElementById('event_id_to_delete').value = eventId;
                    document.getElementById('deleteAll').submit();
                }
            });
        }

        function ConfirmDeleteSelected(event, rowId, judgeId, judgename, score) {
            event.preventDefault(); // Prevent form submission initially

            Swal.fire({
                title: `Are you sure you want to delete the judge?`,
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteSelected');
                    // Replace the placeholders with the actual rowId and judgeId
                    const actionUrl = form.action.replace(':id', rowId).replace(':judge_id', judgeId);
                    form.action = actionUrl;
                    form.submit();
                }
            });

            return false; 
        }

    </script>
