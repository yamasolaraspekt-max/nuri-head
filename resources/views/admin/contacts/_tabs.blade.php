{{--
    Kontakte-Typ-Tabs (NAV Phase III, Fläche 1 — Design A).
    Eine geteilte Tab-Leiste über den bestehenden Kontakt-Routen. KEINE Query-/Daten-Logik:
    jeder Tab verlinkt die vorhandene Route (Datenquelle unverändert), aktiv = aktuelle Route.
    Eingebunden in: contacts.contacts, product.brand.brand, product.distributor.distributor,
    employee.external.external. Route::has()-Guard => nichts bricht, falls eine Route fehlt.
--}}
@php
    $contactTabs = [
        ['label' => 'Alle Kontakte',        'route' => 'all.contacts'],
        ['label' => 'Hersteller / Marken',  'route' => 'brand.index'],
        ['label' => 'Lieferanten',          'route' => 'distributors.index'],
        ['label' => 'Externe Firmen',       'route' => 'external.info'],
        ['label' => 'Nachunternehmer',      'route' => 'brand.sub.contractor'],
        ['label' => 'Architekten',          'route' => 'brand.architect'],
        ['label' => 'Banken',               'route' => 'brand.bank'],
        ['label' => 'Versicherungen',       'route' => 'brand.insurance'],
    ];
    $currentRoute = Route::currentRouteName();
@endphp

<div class="px-3 px-md-4 pt-3">
    <ul class="nav nav-tabs" role="tablist">
        @foreach ($contactTabs as $tab)
            @if (Route::has($tab['route']))
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $currentRoute === $tab['route'] ? 'active' : '' }}"
                       href="{{ route($tab['route']) }}">{{ $tab['label'] }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
