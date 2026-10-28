<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
    <title>Water Monitoring System - Politeknik Negeri Madura</title>
</head>
<body>
    @php $signedIn = auth()->check() || session('user'); @endphp

    <nav class="navbar navbar-expand-md wm-public-nav">
        <div class="container">
            <a class="navbar-brand" href="#top">
                <span class="wm-sidebar__brand-icon"><i class="fas fa-droplet"></i></span>
                Water Monitor
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav ms-auto align-items-md-center gap-md-1 pt-3 pt-md-0">
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#monitoring">Live status</a></li>
                    <li class="nav-item ms-md-2 mt-2 mt-md-0">
                        @if($signedIn)
                            <a class="btn btn-primary btn-sm" href="{{ route('dashboard') }}">Open dashboard</a>
                        @else
                            <a class="btn btn-primary btn-sm" href="{{ route('login') }}">Log in</a>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="wm-hero" id="top">
        <div class="container">
            <span class="wm-hero__eyebrow"><i class="fas fa-satellite-dish"></i> Smart water management</span>
            <h1>Automated water control for campus</h1>
            <p>Monitor tank levels, control pumps and respond to threshold alerts across every building, from one dashboard.</p>
            <div class="d-flex flex-wrap gap-2 mt-4">
                @if($signedIn)
                    <a class="btn btn-primary" href="{{ route('dashboard') }}">Open dashboard</a>
                @else
                    <a class="btn btn-primary" href="{{ route('login') }}">Get started</a>
                @endif
                <a class="btn btn-outline-light" href="#monitoring" style="min-height: 3rem; padding-inline: 1.5rem;">See live status</a>
            </div>
        </div>
    </header>

    <section class="wm-public-section" id="features">
        <div class="container">
            <h2 class="wm-section-heading">Everything you need to run the network</h2>
            <p class="wm-section-lead">Built for facilities teams working at a desk or in the field.</p>
            <div class="row g-3 g-lg-4">
                @foreach([
                    ['fa-gauge-high', 'Live monitoring', 'Continuous level and volume tracking for every tank and building.'],
                    ['fa-power-off', 'Pump control', 'Switch pumps on or off remotely, with confirmation and status feedback.'],
                    ['fa-bell', 'Threshold alerts', 'Get notified when levels drop below warning or minimum limits.'],
                    ['fa-chart-line', 'Usage analytics', 'Daily and monthly consumption trends to plan supply and spot leaks.'],
                    ['fa-building', 'Multi-building', 'Central and building tanks organised in one hierarchy.'],
                    ['fa-shield-halved', 'Secure access', 'Authenticated operations with a clear record of every action.'],
                ] as [$icon, $title, $desc])
                    <div class="col-md-6 col-lg-4">
                        <article class="wm-feature">
                            <span class="wm-feature__icon"><i class="fas {{ $icon }}"></i></span>
                            <h3>{{ $title }}</h3>
                            <p>{{ $desc }}</p>
                        </article>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="wm-public-section wm-public-section--alt" id="monitoring">
        <div class="container">
            <h2 class="wm-section-heading">Campus water status</h2>
            <p class="wm-section-lead">Latest reading from each monitoring point.</p>
            <div class="row g-3 g-lg-4">
                @forelse($tandons as $tandon)
                    @php
                        $latest = $tandon->readings->first();
                        $pct = null;
                        $tone = 'neutral';
                        $fill = 'offline';
                        if ($latest && $tandon->height_max > $tandon->height_min) {
                            $pct = max(0, min(100, round(($latest->water_height - $tandon->height_min) / ($tandon->height_max - $tandon->height_min) * 100)));
                            [$tone, $fill] = $pct <= 20 ? ['danger', 'critical'] : ($pct <= 50 ? ['warning', 'warning'] : ['success', 'online']);
                        }
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <article class="wm-feature">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <h3 class="mb-0">{{ $tandon->building_name ?: $tandon->name }}</h3>
                                @if($pct !== null)
                                    <x-ui.status-badge :tone="$tone" size="sm">{{ $pct }}%</x-ui.status-badge>
                                @else
                                    <x-ui.status-badge tone="neutral" size="sm">No data</x-ui.status-badge>
                                @endif
                            </div>
                            <div class="wm-level">
                                <div class="wm-level__track"><div class="wm-level__fill wm-level__fill--{{ $fill }}" style="width: {{ $pct ?? 0 }}%"></div></div>
                            </div>
                            <p class="small mt-3">
                                @if($latest)
                                    Height {{ number_format($latest->water_height, 2) }} m &middot; Volume {{ number_format($latest->water_volume, 2) }} m³
                                @else
                                    Waiting for first reading
                                @endif
                            </p>
                        </article>
                    </div>
                @empty
                    <div class="col-12">
                        <x-ui.empty-state icon="fa-water" title="No monitoring points yet" message="Tanks will appear here once they are added." />
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <footer class="wm-public-footer">
        <div class="container d-flex flex-wrap justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} Water Monitoring System, Politeknik Negeri Madura</span>
            <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none" data-theme-toggle>Toggle dark mode</button>
        </div>
    </footer>
</body>
</html>
