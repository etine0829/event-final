@if (Auth::user()->hasRole('admin'))
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
        <div class="flex justify-between mb-4 sm:-mt-4 ">
            <div class="font-bold text-md tracking-wide text-black  mt-2 uppercase">Admin / Manage Event</div>
            
        
        </div>
        
        <hr class="border-gray-200 my-4">

            <div x-data="{ open: false }">
                <div class="flex justify-end">
                <button @click="open = true" class="bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 mb-4 flex justify-end >
                    <i class="fa-solid fa-plus fa-xs " style="color: #ffffff;"></i> Add Event</button>
                </div>
                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.away="open = true" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-xl font-bold ">Add Event</p>
                            <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                        </div>
                        <div class="mb-4">
                            <form action="{{ route('admin.event.store') }}" method="POST" class="">
                                @csrf
                                <div class="mb-4  ">
                                    <label for="event_name" class="block text-gray-700 text-md font-bold mb-2">Event Name</label>
                                    <!-- <input type="text" name="event_name" id="event_name" value="{{ old('event_name') }}"  class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_name') is-invalid @enderror" required autofocus> -->
                                    <input type="text" name="event_name" id="event_name" value="{{ old('event_name') }}"  class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('event_name') is-invalid @enderror" required autofocus>
                                    <x-input-error :messages="$errors->get('event_name')" class="mt-2" />
                                </div>
                               
                                <div class="mb-4">
                                    <label for="venue" class="block text-gray-700 text-md font-bold mb-2">Venue</label>
                                    <!-- <input type="text" name="event_name" id="event_name" value="{{ old('event_name') }}"  class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_name') is-invalid @enderror" required autofocus> -->
                                    <input type="text" name="venue" id="venue" value="{{ old('venue') }}"  class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('venue') is-invalid @enderror" required autofocus>
                                    <x-input-error :messages="$errors->get('venue')" class="mt-2" />
                                </div>

                                <div class="mb-2">
                                        <label for="type_of_scoring" class="block text-gray-700 text-md font-bold mb-2">Scoring Type: </label>
                                        <select id="type_of_scoring" name="type_of_scoring" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('type_of_scoring') is-invalid @enderror" required>
                                                <option value="">Select Option</option>
                                                <option value="points">By Points</option>
                                                <option value="ranking">By Ranking </option>
                                        </select>
                                        <x-input-error :messages="$errors->get('type_of_scoring')" class="mt-2" />
                                </div> 

                                <div class="flex mb-4 mt-5 justify-center">
                                    <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
       
        @if($search && $events->isEmpty())
            <p class="text-black mt-8 text-center">No events found for matching "{{ $search }}"</p>
        @elseif(!$search && $events->isEmpty())
            <p class="text-black mt-8 text-center">No data available in table</p>
        @else
            <table class="table-auto border-collapse border border-gray-400 w-full text-sm text-center mb-4">
                <thead class="bg-gray-200 text-black">
                    <tr>    
                      
                        <th class="border border-gray-400 px-3 py-2">
                            <button wire:click="sortBy('event_name')" class=" w-full h-full flex items-center justify-center">
                                Event Name
                                @if ($sortField == 'event_name')
                                    @if ($sortDirection == 'asc')
                                        &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                    @else
                                        &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                    @endif
                                @endif
                            </button>
                        </th>
                        <th class="border border-gray-400 px-3 py-2">
                            <button wire:click="sortBy('venue')" class="w-full h-full flex items-center justify-center">
                                Venue
                                @if ($sortField == 'venue')
                                    @if ($sortDirection == 'asc')
                                        &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                    @else
                                        &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                    @endif
                                @endif
                            </button>
                        </th>
                        <th class="border border-gray-400 px-3 py-2">
                            <button wire:click="sortBy('type_of_scoring')" class="w-full h-full flex items-center justify-center">
                                Type of Scoring
                                @if ($sortField == 'type_of_scoring')
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
                <tbody>
                    @foreach ($events as $event)
                        <tr>
                            <td class="text-black border border-gray-400 px-3 py-2 ">{{ $event->event_name }}</td>
                            <td class="text-black border border-gray-400 px-3 py-2">{{ $event->venue}}</td>
                            <td class="text-black border border-gray-400 px-3 py-1">
                                @if($event->type_of_scoring == 'points')
                                    By Points
                                @else
                                    By Ranking
                                @endif
                            </td>
                            <td class="text-center text-black border border-gray-400 px-2 py-.7">
                                <div class="flex justify-center items-center space-x-2">
                                    <div x-data="{ open: false, 
                                            Id: '{{ $event->id }}', 
                                            event_name: '{{ $event->event_name }}',
                                            venue: '{{ $event->venue }}',
                                            scoring_type: '{{ $event->type_of_scoring }}' }">
                                        <!-- edit button   -->
                                        <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                                            <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                        </a>
                                        <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                            <div @click.away="open = true" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                                                <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                    <p class="text-xl font-bold">Edit Event</p>
                                                    <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                                </div>
                                                <div class="mb-4">
                                                    <form id="updateEventForm" action="{{ route('admin.event.update', $event->id )}}" method="POST" class="">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="mb-4">
                                                            <label for="Id" class="block text-gray-700 text-md font-bold mb-2 text-left">Event ID:</label>
                                                            <input type="text" id="Id" x-model="Id" readonly class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('Id') is-invalid @enderror">
                                                            <x-input-error :messages="$errors->get('Id')" class="mt-2" />
                                                        </div>
                                                        <div class="mb-4">
                                                            <label for="event_name" class="block text-gray-700 text-md font-bold mb-2 text-left">Event Name</label>
                                                            <input type="text" name="event_name" id="event_name" x-model="event_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('event_name') is-invalid @enderror" required autofocus>
                                                            <x-input-error :messages="$errors->get('event_name')" class="mt-2" />
                                                        </div>
                                                        <div class="mb-4">
                                                            <label for="venue" class="block text-gray-700 text-md font-bold mb-2 text-left">Event Venue</label>
                                                            <input type="text" name="venue" id="venue" x-model="venue" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('venue') is-invalid @enderror" required >
                                                            <x-input-error :messages="$errors->get('venue')" class="mt-2" />
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="type_of_scoring" class="block text-gray-700 text-md font-bold mb-2 text-left">Scoring Type: </label>
                                                            <select id="type_of_scoring" name="type_of_scoring" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('scoring_type') is-invalid @enderror" required>
                                                            @if($event->type_of_scoring === 'points')  
                                                                    <option value="{{ $event->type_of_scoring }}">
                                                                        @if($event->type_of_scoring == 'points')
                                                                            By Points
                                                                        @endif
                                                                    </option>
                                                                    <option value="ranking">By Ranking</option>
                                                                @else
                                                                    <option value="{{ $event->type_of_scoring }}">By Ranking</option>
                                                                    <option value="points">By Points</option>
                                                                @endif
                                                            </select>
                                                            <x-input-error :messages="$errors->get('type_of_scoring')" class="mt-2" />
                                                        </div>
                                                        <div class="flex mb-4 mt-5 justify-center">
                                                            <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md text-sm">
                                                                Save
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <form id="deleteSelected" action="{{ route('admin.event.destroy', $event->id ) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $event->id }}', '{{ $event->event_name }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 text-white text-sm px-3 py-1.5 rounded hover:bg-red-700">
                                            <i class="fa-solid fa-trash fa-xs" style="color: #ffffff;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $events->links() }}
        @endif
    </div>

    <script>

        
    
    // Function to confirm deletion of a selected event with visually appealing design
    function ConfirmDeleteSelected(event, eventID, eventName) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete the event "${eventName}"?`,
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ff6b6b', // Soft red for confirmation
            cancelButtonColor: '#6c757d', // Gray for cancellation
            confirmButtonText: '<i class="fas fa-trash"></i> Yes, delete it!',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel',

            color: '#4a4a4a', // Dark   er text color
            customClass: {
                popup: 'animate__animated animate__fadeInUp', // Fade in animation for the popup
                confirmButton: 'btn btn-danger rounded-full px-6 py-3 text-lg', // Large rounded confirmation button
                cancelButton: 'btn btn-secondary rounded-full px-6 py-3 text-lg' // Large rounded cancel button
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteSelected');
                // Dynamically set form action with the event ID
                form.action = `{{ route('admin.event.destroy', ':Id') }}`.replace(':Id', eventID);
                form.submit(); // Submit the form if confirmed
            }
        });

        return false; // Prevent the default form submission until confirmed
    }

    </script>


@elseif (Auth::user()->hasRole('admin_staff')) 


        @if (session('success'))
            <x-sweetalert type="success" :message="session('success')" />
        @endif

        @if (session('info'))
            <x-sweetalert type="info" :message="session('info')" />
        @endif

        @if (session('error'))
            <x-sweetalert type="error" :message="session('error')" />
        @endif
        <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-tight text-black  mt-2 uppercase">Staff / Manage School Year</div>
            <div x-data="{ open: false }">
                <button @click="open = true" class="bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                    <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add School
                </button>
                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.away="open = true" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-xl font-bold">Add School</p>
                            <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                        </div>
                        <div class="mb-4">
                            <form action="" method="POST" class="">
                                @csrf
                                <div class="mb-4">
                                    <label for="abbreviation" class="block text-gray-700 text-md font-bold mb-2">Abbreviation:</label>
                                    <input type="text" name="abbreviation" id="abbreviation" value="{{ old('abbreviation') }}"  class="shadow appearance-none  rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_name') is-invalid @enderror" required autofocus>
                                    <x-input-error :messages="$errors->get('abbreviation')" class="mt-2" />
                                </div>
                                <div class="mb-4">
                                    <label for="school_name" class="block text-gray-700 text-md font-bold mb-2">School Name</label>
                                    <input type="text" name="school_name" id="school_name" value="{{ old('school_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('department_description') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('school_name')" class="mt-2" />
                                </div>
                                <div class="flex mb-4 mt-5 justify-center">
                                    <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-gray-200 my-4">
        <div class="flex items-center mb-4 justify-end">
            <div class="flex w-full sm:w-auto mt-2 sm:mt-0 sm:ml-2">
                <input wire:model.live="search" type="text" class="border text-black border-gray-300 rounded-md p-2 w-full" placeholder="Search..." autofocus>
            </div>
        </div>  


    <script>

        function confirmDeleteAll(event) {
            event.preventDefault(); // Prevent form submission initially

            Swal.fire({
                title: 'Are you sure to delete all records?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form programmatically
                    document.getElementById('deleteAll').submit();
                }
            });
        }

        function ConfirmDeleteSelected(event, schoolID, schoolName) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete the school ${schoolName}?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteSelected');
                // Replace the placeholder with the actual school ID
                form.action = `{{ route('staff.school.destroy', ':schoolId') }}`.replace(':schoolId', schoolID);
                form.submit();
            }
        });

        return false; 
    }

    </script>

   
@endif