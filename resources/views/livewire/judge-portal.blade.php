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

            <!-- Display Categories below the Event Name -->
            <div class="grid grid-cols-2 gap-4 w-full max-w-2xl">
                @foreach($categories as $index => $category)
                    <div class="{{ ['bg-yellow-600', 'bg-green-600', 'bg-blue-600', 'bg-red-600'][$index % 4] }} p-4 rounded-md text-white text-center cursor-pointer" 
                         wire:click="showCategories({{ $category->id }})">
                        {{ $category->category_name }}
                        {{ $category->score }}
                    </div>
                @endforeach
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
