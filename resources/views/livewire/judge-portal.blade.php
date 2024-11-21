<div class="flex flex-col items-center">
    <!-- Display Event Name -->
    <div class="text-4xl font-bold text-center my-4">
        @forelse ($events as $event)
            <p>{{ $event->event_name }}</p>
        @empty
            <p>No events available.</p>
        @endforelse
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 w-full max-w-5xl">
        @foreach($categories as $category)
            <div class="bg-gray-100 border border-gray-300 p-4 rounded-lg shadow-md text-center cursor-pointer hover:shadow-lg transition-shadow duration-300" 
                wire:click="goToCategoryDetails({{ $category->id }})">
                <h2 class="text-xl font-bold">{{ $category->category_name }} {{ $category->score }} %</h2>
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
