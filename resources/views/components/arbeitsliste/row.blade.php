@props([
    'title' => '',
    'meta' => null,
    'amount' => null,
])
{{--
    Eine Arbeitslisten-Zeile: Titel + Meta links, Betrag rechtsbuendig (tabular-nums via .al-row-amount),
    dann Pill + Primaer-Aktion (benannte Slots). Die Zeile selbst ist NICHT interaktiv; die Aktion ist
    ein echtes <a>/<button> (fokussierbar, sinnvolle Tab-Reihenfolge).
--}}
<div {{ $attributes->merge(['class' => 'al-row']) }}>
    <div class="al-row-main">
        <span class="al-row-title">{{ $title }}</span>
        @if (!is_null($meta) && $meta !== '')
            <span class="al-row-meta">{{ $meta }}</span>
        @endif
    </div>
    <div class="al-row-side">
        @if (!is_null($amount) && $amount !== '')
            <span class="al-row-amount">{{ $amount }}</span>
        @endif
        @isset($pill)
            {{ $pill }}
        @endisset
        @isset($action)
            {{ $action }}
        @endisset
    </div>
</div>
