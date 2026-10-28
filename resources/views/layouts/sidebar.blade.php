@php
    $user = Auth::user();
    $displayName = $user->name ?: trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
    $initials = Str::of($displayName)
        ->explode(' ')
        ->filter()
        ->take(2)
        ->map(fn ($part) => Str::upper(Str::substr($part, 0, 1)))
        ->implode('');
@endphp

{{-- Backdrop: mobile drawer only --}}
<div
    x-show="open"
    x-cloak
    @click="open = false"
    class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
    aria-hidden="true"
></div>

<aside
    id="app-sidebar"
    class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-gray-200 bg-white transition-all duration-300 ease-in-out lg:translate-x-0"
    :class="open ? 'is-drawer-open' : ''"
    aria-label="{{ __('Main navigation') }}"
>
    {{-- Brand --}}
    <div class="sidebar-rail-center flex h-16 shrink-0 items-center gap-3 border-b border-gray-200 px-4">
        <a
            href="{{ route('dashboard') }}"
            class="flex min-w-0 items-center gap-3 rounded-md focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
            :title="collapsed ? {{ Illuminate\Support\Js::from(config('app.name', 'Action Hub')) }} : null"
        >
            <x-application-logo class="h-8 w-8 shrink-0 fill-current text-indigo-600" />
            <span class="sidebar-hide-rail truncate text-sm font-semibold text-gray-800">
                {{ config('app.name', 'Action Hub') }}
            </span>
        </a>
    </div>

    {{-- Collapse / expand: icon-only, centred on the sidebar's top edge (desktop only) --}}
    <button
        type="button"
        @click="toggleRail()"
        class="absolute -right-3 top-5 z-10 hidden h-6 w-6 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-500 shadow-sm transition-colors duration-150 hover:bg-gray-50 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 lg:flex"
        x-bind:aria-expanded="(! collapsed).toString()"
        :title="collapsed ? {{ Illuminate\Support\Js::from(__('Expand sidebar')) }} : {{ Illuminate\Support\Js::from(__('Collapse sidebar')) }}"
        :aria-label="collapsed ? {{ Illuminate\Support\Js::from(__('Expand sidebar')) }} : {{ Illuminate\Support\Js::from(__('Collapse sidebar')) }}"
    >
        <svg class="h-3.5 w-3.5 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="M14.5 6.5L9 12l5.5 5.5" />
        </svg>
    </button>

    {{-- Links --}}
    <nav class="flex-1 space-y-1 overflow-y-auto overflow-x-hidden px-3 py-4">
        <a href="{{ route('dashboard') }}"
           :title="collapsed ? {{ Illuminate\Support\Js::from(__('Dashboard')) }} : null"
           class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
        >
            <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" />
                    <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" />
                    <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" />
                    <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" />
                </svg>
            </span>
            <span class="sidebar-hide-rail truncate">{{ __('Dashboard') }}</span>
        </a>





        <x-sidebar-section label="{{ __('Organization') }}" />

        @can('view companies')
            <a href="{{ route('companies.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Company')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('companies.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4.5 20.5V5.5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v15" />
                        <path d="M13.5 20.5V9.5h5a1 1 0 0 1 1 1v10" />
                        <path d="M3 20.5h18" />
                        <path d="M7.5 8h1.5M7.5 11.5h1.5M7.5 15h1.5" />
                        <path d="M16 13h1M16 16.5h1" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Company') }}</span>
            </a>
        @endcan

        @can('view departments')
            <a href="{{ route('departments.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Department')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('departments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="9" y="3.5" width="6" height="4.5" rx="1" />
                        <rect x="3" y="15.5" width="6" height="4.5" rx="1" />
                        <rect x="15" y="15.5" width="6" height="4.5" rx="1" />
                        <path d="M12 8v2.5M6 15.5v-2.5h12v2.5" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Department') }}</span>
            </a>
        @endcan

        @can('view groups')
            <a href="{{ route('groups.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Group')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('groups.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3.5" y="4.5" width="17" height="15" rx="3.5" />
                        <circle cx="8.5" cy="12" r="1.35" />
                        <circle cx="12" cy="12" r="1.35" />
                        <circle cx="15.5" cy="12" r="1.35" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Group') }}</span>
            </a>
        @endcan

        <x-sidebar-section label="{{ __('People and  Access') }}" />

        @can('view users')
            <a href="{{ route('users.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Users')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('users.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9.5" cy="8.5" r="3.5" />
                        <path d="M3.5 20.2c0-3.3 2.7-6 6-6s6 2.7 6 6" />
                        <path d="M16.2 6.4a3.5 3.5 0 0 1 0 6.4" />
                        <path d="M17.6 14.6c1.8 1 2.9 2.8 2.9 4.8" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Users') }}</span>
            </a>
        @endcan

        @can('view roles')
            <a href="{{ route('roles.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Roles')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('roles.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3.25l7 2.5v6.1c0 4.05-2.9 7.3-7 8.65-4.1-1.35-7-4.6-7-8.65v-6.1l7-2.5z" />
                        <path d="M9.25 12l1.9 1.9 3.6-3.9" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Roles') }}</span>
            </a>
        @endcan

        @can('view task categories')
            <a href="{{ route('task-categories.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Task categories')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('task-categories.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12.6 3.5H20a.5.5 0 0 1 .5.5v7.4a2 2 0 0 1-.6 1.4l-7.6 7.6a2 2 0 0 1-2.8 0l-5.4-5.4a2 2 0 0 1 0-2.8l7.6-7.6a2 2 0 0 1 1.4-.6z" />
                        <circle cx="16.5" cy="7.5" r="1.25" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Task categories') }}</span>
            </a>
        @endcan

        @can('view tasks')
            <a href="{{ route('tasks.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Tasks')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('tasks.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3.5" y="3.5" width="17" height="17" rx="4" />
                        <path d="M8.5 12.4l2.4 2.4 4.6-5" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Tasks') }}</span>
            </a>
        @endcan

        @can('view projects')
            <a href="{{ route('projects.index') }}"
               :title="collapsed ? {{ Illuminate\Support\Js::from(__('Projects')) }} : null"
               class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('projects.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3.5 7.5a2 2 0 0 1 2-2h3l2 2.5h8a2 2 0 0 1 2 2v8.5a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2V7.5z" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Projects') }}</span>
            </a>
        @endcan
    </nav>

    {{-- Account --}}
    <div class="shrink-0 space-y-1 border-t border-gray-200 p-3">
        <div
            class="sidebar-rail-center flex items-center gap-3 rounded-lg px-1 py-1"
            :title="collapsed ? {{ Illuminate\Support\Js::from($displayName) }} : null"
        >
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-semibold text-indigo-700">
                {{ $initials }}
            </span>
            <span class="sidebar-hide-rail min-w-0">
                <span class="block truncate text-sm font-medium text-gray-800">{{ $displayName }}</span>
                <span class="block truncate text-xs text-gray-500">{{ $user->email }}</span>
            </span>
        </div>

        <a href="{{ route('profile.edit') }}"
           :title="collapsed ? {{ Illuminate\Support\Js::from(__('Profile')) }} : null"
           class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 {{ request()->routeIs('profile.edit') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}"
        >
            <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8.5" r="3.5" />
                    <path d="M5.5 20.2c0-3.6 2.9-6.5 6.5-6.5s6.5 2.9 6.5 6.5" />
                </svg>
            </span>
            <span class="sidebar-hide-rail truncate">{{ __('Profile') }}</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a
                href="{{ route('logout') }}"
                onclick="event.preventDefault(); this.closest('form').submit();"
                :title="collapsed ? {{ Illuminate\Support\Js::from(__('Log Out')) }} : null"
                class="sidebar-rail-center group relative flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-1 text-gray-600 hover:bg-gray-100 hover:text-gray-900"
            >
                <span class="shrink-0 [&>svg]:h-5 [&>svg]:w-5" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.5 8.5v-2a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h5.5a2 2 0 0 0 2-2v-2" />
                        <path d="M10.5 12h9.5" />
                        <path d="M17.5 9l3 3-3 3" />
                    </svg>
                </span>
                <span class="sidebar-hide-rail truncate">{{ __('Log Out') }}</span>
            </a>
        </form>
    </div>
</aside>