@props([
    'tone' => 'neutral',
    'size' => 'md',
])

@php
    $toneMap = [
        'success' => 'wm-badge--success',
        'danger' => 'wm-badge--danger',
        'warning' => 'wm-badge--warning',
        'info' => 'wm-badge--info',
        'neutral' => 'wm-badge--neutral',
    ];

    $sizeMap = [
        'sm' => 'wm-badge--sm',
        'md' => 'wm-badge--md',
    ];
@endphp

<span {{ $attributes->class(['wm-badge', $toneMap[$tone] ?? $toneMap['neutral'], $sizeMap[$size] ?? $sizeMap['md']]) }}>
    {{ $slot }}
</span>
