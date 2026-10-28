@extends('layouts.app')

@section('title', 'Water Tanks Management')

@section('content')
    <x-ui.page-header
        title="Water Tanks Management"
        subtitle="Manage all water tanks and their settings"
        icon="fa-water"
    >
        <a href="{{ route('tandons.create') }}" class="btn wm-btn wm-btn--primary">
            <i class="fas fa-plus-circle"></i>
            Add New Water Tank
        </a>
    </x-ui.page-header>

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card title="Water Tanks Directory" icon="fa-list" subtitle="Grouped for operations, telemetry, and maintenance">
            <x-ui.data-table
                id="tandons-table"
                :bulk-action="route('tandons.bulk-destroy')"
                :bulk-actions="['delete' => 'Delete selected']"
            >
                <thead>
                    <tr>
                        <th style="width: 48px;"><input type="checkbox" data-master-select></th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Building</th>
                        <th>Area (m²)</th>
                        <th>Parent</th>
                        <th class="text-center">Pump Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tandons as $tandon)
                        <tr>
                            <td><input type="checkbox" data-row-select value="{{ $tandon->id }}"></td>
                            <td><strong>{{ $tandon->name }}</strong></td>
                            <td><x-ui.status-badge tone="neutral">{{ $tandon->type }}</x-ui.status-badge></td>
                            <td>{{ $tandon->building_name ?? '-' }}</td>
                            <td>{{ $tandon->cross_section_area !== null ? num_id($tandon->cross_section_area) : '-' }}</td>
                            <td>{{ $tandon->parent->name ?? '-' }}</td>
                            <td class="text-center">
                                @if($tandon->pump_status === 1)
                                    <x-ui.status-badge tone="success">ON</x-ui.status-badge>
                                @elseif($tandon->pump_status === 0)
                                    <x-ui.status-badge tone="neutral">OFF</x-ui.status-badge>
                                @else
                                    <x-ui.status-badge tone="warning">UNKNOWN</x-ui.status-badge>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="wm-table-actions justify-content-center">
                                    <a href="{{ route('tandon-readings.index', $tandon) }}" class="btn btn-sm btn-outline-secondary" title="View Readings"><i class="fas fa-chart-line"></i></a>
                                    <a href="{{ route('tandons.show', $tandon) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('tandons.edit', $tandon) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form method="POST" action="{{ route('tandons.destroy', $tandon) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this water tank?')" title="Delete"><i class="fas fa-trash"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('tandons.truncate-data', $tandon) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-dark" onclick="return confirm('Are you sure you want to delete ALL readings and usages for this tank? This cannot be undone.')" title="Delete All Readings & Usages"><i class="fas fa-eraser"></i></button>
                                    </form>
                                    <form method="POST" action="{{ route('tandons.pump-on', $tandon) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm {{ $tandon->pump_status === 1 ? 'btn-danger' : 'btn-success' }}" onclick="return confirm('{{ $tandon->pump_status === 1 ? 'Turn OFF' : 'Turn ON' }} pump for this water tank?')" title="{{ $tandon->pump_status === 1 ? 'Turn Pump OFF' : 'Turn Pump ON' }}"><i class="fas fa-power-off"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.empty-state icon="fa-inbox" title="No water tanks found" message="Add your first water tank to start monitoring.">
                                    <a href="{{ route('tandons.create') }}" class="btn wm-btn wm-btn--primary">Create Water Tank</a>
                                </x-ui.empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.data-table>

            <x-ui.pagination-controls :paginator="$tandons" />
        </x-ui.card>
    </div>
@endsection
