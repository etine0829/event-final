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
        <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900">
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

                <!-- Ranking Table -->
                @if ($category->event->type_of_scoring === 'ranking(H-L)' || $category->event->type_of_scoring === 'ranking(L-H)')
                    <div class="mt-8 bg-gray-100 p-6 rounded-lg">
                        <!-- Single Table for Ranking -->
                        <table class="table-auto w-full border-collapse border border-gray-300">
                            <thead class="bg-gray-300">
                                <tr>
                                    <th class="border border-gray-400 px-4 py-2 text-center font-bold">Photo</th>
                                    <th class="border border-gray-400 px-4 py-2 text-center font-bold">Participant Number</th>
                                    <th class="border border-gray-400 px-4 py-2 text-center font-bold">Participant Name</th>
                                    @foreach ($criteria as $criterion)
                                        <th class="border border-gray-400 px-4 py-2 text-center font-bold">{{ $criterion->criteria_name }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($participants as $participant)
                                    @if ($genderFilter === 'all' || $participant->participant_gender === $genderFilter)
                                        <tr>
                                            <!-- Participant Photo -->
                                            <td class="border border-gray-400 px-4 py-2 text-center">
                                                <img 
                                                    src="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" 
                                                    alt="{{ $participant->participant_name }}" 
                                                    class="w-16 h-16 rounded-lg object-cover border border-gray-300"
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

                                            <!-- Criteria Scores -->
                                            @foreach ($criteria as $criterion)
                                                <td class="border border-gray-400 px-4 py-2 text-center">
                                                    <input 
                                                        type="number" 
                                                        wire:model.defer="scores.{{ $participant->id }}.{{ $criterion->id }}" 
                                                        min="1" 
                                                        max="{{ $participants->count() }}" 
                                                        class="w-full p-2 border rounded-md text-center"
                                                    />
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + count($criteria) }}" class="border border-gray-400 px-4 py-2 text-center text-gray-500">
                                            No participants available.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                        <!-- Submit Scores Button -->
                        <div class="mt-6 text-center">
                            <button 
                                wire:click="saveScores" 
                                class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
                                Submit Scores
                            </button>
                        </div>
                    </div>
                @endif
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