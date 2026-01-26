@extends('layouts.app')

@section('title', $tandon->name)

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-info-circle"></i> {{ $tandon->name }}</h1>
            <p class="lead mb-0">Water tank details and specifications</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="mb-0">Water Tank Information</h3>
                            <div class="btn-group">
                                <a href="{{ route('tandons.edit', $tandon) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                <a href="{{ route('tandons.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-tag"></i> Water Tank Name</label>
                                    <div class="fw-semibold">{{ $tandon->name }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-cube"></i> Type</label>
                                    <div class="fw-semibold">
                                        @if($tandon->type === 'PUSAT')
                                            <span class="badge bg-primary">PUSAT (Central)</span>
                                        @else
                                            <span class="badge bg-info">GEDUNG (Building)</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-building"></i> Building Name</label>
                                    <div class="fw-semibold">{{ $tandon->building_name ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-sitemap"></i> Parent Water Tank</label>
                                    <div class="fw-semibold">
                                        @if($tandon->parent)
                                            <a href="{{ route('tandons.show', $tandon->parent) }}" class="text-primary text-decoration-none">
                                                {{ $tandon->parent->name }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-expand"></i> Cross Section Area</label>
                                    <div class="fw-semibold">@num($tandon->cross_section_area) m²</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-arrows-alt-v"></i> Max Height</label>
                                    <div class="fw-semibold text-success">@num($tandon->height_max) m</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-level-down-alt"></i> Min Height</label>
                                    <div class="fw-semibold text-danger">@num($tandon->height_min) m</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-exclamation-triangle"></i> Warning Height</label>
                                    <div class="fw-semibold text-warning">@num($tandon->height_warning) m</div>
                                </div>
                            </div>

                            <div class="col-12"><hr></div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-calendar-plus"></i> Created At</label>
                                    <div class="fw-semibold">{{ $tandon->created_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="detail-item">
                                    <label class="text-muted small mb-1"><i class="fas fa-calendar-check"></i> Updated At</label>
                                    <div class="fw-semibold">{{ $tandon->updated_at->format('d M Y, H:i') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <form method="POST" action="{{ route('tandons.destroy', $tandon) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this water tank?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete Water Tank
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
