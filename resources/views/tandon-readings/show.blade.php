@extends('layouts.app')

@section('title', 'Reading Details - ' . $tandon->name)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-chart-line"></i> Reading Details</h1>
            <p class="lead mb-0">Detailed information for this water measurement</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Water Tank: {{ $tandon->name }}</h3>
                            <div class="btn-group">
                                <a href="{{ route('tandon-readings.index', $tandon) }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-water"></i> Water Tank Name</label>
                                    <div class="fw-semibold">{{ $tandon->name }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-cube"></i> Water Tank Type</label>
                                    <div class="fw-semibold">
                                        @if($tandon->type === 'PUSAT')
                                            <span class="badge bg-primary">PUSAT (Central)</span>
                                        @else
                                            <span class="badge bg-info">GEDUNG (Building)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-arrows-alt-v"></i> Water Height</label>
                                    <div class="fw-semibold">{{ number_format($reading->water_height, 3) }} m</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-liquid"></i> Water Volume</label>
                                    <div class="fw-semibold">{{ number_format($reading->water_volume, 3) }} L</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-calendar"></i> Recorded At</label>
                                    <div class="fw-semibold">{{ $reading->recorded_at->format('d M Y, H:i:s') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-check-circle"></i> Status</label>
                                    <div class="fw-semibold">
                                        @if($reading->water_height >= $tandon->height_max)
                                            <span class="badge bg-danger">High - Above Maximum</span>
                                        @elseif($reading->water_height >= $tandon->height_warning)
                                            <span class="badge bg-warning text-dark">Warning - Above Threshold</span>
                                        @elseif($reading->water_height <= $tandon->height_min)
                                            <span class="badge bg-danger">Low - Below Minimum</span>
                                        @else
                                            <span class="badge bg-success">Normal</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-expand"></i> Cross Section Area</label>
                                    <div class="fw-semibold">{{ number_format($tandon->cross_section_area, 4) }} m²</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-exclamation-triangle"></i> Warning Height</label>
                                    <div class="fw-semibold text-warning">{{ number_format($tandon->height_warning, 3) }} m</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-level-down-alt"></i> Min Height</label>
                                    <div class="fw-semibold text-danger">{{ number_format($tandon->height_min, 3) }} m</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-level-up-alt"></i> Max Height</label>
                                    <div class="fw-semibold text-success">{{ number_format($tandon->height_max, 3) }} m</div>
                                </div>
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-calendar-plus"></i> Created At</label>
                                    <div class="fw-semibold">{{ $reading->created_at->format('d M Y, H:i:s') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-calendar-check"></i> Updated At</label>
                                    <div class="fw-semibold">{{ $reading->updated_at->format('d M Y, H:i:s') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <form method="POST" action="{{ route('tandon-readings.destroy', [$tandon, $reading]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this reading?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete Reading
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
    .detail-item {
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 0.375rem;
        height: 100%;
    }
</style>
@endsection
