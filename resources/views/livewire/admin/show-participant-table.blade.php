<div class="mb-4">
        @php
            session(['selectedEvent' => $selectedEvent]);
            session(['selectedGroup' => $selectedGroup]);
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
        <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Admin / Manage Participant</div>
    @else
        <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase">Event Manager / Manage Participant</div>
    @endif
    </div>




   
    <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-tight text-md text-black  mt-2 uppercase"></div>
        </div>
        <div class="flex flex-col md:flex-row items-start md:items-center md:justify-start">
            <!-- Dropdown and Delete Button -->
            <div class="flex items-center w-full md:w-auto">
                <label for="event_id" class="block text-sm text-gray-700 font-bold md:mr-4 truncate uppercase">Event:</label>
                <select wire:model="selectedEvent" id="event_id" name="event_id" wire:change="updateEmployees"
                        class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror md:w-auto"
                        required>
                    <option value="">Event</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                    @endforeach
                </select>
               
                @if($eventToShow)
                    <!-- <form id="deleteAll" action="{{ route('admin.group.deleteAll') }}" method="POST" onsubmit="return confirmDeleteAll(event);" class="flex ml-4">
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




        @if(!$eventToShow)
            <p class="text-black text-sm mt-11 mb-4 uppercase text-center">No selected Event</p>
        @endif
       




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
                        <div class="mb-4">
                            <form action="{{ route('admin.participant.store') }}" method="POST" class=""  enctype="multipart/form-data">
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
                                        <label for="participant_number" class="block text-gray-700 text-md font-bold mb-2">Participant Number</label>
                                        <input type="number" name="participant_number" id="participant_number" value="{{ old('participant_number') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_number') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('participant_number')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="group_id" class="block text-gray-700 text-md font-bold mb-2">Group</label>
                                        <select id="group_id" name="group_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('group_id') is-invalid @enderror">
                                           
                                            <!-- Placeholder option -->
                                            <option value="">Select Group</option>
                                           
                                            <!-- Dynamically set the selected option -->
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}"
                                                    @if(isset($groupToShow) && $group->id == $groupToShow->id) selected @endif>
                                                    {{ $group->group_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error :messages="$errors->get('group_id')" class="mt-2" />
                                    </div>

                                    <div class="mb-2">
                                        <label for="participant_name" class="block text-gray-700 text-md font-bold mb-2">Name</label>
                                        <input type="text" name="participant_name" id="participant_name" value="{{ old('participant_name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_name') is-invalid @enderror" required>
                                        <x-input-error :messages="$errors->get('participant_name')" class="mt-2" />
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




                                    <div class="mb-2">
                                        <input type="file" name="participant_photo" id="participant_photo" class="hidden" accept="image/*" onchange="previewImage(event)">
                                        <label for="participant_photo" class="cursor-pointer flex flex-col items-center">
                                            <div id="imagePreviewContainer" class="mb-2 text-center">
                                                <img id="imagePreview" src="{{ asset('storage/default/user.png') }}" class="rounded-lg w-32 h-auto">
                                            </div>
                                            <span class="text-sm text-gray-500">Select Photo</span>
                                        </label>
                                        <span id="photoError" class="text-red-500 text-sm"></span>
                                    </div>
                                   
                                    <!-- Save Button -->
                                    <div class="flex mb-4 mt-10 justify-center">
                                        <button type="submit" id="saveButton" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
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
        <p class="text-black mt-8 text-center">No Group found in <text class="text-red-500">{{ $eventToShow->event_name }}</text> for matching "{{ $search }}"</p>  
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
                                    <button wire:click="sortBy('participant_number')" class="w-full h-full flex items-center justify-center">
                                        Participant No.
                                        @if ($sortField == 'participant_number')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('group_name')" class="w-full h-full flex items-center justify-center">
                                        Group Name
                                        @if ($sortField == 'group_name')
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
                                <th class="border border-gray-400 px-3 py-2">Action</th>
                            </tr>
                        </thead>
                        <tbody >
                            @foreach ($participants as $participant)
                                <tr class="hover:bg-gray-100" wire:key="participant-{{ $participant->id }}">
                                    <td class="text-black border border-gray-400">{{ $participant->participant_number}}</td>        
                                    <td class="text-black border border-gray-400">{{ $participant->group?->group_name ?? 'N/A' }}</td>          
                                    <td class="text-black border border-gray-400">{{ $participant->participant_name}}</td>
                                    <td class="text-black border border-gray-400">{{ $participant->participant_gender}}</td>
                                    <td class="text-black border border-gray-400">{{ $participant->participant_photo}}</td>
                                    <td class="text-black border border-gray-400 px-1 py-1">
                                    <div class="flex justify-center items-center space-x-2 flex-nowrap">
                                        @if($eventToShow && $participant)
                                        <div x-data="{ open: false,
                                                id: {{ json_encode($participant->id) }},
                                                event: {{ json_encode($participant->event_id) }},
                                                participant_number: {{ json_encode($participant->participant_number) }},
                                                group: {{ json_encode($participant->group_id) }},
                                                participant_name: {{ json_encode($participant->participant_name) }},
                                                participant_gender: {{ json_encode($participant->participant_gender) }},
                                                participant_photo: {{ json_encode($participant->participant_photo) }}
                                            }">
                                            <!-- Edit Button -->
                                            <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                                                <i class="fa-solid fa-pen fa-xs"></i>
                                            </a>
                                            <!-- Edit Modal Content -->
                                            <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                                                <div @click.away="open = false" class="w-[35%] bg-white p-6 rounded-lg shadow-lg mx-auto">
                                                    <div class="flex justify-between items-start pb-3">
                                                        <p class="text-xl font-bold">Edit Participant</p>
                                                        <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                                    </div>
                                                    <form action="{{ route('admin.participant.update', $participant->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        @method('PUT')

                                                        <!-- Event Selection -->
                                                        <div class="mb-2">
                                                            <label for="event_id" class="block text-left text-gray-700 text-md font-bold mb-2">Event</label>
                                                            <select id="event_id" name="event_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('event_id') is-invalid @enderror" required>
                                                                <option value="">Select Event</option>
                                                                @foreach($events as $event)
                                                                    <option value="{{ $event->id }}" {{ $participant->event_id == $event->id ? 'selected' : '' }}>{{ $event->event_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <x-input-error :messages="$errors->get('event_id')" class="mt-2" />
                                                        </div>

                                                        <!-- Participant Name -->
                                                        <div class="mb-2">
                                                            <label for="participant_number" class="block text-left text-gray-700 text-md font-bold mb-2">Participant Number</label>
                                                            <input type="number" name="participant_number" id="participant_number" value="{{ old('participant_number', $participant->participant_number) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_number') is-invalid @enderror" required>
                                                            <x-input-error :messages="$errors->get('participant_number')" class="mt-2" />
                                                        </div>

                                                        <!-- Group Selection -->
                                                        <div class="mb-2">
                                                            <label for="group_id" class="block text-left text-gray-700 text-md font-bold mb-2">Group</label>
                                                            <select id="group_id" name="group_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('group_id') is-invalid @enderror">
                                                                <option value="">Select Group</option>
                                                                @foreach($groups as $group)
                                                                    <option value="{{ $group->id }}" {{ $participant->group_id == $group->id ? 'selected' : '' }}>{{ $group->group_name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <x-input-error :messages="$errors->get('group_id')" class="mt-2" />
                                                        </div>

                                                        <!-- Participant Name -->
                                                        <div class="mb-2">
                                                            <label for="participant_name" class="block text-left text-gray-700 text-md font-bold mb-2">Name</label>
                                                            <input type="text" name="participant_name" id="participant_name" value="{{ old('participant_name', $participant->participant_name) }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('participant_name') is-invalid @enderror" required>
                                                            <x-input-error :messages="$errors->get('participant_name')" class="mt-2" />
                                                        </div>

                                                        <!-- Participant Gender -->
                                                        <div class="mb-2">
                                                            <label for="participant_gender" class="block text-left text-gray-700 text-md font-bold mb-2">Gender</label>
                                                            <select id="participant_gender" name="participant_gender" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('participant_gender') is-invalid @enderror" required>
                                                                <option value="">Select Gender</option>
                                                                <option value="male" {{ $participant->participant_gender == 'male' ? 'selected' : '' }}>Male</option>
                                                                <option value="female" {{ $participant->participant_gender == 'female' ? 'selected' : '' }}>Female</option>
                                                            </select>
                                                            <x-input-error :messages="$errors->get('participant_gender')" class="mt-2" />
                                                        </div>




                                                        <!-- Participant Photo -->
                                                        <div class="mb-2">
                                                            <div class="cursor-pointer flex flex-col items-center">
                                                           
                                                                <!-- Display the current photo or a default image -->
                                                                <img
                                                                    id="imagePreview"
                                                                    src="{{ $participant->participant_photo ? asset('storage/participant_photo/' . $participant->participant_photo) : asset('assets/img/user.png') }}"
                                                                    class="rounded-full w-32 h-32 mb-2 object-cover"
                                                                >
                                                            </div>
                                                            <input type="file" name="participant_photo" id="participant_photo" accept="image/*">
                                                            <x-input-error :messages="$errors->get('participant_photo')" class="mt-2" />
                                                        </div>

                                                        <!-- Submit Button -->
                                                        <div class="flex justify-center mt-4">
                                                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Save Changes</button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Button -->
                                       <!-- Delete Button -->
                                        <form id="deleteSelected" action="{{ route('admin.participant.destroy', $participant->id) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $participant->id }}', '{{ $participant->participant_name }}');">
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
                                        <p class="text-black mt-2 text-sm mb-4 uppercase">Total # of group: <text class="ml-2">{{ $groupCounts[$eventToShow->id]->group_count ?? 0 }}</text></p>
                                       
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
               
            @endif
        @endif
    </div>




<script src="https://unpkg.com/@popperjs/core@2"></script>
<script src="https://unpkg.com/tippy.js@6"></script>




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
            title: 'Select Participant to Delete All Records',
            html: `
           
                <select id="group_id_select" class="cursor-pointer hover:border-red-500 swal2-select">
                    <option value="">Select group</option>
                     @foreach($participants as $group)
                            <option value="{{ $group->id }}">{{ $group->group_id }} | {{ $group->group_name }} - {{ $group->score }}</option>
                        @endforeach
                </select>
            `,
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!',
            preConfirm: () => {
                const groupId = Swal.getPopup().querySelector('#group_id_select').value;
                if (!groupId) {
                    Swal.showValidationMessage(`Please select a group`);
                }
                return { groupId: groupId };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const eventId = result.value.eventId;
                document.getElementById('group_id_to_delete').value = eventId;
                document.getElementById('deleteAll').submit();
            }
        });
    }




    function ConfirmDeleteSelected(event, rowId, participantName) {
        event.preventDefault(); // Prevent form submission initially




        Swal.fire({
            title: `Are you sure you want to delete the participant ${participantName} ?`,
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
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('imagePreview');
            output.src = reader.result;
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




<!-- this will handle the photo large size -->




<script>
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput = document.getElementById('participant_photo');
        const saveButton = document.getElementById('saveButton');
        const photoError = document.getElementById('photoError');
        const maxSize = 2 * 1024 * 1024; // 2MB in bytes




        photoInput.addEventListener('change', function () {
            const file = photoInput.files[0];




            // Reset the error message and enable button by default
            photoError.textContent = '';
            saveButton.disabled = false;




            if (file && file.size > maxSize) {
                photoError.textContent = 'The photo exceeds the maximum size of 2MB.';
                saveButton.disabled = true; // Disable the Save button
            }




            // Show a preview of the image
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('imagePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>




<script>
    document.getElementById('participant_photo').addEventListener('change', function() {
        const file = this.files[0];
        const maxSize = 2 * 1024 * 1024; // 2MB
        if (file.size > maxSize) {
            alert('The photo exceeds the maximum size of 2MB.');
            this.value = ''; // Reset the input
        }
    });
</script>
<script>
    document.getElementById('participant_photo').addEventListener('change', function(event) {
        const file = event.target.files[0]; // Get the selected file
        if (file && file.type.startsWith('image/')) { // Validate that it's an image
            const reader = new FileReader(); // Create a FileReader object
            reader.onload = function(e) {
                // Update the image preview src
                document.getElementById('imagePreview').src = e.target.result;
            };
            reader.readAsDataURL(file); // Read the file as a Data URL
        } else {
            alert('Please select a valid image file.');
        }
    });
</script>





