<div>
    @if (Auth::user()->hasRole('judge'))
        <div class="container mx-auto p-4">
            <!-- Back Button -->
            <div class="mb-4">
                <a href="{{ route('judge.dashboard')}}" >
                <button class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-md">
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

            <form action="{{ route('score.store') }}" method="POST" enctype="multipart/form-data">
                @csrf <!-- CSRF Token -->
                @foreach ($participants as $index => $participant)
                    <!-- Check gender filter -->
                    @if ($genderFilter == 'all' || $participant->participant_gender == $genderFilter)
                        <div class="flex items-start mb-6 border-b pb-4">
                            <div class="w-1/4 flex items-center justify-center">
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

                            <div class="w-3/4 pl-4">
                                <h4 class="text-lg font-semibold">Group: {{ $participant->group->group_name ?? 'No Group' }} </h4>
                                <p class="text-gray-600">Gender: {{ $participant->participant_gender }}</p>
                                <p class="text-gray-800 font-medium">Name: {{ $participant->participant_name }}</p>

                                <div class="mt-4">
                                    @foreach ($criteria as $criterion)
                                        <div class="flex items-center mb-2">
                                            <label class="w-1/2 text-gray-700">{{ $criterion->criteria_name }}</label> 
                                            <input type="number" name="scores[{{ $participant->id }}][criteria_scores][{{ $criterion->id }}]" required />
                                            <input type="hidden" name="category_id" value="{{ $category->id }}">
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
            </form>
        </div>
    @endif
</div>


