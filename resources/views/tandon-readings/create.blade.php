@extends('layouts.app')

@section('title', 'Add Reading - ' . $tandon->name)

@section('content')
    <x-ui.page-header
        title="Record New Reading"
        :subtitle="'Add a water measurement for ' . $tandon->name"
        icon="fa-plus-circle"
    />

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card title="Reading Form" icon="fa-ruler-vertical" subtitle="Type: {{ $tandon->type }} | Area: @num($tandon->cross_section_area) m²">
            <form method="POST" action="{{ route('tandon-readings.store', $tandon) }}">
                @csrf

                <div class="wm-form-grid">
                    <div>
                        <label for="water_height" class="form-label">Water Height (m)</label>
                        <input type="number" step="0.001" name="water_height" id="water_height" value="{{ old('water_height') }}" required class="form-control" placeholder="e.g., 1.250">
                        <small class="text-muted d-block mt-1">Min: @num($tandon->height_min) m | Warning: @num($tandon->height_warning) m | Max: @num($tandon->height_max) m</small>
                    </div>

                    <div>
                        <label for="water_volume" class="form-label">Water Volume (m³)</label>
                        <input type="number" step="0.001" name="water_volume" id="water_volume" value="{{ old('water_volume') }}" readonly class="form-control bg-light" placeholder="Auto-calculated">
                    </div>

                    <div class="wm-form-field--full">
                        <label for="recorded_at" class="form-label">Recorded At</label>
                        <input type="text" name="recorded_at" id="recorded_at" value="{{ old('recorded_at', now('Asia/Jakarta')->format('Y-m-d H:i')) }}" required class="form-control" placeholder="YYYY-MM-DD HH:mm">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('tandon-readings.index', $tandon) }}" class="btn wm-btn wm-btn--secondary">Cancel</a>
                    <button type="submit" class="btn wm-btn wm-btn--primary">Record Reading</button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection

@section('scripts')
<script>
    const waterHeightInput = document.getElementById('water_height');
    const waterVolumeInput = document.getElementById('water_volume');
    const area = {{ $tandon->cross_section_area }};

    waterHeightInput.addEventListener('input', function() {
        const height = parseFloat(this.value) || 0;
        waterVolumeInput.value = (height * area).toFixed(3);
    });

    if (waterHeightInput.value) {
        const height = parseFloat(waterHeightInput.value) || 0;
        waterVolumeInput.value = (height * area).toFixed(3);
    }
</script>
@endsection
