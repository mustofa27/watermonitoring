<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Water Monitoring System - Politeknik Negeri Madura</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Source+Serif+4:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="wm-marketing-page" style="background: var(--wm-bg);">
    <div class="wm-bg-orb wm-bg-orb--one"></div>
    <div class="wm-bg-orb wm-bg-orb--two"></div>

    <nav class="navbar wm-nav" data-nav-root>
        <div class="container">
            <a class="navbar-brand wm-nav__brand" href="#top"><i class="fas fa-droplet"></i> Water Monitor</a>
            <button class="wm-nav__toggle" type="button" data-nav-toggle aria-controls="homeNav" aria-expanded="false" aria-label="Toggle navigation">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="wm-nav__menu" id="homeNav" data-nav-menu>
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 mb-0">
                    <li class="nav-item"><a class="nav-link wm-nav__link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link wm-nav__link" href="#monitoring">Monitoring</a></li>
                    @if(session('user'))
                        <li class="nav-item"><a class="btn wm-btn wm-btn--primary" href="{{ route('tandons.index') }}">Dashboard</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn wm-btn wm-btn--secondary">Logout</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item"><a class="btn wm-btn wm-btn--primary" href="{{ route('login') }}">Login</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <header class="container py-5" id="top">
        <div class="wm-card overflow-hidden" style="background: linear-gradient(145deg, #102a25 20%, #156c61 80%); color: #f4fffc; border: 0;">
            <div class="wm-card__body p-5">
                <p class="text-uppercase mb-2" style="letter-spacing: 0.15em; opacity: 0.8;">Smart Water Management</p>
                <h1 style="font-family: 'Source Serif 4', serif; font-size: clamp(2rem, 4.4vw, 3.2rem);">Automated Water Control System</h1>
                <p class="mb-4" style="max-width: 650px; opacity: 0.9;">A centralized telemetry platform for automated monitoring, usage analytics, and intelligent pump control across campus water infrastructure.</p>
                @if(!session('user'))
                    <a class="btn wm-btn wm-btn--primary" href="{{ route('login') }}">Get Started</a>
                @else
                    <a class="btn wm-btn wm-btn--primary" href="{{ route('tandons.index') }}">Go to Dashboard</a>
                @endif
            </div>
        </div>
    </header>

    <section class="container py-4" id="features">
        <h2 class="mb-4" style="font-family: 'Source Serif 4', serif;">Automation Features</h2>
        <div class="row g-3">
            @foreach([
                ['fa-robot', 'Automated Monitoring', 'Continuous level tracking with proactive threshold handling.'],
                ['fa-cog', 'Smart Pump Control', 'Automatic pump orchestration based on live conditions.'],
                ['fa-bell', 'Intelligent Alerts', 'Actionable alerts with operational severity context.'],
                ['fa-chart-line', 'Predictive Analytics', 'Usage trend analysis for future planning.'],
                ['fa-building', 'Multi-Building Control', 'Single-pane operations for distributed tanks.'],
                ['fa-shield-alt', 'Secure Automation', 'Controlled workflows with auditable actions.'],
            ] as [$icon, $title, $desc])
                <div class="col-md-6 col-lg-4">
                    <article class="wm-card h-100">
                        <div class="wm-card__body">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="wm-badge wm-badge--info"><i class="fas {{ $icon }}"></i></span>
                                <h3 class="h6 mb-0">{{ $title }}</h3>
                            </div>
                            <p class="mb-0 text-muted">{{ $desc }}</p>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <section class="container py-4" id="monitoring">
        <h2 class="mb-4" style="font-family: 'Source Serif 4', serif;">Campus Water Monitoring</h2>
        <div class="row g-3">
            @forelse($tandons as $tandon)
                @php
                    $latestReading = $tandon->readings->first();
                    $capacityPct = null;
                    $capacityTone = 'neutral';
                    if ($latestReading && $tandon->height_max > $tandon->height_min) {
                        $capacityPct = round(($latestReading->water_height - $tandon->height_min) / ($tandon->height_max - $tandon->height_min) * 100);
                        $capacityPct = max(0, min(100, $capacityPct));
                        $capacityTone = $capacityPct <= 20 ? 'danger' : ($capacityPct <= 50 ? 'warning' : 'info');
                    }
                @endphp
                <div class="col-md-6 col-lg-4">
                    <article class="wm-card h-100">
                        <div class="wm-card__body">
                            <h3 class="h6 mb-2">{{ $tandon->building_name ?: $tandon->name }}</h3>
                            <x-ui.status-badge tone="success">Active</x-ui.status-badge>
                            @if($capacityPct !== null)
                                <x-ui.status-badge :tone="$capacityTone">{{ $capacityPct }}% Capacity</x-ui.status-badge>
                            @else
                                <x-ui.status-badge tone="neutral">No data</x-ui.status-badge>
                            @endif
                            @if($latestReading)
                                <p class="small text-muted mt-2 mb-0">Height: {{ number_format($latestReading->water_height, 2) }} m | Volume: {{ number_format($latestReading->water_volume, 2) }} m³</p>
                            @endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <x-ui.empty-state title="No water tanks found" message="No monitoring points are available yet." />
                </div>
            @endforelse
        </div>
    </section>

    <footer class="container py-4 text-center text-muted">
        Copyright © Water Monitoring System 2026
    </footer>
</body>
</html>
