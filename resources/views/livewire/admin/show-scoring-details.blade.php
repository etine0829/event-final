<div class="">
    @include('layouts.judge_head')

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

    @if (Auth::user()->hasRole('judge'))
        <!-- Back Button -->
        <div class="mt-4 mr-5 ml-3 mb-3">
            <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2 text-lg"></i> 
                <span>Back</span>
            </a>
        </div>

        <!-- Centered Content -->
        <div class="flex justify-center items-center min-h-screen ">
            <div class=" relative p-[5px] bg-white rounded-lg custom-gradient-ring">
                <h2 class=" pacifico-font text-3xl text-center font-bold mb-6">{{ $category->category_name }}</h2>
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

                <!-- Form for Storing Scores -->
                 
                <form action="{{ route('score.store') }}" method="POST" enctype="multipart/form-data" id="scoreForm">
                    @csrf
                    <input type="hidden" name="category_id" value="{{ $category->id }}">

                    <div class="space-y-6 ">
                        @foreach ($participants as $index => $participant)
                            @if ($genderFilter == 'all' || $participant->participant_gender == $genderFilter)
                                <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 border-b-2 border-blue-500 pb-4">
                                    <!-- Participant Image -->
                                    <div class="w-full md:w-1/4 flex items-center justify-center overflow-hidden">
                                        <img src="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" 
                                            alt="{{ $participant->participant_name }}" 
                                            class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
                                    </div>

                                    <!-- Participant Info and Score Inputs -->
                                    <div class="w-full md:w-3/4 pl-4">
                                        <h4 class="text-lg">Participant No. {{ $index + 1 }}: <span class="font-semibold "> {{ $participant->participant_name }}</span></h4>
                                        <p class="text-gray-600">Gender: {{ $participant->participant_gender }}</p>

                                        <div class="mt-4 space-y-4 ">
                                            @foreach ($criteria as $criterion)
                                                <div class="flex items-center space-x-7">
                                                    <label class="w-1/2 mr-5"> <span class="font-bold"> {{ $criterion->criteria_name }}</span> {{ $criterion->criteria_score }}%</label> 
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
                                                    <!-- Error message container for when the value exceeds the max score -->
                                                    <span class="text-red-500 text-sm hidden" data-warning></span>
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

<script>
    function validateMaxValue(input) {
        const maxScore = parseFloat(input.getAttribute('data-max'));
        const value = parseFloat(input.value);
        const warningMessage = input.closest('div').querySelector('[data-warning]');
        
        if (value > maxScore) {
            warningMessage.textContent = `Score cannot exceed ${maxScore}.`;
            warningMessage.classList.remove('hidden');
            input.classList.add('border-red-500'); // Add red border to indicate error
        } else {
            warningMessage.classList.add('hidden');
            input.classList.remove('border-red-500');
        }
    }
</script>
