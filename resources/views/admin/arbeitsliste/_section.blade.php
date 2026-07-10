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
        <span class="al-badge {{ count($category['items']) === 0 ? 'is-zero' : '' }}">
            {{ count($category['items']) }}
        </span>
    </div>

    @if (empty($category['items']))
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
    @endif
</section>
