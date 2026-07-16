{{--
    Seitenkopf — EINE Wahrheit für alle Hauptseiten (CI-Vereinheitlichung 2026-07-15).
    Ersetzt die je Seite kopierten Köpfe (ac-/iv-/oc-Titelbars). Werte abgeleitet aus der
    führenden Fassung (Arbeitsliste/Planner): Titel 26px/800 GROSS, Untertitel gedämpft,
    Breadcrumb „Dashboard › Seite", Aktionen rechts. Marke (Navy #1C3F94) nur als Akzent.
    Nutzung:
        <x-page-head title="Filialen" sub="Beschreibung…" current="Filialen">
            <x-slot:actions> …Buttons der Seite… </x-slot:actions>
        </x-page-head>
--}}
@props([
    'title',
    'sub' => null,
    'current' => null,
])
@once
<style>
    /* sa-ph = Solar-Aspekt-Seitenkopf. Gekapselt; überschreibt nichts Bestehendes (additiv). */
    .sa-ph { margin: 18px 18px 14px; }
    .sa-ph-titlebar { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
    .sa-ph-title {
        margin: 0; font-size: 26px; font-weight: 800; letter-spacing: -.025em;
        text-transform: uppercase; color: #111827; line-height: 1.15;
    }
    .sa-ph-sub { margin: 4px 0 0; font-size: 13.5px; color: #6b7280; max-width: 780px; }
    .sa-ph-breadcrumb { margin-top: 8px; font-size: 12.5px; color: #6b7280; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .sa-ph-breadcrumb a { color: #6b7280; text-decoration: none; }
    .sa-ph-breadcrumb a:hover { color: #1C3F94; }
    .sa-ph-breadcrumb .current { color: #111827; font-weight: 700; }
    .sa-ph-actions { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    @media (max-width: 767px) {
        .sa-ph { margin: 12px 12px 10px; }
        .sa-ph-titlebar { flex-direction: column; }
    }
</style>
@endonce
<div {{ $attributes->merge(['class' => 'sa-ph']) }}>
    <div class="sa-ph-titlebar">
        <div>
            <h1 class="sa-ph-title">{{ $title }}</h1>
            @if($sub)
                <p class="sa-ph-sub">{{ $sub }}</p>
            @endif
            <nav class="sa-ph-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                <span aria-hidden="true">›</span>
                <span class="current">{{ $current ?? $title }}</span>
            </nav>
        </div>
        @isset($actions)
            <div class="sa-ph-actions">{{ $actions }}</div>
        @endisset
    </div>
</div>
