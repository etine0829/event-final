@if (Auth::user()->hasRole('admin'))
    <x-app-layout>
        <x-user-route-page-name :routeName="'admin.dashboard'" />
        <x-content-design>
            <!-- Content Area -->
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height)}" 
                x-init="window.addEventListener('resize', () => {
                    isFullScreen = (window.innerHeight === screen.height); });"
                 class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium ">
                <div class="relative">
                    <div class="container shadow-lg p-5 sm:p-6 md:p-7 lg:p-8 bg-white rounded-md text-black font-medium"
                        :style="{ 'width': isFullScreen ? 'calc(100vw - 16px)' : 'auto', 'margin-left': isFullScreen ? '-192px' : '0' }">
                        <livewire:admin.show-dashboard />  
                              
                    </div>
                </div>
            </div>
        </x-content-design>
    </x-app-layout>

    <x-show-hide-sidebar
        toggleButtonId="toggleButton"
        sidebarContainerId="sidebarContainer"
        dashboardContentId="dashboardContent"
        toggleIconId="toggleIcon"
    />

@elseif (Auth::user()->hasRole('judge'))
    <x-portal>
        <x-user-route-page-name :routeName="'judge.dashboard'" />
            <x-content-design-judge>
            <!-- Content Area -->
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" 
                x-init="
                    window.addEventListener('resize', () => {
                        isFullScreen = (window.innerHeight === screen.height);
                    });
                " 
                class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium w-full">
                
                <div class="relative w-full">
                    <div :class="{ 'w-full h-screen': isFullScreen, 'w-full h-auto': !isFullScreen }">
                        <livewire:admin.judge-portal />
                    </div>
                </div>
            </div>

        </x-content-design-judge>
    </x-portal> 

    <x-show-hide-sidebar
        toggleButtonId="toggleButton"
        sidebarContainerId="sidebarContainer"
        dashboardContentId="dashboardContent"
        toggleIconId="toggleIcon"
    />

    @elseif (Auth::user()->hasAnyRole(['judge', 'judge_chairman']))
    <x-portal>
        <x-user-route-page-name :routeName="'judge.dashboard'" />
            <x-content-design-judge>
            <!-- Content Area -->
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" 
                x-init="
                    window.addEventListener('resize', () => {
                        isFullScreen = (window.innerHeight === screen.height);
                    });
                " 
                class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium w-full">
                
                <div class="relative w-full">
                    <div :class="{ 'w-full h-screen': isFullScreen, 'w-full h-auto': !isFullScreen }">
                        <livewire:admin.judge-portal />
                    </div>
                </div>
            </div>

        </x-content-design-judge>
    </x-portal> 

    <x-show-hide-sidebar
        toggleButtonId="toggleButton"
        sidebarContainerId="sidebarContainer"
        dashboardContentId="dashboardContent"
        toggleIconId="toggleIcon"
    />

@elseif (Auth::user()->hasRole('event_manager'))
    <x-app-layout>
        <x-user-route-page-name :routeName="'event_manager.dashboard'" />
        <x-content-design>
            <!-- Content Area -->
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                window.addEventListener('resize', () => {
                    isFullScreen = (window.innerHeight === screen.height);
                });
                " class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium">
                <div class="relative">
                    <div class="container shadow-lg p-5 sm:p-6 md:p-7 lg:p-8 bg-white rounded-md text-black font-medium"
                        :style="{ 'width': isFullScreen ? 'calc(100vw - 16px)' : 'auto', 'margin-left': isFullScreen ? '-192px' : '0' }">
                      
                    </div>
                </div>
            </div>
        </x-content-design>
    </x-app-layout>

    <x-show-hide-sidebar
        toggleButtonId="toggleButton"
        sidebarContainerId="sidebarContainer"
        dashboardContentId="dashboardContent"
        toggleIconId="toggleIcon"
    />

    @else
    <x-app-layout>
        <x-user-route-page-name :routeName="'staff.dashboard'" />
        <x-content-design>
            <!-- Content Area -->
            <div x-data="{ isFullScreen: (window.innerHeight === screen.height) }" x-init="
                window.addEventListener('resize', () => {
                    isFullScreen = (window.innerHeight === screen.height);
                });
                " class="rounded-md p-2 sm:p-2 md:p-2 lg:p-2 text-black font-medium">
                <div class="relative">
                    <div class="container shadow-lg p-5 sm:p-6 md:p-7 lg:p-8 bg-white rounded-md text-black font-medium"
                        :style="{ 'width': isFullScreen ? 'calc(100vw - 16px)' : 'auto', 'margin-left': isFullScreen ? '-192px' : '0' }">
                      
                    </div>
                </div>
            </div>
        </x-content-design>
    </x-app-layout>

    <x-show-hide-sidebar
        toggleButtonId="toggleButton"
        sidebarContainerId="sidebarContainer"
        dashboardContentId="dashboardContent"
        toggleIconId="toggleIcon"
    />

@endif