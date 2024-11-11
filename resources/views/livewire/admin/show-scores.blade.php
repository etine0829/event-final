<div>
    @foreach($categories as $category)
        <h2 class="text-lg font-bold mb-4">{{ $category['name'] }}</h2>
        
        <table class="table-auto w-full border mb-8">
            <thead>
                <tr>
                    <th class="border px-4 py-2">Participant Name</th>
                    @foreach($category['criteria'] as $criteria)
                        <th class="border px-4 py-2">{{ $criteria['name'] }}</th>
                    @endforeach
                    <th class="border px-4 py-2">Average Score</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category['criteria'][0]['participants'] ?? [] as $index => $participant)
                    <tr>
                        <td class="border px-4 py-2">{{ $participant['name'] }}</td>
                        @foreach($category['criteria'] as $criteria)
                            <td class="border px-4 py-2">
                                {{ $criteria['participants'][$index]['score'] ?? 'N/A' }}
                            </td>
                        @endforeach
                        <td class="border px-4 py-2">{{ $participant['avg_score'] ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
