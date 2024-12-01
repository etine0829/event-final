@php
    session(['selectedEvent' => $selectedEvent]);

    // Fetch the selected event from the session
    $eventToShow = App\Models\Admin\Event::find($selectedEvent);

    // Prepare an array to store participants and their total scores by category
    $categoriesWithScores = [];

    // Loop through categories and calculate scores for each participant
    foreach ($categories as $category) {
        $participantsWithScores = [];

        // Collect participants with their total scores
        foreach ($category['participants'] as $key => $participant) {
            $totalScore = 0;
            $judgeCount = count($judges);  // Number of judges

            // Loop through each judge and calculate the total score for each participant
            foreach ($judges as $judge) {
                // Loop through each criterion in the category and fetch the score for this judge and participant
                foreach ($category['criteria'] as $criteria) {
                    // Fetch the scorecard for each participant and judge
                    $scorecard = App\Models\Admin\Scorecard::where('user_id', $judge->id)
                        ->where('event_id', $selectedEvent)
                        ->where('participant_id', is_array($participant) ? $participant['id'] : $participant->id)
                        ->where('criteria_id', $criteria['id'])
                        ->first();

                    if ($scorecard) {
                        $totalScore += $scorecard->score;
                    }
                }
            }

            // Calculate average score (by dividing the total score by the number of judges)
            $averageScore = $judgeCount > 0 ? $totalScore / $judgeCount : 0;

            // Store the participant and their total score, along with their original position
            $participantsWithScores[] = [
                'participant' => $participant,
                'totalScore' => $totalScore,
                'averageScore' => $averageScore,
                'judgeCount' => $judgeCount
            ];
        }

        // Sort participants by average score in descending order (highest score first)
        usort($participantsWithScores, function($a, $b) {
            return $b['averageScore'] - $a['averageScore'];
        });

        // Assign ranks based on the sorted average scores
        $rank = 1;
        foreach ($participantsWithScores as $index => $data) {
            $participantsWithScores[$index]['participant']['rank'] = $rank++; // Rank starts from 1
        }

        // Store the category and its participants with their scores and ranks
        $categoriesWithScores[] = [
            'category' => $category,
            'participants' => $participantsWithScores
        ];
    }
@endphp


