<!-- Navigation Links -->
<div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        {{ __('Dashboard') }}
    </x-nav-link>
    
    <x-nav-link :href="route('queues')" :active="request()->routeIs('queues')">
        {{ __('Queues') }}
    </x-nav-link>
    
    <x-nav-link :href="route('reports')" :active="request()->routeIs('reports')">
        {{ __('Reports') }}
    </x-nav-link>
    
    <x-nav-link :href="route('export-reports')" :active="request()->routeIs('export-reports')">
        {{ __('Export Reports') }}
    </x-nav-link>
    
    <!-- Other navigation links -->
</div>

