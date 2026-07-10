@extends('admin.layouts.app')

@section('title')
    Arbeitsliste
@endsection

@section('style')
    {{-- Design-Tokens: EINE Wahrheit fuers Design (nur hier stehen Farb-Hex-Werte). --}}
    @include('admin.arbeitsliste._tokens')
@endsection

@section('content')
    @php
        $errored = !$overdueInvoices['ok'] || !$followUpTasks['ok'] || !$dealsNoInvoice['ok'];
        $allEmpty = empty($overdueInvoices['items'])
            && empty($followUpTasks['items'])
            && empty($dealsNoInvoice['items']);
    @endphp

    <div class="al-page">
        <header class="al-page-head">
            <h1 class="al-page-title">Was braucht mich jetzt?</h1>
            <p class="al-page-sub">
                Deine offenen Posten aus Rechnungen, Angeboten und Aufträgen — der nächste Schritt zuerst.
            </p>
        </header>

        @if ($allEmpty && !$errored)
            {{-- Globaler Leer-Zustand: alle drei Kategorien leer und fehlerfrei. --}}
            <section class="al-section" aria-label="Arbeitsliste leer">
                <x-arbeitsliste.empty-state
                    title="Nichts offen — alles erledigt"
                    text="Kein überfälliger Beleg, keine offene Angebots-Aufgabe, kein unberechneter Auftrag. Neue Posten erscheinen hier automatisch — sobald ein Lead in die Angebots-Phase wandert, ein Zahlungsziel überschritten wird oder ein Auftrag noch abzurechnen ist. Nichts weiter zu tun." />
            </section>
        @endif

        {{-- Sektionen nur zeigen, wenn NICHT der globale Leer-Zustand greift.
             errored -> Sektionen bleiben (mit Alerts), aber ohne Sektions-Empty-States (s.u.). --}}
        @unless ($allEmpty && !$errored)
        {{-- ============ 1) Überfällige Rechnungen ============ --}}
        <section class="al-section" aria-labelledby="al-sec-overdue">
            <div class="al-section-head">
                <i data-lucide="alert-triangle" class="al-section-icon" aria-hidden="true"></i>
                <h2 class="al-section-title" id="al-sec-overdue">Überfällige Rechnungen</h2>
                <span class="al-badge {{ count($overdueInvoices['items']) === 0 ? 'is-zero' : '' }}">
                    {{ count($overdueInvoices['items']) }}
                </span>
            </div>

            @if (!$overdueInvoices['ok'])
                <div class="al-error" role="alert">
                    <i data-lucide="alert-octagon" class="al-error-icon" aria-hidden="true"></i>
                    <span>{{ $overdueInvoices['error'] ?? 'Fehler beim Laden.' }}</span>
                </div>
            @elseif (empty($overdueInvoices['items']) && !$errored)
                <x-arbeitsliste.empty-state
                    title="Keine überfälligen Rechnungen"
                    text="Alle fälligen Rechnungen sind bezahlt oder noch nicht fällig." />
            @else
                <div class="al-list" role="list">
                    @foreach ($overdueInvoices['items'] as $row)
                        <x-arbeitsliste.row
                            role="listitem"
                            :title="$row['title']"
                            :meta="$row['meta']"
                            :amount="$row['amount']">
                            <x-slot:pill>
                                <x-arbeitsliste.pill variant="danger" icon="clock">überfällig</x-arbeitsliste.pill>
                            </x-slot:pill>
                            <x-slot:action>
                                <x-arbeitsliste.button variant="primary" :href="$row['href']" icon="receipt-text">
                                    Rechnung öffnen
                                </x-arbeitsliste.button>
                            </x-slot:action>
                        </x-arbeitsliste.row>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ============ 2) Offene Angebots-Aufgaben (A1) ============ --}}
        <section class="al-section" aria-labelledby="al-sec-followup">
            <div class="al-section-head">
                <i data-lucide="file-plus" class="al-section-icon" aria-hidden="true"></i>
                <h2 class="al-section-title" id="al-sec-followup">Offene Angebots-Aufgaben</h2>
                <span class="al-badge {{ count($followUpTasks['items']) === 0 ? 'is-zero' : '' }}">
                    {{ count($followUpTasks['items']) }}
                </span>
            </div>

            @if (!$followUpTasks['ok'])
                <div class="al-error" role="alert">
                    <i data-lucide="alert-octagon" class="al-error-icon" aria-hidden="true"></i>
                    <span>{{ $followUpTasks['error'] ?? 'Fehler beim Laden.' }}</span>
                </div>
            @elseif (empty($followUpTasks['items']) && !$errored)
                <x-arbeitsliste.empty-state
                    title="Keine offenen Angebots-Aufgaben"
                    text="Für alle Vorgänge ist das Angebot angestoßen." />
            @else
                <div class="al-list" role="list">
                    @foreach ($followUpTasks['items'] as $row)
                        <x-arbeitsliste.row
                            role="listitem"
                            :title="$row['title']"
                            :meta="$row['meta']"
                            :amount="$row['amount']">
                            <x-slot:pill>
                                <x-arbeitsliste.pill variant="warning" icon="file-text">Angebot fehlt</x-arbeitsliste.pill>
                            </x-slot:pill>
                            <x-slot:action>
                                <x-arbeitsliste.button variant="primary" :href="$row['href']" icon="arrow-right">
                                    Angebot erstellen
                                </x-arbeitsliste.button>
                            </x-slot:action>
                        </x-arbeitsliste.row>
                    @endforeach
                </div>
            @endif
        </section>

        {{-- ============ 3) Aufträge ohne Rechnung ============ --}}
        <section class="al-section" aria-labelledby="al-sec-deals">
            <div class="al-section-head">
                <i data-lucide="briefcase" class="al-section-icon" aria-hidden="true"></i>
                <h2 class="al-section-title" id="al-sec-deals">Aufträge ohne Rechnung</h2>
                <span class="al-badge {{ count($dealsNoInvoice['items']) === 0 ? 'is-zero' : '' }}">
                    {{ count($dealsNoInvoice['items']) }}
                </span>
            </div>

            @if (!$dealsNoInvoice['ok'])
                <div class="al-error" role="alert">
                    <i data-lucide="alert-octagon" class="al-error-icon" aria-hidden="true"></i>
                    <span>{{ $dealsNoInvoice['error'] ?? 'Fehler beim Laden.' }}</span>
                </div>
            @elseif (empty($dealsNoInvoice['items']) && !$errored)
                <x-arbeitsliste.empty-state
                    title="Keine Aufträge ohne Rechnung"
                    text="Zu jedem laufenden Auftrag existiert bereits eine Rechnung." />
            @else
                <div class="al-list" role="list">
                    @foreach ($dealsNoInvoice['items'] as $row)
                        <x-arbeitsliste.row
                            role="listitem"
                            :title="$row['title']"
                            :meta="$row['meta']"
                            :amount="$row['amount']">
                            <x-slot:pill>
                                <x-arbeitsliste.pill variant="info" icon="receipt-text">Rechnung fehlt</x-arbeitsliste.pill>
                            </x-slot:pill>
                            <x-slot:action>
                                <x-arbeitsliste.button variant="primary" :href="$row['href']" icon="plus">
                                    Rechnung erstellen
                                </x-arbeitsliste.button>
                            </x-slot:action>
                        </x-arbeitsliste.row>
                    @endforeach
                </div>
            @endif
        </section>
        @endunless
    </div>
@endsection

@section('script')
    <script>
        // Lucide-Icons dieser Seite rendern (Bestand nutzt dieselbe lib global).
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>
@endsection
