<div >
    <div class="flex justify-between mb-4 sm:-mt-4">
        <div class="font-bold text-md tracking-wide text-black mt-2 uppercase">Admin / Manage Event</div>   
    </div>

    <hr class="border-gray-200 my-4">

    <div>
        <!-- Grid Layout for Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 p-8">
            <!-- Event Card -->
            <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold">EVENT</h2>
                <p class="text-4xl font-extrabold mt-4">{{ $eventCount }}</p>
                <p class="mt-2 text-sm">Number of Event</p>
            </div>
            <!-- Judges Card -->
            <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold">JUDGES</h2>
                <p class="text-4xl font-extrabold mt-4">{{ $judgeCount }}</p>
                <p class="mt-2 text-sm">Number of Users</p>
            </div>
            <!-- Category Card -->
            <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold">CATEGORY</h2>
                <p class="text-4xl font-extrabold mt-4">{{ $categoryCount }}</p>
                <p class="mt-2 text-sm">Number of Category</p>
            </div>
            <!-- Criteria Card -->
            <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                <h2 class="text-xl font-bold">CRITERIA</h2>
                <p class="text-4xl font-extrabold mt-4">{{ $criteriaCount }}</p>
                <p class="mt-2 text-sm">Number of Criteria</p>
            </div>
        </div>
    </div>      
</div>
