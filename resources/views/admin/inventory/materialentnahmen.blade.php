@extends('admin.layouts.app')
@section('title', 'Materialentnahmen')

{{--
    Materialentnahmen — Welle B2 (2026-07-16). Projektübergreifende Material-Übersicht über den
    Anforderungs-/Ausgabe-Fluss (planner_item_material_requests). ticket hat kein separates
    Lagerbuch — „Entnahme" = angeforderte/freigegebene Materialausgabe je Projekt. LESEND;
    Statuswechsel bleibt im Planer. sa-ui-Tokens, Pills Farbe+Text, Kontrast AA (#6b7280).
--}}

@section('content')
<style>
    .me-wrap { margin: 0 18px 40px; color: #1f2937; }
    .me-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 4px 0 16px; }
    .me-card { flex: 1 1 150px; min-width: 150px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .me-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; }
    .me-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; }
    .me-card.total { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .me-card.tone-warning .v { color: #d97706; } .me-card.tone-success .v { color: #047857; } .me-card.tone-danger .v { color: #b91c1c; }

    .me-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin: 0 0 14px; }
    .me-tab { font-size: 12.5px; font-weight: 600; padding: 6px 13px; border-radius: 999px; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .me-tab.active { border-color: var(--sa-accent, #93c21c); background: #f4fae7; color: #4d7c0f; }

    .me-hinweis { background: var(--sa-info-bg, #f3f4f6); border: 1px solid #e5e7eb; border-radius: 10px; padding: 9px 13px; font-size: 12px; color: #374151; margin: 0 0 14px; }

    .me-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .me-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding: 8px 11px; background: #f9fafb; }
    .me-table td { border-bottom: 1px solid #f3f4f6; padding: 8px 11px; vertical-align: middle; }
    .me-table tbody tr:hover { background: #f9fafb; }
    .me-table .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    .me-art { font-weight: 600; }
    .me-sub { color: #6b7280; font-size: 11.5px; }
    .me-cust { max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

    .me-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 10px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .me-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .me-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .me-pill-info i    { background: #6b7280; }
    .me-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #b45309; } .me-pill-warning i { background: #f59e0b; }
    .me-pill-success { background: var(--sa-success-bg, #ecfdf5); color: #047857; } .me-pill-success i { background: #10b981; }
    .me-pill-danger  { background: var(--sa-danger-bg, #fef2f2);  color: #b91c1c; } .me-pill-danger i  { background: #ef4444; }
    .me-prio-hoch { color: #b91c1c; font-weight: 700; }

    .me-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .me-pager { display: flex; gap: 8px; align-items: center; justify-content: flex-end; margin-top: 14px; font-size: 12.5px; color: #6b7280; }
    .me-pager a { border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 12px; color: #374151; text-decoration: none; font-weight: 600; background: #fff; }
    .me-pager a:hover { border-color: var(--sa-accent, #93c21c); color: #4d7c0f; }
</style>

@php
    $statusLabel = function ($raw) {
        $s = mb_strtolower(trim((string) ($raw ?: 'requested')));
        return match (true) {
            in_array($s, ['accepted','approved','ordered','received','done','completed','added'], true) => ['success', 'Freigegeben'],
            in_array($s, ['rejected','declined','blocked'], true) => ['danger', 'Abgelehnt'],
            in_array($s, ['requested','open','new','send','angefordert'], true) => ['info', 'Angefordert'],
            default => ['info', ucfirst($s)], // Operanden-Gate: unbekannten Wert sichtbar lassen
        };
    };
    $tab = fn ($key, $label, $count) => '<a class="me-tab' . ($filter === $key ? ' active' : '') . '" href="?' . ($key ? 'status=' . $key : '') . '">' . $label . ' (' . $count . ')</a>';
@endphp

<div class="me-wrap">
    <x-page-head title="Materialentnahmen" sub="Angefordertes und freigegebenes Material über alle Projekte — aus dem Material-Anforderungsfluss des Planers." current="Materialentnahmen" />

    <div class="me-cards">
        <div class="me-card total"><div class="k">Gesamt</div><div class="v">{{ $zaehler['gesamt'] }}</div></div>
        <div class="me-card tone-warning"><div class="k">Angefordert</div><div class="v">{{ $zaehler['offen'] }}</div></div>
        <div class="me-card tone-success"><div class="k">Freigegeben</div><div class="v">{{ $zaehler['freigegeben'] }}</div></div>
        <div class="me-card tone-danger"><div class="k">Abgelehnt</div><div class="v">{{ $zaehler['abgelehnt'] }}</div></div>
    </div>

    <div class="me-tabs">
        {!! $tab(null, 'Alle', $zaehler['gesamt']) !!}
        {!! $tab('offen', 'Angefordert', $zaehler['offen']) !!}
        {!! $tab('freigegeben', 'Freigegeben', $zaehler['freigegeben']) !!}
        {!! $tab('abgelehnt', 'Abgelehnt', $zaehler['abgelehnt']) !!}
    </div>

    <div class="me-hinweis">
        Grundlage ist der Material-Anforderungsfluss des Planers. ticket führt kein separates
        Lagerbestands-Buch — die Statuswerte spiegeln Anforderung/Freigabe je Projekt; der
        Statuswechsel selbst erfolgt im Planer, diese Fläche ist nur der Überblick.
    </div>

    @if ($zeilen->isEmpty())
        <div class="me-empty">
            Keine Materialanforderungen im gewählten Filter.<br>
            Material wird im Planer je Projekt angefordert.
        </div>
    @else
        <table class="me-table">
            <thead>
                <tr>
                    <th>Artikel</th>
                    <th class="num">Menge</th>
                    <th>Projekt / Kunde</th>
                    <th>Objekt</th>
                    <th>Angefordert von</th>
                    <th>Benötigt</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($zeilen as $z)
                    @php
                        [$ton, $label] = $statusLabel($z->status);
                        $kunde = $z->c_firma ?: trim(($z->c_name ?? '') . ' ' . ($z->c_lastname ?? ''));
                        $artikel = $z->article_name ?: ($z->pos_name ?: ($z->article_group ?: '—'));
                        $anforderer = trim(($z->e_name ?? '') . ' ' . ($z->e_lastname ?? ''));
                        $prioHoch = in_array(mb_strtolower(trim((string) $z->priority)), ['hoch', 'high', 'dringend', 'urgent'], true);
                    @endphp
                    <tr>
                        <td>
                            <div class="me-art">{{ $artikel }}</div>
                            @if ($z->article_no)<div class="me-sub">Art.-Nr. {{ $z->article_no }}</div>@endif
                        </td>
                        <td class="num">{{ rtrim(rtrim(number_format((float) $z->quantity, 3, ',', '.'), '0'), ',') }} {{ $z->unit }}</td>
                        <td class="me-cust">{{ $kunde ?: '—' }}</td>
                        <td class="me-cust">{{ $z->object_name ?: '—' }}</td>
                        <td>{{ $anforderer ?: '—' }}</td>
                        <td>
                            {{ $z->needed_at ? \Illuminate\Support\Carbon::parse($z->needed_at)->format('d.m.Y') : '—' }}
                            @if ($prioHoch)<span class="me-prio-hoch"> · dringend</span>@endif
                        </td>
                        <td><span class="me-pill me-pill-{{ $ton }}"><i></i>{{ $label }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="me-pager">
            <span>{{ $zeilen->firstItem() }}–{{ $zeilen->lastItem() }} von {{ $zeilen->total() }}</span>
            @if ($zeilen->previousPageUrl())<a href="{{ $zeilen->previousPageUrl() }}">‹ Zurück</a>@endif
            @if ($zeilen->nextPageUrl())<a href="{{ $zeilen->nextPageUrl() }}">Weiter ›</a>@endif
        </div>
    @endif
</div>
@endsection
