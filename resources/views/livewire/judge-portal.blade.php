<div class="flex flex-col items-center">
    <!-- Display Event Name -->
    <div class="text-4xl font-bold text-center my-4">
        @forelse ($events as $event)
            <p>{{ $event->event_name }}</p>
        @empty
            <p>No events available.</p>
        @endforelse
    </div>

    <div class="grid grid-cols gap-4 w-full max-w-2xl">
        @foreach($categories as $index => $category)
            <div class="{{ ['bg-yellow-600', 'bg-green-600', 'bg-blue-600', 'bg-red-600'][$index % 4] }} p-4 rounded-md text-white text-center cursor-pointer hover:scale-105 transition-transform duration-300" wire:click="goToCategoryDetails({{ $category->id }})">
                {{ $category->category_name }} {{ $category->score }} %
                <h3>(click to open)</h3>
            </div>
        @endforeach

         <!-- Show the My Score button only for the current event -->
         <a href="{{ route('scores.show', ['eventId' => $event->id]) }}">
                <button class="bg-blue-200">
                    My Score
                </button>
            </a>

    </div>
</div>
