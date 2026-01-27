@extends('layouts.app')

@section('title', 'System Alerts')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-bell"></i> System Alerts</h1>
            <p class="lead mb-0">Monitor and manage water tank alerts</p>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Active Alerts Summary -->
        @if($activeAlerts->count() > 0)
            <div class="card mb-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Active Alerts ({{ $activeAlerts->count() }})</h5>
                        <form method="POST" action="{{ route('alerts.resolveAll') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light btn-sm" onclick="return confirm('Resolve all active alerts?')">
                                <i class="fas fa-check-double"></i> Resolve All
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($activeAlerts as $alert)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-{{ $alert->type === 'LOW_LEVEL' ? 'danger' : 'warning' }} me-2">{{ $alert->type }}</span>
                                            <strong>{{ $alert->tandon->name }}</strong>
                                        </div>
                                        <p class="mb-2">{{ $alert->message }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $alert->triggered_at->format('d M Y, H:i:s') }}
                                            ({{ $alert->triggered_at->diffForHumans() }})
                                        </small>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('alerts.show', $alert) }}" class="btn btn-sm btn-outline-info" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Resolve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> No active alerts. All systems operating normally.
            </div>
        @endif

        <!-- All Alerts -->
        <div class="card">
            <div class="card-header bg-light border-bottom">
                <h5 class="mb-0"><i class="fas fa-history"></i> Alert History</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr style="background-color: #212529;">
                                <th class="px-4 py-3" style="color: #ffc800;">Type</th>
                                <th class="px-4 py-3" style="color: #ffc800;">Water Tank</th>
                                <th class="px-4 py-3" style="color: #ffc800;">Message</th>
                                <th class="px-4 py-3" style="color: #ffc800;">Triggered</th>
                                <th class="px-4 py-3" style="color: #ffc800;">Status</th>
                                <th class="px-4 py-3 text-center" style="color: #ffc800;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alerts as $alert)
                                <tr class="{{ !$alert->resolved_at ? 'table-warning' : '' }}">
                                    <td class="px-4 py-3">
                                        <span class="badge bg-{{ $alert->type === 'LOW_LEVEL' ? 'danger' : 'warning' }}">
                                            {{ $alert->type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('tandons.show', $alert->tandon) }}" class="text-decoration-none">
                                            {{ $alert->tandon->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3">{{ Str::limit($alert->message, 60) }}</td>
                                    <td class="px-4 py-3">
                                        <small>{{ $alert->triggered_at->format('d M Y, H:i') }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($alert->resolved_at)
                                            <span class="badge bg-success">Resolved</span><br>
                                            <small class="text-muted">{{ $alert->resolved_at->format('d M Y, H:i') }}</small>
                                        @else
                                            <span class="badge bg-danger">Active</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('alerts.show', $alert) }}" class="btn btn-action btn-view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(!$alert->resolved_at)
                                                <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-action btn-success" title="Resolve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('alerts.unresolve', $alert) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-action btn-warning" title="Mark as Unresolved">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('alerts.destroy', $alert) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action btn-delete" onclick="return confirm('Delete this alert?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-5 text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p class="mb-0">No alerts recorded yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($alerts->hasPages())
            <div class="mt-4">
                {{ $alerts->links() }}
            </div>
        @endif
    </div>

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

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
            color: white;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #000;
        }

        .btn-warning:hover {
            background-color: #e0a800;
            color: #000;
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
