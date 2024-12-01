<div>
    @include('layouts.judge_head')

    <div class="flex items-center space-x-4">
        <div x-data="{ open: false }" class="relative">
            <div @click="open = !open" class="cursor-pointer">
                <i class="fa-solid fa-user-gear px-3 py-2 rounded-md border border-transparent hover:border-blue-500 text-white"></i>
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
        <div class="mt-4 mr-5 ml-3 mb-3">
            <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2 text-lg"></i> 
                <span>Back</span>
            </a>
        </div>

        <div class="flex justify-center items-center min-h-screen">
            <div class="p-5 bg-white rounded-lg custom-gradient-ring">
                <h2 class="pacifico-font text-3xl text-center font-bold mb-6">{{ $category->category_name }}</h2>
                <h3 class="text-xl font-semibold mb-4">Participants</h3>

                <!-- Gender Filter Tabs -->
                <div class="mb-8">
                    <div class="inline-flex justify-center rounded-md shadow-sm">
                        @foreach (['all' => 'All', 'male' => 'Male', 'female' => 'Female'] as $filter => $label)
                            <button 
                                class="px-4 py-2 text-sm font-medium border rounded-lg focus:outline-none  
                                    {{ $genderFilter === $filter ? 'bg-black text-white' : 'bg-gray-100 text-black hover:bg-black hover:text-white' }}"
                                wire:click="$set('genderFilter', '{{ $filter }}')">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Scores Form -->
                <div class="space-y-6">
                    @foreach ($participants as $participant)
                        @if ($genderFilter === 'all' || $participant->participant_gender === $genderFilter)
                            <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 border-b-2 border-blue-500 pb-4">
                                <!-- Participant Image -->
                                <div class="w-full md:w-1/4 flex items-center justify-center">
                                    <img src="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" 
                                        alt="{{ $participant->participant_name }}" 
                                        class="w-32 h-32 rounded-lg object-cover border border-gray-300 mb-4">
                                </div>

                                <!-- Participant Info and Scores -->
                                <div class="w-full md:w-3/4 pl-4">
                                    <h4 class="text-lg font-semibold">Participant No.: {{ $participant->participant_number }}</h4>
                                    <h4 class="text-lg font-semibold">Name: {{ $participant->participant_name }}</h4>
                                        @if (!empty(optional($participant->group)->group_name))
                                            <h4 class="text-lg font-semibold">Group: {{ optional($participant->group)->group_name }}</h4>
                                        @endif
                                    <p class="text-gray-600">Gender: {{ $participant->participant_gender }}</p>

                                    <div class="mt-4 space-y-4">
                                        @foreach ($criteria as $criterion)
                                            <div>
                                                <!-- Label for the Criterion -->
                                                     

                                                <!-- Check for Scoring Type -->
                                               
                                                @if ($category->event->type_of_scoring === 'points')
                                                    <div class="flex items-center justify-between">
                                                        <label class="font-bold w-full">
                                                            {{ $criterion->criteria_name }} ({{ $criterion->criteria_score }}%)
                                                        </label>
                                                        <!-- For Points Scoring -->
                                                        <input 
                                                            type="number" 
                                                            wire:model.defer="scores.{{ $participant->id }}.{{ $criterion->id }}" 
                                                            min="0" 
                                                            max="{{ $criterion->criteria_score }}" 
                                                            class="score-input p-2 ml-2 border rounded-md 
                                                                @if (session()->has('validationErrors') && collect(session('validationErrors'))->contains(function ($error) use ($participant, $criterion) {
                                                                        return str_contains($error, "Participant ID $participant->id and Criteria ID $criterion->id");
                                                                })) 
                                                                    border-red-500 
                                                                @endif"
                                                            data-max="{{ $criterion->criteria_score }}"
                                                            id="score-{{ $participant->id }}-{{ $criterion->id }}" 
                                                            pattern="^(?!0\d)\d+$"
                                                            style="text-align: right;"
                                                            title="@if (session()->has('validationErrors') && collect(session('validationErrors'))->contains(function ($error) use ($participant, $criterion) {
                                                                    return str_contains($error, "Participant ID $participant->id and Criteria ID $criterion->id");
                                                                }))
                                                                {{ collect(session('validationErrors'))->first(function ($error) use ($participant, $criterion) {
                                                                        return str_contains($error, "Participant ID $participant->id and Criteria ID $criterion->id");
                                                                }) }}
                                                            @endif"
                                                        />

                                                    </div>
                                                
                                                @elseif ($category->event->type_of_scoring === 'ranking(H-L)' || $category->event->type_of_scoring === 'ranking(L-H)')
                                                    <!-- For Ranking Scoring -->
                                                    
                                                    <div class="flex items-center justify-between">
                                                        
                                                        <label class="font-bold w-full">
                                                                {{ $criterion->criteria_name }} 
                                                        </label>
                                                        <input
                                                            type="number"
                                                            wire:model.defer="scores.{{ $participant->id }}.{{ $criterion->id }}"
                                                            min="1"
                                                            max="{{ $participants->count() }}"
                                                            class="score-input p-2 ml-2 border rounded-md 
                                                                @if (session()->has('validationErrors') && collect(session('validationErrors'))->contains(function ($error) use ($participant, $criterion) {
                                                                        return str_contains($error, "Participant ID $participant->id and Criteria ID $criterion->id");
                                                                })) 
                                                                    border-red-500 
                                                                @endif"
                                                            data-max="{{ $participants->count() }}"
                                                            id="ranking-{{ $participant->id }}-{{ $criterion->id }}"
                                                            style="text-align: right;"
                                                            title="@if (session()->has('validationErrors') && collect(session('validationErrors'))->contains(function ($error) use ($participant, $criterion) {
                                                                    return str_contains($error, "Participant ID $participant->id and Criteria ID $criterion->id");
                                                                }))
                                                                {{ collect(session('validationErrors'))->first(function ($error) use ($participant, $criterion) {
                                                                        return str_contains($error, "Participant ID $participant->id and Criteria ID $criterion->id");
                                                                }) }}
                                                            @endif"
                                                            />
                                                    </div>
                                                    
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        @endif
                    @endforeach

                    <div x-data="{ showButton: false }" @scroll.window="showButton = (window.scrollY > 100)" class="fixed bottom-8 right-8">
                        <button 
                            x-show="showButton" 
                            @click="window.scrollTo({ top: 0, behavior: 'smooth' })" 
                            class="bg-gradient-to-r from-red-500 to-orange-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg shadow-lg transition-opacity duration-500"
                            style="display: none;">
                            <i class="fa-sharp fa-solid fa-arrow-up"></i> Back to top <!-- Upward arrow symbol -->
                        </button>
                    </div>

                    <!-- Save Button -->
                    <div class="mt-4 text-center">
                        <!-- Display the button for submitting or updating scores -->
                        <button wire:click="saveScores" 
                                class="btn mt-4 
                                    @if ($isValidated) bg-blue-500 hover:bg-blue-600 @else bg-green-500 hover:bg-green-600 @endif 
                                    text-white font-semibold py-2 px-4 rounded-md">
                            <!-- Dynamically change button text based on whether scores exist or not -->
                            @if ($scoresExist)
                                Update Scores
                            @else
                                Submit Scores
                            @endif
                        </button>
                    </div>


                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <!-- Display validation errors if they exist -->
            @if (session()->has('validationErrors'))
                <div x-data="{ showErrors: true }" x-show="showErrors" class="text-white p-4 rounded-lg shadow-lg fixed inset-0 z-50 flex items-center justify-center" role="alert">
                    <div class="w-full max-w-lg sm:w-3/4 lg:w-1/2 p-4 bg-red-600 rounded-lg shadow-md">
                        <div class="flex justify-between items-center">
                            <strong class="font-semibold">Validation Errors</strong>
                            <button @click="showErrors = false" class="text-white hover:text-red-900 focus:outline-none">
                                &times;
                            </button>
                        </div>
                        <ul class="list-disc list-inside mt-2">
                            @foreach (session('validationErrors', []) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Display success message if no errors occurred -->
            @if (session()->has('success'))
                <div class="fixed inset-0 flex items-center justify-center z-50">
                    <div 
                        x-data="{ show: true }" 
                        x-show="show" 
                        x-init="setTimeout(() => show = false, 3000)" 
                        class="bg-green-500 text-white p-4 rounded-lg shadow-md flex items-center space-x-2">
                        <i class="fa-solid fa-check-circle text-xl"></i>
                        <span class="font-semibold">{{ session('success') }}</span>
                    </div>
                </div>
            @endif
            
            <!-- Info Message -->
            @if (session()->has('info'))
                <div class="bg-blue-500 text-white p-4 rounded-lg shadow-md flex items-center space-x-2 mt-4">
                    <i class="fa-solid fa-info-circle text-xl"></i>
                    <span class="font-semibold">{{ session('info') }}</span>
                </div>
            @endif

            @if (session()->has('validationErrors'))
                <div x-data="{ showErrors: true }" x-show="showErrors" class="text-white p-4 rounded-lg shadow-lg fixed inset-0 z-50 flex items-center justify-center" role="alert">
                    <div class="w-full max-w-lg sm:w-3/4 lg:w-1/2 p-4 bg-red-600 rounded-lg shadow-md">
                        <div class="flex justify-between items-center">
                            <strong class="font-semibold">Validation Errors</strong>
                            <button @click="showErrors = false" class="text-white hover:text-red-900 focus:outline-none">
                                &times;
                            </button>
                        </div>
                        <ul class="list-disc list-inside mt-2">
                            @foreach (session('validationErrors', []) as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
        @endif

</div>