
    <!-- Sidebar -->
    <div id="sidebar" class="bg-red-dark text-yellow-dark w-80 space-y-6 px-6 py-7 absolute inset-y-0 left-0 transform -translate-x-full transition-transform duration-200 ease-in-out md:relative md:translate-x-0 z-50 h-screen">
        <!-- Back Button (Visible on mobile) -->
        <button @click="open = false" id="back-btn" class="md:hidden text-white focus:outline-none p-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        <!-- Logo -->
        <div class="flex items-center justify-center h-8">
            <img src="{{ asset('/img/tres (2).png') }}" alt="LOGO OF THE TEAM" class="w-32">
            
        </div>
        <div class="flex justify-center w-32 text-center">
            <p>Welcome {{ Auth::user()->email }}</p>
        </div>
        <!-- Navigation -->
        <nav class="font-bold text-yellow-dark">
            <a href="#" class="block py-2.5 px-4 rounded hover:bg-red-800 hover:text-yellow-200">Event</a>
            <a href="#" class="block py-2.5 px-4 rounded hover:bg-red-800 hover:text-yellow-200">Category & Criteria</a>
            <a href="#" class="block py-2.5 px-4 rounded hover:bg-red-800 hover:text-yellow-200">Participant</a>
            <a href="#" class="block py-2.5 px-4 rounded hover:bg-red-800 hover:text-yellow-200">Judge</a>
            <a href="#" class="block py-2.5 px-4 rounded hover:bg-red-800 hover:text-yellow-200">Result</a>
        </nav>
    </div>
