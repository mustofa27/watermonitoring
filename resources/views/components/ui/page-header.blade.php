@props([
    'title',
    'subtitle' => null,
    'icon' => 'fa-layer-group',
])

<header class="wm-page-header">
    <div class="container wm-page-header__inner">
        <div>
            <h1 class="wm-page-title">
                <i class="fas {{ $icon }}"></i>
                {{ $title }}
            </h1>
            @if($subtitle)
                <p class="wm-page-subtitle">{{ $subtitle }}</p>
            @endif
        </div>

        @if(trim($slot) !== '')
            <div class="wm-page-header__actions">
                {{ $slot }}
            </div>
        @endif
    </div>
</header>
