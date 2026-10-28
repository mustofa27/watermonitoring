<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Water Monitoring System')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Source+Serif+4:wght@500;700&display=swap" rel="stylesheet">

    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="wm-app-body" data-theme="light">
    @php
        $activeAlertsCount = \App\Models\Alert::whereNull('resolved_at')->count();
    @endphp

    <div class="wm-bg-orb wm-bg-orb--one"></div>
    <div class="wm-bg-orb wm-bg-orb--two"></div>

    <div class="wm-shell" data-shell-root>
        <aside class="wm-sidebar" data-sidebar>
            <a class="wm-sidebar__brand" href="{{ route('dashboard') }}">
                <i class="fas fa-droplet"></i>
                <span>Water Monitor</span>
            </a>

            <nav class="wm-sidebar__nav">
                <a href="{{ route('dashboard') }}" class="wm-sidebar__link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    Dashboard
                </a>
                <a href="{{ route('tandons.index') }}" class="wm-sidebar__link {{ request()->routeIs('tandons.*') ? 'is-active' : '' }}">
                    <i class="fas fa-water"></i>
                    Water Tanks
                </a>
                <a href="{{ route('alerts.index') }}" class="wm-sidebar__link {{ request()->routeIs('alerts.*') ? 'is-active' : '' }}">
                    <i class="fas fa-triangle-exclamation"></i>
                    Alerts
                    @if($activeAlertsCount > 0)
                        <span class="wm-sidebar__badge">{{ $activeAlertsCount }}</span>
                    @endif
                </a>
            </nav>

            <div class="wm-sidebar__footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn wm-btn wm-btn--ghost w-100">
                        <i class="fas fa-right-from-bracket"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="wm-main">
            <header class="wm-topbar">
                <button class="wm-topbar__menu" type="button" data-sidebar-toggle aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="wm-topbar__status">
                    <span class="wm-topbar__chip">
                        <i class="fas fa-satellite-dish"></i>
                        Telemetry Active
                    </span>
                    <a href="{{ route('alerts.index') }}" class="wm-topbar__chip wm-topbar__chip--alert">
                        <i class="fas fa-bell"></i>
                        {{ $activeAlertsCount }} Active Alerts
                    </a>
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
