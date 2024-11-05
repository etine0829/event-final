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
                    @forelse ($events as $event)
                        <li> {{ $event->event_name }}</li>
                    @empty
                        <li>No events assigned.</li>
                    @endforelse
                </ul>
            </div>
            
            <div>
                <ul>
                    @foreach($categories as $category)
                        <li class="cursor-pointer" wire:click="showCategories({{ $category->id }})">
                            {{ $category->category_name }}
                        </li>
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


