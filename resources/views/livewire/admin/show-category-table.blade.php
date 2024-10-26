@php
    session(['selectedEvent' => $selectedEvent]);
@endphp
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

        <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Admin / Manage Category</div>
        </div>
        <div class="flex flex-col md:flex-row items-start md:items-center md:justify-start">
            <!-- Dropdown and Delete Button -->
            <div class="flex items-center w-full md:w-auto">
                <label for="event_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Event:</label>
                <select wire:model="selectedEvent" id="event_id" name="event_id" wire:change="updateCategory"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror md:w-auto"
                        required>
                    <option value="">Event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                    @endforeach
                </select>
                
                @if($eventToShow)
                    <!-- <form id="deleteAll" action="{{ route('admin.category.deleteAll') }}" method="POST" onsubmit="return confirmDeleteAll(event);" class="flex ml-4">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="event_id" id="event_id_to_delete">
                    <button type="submit" class="text-xs lg:text-sm bg-red-500 text-white px-3 py-2 rounded-md hover:bg-red-700">
                        <i class="fa-solid fa-trash fa-sm"></i>
                    </button>
                </form> -->
                @else
                    
                @endif
            </div>
            <!-- Search Input -->
            <div class="w-full flex justify-end mt-4 md:mt-0 md:ml-4">
                @if(empty($selectedEvent)) 
                    
                @else
                    <input wire:model.live="search" type="text" class="text-sm border text-black border-gray-300 rounded-md px-3 py-1.5 w-64" placeholder="Search..." autofocus>
                @endif
            </div>
        </div>
        <hr class="border-gray-200 my-4">
        
        @if($eventToShow)
       
        <div class="flex justify-between">
            <p class="text-black mt-2 text-sm mb-4">Selected Event: <text class="uppercase text-red-500">{{ $eventToShow->event_name }}</text></p>
            <div x-data="{ open: false }">
                <button @click="open = true" class="bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                    <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Category
                </button>
                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.away="open = true" class="w-[30%] max-h-[90%]  bg-white p-6 rounded-lg shadow-lg  mx-auto overflow-y-auto">
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-xl font-bold">Add Category</p>
                            <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                        </div>
                        <div class="mb-4">
                            <form action="{{ route('admin.category.store') }}" method="POST" class="">
                            <x-caps-lock-detector />
                                @csrf
                                    <div class="mb-2">
                                        <label for="event_id" class="block text-gray-700 text-md font-bold mb-2">Event: </label>
                                        <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                                <option value="{{ $eventToShow->id }}">{{ $eventToShow->event_name }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="category_name" class="block text-gray-700 text-md font-bold mb-2">Category Name</label>
                                        <input type="text" name="category_name" id="category_name" value="{{ old('category_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_name') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('category_name')" class="mt-2" />
                                    </div>                                  

                                    @if($type_of_scoring === 'points')
                                        <div class="mb-2" id="scoreContainer">
                                            <label for="score" class="block text-gray-700 text-md font-bold mb-2">Score</label>
                                            <input type="number" name="score" id="score" value="{{ old('score') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('score') is-invalid @enderror" required>
                                            <x-input-error :messages="$errors->get('score')" class="mt-2" />
                                        </div>
                                    @endif

                                <div class="flex mb-4 mt-10 justify-center">
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
        @else
            
        @endif
        @if($search && $categories->isEmpty())
        <p class="text-black mt-8 text-center">No category found in <text class="text-red-500">{{ $eventToShow->event_name }}</text> for matching "{{ $search }}"</p>  
        <div class="flex justify-center mt-2">
            @if($search)
                <p><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
            @endif
        </div>
        @elseif(!$search && $categories->isEmpty())
            
            <p class="text-black mt-8 text-center uppercase">No data available in event<text class="text-red-500">
                @if($eventToShow)
                {{ $eventToShow->event_name}}
            @endif</text></p>
        @else

            @if($eventToShow)
                
                <div class="overflow-x-auto">
                    <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                        <thead class="bg-gray-200 text-black">
                            <tr>
                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('id')" class="w-full h-full flex items-center justify-center">
                                        Count #
                                        @if ($sortField == 'id')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                
                                
                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('event_name')" class="w-full h-full flex items-center justify-center">
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
                                    <button wire:click="sortBy('category_name')" class="w-full h-full flex items-center justify-center">
                                        Category Name
                                        @if ($sortField == 'category_name')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                              
                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('category_name')" class="w-full h-full flex items-center justify-center">
                                        Score
                                        @if ($sortField == 'score')
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
                            @foreach ($categories as $category)
                                <tr class="hover:bg-gray-100" wire:model="selectedCategory">
                                    <td class="text-black border border-gray-400  ">{{ $category->id }}</td>      
                                    <td class="text-black border border-gray-400">{{ $category->event->event_name}}</td>               
                                    <td class="text-black border border-gray-400">{{ $category->category_name}}</td>
                                    <td class="text-black border border-gray-400">{{ $category->score}}</td>
                                    <!-- <td class="text-black border border-gray-400">{{ $category->event->name}}</td>
                                    <td class="text-black border border-gray-400">{{ ucfirst($category->dept_identifier) }}</td> -->
                                    <td class="text-black border border-gray-400 px-1 py-1">
                                        <div class="flex justify-center items-center space-x-2">
                                            @if($eventToShow && $category)
                                            <div x-data="{ open: false, 
                                                id: {{ json_encode($category->id) }},
                                                    event: {{ json_encode($category->event_id) }},
                                                    category_name: {{ json_encode($category->category_name) }},
                                                    score: {{ json_encode($category->score) }},
                                                    }">
                                                <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                                                    <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                                </a>
                                                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                    <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg  mx-auto">
                                                        <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                            <p class="text-xl font-bold">Edit category</p>
                                                            <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                                        </div>
                                                        <div class="mb-4">
                                                            <form id="updateCategoryForm" action="{{ route('admin.category.update', $category->id )}}" method="POST" class="">
                                                                <x-caps-lock-detector />
                                                                @csrf
                                                                @method('PUT')
                                                                
                                                                <div class="mb-2">
                                                                        <label for="event_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Event: </label>
                                                                        <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                                                                <option value="{{ $eventToShow->id }}">{{ $eventToShow->event_name }}</option>
                                                                        </select>
                                                                        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                                                    </div>

                                                                    <div class="mb-4">
                                                                        <label for="category_name" class="block text-gray-700 text-md font-bold mb-2 text-left">Category Name</label>
                                                                        <input type="text" name="category_name" id="category_name" x-model="category_name" value="{{ $category->category_name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_name') is-invalid @enderror" required>
                                                                        <x-input-error :messages="$errors->get('category_name')" class="mt-2" />
                                                                    </div>

                                                                    @if($type_of_scoring === 'points')
                                                                        <div class="mb-4" id="scoreContainer">
                                                                            <label for="score" class="block text-gray-700 text-md font-bold mb-2 text-left">Score</label>
                                                                            <input type="text" name="score" id="score" x-model="score" value="{{ $category->score }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('score') is-invalid @enderror" required>
                                                                            <x-input-error :messages="$errors->get('score')" class="mt-2" />
                                                                        </div>
                                                                    @endif

                                                                    <div class="flex mb-4 mt-10 justify-center">
                                                                    <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                                                        Save Changes
                                                                    </button>
                                                                    </div>

                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <form id="deleteSelected" action="{{ route('admin.category.destroy', $category->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="bg-red-500 text-white text-sm px-3 py-2 rounded hover:bg-red-700">
                                                    <i class="fa-solid fa-trash fa-xs" style="color: #ffffff;"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @if($eventToShow)
                        <tr>
                            <td colspan="2">
                                <div class="flex justify-between">
                                    <div class="uppercase text-black mt-2 text-sm mb-4">
                                        @if($search)
                                            {{ $categories->total() }} Search results 
                                        @endif                                    
                                    </div>
                                    <div class="justify-end">
                                        <p class="text-black mt-2 text-sm mb-4 uppercase">Total # of Category: <text class="ml-2">{{ $categoryCounts[$eventToShow->id]->category_count ?? 0 }}</text></p>
                                        
                                    </div>
                                </div> 
                            </td>
                            <td>
                                {{ $categories->links() }}
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

   <!-- <script>
            document.addEventListener('DOMContentLoaded', function () {
        const eventSelect = document.getElementById('event_id');
        const scoreContainer = document.getElementById('scoreContainer');

        // Hide or show the score input based on scoring type
        function toggleScoreVisibility() {
            const selectedEvent = eventSelect.options[eventSelect.selectedIndex];
            const scoringType = selectedEvent.getAttribute('data-scoring');

            if (scoringType === 'ranking') {
                scoreContainer.style.display = 'none'; // Hide score input
            } else {
                scoreContainer.style.display = 'block'; // Show score input
            }
        }

        // Initial check
        toggleScoreVisibility();

        // Add event listener if the event selection changes
        eventSelect.addEventListener('change', toggleScoreVisibility);
    });
    </script>  -->

    <script>

    function searchcategories(event) {
            let searchTerm = event.target.value.toLowerCase();
            if (searchTerm === '') {
                this.categoriesToShow = @json($categoryToShow->toArray());
            } else {
                this.categoriesToShow = this.categoriesToShow.filter(category =>
                    category.category_name.toLowerCase().includes(searchTerm) ||
                    category.name.toLowerCase().includes(searchTerm) ||
                    category.event.event_name.toLowerCase().includes(searchTerm)
                );
            }
        }

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

        function ConfirmDeleteSelected(event, rowId, categoryId, categoryname, score) {
            event.preventDefault(); // Prevent form submission initially

            Swal.fire({
                title: `Are you sure you want to delete the category ${categoryId} - ${categoryname} ${score} ?`,
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteSelected');
                    // Replace the placeholders with the actual rowId and categoryId
                    const actionUrl = form.action.replace(':id', rowId).replace(':category_id', categoryId);
                    form.action = actionUrl;
                    form.submit();
                }
            });

            return false; 
        }

    </script>



@endif