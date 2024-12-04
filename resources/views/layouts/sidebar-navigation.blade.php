@if (Auth::user()->hasRole('admin'))
<div x-cloak x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" id="sidebarContainer"  class="fixed flex flex-col left-0 w-14 hover:w-48 md:w-48 bg-blue-950  text-yellow-dark h-full transition-all duration-300 border-r-2 border-gray-300 dark:border-gray-600 sidebar z-50">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow mr-0.5">
            <ul class="flex flex-col py-2 space-y-1 text-gray-800" >
                <a href="#" class="flex justify-center items-center mb-5">
                    <img class="mt-4 mb-5 w-32 h-auto object-contain" src="{{ asset('assets/img/tres.png') }}" alt="Event Tabulation System">
                </a>

                <label class="relative flex flex-row justify-center items-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent  pr-3 ">
                    <span class=" text-sm tracking-wide truncate text-gray-200">{{ Auth::user()->name }}</span>
                </label>
                <label class="relative flex flex-row justify-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent   ">
                    <span class=" text-xs tracking-wide truncate text-gray-200">{{ Auth::user()->email }}</span>
                </label>
                <div class="border-t"></div>
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6 
                    {{ request()->routeIs('admin.dashboard') ? ' border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                        <i class="fa-solid fa-layer-group fa-sm text-gray-200"></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Dashboard</span>
                    </a>
                </li>
                <!-- <li x-data="{ open: {{ request()->routeIs('admin.event.index') || request()->routeIs('admin.attendance.student_attendance') || request()->routeIs('admin.attendance.employee_attendance.search') || request()->routeIs('admin.attendance.employee_attendance.payroll') || request()->routeIs('admin.attendance.employee_attendance.payroll.all') ? 'true'  : 'false' }} }"> -->
                <li x-data="{ open: {{ request()->routeIs('admin.event.index') || request()->routeIs('admin.category.index') || request()->routeIs('admin.criteria.index') || request()->routeIs('admin.participant.index') || request()->routeIs('admin.judge.index')  ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="w-full cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                        <i class="fa-solid fa-calendar-check fa-sm text-gray-200"></i>
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
                            <a href="{{ route('admin.event.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('admin.event.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;<i class="fa-solid fa-list fa-sm text-gray-200 mr-2"></i> Event List
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.category.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('admin.category.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;
                                <i class="fa-solid fa-project-diagram fa-sm text-gray-200 mr-2"></i>
                                Category
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.criteria.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('admin.criteria.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;
                                <i class="fa-solid fa-list-check fa-sm text-gray-200 mr-2"></i>
                                Criteria
                            </a>
                        </li>

                        <li x-data="{ open: {{ request()->routeIs('admin.group.index') || request()->routeIs('admin.participant.index')  ? 'true'  : 'false' }} }">
                            <a @click="open = !open" class="w-full cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6">
                                <span class="inline-flex justify-center items-center ml-3">
                                    <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                                </span>
                                <span class="text-sm tracking-wide truncate text-gray-200 ml-2 font-bold">Participant</span>
                                <span class="ml-auto">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                        <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </a>
                            <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1 w-full">
                                <li>
                                    
                                    <a href="{{ route('admin.group.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                                    {{ request()->routeIs('admin.group.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                        &nbsp;<i class="fa-solid fa-flag fa-sm text-gray-200 mr-2"></i>


                                       Assign Group
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('admin.participant.index') }}" 
                                    class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm text-white border-l-4 border-transparent hover:bg-blue-800 hover:text-white dark:hover:bg-slate-700 dark:hover:border-yellow-500 
                                    {{ request()->routeIs('admin.participant.index') ? 'border-l-yellow-500 bg-[#172029]' : 'hover:border-blue-500' }}">
                                    <i class="fa-solid fa-address-book fa-sm text-gray-200 mr-2"></i>

                                    Participant List
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('admin.judge.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('admin.judge.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;<i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i> Assign Judge
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="{{ route('admin.user.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                        {{ request()->routeIs('admin.user.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                        &nbsp;<i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i> Add User 
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.result.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6 
                    {{ request()->routeIs('admin.result.index') ? ' border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                        <i class="fa-solid fa-layer-group fa-sm text-gray-200"></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Result</span>
                    </a>
                </li>

            </ul>
                <p class="mb-14 px-5 py-3 hidden md:block text-center text-xs text-white">Copyright @2024</p>
        </div>
    </div>
@elseif (Auth::user()->hasRole('event_manager'))
    <div x-cloak x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" id="sidebarContainer"  class="fixed flex flex-col left-0 w-14 hover:w-48 md:w-48 bg-blue-950  text-yellow-dark h-full transition-all duration-300 border-r-2 border-gray-300 dark:border-gray-600 sidebar z-50">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow mr-0.5">
            <ul class="flex flex-col py-2 space-y-1 text-gray-800" >
                <a href="#" class="flex justify-center items-center mb-5">
                    <img class="mt-4 mb-5 w-32 h-auto object-contain" src="{{ asset('assets/img/tres.png') }}" alt="Event Tabulation System">
                </a>

                <label class="relative flex flex-row justify-center items-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent  pr-3 ">
                    <span class=" text-sm tracking-wide truncate text-gray-200">{{ Auth::user()->name }}</span>
                </label>
                <label class="relative flex flex-row justify-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent   ">
                    <span class=" text-xs tracking-wide truncate text-gray-200">{{ Auth::user()->email }}</span>
                </label>
                <div class="border-t"></div>
                <li>
                    <a href="{{ route('event_manager.dashboard') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6 
                    {{ request()->routeIs('event_manager.dashboard') ? ' border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                        <i class="fa-solid fa-layer-group fa-sm text-gray-200"></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Dashboard</span>
                    </a>
                </li>
                <li x-data="{ open: {{ request()->routeIs('event_manager.event.index') || request()->routeIs('event_manager.category.index') || request()->routeIs('event_manager.criteria.index') || request()->routeIs('event_manager.participant.index') || request()->routeIs('event_manager.judge.index')  ? 'true'  : 'false' }} }">
                    <a @click="open = !open" class="w-full cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6">
                        <span class="inline-flex justify-center items-center ml-3">
                        <i class="fa-solid fa-calendar-check fa-sm text-gray-200"></i>
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
                            <a href="{{ route('event_manager.event.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('event_manager.event.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;<i class="fa-solid fa-list fa-sm text-gray-200 mr-2"></i> Event List
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('event_manager.category.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('event_manager.category.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;
                                <i class="fa-solid fa-project-diagram fa-sm text-gray-200 mr-2"></i>
                                Category
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('event_manager.criteria.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('event_manager.criteria.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;
                                <i class="fa-solid fa-list-check fa-sm text-gray-200 mr-2"></i>
                                Criteria
                            </a>
                        </li>

                        <li x-data="{ open: {{ request()->routeIs('event_manager.group.index') || request()->routeIs('event_manager.participant.index')  ? 'true'  : 'false' }} }">
                            <a @click="open = !open" class="w-full cursor-pointer relative flex flex-row items-center h-11 focus:outline-none hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6">
                                <span class="inline-flex justify-center items-center ml-3">
                                    <i class="fa-solid fa-users fa-sm text-gray-200"></i>
                                </span>
                                <span class="text-sm tracking-wide truncate text-gray-200 ml-2 font-bold">Participant</span>
                                <span class="ml-auto">
                                    <svg fill="currentColor" viewBox="0 0 20 20" class="w-4 h-4">
                                        <path x-show="!open" fill-rule="evenodd" d="M5.293 9.293a1 1 0 011.414 0L10 12.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        <path x-show="open" fill-rule="evenodd" d="M14.707 10.707a1 1 0 01-1.414 0L10 7.414 6.707 10.707a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </a>
                            <ul x-show="open"  x-cloak class="ml-3 mt-1 space-y-1 w-full">
                                <li>                                   
                                    <a href="{{ route('event_manager.group.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                                    {{ request()->routeIs('event_manager.group.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                        &nbsp;<i class="fa-solid fa-flag fa-sm text-gray-200 mr-2"></i>

                                       Assign Group
                                    </a>
                                </li>

                                <li>
                                    <a href="{{ route('event_manager.participant.index') }}" 
                                    class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm text-white border-l-4 border-transparent hover:bg-blue-800 hover:text-white dark:hover:bg-slate-700 dark:hover:border-yellow-500 
                                    {{ request()->routeIs('event_manager.participant.index') ? 'border-l-yellow-500 bg-[#172029]' : 'hover:border-blue-500' }}">
                                    <i class="fa-solid fa-address-book fa-sm text-gray-200 mr-2"></i>

                                    Participant List
                                    </a>
                                </li>

                            </ul>
                        </li>

                        <li>
                            <a href="{{ route('event_manager.judge.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                            {{ request()->routeIs('event_manager.judge.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                                &nbsp;<i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i>Assign
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li>
                    <a href="{{ route('event_manager.user.index')}}" class="w-[500px] flex items-center h-11 pl-4 pr-6 text-sm hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 over:bg-blue-800 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white 
                        {{ request()->routeIs('event_manager.user.index') ? 'border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                        &nbsp;<i class="fa-solid fa-user-circle fa-sm text-gray-200 mr-2"></i> Add User 
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('event_manager.result.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6 
                    {{ request()->routeIs('event_manager.result.index') ? ' border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                        <i class="fa-solid fa-layer-group fa-sm text-gray-200"></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Result</span>
                    </a>
                </li>

            </ul>
                <p class="mb-14 px-5 py-3 hidden md:block text-center text-xs text-white">Copyright @2024</p>
        </div>
    </div>

@else
    <div x-cloak x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" id="sidebarContainer"  class="fixed flex flex-col left-0 w-14 hover:w-48 md:w-48 bg-blue-950  text-yellow-dark h-full transition-all duration-300 border-r-2 border-gray-300 dark:border-gray-600 sidebar z-50">
        <div class="overflow-y-auto overflow-x-hidden flex flex-col justify-between flex-grow mr-0.5">
            <ul class="flex flex-col py-2 space-y-1 text-gray-800" >
                <a href="#" class="flex justify-center items-center mb-5">
                    <img class="mt-4 mb-5 w-32 h-auto object-contain" src="{{ asset('assets/img/tres.png') }}" alt="Event Tabulation System">
                </a>

                <label class="relative flex flex-row justify-center items-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent  pr-3 ">
                    <span class=" text-sm tracking-wide truncate text-gray-200">{{ Auth::user()->name }}</span>
                </label>
                <label class="relative flex flex-row justify-center h-5 focus:outline-none   text-white-600 hover:text-white-800 border-l-4 border-transparent   ">
                    <span class=" text-xs tracking-wide truncate text-gray-200">{{ Auth::user()->email }}</span>
                </label>
                <div class="border-t"></div>
                
                <li>
                    <a href="{{ route('result.index') }}" class="relative flex flex-row items-center h-11 focus:outline-none  hover:bg-blue-800 dark:hover:bg-slate-700 text-white hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white pr-6 
                    {{ request()->routeIs('staff.result.index') ? ' border-l-yellow-500 bg-[#172029] text-white' : 'hover:bg-blue-800 dark:hover:bg-slate-700 text-white-600 hover:text-white-800 border-l-4 border-transparent hover:border-blue-500 dark:hover:border-yellow-500 hover:text-white' }}">
                        <span class="inline-flex justify-center items-center ml-4">
                        <i class="fa-solid fa-layer-group fa-sm text-gray-200"></i>
                        </span>
                        <span class="ml-2 text-sm tracking-wide truncate text-gray-200">Result</span>
                    </a>
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


