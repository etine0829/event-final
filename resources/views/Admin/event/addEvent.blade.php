<x-app-layout>
    <form action="{{ route('add_event') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <!-- Event Title -->
            <div>
                <label for="eventTitle" class="block text-sm font-medium text-gray-700">Event Title</label>
                <input type="text" id="eventTitle" name="event_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
            </div>

            <!-- Date -->
            <div>
                <label for="eventDate" class="block text-sm font-medium text-gray-700">Date</label>
                <input type="datetime-local" id="eventDate" name="event_date" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
            </div>

            <!-- Venue -->
            <div>
                <label for="eventVenue" class="block text-sm font-medium text-gray-700">Venue</label>
                <input type="text" id="eventVenue" name="venue" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
            </div>

            <!-- Scoring System -->
            <div>
                <label for="scoringSystem" class="block text-sm font-medium text-gray-700">Scoring System</label>
                <select id="scoringSystem"  name="type_of_scoring" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-yellow-500 focus:border-yellow-500 sm:text-sm">
                    <option value="" disabled selected>Select a scoring system</option>
                    <option value="Point System">Point System</option>
                    <option value="Ranking (H-L)">Ranking (H-L)</option>
                    <option value="Ranking (L-H)">Ranking (L-H)</option>
                </select>
            </div>
            
            <!-- Upload Poster -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Upload Event Poster</label>
                <div class="mt-1 flex items-center space-x-4">
                    <label class="block">
                        <span class="sr-only">Choose File</span>
                        <input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-yellow-500 file:text-white hover:file:bg-yellow-600">
                    </label>
                    <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Actions -->
        <div class="mt-6 flex justify-end space-x-4">
            <button type="button" id="cancelButton" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600">Cancel</button>
            <button type="submit" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Submit</button>
        </div>
    </form>
</x-app-layout>