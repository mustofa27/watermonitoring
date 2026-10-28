@props([
    'icon' => 'fa-inbox',
    'title' => 'No data found',
    'message' => null,
])

<div class="wm-empty-state">
    <i class="fas {{ $icon }} wm-empty-state__icon"></i>
    <h3 class="wm-empty-state__title">{{ $title }}</h3>
    @if($message)
        <p class="wm-empty-state__message">{{ $message }}</p>
    @endif

    @if(trim($slot) !== '')
        <div class="wm-empty-state__actions">
            {{ $slot }}
        </div>
    @endif
</div>
