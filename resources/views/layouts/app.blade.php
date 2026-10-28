<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <title>@yield('title', 'Water Monitoring System')</title>
    @yield('styles')
</head>
<body class="wm-app-body">
    @php
        $activeAlertsCount = \App\Models\Alert::whereNull('resolved_at')->count();
    @endphp

    <div class="wm-shell" data-shell-root>
        <aside class="wm-sidebar" data-sidebar aria-label="Primary">
            <a class="wm-sidebar__brand" href="{{ route('dashboard') }}">
                <span class="wm-sidebar__brand-icon"><i class="fas fa-droplet"></i></span>
                Water Monitor
            </a>

            <div>
                <p class="wm-sidebar__label">Monitoring</p>
                <nav class="wm-sidebar__nav">
                    <a href="{{ route('dashboard') }}" class="wm-sidebar__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                        <i class="fas fa-gauge-high"></i> Dashboard
                    </a>
                    <a href="{{ route('tandons.index') }}" class="wm-sidebar__link {{ request()->routeIs('tandons.*', 'tandon-readings.*') ? 'is-active' : '' }}">
                        <i class="fas fa-water"></i> Water Tanks
                    </a>
                    <a href="{{ route('alerts.index') }}" class="wm-sidebar__link {{ request()->routeIs('alerts.*') ? 'is-active' : '' }}">
                        <i class="fas fa-bell"></i> Alerts
                        @if($activeAlertsCount > 0)
                            <span class="wm-sidebar__badge">{{ $activeAlertsCount }}</span>
                        @endif
                    </a>
                </nav>
            </div>

            <div class="wm-sidebar__footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn wm-btn--sidebar w-100">
                        <i class="fas fa-right-from-bracket"></i> Log out
                    </button>
                </form>
            </div>
        </aside>
        <div class="wm-backdrop" data-sidebar-backdrop></div>

        <div class="wm-main">
            <header class="wm-topbar">
                <button class="btn wm-icon-btn wm-topbar__menu" type="button" data-sidebar-toggle aria-label="Open navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <p class="wm-topbar__title">@yield('title', 'Water Monitoring System')</p>

                <div class="wm-topbar__actions">
                    <a href="{{ route('alerts.index') }}" class="wm-alert-chip {{ $activeAlertsCount > 0 ? 'has-alerts' : '' }}">
                        <i class="fas fa-bell"></i>
                        <span>{{ $activeAlertsCount }} active {{ \Illuminate\Support\Str::plural('alert', $activeAlertsCount) }}</span>
                        @if($activeAlertsCount === 0)
                            <span class="visually-hidden">No active alerts</span>
                        @endif
                    </a>
                    <button type="button" class="btn wm-icon-btn" data-theme-toggle aria-label="Toggle dark mode">
                        <i class="fas fa-circle-half-stroke"></i>
                    </button>
                </div>
            </header>

            <main class="wm-main__content">
                @yield('content')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
