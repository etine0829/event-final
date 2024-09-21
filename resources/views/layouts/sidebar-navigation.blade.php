@if (Auth::user()->hasRole('admin'))
    <div x-cloak x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" id="sidebarContainer"  class="fixed flex flex-col left-0 w-14 hover:w-48 md:w-48 bg-red-dark text-yellow-dark h-full transition-all duration-300 border-r-2 border-gray-300 dark:border-gray-600 sidebar z-50">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow mr-0.5">
            <ul class="flex flex-col py-2 space-y-1 text-gray-800" >
                <a href="#" class="flex justify-center items-center mb-5">
                    <img class="mb-9 w-32 h-auto object-contain" src="{{ asset('assets/img/tres.png') }}" alt="Event Tabulation System">
                </a>

                <label class="relative flex flex-row justify-center items-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent  pr-3 ">
                    <span class=" text-sm tracking-wide truncate text-gray-200">{{ Auth::user()->name }}</span>
                </label>
                <label class="relative flex flex-row justify-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent   ">
                    <span class=" text-xs tracking-wide truncate text-gray-200">{{ Auth::user()->email }}</span>
                </label>
                <div class="border-t"></div>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('admin.dashboard') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            <i class="fa-solid fa-gauge-high fa-sm text-gray-200 "></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Dashboard</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('admin.attendance.employee_attendance') || request()->routeIs('admin.attendance.student_attendance') || request()->routeIs('admin.attendance.employee_attendance.search') || request()->routeIs('admin.attendance.employee_attendance.payroll') || request()->routeIs('admin.attendance.employee_attendance.payroll.all') ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="w-full cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                            <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                        </span>
                        <span class="text-sm tracking-wide truncate text-gray-200 ml-2">Manage Event</span>
                        <span class="ml-auto">
                            <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </span>
                    </a>
                    <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1 w-full">
                        <li>
                            <a href="" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white {{ request()->routeIs('admin.attendance.employee_attendance.search') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Event List
                            </a>
                        </li>
                       
                        <li class="w-full">
                            <a href="{{ route('Admin.event.addEvent.blade.php') }}" class="flex items-center  h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white 
                            {{ request()->routeIs('admin.attendance.employee_attendance') ? 'border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                                <i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Add Event
                            </a>
                        </li>
                        
                    </ul>
                </li>
                
                <li>
                    <a href="" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6 
                    {{ request()->routeIs('admin.dashboard') ? ' border-l-green-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                            {{-- <i class="fa-solid fa-gauge-high fa-sm text-gray-200 "></i> --}}
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Add User</span>
                    </a>
                </li>
                
                <li>
                    <form id="logout" method="POST" action="{{ route('logout') }}" onsubmit="return confirmLogout(event)">
                        @csrf

                        <button type="submit" class="relative flex flex-row items-center w-full h-11 focus:outline-none  hover:bg-[#172029] text-white] dark:hover:bg-slate-700 text-gray-200 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-green-500 hover:text-white pr-6">
                            <span class="inline-flex justify-center items-center ml-5">
                                <i class="fa-solid fa-right-from-bracket fa-sm text-gray-200"></i>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate text-gray-200">{{ __('Sign Out') }}</span>
                        </button>
                    </form>
                </li>
            </ul>
                <p class="mb-14 px-5 py-3 hidden md:block text-center text-xs text-white">Copyright @2024</p>
        </div>
    </div>

@endif


<!-- end of admin navigation -->
    <script>
            function confirmLogout(event) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: 'Are you sure you want to logout?',
            text: "Save everything before leaving",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, submit the deleteSelectedForm form programmatically
                document.getElementById('logout').submit();
            }
        });
    }
    </script>


{{-- 
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
    </div> --}}
