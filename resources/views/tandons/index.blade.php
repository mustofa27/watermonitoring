@extends('layouts.app')

@section('title', 'Water Tanks Management')

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1><i class="fas fa-water"></i> Water Tanks Management</h1>
            <p class="lead mb-0">Manage all water tanks and their settings</p>
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
            <div class="col-md-12 text-end">
                <a href="{{ route('tandons.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle"></i> Add New Water Tank
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="px-4 py-3">Name</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Building</th>
                                <th class="px-4 py-3">Area (m²)</th>
                                <th class="px-4 py-3">Parent</th>
                                <th class="px-4 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tandons as $tandon)
                                <tr>
                                    <td class="px-4 py-3">
                                        <strong>{{ $tandon->name }}</strong>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-secondary">{{ $tandon->type }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $tandon->building_name ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $tandon->cross_section_area !== null ? num_id($tandon->cross_section_area) : '-' }}</td>
                                    <td class="px-4 py-3">{{ $tandon->parent->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tandon-readings.index', $tandon) }}" class="btn btn-action btn-readings" title="View Readings">
                                                <i class="fas fa-chart-line"></i>
                                            </a>
                                            <a href="{{ route('tandons.show', $tandon) }}" class="btn btn-action btn-view" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tandons.edit', $tandon) }}" class="btn btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('tandons.destroy', $tandon) }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this water tank?')" title="Delete">
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
                                        <p class="mb-0">No water tanks found. Click "Add New Water Tank" to get started.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($tandons->hasPages())
            <div class="mt-4">
                {{ $tandons->links() }}
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

        .btn-edit {
            background-color: #ffc800;
            color: #000;
        }

        .btn-edit:hover {
            background-color: #e6b400;
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

        .btn-readings {
            background-color: #17a2b8;
            color: white;
        }

        .btn-readings:hover {
            background-color: #138496;
            color: white;
        }
    </style>
@endsection
