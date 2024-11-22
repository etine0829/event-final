<div>
    @include('layouts.judge_head')

    <div>

        <div class="mt-16 mr-5 ml-3 mb-3">
            <a href="{{ route('judge.dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-2 text-lg"></i> 
                <span>Back</span>
            </a>
        </div>

        @forelse ($categories as $category)
            <div class="mb-6 border p-4 rounded shadow">
                <h2 class="text-xl font-semibold mb-2">{{ $category['name'] }}</h2>
                <div class="overflow-x-auto"> <!-- Add this div to make the table scrollable on small screens -->
                    <table class="table-auto border-collapse border border-gray-400 w-full mt-2">
                        <thead>
                            <tr>
                                <th class="border border-gray-400 px-4 py-2">Participant Name</th>
                                @foreach ($category['criteria'] as $criteria)
                                    <th class="border border-gray-400 px-4 py-2">{{ $criteria['name'] }}</th>
                                @endforeach
                                <th class="border border-gray-400 px-4 py-2">Average Score</th>
                                <th class="border border-gray-400 px-4 py-2">Rank</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($category['participants'] as $participant)
                                <tr>
                                    <td class="border border-gray-400 px-4 py-2">{{ $participant['name'] }}</td>
                                    @foreach ($category['criteria'] as $criteria)
                                        <td class="border border-gray-400 px-4 py-2">
                                            <span>{{ $participant['scores'][$criteria['id']] ?? 'N/A' }}</span>
                                        </td>
                                    @endforeach
                                    <td class="border border-gray-400 px-4 py-2">{{ $participant['avg_score'] }}</td>
                                    <td class="border border-gray-400 px-4 py-2">{{ $participant['rank'] ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

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
</div>
