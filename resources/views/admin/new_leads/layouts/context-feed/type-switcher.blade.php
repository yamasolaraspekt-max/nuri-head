@php
    $currentFeed = $currentFeed ?? 'notes';

    $feedTypes = [
        [
            'key' => 'notes',
            'label' => 'Aktuelle Notizen',
            'subtitle' => 'Kunden-, Objekt- und Produktnotizen',
            'icon' => 'message-square',
            'color' => 'blue',
        ],
        [
            'key' => 'offers',
            'label' => 'Angebot',
            'subtitle' => 'Offer Folder, Angebote und Kommentare',
            'icon' => 'folder',
            'color' => 'orange',
        ],
        [
            'key' => 'deals',
            'label' => 'Auftrag',
            'subtitle' => 'Aufträge und Auftragsnotizen',
            'icon' => 'package',
            'color' => 'blue',
        ],
        [
            'key' => 'appointments',
            'label' => 'Termine',
            'subtitle' => 'Kalender, Berichte und Kommentare',
            'icon' => 'calendar',
            'color' => 'green',
        ],
        [
            'key' => 'tickets',
            'label' => 'Tickets',
            'subtitle' => 'Probleme, Aufgaben und Kommentare',
            'icon' => 'alert-triangle',
            'color' => 'pink',
        ],
        [
            'key' => 'tasks',
            'label' => 'Aufgaben',
            'subtitle' => 'Tasks, Schritte und Kommentare',
            'icon' => 'check-square',
            'color' => 'orange',
        ],
        [
            'key' => 'customer_reports',
            'label' => 'Kundenberichte',
            'subtitle' => 'Berichte und Report-Kommentare',
            'icon' => 'file-text',
            'color' => 'green',
        ],
    ];

    $activeFeed = collect($feedTypes)->firstWhere('key', $currentFeed) ?? $feedTypes[0];
@endphp

<div class="ma-note-type-switcher" id="maNoteTypeSwitcher">
    <button type="button" class="ma-note-type-current" data-note-feed-current>
        <span class="ma-note-type-icon bg-{{ $activeFeed['color'] }}">
            <i data-feather="{{ $activeFeed['icon'] }}"></i>
        </span>

        <span class="ma-note-type-text">
            <strong>{{ $activeFeed['label'] }}</strong>
            <small>{{ $activeFeed['subtitle'] }}</small>
        </span>

        <i data-feather="chevron-down" class="ma-note-type-chevron"></i>
    </button>

    <div class="ma-note-type-menu" data-note-feed-menu>
        @foreach($feedTypes as $feed)
            <button type="button" class="ma-note-type-item {{ $currentFeed === $feed['key'] ? 'active' : '' }}"
                data-feed-type="{{ $feed['key'] }}" data-label="{{ $feed['label'] }}"
                data-subtitle="{{ $feed['subtitle'] }}" data-icon="{{ $feed['icon'] }}" data-color="{{ $feed['color'] }}">
                <span class="ma-note-type-icon bg-{{ $feed['color'] }}">
                    <i data-feather="{{ $feed['icon'] }}"></i>
                </span>

                <span>
                    <strong>{{ $feed['label'] }}</strong>
                    <small>{{ $feed['subtitle'] }}</small>
                </span>
            </button>
        @endforeach
    </div>
</div>