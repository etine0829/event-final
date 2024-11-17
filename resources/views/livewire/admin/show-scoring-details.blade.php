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
                    <div class="bg-slate-300 container mx-auto p-4">
                        <!-- Back Button -->
                        <div class="mb-4">
                            <a href="{{ route('judge.dashboard')}}" >
                            <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md Z-">
                                Back
                            </button></a>
                        </div>

                        <h2 class="text-3xl font-bold mb-4">{{ $category->category_name }}</h2>

                        <h3 class="text-xl font-semibold mb-4">Participants</h3>

                        <!-- Gender Filter Tabs -->
                        <div class="mb-4">
                            <div class="inline-flex rounded-md shadow-sm">
                                <button 
                                    class="px-4 py-2 text-sm font-medium text-center text-gray-700 bg-gray-100 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:bg-gray-200"
                                    wire:click="$set('genderFilter', 'all')" 
                                    :class="{'bg-blue-500 text-white': genderFilter === 'all'}"
                                >
                                    All
                                </button>
                                <button 
                                    class="px-4 py-2 text-sm font-medium text-center text-gray-700 bg-gray-100 border-t border-b border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:bg-gray-200"
                                    wire:click="$set('genderFilter', 'male')" 
                                    :class="{'bg-blue-500 text-white': genderFilter === 'male'}"
                                >
                                    Male
                                </button>
                                <button 
                                    class="px-4 py-2 text-sm font-medium text-center text-gray-700 bg-gray-100 border-t border-b border-gray-300 rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 hover:bg-gray-200"
                                    wire:click="$set('genderFilter', 'female')" 
                                    :class="{'bg-blue-500 text-white': genderFilter === 'female'}"
                                >
                                    Female
                                </button>
                            </div>
                        </div>

                        <form action="{{ route('score.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-lg rounded-lg p-8 w-full max-w-4xl mx-auto">
                            @csrf <!-- CSRF Token -->

                            @foreach ($participants as $index => $participant)
                                <!-- Check gender filter -->
                                @if ($genderFilter == 'all' || $participant->participant_gender == $genderFilter)
                                    <div class="flex items-start mb-6 pb-6 border-b">
                                        <!-- Profile Image Upload -->
                                        <div class="w-1/4 flex justify-center items-center">
                                            <label class="block">
                                                <span class="sr-only">Upload image</span>
                                                <div class="w-32 h-32 bg-gray-200 flex items-center justify-center rounded-lg">
                                                    <svg class="h-12 w-12 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m16-10V3a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2m3 4l3 3l4-4l5 5M13 7h6v6"/>
                                                    </svg>
                                                </div>
                                                <input type="file" class="sr-only" />
                                            </label>
                                        </div>

                                        <!-- Participant Information -->
                                        <div class="w-3/4 pl-6">
                                            <h4 class="text-xl font-semibold text-gray-700 mb-2">Group: <span class="font-medium">{{ $participant->group->group_name ?? 'No Group' }}</span></h4>
                                            <p class="text-gray-600">Gender: <span class="font-medium">{{ $participant->participant_gender }}</span></p>
                                            <p class="text-gray-800 font-medium mb-4">Name: <span class="font-medium">{{ $participant->participant_name }}</span></p>

                                            <!-- Criteria Scores -->
                                            <div class="space-y-4">
                                                @foreach ($criteria as $criterion)
                                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4">
                                                        <label class="w-full sm:w-1/3 text-gray-700 font-medium">{{ $criterion->criteria_name }}</label> 
                                                        <div class="w-full sm:w-2/3">
                                                            <input type="number" 
                                                                name="scores[{{ $participant->id }}][criteria_scores][{{ $criterion->id }}]" 
                                                                required
                                                                class="border border-gray-300 p-3 rounded-md w-full text-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"/>
                                                            <span class="text-sm text-gray-500 ml-2">/ {{ $criterion->criteria_score }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Submit Button -->
                            <div class="text-center mt-6">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-md w-full sm:w-auto">
                                    Submit Scores
                                </button>
                            </div>
                        </form>

                    </div>
                @endif
            </div>
            </div>
    </div>
</div>

