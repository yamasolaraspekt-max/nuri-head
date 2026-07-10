@props([
    'title' => 'Nichts offen — alles erledigt',
    'text' => null,
    'icon' => 'check-circle-2',
])
{{-- Empty-State: sagt, dass alles erledigt ist (nicht „keine Daten"); optionaler Aktions-Slot. --}}
<div {{ $attributes->merge(['class' => 'al-empty']) }}>
    <i data-lucide="{{ $icon }}" class="al-empty-icon" aria-hidden="true"></i>
    <p class="al-empty-title">{{ $title }}</p>
    @if ($text)
        <p class="al-empty-text">{{ $text }}</p>
    @endif
    @isset($action)
        <div>{{ $action }}</div>
    @endisset
</div>
