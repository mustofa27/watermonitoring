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
<body class="wm-app-body">
    @php
        $activeAlertsCount = \App\Models\Alert::whereNull('resolved_at')->count();
    @endphp

    <div class="wm-bg-orb wm-bg-orb--one"></div>
    <div class="wm-bg-orb wm-bg-orb--two"></div>

    <nav class="navbar navbar-expand-lg wm-nav sticky-top">
        <div class="container">
            <a class="navbar-brand wm-nav__brand" href="{{ route('dashboard') }}">
                <i class="fas fa-droplet"></i>
                Water Monitor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#wm-navbar" aria-controls="wm-navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="wm-navbar">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link wm-nav__link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link wm-nav__link" href="{{ route('tandons.index') }}">Water Tanks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link wm-nav__link" href="{{ route('alerts.index') }}">
                            Alerts
                            @if($activeAlertsCount > 0)
                                <span class="wm-nav__alert-dot">{{ $activeAlertsCount }}</span>
                            @endif
                        </a>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-sm wm-btn wm-btn--ghost">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="pb-5">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>
