@if (Auth::user()->hasRole('admin'))
    <div class="transition-all duration-300 min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased text-black dark:text-white">
        <div id="dashboardContent" class="h-full ml-14  md:ml-48 transition-all duration-300">
            <div class="max-w-full mx-auto">
                <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" class="flex w-full p-2 bg-blue-500 justify-between">
                    <div class="ml-2 mt-0.5 font-semibold text-xs tracking-wide text-white uppercase sm:text-sm md:text-md lg:text-md xl:text-md">
                        <button id="toggleButton" class="text-white mr-0 px-3 py-1 rounded-md border border-transparent hover:border-blue-500">
                            <i id="toggleIcon" class="fa-solid fa-bars" style="color: #ffffff;"></i>
                        </button>
                        Event Tabulation Management System
                    </div>
                    <div x-cloak class="relative" x-data="{ open: false }">
                        <div @click="open = !open" class="mr-5 cursor-pointer">
                            <i class="fa-solid fa-user-gear px-3 py-2 rounded-md border border-transparent hover:border-blue-500" style="color: #ffffff;"></i>
                        </div>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20">
                            <a href="" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <i class="fa-regular fa-user"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Content Area -->
                {{ $slot }}
            </div>
        </div>
    </div>

@elseif (Auth::user()->hasRole('admin_staff'))
    <div class="transition-all duration-300 min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-gradient-to-r from-yellow-400 to-red-500 text-black dark:text-white">
        <div id="dashboardContent" class="h-full ml-14  md:ml-48 transition-all duration-300">
            <div class="max-w-full mx-auto">
                <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                        window.addEventListener('resize', () => {
                            isFullScreen = (window.innerHeight === screen.height);
                        });
                    " x-show="!isFullScreen" class="flex w-full p-2  bg-blue-500 justify-between">
                    <div class="ml-2 mt-0.5 font-semibold text-xs tracking-wide text-white uppercase sm:text-sm md:text-md lg:text-md xl:text-md">
                        <button id="toggleButton" class="text-white mr-0 px-3 py-1 rounded-md border border-transparent hover:border-blue-500">
                            <i id="toggleIcon" class="fa-solid fa-bars" style="color: #ffffff;"></i>
                        </button>
                        Event Tabulation Management System
                    </div>
                    <div x-cloak class="relative" x-data="{ open: false }">
                        <div @click="open = !open" class="mr-5 cursor-pointer">
                            <i class="fa-solid fa-user-gear px-3 py-2 rounded-md border border-transparent hover:border-blue-500" style="color: #ffffff;"></i>
                        </div>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20">
                            <a href="" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <i class="fa-regular fa-user"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Content Area -->
                {{ $slot }}
            </div>
        </div>
    </div>

    @elseif (Auth::user()->hasRole('judge'))
    <div class="transition-all duration-300 h-screen flex flex-col flex-auto flex-shrink-0 antialiased dark:text-white">
    <div id="dashboardContent" class="h-full transition-all duration-300">
        <div class="max-w-full mx-auto">
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                    window.addEventListener('resize', () => {
                        isFullScreen = (window.innerHeight === screen.height);
                    });
                " x-show="!isFullScreen" class="flex w-full p-2 bg-blue-500 justify-between">
                <div class="ml-2 mt-0.5 font-semibold tracking-wide text-white uppercase flex items-center space-x-2">
                    <span class="text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl">
                       {{-- @php
                            $user = Auth::user();

                           // if ($user && $user->hasRole('judge')) {
                           //     $events = \App\Models\Admin\Event::where('id', $user->event_id)->get();
                          //  } else {
                                $events = collect();
                          //  }
                        @endphp

                       // @forelse ($events as $event)
                            <p style="font-family:Algerian;font-weight:bold">{{ $user->name }}</p>
                       // @empty
                            <!-- <p>No events available.</p> -->
                       // @endforelse --}}
                       <p style="font-family:arial;font-weight:bold">Login as: {{ Auth::user()->name }}</p>
                    </span>
                </div>
                <div class="flex items-center space-x-4">
                    
                    <div x-cloak class="relative" x-data="{ open: false }">
                        <div @click="open = !open" class="cursor-pointer">
                            <i class="fa-solid fa-user-gear px-3 py-2 rounded-md border border-transparent hover:border-blue-500" style="color: #ffffff;"></i>
                        </div>
                        
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-20">
                            <a href="" class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                <i class="fa-regular fa-user"></i> Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="block px-4 py-2 text-gray-800 hover:bg-gray-200">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>

@endif