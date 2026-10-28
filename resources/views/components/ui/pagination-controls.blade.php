@props([
    'paginator',
    'perPageOptions' => [10, 25, 50, 100],
])

@if($paginator->count() > 0)
    <div class="wm-pagination-wrap">
        <p class="wm-pagination-meta">
            Showing {{ $paginator->firstItem() ?? 0 }} to {{ $paginator->lastItem() ?? 0 }} of {{ $paginator->total() }} records
        </p>

        <div class="wm-pagination-controls">
            <form method="GET" class="wm-per-page-form">
                @foreach(request()->except(['per_page', 'page']) as $queryKey => $queryValue)
                    @if(is_array($queryValue))
                        @foreach($queryValue as $item)
                            <input type="hidden" name="{{ $queryKey }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
                    @endif
                @endforeach
                <label for="per-page-{{ $paginator->getPageName() }}">Items per page</label>
                <select id="per-page-{{ $paginator->getPageName() }}" name="per_page" onchange="this.form.submit()" class="form-select form-select-sm">
                    @foreach($perPageOptions as $size)
                        <option value="{{ $size }}" @selected((int) request('per_page', $paginator->perPage()) === (int) $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>

            <div>
                {{ $paginator->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endif
