@extends('layouts.app')

@section('title', 'Alert Details')

@section('content')
    <x-ui.page-header
        title="Alert Details"
        subtitle="Detailed information about this alert"
        icon="fa-bell"
    >
        <a href="{{ route('alerts.index') }}" class="btn wm-btn wm-btn--secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Alerts
        </a>
        @if(!$alert->resolved_at)
            <form method="POST" action="{{ route('alerts.resolve', $alert) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn wm-btn wm-btn--primary">
                    <i class="fas fa-check"></i>
                    Mark as Resolved
                </button>
            </form>
        @else
            <form method="POST" action="{{ route('alerts.unresolve', $alert) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn wm-btn wm-btn--secondary">
                    <i class="fas fa-undo"></i>
                    Mark as Unresolved
                </button>
            </form>
        @endif
    </x-ui.page-header>

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card :title="'Alert #' . $alert->id" :subtitle="$alert->resolved_at ? 'Resolved alert record' : 'Active alert record'" icon="fa-triangle-exclamation">
            <div class="wm-detail-grid">
                <div class="wm-detail-item"><p class="wm-detail-item__label">Alert Type</p><p class="wm-detail-item__value"><x-ui.status-badge :tone="$alert->type === 'LOW_LEVEL' ? 'danger' : 'warning'">{{ $alert->type }}</x-ui.status-badge></p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Water Tank</p><p class="wm-detail-item__value"><a href="{{ route('tandons.show', $alert->tandon) }}">{{ $alert->tandon->name }}</a></p></div>
                <div class="wm-detail-item wm-form-field--full"><p class="wm-detail-item__label">Alert Message</p><p class="wm-detail-item__value">{{ $alert->message }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Triggered At</p><p class="wm-detail-item__value">{{ $alert->triggered_at->format('d M Y, H:i:s') }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Resolved At</p><p class="wm-detail-item__value">{{ $alert->resolved_at ? $alert->resolved_at->format('d M Y, H:i:s') : 'Not yet resolved' }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Building</p><p class="wm-detail-item__value">{{ $alert->tandon->building_name ?? '-' }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Tank Type</p><p class="wm-detail-item__value">{{ $alert->tandon->type }}</p></div>
            </div>

            <div class="mt-4">
                <form method="POST" action="{{ route('alerts.destroy', $alert) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this alert?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete Alert
                    </button>
                </form>
            </div>
        </x-ui.card>
    </div>
@endsection
