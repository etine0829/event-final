<div class="mb-4">
        @php
            session(['selectedEvent' => $selectedEvent]);
            session(['selectedCategory1' => $selectedCategory1]);
        @endphp
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
    @if (Auth::user()->hasRole('admin'))
        <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Admin / Manage Criteria</div>
    @else
        <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Event Manager / Manage Criteria</div>
    @endif
    </div>

        <div class="flex flex-column overflow-x-auto -mb-5">
            <div class="col-span-3 p-4">
                <label for="event_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Select Event:</label>
                <select wire:model="selectedEvent" id="event_id" name="event_id" wire:change="updateEmployees"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror md:w-auto"
                        required>
                    <option value="">Select Event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                    @endforeach
                </select>
                @if($eventToShow)
                    <p class="text-black mt-2 text-sm mb-1 ">Selected Event: <span class="text-red-500 ml-2">{{ $eventToShow->event_name }}</span></p>
                    <!-- <p class="text-black  text-sm ml-4">Selected event: <span class="text-red-500 ml-2">{{ $eventToShow->event_name }}</span></p> -->
                @endif
            </div>

        <div class="col-span-1 p-4">
            @if(!empty($selectedEvent))
                <label for="category_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Category:</label>
                <select wire:model="selectedCategory1" id="category_id" name="category_id"
                        wire:change="updateEmployeesByDepartment"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('category_id') is-invalid @enderror md:w-auto"
                        required>
                        @if($categories->isEmpty())
                            <option value="0">No Category</option>
                        @else
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>    
                            @endforeach
                        @endif
                    </select>
                    @if($categoryToShow)
                        <p class="text-black mt-2 text-sm mb-1 ">Selected Category: <span class="text-red-500 ml-2">{{ $categoryToShow->category_name }}</span></p>
                    @endif
                @endif
            </div>

    </div>
    <hr class="border-gray-200 my-4">
    @if(!$eventToShow)
            <p class="text-black text-sm mt-11 mb-4 uppercase text-center">No selected Event</p>
        @endif
        @if(!empty($selectedEvent))
            @if(!$categoryToShow)
                <p class="text-black text-sm mt-11 mb-4 uppercase text-center">No selected Category</p>
            @endif
        @endif

    @if($categoryToShow)
        @if($search && $criterion->isEmpty())
        <p class="text-black mt-8 text-center">No criteria found in <text class="text-red-500">{{ $categoryToShow->category_name }}</text> for matching "{{ $search }}"</p>
        <p class="text-center mt-5"><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
        @elseif(!$search && $criterion->isEmpty())
            <p class="text-black mt-8 text-center uppercase">No data available in <text class="text-red-500">{{$categoryToShow->category_name}} - {{ $categoryToShow->score }} category.</text></p>
            <div class="flex justify-center items-center mt-5">
                <div x-data="{ open: false }">
                    <button @click="open = true" class="-mt-1 mb-2 bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                        <!-- <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> {{$categoryToShow->category_id}} - {{$categoryToShow->score}} -->
                        <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Criteria in {{$categoryToShow->category_name}} - {{ $categoryToShow->score }} category
                    </button>
                    <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                        <div  class="w-[35%] bg-white p-6 rounded-lg shadow-lg mx-auto max-h-[90vh] overflow-y-auto">
                            <div class="flex justify-between items-center pb-3">
                                <p class="text-xl font-bold">Add Criteria</p>
                                <button @click="open = false" class="text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                            </div>
                            <div class="mb-4">
                            @if (Auth::user()->hasRole('admin'))
                                <form action="{{ route('admin.criteria.store') }}" method="POST" class="" enctype="multipart/form-data">
                            @else
                                <form action="{{ route('event_manager.criteria.store') }}" method="POST" class="" enctype="multipart/form-data">
                            @endif
                                    <x-caps-lock-detector />
                                    @csrf

                                    <div class="mb-2">
                                        <label for="event_id" class="block text-gray-700 text-md font-bold mb-2">Event:</label>
                                        <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                            <!-- <option value="{{ $categoryToShow->event->id }}">{{ $categoryToShow->event->id }} | {{ $categoryToShow->event->event_name }}</option> -->
                                                <option value="{{ $categoryToShow->event->id }}">{{ $categoryToShow->event->event_name }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="category_id" class="block text-gray-700 text-md font-bold mb-2">Category:</label>
                                        <select id="category_id" name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('category_id') is-invalid @enderror" required>
                                            <!-- <option value="{{ $categoryToShow->id }}">{{ $categoryToShow->category_id }} | {{ $categoryToShow->score }}</option> -->
                                            <option value="{{ $categoryToShow->id }}">{{ $categoryToShow->category_name }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('id')" class="mt-2" />

                                    <div class="mb-2">
                                        <label for="criteria_name" class="block text-gray-700 text-md font-bold mb-2">Criteria Name</label>
                                        <input type="text" name="criteria_name" id="criteria_name" value="{{ old('criteria_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('criteria_name') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('criteria_name')" class="mt-2" />
                                    </div>

                                    @if($type_of_scoring === 'points')
                                        <div class="mb-2">
                                            <label for="criteria_score" class="block text-gray-700 text-md font-bold mb-2">Score</label>
                                            <input type="text" name="criteria_score" id="criteria_score" value="{{ old('criteria_score') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('criteria_score') is-invalid @enderror" required>
                                            <x-input-error :messages="$errors->get('criteria_score')" class="mt-2" />
                                        </div>
                                    @endif
                                    
                                    </div>
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
            <div class="flex justify-between">
                <div class="">
                    <!-- delete area -->
                </div>
                <div class="flex justify-center items-center">
                    <div x-data="{ open: false }">
                        <button @click="open = true" class="-mt-1 mb-2 bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                            <!-- <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> {{$categoryToShow->category_id}} - {{$categoryToShow->score}} -->
                            <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Criteria
                        </button>
                        <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                            <div  class="w-[35%] bg-white p-6 rounded-lg shadow-lg mx-auto max-h-[90vh] overflow-y-auto">
                                <div class="flex justify-between items-center pb-3">
                                    <p class="text-xl font-bold">Add Criteria</p>
                                    <button @click="open = false" class="text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                                </div>
                                <div class="mb-4">
                                @if (Auth::user()->hasRole('admin'))
                                    <form action="{{ route('admin.criteria.store') }}" method="POST" class="" enctype="multipart/form-data">
                                @else
                                    <form action="{{ route('event_manager.criteria.store') }}" method="POST" class="" enctype="multipart/form-data">
                                @endif
                                        
                                        <x-caps-lock-detector />
                                        @csrf 

                                        <div class="mb-2">
                                            <label for="event_id" class="block text-gray-700 text-md font-bold mb-2">Event</label>
                                            <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                                <!-- <option value="{{ $categoryToShow->event->id }}">{{ $categoryToShow->event->id }} | {{ $categoryToShow->event->event_name }}</option> -->
                                                 <option value="{{ $categoryToShow->event->id }}">{{ $categoryToShow->event->event_name }}</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                        </div>

                                        <div class="mb-2">
                                            <label for="category_id" class="block text-gray-700 text-md font-bold mb-2">Category</label>
                                            <select id="category_id" name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('category_id') is-invalid @enderror" required>
                                                <!-- <option value="{{ $categoryToShow->id }}">{{ $categoryToShow->category_id }} | {{ $categoryToShow->score }}</option> -->
                                                <option value="{{ $categoryToShow->id }}">{{ $categoryToShow->category_name }}</option>
                                            </select>
                                            <x-input-error :messages="$errors->get('id')" class="mt-2" />

                                        <div class="mb-2">
                                            <label for="criteria_name" class="block text-gray-700 text-md font-bold mb-2">Criteria Name</label>
                                            <input type="text" name="criteria_name" id="criteria_name" value="{{ old('criteria_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('criteria_name') is-invalid @enderror" required>
                                            <x-input-error :messages="$errors->get('criteria_name')" class="mt-2" />
                                        </div>

                                        @if($type_of_scoring === 'points')
                                            <div class="mb-2">
                                                <label for="criteria_score" class="block text-gray-700 text-md font-bold mb-2">Score</label>
                                                <input type="text" name="criteria_score" id="criteria_score" value="{{ old('criteria_score') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('criteria_score') is-invalid @enderror" required>
                                                <x-input-error :messages="$errors->get('criteria_score')" class="mt-2" />
                                            </div>
                                        @endif
                                      
                                        </div>
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
            </div>
            <div class="flex justify-between mt-1 mb-2">
                <div class="mt-2 text-sm font-bold uppercase">
                    Criteria List in <text class="text-red-500">{{$categoryToShow->category_name}}</text> 
                </div>
                <div>
                    <input wire:model.live="search" type="text" class="text-sm border text-black border-gray-300 rounded-md px-3 py-1.5 w-full md:w-64" placeholder="Search..." autofocus>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                    <thead class="bg-gray-200 text-black">
                        <!-- <tr >
                            <th colspan="9" class="border-none bg-white border border-gray-400 px-3 py-2 uppercase"></th>
                        </tr> -->
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
                                <button wire:click="sortBy('event_id')" class="w-full h-full flex items-center justify-center">
                                    Event
                                    @if ($sortField == 'event_id')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                                
                           
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('category_id')" class="w-full h-full flex items-center justify-center">
                                    Category
                                    @if ($sortField == 'category_id')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>

                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('criteria_name')" class="w-full h-full flex items-center justify-center">
                                    Criteria Name
                                    @if ($sortField == 'criteria_name')
                                        @if ($sortDirection == 'asc')
                                            &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                        @else
                                            &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                        @endif
                                    @endif
                                </button>
                            </th>
                            <th class="border border-gray-400 px-3 py-2">
                                <button wire:click="sortBy('criteria_score')" class="w-full h-full flex items-center justify-center">
                                    Score
                                    @if ($sortField == 'criteria_score')
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
                        @foreach ($criterion as $criteria)
                            <tr class="hover:bg-gray-100" wire:model="selectedCategory">
                                <td class="text-black border border-gray-400">{{ $criteria->id}}</td>
                                <td class="text-black border border-gray-400">{{ $criteria->event->event_name}}</td> 
                                <td class="text-black border border-gray-400">{{ $criteria->category->category_name}}</td>
                                <td class="text-black border border-gray-400">{{ $criteria->criteria_name}}</td>
                                <td class="text-black border border-gray-400">{{ $criteria->criteria_score}}</td>
                                <td class="text-black border border-gray-400">
                                    <div class="flex justify-center items-center space-x-2">
                                        <div x-data="{ open: false
                                                }">
                                            <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-2 py-[5px] rounded hover:bg-blue-700">
                                                <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                            </a>
                                            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg max-h-[90vh] overflow-y-auto  mx-auto">
                                                    <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                        <p class="text-xl font-bold">Edit Criteria</p>
                                                        <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                                    </div>
                                                    <div class="mb-4">
                                                    @if (Auth::user()->hasRole('admin'))
                                                        <form action="{{ route('admin.criteria.update', $criteria->id) }}" method="POST" class="" enctype="multipart/form-data">
                                                    @else
                                                        <form action="{{ route('event_manager.criteria.update', $criteria->id) }}" method="POST" class="" enctype="multipart/form-data">
                                                    @endif
                                                        
                                                            <x-caps-lock-detector />
                                                            @csrf
                                                            @method('PUT')

                                                            <div class="mb-2">
                                                                <label for="event_id" class="block text-gray-700 text-md font-bold mb-2">Event</label>
                                                                <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                                                    <!-- <option value="{{ $categoryToShow->event->id }}">{{ $categoryToShow->event->id }} | {{ $categoryToShow->event->event_name }}</option> -->
                                                                    <option value="{{ $categoryToShow->event->id }}">{{ $categoryToShow->event->event_name }}</option>
                                                                </select>
                                                                <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                                            </div>

                                                            <div class="mb-2">
                                                                <label for="category_id" class="block text-gray-700 text-md font-bold mb-2">Category:</label>
                                                                <select id="category_id" name="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('category_id') is-invalid @enderror" required>
                                                                    <!-- <option value="{{ $categoryToShow->id }}">{{ $categoryToShow->category_id }} | {{ $categoryToShow->score }}</option> -->
                                                                    <option value="{{ $categoryToShow->id }}">{{ $categoryToShow->category_name }}</option>
                                                                </select>
                                                                <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                                            </div>

                                                            <div class="mb-2">
                                                                <label for="criteria_name" class="block text-gray-700 text-md font-bold mb-2">Criteria Name</label>
                                                                <input type="text" name="criteria_name" id="criteria_name" value="{{ $criteria->criteria_name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('criteria_name') is-invalid @enderror" required>
                                                                <x-input-error :messages="$errors->get('criteria_name')" class="mt-2" />
                                                            </div>

                                                            @if($type_of_scoring === 'points')
                                                                <div class="mb-2">
                                                                    <label for="criteria_score" class="block text-gray-700 text-md font-bold mb-2">Score</label>
                                                                    <input type="text" name="criteria_score" id="criteria_score" value="{{ $criteria->criteria_score }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('criteria_score') is-invalid @enderror" required>
                                                                    <x-input-error :messages="$errors->get('criteria_score')" class="mt-2" />
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
                                        @if (Auth::user()->hasRole('admin'))
                                            @if (Auth::user()->hasRole('admin'))
                                                <form id="deleteSelected" action="" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $criteria->id }}', '{{ $criteria->criteria_name}}', '{{ $criteria->criteria_score}}');">
                                            @else
                                                <form id="deleteSelected" action="" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $criteria->id }}', '{{ $criteria->criteria_name}}', '{{ $criteria->criteria_score}}');">
                                            @endif
                                                @csrf
                                                @method('DELETE')
                                                <button class="bg-red-500 text-white text-sm px-2 py-1 rounded hover:bg-red-700" id="hehe">
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
                @if($categoryToShow)
                    <tr>
                        <td colspan="2">
                            <div class="flex justify-between">
                                <div class="uppercase text-black mt-2 text-sm mb-4">
                                    @if($search)
                                        {{ $criterion->total() }} Search results 
                                    @endif                                    
                                </div>
                                <div class="justify-end">
                                    <p class="text-black mt-2 text-sm mb-4 uppercase">Total # of criteria: <text class="ml-2">{{ $categoryCounts[$categoryToShow->id]->criteria_count ?? 0 }}</text></p>
                                    @if($search)
                                        <p><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
                                    @endif
                                </div>
                            </div> 
                        </td>
                    </tr>
                @endif
            </div>
            
            <text  class="font-bold uppercase">{{ $criterion->links() }}</text>
        @endif
 
        
    @endif  
   
