@php
    session(['selectedEvent' => $selectedEvent]);
@endphp

@if (Auth::user()->hasRole('admin'))
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

        <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-tight text-md text-black mt-2 uppercase">Admin / Result</div>
        </div>

        <!-- Event Selection -->
        <div>
            <label for="event_id" class="block text-sm text-gray-700 font-bold">Event:</label>
            <select wire:model="selectedEvent" wire:change="updatingSelectedEvent" class="text-sm border rounded py-2 px-2">
                <option value="">Select Event</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->event_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Show categories and criteria for the selected event -->
        @if($selectedEvent)
            <div class="mt-4">
                <h3 class="font-bold text-lg">Categories for {{ $eventToShow->event_name }}</h3>

                <!-- Display categories -->
                <ul>
                    @foreach($categories as $category)
                        <li class="mt-2">
                            <strong>{{ $category->category_name }}</strong>

                            <!-- Display criteria for each category -->
                            @if($category->criteria->isNotEmpty())
                                <ul>
                                    @foreach($category->criteria as $criterion)
                                        <li>{{ $criterion->criterion_name }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p>No criteria available for this category.</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Results Table -->
        <div class="mt-6">
            @if($results->isNotEmpty())
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Category</th>
                            <th class="px-4 py-2">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr>
                                <td class="border px-4 py-2">{{ $result->category->category_name }}</td>
                                <td class="border px-4 py-2">{{ $result->score }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $results->links() }}
            @else
                <p>No results found for this event.</p>
            @endif
        </div>
    </div>

@endif
