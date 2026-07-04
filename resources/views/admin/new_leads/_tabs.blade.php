{{--
    Leadliste-Sichten-Tabs (NAV Phase III, Fläche 3 — Design A, 3a).
    Geteilte Tab-Leiste über den bestehenden Lead-Listen-Routen. KEINE Query-/Daten-Logik:
    jeder Tab verlinkt die vorhandene Route (Datenquelle = NewLeadsController, unverändert), aktiv = aktuelle Route.
    Eingebunden: waiting_loops + reason_list UNKONDITIONAL (listen-exklusiv);
    customer_view KONDITIONAL via @if($showLeadTabs) — nur index(Aktiv)/my_lead(Meine) setzen das Flag,
    new_lead (new.leads, 6. Sicht) bleibt tab-frei (Option a).
    Surft die zuvor UNVERLINKTE my_leads ("Meine"). Route::has()-Guard. Sidebar (3b) geparkt.
--}}
@php
    $leadTabs = [
        ['label' => 'Aktiv',         'route' => 'new.lead.view'],
        ['label' => 'Meine',         'route' => 'my.leads'],
        ['label' => 'Warteschleife', 'route' => 'waiting.loop.leads'],
        ['label' => 'Junk',          'route' => 'lead.junks'],
        ['label' => 'Gelöscht',      'route' => 'deleted.leads'],
    ];
    $currentRoute = Route::currentRouteName();
@endphp

<div class="px-3 px-md-4 pt-3">
    <ul class="nav nav-tabs" role="tablist">
        @foreach ($leadTabs as $tab)
            @if (Route::has($tab['route']))
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $currentRoute === $tab['route'] ? 'active' : '' }}"
                       href="{{ route($tab['route']) }}">{{ $tab['label'] }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
