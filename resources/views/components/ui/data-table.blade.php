@props([
    'id',
    'bulkAction' => null,
    'bulkActions' => [],
    'selectName' => 'ids[]',
])

@php
    $tableId = $id;
    $toolbarId = $tableId.'-bulk-toolbar';
    $counterId = $tableId.'-bulk-count';
    $baseSelectName = str_ends_with($selectName, '[]') ? substr($selectName, 0, -2) : $selectName;
@endphp

<div class="wm-table-shell" data-table-shell data-table-id="{{ $tableId }}">
    @if($bulkAction && count($bulkActions) > 0)
        <form method="POST" action="{{ $bulkAction }}" id="{{ $toolbarId }}" class="wm-bulk-toolbar" data-bulk-toolbar hidden>
            @csrf
            @foreach(request()->only('per_page', 'page') as $queryKey => $queryValue)
                <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
            @endforeach
            <p class="mb-0">
                <strong data-bulk-count id="{{ $counterId }}">0</strong> selected
            </p>
            <div class="d-flex align-items-center gap-2">
                @if(count($bulkActions) === 1)
                    @php $singleValue = array_key_first($bulkActions); @endphp
                    <input type="hidden" name="action" value="{{ $singleValue }}">
                    <span class="small text-muted">{{ $bulkActions[$singleValue] }}</span>
                @else
                    <select name="action" class="form-select form-select-sm" required>
                        <option value="">Choose action</option>
                        @foreach($bulkActions as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @endif
                <button type="submit" class="btn btn-sm btn-danger">
                    Apply
                </button>
            </div>
        </form>
    @endif

    <div class="table-responsive">
        <table class="table wm-table mb-0" id="{{ $tableId }}">
            {{ $slot }}
        </table>
    </div>

    @if($bulkAction)
        <template data-selection-template>
            <input type="hidden" name="{{ $baseSelectName }}[]" value="__VALUE__">
        </template>
    @endif
</div>
