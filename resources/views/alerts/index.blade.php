@extends('layouts.app')

@section('title', 'System Alerts')

@section('content')
    <x-ui.page-header
        title="System Alerts"
        subtitle="Monitor and manage water tank alerts"
        icon="fa-bell"
    />

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        @if($activeAlerts->count() > 0)
            <x-ui.card title="Active Alerts ({{ $activeAlerts->count() }})" icon="fa-exclamation-triangle" class="mb-4">
                <x-slot:actions>
                    <form method="POST" action="{{ route('alerts.resolveAll') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn wm-btn wm-btn--secondary btn-sm" onclick="return confirm('Resolve all active alerts?')">
                            <i class="fas fa-check-double"></i>
                            Resolve All
                        </button>
                    </form>
                </x-slot:actions>

                <div class="list-group list-group-flush">
                    @foreach($activeAlerts as $alert)
                        <div class="list-group-item border-0 px-0 py-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <x-ui.status-badge :tone="$alert->type === 'LOW_LEVEL' ? 'danger' : 'warning'">{{ $alert->type }}</x-ui.status-badge>
                                        <strong>{{ $alert->tandon->name }}</strong>
                                    </div>
                                    <p class="mb-1">{{ $alert->message }}</p>
                                    <small class="text-muted">{{ $alert->triggered_at->format('d M Y, H:i:s') }} ({{ $alert->triggered_at->diffForHumans() }})</small>
                                </div>
                                <div class="wm-table-actions">
                                    <a href="{{ route('alerts.show', $alert) }}" class="btn btn-sm btn-outline-info" title="View Details"><i class="fas fa-eye"></i></a>
                                    <form method="POST" action="{{ route('alerts.resolve', $alert) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Resolve"><i class="fas fa-check"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-ui.card>
        @else
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                No active alerts. All systems operating normally.
            </div>
        @endif

        <x-ui.card title="Alert History" icon="fa-history">
            <x-ui.data-table
                id="alerts-table"
                :bulk-action="route('alerts.bulk-action')"
                :bulk-actions="['resolve' => 'Mark as resolved', 'unresolve' => 'Mark as unresolved', 'delete' => 'Delete selected']"
            >
                <thead>
                    <tr>
                        <th style="width: 48px;"><input type="checkbox" data-master-select></th>
                        <th>Type</th>
                        <th>Water Tank</th>
                        <th>Message</th>
                        <th>Triggered</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($alerts as $alert)
                        <tr>
                            <td><input type="checkbox" data-row-select value="{{ $alert->id }}"></td>
                            <td>
                                <x-ui.status-badge :tone="$alert->type === 'LOW_LEVEL' ? 'danger' : 'warning'">
                                    {{ $alert->type }}
                                </x-ui.status-badge>
                            </td>
                            <td><a href="{{ route('tandons.show', $alert->tandon) }}">{{ $alert->tandon->name }}</a></td>
                            <td>{{ Str::limit($alert->message, 70) }}</td>
                            <td><small>{{ $alert->triggered_at->format('d M Y, H:i') }}</small></td>
                            <td>
                                @if($alert->resolved_at)
                                    <x-ui.status-badge tone="success">Resolved</x-ui.status-badge>
                                    <div><small class="text-muted">{{ $alert->resolved_at->format('d M Y, H:i') }}</small></div>
                                @else
                                    <x-ui.status-badge tone="danger">Active</x-ui.status-badge>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="wm-table-actions justify-content-center">
                                    <a href="{{ route('alerts.show', $alert) }}" class="btn btn-sm btn-outline-info" title="View"><i class="fas fa-eye"></i></a>
                                    @if(!$alert->resolved_at)
                                        <form method="POST" action="{{ route('alerts.resolve', $alert) }}">@csrf<button type="submit" class="btn btn-sm btn-outline-success" title="Resolve"><i class="fas fa-check"></i></button></form>
                                    @else
                                        <form method="POST" action="{{ route('alerts.unresolve', $alert) }}">@csrf<button type="submit" class="btn btn-sm btn-outline-warning" title="Mark as Unresolved"><i class="fas fa-undo"></i></button></form>
                                    @endif
                                    <form method="POST" action="{{ route('alerts.destroy', $alert) }}">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this alert?')" title="Delete"><i class="fas fa-trash"></i></button></form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"><x-ui.empty-state title="No alerts recorded yet" /></td>
                        </tr>
                    @endforelse
                </tbody>
            </x-ui.data-table>

            <x-ui.pagination-controls :paginator="$alerts" />
        </x-ui.card>
    </div>
@endsection
