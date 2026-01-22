@extends('layouts.app')

@section('title', $tandon->name . ' - Readings')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-chart-line"></i> {{ $tandon->name }} - Water Readings</h1>
            <p class="lead mb-0">Historical water level and volume measurements</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <small class="text-muted d-block"><i class="fas fa-water"></i> Water Tank Information</small>
                        <h5 class="mb-0">{{ $tandon->name }}</h5>
                        <small class="text-muted">
                            Type: <strong>{{ $tandon->type }}</strong> | 
                            Area: <strong>{{ number_format($tandon->cross_section_area, 4) }} m²</strong>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-end">
                    <a href="{{ route('tandon-readings.create', $tandon) }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle"></i> Add Reading
                    </a>
                    <a href="{{ route('tandons.show', $tandon) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Water Tank
                    </a>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Recorded At</th>
                                <th class="px-4 py-3">Water Height</th>
                                <th class="px-4 py-3">Water Volume</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($readings as $reading)
                                <tr>
                                    <td class="px-4 py-3">
                                        <strong>{{ $reading->recorded_at->format('d M Y, H:i') }}</strong>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ number_format($reading->water_height, 3) }} m
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ number_format($reading->water_volume, 3) }} L
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($reading->water_height >= $tandon->height_max)
                                            <span class="badge bg-danger">High</span>
                                        @elseif($reading->water_height >= $tandon->height_warning)
                                            <span class="badge bg-warning text-dark">Warning</span>
                                        @elseif($reading->water_height <= $tandon->height_min)
                                            <span class="badge bg-danger">Low</span>
                                        @else
                                            <span class="badge bg-success">Normal</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tandon-readings.show', [$tandon, $reading]) }}" class="btn btn-action btn-view" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form method="POST" action="{{ route('tandon-readings.destroy', [$tandon, $reading]) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this reading?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-5 text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p class="mb-0">No readings found. Click "Add Reading" to record the first measurement.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($readings->hasPages())
            <div class="mt-4">
                {{ $readings->links() }}
            </div>
        @endif
    </div>

    <!-- Bootstrap core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .btn-action {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            border: none;
        }

        .btn-view {
            background-color: #17a2b8;
            color: white;
        }

        .btn-view:hover {
            background-color: #138496;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
            color: white;
        }
    </style>
@endsection
