@props([
    'variant' => 'info',
    'icon' => null,
])
@php
    // Pill traegt IMMER Farbe UND Text (nie nur eingefaerbt). Unbekannte Variante -> info.
    $variants = ['danger', 'warning', 'success', 'info'];
    $v = in_array($variant, $variants, true) ? $variant : 'info';
@endphp
<span {{ $attributes->merge(['class' => 'al-pill al-pill--' . $v]) }}>
    @if ($icon)
        <i data-lucide="{{ $icon }}" class="al-pill-icon" style="width:13px;height:13px;" aria-hidden="true"></i>
    @endif
    {{ $slot }}
</span>
