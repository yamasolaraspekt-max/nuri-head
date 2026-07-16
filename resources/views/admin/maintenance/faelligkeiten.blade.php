@extends('admin.layouts.app')
@section('title', 'Fällige Wartungen')

{{--
    Fällige Wartungen — Welle A3 (2026-07-16). Lese-Fläche auf CustomerMaintenanceContract
    (gleiche Logik wie der incoming-Feed): aktive Verträge mit Fälligkeit im Fenster,
    überfällige zuerst. Wiederkehrender Umsatz aus bestehenden Verträgen, sichtbar gemacht.
--}}

@section('content')
<style>
    .wf-wrap { margin: 0 18px 40px; color: #1f2937; }
    .wf-cards { display: flex; gap: 12px; flex-wrap: wrap; margin: 4px 0 18px; }
    .wf-card { flex: 1 1 170px; min-width: 170px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .wf-card .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .wf-card .v { font-size: 19px; font-weight: 800; margin-top: 4px; color: #1f2937; }
    .wf-card .n { font-size: 11.5px; color: #6b7280; margin-top: 2px; }
    .wf-card.due .v { color: #b91c1c; } .wf-card.due { border-color: var(--sa-danger, #ef4444); }
    .wf-switch { display: flex; gap: 8px; margin: 0 0 16px; }
    .wf-chip { border: 1px solid #d1d5db; background: #fff; border-radius: 999px; padding: 5px 14px; font-size: 12px; color: #374151; text-decoration: none; }
    .wf-chip.is-active { background: var(--sa-accent, #93c21c); border-color: var(--sa-accent, #93c21c); color: #fff; font-weight: 700; }
    .wf-chip:hover { border-color: var(--sa-accent, #93c21c); }
    .wf-table { width: 100%; border-collapse: collapse; font-size: 12.5px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .wf-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 9px 12px; background: #f9fafb; }
    .wf-table td { border-bottom: 1px solid #f3f4f6; padding: 9px 12px; vertical-align: middle; }
    .wf-table tbody tr:hover { background: #f9fafb; }
    .wf-cust { max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .wf-pill { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 3px 11px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .wf-pill i { width: 7px; height: 7px; border-radius: 50%; display: inline-block; }
    .wf-pill-info    { background: var(--sa-info-bg, #f3f4f6);    color: #374151; } .wf-pill-info i    { background: var(--sa-info, #6b7280); }
    .wf-pill-warning { background: var(--sa-warning-bg, #fff7ed); color: #d97706; } .wf-pill-warning i { background: var(--sa-warning, #f59e0b); }
    .wf-pill-danger  { background: var(--sa-danger-bg, #fef2f2);  color: #b91c1c; } .wf-pill-danger i  { background: var(--sa-danger, #ef4444); }
    .wf-btn-soft { display: inline-flex; border-radius: 8px; padding: 6px 12px; font-size: 12px; font-weight: 600; border: 1px solid #d1d5db; background: #fff; color: #374151; text-decoration: none; }
    .wf-btn-soft:hover { border-color: var(--sa-accent, #93c21c); color: var(--sa-accent-hover, #7baa18); }
    .wf-empty { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 40px; text-align: center; color: #6b7280; font-size: 13.5px; }
</style>

<x-page-head title="Fällige Wartungen"
    sub="Aktive Wartungsverträge mit Fälligkeit in den nächsten {{ $days }} Tagen — überfällige zuerst. Wiederkehrender Umsatz, der nur terminiert werden muss."
    current="Fälligkeiten">
    <x-slot:actions>
        <a href="{{ url('admin/maintenance/contracts') }}" class="wf-btn-soft">Zu den Wartungsverträgen</a>
    </x-slot:actions>
</x-page-head>

<div class="wf-wrap">
    <div class="wf-switch">
        @foreach ([30, 60, 90, 180] as $window)
            <a class="wf-chip {{ $days === $window ? 'is-active' : '' }}" href="{{ route('admin.maintenance.contracts.faelligkeiten', ['days' => $window]) }}">{{ $window }} Tage</a>
        @endforeach
    </div>

    <div class="wf-cards">
        <div class="wf-card due">
            <div class="k">Überfällig</div>
            <div class="v">{{ $ueberfaellig }}</div>
            <div class="n">Termin liegt in der Vergangenheit</div>
        </div>
        <div class="wf-card">
            <div class="k">Fällig in {{ $days }} Tagen</div>
            <div class="v">{{ $contracts->count() }}</div>
            <div class="n">inkl. überfälliger Verträge</div>
        </div>
    </div>

    @if ($contracts->isEmpty())
        <div class="wf-empty">Keine Wartung fällig in den nächsten {{ $days }} Tagen.</div>
    @else
        <table class="wf-table">
            <thead>
                <tr>
                    <th>Fällig am</th>
                    <th>Stand</th>
                    <th>Vertrag</th>
                    <th>Kunde</th>
                    <th>Anlage</th>
                    <th>Intervall</th>
                    <th>Techniker</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($contracts as $c)
                    @php
                        $due = \Carbon\Carbon::parse($c->next_service_date)->startOfDay();
                        $diff = $today->diffInDays($due, false); // <0 = überfällig
                        $lead = $c->lead;
                        $kunde = trim((string) ($lead->firma ?? '')) !== '' ? $lead->firma : trim(($lead->name ?? '') . ' ' . ($lead->lastname ?? ''));
                    @endphp
                    <tr>
                        <td style="font-weight:600">{{ $due->format('d.m.Y') }}</td>
                        <td>
                            @if ($diff < 0)
                                <span class="wf-pill wf-pill-danger"><i></i> {{ abs($diff) }} {{ abs($diff) === 1 ? 'Tag' : 'Tage' }} überfällig</span>
                            @elseif ($diff <= 7)
                                <span class="wf-pill wf-pill-warning"><i></i> {{ $diff === 0 ? 'heute' : 'in ' . $diff . ' ' . ($diff === 1 ? 'Tag' : 'Tagen') }}</span>
                            @else
                                <span class="wf-pill wf-pill-info"><i></i> in {{ $diff }} Tagen</span>
                            @endif
                        </td>
                        <td>{{ $c->contract_no ?: ($c->title ?: '#' . $c->id) }}</td>
                        <td class="wf-cust" title="{{ $kunde }}">{{ $kunde !== '' ? $kunde : '— kein Kunde verknüpft —' }}</td>
                        <td class="wf-cust">{{ $c->asset->title ?? '–' }}</td>
                        <td>{{ $c->interval_months ? 'alle ' . $c->interval_months . ' Monate' : ($c->interval_type ?: '–') }}</td>
                        <td>{{ $c->responsibleEmployee->name ?? '– offen –' }}</td>
                        <td><a class="wf-btn-soft" href="{{ url('admin/maintenance/contracts') }}">Öffnen</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
