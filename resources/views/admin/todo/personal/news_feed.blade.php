{{-- resources/views/partials/news_feed.blade.php --}}

<div id="sa-news-feed"
     class="sa-feed"
     data-feed-url="{{ route('personal-tasks.feed.items') }}">
    <div class="sa-feed-left">
        <div class="sa-feed-icon">
            {{-- small lightning SVG --}}
            <svg viewBox="0 0 24 24" aria-hidden="true">
                <path d="M13 2L4 14h6l-1 8 9-12h-6z"
                      fill="currentColor"/>
            </svg>
        </div>
        <div class="sa-feed-label">BENACHRICHTIGUNG</div>
    </div>

    <div class="sa-feed-main">
        <div class="sa-feed-line">
            <span class="sa-feed-pill" data-feed-badge>—</span>
            <span class="sa-feed-title" data-feed-title>Keine Benachrichtigungen</span>
            <span class="sa-feed-time" data-feed-time>–</span>
        </div>
        <div class="sa-feed-sub" data-feed-message>
            Lade Daten…
        </div>
    </div>

    <div class="sa-feed-controls">
        <button type="button" class="sa-feed-btn" data-feed-action="prev" title="Zurück">
            <span>&#9664;</span>
        </button>
        <button type="button" class="sa-feed-btn" data-feed-action="toggle" title="Pause">
            <span data-feed-icon="toggle">&#10074;&#10074;</span> {{-- || --}}
        </button>
        <button type="button" class="sa-feed-btn" data-feed-action="next" title="Weiter">
            <span>&#9654;</span>
        </button>
    </div>
</div>
