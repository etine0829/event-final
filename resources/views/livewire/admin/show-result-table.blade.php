@php
    session(['selectedEvent' => $selectedEvent]);
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

        <!-- Loop through categories and display results -->
        @foreach($categories as $category)
    <div class="mb-8">
        <h3 class="font-bold text-lg mb-4">{{ $category['name'] }}</h3>
        <table class="table-auto min-w-full text-center text-sm mb-4 divide-y divide-gray-200">
            <thead class="bg-orange-400 text-white">
                <tr>
                    <th class="border border-gray-400 px-3 py-2">Participant No.</th> <!-- New column for Participant Number -->
                    <th class="border border-gray-400 px-3 py-2">Participant</th>
                    @foreach ($judges as $judge)
                        <th class="border border-gray-400 px-3 py-2">Judge {{ $judge->name }}</th>
                    @endforeach
                    <th class="border border-gray-400 px-3 py-2">Deduction</th>
                    <th class="border border-gray-400 px-3 py-2">Total Score</th>
                    <th class="border border-gray-400 px-3 py-2">Rank</th>
                </tr>
            </thead>
            <tbody>
                @foreach($category['participants'] as $participant)
                    <tr class="hover:bg-gray-100">
                        <!-- Display the Participant Number (ID) -->
                        <td class="text-black border border-gray-400">{{ $participant['id'] }}</td>
                        <td class="text-black border border-gray-400">{{ $participant['name'] }}</td>
                        @foreach ($judges as $judge)
                            <td class="text-black border border-gray-400">
                                {{ $participant['rank'] }}
                            </td>
                        @endforeach
                        <td class="text-black border border-gray-400">{{ $participant['deduction'] ?? 0 }}</td>
                        <td class="text-black border border-gray-400">{{ $participant['total_score'] }}</td>
                        <td class="text-black border border-gray-400">{{ $participant['rank'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endforeach


    </div>
@endif
