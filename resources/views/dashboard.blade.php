@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-chart-pie"></i> Water Usage Dashboard</h1>
            <p class="lead mb-0">Real-time monitoring of water usage across all water tanks</p>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mb-5">
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted d-block"><i class="fas fa-water"></i> Total Water Usage</small>
                                <h3 class="mb-0 mt-2">{{ number_format($totalUsage, 2) }} <small style="font-size: 0.6em;">L</small></h3>
                            </div>
                            <div style="font-size: 2.5rem; color: #17a2b8; opacity: 0.2;">
                                <i class="fas fa-liquid"></i>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">All-time usage recorded</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted d-block"><i class="fas fa-calendar-day"></i> Today's Usage</small>
                                <h3 class="mb-0 mt-2">{{ number_format($todayUsage, 2) }} <small style="font-size: 0.6em;">L</small></h3>
                            </div>
                            <div style="font-size: 2.5rem; color: #ffc800; opacity: 0.2;">
                                <i class="fas fa-sun"></i>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">{{ now()->format('d M Y') }}</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <small class="text-muted d-block"><i class="fas fa-building"></i> Total Water Tanks</small>
                                <h3 class="mb-0 mt-2">{{ count($tandons) }}</h3>
                            </div>
                            <div style="font-size: 2.5rem; color: #28a745; opacity: 0.2;">
                                <i class="fas fa-cube"></i>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-2">Active water tanks</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Water Tank Usage Cards -->
        <div class="card mb-4">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0"><i class="fas fa-list"></i> Water Usage by Water Tank (This Month)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr style="background-color: #212529;">
                                <th class="px-4 py-3" style="color: #ffc800;">Water Tank Name</th>
                                <th class="px-4 py-3" style="color: #ffc800;">Type</th>
                                <th class="px-4 py-3" style="color: #ffc800;">Building</th>
                                <th class="px-4 py-3 text-end" style="color: #ffc800;">This Month (L)</th>
                                <th class="px-4 py-3 text-end" style="color: #ffc800;">Total Usage (L)</th>
                                <th class="px-4 py-3 text-center" style="color: #ffc800;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tandonUsage as $usage)
                                <tr>
                                    <td class="px-4 py-3">
                                        <strong>{{ $usage['tandon']->name }}</strong>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-secondary">{{ $usage['tandon']->type }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $usage['tandon']->building_name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-end">
                                        <strong>{{ number_format($usage['this_month'], 2) }}</strong>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <strong>{{ number_format($usage['total_usage'], 2) }}</strong>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tandon-readings.index', $usage['tandon']) }}" class="btn btn-action btn-readings" title="View Readings">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                            <a href="{{ route('tandons.show', $usage['tandon']) }}" class="btn btn-action btn-view" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-5 text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p class="mb-0">No water tanks available. <a href="{{ route('tandons.index') }}">Create a water tank</a> to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Water Usages -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Recent Water Usage Records</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr style="background-color: #212529;">
                                        <th class="px-4 py-3" style="color: #ffc800;">Date</th>
                                        <th class="px-4 py-3" style="color: #ffc800;">Water Tank</th>
                                        <th class="px-4 py-3 text-end" style="color: #ffc800;">Volume Used (L)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recentUsages as $usage)
                                        <tr>
                                            <td class="px-4 py-3">
                                                <strong>{{ $usage->usage_date->format('d M Y') }}</strong>
                                            </td>
                                            <td class="px-4 py-3">
                                                <a href="{{ route('tandons.show', $usage->tandon) }}" class="text-decoration-none">
                                                    {{ $usage->tandon->name }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-end">
                                                <strong>{{ number_format($usage->volume_used, 3) }}</strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-5 text-center text-muted">
                                                No water usage records yet
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('tandons.index') }}" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-water"></i> Manage Water Tanks
                        </a>
                        <a href="{{ route('tandons.create') }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-plus-circle"></i> Add New Water Tank
                        </a>
                        <hr>
                        <p class="small text-muted mb-2"><i class="fas fa-info-circle"></i> Dashboard Information</p>
                        <div class="alert alert-info small mb-0">
                            <strong>Current System Status:</strong> All systems operational
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .page-header {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-family: "Montserrat", sans-serif;
            font-weight: 700;
            font-size: 2.5rem;
        }

        .stats-card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
            border-left: 4px solid #ffc800;
        }

        .stats-card .card-body {
            padding: 1.5rem;
        }

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

        .btn-readings {
            background-color: #6c757d;
            color: white;
        }

        .btn-readings:hover {
            background-color: #5a6268;
            color: white;
        }

        .btn-primary {
            background-color: #ffc800;
            border-color: #ffc800;
            color: #000;
            font-weight: 700;
            font-family: "Montserrat", sans-serif;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary:hover {
            background-color: #e6b400;
            border-color: #e6b400;
            color: #000;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            font-weight: 600;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #218838;
        }

        .card {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }

        .table thead th {
            border-bottom: 1px solid #dee2e6;
        }
    </style>
@endsection
