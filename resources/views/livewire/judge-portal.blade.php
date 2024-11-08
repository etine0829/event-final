@if (Auth::user()->hasRole('judge'))
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
        
        <div class="flex flex-col items-center">
            <!-- Display Event Name -->
            <div class="text-4xl font-bold text-center my-4">
                @forelse ($events as $event)
                    <p>{{ $event->event_name }}</p>
                @empty
                    <p>No events available.</p>
                @endforelse
            </div>

            
            <div x-data="{ open: false }">
                <!-- Display Categories below the Event Name -->
                <a @click="open = true">
                    <div class="grid grid-cols-2 gap-4 w-full max-w-2xl">
                        @foreach($categories as $index => $category)
                            <div class="{{ ['bg-yellow-600', 'bg-green-600', 'bg-blue-600', 'bg-red-600'][$index % 4] }} p-4 rounded-md text-white text-center cursor-pointer" 
                                wire:click="({{ $category->id }})">
                                {{ $category->category_name }}
                                {{ $category->score }}
                            </div>
                        @endforeach
                    </div>
                </a>
                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.away="open = true" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                        <div class="flex justify-between items-center pb-3">
                            <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                        </div>
                        <div class="mb-4">
                            <form>
                                <div class="p-8 bg-gray-100 min-h-screen flex flex-col items-center">
                                   <!-- pang scroll -->
                                    <div class="overflow-y-scroll h-[600px] w-full max-w-lg border border-gray-300 rounded-lg shadow-md bg-white mt-8">
                                        
                                        <!-- Participant Section 1 -->
                                        <div class="p-4 border-b border-gray-200">
                                            <!-- Image Upload -->
                                            <div class="flex items-center justify-center mb-4">
                                                <div class="w-24 h-24 bg-gray-300 flex items-center justify-center rounded-lg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            
                                            <!-- Participant Info -->
                                            <div class="mb-4">
                                                <p class="text-sm font-semibold">Participant No</p>
                                                <p class="text-lg font-bold">Mr. Bil-isan</p>
                                                <p class="text-sm">Name:</p>
                                            </div>
                                            
                                            <!-- Criteria Inputs -->
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 30%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 30%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 20%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 20%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                            </div>

                                            <!-- Comment Box -->
                                            <div class="mt-4">
                                                <textarea class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:border-blue-500" placeholder="Comment"></textarea>
                                            </div>
                                        </div>

                                        <!-- Participant Section 2 -->
                                        <div class="p-4 border-b border-gray-200">
                                            <!-- Repeat inner content similar to the above section -->
                                            <div class="flex items-center justify-center mb-4">
                                                <div class="w-24 h-24 bg-gray-300 flex items-center justify-center rounded-lg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <p class="text-sm font-semibold">Participant No. 1</p>
                                                <p class="text-lg font-bold">Mr. Bil-isan</p>
                                                <p class="text-sm">Name: John Kenneth Clemen</p>
                                            </div>
                                            
                                            <div class="space-y-2">
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 30%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 30%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 20%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                                <div class="flex items-center">
                                                    <label class="text-sm font-semibold w-24">Criteria 20%</label>
                                                    <input type="text" class="border border-gray-300 rounded p-1 w-full focus:outline-none focus:border-blue-500"/>
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <textarea class="border border-gray-300 rounded p-2 w-full focus:outline-none focus:border-blue-500" placeholder="Comment"></textarea>
                                            </div>
                                        </div>
                                        
                                        <!-- More Participant Sections as needed -->

                                    </div>

                                    <!-- Done Button -->
                                    <button class="mt-6 bg-yellow-600 text-white px-6 py-2 rounded shadow hover:bg-yellow-700">DONE</button>
                                </div>

                              
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
@endif

<script>
    function confirmDeleteAll(event) {
        event.preventDefault();
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
                document.getElementById('deleteAll').submit();
            }
        });
    }

    function ConfirmDeleteSelected(event, eventID, eventName) {
        event.preventDefault();
        Swal.fire({
            title: `Are you sure you want to delete the event ${eventName}?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteSelected');
                form.action = `{{ route('admin.event.destroy', ':Id') }}`.replace(':Id', eventID);
                form.submit();
            }
        });
    }
</script>
