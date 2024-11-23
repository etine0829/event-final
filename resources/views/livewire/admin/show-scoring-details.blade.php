<div>
    @include('layouts.judge_head')

    @if (session('success'))
        <x-sweetalert type="success" :message="session('success')" />
    @endif

    @if (session('info'))
        <x-sweetalert type="info" :message="session('info')" />
    @endif

    @if (session('error'))
        <x-sweetalert type="error" :message="session('error')" />
    @endif

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
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border focus:outline-none hover:bg-gray-200 {{ $genderFilter === $filter ? 'bg-blue-500 text-white' : '' }}"
                                wire:click="$set('genderFilter', '{{ $filter }}')">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Scores Form -->
                <div class="space-y-6">
                    @foreach ($participants as $index => $participant)
                        @if ($genderFilter === 'all' || $participant->participant_gender === $genderFilter)
                            <div class="flex flex-col md:flex-row items-start space-y-4 md:space-y-0 border-b-2 border-blue-500 pb-4">
                                <!-- Participant Image -->
                                <div class="w-full md:w-1/4 flex items-center justify-center">
                                    <img src="{{ asset('storage/participant_photo/' . $participant->participant_photo) }}" 
                                         alt="{{ $participant->participant_name }}" 
                                         class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
                                </div>

                                <!-- Participant Info and Scores -->
                                <div class="w-full md:w-3/4 pl-4">
                                    <h4 class="text-lg font-semibold">Participant No. {{ $index + 1 }}: {{ $participant->participant_name }}</h4>
                                    <p class="text-gray-600">Gender: {{ $participant->participant_gender }}</p>

                                    <div class="mt-4 space-y-4">
                                        @foreach ($criteria as $criterion)
                                            <div class="flex items-center">
                                                <label class="w-1/2 font-bold">{{ $criterion->criteria_name }} ({{ $criterion->criteria_score }}%)</label>
                                                <input 
                                                    type="number" 
                                                    wire:model.defer="scores.{{ $participant->id }}.{{ $criterion->id }}" 
                                                    min="0" 
                                                    max="{{ $criterion->criteria_score }}" 
                                                    class="score-input p-2 border rounded-md @error('scores.' . $participant->id . '.' . $criterion->id) border-red-500 @enderror"
                                                    data-max="{{ $criterion->criteria_score }}"
                                                    id="score-{{ $participant->id }}-{{ $criterion->id }}"
                                                />
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Save Button -->
                    <div class="text-right mt-4">
                        <button wire:click="saveScores" class="bg-green-500 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-md">
                            Save Scores
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    document.querySelectorAll('.score-input').forEach(function(input) {
        input.addEventListener('input', function() {
            const maxValue = parseInt(input.getAttribute('data-max'));
            const value = parseInt(input.value);
            
            if (value > maxValue) {
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });
    });
</script>
