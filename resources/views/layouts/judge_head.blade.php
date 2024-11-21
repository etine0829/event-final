<div class="transition-all duration-300 fixed top-0 left-0 right-0 w-full flex flex-col flex-auto flex-shrink-0 antialiased z-50">
    <div id="dashboardContent" class="transition-all duration-300">
        <div class="max-w-full mx-auto">
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                    window.addEventListener('resize', () => {
                        isFullScreen = (window.innerHeight === screen.height);
                    });
                " x-show="!isFullScreen" class="flex w-full p-2 bg-blue-500 justify-between">
                <div class="ml-2 mt-0.5 font-semibold tracking-wide text-white uppercase flex items-center space-x-2">
                    <span class="text-xs sm:text-sm md:text-base lg:text-lg xl:text-xl">
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
        </div>
    </div>
</div>