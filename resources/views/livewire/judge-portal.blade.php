<div>
    @if (Auth::user()->hasRole('judge'))
        <div class="flex flex-col items-center">
            <!-- Display Event Name -->
            <div class="text-4xl font-bold text-center my-4">
                @forelse ($events as $event)
                    <p>{{ $event->event_name }}</p>
                @empty
                    <p>No events available.</p>
                @endforelse
            </div>

            <div class="grid grid-cols-2 gap-4 w-full max-w-2xl">
                @foreach($categories as $index => $category)
                    <div class="{{ ['bg-yellow-600', 'bg-green-600', 'bg-blue-600', 'bg-red-600'][$index % 4] }} p-4 rounded-md text-white text-center cursor-pointer" 
                        wire:click="goToCategoryDetails({{ $category->id }})">
                        {{ $category->category_name }}
                        {{ $category->score }}
                    </div>
                @endforeach

                <a href="{{ route('my-scores') }}">
                    <button class="bg-blue-200">
                        My Score
                    </button>
                </a>
            </div>
        </div>
    @endif
</div>
