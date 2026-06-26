@php
    $message = $message ?? 'Keine Einträge gefunden.';
    $icon = $icon ?? 'inbox';
    $title = $title ?? 'Leer';
@endphp

<div class="ma-feed-empty">
    <div class="d-flex align-items-center">
        <span class="ma-note-type-icon bg-blue mr-2">
            <i data-feather="{{ $icon }}"></i>
        </span>

        <div>
            <div class="ma-feed-title">{{ $title }}</div>
            <div class="ma-feed-meta">{{ $message }}</div>
        </div>
    </div>
</div>