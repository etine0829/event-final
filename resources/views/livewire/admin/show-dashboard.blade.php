<div >
    @if (Auth::user()->hasRole('admin'))
        <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-wide text-black mt-2 uppercase">Admin Dashboard</div>   
        </div>
    @else
        <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-wide text-black mt-2 uppercase">Event Manager Dashboard</div>   
        </div>
    @endif

    <hr class="border-gray-200 my-4">

    <div>
        <!-- Grid Layout for Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 p-8">
            <!-- Event Card -->
            <a href="{{ Auth::user()->hasRole('admin') ? route('admin.event.index') : route('event_manager.event.index') }}">
                <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">EVENT</h2>
                    <p class="text-4xl font-extrabold mt-4">{{ $eventCount }}</p>
                    <p class="mt-2 text-sm">Number of Event</p>
                </div>
            </a>
            <!-- Judges Card -->
            <a href="{{ Auth::user()->hasRole('admin') ? route('admin.judge.index') : route('event_manager.judge.index') }}">
                <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">JUDGES</h2>
                    <p class="text-4xl font-extrabold mt-4">{{ $judgeCount }}</p>
                    <p class="mt-2 text-sm">Number of Users</p>
                </div>
            </a>

            <!-- Category Card -->
            <a href="{{ Auth::user()->hasRole('admin') ? route('admin.category.index') : route('event_manager.category.index') }}">    
                <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">CATEGORY</h2>
                    <p class="text-4xl font-extrabold mt-4">{{ $categoryCount }}</p>
                    <p class="mt-2 text-sm">Number of Category</p>
                </div>
            </a>
            <!-- Criteria Card -->
            
            <a href="{{ Auth::user()->hasRole('admin') ? route('admin.criteria.index') : route('event_manager.criteria.index') }}">
                <div class="bg-blue-900 text-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold">CRITERIA</h2>
                    <p class="text-4xl font-extrabold mt-4">{{ $criteriaCount }}</p>
                    <p class="mt-2 text-sm">Number of Criteria</p>
                </div>
            </a>
        </div>
    </div>      
</div>
