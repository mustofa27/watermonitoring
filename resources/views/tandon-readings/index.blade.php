@extends('layouts.app')

@section('title', $tandon->name . ' - Readings')

@section('content')
    <x-ui.page-header
        :title="$tandon->name . ' - Water Readings'"
        subtitle="Historical water level and volume measurements"
        icon="fa-chart-line"
    >
        <a href="{{ route('tandon-readings.create', $tandon) }}" class="btn wm-btn wm-btn--primary">
            <i class="fas fa-plus-circle"></i>
            Add Reading
        </a>
        <a href="{{ route('tandons.index') }}" class="btn wm-btn wm-btn--secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Water Tanks
        </a>
    </x-ui.page-header>

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card title="Water Tank Information" icon="fa-water" class="mb-4">
            <p class="mb-1"><strong>{{ $tandon->name }}</strong></p>
            <p class="mb-0 text-muted">Type: <strong>{{ $tandon->type }}</strong> | Area: <strong>@num($tandon->cross_section_area) m²</strong></p>
        </x-ui.card>

        @php
            $chartReadings = $readings->getCollection()->sortBy('recorded_at')->values();
            $chartLabels = $chartReadings->map(fn ($reading) => $reading->recorded_at->format('d M H:i'));
            $chartHeights = $chartReadings->map(fn ($reading) => round($reading->water_height, 3));
            $chartVolumes = $chartReadings->map(fn ($reading) => round($reading->water_volume * 1000, 2));
        @endphp

        <x-ui.card title="Readings History Chart" icon="fa-chart-line" subtitle="Showing {{ $chartReadings->count() }} point(s) from current page" class="mb-4">
            @if($chartReadings->isNotEmpty())
                <div style="height: 320px;"><canvas id="readingsHistoryChart"></canvas></div>
            @else
                <x-ui.empty-state icon="fa-chart-line" title="No data available for chart" />
            @endif
        </x-ui.card>

        <x-ui.card title="Readings Log" icon="fa-table-list" :padded="false">
            <x-ui.data-table
                id="readings-table"
                :bulk-action="route('tandon-readings.bulk-destroy', $tandon)"
                :bulk-actions="['delete' => 'Delete selected']"
                select-name="reading_ids[]"
            >
                <thead>
                    <tr>
                        <th style="width: 48px;"><input type="checkbox" data-master-select></th>
                        <th>Recorded At</th>
                        <th>Water Height</th>
                        <th>Water Volume</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($readings as $reading)
                        <tr>
                            <td><input type="checkbox" data-row-select value="{{ $reading->id }}"></td>
                            <td class="text-nowrap"><strong>{{ $reading->recorded_at->format('d M Y, H:i') }}</strong></td>
                            <td>@num($reading->water_height) m</td>
                            <td>@num($reading->water_volume * 1000) L</td>
                            <td>
                                @if($reading->water_height >= $tandon->height_max)
                                    <x-ui.status-badge tone="danger">High</x-ui.status-badge>
                                @elseif($reading->water_height <= $tandon->height_warning && $reading->water_height > $tandon->height_min)
                                    <x-ui.status-badge tone="warning">Warning</x-ui.status-badge>
                                @elseif($reading->water_height <= $tandon->height_min)
                                    <x-ui.status-badge tone="danger">Low</x-ui.status-badge>
                                @else
                                    <x-ui.status-badge tone="success">Normal</x-ui.status-badge>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="wm-table-actions justify-content-center">
                                    <a href="{{ route('tandon-readings.show', [$tandon, $reading]) }}" class="btn btn-sm btn-outline-info" title="View Details"><i class="fas fa-eye"></i></a>
                                    <form method="POST" action="{{ route('tandon-readings.destroy', [$tandon, $reading]) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this reading?')" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"><x-ui.empty-state title="No readings found" message="Add a reading to record the first measurement." /></td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.data-table>

            <x-ui.pagination-controls :paginator="$readings" />
        </x-ui.card>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
        const chartLabels = @json($chartLabels);
        const chartHeights = @json($chartHeights);
        const chartVolumes = @json($chartVolumes);

        wmApplyChartTheme();

        if (chartLabels.length > 0) {
            new Chart(document.getElementById('readingsHistoryChart'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Water Height (m)',
                            data: chartHeights,
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.15)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.25,
                            yAxisID: 'yHeight'
                        },
                        {
                            label: 'Water Volume (L)',
                            data: chartVolumes,
                            borderColor: '#198754',
                            backgroundColor: 'rgba(25, 135, 84, 0.12)',
                            borderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            tension: 0.25,
                            yAxisID: 'yVolume'
                        },
                        {
                            label: 'Warning Threshold',
                            data: Array(chartLabels.length).fill({{ (float) $tandon->height_warning }}),
                            borderColor: '#ffc107',
                            borderWidth: 1.5,
                            borderDash: [6, 4],
                            pointRadius: 0,
                            yAxisID: 'yHeight'
                        },
                        {
                            label: 'Minimum Threshold',
                            data: Array(chartLabels.length).fill({{ (float) $tandon->height_min }}),
                            borderColor: '#dc3545',
                            borderWidth: 1.5,
                            borderDash: [6, 4],
                            pointRadius: 0,
                            yAxisID: 'yHeight'
                        },
                        {
                            label: 'Maximum Threshold',
                            data: Array(chartLabels.length).fill({{ (float) $tandon->height_max }}),
                            borderColor: '#6610f2',
                            borderWidth: 1.5,
                            borderDash: [6, 4],
                            pointRadius: 0,
                            yAxisID: 'yHeight'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 16 } } },
                    scales: {
                        yHeight: {
                            type: 'linear',
                            position: 'left',
                            title: { display: true, text: 'Height (m)' }
                        },
                        yVolume: {
                            type: 'linear',
                            position: 'right',
                            title: { display: true, text: 'Volume (L)' },
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }
        });
    </script>
@endsection
