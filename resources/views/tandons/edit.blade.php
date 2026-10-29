@extends('layouts.app')

@section('title', 'Edit Water Tank')

@section('content')
    <x-ui.page-header
        title="Edit Water Tank"
        subtitle="Update water tank information"
        icon="fa-edit"
    />

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card title="Update Water Tank" icon="fa-pen-to-square">
            <form method="POST" action="{{ route('tandons.update', $tandon) }}">
                @csrf
                @method('PUT')

                <div class="wm-form-grid">
                    <div class="wm-form-field--full">
                        <label for="name" class="form-label">Water Tank Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $tandon->name) }}" required class="form-control" placeholder="e.g., Main Water Tank">
                        <small class="text-muted d-block mt-1">Must match exactly with Device ID on mqtt.icminovasi.my.id.</small>
                    </div>

                    <div>
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" required class="form-select">
                            <option value="">Select Type</option>
                            <option value="PUSAT" {{ old('type', $tandon->type) == 'PUSAT' ? 'selected' : '' }}>PUSAT (Central)</option>
                            <option value="GEDUNG" {{ old('type', $tandon->type) == 'GEDUNG' ? 'selected' : '' }}>GEDUNG (Building)</option>
                        </select>
                    </div>

                    <div>
                        <label for="building_name" class="form-label">Building Name</label>
                        <input type="text" name="building_name" id="building_name" value="{{ old('building_name', $tandon->building_name) }}" class="form-control" placeholder="e.g., Engineering Building">
                    </div>

                    <div>
                        <label for="parent_id" class="form-label">Parent Water Tank</label>
                        <select name="parent_id" id="parent_id" class="form-select">
                            <option value="">No Parent</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $tandon->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="cross_section_area" class="form-label">Cross Section Area (m²)</label>
                        <input type="number" step="0.0001" name="cross_section_area" id="cross_section_area" value="{{ old('cross_section_area', $tandon->cross_section_area) }}" required class="form-control">
                    </div>

                    <div>
                        <label for="height_max" class="form-label">Max Height (m)</label>
                        <input type="number" step="0.001" name="height_max" id="height_max" value="{{ old('height_max', $tandon->height_max) }}" required class="form-control">
                    </div>

                    <div>
                        <label for="height_min" class="form-label">Min Height (m)</label>
                        <input type="number" step="0.001" name="height_min" id="height_min" value="{{ old('height_min', $tandon->height_min) }}" required class="form-control">
                    </div>

                    <div>
                        <label for="height_warning" class="form-label">Warning Height (m)</label>
                        <input type="number" step="0.001" name="height_warning" id="height_warning" value="{{ old('height_warning', $tandon->height_warning) }}" required class="form-control">
                    </div>

                    <div class="wm-form-field--full">
                        <label for="sensor_distance_full" class="form-label">Sensor Distance When Full (m)</label>
                        <input type="number" step="0.001" min="0" name="sensor_distance_full" id="sensor_distance_full" value="{{ old('sensor_distance_full', $tandon->sensor_distance_full) }}" class="form-control">
                        <small class="text-muted d-block mt-1">Distance from the sensor to the water surface when the tank is full. The edge device uses it to compute water height (Max Height - (measured distance - this value)). Leave empty only if the device does not need it.</small>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('tandons.index') }}" class="btn wm-btn wm-btn--secondary">Cancel</a>
                    <button type="submit" class="btn wm-btn wm-btn--primary">Update Water Tank</button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
