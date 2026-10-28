@props([
    'paginator',
    'perPageOptions' => [10, 25, 50, 100],
    'noun' => 'records',
])

@if($paginator->total() > 0)
    @php $pageName = $paginator->getPageName(); @endphp
    <div class="wm-pagination-wrap">
        <p class="wm-pagination-meta">
            Showing {{ number_format($paginator->firstItem()) }} to {{ number_format($paginator->lastItem()) }}
            of {{ number_format($paginator->total()) }} {{ $noun }}
        </p>

        <div class="wm-pagination-controls">
            <form method="GET" class="wm-per-page-form">
                @foreach(request()->except(['per_page', $pageName]) as $queryKey => $queryValue)
                    @if(is_array($queryValue))
                        @foreach($queryValue as $item)
                            <input type="hidden" name="{{ $queryKey }}[]" value="{{ $item }}">
                        @endforeach
                    @else
                        <input type="hidden" name="{{ $queryKey }}" value="{{ $queryValue }}">
                    @endif
                @endforeach
                <label for="per-page-{{ $pageName }}">Items per page</label>
                <select id="per-page-{{ $pageName }}" name="per_page" onchange="this.form.submit()" class="form-select form-select-sm">
                    @foreach($perPageOptions as $size)
                        <option value="{{ $size }}" @selected((int) $paginator->perPage() === (int) $size)>{{ $size }}</option>
                    @endforeach
                </select>
            </form>

            {{ $paginator->onEachSide(1)->links('pagination.wm') }}
        </div>
    </div>
@endif
