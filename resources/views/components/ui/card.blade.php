@props([
    'title' => null,
    'icon' => null,
    'subtitle' => null,
])

<section {{ $attributes->class(['wm-card']) }}>
    @if($title)
        <header class="wm-card__header">
            <div>
                <h2 class="wm-card__title">
                    @if($icon)
                        <i class="fas {{ $icon }}"></i>
                    @endif
                    {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="wm-card__subtitle">{{ $subtitle }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="wm-card__actions">{{ $actions }}</div>
            @endisset
        </header>
    @endif

    <div class="wm-card__body">
        {{ $slot }}
    </div>
</section>
