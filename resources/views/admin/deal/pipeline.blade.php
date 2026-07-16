@extends('admin.layouts.app')
@section('title', 'Pipeline')

{{--
    Pipeline (Abwicklung) — Welle B2, 2026-07-16. Board über deals.project_status (die EINE
    Pipeline-Wahrheit, Planner-Vorlage). LESEND: keine Statusänderung hier, jede Karte
    verlinkt ins Deal-Profil. sa-ui-Tokens, Pills Farbe+Text, kein Schwarz/Fremdblau.
--}}

@section('content')
<style>
    .pl-wrap { margin: 0 18px 40px; color: #1f2937; }
    .pl-board { display: flex; gap: 14px; overflow-x: auto; padding: 4px 0 12px; align-items: flex-start; }
    .pl-col { flex: 0 0 260px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; display: flex; flex-direction: column; max-height: calc(100vh - 220px); }
    .pl-col-head { display: flex; align-items: center; justify-content: space-between; padding: 11px 13px; border-bottom: 1px solid #e5e7eb; position: sticky; top: 0; background: #f9fafb; border-radius: 12px 12px 0 0; }
    .pl-col-title { display: inline-flex; align-items: center; gap: 7px; font-size: 12.5px; font-weight: 700; }
    .pl-col-title i { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .pl-count { font-size: 11.5px; font-weight: 700; color: #6b7280; background: #fff; border: 1px solid #e5e7eb; border-radius: 999px; padding: 1px 9px; }
    .pl-cards { padding: 10px; display: flex; flex-direction: column; gap: 9px; overflow-y: auto; }
    .pl-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 11px; text-decoration: none; color: #1f2937; display: block; transition: border-color .1s; }
    .pl-card:hover { border-color: var(--sa-accent, #93c21c); }
    .pl-card-nr { font-size: 12.5px; font-weight: 700; }
    .pl-card-kunde { font-size: 12.5px; margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pl-card-meta { font-size: 11.5px; color: #6b7280; margin-top: 3px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pl-card-foot { display: flex; align-items: center; justify-content: space-between; margin-top: 7px; }
    .pl-card-preis { font-size: 12.5px; font-weight: 700; font-variant-numeric: tabular-nums; }
    .pl-card-alter { font-size: 11px; color: #6b7280; }
    .pl-mehr { font-size: 11.5px; color: #6b7280; text-align: center; padding: 6px; }
    .pl-empty { font-size: 12px; color: #6b7280; padding: 14px 10px; text-align: center; }

    /* semantische Töne — Farbe + Text (Pill trägt immer den Punkt UND das Label im Kopf) */
    .tone-info    i { background: #6b7280; }
    .tone-warning i { background: #f59e0b; }
    .tone-accent  i { background: var(--sa-accent, #93c21c); }
    .tone-danger  i { background: #ef4444; }
    .tone-success i { background: #10b981; }

    .pl-summary { display: flex; gap: 10px; flex-wrap: wrap; margin: 4px 0 16px; font-size: 12.5px; color: #6b7280; }
    .pl-summary b { color: #1f2937; }
    .pl-hinweis { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 9px 13px; font-size: 12px; color: #374151; margin: 0 0 16px; }
</style>

<div class="pl-wrap">
    <x-page-head title="Pipeline" sub="Abwicklungsstand aller Aufträge — Statuswechsel erfolgt in den jeweiligen Vorgängen, hier nur Überblick." current="Pipeline" />

    <div class="pl-summary">
        <span><b>{{ $gesamt }}</b> Aufträge in Abwicklung</span>
        @if ($abgebrochen > 0)
            <span>· <b>{{ $abgebrochen }}</b> abgebrochen (nicht im Board)</span>
        @endif
    </div>

    <div class="pl-board">
        @foreach ($spalten as $spalte)
            <div class="pl-col">
                <div class="pl-col-head">
                    <span class="pl-col-title tone-{{ $spalte['ton'] }}"><i></i>{{ $spalte['label'] }}</span>
                    <span class="pl-count">{{ $spalte['anzahl'] }}</span>
                </div>
                <div class="pl-cards">
                    @forelse ($spalte['karten'] as $karte)
                        <a class="pl-card" href="{{ route('deal.profile', $karte['id']) }}">
                            <div class="pl-card-nr">{{ $karte['order_number'] ?: 'Auftrag #' . $karte['id'] }}</div>
                            <div class="pl-card-kunde">{{ $karte['kunde'] ?: '—' }}</div>
                            @if ($karte['objekt'] || $karte['produkt'])
                                <div class="pl-card-meta">{{ collect([$karte['objekt'], $karte['produkt']])->filter()->implode(' · ') }}</div>
                            @endif
                            <div class="pl-card-foot">
                                <span class="pl-card-preis">{{ $karte['price'] !== null ? number_format($karte['price'], 2, ',', '.') . ' €' : '' }}</span>
                                @if ($karte['aktualisiert'])
                                    <span class="pl-card-alter" title="Zuletzt aktualisiert">akt. {{ $karte['aktualisiert']->diffForHumans(null, true) }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="pl-empty">—</div>
                    @endforelse

                    @if ($spalte['anzahl'] > count($spalte['karten']))
                        <div class="pl-mehr">+{{ $spalte['anzahl'] - count($spalte['karten']) }} weitere (Deckel {{ $capJeSpalte }}/Spalte)</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="pl-hinweis">
        Grundlage ist ausschließlich <strong>project_status</strong> (Durchführungsstand). Der
        kaufmännische Auftragsstatus, der Vertriebs-Funnel und der Aufmaß-Status sind eigene
        Wahrheiten und hier bewusst nicht vermischt. Die Zeitangabe ist der letzte Bearbeitungs­stand
        des Auftrags, nicht die Dauer im aktuellen Status.
    </div>
</div>
@endsection