</div>


<script>

    document.addEventListener('DOMContentLoaded', function() {
        tippy('[data-tippy-content]', {
            allowHTML: true,
            theme: 'light', // Optional: Change the tooltip theme (light, dark, etc.)
            placement: 'right-end', // Optional: Adjust tooltip placement
        });
    });

</script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
      Fancybox.bind('[data-fancybox]', {
        contentClick: "iterateZoom",
        Images: {
            Panzoom: {
                maxScale: 3,
                },
            initialSize: "fit",
        },
        Toolbar: {
          display: {
            left: ["infobar"],
            middle: [
              "zoomIn",
              "zoomOut",
              "toggle1to1",
              "rotateCCW",
              "rotateCW",
              "flipX",
              "flipY",
            ],
            right: ["slideshow", "download", "thumbs", "close"],
          },
        },
      });    
</script>


<script>
    function confirmDeleteAll(event) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: 'Select Employee to Delete All Records',
            html: `
            
                <select id="category_id_select" class="cursor-pointer hover:border-red-500 swal2-select">
                    <option value="">Select category</option>
                     @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_id }} | {{ $category->category_name }} - {{ $category->score }}</option>
                        @endforeach
                </select>
            `,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!',
            preConfirm: () => {
                const categoryId = Swal.getPopup().querySelector('#category_id_select').value;
                if (!categoryId) {
                    Swal.showValidationMessage(`Please select a category`);
                }
                return { categoryId: categoryId };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const eventId = result.value.eventId;
                document.getElementById('category_id_to_delete').value = eventId;
                document.getElementById('deleteAll').submit();
            }
        });
    }

    function ConfirmDeleteSelected(event, rowId, criteriaName) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete the criteria ${criteriaName} ?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteSelected');
                // Replace the placeholders with the actual rowId and employeeId
                const actionUrl = form.action.replace(':id', rowId);
                form.action = actionUrl;
                form.submit();
            }
        });

        return false; 
    }
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var output = document.getElementById('imagePreview');
            output.src = reader.result;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
<script>
function handleImageError(image) {
    // Set the default image
    image.src = "{{ asset('assets/img/user.png') }}";
    
    // Display the error message
    document.getElementById('errorMessage').style.display = 'block';
}
</script>

<script>
         function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#blah')
                        .attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
</script>
<!--  -->
