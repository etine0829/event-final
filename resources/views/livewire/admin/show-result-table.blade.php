@php
    session(['selectedEvent' => $selectedEvent]);


    // Fetch the selected event from the session
    $eventToShow = App\Models\Admin\Event::find($selectedEvent);


    // Prepare an array to store participants and their total scores by category
    $categoriesWithScores = [];


    // Loop through categories and calculate scores for each participant
    foreach ($categories as $category) {
        $participantsWithScores = [];


        // Check if 'participants' key exists in the category
        if (isset($category['participants']) && is_array($category['participants'])) {
            // Collect participants with their total scores
            foreach ($category['participants'] as $key => $participant) {
                $totalScore = 0;
                $judgeCount = count($judges);  // Number of judges
                $sumOfAverageScores = 0; // Variable to accumulate the sum of average scores from all judges


                // Loop through each judge and calculate the total score for each participant
                foreach ($judges as $judge) {
                    $averageScore = 0;


                    // Loop through each criterion in the category and fetch the score for this judge and participant
                    foreach ($category['criteria'] as $criteria) {
                        // Fetch the scorecard for each participant and judge
                        $scorecard = App\Models\Admin\Scorecard::where('user_id', $judge->id)
                            ->where('event_id', $selectedEvent)
                            ->where('participant_id', is_array($participant) ? $participant['id'] : $participant->id)
                            ->where('criteria_id', $criteria['id'])
                            ->first();


                        if ($scorecard) {
                            // Calculate the score for the judge and criterion
                            $score = $scorecard->score;


                            // If type_of_scoring is 'points', adjust by category score
                            if ($eventToShow->type_of_scoring == 'points') {
                                $averageScore += $score * $category['category_score'] / 100;
                            } else {
                                // Ranking-based scoring (H-L or L-H)
                                $averageScore += $score;
                            }
                        }
                    }


                    // Add the average score for this judge to the sum of average scores
                    $sumOfAverageScores += $averageScore;
                }


                // Calculate the total average score by dividing the sum of average scores by the number of judges
                if ($judgeCount > 0) {
                    $totalAverageScore = $sumOfAverageScores / $judgeCount;
                } else {
                    $totalAverageScore = 0;
                }


                // Round the total average score to 2 decimal places
                $totalAverageScore = round($totalAverageScore, 2);


                // Store the participant and their total score, along with their total average score
                $participantsWithScores[] = [
                    'participant' => $participant,
                    'totalScore' => $totalScore,
                    'totalAverageScore' => $totalAverageScore,
                    'judgeCount' => $judgeCount
                ];
            }          
               
            // Add the category with the participants and their calculated scores
            $categoriesWithScores[] = [
                'category' => $category,
                'participants' => $participantsWithScores
            ];
        }
    }
@endphp


