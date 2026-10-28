@extends('layouts.app')

@section('title', $tandon->name)

@section('content')
    <x-ui.page-header
        :title="$tandon->name"
        subtitle="Water tank details and specifications"
        icon="fa-circle-info"
    >
        <a href="{{ route('tandons.edit', $tandon) }}" class="btn wm-btn wm-btn--primary">
            <i class="fas fa-edit"></i>
            Edit
        </a>
        <a href="{{ route('tandons.index') }}" class="btn wm-btn wm-btn--secondary">
            <i class="fas fa-arrow-left"></i>
            Back
        </a>
    </x-ui.page-header>

    <div class="container wm-section">
        @include('layouts.partials.flash-messages')

        <x-ui.card title="Water Tank Information" icon="fa-water">
            <div class="wm-detail-grid">
                <div class="wm-detail-item"><p class="wm-detail-item__label">Water Tank Name</p><p class="wm-detail-item__value">{{ $tandon->name }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Type</p><p class="wm-detail-item__value">{{ $tandon->type }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Building Name</p><p class="wm-detail-item__value">{{ $tandon->building_name ?: '-' }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Parent Water Tank</p><p class="wm-detail-item__value">{{ $tandon->parent->name ?? '-' }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Cross Section Area</p><p class="wm-detail-item__value">@num($tandon->cross_section_area) m²</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Max Height</p><p class="wm-detail-item__value">@num($tandon->height_max) m</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Min Height</p><p class="wm-detail-item__value">@num($tandon->height_min) m</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Warning Height</p><p class="wm-detail-item__value">@num($tandon->height_warning) m</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Created At</p><p class="wm-detail-item__value">{{ $tandon->created_at->format('d M Y, H:i') }}</p></div>
                <div class="wm-detail-item"><p class="wm-detail-item__label">Updated At</p><p class="wm-detail-item__value">{{ $tandon->updated_at->format('d M Y, H:i') }}</p></div>
            </div>

            <div class="mt-4">
                <form method="POST" action="{{ route('tandons.destroy', $tandon) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this water tank?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i>
                        Delete Water Tank
                    </button>
                </form>
            </div>
        </x-ui.card>
    </div>
@endsection
