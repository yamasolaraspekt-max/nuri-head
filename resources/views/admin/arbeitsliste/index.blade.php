@extends('admin.layouts.app')

@section('title')
    Arbeitsliste
@endsection

@section('style')
    {{-- Design-Tokens: EINE Wahrheit fuers Design (nur hier stehen Farb-Hex-Werte). --}}
    @include('admin.arbeitsliste._tokens')
@endsection

@section('content')
    <div class="al-page">
        <header class="al-page-head">
            <h1 class="al-page-title">Was braucht mich jetzt?</h1>
            <p class="al-page-sub">
                Deine offenen Posten aus Rechnungen, Angeboten und Aufträgen — der nächste Schritt zuerst.
            </p>
        </header>

        {{-- EINE Zustandsmaschine: der Controller entscheidet $viewState zentral, die View rendert
             immer genau EINEN Zustand. So kollidieren globaler und Sektions-Zustand nicht mehr. --}}
        @switch($viewState)
            @case('loading')
                {{-- Nur fuer kuenftige Async-Nutzung reserviert; SSR erreicht diesen Zweig nicht. --}}
                <div class="al-empty" role="status" aria-live="polite" aria-busy="true">
                    <p class="al-empty-title">Arbeitsliste wird geladen …</p>
                </div>
                @break

            @case('error')
                {{-- Fehler: assertiv (role="alert"), rot, sagt was passiert ist. KEINE Sektionen/Empty-States. --}}
                <div class="al-error al-error-panel" role="alert">
                    <i data-lucide="alert-octagon" class="al-error-icon" aria-hidden="true"></i>
                    <div>
                        <p class="al-error-title">Die Arbeitsliste konnte gerade nicht vollständig geladen werden.</p>
                        <p class="al-error-text">Bitte lade die Seite neu. Bleibt es bestehen, wende dich an den Support.</p>
                    </div>
                </div>
                @break

            @case('empty')
                {{-- Leer = ERFOLG: hoeflich (role="status"), positiv, handlungsleitend. Genau EIN Block. --}}
                <x-arbeitsliste.empty-state
                    role="status"
                    aria-live="polite"
                    title="Nichts offen — du bist auf dem Stand."
                    text="Kein überfälliger Beleg, keine offene Angebots-Aufgabe, kein unberechneter Auftrag. Neue Posten erscheinen hier automatisch, sobald etwas anliegt." />
                @break

            @default
                {{-- data: die drei Sektionen; eine einzeln leere Sektion zeigt nur eine leise Inline-Zeile. --}}
                @include('admin.arbeitsliste._section', [
                    'id' => 'overdue', 'icon' => 'alert-triangle', 'heading' => 'Überfällige Rechnungen',
                    'category' => $overdueInvoices, 'emptyText' => 'Keine überfälligen Rechnungen.',
                    'pillVariant' => 'danger', 'pillIcon' => 'clock', 'pillLabel' => 'überfällig',
                    'actionIcon' => 'receipt-text', 'actionLabel' => 'Rechnung öffnen',
                ])

                @include('admin.arbeitsliste._section', [
                    'id' => 'followup', 'icon' => 'file-plus', 'heading' => 'Offene Angebots-Aufgaben',
                    'category' => $followUpTasks, 'emptyText' => 'Keine offenen Angebots-Aufgaben.',
                    'pillVariant' => 'warning', 'pillIcon' => 'file-text', 'pillLabel' => 'Angebot fehlt',
                    'actionIcon' => 'arrow-right', 'actionLabel' => 'Angebot erstellen',
                ])

                @include('admin.arbeitsliste._section', [
                    'id' => 'deals', 'icon' => 'briefcase', 'heading' => 'Aufträge ohne Rechnung',
                    'category' => $dealsNoInvoice, 'emptyText' => 'Keine Aufträge ohne Rechnung.',
                    'pillVariant' => 'info', 'pillIcon' => 'receipt-text', 'pillLabel' => 'Rechnung fehlt',
                    'actionIcon' => 'plus', 'actionLabel' => 'Rechnung erstellen',
                ])
        @endswitch
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
