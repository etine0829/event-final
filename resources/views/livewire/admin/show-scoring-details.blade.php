<div>
    @include('layouts.judge_head')

    <!-- User Dropdown Menu -->
    <div class="flex items-center space-x-4">
        <div x-data="{ open: false }" class="relative">
            <div @click="open = !open" class="cursor-pointer">
                <i class="fa-solid fa-user-gear px-3 py-2 rounded-md border border-transparent hover:border-blue-500 text-white"></i>
            </div>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20">
                <a href="#" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
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
            <!-- Back Button -->
            <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 mb-4 mt-4">
                <i class="fas fa-arrow-left mr-2 text-lg"></i>
                <span>Back</span>
            </a>

            <!-- Content Wrapper -->
            <div class="flex justify-center items-center min-h-screen">
                <div class="p-5 bg-white rounded-lg custom-gradient-ring">
                    <!-- Category Title -->
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

                    <!-- Ranking Table or Alternate Layout -->
                    @if ($category->event->type_of_scoring === 'ranking(H-L)' || $category->event->type_of_scoring === 'ranking(L-H)')
                    <div class="mt-8 bg-gray-100 p-6 rounded-lg">
                        <!-- Container for ranking table grouped by criteria -->
                        <div class="overflow-x-auto">
                            <div class="overflow-y-auto max-h-[400px]"> <!-- Set max height and enable vertical scrolling -->

                                <!-- Loop through each criterion -->
                                @foreach ($criteria as $criterion)
                                    <div class="mb-6">
                                        <!-- Criterion Header -->
                                        <h2 class="text-2xl font-bold bg-gray-200 p-4 rounded-t-lg">{{ $criterion->criteria_name }}</h2>
                                        
                                        <!-- Table for Participants under this criterion -->
                                        <div class="overflow-x-auto">
                                            <table class="table-auto min-w-full border-collapse border border-gray-300">
                                                <thead class="bg-gray-300">
                                                    <tr class="hidden sm:table-row">
                                                        <th class="border border-gray-400 px-4 py-2 text-center font-bold">Photo</th>
                                                        <th class="border border-gray-400 px-4 py-2 text-center font-bold">Participant Number</th>
                                                        <th class="border border-gray-400 px-4 py-2 text-center font-bold">Participant Name</th>
                                                        <th class="border border-gray-400 px-4 py-2 text-center font-bold">Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Loop through each participant -->
                                                    @forelse ($participants as $participant)
                                                        @if ($genderFilter === 'all' || $participant->participant_gender === $genderFilter)
                                                            <tr class="hover:bg-gray-200 sm:table-row">
                                                                <!-- Participant Photo -->
                                                                <td class="border border-gray-400 px-4 py-2 text-center">
                                                                    <img 
                                                                        src="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" 
                                                                        alt="{{ $participant->participant_name }}" 
                                                                        class="w-12 h-12 sm:w-16 sm:h-16 md:w-20 md:h-20 rounded-lg object-cover border border-gray-300"
                                                                    />
                                                                </td>

                                                                <!-- Participant Number -->
                                                                <td class="border border-gray-400 px-4 py-2 text-center">
                                                                    {{ $participant->participant_number }}
                                                                </td>

                                                                <!-- Participant Name -->
                                                                <td class="border border-gray-400 px-4 py-2 text-center">
                                                                    {{ $participant->participant_name }}
                                                                </td>

                                                                <!-- Score for this criterion -->
                                                                <td class="border border-gray-400 px-4 py-2 text-center">
                                                                    <input 
                                                                        required
                                                                        type="number" 
                                                                        wire:model.defer="scores.{{ $participant->id }}.{{ $criterion->id }}" 
                                                                        min="1" 
                                                                        max="{{ $participants->count() }}" 
                                                                        class="w-full p-2 border rounded-md text-center 
                                                                            @error('scores.' . $participant->id . '.' . $criterion->id) border-red-500 @enderror"
                                                                    />
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="border border-gray-400 px-4 py-2 text-center text-gray-500">
                                                                No participants available.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div> 
                                @endforeach
                            </div>
                        </div>
                    </div>


                    @elseif ($category->event->type_of_scoring === 'points')
                        @foreach ($participants as $participant)
                            <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 border-b-2 border-blue-500 pb-4 mt-4">
                                <!-- Participant Image -->
                                <div class="w-full md:w-1/4 flex items-center justify-center">
                                    <img 
                                        src="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" 
                                        alt="{{ $participant->participant_name }}" 
                                        class="w-32 h-32 rounded-lg object-cover border border-gray-300 mb-4"
                                    >
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
                                                <div class="flex items-center justify-between">
                                                    <label class="font-bold w-full">
                                                        {{ $criterion->criteria_name }} ({{ $criterion->criteria_score }}%)
                                                    </label>
                                                    <!-- Points Scoring -->
                                                    <input 
                                                        required
                                                        type="number" 
                                                        wire:model.defer="scores.{{ $participant->id }}.{{ $criterion->id }}" 
                                                        min="0" 
                                                        max="{{ $criterion->criteria_score }}" 
                                                        class="score-input p-2 ml-2 border rounded-md 
                                                            @error('scores.' . $participant->id . '.' . $criterion->id) border-red-500 @enderror"
                                                    />
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

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
                        <button wire:click="saveScores" 
                                class="btn mt-4 
                                    @if ($isValidated) bg-blue-500 hover:bg-blue-600 @else bg-green-500 hover:bg-green-600 @endif 
                                    text-white font-semibold py-2 px-4 rounded-md">
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

                        <div class="mt-2 space-y-2">
                            @foreach (session('validationErrors', []) as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
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