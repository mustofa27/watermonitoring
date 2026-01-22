@extends('layouts.app')

@section('title', 'Edit Water Tank')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-edit"></i> Edit Water Tank</h1>
            <p class="lead mb-0">Update water tank information</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('tandons.update', $tandon) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Water Tank Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $tandon->name) }}" required class="form-control" placeholder="e.g., Main Water Tank">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> <strong>Important:</strong> Must match exactly with the Device ID registered on <a href="https://mqtt.icminovasi.my.id" target="_blank" rel="noopener">mqtt.icminovasi.my.id</a>
                                </small>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type</label>
                                <select name="type" id="type" required class="form-select">
                                    <option value="">Select Type</option>
                                    <option value="PUSAT" {{ old('type', $tandon->type) == 'PUSAT' ? 'selected' : '' }}>PUSAT (Central)</option>
                                    <option value="GEDUNG" {{ old('type', $tandon->type) == 'GEDUNG' ? 'selected' : '' }}>GEDUNG (Building)</option>
                                </select>
                                @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="building_name" class="form-label">Building Name</label>
                                <input type="text" name="building_name" id="building_name" value="{{ old('building_name', $tandon->building_name) }}" class="form-control" placeholder="e.g., Engineering Building">
                                @error('building_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="parent_id" class="form-label">Parent Water Tank</label>
                                <select name="parent_id" id="parent_id" class="form-select">
                                    <option value="">No Parent</option>
                                    @foreach($parents as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', $tandon->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                    @endforeach
                                </select>
                                @error('parent_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cross_section_area" class="form-label">Cross Section Area (m²)</label>
                                    <input type="number" step="0.0001" name="cross_section_area" id="cross_section_area" value="{{ old('cross_section_area', $tandon->cross_section_area) }}" required class="form-control">
                                    @error('cross_section_area') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="height_max" class="form-label">Max Height (m)</label>
                                    <input type="number" step="0.001" name="height_max" id="height_max" value="{{ old('height_max', $tandon->height_max) }}" required class="form-control">
                                    @error('height_max') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="height_min" class="form-label">Min Height (m)</label>
                                    <input type="number" step="0.001" name="height_min" id="height_min" value="{{ old('height_min', $tandon->height_min) }}" required class="form-control">
                                    @error('height_min') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="height_warning" class="form-label">Warning Height (m)</label>
                                    <input type="number" step="0.001" name="height_warning" id="height_warning" value="{{ old('height_warning', $tandon->height_warning) }}" required class="form-control">
                                    @error('height_warning') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('tandons.index') }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Water Tank</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
