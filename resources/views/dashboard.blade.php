@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php
        $statusTone = ['online' => 'success', 'warning' => 'warning', 'critical' => 'danger', 'offline' => 'neutral'];
        $nodes = $nodeStatuses->values();
        $mapCols = 2;
        $mapRows = max(1, (int) ceil($nodes->count() / $mapCols));
        $mapHeight = max(200, $mapRows * 64 + 40);
        $avgTone = $kpi['avg_level'] <= 20 ? 'danger' : ($kpi['avg_level'] <= 40 ? 'warning' : 'success');
        $conn = $kpi['connectivity'];
    @endphp

    <x-ui.page-header
        title="Dashboard"
        subtitle="Live status of campus water tanks and sensor nodes"
        icon="fa-gauge-high"
    >
        <a href="{{ route('tandons.create') }}" class="btn btn-secondary"><i class="fas fa-plus"></i> Add tank</a>
        <a href="{{ route('alerts.index') }}" class="btn btn-primary"><i class="fas fa-bell"></i> View alerts</a>
    </x-ui.page-header>

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <section class="wm-kpi-grid" aria-label="Key metrics">
            <article class="wm-kpi">
                <div class="wm-kpi__top">
                    <p class="wm-kpi__label">Average water level</p>
                    <span class="wm-kpi__icon wm-kpi__icon--{{ $avgTone }}"><i class="fas fa-water"></i></span>
                </div>
                <p class="wm-kpi__value">{{ number_format($kpi['avg_level'], 1, ',', '.') }}<span class="wm-kpi__unit">%</span></p>
                <p class="wm-kpi__meta">Across {{ $conn['total'] }} {{ \Illuminate\Support\Str::plural('tank', $conn['total']) }}</p>
            </article>

            <article class="wm-kpi">
                <div class="wm-kpi__top">
                    <p class="wm-kpi__label">Estimated flow rate</p>
                    <span class="wm-kpi__icon"><i class="fas fa-arrows-rotate"></i></span>
                </div>
                <p class="wm-kpi__value">{{ number_format($kpi['flow_lpm'], 1, ',', '.') }}<span class="wm-kpi__unit">L/min</span></p>
                <p class="wm-kpi__meta">Draw-down over the last 90 minutes</p>
            </article>

            <article class="wm-kpi">
                <div class="wm-kpi__top">
                    <p class="wm-kpi__label">Usage today</p>
                    <span class="wm-kpi__icon"><i class="fas fa-droplet"></i></span>
                </div>
                <p class="wm-kpi__value">{{ number_format($todayUsage, 2, ',', '.') }}<span class="wm-kpi__unit">m³</span></p>
                <p class="wm-kpi__meta">{{ number_format($totalUsage, 1, ',', '.') }} m³ all time</p>
            </article>

            <article class="wm-kpi">
                <div class="wm-kpi__top">
                    <p class="wm-kpi__label">Active alerts</p>
                    <span class="wm-kpi__icon wm-kpi__icon--{{ $kpi['active_alerts'] > 0 ? 'danger' : 'success' }}"><i class="fas fa-triangle-exclamation"></i></span>
                </div>
                <p class="wm-kpi__value">{{ $kpi['active_alerts'] }}</p>
                <p class="wm-kpi__meta">
                    @if($kpi['active_alerts'] > 0)
                        <a href="{{ route('alerts.index') }}">Review unresolved alerts</a>
                    @else
                        All thresholds within range
                    @endif
                </p>
            </article>

            <article class="wm-kpi">
                <div class="wm-kpi__top">
                    <p class="wm-kpi__label">Node connectivity</p>
                    <span class="wm-kpi__icon wm-kpi__icon--{{ $conn['offline'] > 0 ? 'warning' : 'success' }}"><i class="fas fa-satellite-dish"></i></span>
                </div>
                <p class="wm-kpi__value">{{ $conn['total'] - $conn['offline'] }}<span class="wm-kpi__unit">/ {{ $conn['total'] }} reporting</span></p>
                <div class="wm-kpi__tally">
                    <span><i class="dot dot--online"></i>Online {{ $conn['online'] }}</span>
                    <span><i class="dot dot--warning"></i>Warning {{ $conn['warning'] }}</span>
                    <span><i class="dot dot--critical"></i>Critical {{ $conn['critical'] }}</span>
                    <span><i class="dot dot--offline"></i>Offline {{ $conn['offline'] }}</span>
                </div>
            </article>

            <article class="wm-kpi">
                <div class="wm-kpi__top">
                    <p class="wm-kpi__label">Water quality</p>
                    <span class="wm-kpi__icon wm-kpi__icon--neutral"><i class="fas fa-flask"></i></span>
                </div>
                <p class="wm-kpi__value">{{ $kpi['water_quality']['available'] > 0 ? $kpi['water_quality']['available'] : 'N/A' }}</p>
                <p class="wm-kpi__meta">No turbidity sensors connected</p>
            </article>
        </section>

        <section class="wm-dashboard-grid">
            <x-ui.card title="Live telemetry" icon="fa-wave-square" subtitle="Average level and stored volume across all tanks">
                <x-slot:actions>
                    <div class="wm-range-selector" role="group" aria-label="Time range">
                        <button class="wm-range-selector__btn" type="button" data-range="1H">1H</button>
                        <button class="wm-range-selector__btn is-active" type="button" data-range="24H">24H</button>
                        <button class="wm-range-selector__btn" type="button" data-range="7D">7D</button>
                        <button class="wm-range-selector__btn" type="button" data-range="30D">30D</button>
                    </div>
                </x-slot:actions>

                <div class="wm-telemetry-chart-wrap">
                    <canvas id="telemetryTrendChart" role="img" aria-label="Water level and volume over time"></canvas>
                    <div id="chartEmpty" class="position-absolute top-50 start-50 translate-middle text-center" hidden>
                        <x-ui.empty-state icon="fa-chart-line" title="No readings in this range" message="Try a longer time range." />
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card title="Sensor nodes" icon="fa-location-dot" subtitle="Schematic layout. Select a node for details.">
                <div class="wm-node-map" id="nodeMap" style="height: {{ $mapHeight }}px;">
                    @foreach($nodes as $i => $node)
                        @php
                            $x = 8 + (($i % $mapCols) * 50);
                            $y = 32 + (intdiv($i, $mapCols) * 64);
                        @endphp
                        <button
                            type="button"
                            class="wm-node-map__pin status-{{ $node['status'] }}"
                            style="left: {{ $x }}%; top: {{ $y }}px;"
                            data-x="{{ $x }}"
                            data-y="{{ $y }}"
                            data-url="{{ route('tandons.show', $node['id']) }}"
                            data-node="{{ json_encode($node) }}"
                            aria-label="{{ $node['name'] }}, {{ $node['status_label'] }}"
                        ><span>{{ $node['name'] }}</span></button>
                    @endforeach

                    <div class="wm-node-map__tooltip" id="nodeTooltip" role="dialog" aria-label="Node details" hidden>
                        <p class="wm-node-map__title" data-tip="name"></p>
                        <span class="wm-badge wm-badge--neutral" data-tip="badge"></span>
                        <p class="wm-node-map__meta" data-tip="level"></p>
                        <p class="wm-node-map__meta" data-tip="seen"></p>
                        <a href="#" class="btn btn-sm btn-secondary mt-2 w-100" data-tip="link">View details</a>
                    </div>
                </div>
                <div class="wm-node-legend mt-3">
                    <span><i class="dot dot--online"></i>Online</span>
                    <span><i class="dot dot--warning"></i>Warning</span>
                    <span><i class="dot dot--critical"></i>Critical</span>
                    <span><i class="dot dot--offline"></i>Offline (no data for 2h)</span>
                </div>
            </x-ui.card>
        </section>

        <x-ui.card title="Tank overview" icon="fa-water" subtitle="Current level, pump state and usage this month" class="mb-4" :padded="false">
            <x-ui.data-table id="tank-overview-table">
                <thead>
                    <tr>
                        <th>Tank</th>
                        <th>Status</th>
                        <th>Level</th>
                        <th class="text-center">Pump</th>
                        <th class="text-end">This month (m³)</th>
                        <th class="text-end">All time (m³)</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tandonUsage as $i => $usage)
                        @php $node = $nodes[$i] ?? null; @endphp
                        <tr>
                            <td>
                                <a href="{{ route('tandons.show', $usage['tandon']) }}" class="fw-semibold text-decoration-none">{{ $usage['tandon']->name }}</a>
                                <div class="small text-muted">{{ $usage['tandon']->building_name && $usage['tandon']->building_name !== $usage['tandon']->name ? $usage['tandon']->building_name : $usage['tandon']->type }}</div>
                            </td>
                            <td>
                                @if($node)
                                    <x-ui.status-badge :tone="$statusTone[$node['status']]">{{ $node['status_label'] }}</x-ui.status-badge>
                                @endif
                            </td>
                            <td>
                                @if($usage['height_percent'] !== null)
                                    <div class="wm-level">
                                        <div class="d-flex justify-content-between small mb-1">
                                            <strong class="tabular">{{ $usage['height_percent'] }}%</strong>
                                            <span class="text-muted tabular">{{ $usage['water_height'] }} m</span>
                                        </div>
                                        <div class="wm-level__track"><div class="wm-level__fill wm-level__fill--{{ $node['status'] ?? 'offline' }}" style="width: {{ min(100, max(0, $usage['height_percent'])) }}%"></div></div>
                                    </div>
                                @else
                                    <span class="text-muted small">No data</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($usage['tandon']->pump_status === 1)
                                    <x-ui.status-badge tone="success">On</x-ui.status-badge>
                                @elseif($usage['tandon']->pump_status === 0)
                                    <x-ui.status-badge tone="neutral">Off</x-ui.status-badge>
                                @else
                                    <x-ui.status-badge tone="warning">Unknown</x-ui.status-badge>
                                @endif
                            </td>
                            <td class="text-end tabular">{{ number_format($usage['this_month'], 2, ',', '.') }}</td>
                            <td class="text-end tabular">{{ number_format($usage['total_usage'], 2, ',', '.') }}</td>
                            <td class="text-end">
                                <div class="wm-table-actions">
                                    <form method="POST" action="{{ route('tandons.pump-on', $usage['tandon']) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn {{ $usage['tandon']->pump_status === 1 ? 'btn-danger' : 'btn-success' }}"
                                            title="{{ $usage['tandon']->pump_status === 1 ? 'Turn pump off' : 'Turn pump on' }}"
                                            aria-label="{{ $usage['tandon']->pump_status === 1 ? 'Turn pump off' : 'Turn pump on' }}"
                                            onclick="return confirm('{{ $usage['tandon']->pump_status === 1 ? 'Turn OFF' : 'Turn ON' }} the pump for this tank?')"
                                        ><i class="fas fa-power-off"></i></button>
                                    </form>
                                    <a href="{{ route('tandon-readings.index', $usage['tandon']) }}" class="btn btn-outline-secondary" title="Readings" aria-label="Readings"><i class="fas fa-chart-line"></i></a>
                                    <a href="{{ route('tandons.show', $usage['tandon']) }}" class="btn btn-outline-info" title="Details" aria-label="Details"><i class="fas fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-ui.empty-state icon="fa-water" title="No water tanks yet" message="Add a tank to start monitoring.">
                                    <a href="{{ route('tandons.create') }}" class="btn btn-primary">Add tank</a>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.data-table>
        </x-ui.card>

        <x-ui.card title="Sensor readings" icon="fa-table-list" subtitle="Latest {{ number_format($readingLogs->count()) }} readings. Filter, select and export." :padded="false">
            <div class="wm-card__toolbar">
                <div class="wm-filter-bar">
                    <div>
                        <label for="filterDateRange">Date range</label>
                        <select id="filterDateRange" class="form-select form-select-sm">
                            <option value="all">All time</option>
                            <option value="24h">Last 24 hours</option>
                            <option value="7d">Last 7 days</option>
                            <option value="30d">Last 30 days</option>
                        </select>
                    </div>
                    <div>
                        <label for="filterLocation">Sensor location</label>
                        <select id="filterLocation" class="form-select form-select-sm">
                            <option value="all">All locations</option>
                            @foreach($readingLogs->pluck('tandon.building_name')->filter()->unique()->sort()->values() as $location)
                                <option value="{{ $location }}">{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="filterSeverity">Severity</label>
                        <select id="filterSeverity" class="form-select form-select-sm">
                            <option value="all">All severities</option>
                            <option value="normal">Normal</option>
                            <option value="warning">Warning</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>
            </div>

            <x-ui.data-table id="readings-log-table" :client-export="true">
                <thead>
                    <tr>
                        <th style="width: 3rem;" data-export-skip><input type="checkbox" data-master-select aria-label="Select all visible rows"></th>
                        <th>Time</th>
                        <th>Node</th>
                        <th>Location</th>
                        <th class="text-end">Height (m)</th>
                        <th class="text-end">Volume (L)</th>
                        <th>Severity</th>
                        <th class="text-end" data-export-skip>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($readingLogs as $reading)
                        @php
                            $t = $reading->tandon;
                            $severity = 'normal';
                            if ($reading->water_height <= $t->height_min || $reading->water_height >= $t->height_max) {
                                $severity = 'critical';
                            } elseif ($reading->water_height <= $t->height_warning) {
                                $severity = 'warning';
                            }
                            $sevTone = ['normal' => 'success', 'warning' => 'warning', 'critical' => 'danger'][$severity];
                        @endphp
                        <tr data-log-row data-log-date="{{ $reading->recordedInstant()->toIso8601String() }}" data-log-location="{{ $t->building_name ?? '' }}" data-log-severity="{{ $severity }}">
                            <td><input type="checkbox" data-row-select value="{{ $reading->id }}" aria-label="Select reading {{ $reading->id }}"></td>
                            <td class="tabular text-nowrap">{{ $reading->recorded_at->format('d M Y, H:i') }}</td>
                            <td class="fw-semibold">{{ $t->name }}</td>
                            <td>{{ $t->building_name ?: '-' }}</td>
                            <td class="text-end tabular">@num($reading->water_height)</td>
                            <td class="text-end tabular">@num($reading->water_volume * 1000)</td>
                            <td><x-ui.status-badge :tone="$sevTone">{{ ucfirst($severity) }}</x-ui.status-badge></td>
                            <td class="text-end">
                                <div class="wm-table-actions">
                                    <a href="{{ route('tandon-readings.show', [$t, $reading]) }}" class="btn btn-outline-info" title="View reading" aria-label="View reading"><i class="fas fa-eye"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    <tr data-log-empty @if($readingLogs->isNotEmpty()) hidden @endif>
                        <td colspan="8">
                            <x-ui.empty-state icon="fa-filter" title="No readings match" message="Adjust the filters or wait for new telemetry." />
                        </td>
                    </tr>
                </tbody>
            </x-ui.data-table>

            <div class="wm-pagination-wrap">
                <p class="wm-pagination-meta" id="logMeta" aria-live="polite"></p>
                <div class="wm-pagination-controls">
                    <div class="wm-per-page-form">
                        <label for="logPerPage">Items per page</label>
                        <select id="logPerPage" class="form-select form-select-sm">
                            <option value="25">25</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <nav aria-label="Readings pagination"><ul class="pagination mb-0" id="logPager"></ul></nav>
                </div>
            </div>
        </x-ui.card>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            const fine = @json($telemetryFine);
            const hourly = @json($telemetryHourly);
            const ranges = {
                '1H': { source: fine, hours: 1 },
                '24H': { source: fine, hours: 24 },
                '7D': { source: hourly, hours: 24 * 7 },
                '30D': { source: hourly, hours: 24 * 30 },
            };

            wmApplyChartTheme();

            const canvas = document.getElementById('telemetryTrendChart');
            const emptyState = document.getElementById('chartEmpty');
            const fmt = (iso, long) => new Date(iso).toLocaleString('en-GB', long
                ? { timeZone: 'Asia/Jakarta', day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' }
                : { timeZone: 'Asia/Jakarta', hour: '2-digit', minute: '2-digit' });

            const chart = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        { label: 'Avg level (%)', data: [], yAxisID: 'yLevel', borderWidth: 2, pointRadius: 0, pointHoverRadius: 4, tension: 0.3, fill: true },
                        { label: 'Avg volume (L)', data: [], yAxisID: 'yVolume', borderWidth: 2, pointRadius: 0, pointHoverRadius: 4, tension: 0.3 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 16 } } },
                    scales: {
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 7, maxRotation: 0 } },
                        yLevel: { position: 'left', beginAtZero: true, suggestedMax: 100, title: { display: true, text: 'Level (%)' } },
                        yVolume: { position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'Volume (L)' } },
                    },
                },
            });

            const paintColors = () => {
                const c1 = wmCssVar('--wm-chart-1');
                const c2 = wmCssVar('--wm-chart-2');
                chart.data.datasets[0].borderColor = c1;
                chart.data.datasets[0].backgroundColor = c1 + '22';
                chart.data.datasets[1].borderColor = c2;
                chart.data.datasets[1].backgroundColor = c2 + '22';
            };

            let currentRange = '24H';
            const renderRange = (key) => {
                currentRange = key;
                const { source, hours } = ranges[key];
                const since = Date.now() - hours * 3600 * 1000;
                const points = source.filter((p) => new Date(p.timestamp).getTime() >= since);
                const long = hours > 24;
                chart.data.labels = points.map((p) => fmt(p.timestamp, long));
                chart.data.datasets[0].data = points.map((p) => p.avg_level);
                chart.data.datasets[1].data = points.map((p) => p.avg_volume_l);
                paintColors();
                chart.update();
                emptyState.hidden = points.length > 0;
            };

            document.querySelectorAll('.wm-range-selector__btn').forEach((btn) => {
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.wm-range-selector__btn').forEach((b) => b.classList.remove('is-active'));
                    btn.classList.add('is-active');
                    renderRange(btn.dataset.range);
                });
            });

            document.addEventListener('wm:themechange', () => {
                wmApplyChartTheme();
                chart.options.plugins.legend.labels.color = wmCssVar('--wm-muted');
                renderRange(currentRange);
            });

            renderRange('24H');

            /* ---- Node map flyout ---- */
            const map = document.getElementById('nodeMap');
            const tip = document.getElementById('nodeTooltip');
            const tipEl = (k) => tip.querySelector(`[data-tip="${k}"]`);
            const tones = { online: 'success', warning: 'warning', critical: 'danger', offline: 'neutral' };
            let activePin = null;

            const closeTip = () => {
                tip.hidden = true;
                activePin?.classList.remove('is-active');
                activePin = null;
            };

            map.querySelectorAll('.wm-node-map__pin').forEach((pin) => {
                pin.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (activePin === pin) return closeTip();
                    closeTip();

                    const node = JSON.parse(pin.dataset.node);
                    tipEl('name').textContent = node.name;
                    tipEl('badge').textContent = node.status_label;
                    tipEl('badge').className = `wm-badge wm-badge--${tones[node.status] || 'neutral'}`;
                    tipEl('level').textContent = node.height_percent !== null
                        ? `Level: ${node.height_percent}% (${node.water_height} m)`
                        : 'Level: no data';
                    tipEl('seen').textContent = node.last_seen
                        ? `Last reading: ${fmt(node.last_seen, true)}`
                        : 'Last reading: never';
                    tipEl('link').href = pin.dataset.url;

                    tip.hidden = false;
                    pin.classList.add('is-active');
                    activePin = pin;

                    const w = map.clientWidth;
                    const y = Number(pin.dataset.y);
                    const px = (Number(pin.dataset.x) / 100) * w;
                    tip.style.left = `${Math.max(8, Math.min(px - tip.offsetWidth / 2, w - tip.offsetWidth - 8))}px`;
                    const below = y + 22;
                    tip.style.top = `${below + tip.offsetHeight > map.clientHeight ? Math.max(8, y - tip.offsetHeight - 16) : below}px`;
                });
            });

            document.addEventListener('click', (e) => { if (!tip.contains(e.target)) closeTip(); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeTip(); });

            /* ---- Readings table: filters + client pagination ---- */
            const shell = document.getElementById('readings-log-table-shell');
            const rows = [...document.querySelectorAll('[data-log-row]')];
            const emptyRow = document.querySelector('[data-log-empty]');
            const dateSel = document.getElementById('filterDateRange');
            const locSel = document.getElementById('filterLocation');
            const sevSel = document.getElementById('filterSeverity');
            const perSel = document.getElementById('logPerPage');
            const meta = document.getElementById('logMeta');
            const pager = document.getElementById('logPager');
            const spans = { '24h': 24, '7d': 24 * 7, '30d': 24 * 30 };
            let page = 1;

            const pageItem = (label, target, { active = false, disabled = false, aria } = {}) => {
                const li = document.createElement('li');
                li.className = `page-item${active ? ' active' : ''}${disabled ? ' disabled' : ''}`;
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'page-link';
                btn.textContent = label;
                if (aria) btn.setAttribute('aria-label', aria);
                if (!disabled && !active) btn.addEventListener('click', () => { page = target; render(); });
                li.appendChild(btn);
                return li;
            };

            const render = () => {
                const now = Date.now();
                const perPage = Number(perSel.value);
                const matches = rows.filter((row) => {
                    if (locSel.value !== 'all' && row.dataset.logLocation !== locSel.value) return false;
                    if (sevSel.value !== 'all' && row.dataset.logSeverity !== sevSel.value) return false;
                    if (dateSel.value !== 'all') {
                        const ageH = (now - new Date(row.dataset.logDate).getTime()) / 3600000;
                        if (ageH > spans[dateSel.value]) return false;
                    }
                    return true;
                });

                const pages = Math.max(1, Math.ceil(matches.length / perPage));
                page = Math.min(page, pages);
                const start = (page - 1) * perPage;
                const visible = new Set(matches.slice(start, start + perPage));
                rows.forEach((row) => { row.style.display = visible.has(row) ? '' : 'none'; });
                emptyRow.hidden = matches.length > 0;

                const from = matches.length ? start + 1 : 0;
                const to = Math.min(start + perPage, matches.length);
                meta.textContent = `Showing ${from.toLocaleString('en-US')} to ${to.toLocaleString('en-US')} of ${matches.length.toLocaleString('en-US')} readings`;

                pager.replaceChildren();
                pager.append(pageItem('\u2039', page - 1, { disabled: page === 1, aria: 'Previous page' }));
                const shown = [...new Set([1, page - 1, page, page + 1, pages])].filter((n) => n >= 1 && n <= pages).sort((a, b) => a - b);
                shown.forEach((n, i) => {
                    if (i > 0 && n - shown[i - 1] > 1) pager.append(pageItem('\u2026', n, { disabled: true }));
                    pager.append(pageItem(String(n), n, { active: n === page }));
                });
                pager.append(pageItem('\u203A', page + 1, { disabled: page === pages, aria: 'Next page' }));

                shell.dispatchEvent(new Event('wm:rows-changed'));
            };

            [dateSel, locSel, sevSel, perSel].forEach((el) => el.addEventListener('change', () => { page = 1; render(); }));
            render();
        });
    </script>
@endsection
