{{--
    Arbeitsliste — eine Kategorie-Sektion (nur im 'data'-Zustand gerendert).
    Erwartet: $id, $icon, $heading, $category(['items'=>...]), $emptyText,
              $pillVariant, $pillIcon, $pillLabel, $actionIcon, $actionLabel.
    Ist die EINZELNE Sektion leer -> nur eine leise Inline-Zeile (role="status"),
    NICHT der grosse globale Empty-State (unterschiedliches visuelles Gewicht).
--}}
<section class="al-section" aria-labelledby="al-sec-{{ $id }}">
    <div class="al-section-head">
        <i data-lucide="{{ $icon }}" class="al-section-icon" aria-hidden="true"></i>
        <h2 class="al-section-title" id="al-sec-{{ $id }}">{{ $heading }}</h2>
        @if ($category['ok'])
            <span class="al-badge {{ count($category['items']) === 0 ? 'is-zero' : '' }}">
                {{ count($category['items']) }}
            </span>
        @endif
    </div>

    @if (! $category['ok'])
        {{-- Sektions-Fehler: role="alert" NUR in dieser Sektion (kein seitenweiter Alarm bei
             Teilausfall). Der In-Sektion-Streifen .al-error (ohne .al-error-panel = ohne Rahmen). --}}
        <div class="al-error" role="alert">
            <i data-lucide="alert-octagon" class="al-error-icon" aria-hidden="true"></i>
            <span>{{ $category['error'] ?? 'Diese Liste konnte gerade nicht geladen werden.' }}</span>
        </div>
    @elseif (empty($category['items']))
        <p class="al-section-empty" role="status">{{ $emptyText }}</p>
    @else
        <div class="al-list" role="list">
            @foreach ($category['items'] as $row)
                <x-arbeitsliste.row
                    role="listitem"
                    :title="$row['title']"
                    :meta="$row['meta']"
                    :amount="$row['amount']">
                    <x-slot:pill>
                        <x-arbeitsliste.pill :variant="$pillVariant" :icon="$pillIcon">{{ $pillLabel }}</x-arbeitsliste.pill>
                    </x-slot:pill>
                    <x-slot:action>
                        <x-arbeitsliste.button variant="primary" :href="$row['href']" :icon="$actionIcon">
                            {{ $actionLabel }}
                        </x-arbeitsliste.button>
                    </x-slot:action>
                </x-arbeitsliste.row>
            @endforeach
        </div>

        {{-- „mehr"-Hinweis: es gibt mehr als das Sicht-Limit (per LIMIT+1 erkannt, kein count(*)).
             Fuehrt eine Vollansicht-Route existiert (more_href != '#') -> Link; sonst nur Hinweis. --}}
        @if (($category['has_more'] ?? false))
            @php($moreHref = $category['more_href'] ?? '#')
            @if ($moreHref !== '#')
                <a class="al-section-more" href="{{ $moreHref }}">
                    <span>Weitere anzeigen</span>
                    <i data-lucide="arrow-right" class="al-section-more-icon" aria-hidden="true"></i>
                </a>
            @else
                <p class="al-section-more al-section-more--static" role="note">Weitere vorhanden — die {{ count($category['items']) }} dringendsten zuerst.</p>
            @endif
        @endif
    @endif
</section>
