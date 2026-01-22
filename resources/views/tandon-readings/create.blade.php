@extends('layouts.app')

@section('title', 'Add Reading - ' . $tandon->name)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-plus-circle"></i> Record New Reading</h1>
            <p class="lead mb-0">Add a water measurement for {{ $tandon->name }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="mb-4 pb-3 border-bottom">
                            <h5 class="mb-0"><i class="fas fa-water"></i> Water Tank: <strong>{{ $tandon->name }}</strong></h5>
                            <small class="text-muted">Type: {{ $tandon->type }} | Area: {{ number_format($tandon->cross_section_area, 4) }} m²</small>
                        </div>

                        <form method="POST" action="{{ route('tandon-readings.store', $tandon) }}">
                            @csrf

                            <div class="mb-3">
                                <label for="water_height" class="form-label">Water Height (m)</label>
                                <input type="number" step="0.001" name="water_height" id="water_height" value="{{ old('water_height') }}" required class="form-control" placeholder="e.g., 1.250">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Min: {{ number_format($tandon->height_min, 3) }} m | 
                                    Warning: {{ number_format($tandon->height_warning, 3) }} m | 
                                    Max: {{ number_format($tandon->height_max, 3) }} m
                                </small>
                                @error('water_height') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="water_volume" class="form-label">Water Volume (L)</label>
                                <input type="number" step="0.001" name="water_volume" id="water_volume" value="{{ old('water_volume') }}" required class="form-control" placeholder="e.g., 5000.000">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-calculator"></i> Calculated from height × cross-section area
                                </small>
                                @error('water_volume') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="recorded_at" class="form-label">Recorded At</label>
                                <input type="text" name="recorded_at" id="recorded_at" value="{{ old('recorded_at', now()->format('Y-m-d H:i')) }}" required class="form-control" placeholder="YYYY-MM-DD HH:mm">
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle"></i> Format: YYYY-MM-DD HH:mm (e.g., 2026-01-22 14:30)
                                </small>
                                @error('recorded_at') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('tandon-readings.index', $tandon) }}" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Record Reading</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
