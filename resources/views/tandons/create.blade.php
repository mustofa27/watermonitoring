@extends('layouts.app')

@section('title', 'Create Water Tank')

@section('content')
    <x-ui.page-header
        title="Create New Water Tank"
        subtitle="Add a new water tank to the monitoring system"
        icon="fa-plus-circle"
    />

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card title="Water Tank Form" icon="fa-pen-to-square">
            <form method="POST" action="{{ route('tandons.store') }}">
                @csrf

                <div class="wm-form-grid">
                    <div class="wm-form-field--full">
                        <label for="name" class="form-label">Water Tank Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required class="form-control" placeholder="e.g., Main Water Tank">
                        <small class="text-muted d-block mt-1">Must match exactly with Device ID on mqtt.icminovasi.my.id.</small>
                    </div>

                    <div>
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" required class="form-select">
                            <option value="">Select Type</option>
                            <option value="PUSAT" {{ old('type') == 'PUSAT' ? 'selected' : '' }}>PUSAT (Central)</option>
                            <option value="GEDUNG" {{ old('type') == 'GEDUNG' ? 'selected' : '' }}>GEDUNG (Building)</option>
                        </select>
                    </div>

                    <div>
                        <label for="building_name" class="form-label">Building Name</label>
                        <input type="text" name="building_name" id="building_name" value="{{ old('building_name') }}" class="form-control" placeholder="e.g., Engineering Building">
                    </div>

                    <div>
                        <label for="parent_id" class="form-label">Parent Water Tank</label>
                        <select name="parent_id" id="parent_id" class="form-select">
                            <option value="">No Parent</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="cross_section_area" class="form-label">Cross Section Area (m²)</label>
                        <input type="number" step="0.0001" name="cross_section_area" id="cross_section_area" value="{{ old('cross_section_area') }}" required class="form-control">
                    </div>

                    <div>
                        <label for="height_max" class="form-label">Max Height (m)</label>
                        <input type="number" step="0.001" name="height_max" id="height_max" value="{{ old('height_max') }}" required class="form-control">
                    </div>

                    <div>
                        <label for="height_min" class="form-label">Min Height (m)</label>
                        <input type="number" step="0.001" name="height_min" id="height_min" value="{{ old('height_min') }}" required class="form-control">
                    </div>

                    <div>
                        <label for="height_warning" class="form-label">Warning Height (m)</label>
                        <input type="number" step="0.001" name="height_warning" id="height_warning" value="{{ old('height_warning') }}" required class="form-control">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('tandons.index') }}" class="btn wm-btn wm-btn--secondary">Cancel</a>
                    <button type="submit" class="btn wm-btn wm-btn--primary">Create Water Tank</button>
                </div>
            </form>
        </x-ui.card>
    </div>
@endsection