<div>
    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('event_manager'))
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
            <div class="flex justify-between mb-4 sm:-mt-4" id="resultsSection">
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
                                <thead class="bg-blue-300 text-black">
                                    <tr>
                                        <th class="border border-blue-400 px-3 py-2">Participant No.</th>
                                        <th class="border border-blue-400 px-3 py-2">Participant</th>
                                        @foreach ($judges as $judge)
                                            <th class="border border-blue-400 px-3 py-2">Judge: {{ $judge->name }}</th>
                                        @endforeach
                                        <th class="border border-blue-400 px-3 py-2">Total Average Score</th>
                                        <th class="border border-blue-400 px-3 py-2">Rank</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    // Sort by Total Average Score to determine ranks
                                    usort($categoryData['participants'], function ($a, $b) {
                                        return $b['totalAverageScore'] <=> $a['totalAverageScore']; // Descending order by score
                                    });

                                    // Assign ranks based on Total Average Score
                                    foreach ($categoryData['participants'] as $index => &$participant) {
                                        $participant['rank'] = $index + 1;
                                    }

                                    // Sort by Participant Number for display purposes
                                    usort($categoryData['participants'], function ($a, $b) {
                                        $participantNoA = is_array($a['participant']) ? $a['participant']['participant_no'] : $a['participant']->participant_number;
                                        $participantNoB = is_array($b['participant']) ? $b['participant']['participant_no'] : $b['participant']->participant_number;
                                        return $participantNoA <=> $participantNoB;
                                    });
                                    @endphp

                                    <!-- Loop through participants and display their data -->
                                    @foreach($categoryData['participants'] as $participantData)
                                        @php
                                            $rank = $participantData['rank'] ?? 'N/A';

                                            // Determine row color based on rank
                                            $rowColor = '';
                                            if ($rank == 1) {
                                                $rowColor = 'bg-yellow-300'; // Gold for 1st place
                                            } elseif ($rank == 2) {
                                                $rowColor = 'bg-gray-300'; // Silver for 2nd place
                                            } elseif ($rank == 3) {
                                                $rowColor = 'bg-orange-300'; // Bronze for 3rd place
                                            }
                                        @endphp

                                        <tr class="hover:bg-gray-100 {{ $rowColor }}">
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

                                            <!-- Display Scores by Judges -->
                                            @foreach ($judges as $judge)
                                                <td class="text-black border border-blue-400">
                                                    @php
                                                        $totalScore = 0;

                                                        foreach ($categoryData['category']['criteria'] as $criteria) {
                                                            $scorecard = App\Models\Admin\Scorecard::where('user_id', $judge->id)
                                                                ->where('event_id', $selectedEvent)
                                                                ->where('participant_id', is_array($participantData['participant']) ? $participantData['participant']['id'] : $participantData['participant']->id)
                                                                ->where('criteria_id', $criteria['id'])
                                                                ->first();

                                                            if ($scorecard) {
                                                                $totalScore += $scorecard->score;
                                                            }
                                                        }

                                                        // Calculate average score
                                                        if ($eventToShow->type_of_scoring == 'points') {
                                                            $averageScore = $totalScore * $categoryData['category']['category_score'] / 100;
                                                        } elseif ($eventToShow->type_of_scoring == 'ranking(H-L)' || $eventToShow->type_of_scoring == 'ranking(L-H)') {
                                                            $averageScore = $totalScore > 0 ? $totalScore : 0;
                                                        } else {
                                                            $averageScore = 0; // Fallback for undefined scoring types
                                                        }
                                                    @endphp
                                                    {{ $averageScore }}
                                                </td>
                                            @endforeach

                                            <!-- Display Total Average Score -->
                                            <td class="text-black border border-blue-400">{{ $participantData['totalAverageScore'] }}</td>

                                            <!-- Display Rank -->
                                            <td class="text-black border border-blue-400">{{ $rank }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach



                    <div class="mb-8">
                        <h3 class="font-bold text-lg mb-4">Overall Rank</h3>
                        <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                            <thead class="bg-blue-300 text-black">
                                <tr>
                                    <th class="border border-blue-400 px-3 py-2">Participant No.</th>
                                    <th class="border border-blue-400 px-3 py-2">Participant</th>

                                    <!-- Add a column for each category -->
                                    @foreach ($categoriesWithScores as $categoryData)
                                        <th class="border border-blue-400 px-3 py-2">{{ $categoryData['category']['name'] }}</th>
                                    @endforeach

                                    <th class="border border-blue-400 px-3 py-2">Total Score</th>
                                    <th class="border border-blue-400 px-3 py-2">Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                // Step 1: Calculate the total score for each participant
                                $participantsWithScores = [];

                                foreach ($categoriesWithScores[0]['participants'] as $participantData) {
                                    $totalScore = 0;
                                    $participantId = is_array($participantData['participant'])
                                        ? $participantData['participant']['id']
                                        : $participantData['participant']->id;

                                    // Calculate total score for each participant
                                    foreach ($categoriesWithScores as $categoryData) {
                                        foreach ($categoryData['participants'] as $categoryParticipantData) {
                                            if ($categoryParticipantData['participant']['id'] === $participantId) {
                                                $categoryScore = $categoryParticipantData['totalAverageScore'];
                                                $totalScore += $categoryScore ?? 0; // Accumulate the total score
                                            }
                                        }
                                    }

                                    // Add participant data and their total score to the array
                                    $participantsWithScores[] = [
                                        'participant' => $participantData['participant'],
                                        'totalScore' => $totalScore
                                    ];
                                }

                                // Step 2: Sort participants by ID in ascending order
                                usort($participantsWithScores, function ($a, $b) {
                                    $idA = is_array($a['participant']) ? $a['participant']['id'] : $a['participant']->id;
                                    $idB = is_array($b['participant']) ? $b['participant']['id'] : $b['participant']->id;
                                    return $idA <=> $idB; // Sort by ID in ascending order
                                });

                                // Step 3: Assign ranks based on total scores
                                $rankedParticipants = $participantsWithScores;

                                if ($eventToShow->type_of_scoring == 'ranking(L-H)') {
                                    // Sort by total score (ascending) for 'ranking(L-H)'
                                    usort($rankedParticipants, function ($a, $b) {
                                        return bccomp($a['totalScore'], $b['totalScore'], 6); // 6 decimal places of precision
                                    });
                                } else {
                                    // Sort by total score (descending) for other scoring types
                                    usort($rankedParticipants, function ($a, $b) {
                                        return bccomp($b['totalScore'], $a['totalScore'], 6); // 6 decimal places of precision
                                    });
                                }

                                // Map ranks back to the original participant order by ID
                                $participantRanks = [];
                                foreach ($rankedParticipants as $index => $rankedParticipant) {
                                    $id = is_array($rankedParticipant['participant']) ? $rankedParticipant['participant']['id'] : $rankedParticipant['participant']->id;
                                    $participantRanks[$id] = $index + 1;
                                }
                            @endphp

                            <!-- Loop through all participants and display their data -->
                            @foreach($participantsWithScores as $participantWithScore)
                                @php
                                    $participantId = is_array($participantWithScore['participant']) 
                                        ? $participantWithScore['participant']['id'] 
                                        : $participantWithScore['participant']->id;
                                    $rank = $participantRanks[$participantId] ?? 'N/A'; // Get the rank for this participant

                                    // Determine row color based on rank
                                    $rowColor = '';
                                    if ($rank == 1) {
                                        $rowColor = 'bg-yellow-300'; // Gold for 1st place
                                    } elseif ($rank == 2) {
                                        $rowColor = 'bg-gray-300'; // Silver for 2nd place
                                    } elseif ($rank == 3) {
                                        $rowColor = 'bg-orange-300'; // Bronze for 3rd place
                                    }
                                @endphp

                                <tr class="hover:bg-gray-100 {{ $rowColor }}">
                                    <td class="text-black border border-blue-400">
                                        @php
                                            $participantNumber = is_array($participantWithScore['participant']) 
                                                ? $participantWithScore['participant']['participant_no'] 
                                                : $participantWithScore['participant']->participant_number;
                                        @endphp
                                        {{ $participantNumber ?? 'N/A' }}
                                    </td>

                                    <td class="text-black border border-blue-400">
                                        @php
                                            $participantName = is_array($participantWithScore['participant']) 
                                                ? $participantWithScore['participant']['name'] 
                                                : $participantWithScore['participant']->participant_name;
                                        @endphp
                                        {{ $participantName ?? 'N/A' }}
                                    </td>

                                    <!-- Loop through all categories and display the participant's score for each one -->
                                    @php
                                        $totalScore = $participantWithScore['totalScore'];
                                    @endphp

                                    @foreach($categoriesWithScores as $categoryData)
                                        @php
                                            $categoryScore = null;
                                        @endphp
                                        @foreach($categoryData['participants'] as $categoryParticipantData)
                                            @if($categoryParticipantData['participant']['id'] === $participantId)
                                                @php
                                                    $categoryScore = $categoryParticipantData['totalAverageScore']; // Get the total average score for this participant
                                                @endphp
                                            @endif
                                        @endforeach

                                        <!-- Display the total average score for each participant in the current category -->
                                        <td class="text-black border border-blue-400">
                                            {{ $categoryScore ?? 'N/A' }}
                                        </td>
                                    @endforeach

                                    <!-- Display total score and rank -->
                                    <td class="text-black border border-blue-400">{{ $totalScore }}</td>
                                    <td class="text-black border border-blue-400">{{ $rank }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>



                @endif
           
        </div>
       
    @elseif(Auth::user()->hasRole('event_manager'))
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
            <div class="flex justify-between mb-4 sm:-mt-4" id="resultsSection">
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
                                <thead class="bg-blue-300 text-black">
                                    <tr>
                                        <th class="border border-blue-400 px-3 py-2">Participant No.</th>
                                        <th class="border border-blue-400 px-3 py-2">Participant</th>
                                        @foreach ($judges as $judge)
                                            <th class="border border-blue-400 px-3 py-2">Judge: {{ $judge->name }}</th>
                                        @endforeach
                                        <th class="border border-blue-400 px-3 py-2">Total Average Score</th>
                                        <th class="border border-blue-400 px-3 py-2">Rank</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    // Sort by Total Average Score to determine ranks
                                    usort($categoryData['participants'], function ($a, $b) {
                                        return $b['totalAverageScore'] <=> $a['totalAverageScore']; // Descending order by score
                                    });

                                    // Assign ranks based on Total Average Score
                                    foreach ($categoryData['participants'] as $index => &$participant) {
                                        $participant['rank'] = $index + 1;
                                    }

                                    // Sort by Participant Number for display purposes
                                    usort($categoryData['participants'], function ($a, $b) {
                                        $participantNoA = is_array($a['participant']) ? $a['participant']['participant_no'] : $a['participant']->participant_number;
                                        $participantNoB = is_array($b['participant']) ? $b['participant']['participant_no'] : $b['participant']->participant_number;
                                        return $participantNoA <=> $participantNoB;
                                    });
                                    @endphp

                                    <!-- Loop through participants and display their data -->
                                    @foreach($categoryData['participants'] as $participantData)
                                        @php
                                            $rank = $participantData['rank'] ?? 'N/A';

                                            // Determine row color based on rank
                                            $rowColor = '';
                                            if ($rank == 1) {
                                                $rowColor = 'bg-yellow-300'; // Gold for 1st place
                                            } elseif ($rank == 2) {
                                                $rowColor = 'bg-gray-300'; // Silver for 2nd place
                                            } elseif ($rank == 3) {
                                                $rowColor = 'bg-orange-300'; // Bronze for 3rd place
                                            }
                                        @endphp

                                        <tr class="hover:bg-gray-100 {{ $rowColor }}">
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

                                            <!-- Display Scores by Judges -->
                                            @foreach ($judges as $judge)
                                                <td class="text-black border border-blue-400">
                                                    @php
                                                        $totalScore = 0;

                                                        foreach ($categoryData['category']['criteria'] as $criteria) {
                                                            $scorecard = App\Models\Admin\Scorecard::where('user_id', $judge->id)
                                                                ->where('event_id', $selectedEvent)
                                                                ->where('participant_id', is_array($participantData['participant']) ? $participantData['participant']['id'] : $participantData['participant']->id)
                                                                ->where('criteria_id', $criteria['id'])
                                                                ->first();

                                                            if ($scorecard) {
                                                                $totalScore += $scorecard->score;
                                                            }
                                                        }

                                                        // Calculate average score
                                                        if ($eventToShow->type_of_scoring == 'points') {
                                                            $averageScore = $totalScore * $categoryData['category']['category_score'] / 100;
                                                        } elseif ($eventToShow->type_of_scoring == 'ranking(H-L)' || $eventToShow->type_of_scoring == 'ranking(L-H)') {
                                                            $averageScore = $totalScore > 0 ? $totalScore : 0;
                                                        } else {
                                                            $averageScore = 0; // Fallback for undefined scoring types
                                                        }
                                                    @endphp
                                                    {{ $averageScore }}
                                                </td>
                                            @endforeach

                                            <!-- Display Total Average Score -->
                                            <td class="text-black border border-blue-400">{{ $participantData['totalAverageScore'] }}</td>

                                            <!-- Display Rank -->
                                            <td class="text-black border border-blue-400">{{ $rank }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endforeach



                    <div class="mb-8">
                        <h3 class="font-bold text-lg mb-4">Overall Rank</h3>
                        <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
                            <thead class="bg-blue-300 text-black">
                                <tr>
                                    <th class="border border-blue-400 px-3 py-2">Participant No.</th>
                                    <th class="border border-blue-400 px-3 py-2">Participant</th>

                                    <!-- Add a column for each category -->
                                    @foreach ($categoriesWithScores as $categoryData)
                                        <th class="border border-blue-400 px-3 py-2">{{ $categoryData['category']['name'] }}</th>
                                    @endforeach

                                    <th class="border border-blue-400 px-3 py-2">Total Score</th>
                                    <th class="border border-blue-400 px-3 py-2">Rank</th>
                                </tr>
                            </thead>
                            <tbody>
                            @php
                                // Step 1: Calculate the total score for each participant
                                $participantsWithScores = [];

                                foreach ($categoriesWithScores[0]['participants'] as $participantData) {
                                    $totalScore = 0;
                                    $participantId = is_array($participantData['participant'])
                                        ? $participantData['participant']['id']
                                        : $participantData['participant']->id;

                                    // Calculate total score for each participant
                                    foreach ($categoriesWithScores as $categoryData) {
                                        foreach ($categoryData['participants'] as $categoryParticipantData) {
                                            if ($categoryParticipantData['participant']['id'] === $participantId) {
                                                $categoryScore = $categoryParticipantData['totalAverageScore'];
                                                $totalScore += $categoryScore ?? 0; // Accumulate the total score
                                            }
                                        }
                                    }

                                    // Add participant data and their total score to the array
                                    $participantsWithScores[] = [
                                        'participant' => $participantData['participant'],
                                        'totalScore' => $totalScore
                                    ];
                                }

                                // Step 2: Sort participants by ID in ascending order
                                usort($participantsWithScores, function ($a, $b) {
                                    $idA = is_array($a['participant']) ? $a['participant']['id'] : $a['participant']->id;
                                    $idB = is_array($b['participant']) ? $b['participant']['id'] : $b['participant']->id;
                                    return $idA <=> $idB; // Sort by ID in ascending order
                                });

                                // Step 3: Assign ranks based on total scores
                                $rankedParticipants = $participantsWithScores;

                                if ($eventToShow->type_of_scoring == 'ranking(L-H)') {
                                    // Sort by total score (ascending) for 'ranking(L-H)'
                                    usort($rankedParticipants, function ($a, $b) {
                                        return bccomp($a['totalScore'], $b['totalScore'], 6); // 6 decimal places of precision
                                    });
                                } else {
                                    // Sort by total score (descending) for other scoring types
                                    usort($rankedParticipants, function ($a, $b) {
                                        return bccomp($b['totalScore'], $a['totalScore'], 6); // 6 decimal places of precision
                                    });
                                }

                                // Map ranks back to the original participant order by ID
                                $participantRanks = [];
                                foreach ($rankedParticipants as $index => $rankedParticipant) {
                                    $id = is_array($rankedParticipant['participant']) ? $rankedParticipant['participant']['id'] : $rankedParticipant['participant']->id;
                                    $participantRanks[$id] = $index + 1;
                                }
                            @endphp

                            <!-- Loop through all participants and display their data -->
                            @foreach($participantsWithScores as $participantWithScore)
                                @php
                                    $participantId = is_array($participantWithScore['participant']) 
                                        ? $participantWithScore['participant']['id'] 
                                        : $participantWithScore['participant']->id;
                                    $rank = $participantRanks[$participantId] ?? 'N/A'; // Get the rank for this participant

                                    // Determine row color based on rank
                                    $rowColor = '';
                                    if ($rank == 1) {
                                        $rowColor = 'bg-yellow-300'; // Gold for 1st place
                                    } elseif ($rank == 2) {
                                        $rowColor = 'bg-gray-300'; // Silver for 2nd place
                                    } elseif ($rank == 3) {
                                        $rowColor = 'bg-orange-300'; // Bronze for 3rd place
                                    }
                                @endphp

                                <tr class="hover:bg-gray-100 {{ $rowColor }}">
                                    <td class="text-black border border-blue-400">
                                        @php
                                            $participantNumber = is_array($participantWithScore['participant']) 
                                                ? $participantWithScore['participant']['participant_no'] 
                                                : $participantWithScore['participant']->participant_number;
                                        @endphp
                                        {{ $participantNumber ?? 'N/A' }}
                                    </td>

                                    <td class="text-black border border-blue-400">
                                        @php
                                            $participantName = is_array($participantWithScore['participant']) 
                                                ? $participantWithScore['participant']['name'] 
                                                : $participantWithScore['participant']->participant_name;
                                        @endphp
                                        {{ $participantName ?? 'N/A' }}
                                    </td>

                                    <!-- Loop through all categories and display the participant's score for each one -->
                                    @php
                                        $totalScore = $participantWithScore['totalScore'];
                                    @endphp

                                    @foreach($categoriesWithScores as $categoryData)
                                        @php
                                            $categoryScore = null;
                                        @endphp
                                        @foreach($categoryData['participants'] as $categoryParticipantData)
                                            @if($categoryParticipantData['participant']['id'] === $participantId)
                                                @php
                                                    $categoryScore = $categoryParticipantData['totalAverageScore']; // Get the total average score for this participant
                                                @endphp
                                            @endif
                                        @endforeach

                                        <!-- Display the total average score for each participant in the current category -->
                                        <td class="text-black border border-blue-400">
                                            {{ $categoryScore ?? 'N/A' }}
                                        </td>
                                    @endforeach

                                    <!-- Display total score and rank -->
                                    <td class="text-black border border-blue-400">{{ $totalScore }}</td>
                                    <td class="text-black border border-blue-400">{{ $rank }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>



                @endif
           
        </div>
       
    @elseif(Auth::user()->hasRole('staff'))
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
                <div class="font-bold text-md tracking-tight text-md text-black mt-2 uppercase">Result</div>
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


                            <!-- Wrap the table in a responsive container -->
                            <div class="overflow-x-auto">
                                <table class="table-auto border-collapse border border-gray-400 w-full mt-2">
                                    <thead class="bg-blue-300 text-black">
                                        <tr>
                                            <th class="border border-blue-400 px-3 py-2">Participant No.</th>
                                            <th class="border border-blue-400 px-3 py-2">Participant</th>
                                            @foreach ($judges as $judge)
                                                <th class="border border-blue-400 px-3 py-2">Judge: {{ $judge->name }}</th>
                                            @endforeach
                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Sort participants based on totalAverageScore -->
                                        @php
                                        if ($eventToShow->type_of_scoring == 'ranking(L-H)') {
                                            usort($categoryData['participants'], function($a, $b) {
                                                return $a['totalAverageScore'] <=> $b['totalAverageScore']; // Ascending order for ranking(L-H)
                                            });
                                        } else {
                                            usort($categoryData['participants'], function($a, $b) {
                                                return $b['totalAverageScore'] <=> $a['totalAverageScore']; // Descending order for other types
                                            });
                                        }
                                        @endphp


                                        <!-- Loop through participants and their scores for the current category -->
                                        @foreach($categoryData['participants'] as $index => $participantData)
                                            <tr class="hover:bg-gray-100">
                                                <td class="text-black border border-blue-400">
                                                    @php
                                                        $participantNumber = is_array($participantData['participant']) ? $participantData['participant']['participant_no'] : $participantData['participant']->participant_number;
                                                    @endphp
                                                    {{ $participantNumber ?? 'N/A' }}
                                                </td>


                                                <td class="text-black border border-blue-400">
                                                    @php
                                                        $participantName = is_array($participantData['participant']) ? $participantData['participant']['name'] : $participantData['participant']->participant_name;
                                                    @endphp
                                                    {{ $participantName ?? 'N/A' }}
                                                </td>


                                                @foreach ($judges as $judge)
                                                    <td class="text-black border border-blue-400">
                                                        @php
                                                            $totalScore = 0;
                                                            $criteriaCount = 0;
                                                            $scoreSubmitted = false;  // Flag to check if score was submitted


                                                            // Loop through criteria and fetch scorecards
                                                            foreach ($categoryData['category']['criteria'] as $criteria) {
                                                                $scorecard = App\Models\Admin\Scorecard::where('user_id', $judge->id)
                                                                    ->where('event_id', $selectedEvent)
                                                                    ->where('participant_id', is_array($participantData['participant']) ? $participantData['participant']['id'] : $participantData['participant']->id)
                                                                    ->where('criteria_id', $criteria['id'])
                                                                    ->first();


                                                                if ($scorecard) {
                                                                    $totalScore += $scorecard->score;
                                                                    $criteriaCount++;
                                                                    // Mark as submitted if there's a score
                                                                    $scoreSubmitted = true;
                                                                }
                                                            }


                                                            // Calculate average score based on the scoring type
                                                            if ($eventToShow->type_of_scoring == 'points') {
                                                                $averageScore = $totalScore * $categoryData['category']['category_score'] / 100;
                                                            } elseif ($eventToShow->type_of_scoring == 'ranking(H-L)' || $eventToShow->type_of_scoring == 'ranking(L-H)') {
                                                                $averageScore = $totalScore > 0 ? $totalScore : 0;
                                                            } else {
                                                                $averageScore = 0;
                                                            }
                                                        @endphp
                                                        <!-- If score was submitted, show 'Submitted', otherwise show the calculated average score -->
                                                        {{ $scoreSubmitted ? 'Submitted' : ($averageScore ?? 'N/A') }}
                                                    </td>
                                                @endforeach


                                            </tr>
                                        @endforeach


                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach


                </div>    
                @endif
            </div>
        </div>
       
    @endif
</div>




   



