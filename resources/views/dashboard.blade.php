@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <x-ui.page-header
        title="Water Usage Dashboard"
        subtitle="Real-time monitoring of water usage across all water tanks"
        icon="fa-chart-pie"
    />

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <div class="wm-stat-grid mb-4">
            <article class="wm-stat-card">
                <p class="wm-stat-card__meta">Total Water Usage</p>
                <p class="wm-stat-card__value">{{ number_format($totalUsage, 2, ',', '.') }} <small>m³</small></p>
                <p class="wm-stat-card__meta mb-0">All-time usage recorded</p>
            </article>
            <article class="wm-stat-card">
                <p class="wm-stat-card__meta">Today's Usage</p>
                <p class="wm-stat-card__value">{{ number_format($todayUsage, 2, ',', '.') }} <small>m³</small></p>
                <p class="wm-stat-card__meta mb-0">{{ now()->format('d M Y') }}</p>
            </article>
            <article class="wm-stat-card">
                <p class="wm-stat-card__meta">Total Water Tanks</p>
                <p class="wm-stat-card__value">{{ count($tandons) }}</p>
                <p class="wm-stat-card__meta mb-0">Active water tanks</p>
            </article>
        </div>

        <x-ui.card title="Water Usage by Water Tank (This Month)" icon="fa-list" class="mb-4">
            <x-ui.data-table id="dashboard-tandon-usage">
                <thead>
                    <tr>
                        <th>Water Tank Name</th>
                        <th>Type</th>
                        <th>Building</th>
                        <th class="text-center">Level (%)</th>
                        <th class="text-center">Pump</th>
                        <th class="text-end">This Month (m³)</th>
                        <th class="text-end">Total Usage (m³)</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tandonUsage as $usage)
                        <tr>
                            <td><strong>{{ $usage['tandon']->name }}</strong></td>
                            <td><x-ui.status-badge tone="neutral">{{ $usage['tandon']->type }}</x-ui.status-badge></td>
                            <td>{{ $usage['tandon']->building_name ?? '-' }}</td>
                            <td class="text-center">
                                @if($usage['height_percent'] !== null)
                                    <strong>{{ $usage['height_percent'] }}%</strong>
                                    <div class="small text-muted">{{ $usage['water_height'] }} m</div>
                                @else
                                    <span class="text-muted small">No data</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($usage['tandon']->pump_status === 1)
                                    <x-ui.status-badge tone="success">ON</x-ui.status-badge>
                                @elseif($usage['tandon']->pump_status === 0)
                                    <x-ui.status-badge tone="neutral">OFF</x-ui.status-badge>
                                @else
                                    <x-ui.status-badge tone="warning">UNKNOWN</x-ui.status-badge>
                                @endif
                            </td>
                            <td class="text-end"><strong>{{ number_format($usage['this_month'], 2, ',', '.') }}</strong></td>
                            <td class="text-end"><strong>{{ number_format($usage['total_usage'], 2, ',', '.') }}</strong></td>
                            <td class="text-center">
                                <div class="wm-table-actions">
                                    <form method="POST" action="{{ route('tandons.pump-on', $usage['tandon']) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-sm {{ $usage['tandon']->pump_status === 1 ? 'btn-danger' : 'btn-success' }}"
                                            title="{{ $usage['tandon']->pump_status === 1 ? 'Turn Pump OFF' : 'Turn Pump ON' }}"
                                            onclick="return confirm('{{ $usage['tandon']->pump_status === 1 ? 'Turn OFF' : 'Turn ON' }} pump for this water tank?')"
                                        >
                                            <i class="fas fa-power-off"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('tandon-readings.index', $usage['tandon']) }}" class="btn btn-sm btn-outline-secondary" title="View Readings">
                                        <i class="fas fa-chart-line"></i>
                                    </a>
                                    <a href="{{ route('tandons.show', $usage['tandon']) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.empty-state title="No water tanks available" message="Create a water tank to get started." icon="fa-water">
                                    <a href="{{ route('tandons.index') }}" class="btn wm-btn wm-btn--secondary">Manage Water Tanks</a>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.data-table>
        </x-ui.card>

        @php
            $usagesForChart = $recentUsages->sortBy('usage_date')->values();
            $chartDates = $usagesForChart->pluck('usage_date')->map(fn($d) => $d->format('d M Y'))->unique()->values();
            $chartTandonNames = $usagesForChart->pluck('tandon.name')->unique()->values();
            $chartPalette = ['#0d6efd','#198754','#ffc107','#dc3545','#6610f2','#0dcaf0','#fd7e14','#20c997','#d63384','#6c757d'];
            $chartDatasets = [];
            foreach ($chartTandonNames as $i => $tandonName) {
                $dataPoints = [];
                foreach ($chartDates as $date) {
                    $match = $usagesForChart->first(fn($u) => $u->tandon->name === $tandonName && $u->usage_date->format('d M Y') === $date);
                    $dataPoints[] = $match ? round($match->volume_used * 1000, 2) : 0;
                }
                $color = $chartPalette[$i % count($chartPalette)];
                $chartDatasets[] = [
                    'label' => $tandonName,
                    'data' => $dataPoints,
                    'backgroundColor' => $color . 'aa',
                    'borderColor' => $color,
                    'borderWidth' => 1.5,
                ];
            }
        @endphp

        <div class="row g-4">
            <div class="col-lg-8">
                <x-ui.card title="Recent Water Usage Records" icon="fa-history" class="h-100">
                    <x-ui.data-table id="dashboard-recent-usage">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Water Tank</th>
                                <th class="text-end">Volume Used (L)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentUsages as $usage)
                                <tr>
                                    <td><strong>{{ $usage->usage_date->format('d M Y') }}</strong></td>
                                    <td><a href="{{ route('tandons.show', $usage->tandon) }}">{{ $usage->tandon->name }}</a></td>
                                    <td class="text-end"><strong>@num($usage->volume_used * 1000)</strong></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <x-ui.empty-state title="No usage records yet" message="Water usage history will appear here once readings are captured." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </x-ui.data-table>
                </x-ui.card>
            </div>

            <div class="col-lg-4">
                <x-ui.card title="Quick Actions" icon="fa-bolt" class="mb-4">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tandons.index') }}" class="btn wm-btn wm-btn--primary">Manage Water Tanks</a>
                        <a href="{{ route('tandons.create') }}" class="btn wm-btn wm-btn--secondary">Add New Water Tank</a>
                    </div>
                </x-ui.card>

                <x-ui.card title="Water Usage History Chart" icon="fa-chart-column" subtitle="Last {{ $usagesForChart->count() }} record(s)">
                    @if($usagesForChart->isNotEmpty())
                        <div style="height: 300px;">
                            <canvas id="usageHistoryChart"></canvas>
                        </div>
                    @else
                        <x-ui.empty-state icon="fa-chart-bar" title="No chart data" message="No usage records to visualize yet." />
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const labels = @json($chartDates);
            const datasets = @json($chartDatasets);

            if (!labels.length) return;

            new Chart(document.getElementById('usageHistoryChart'), {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ` ${ctx.dataset.label}: ${ctx.formattedValue} L`
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Volume Used (L)' }
                        }
                    }
                }
            });
        })();
    </script>
@endsection
