@if (Auth::user()->hasRole('judge'))
    <div>
        @if (session('success'))
            <x-sweetalert type="success" :message="session('success')" />
        @endif
        @if (session('info'))
            <x-sweetalert type="info" :message="session('info')" />
        @endif
        @if (session('error'))
            <x-sweetalert type="error" :message="session('error')" />
        @endif
       
        <div class="flex flex-col items-center">
            
            <div class="text-4xl font-bold text-center my-4">
                @forelse ($events as $event)
                    <p style="font-family: 'Lucida Handwriting'">{{ $event->event_name }}</p>
                @empty
                    <p>No events available.</p>
                @endforelse
            </div>
            
            <div x-data="{ open: false, selectedGender: 'all' }">
                <div class="grid gap-4 w-full justify-center p-2">
                    @foreach($categories as $index => $category)
                        <div 
                            class="w-full sm:w-[400px] md:w-[400px] lg:w-[500px] p-6 rounded-md text-white text-center cursor-pointer 
                                {{ ['bg-yellow-600', 'bg-green-600', 'bg-blue-600', 'bg-red-600'][$index % 4] }} 
                                hover:{{ ['bg-yellow-700', 'bg-green-700', 'bg-blue-700', 'bg-red-700'][$index % 4] }}"
                            wire:click="loadCategoryDetails({{ $category->id }})"
                            @click="open = true"
                            wire:key="category-{{ $category->id }}">
                            <div class="font-bold text-lg uppercase">{{ $category->category_name }} <span>({{ $category->score }} points)</span></div>
                        </div>
                    @endforeach

                    <!-- My Scores Button -->
                    <div class="w-full sm:w-[300px] md:w-[400px] lg:w-[500px] p-6 rounded-md text-white text-center cursor-pointer 
                            bg-yellow-600 hover:bg-yellow-700">
                        <div class="font-bold text-lg">My Scores</div>
                    </div>
                </div>

                <!-- Category Details Modal -->
                <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div class="bg-white p-6 rounded-lg shadow-lg max-w-2xl w-full h-[700px] overflow-y-auto">
                        <div class="pb-3 flex justify-between items-center">
                            <h2 class="text-xl font-bold">
                                {{ $selectedCategory->category_name ?? 'No category selected' }}
                            </h2>
                            <button @click="open = false" class="text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                        </div>

                        <!-- Gender Tabs -->
                        <div class="mt-4 flex justify-center space-x-4">
                            @foreach (['all' => 'All', 'male' => 'Male', 'female' => 'Female'] as $genderKey => $genderLabel)
                                <button @click="selectedGender = '{{ $genderKey }}'" 
                                    :class="{ 'bg-gray-300': selectedGender === '{{ $genderKey }}' }" 
                                    class="px-4 py-2">{{ $genderLabel }}</button>
                            @endforeach
                        </div>

                        <!-- Participant List -->
                        <div class="space-y-4 mt-4">
                            @foreach($participants as $participant)
                                <template x-if="selectedGender === 'all' || selectedGender === '{{ $participant->participant_gender }}'">
                                    <div class="flex items-start space-x-4 border-b pb-4">
                                        <div class="w-24 h-24 bg-gray-300 flex items-center justify-center rounded">
                                            <img src="{{ $participant->participant_photo }}" alt="Photo" class="w-full h-full object-cover rounded" />
                                        </div>
                                        <div class="flex-1">
                                            <p>Name: {{ $participant->participant_name }}</p>
                                            <p>Group: {{ $participant->group->group_name ?? 'No Group' }}</p>
                                            @if($criteria->isEmpty())
                                                <p class="text-gray-600 mt-2">No criteria associated with this category.</p>
                                            @else
                                                @foreach($criteria as $criterion)
                                                    <div class="flex items-center space-x-2 mt-2">
                                                        <label class="text-gray-600">{{ $criterion->criteria_name }}:</label>
                                                        <input type="number" placeholder="Score" class="border rounded p-1 w-20" required />
                                                        <label class="text-gray-600">/{{ $criterion->criteria_score }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </template>
                            @endforeach
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4 text-right">
                            <button @click="open = false" class="bg-blue-500 text-white px-4 py-2 rounded">SUBMIT</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
