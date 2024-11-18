<div class="transition-all duration-300 h-screen flex flex-col flex-auto flex-shrink-0 antialiased">
    <div id="dashboardContent" class="h-full transition-all duration-300">
        <div class="max-w-full mx-auto">
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                    window.addEventListener('resize', () => {
                        isFullScreen = (window.innerHeight === screen.height);
                    });
                " x-show="!isFullScreen" class="flex w-full p-2 bg-blue-500 justify-between">
                <div class="ml-2 mt-0.5 font-semibold tracking-wide text-white uppercase flex items-center space-x-2">
                    <span class="text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl">
                       {{-- @php
                            $user = Auth::user();

                           // if ($user && $user->hasRole('judge')) {
                           //     $events = \App\Models\Admin\Event::where('id', $user->event_id)->get();
                          //  } else {
                                $events = collect();
                          //  }
                        @endphp

                       // @forelse ($events as $event)
                            <p style="font-family:Algerian;font-weight:bold">{{ $user->name }}</p>
                       // @empty
                            <!-- <p>No events available.</p> -->
                       // @endforelse --}}
                       <p style="font-family:arial;font-weight:bold">Login as: {{ Auth::user()->name }}</p>
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    
                    <div x-cloak class="relative" x-data="{ open: false }">
                        <div @click="open = !open" class="cursor-pointer">
                            <i class="fa-solid fa-user-gear px-3 py-2 rounded-md border border-transparent hover:border-blue-500" style="color: #ffffff;"></i>
                        </div>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20">
                            <a href="" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <i class="fa-regular fa-user"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                @if (Auth::user()->hasRole('judge'))
                    <!-- Back Button -->
                    <div class=" mt-4 mr-5 ml-3 mb-3">
                        <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-2 text-lg"></i> <!-- Adjust margin-right and size -->
                            <span>Back</span> <!-- Wrap the text in a span for better control -->
                        </a>
                    </div>

                   <!-- This div centers the content both vertically and horizontally -->
                    <div class="flex justify-center items-center min-h-screen ">
                        <!-- This container ensures the content has a maximum width of 4xl and centers it -->
                        <div class="container mx-auto p-4 bg-slate-200 ring-offset-neutral-300 rounded-tl-lg rounded-tr-lg rounded-b-lg w-full max-w-4xl mr-4 ml-4">
                            <h2 class="text-3xl text-center font-bold mb-6">{{ $category->category_name }}</h2>
                            <h3 class="text-xl font-semibold mb-4">Participants</h3>

                            <!-- Gender Filter Tabs -->
                            <div class="mb-8">
                                <div class="inline-flex justify-center rounded-md shadow-sm">
                                    <button 
                                        class="px-4 py-2 text-sm font-medium text-center text-gray-700 bg-gray-100 border border-gray-300 rounded-l-md focus:outline-none hover:bg-gray-200"
                                        wire:click="$set('genderFilter', 'all')"
                                        :class="{'bg-blue-500 text-white': genderFilter === 'all'}"
                                    >
                                        All
                                    </button>
                                    <button 
                                        class="px-4 py-2 text-sm font-medium text-center text-gray-700 bg-gray-100 border-t border-b border-gray-300 focus:outline-none hover:bg-gray-200"
                                        wire:click="$set('genderFilter', 'male')" 
                                        :class="{'bg-blue-500 text-white': genderFilter === 'male'}"
                                    >
                                        Male
                                    </button>
                                    <button 
                                        class="px-4 py-2 text-sm font-medium text-center text-gray-700 bg-gray-100 border-t border-b border-gray-300 rounded-r-md focus:outline-none hover:bg-gray-200"
                                        wire:click="$set('genderFilter', 'female')" 
                                        :class="{'bg-blue-500 text-white': genderFilter === 'female'}"
                                    >
                                        Female
                                    </button>
                                </div>
                            </div>

                            <form action="{{ route('score.store') }}" method="POST" enctype="multipart/form-data" id="scoreForm">
                                @csrf <!-- CSRF Token -->
                                <div class="space-y-6">
                                    @foreach ($participants as $index => $participant)
                                        @if ($genderFilter == 'all' || $participant->participant_gender == $genderFilter)
                                            <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 border-b-2 border-blue-500 pb-4">
                                                <!-- Participant Image -->
                                                <div class="w-full md:w-1/4 flex items-center justify-center">
                                                    <div class="w-32 h-32 bg-gray-200 flex items-center justify-center rounded-lg">
                                                        <svg class="h-12 w-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m16-10V3a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2m3 4l3 3l4-4l5 5M13 7h6v6"/>
                                                        </svg>
                                                    </div>
                                                </div>

                                                <!-- Participant Info and Score Inputs -->
                                                <div class="w-full md:w-3/4 pl-4">
                                                    <h4 class="text-lg font-semibold">Participant No. {{ $index + 1 }}: {{ $participant->participant_name }}</h4>
                                                    <p class="text-gray-600">Gender: {{ $participant->participant_gender }}</p>

                                                    <div class="mt-4 space-y-4">
                                                        @foreach ($criteria as $criterion)
                                                            <div class="flex items-center">
                                                                <label class="w-1/2 text-gray-700">{{ $criterion->criteria_name }}</label> 
                                                                <input 
                                                                    type="number" 
                                                                    name="scores[{{ $participant->id }}][criteria_scores][{{ $criterion->id }}]" 
                                                                    required 
                                                                    min="0"
                                                                    max="{{ $criterion->criteria_score }}"
                                                                    class="score-input p-2 border rounded-md"
                                                                    data-max="{{ $criterion->criteria_score }}"
                                                                    oninput="validateMaxValue(this)"
                                                                />
                                                                /{{ $criterion->criteria_score }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    <!-- Submit Button -->
                                    <div class="text-right mt-4">
                                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md">
                                            Submit Scores
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                @endif
            </div>
            </div>
    </div>
</div>

