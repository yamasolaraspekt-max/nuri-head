@props([
    'variant' => 'secondary',
    'href' => null,
    'icon' => null,
])
@php
    $v = $variant === 'primary' ? 'primary' : 'secondary';
    $classes = 'al-btn al-btn--' . $v;
    $tag = $href ? 'a' : 'button';
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" style="width:15px;height:15px;" aria-hidden="true"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $attributes->get('type', 'button') }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i data-lucide="{{ $icon }}" style="width:15px;height:15px;" aria-hidden="true"></i>
        @endif
        {{ $slot }}
    </button>
@endif
