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
            <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Admin / Manage Participant</div>
        </div>
        <div class="flex flex-col md:flex-row items-start md:items-center md:justify-start">
            <!-- Dropdown and Delete Button -->
            <div class="flex items-center w-full md:w-auto">
                <label for="event_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Event</label>
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
                    <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add Participant
                </button>
                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.away="open = true" class="w-[30%] max-h-[90%]  bg-white p-6 rounded-lg shadow-lg  mx-auto overflow-y-auto">
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-xl font-bold">Add Participant</p>
                            <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                        </div>
                        <div class="mb-4" id="dynamicInput">
                            <form action="{{ route('admin.participant.store') }}" method="POST" enctype="multipart/form-data">
                            <x-caps-lock-detector />
                                @csrf
                                    <div class="mb-2">
                                        <label for="event_id" class="block text-gray-700 text-md font-bold mb-2">Event</label>
                                        <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                                <option value="{{ $eventToShow->id }}">{{ $eventToShow->event_name }}</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <input type="file" name="participant_photo" id="participant_photo" class="hidden" accept="image/*" onchange="previewImage(event)">
                                        <label for="participant_photo" class="cursor-pointer flex flex-col items-center">
                                            <div id="imagePreviewContainer" class="mb-2 text-center">
                                                <img id="imagePreview" src="{{ asset('assets/img/user.png') }}" class="rounded-lg w-32 h-auto">
                                            </div>
                                            <span class="text-sm text-gray-500">Select Photo</span>
                                        </label>
                                        <x-input-error :messages="$errors->get('participant_photo')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="participant_name" class="block text-gray-700 text-md font-bold mb-2">Name</label>
                                        <input type="text" name="participant_name" id="participant_name" value="{{ old('participant_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_name') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('participant_name')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="participant_group" class="block text-gray-700 text-md font-bold mb-2">Group/Department</label>
                                        <input type="text" name="participant_group" id="participant_group" value="{{ old('participant_group') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_group') is-invalid @enderror">
                                        <x-input-error :messages="$errors->get('participant_group')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="participant_gender" class="block text-gray-700 text-md font-bold mb-2">Gender</label>
                                        <select id="participant_gender" name="participant_gender" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('participant_gender') is-invalid @enderror" required>
                                                <option value="">Select Option</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('participant_gender')" class="mt-2" />
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
        @else
            
        @endif
        @if($search && $participants->isEmpty())
        <p class="text-black mt-8 text-center">No participant found in <text class="text-red-500">{{ $eventToShow->event_name }}</text> for matching "{{ $search }}"</p>  
        <div class="flex justify-center mt-2">
            @if($search)
                <p><button class="ml-2 border border-gray-600 px-3 py-2 text-black hover:border-red-500 hover:text-red-500" wire:click="$set('search', '')"><i class="fa-solid fa-remove"></i> Clear Search</button></p>
            @endif
        </div>
        @elseif(!$search && $participants->isEmpty())
            
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
                                        Event
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
                                    <button wire:click="sortBy('participant_photo')" class="w-full h-full flex items-center justify-center">
                                        Photo
                                        @if ($sortField == 'participant_photo')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('participant_name')" class="w-full h-full flex items-center justify-center">
                                        Name
                                        @if ($sortField == 'participant_name')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('participant_gender')" class="w-full h-full flex items-center justify-center">
                                        Gender
                                        @if ($sortField == 'participant_gender')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('participant_group')" class="w-full h-full flex items-center justify-center">
                                        Group/Department
                                        @if ($sortField == 'participant_group')
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
                        @foreach ($participants as $participant)
                                <tr class="hover:bg-gray-100" wire:model="selectedEvent">                                  
                                    <td class="text-black border border-gray-400  ">{{ $participant->id }}</td> 
                                    <td class="text-black border border-gray-400">{{ $participant->event->event_name}}</td> 
                                    <td class="text-black border border-gray-400 border-t-0 border-r-0 border-l-0 px-2 py-1 flex items-center justify-center" >
                                        @if ($participant->participant_photo && Storage::exists('public/participant_photo/' . $participant->participant_photo))
                                            <a  href="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" 
                                                class="hover:border border-red-500 rounded-full" title="Click to view Picture"
                                                data-fancybox data-caption="Participant: {{ $participant->participant_name }}, {{ $participant->participant_gender }}">
                                                <img src="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" class="rounded-full w-9 h-9">
                                            </a>
                                        @else
                                            <img data-fancybox src="{{ asset('assets/img/user.png') }}" class="cursor-pointer w-9 h-9 hover:border hover:border-red-500 rounded-full" title="Click to view Picture" >
                                        @endif
                                    </td>                         
                                    <td class="text-black border border-gray-400">{{ $participant->participant_name}}</td>
                                    <td class="text-black border border-gray-400">{{ $participant->participant_gender}}</td>
                                    <td class="text-black border border-gray-400">{{ $participant->participant_group}}</td>
                                    <td class="text-black border border-gray-400 px-1 py-1">
                                        <div class="flex justify-center items-center space-x-2">
                                            @if($eventToShow && $participant)
                                            <div x-data="{ open: false, 
                                                id: {{ json_encode($participant->id) }},
                                                    event: {{ json_encode($participant->event_id) }},
                                                    participant_photo: {{ json_encode($participant->participant_photo) }},
                                                    participant_name: {{ json_encode($participant->participant_name) }},
                                                    participant_gender: {{ json_encode($participant->participant_gender) }},
                                                    participant_group: {{ json_encode($participant->participant_group) }},}">
                                                <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                                                    <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                                </a>
                                                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                    <div @click.away="open = true" class="w-[35%] bg-white p-6 rounded-lg shadow-lg  mx-auto">
                                                        <div class="flex justify-between items-start pb-3"> <!-- Changed items-center to items-start -->
                                                            <p class="text-xl font-bold">Edit Participant</p>
                                                            <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                                        </div>
                                                        <div class="mb-4">
                                                            <form id="updateCategoryForm" action="{{ route('admin.participant.update', $participant->id )}}" method="POST" class="h-[500px] overflow-y-auto pr-4">
                                                                <x-caps-lock-detector />
                                                                @csrf
                                                                @method('PUT')
                                                                
                                                                <div class="mb-2">
                                                                        <label for="event_id" class="block text-gray-700 text-md font-bold mb-2 text-left">Event</label>
                                                                        <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                                                                <option value="{{ $eventToShow->id }}">{{ $eventToShow->event_name }}</option>
                                                                        </select>
                                                                        <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                                                    </div>

                                                                    <div class="mb-4 text-center flex flex-col items-center">
                                                                        <img id="blah2" src="{{ $participant->participant_photo ? asset('storage/participant_photo/' . $participant->participant_photo) : asset('assets/img/user.png') }}" alt="Default photo Icon" class="max-w-xs mb-2" />
                                                                        <input type="file" onchange="readURL2(this);" name="participant_photo" id="participant_photo" class="p-2 bg-gray-800 text-white" accept="image/*" />
                                                                    </div>
                                                                    
                                                                    <div class="mb-4">
                                                                        <label for="participant_name" class="block text-gray-700 text-md font-bold mb-2 text-left">Name</label>
                                                                        <input type="text" name="participant_name" id="participant_name" x-model="participant_name" value="{{ $participant->participant_name }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_name') is-invalid @enderror" required>
                                                                        <x-input-error :messages="$errors->get('participant_name')" class="mt-2" />
                                                                    </div>

                                                                    <div class="mb-2">
                                                                        <label for="participant_gender" class="block text-gray-700 text-md font-bold mb-2 text-left">Gender </label>
                                                                        <select id="participant_gender" name="participant_gender" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('participant_gender') is-invalid @enderror" required>
                                                                        @if($participant->participant_gender === 'male')  
                                                                                <option value="{{ $participant->participant_gender }}">
                                                                                    @if($participant->participant_gender == 'male')
                                                                                        Male
                                                                                    @endif
                                                                                </option>
                                                                                <option value="female">Female</option>
                                                                            @else
                                                                                <option value="{{ $participant->participant_gender }}">Female</option>
                                                                                <option value="male">Male</option>
                                                                            @endif
                                                                        </select>
                                                                        <x-input-error :messages="$errors->get('participant_gender')" class="mt-2" />
                                                                    </div>

                                                                    <div class="mb-4">
                                                                        <label for="participant_group" class="block text-gray-700 text-md font-bold mb-2 text-left">Group/Department</label>
                                                                        <input type="text" name="participant_group" id="participant_group" x-model="participant_group" value="{{ $participant->participant_group }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_group') is-invalid @enderror">
                                                                        <x-input-error :messages="$errors->get('participant_group')" class="mt-2" />
                                                                    </div>

                                                                   
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
                                            <form id="deleteSelected" action="{{ route('admin.participant.destroy', [':id']) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $participant->id }}', '{{ $participant->participant_id }}', '{{ $participant->participant_name }}', '{{ $participant->score }}');">
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
                                            {{ $participants->total() }} Search results 
                                        @endif                                    
                                    </div>
                                    <div class="justify-end">
                                        <p class="text-black mt-2 text-sm mb-4 uppercase">Total # of participant: <text class="ml-2">{{ $participantCounts[$eventToShow->id]->participant_count ?? 0 }}</text></p>
                                        
                                    </div>
                                </div> 
                            </td>
                            <td>
                                {{ $participants->links() }}
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

    function searchparticipants(event) {
            let searchTerm = event.target.value.toLowerCase();
            if (searchTerm === '') {
                this.participantsToShow = @json($participantToShow->toArray());
            } else {
                this.participantsToShow = this.participantsToShow.filter(participant =>
                    participant.participant_photo.toLowerCase().includes(searchTerm) ||
                    participant.participant_name.toLowerCase().includes(searchTerm) ||
                    participant.participant_gender.toLowerCase().includes(searchTerm) ||
                    participant.participant_group.toLowerCase().includes(searchTerm) ||
                    participant.event.event_name.toLowerCase().includes(searchTerm)
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
                            <option value="{{ $event->id }}">{{ $event->name }} - {{ $event->venue }} - {{ $event->participant_gender }}</option>
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

        function ConfirmDeleteSelected(event, rowId, participantId, participantname, participant_comment) {
            event.preventDefault(); // Prevent form submission initially

            Swal.fire({
                title: `Are you sure you want to delete the participant ${participantId} - ${participantname} ${participant_comment} ?`,
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('deleteSelected');
                    // Replace the placeholders with the actual rowId and participantId
                    const actionUrl = form.action.replace(':id', rowId).replace(':participant_id', participantId);
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
    <script>
            function readURL2(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#blah2')
                            .attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }
</script>


@endif