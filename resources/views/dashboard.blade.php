<x-app-layout>
    @if (Auth::user()->hasRole('admin'))
        <x-user-route-page-name :routeName="'admin.dashboard'" />
    @elseif(Auth::user()->hasRole('event_manager'))
        <x-user-route-page-name :routeName="'event_manager.dashboard'" />
    @elseif(Auth::user()->hasRole('judges'))
        <x-user-route-page-name :routeName="'judge.dashboard'" />
    @else
        <div>You do not have permission to view this page.</div>
    @endif

    <x-content-design>
        <div class="">HI</div>
    </x-content-design>
</x-app-layout>