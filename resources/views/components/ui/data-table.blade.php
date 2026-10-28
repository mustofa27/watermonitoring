@props([
    'id',
    'bulkAction' => null,
    'bulkActions' => [],
    'selectName' => 'ids[]',
    'clientExport' => false,
])

@php
    $baseSelectName = str_ends_with($selectName, '[]') ? substr($selectName, 0, -2) : $selectName;
    $hasForm = $bulkAction && count($bulkActions) > 0;
@endphp

<div class="wm-table-shell" data-table-shell id="{{ $id }}-shell">
    @if($hasForm)
        <form method="POST" action="{{ $bulkAction }}" class="wm-bulk-toolbar" data-bs-theme="dark" data-bulk-toolbar hidden>
            @csrf
            @foreach(request()->only('per_page', 'page') as $queryKey => $queryValue)
                <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
            @endforeach

            <p class="wm-bulk-toolbar__count"><strong data-bulk-count>0</strong> selected</p>

            <div class="wm-bulk-toolbar__controls">
                @if(count($bulkActions) === 1)
                    <input type="hidden" name="action" value="{{ array_key_first($bulkActions) }}">
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> {{ reset($bulkActions) }}
                    </button>
                @else
                    <select name="action" class="form-select form-select-sm" aria-label="Bulk action" required>
                        <option value="">Choose action...</option>
                        @foreach($bulkActions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                @endif
                <button type="button" class="btn btn-sm btn-link" data-bulk-clear>Clear</button>
            </div>

            <template data-selection-template>
                <input type="hidden" name="{{ $baseSelectName }}[]" value="__VALUE__">
            </template>
        </form>
    @elseif($clientExport)
        <div class="wm-bulk-toolbar" data-bs-theme="dark" data-bulk-toolbar hidden>
            <p class="wm-bulk-toolbar__count"><strong data-bulk-count>0</strong> selected</p>
            <div class="wm-bulk-toolbar__controls">
                <button type="button" class="btn btn-sm btn-primary" data-export="csv"><i class="fas fa-file-csv"></i> Export CSV</button>
                <button type="button" class="btn btn-sm btn-secondary" data-export="print"><i class="fas fa-file-pdf"></i> Print / PDF</button>
                <button type="button" class="btn btn-sm btn-link" data-bulk-clear>Clear</button>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover align-middle wm-table" id="{{ $id }}">
            {{ $slot }}
        </table>
    </div>
</div>
