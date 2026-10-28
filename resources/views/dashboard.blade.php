@extends('layouts.app')

@section('title', 'Executive Telemetry Dashboard')

@section('content')
    <x-ui.page-header
        title="Executive Telemetry Dashboard"
        subtitle="Live operational intelligence for campus water infrastructure"
        icon="fa-chart-line"
    >
        <button type="button" class="btn wm-btn wm-btn--secondary" id="themeToggleBtn" data-theme-toggle>
            <i class="fas fa-circle-half-stroke"></i>
            Toggle Theme
        </button>
        <a href="{{ route('alerts.index') }}" class="btn wm-btn wm-btn--primary">
            <i class="fas fa-bell"></i>
            Open Alerts ({{ $kpi['active_alerts'] }})
        </a>
    </x-ui.page-header>

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <section class="wm-exec-kpi-grid">
            <article class="wm-kpi-card wm-kpi-card--level">
                <p class="wm-kpi-card__label">Average Water Level</p>
                <h3 class="wm-kpi-card__value">{{ number_format($kpi['avg_level'], 1) }}<small>%</small></h3>
                <p class="wm-kpi-card__meta">Across {{ $kpi['connectivity']['total'] }} active tanks</p>
            </article>

            <article class="wm-kpi-card wm-kpi-card--flow">
                <p class="wm-kpi-card__label">Estimated Flow Rate</p>
                <h3 class="wm-kpi-card__value">{{ number_format($kpi['flow_lpm'], 1) }}<small>L/min</small></h3>
                <p class="wm-kpi-card__meta">Calculated from last 90 minutes</p>
            </article>

            <article class="wm-kpi-card wm-kpi-card--quality">
                <p class="wm-kpi-card__label">Water Quality Inputs</p>
                <h3 class="wm-kpi-card__value">{{ $kpi['water_quality']['available'] }}<small>/{{ $kpi['water_quality']['total'] }}</small></h3>
                <p class="wm-kpi-card__meta">Turbidity sensors reporting</p>
            </article>

            <article class="wm-kpi-card wm-kpi-card--alerts">
                <p class="wm-kpi-card__label">Active Alerts</p>
                <h3 class="wm-kpi-card__value">{{ $kpi['active_alerts'] }}</h3>
                <p class="wm-kpi-card__meta">Threshold exceptions requiring action</p>
            </article>

            <article class="wm-kpi-card wm-kpi-card--status">
                <p class="wm-kpi-card__label">Connectivity Status</p>
                <div class="wm-kpi-status-list">
                    <span><i class="dot dot--online"></i>Online {{ $kpi['connectivity']['online'] }}</span>
                    <span><i class="dot dot--warning"></i>Warning {{ $kpi['connectivity']['warning'] }}</span>
                    <span><i class="dot dot--critical"></i>Critical {{ $kpi['connectivity']['critical'] }}</span>
                    <span><i class="dot dot--offline"></i>Offline {{ $kpi['connectivity']['offline'] }}</span>
                </div>
            </article>
        </section>

        <section class="wm-dashboard-grid mt-4">
            <x-ui.card title="Live Telemetry Trends" icon="fa-wave-square" subtitle="Time-series level and volume trends">
                <x-slot:actions>
                    <div class="wm-range-selector" role="group" aria-label="Chart range selector">
                        <button class="wm-range-selector__btn is-active" type="button" data-range="1H">1H</button>
                        <button class="wm-range-selector__btn" type="button" data-range="24H">24H</button>
                        <button class="wm-range-selector__btn" type="button" data-range="7D">7D</button>
                        <button class="wm-range-selector__btn" type="button" data-range="30D">30D</button>
                    </div>
                </x-slot:actions>

                <div class="wm-telemetry-chart-wrap">
                    <canvas id="telemetryTrendChart"></canvas>
                </div>
            </x-ui.card>

            <x-ui.card title="GIS Node View" icon="fa-map-location-dot" subtitle="Interactive installation points with status flyouts">
                <div class="wm-node-map" id="nodeMapPanel">
                    <div class="wm-node-map__grid" aria-hidden="true"></div>
                    @foreach($nodeStatuses as $index => $node)
                        @php
                            $x = 10 + (($index % 4) * 23);
                            $y = 15 + (intdiv($index, 4) * 28);
                            $toneClass = 'status-offline';
                            if ($node['status'] === 'online') {
                                $toneClass = 'status-online';
                            } elseif ($node['status'] === 'warning') {
                                $toneClass = 'status-warning';
                            } elseif ($node['status'] === 'critical') {
                                $toneClass = 'status-critical';
                            }
                        @endphp
                        <button
                            type="button"
                            class="wm-node-map__pin {{ $toneClass }}"
                            style="left: {{ $x }}%; top: {{ $y }}%;"
                            data-node='@json($node)'
                            aria-label="{{ $node['name'] }} status {{ $node['status_label'] }}"
                        ></button>
                    @endforeach

                    <div class="wm-node-map__tooltip" id="nodeMapTooltip" hidden>
                        <p class="wm-node-map__title" data-node-name>Node</p>
                        <p class="wm-node-map__status" data-node-status>Status</p>
                        <p class="wm-node-map__meta" data-node-meta>Last update</p>
                    </div>
                </div>
                <div class="wm-node-legend mt-3">
                    <span><i class="dot dot--online"></i>Online</span>
                    <span><i class="dot dot--warning"></i>Warning</span>
                    <span><i class="dot dot--critical"></i>Critical</span>
                    <span><i class="dot dot--offline"></i>Offline</span>
                </div>
            </x-ui.card>
        </section>

        <x-ui.card title="Historical Sensor Logs" icon="fa-table" subtitle="Filter, triage, and bulk process node telemetry" class="mt-4">
            <div class="wm-filter-bar mb-3">
                <div>
                    <label for="filterDateRange">Date range</label>
                    <select id="filterDateRange" class="form-select form-select-sm" data-log-filter="date-range">
                        <option value="all">All</option>
                        <option value="24h">Last 24 hours</option>
                        <option value="7d">Last 7 days</option>
                        <option value="30d">Last 30 days</option>
                    </select>
                </div>
                <div>
                    <label for="filterLocation">Sensor location</label>
                    <select id="filterLocation" class="form-select form-select-sm" data-log-filter="location">
                        <option value="all">All locations</option>
                        @foreach($readingLogs->pluck('tandon.building_name')->filter()->unique()->values() as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="filterSeverity">Severity</label>
                    <select id="filterSeverity" class="form-select form-select-sm" data-log-filter="severity">
                        <option value="all">All</option>
                        <option value="normal">Normal</option>
                        <option value="warning">Warning</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
            </div>

            <x-ui.data-table
                id="executive-logs-table"
                bulk-action="#"
                :bulk-actions="['export_csv' => 'Export CSV', 'export_pdf' => 'Export PDF', 'mark_read' => 'Batch Mark Read']"
                select-name="reading_ids[]"
            >
                <thead>
                    <tr>
                        <th style="width: 48px;"><input type="checkbox" data-master-select></th>
                        <th>Timestamp</th>
                        <th>Node</th>
                        <th>Location</th>
                        <th class="text-end">Water Height (m)</th>
                        <th class="text-end">Volume (L)</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody id="telemetryLogBody">
                    @forelse($readingLogs as $reading)
                        @php
                            $severity = 'normal';
                            $statusLabel = 'Normal';

                            if ($reading->water_height <= $reading->tandon->height_min || $reading->water_height >= $reading->tandon->height_max) {
                                $severity = 'critical';
                                $statusLabel = 'Critical';
                            } elseif ($reading->water_height <= $reading->tandon->height_warning) {
                                $severity = 'warning';
                                $statusLabel = 'Warning';
                            }
                        @endphp
                        <tr
                            data-log-row
                            data-log-date="{{ $reading->recorded_at->toIso8601String() }}"
                            data-log-location="{{ $reading->tandon->building_name ?? '-' }}"
                            data-log-severity="{{ $severity }}"
                        >
                            <td><input type="checkbox" data-row-select value="{{ $reading->id }}"></td>
                            <td>{{ $reading->recorded_at->format('d M Y H:i') }}</td>
                            <td><strong>{{ $reading->tandon->name }}</strong></td>
                            <td>{{ $reading->tandon->building_name ?? '-' }}</td>
                            <td class="text-end">@num($reading->water_height)</td>
                            <td class="text-end">@num($reading->water_volume * 1000)</td>
                            <td>
                                @if($severity === 'critical')
                                    <x-ui.status-badge tone="danger">{{ $statusLabel }}</x-ui.status-badge>
                                @elseif($severity === 'warning')
                                    <x-ui.status-badge tone="warning">{{ $statusLabel }}</x-ui.status-badge>
                                @else
                                    <x-ui.status-badge tone="success">{{ $statusLabel }}</x-ui.status-badge>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="wm-table-actions justify-content-center">
                                    <a href="{{ route('tandons.show', $reading->tandon) }}" class="btn btn-sm btn-outline-info" title="Open node"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('tandon-readings.show', [$reading->tandon, $reading]) }}" class="btn btn-sm btn-outline-secondary" title="View reading"><i class="fas fa-chart-line"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.empty-state title="No historical readings yet" message="Sensor logs will appear when telemetry starts streaming." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.data-table>

            <div class="wm-pagination-wrap">
                <p class="wm-pagination-meta" id="logCountMeta">Showing 1-{{ min(50, $readingLogs->count()) }} of {{ $readingLogs->count() }} readings</p>
                <div class="wm-pagination-controls">
                    <form class="wm-per-page-form" onsubmit="return false;">
                        <label for="logPerPage">Items per page</label>
                        <select id="logPerPage" class="form-select form-select-sm" data-log-per-page>
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                        </select>
                    </form>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const telemetrySeries = @json($telemetrySeries);
            const chartCanvas = document.getElementById('telemetryTrendChart');
            const rangeButtons = document.querySelectorAll('.wm-range-selector__btn');

            const chartDataByRange = {
                '1H': 1,
                '24H': 24,
                '7D': 24 * 7,
                '30D': 24 * 30,
            };

            const toPoints = (hours) => {
                const slice = telemetrySeries.slice(-hours);
                return {
                    labels: slice.map(item => new Date(item.timestamp).toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })),
                    levels: slice.map(item => item.avg_level),
                    volumes: slice.map(item => item.avg_volume_l),
                };
            };

            const initial = toPoints(24);

            const chart = new Chart(chartCanvas, {
                type: 'line',
                data: {
                    labels: initial.labels,
                    datasets: [
                        {
                            label: 'Avg Water Level (%)',
                            data: initial.levels,
                            borderColor: '#09a07f',
                            backgroundColor: 'rgba(9, 160, 127, 0.16)',
                            borderWidth: 2.5,
                            tension: 0.25,
                            fill: true,
                            yAxisID: 'yLevel',
                        },
                        {
                            label: 'Avg Volume (L)',
                            data: initial.volumes,
                            borderColor: '#f5b700',
                            backgroundColor: 'rgba(245, 183, 0, 0.18)',
                            borderWidth: 2,
                            tension: 0.25,
                            yAxisID: 'yVolume',
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        yLevel: {
                            type: 'linear',
                            position: 'left',
                            beginAtZero: true,
                            suggestedMax: 100,
                            title: { display: true, text: 'Water Level (%)' }
                        },
                        yVolume: {
                            type: 'linear',
                            position: 'right',
                            beginAtZero: true,
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'Volume (L)' }
                        }
                    }
                }
            });

            rangeButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    rangeButtons.forEach((node) => node.classList.remove('is-active'));
                    button.classList.add('is-active');

                    const points = toPoints(chartDataByRange[button.dataset.range] || 24);
                    chart.data.labels = points.labels;
                    chart.data.datasets[0].data = points.levels;
                    chart.data.datasets[1].data = points.volumes;
                    chart.update();
                });
            });

            const pins = document.querySelectorAll('.wm-node-map__pin');
            const tooltip = document.getElementById('nodeMapTooltip');
            const tooltipName = tooltip?.querySelector('[data-node-name]');
            const tooltipStatus = tooltip?.querySelector('[data-node-status]');
            const tooltipMeta = tooltip?.querySelector('[data-node-meta]');

            const showTooltip = (pin) => {
                if (!tooltip || !tooltipName || !tooltipStatus || !tooltipMeta) return;

                const node = JSON.parse(pin.getAttribute('data-node') || '{}');
                tooltipName.textContent = node.name || 'Node';
                tooltipStatus.textContent = `Status: ${node.status_label || '-'}`;
                tooltipMeta.textContent = node.last_seen
                    ? `Last seen: ${new Date(node.last_seen).toLocaleString('id-ID')}`
                    : 'Last seen: No data';

                tooltip.hidden = false;
                tooltip.style.left = pin.style.left;
                tooltip.style.top = pin.style.top;
            };

            pins.forEach((pin) => {
                pin.addEventListener('mouseenter', () => showTooltip(pin));
                pin.addEventListener('focus', () => showTooltip(pin));
            });

            document.getElementById('nodeMapPanel')?.addEventListener('mouseleave', () => {
                if (tooltip) tooltip.hidden = true;
            });

            const logRows = Array.from(document.querySelectorAll('[data-log-row]'));
            const filters = {
                dateRange: document.querySelector('[data-log-filter="date-range"]'),
                location: document.querySelector('[data-log-filter="location"]'),
                severity: document.querySelector('[data-log-filter="severity"]'),
            };
            const perPageSelect = document.querySelector('[data-log-per-page]');
            const countMeta = document.getElementById('logCountMeta');

            const applyLogFilters = () => {
                const now = new Date();
                const dateRange = filters.dateRange?.value ?? 'all';
                const location = filters.location?.value ?? 'all';
                const severity = filters.severity?.value ?? 'all';
                const perPage = parseInt(perPageSelect?.value || '50', 10);

                const filtered = logRows.filter((row) => {
                    const logDate = new Date(row.dataset.logDate);
                    const logLocation = row.dataset.logLocation || '-';
                    const logSeverity = row.dataset.logSeverity || 'normal';

                    if (location !== 'all' && logLocation !== location) return false;
                    if (severity !== 'all' && logSeverity !== severity) return false;

                    if (dateRange !== 'all') {
                        const diffHours = (now.getTime() - logDate.getTime()) / 3600000;
                        if (dateRange === '24h' && diffHours > 24) return false;
                        if (dateRange === '7d' && diffHours > 24 * 7) return false;
                        if (dateRange === '30d' && diffHours > 24 * 30) return false;
                    }

                    return true;
                });

                logRows.forEach((row) => {
                    row.style.display = 'none';
                });

                filtered.slice(0, perPage).forEach((row) => {
                    row.style.display = '';
                });

                const showing = Math.min(perPage, filtered.length);
                if (countMeta) {
                    countMeta.textContent = filtered.length > 0
                        ? `Showing 1-${showing} of ${filtered.length} readings`
                        : 'Showing 0-0 of 0 readings';
                }
            };

            Object.values(filters).forEach((select) => {
                select?.addEventListener('change', applyLogFilters);
            });
            perPageSelect?.addEventListener('change', applyLogFilters);

            applyLogFilters();
        })();
    </script>
@endsection