@if (Auth::user()->hasRole('admin')) 
    <div>
        <!-- Success, Info, Error Messages -->
        @if (session('success'))
            <x-sweetalert type="success" :message="session('success')" />
        @endif
        @if (session('info'))
            <x-sweetalert type="info" :message="session('info')" />
        @endif
        @if (session('error'))
            <x-sweetalert type="error" :message="session('error')" />
        @endif

        <!-- Header -->
        <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-tight text-md text-black mt-2 uppercase">Admin / Result</div>
        </div>

        <!-- Event Selection -->
        <div>
            <!-- Event Selection Dropdown -->
            <select wire:model="selectedEvent" id="event_id" name="event_id" wire:change="updateCategory"
                class="cursor-pointer text-sm shadow appearance-none border pr-16 rounded py-2 px-2 text-black leading-tight focus:outline-none focus:shadow-outline"
                required>
            <option value="">Event</option>
            @foreach($events as $event)
                <option value="{{ $event->id }}">{{ $event->event_name }}</option>
            @endforeach
            </select>
            <p class="text-black mt-2 text-sm mb-4">
                Selected Event: 
                @if($eventToShow && $eventToShow->event_name)
                    <text class="uppercase text-red-500">{{ $eventToShow->event_name }}</text>
                @else
                    <span class="text-gray-500">No event selected</span>
                @endif
            </p>
            @if($eventToShow) <!-- Ensure the event data is valid -->
                <div class="mt-4">
                    <strong>Scoring Type: </strong>
                    @if($eventToShow->type_of_scoring == 'ranking(H-L)')
                        By Ranking: Highest is the winner
                    @elseif($eventToShow->type_of_scoring == 'ranking(L-H)')
                        By Ranking: Lowest is the winner
                    @else
                        By Points
                    @endif
                </div>
            @endif

            <!-- Check if categories are available for the selected event -->
            @if(empty($categories) || count($categories) == 0)
                @if($selectedEvent && isset($selectedEvent->event_name))
                    <p class="text-center text-red-500 font-semibold mt-4">No results for this event: {{ $selectedEvent->event_name }}</p>
                @endif
            @else
                <hr class="border-gray-200 my-4">   

                <!-- Loop through categories and display results -->
                @foreach($categoriesWithScores as $categoryData)
                    <div class="mb-8">
                        <h3 class="font-bold text-lg mb-4">{{ $categoryData['category']['name'] }} ({{ $categoryData['category']['category_score'] }}%)</h3>
                        <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                            <thead class="bg-slate-300 text-black">
                                <tr>
                                    <th class="border border-blue-400 px-3 py-2">Participant No.</th>
                                    <th class="border border-blue-400 px-3 py-2">Participant</th>
                                    @foreach ($judges as $judge)
                                        <th class="border border-blue-400 px-3 py-2">Judge: {{ $judge->name }}</th>
                                    @endforeach
                                    <th class="border border-blue-400 px-3 py-2">Average Score</th>
                                    <th class="border border-blue-400 px-3 py-2">Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Loop through participants and their scores for the current category -->
                                @foreach($categoryData['participants'] as $participantData)
                                    <tr class="hover:bg-gray-100">
                                        <!-- Display Participant Number -->
                                        <td class="text-black border border-blue-400">
                                            @php
                                                $participantNumber = is_array($participantData['participant']) ? $participantData['participant']['participant_no'] : $participantData['participant']->participant_number;
                                            @endphp
                                            {{ $participantNumber ?? 'N/A' }}
                                        </td>

                                        <!-- Display Participant Name -->
                                        <td class="text-black border border-blue-400">
                                            @php
                                                $participantName = is_array($participantData['participant']) ? $participantData['participant']['name'] : $participantData['participant']->participant_name;
                                            @endphp
                                            {{ $participantName ?? 'N/A' }}
                                        </td>

                                        <!-- Loop through judges and display their scores -->
                                        @foreach ($judges as $judge)
                                            <td class="text-black border border-blue-400">
                                                @php
                                                    $totalScore = 0;
                                                    $criteriaCount = 0;

                                                    foreach ($categoryData['category']['criteria'] as $criteria) {
                                                        $scorecard = App\Models\Admin\Scorecard::where('user_id', $judge->id)
                                                            ->where('event_id', $selectedEvent)
                                                            ->where('participant_id', is_array($participantData['participant']) ? $participantData['participant']['id'] : $participantData['participant']->id)
                                                            ->where('criteria_id', $criteria['id'])
                                                            ->first();

                                                        if ($scorecard) {
                                                            $totalScore += $scorecard->score;
                                                            $criteriaCount++;
                                                        }
                                                    }

                                                    // Check if event has a type_of_scoring == 'points'
                                                    if ($eventToShow->type_of_scoring == 'points') {
                                                        // Calculate average score based on points
                                                        $averageScore = $criteriaCount > 0 ? $totalScore / $criteriaCount : 0;
                                                    } elseif ($eventToShow->type_of_scoring == 'ranking(H-L)' || $eventToShow->type_of_scoring == 'ranking(L-H)') {
                                                        // For ranking types (H-L or L-H), average score is just the total score
                                                        $averageScore = $totalScore > 0 ? $totalScore : 0;
                                                    } else {
                                                        $averageScore = 0; // Fallback for any undefined scoring types
                                                    }
                                                @endphp
                                                {{ $averageScore }}
                                            </td>
                                        @endforeach

                                        <!-- Display Deduction and Rank -->
                                        <td class="text-black border border-blue-400">{{ $participantData['averageScore']    }}</td>
                                        <td class="text-black border border-blue-400">{{ $participantData['participant']['rank'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endif
