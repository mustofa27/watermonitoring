@extends('layouts.app')

@section('title', 'Reading Details - ' . $tandon->name)

@section('content')
    <x-ui.page-header
        title="Reading Details"
        subtitle="Detailed information for this water measurement"
        icon="fa-chart-line"
    >
        <a href="{{ route('tandon-readings.index', $tandon) }}" class="btn wm-btn wm-btn--secondary">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>
    </x-ui.page-header>

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card :title="'Water Tank: ' . $tandon->name" icon="fa-water">
            <div class="wm-detail-grid">
                <div class="wm-detail-item"><p class="wm-detail-item__label">Water Tank Name</p><p class="wm-detail-item__value">{{ $tandon->name }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Water Tank Type</p><p class="wm-detail-item__value">{{ $tandon->type }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Water Height</p><p class="wm-detail-item__value">@num($reading->water_height) m</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Water Volume</p><p class="wm-detail-item__value">@num($reading->water_volume * 1000) L</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Recorded At</p><p class="wm-detail-item__value">{{ $reading->recorded_at->format('d M Y, H:i:s') }}</p></div>
                <div class="wm-detail-item">
                    <p class="wm-detail-item__label">Status</p>
                    <p class="wm-detail-item__value">
                        @if($reading->water_height >= $tandon->height_max)
                            <x-ui.status-badge tone="danger">High - Above Maximum</x-ui.status-badge>
                        @elseif($reading->water_height >= $tandon->height_warning)
                            <x-ui.status-badge tone="warning">Warning - Above Threshold</x-ui.status-badge>
                        @elseif($reading->water_height <= $tandon->height_min)
                            <x-ui.status-badge tone="danger">Low - Below Minimum</x-ui.status-badge>
                        @else
                            <x-ui.status-badge tone="success">Normal</x-ui.status-badge>
                        @endif
                    </p>
                </div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Cross Section Area</p><p class="wm-detail-item__value">@num($tandon->cross_section_area) m²</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Warning Height</p><p class="wm-detail-item__value">@num($tandon->height_warning) m</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Min Height</p><p class="wm-detail-item__value">@num($tandon->height_min) m</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Max Height</p><p class="wm-detail-item__value">@num($tandon->height_max) m</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Created At</p><p class="wm-detail-item__value">{{ $reading->created_at->format('d M Y, H:i:s') }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Updated At</p><p class="wm-detail-item__value">{{ $reading->updated_at->format('d M Y, H:i:s') }}</p></div>
            </div>

            <div class="mt-4">
                <form method="POST" action="{{ route('tandon-readings.destroy', [$tandon, $reading]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this reading?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete Reading
                    </button>
                </form>
            </div>
        </x-ui.card>
    </div>
@endsection
