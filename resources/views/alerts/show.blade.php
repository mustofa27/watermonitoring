@extends('layouts.app')

@section('title', 'Alert Details')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-bell"></i> Alert Details</h1>
            <p class="lead mb-0">Detailed information about this alert</p>
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
            <div class="col-md-12">
                <a href="{{ route('alerts.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Alerts
                </a>
                @if(!$alert->resolved_at)
                    <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check"></i> Mark as Resolved
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('alerts.unresolve', $alert) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-undo"></i> Mark as Unresolved
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header {{ !$alert->resolved_at ? 'bg-danger text-white' : 'bg-success text-white' }}">
                <h5 class="mb-0">
                    <i class="fas fa-{{ !$alert->resolved_at ? 'exclamation-triangle' : 'check-circle' }}"></i>
                    Alert #{{ $alert->id }} - {{ !$alert->resolved_at ? 'ACTIVE' : 'RESOLVED' }}
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="detail-item">
                            <label class="text-muted small mb-1"><i class="fas fa-tag"></i> Alert Type</label>
                            <div class="fw-semibold">
                                <span class="badge bg-{{ $alert->type === 'LOW_LEVEL' ? 'danger' : 'warning' }} fs-6">
                                    {{ $alert->type }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <label class="text-muted small mb-1"><i class="fas fa-water"></i> Water Tank</label>
                            <div class="fw-semibold">
                                <a href="{{ route('tandons.show', $alert->tandon) }}" class="text-decoration-none">
                                    {{ $alert->tandon->name }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-12">
                        <div class="detail-item">
                            <label class="text-muted small mb-1"><i class="fas fa-comment"></i> Alert Message</label>
                            <div class="fw-semibold alert alert-warning">
                                {{ $alert->message }}
                            </div>
                        </div>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <label class="text-muted small mb-1"><i class="fas fa-clock"></i> Triggered At</label>
                            <div class="fw-semibold">{{ $alert->triggered_at->format('d M Y, H:i:s') }}</div>
                            <small class="text-muted">{{ $alert->triggered_at->diffForHumans() }}</small>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <label class="text-muted small mb-1"><i class="fas fa-{{ $alert->resolved_at ? 'check-circle' : 'hourglass-half' }}"></i> Resolved At</label>
                            @if($alert->resolved_at)
                                <div class="fw-semibold text-success">{{ $alert->resolved_at->format('d M Y, H:i:s') }}</div>
                                <small class="text-muted">{{ $alert->resolved_at->diffForHumans() }}</small>
                            @else
                                <div class="fw-semibold text-danger">Not yet resolved</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12"><hr></div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <label class="text-muted small mb-1"><i class="fas fa-building"></i> Building</label>
                            <div class="fw-semibold">{{ $alert->tandon->building_name ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="detail-item">
                            <label class="text-muted small mb-1"><i class="fas fa-tag"></i> Tank Type</label>
                            <div class="fw-semibold">
                                <span class="badge bg-secondary">{{ $alert->tandon->type }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <form method="POST" action="{{ route('alerts.destroy', $alert) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this alert?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete Alert
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .detail-item {
            margin-bottom: 1rem;
        }
    </style>
@endsection
