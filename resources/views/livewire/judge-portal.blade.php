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
        
        <div class="flex">
            <!-- Sidebar for categories -->
            <div>
                <ul>
                    <div class="text-4xl flex flex-col text-center pt-4">  
                        <ul class="list-none text-center">
                            @forelse ($events as $event)
                                <li>{{ $event->event_name }}</li>
                            @empty
                                <li>No events available.</li>
                            @endforelse
                        </ul>
                    </div>
                </ul>
            </div>
            
            <div>
                <ul>
                    @foreach($categories as $index => $category)
                        <div class="@php echo ['bg-yellow-600', 'bg-green-600', 'bg-blue-600', 'bg-red-600'][$index % 4]; @endphp p-4 rounded-md mb-2">
                            <li class="cursor-pointer text-white" wire:click="showCategories({{ $category->id }})">
                                {{ $category->category_name }}
                            </li>
                        </div>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

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

        function ConfirmDeleteSelected(event, eventID, eventName) {
        event.preventDefault(); // Prevent form submission initially

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
                // Replace the placeholder with the actual event ID
                form.action = `{{ route('admin.event.destroy', ':Id') }}`.replace(':Id', eventID);
                form.submit();
            }
        });

        return false; 
    }

    </script>


