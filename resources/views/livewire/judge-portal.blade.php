<div class="flex flex-col items-center bg-gray-50 min-h-screen p-6">
    <!-- Display Event Name -->
    <div class="text-4xl font-bold text-center my-6 text-blue-600">
        @forelse ($events as $event)
            <p class="hover:text-blue-800 transition-colors duration-300 bonheur-font">
                {{ $event->event_name }}
            </p>
        @empty
            <p class="text-gray-500">No events available.</p>
        @endforelse
    </div>

    <!-- Categories Grid -->
    <div class="flex justify-center items-center ">
        <div class="uppercase grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 w-full max-w-6xl">
            @foreach($categories as $category)
                <div 
                    class="bg-white border border-gray-300 p-6 rounded-lg shadow-md text-center cursor-pointer hover:shadow-lg hover:bg-blue-100 transition-all duration-300 transform hover:-translate-y-1"
                    wire:click="goToCategoryDetails({{ $category->id }})"
                >
                    <h2 class="text-2xl font-bold text-gray-700 mb-2">
                        {{ $category->category_name }}
                    </h2>
                    <p class="text-lg text-gray-500">
                        {{ $category->score }}%
                    </p>
                    <span class="text-blue-600 hover:text-blue-800">(Click to open)</span>
                </div>
            @endforeach
        </div>
    </div>
    <!-- My Score Button -->
    <div class="mt-8">
        @if ($event)
            <a href="{{ route('scores.show', ['eventId' => $event->id]) }}">
                <button 
                    class="bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:bg-blue-700 transition-colors duration-300">
                    Scorecard
                </button>
            </a>
        @endif
    </div>
</div>
