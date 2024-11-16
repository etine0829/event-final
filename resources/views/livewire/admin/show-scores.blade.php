<div>
    <h1 class="text-2xl font-bold mb-4">Editable Scores</h1>

    @forelse ($categories as $category)
        <div class="mb-6 border p-4 rounded shadow">
            <h2 class="text-xl font-semibold mb-2">{{ $category['name'] }}</h2>
            <table class="table-auto border-collapse border border-gray-400 w-full mt-2">
                <thead>
                    <tr>
                        <th class="border border-gray-400 px-4 py-2">Participant Name</th>
                        @foreach ($category['criteria'] as $criteria)
                            <th class="border border-gray-400 px-4 py-2">{{ $criteria['name'] }}</th>
                        @endforeach
                        <th class="border border-gray-400 px-4 py-2">Average Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($category['participants'] as $participant)
                        <tr>
                            <td class="border border-gray-400 px-4 py-2">{{ $participant['name'] }}</td>
                            @foreach ($category['criteria'] as $criteria)
                                <td class="border border-gray-400 px-4 py-2">
                                    <input 
                                        type="number" 
                                        class="border px-2 py-1 w-full" 
                                        wire:model.defer="scores.{{ $category['id'] }}.{{ $participant['id'] }}.{{ $criteria['id'] }}"
                                    />
                                </td>
                            @endforeach
                            <td class="border border-gray-400 px-4 py-2">{{ $participant['avg_score'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <button 
                class="bg-green-500 text-white px-4 py-2 rounded mt-4 hover:bg-green-600"
                wire:click="updateScores({{ $category['id'] }})"
            >
                Save Changes for {{ $category['name'] }}
            </button>
        </div>
    @empty
        <p>No categories available for this event.</p>
    @endforelse

    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-3 rounded mt-4">
            {{ session('success') }}
        </div>
    @endif
</div>
