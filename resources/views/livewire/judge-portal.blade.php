<div>
    <style>
        .aa {
            font-family: arial, cursive, sans-serif;
            font-weight: bold;
        }

    </style>
    @if (Auth::user()->hasRole('judge'))
        <div class="flex flex-col items-center">
            <div class="text-4xl font-bold text-center my-4 mt-4 mb-2" style="">
                @forelse ($events as $event)
                    <p class="aa text-[40px] sm:text-base md:text-lg lg:text-xl">{{ $event->event_name }}</p>
                @empty
                    <p>No events available.</p>
                @endforelse
            </div>

            <div class="p-4 flex flex-col gap-4 w-full max-w-2xl">
                @foreach($categories as $index => $category)
                    <div class="{{ ['bg-yellow-600', 'bg-green-600', 'bg-blue-600', 'bg-red-600'][$index % 4] }} pt-8 pb-8 rounded-md text-white text-center cursor-pointer" 
                        wire:click="goToCategoryDetails({{ $category->id }})">
                        <span class="text-[32px] sm:text-sm md:text-lg lg:text-xl">{{ $category->category_name }} </span>
                        <span class="text-[32px] sm:text-sm md:text-base lg:text-lg">{{ $category->score }}</span><br>
                        <span class="text-white text-base">(click to open)</span>
                    </div>
                @endforeach
                    <div class="bg-white pt-8 pb-8 rounded-md text-black text-center cursor-pointer" 
                        wire:click="goToCategoryDetails({{ $category->id }})">
                        <span class="text-[32px] sm:text-sm md:text-lg lg:text-xl uppercase">Score </span><br>
                        <span class="text-black text-base">(click to open)</span>
                    </div>
            </div>
        </div>
    @endif
</div>
