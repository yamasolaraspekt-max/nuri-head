{{--
    Anfragen-Sichten-Tabs (NAV Phase III, Fläche 2 — Design A, 2a).
    Eine geteilte Tab-Leiste über den bestehenden Inquiry-Listen-Routen. KEINE Query-/Daten-Logik:
    jeder Tab verlinkt die vorhandene Route (Datenquelle = InquiryController, unverändert), aktiv = aktuelle Route.
    Eingebunden in: admin.inquiry.contact_list. Route::has()-Guard => nichts bricht, falls eine Route fehlt.
    Surft u.a. die zuvor UNVERLINKTE inquiry.view ("Alle"). Kein Website-Tab (W-Website: weggelassen, Yama).
    Sidebar bleibt in 2a UNBERÜHRT (Kollision Parallel-Strang) — Reduktion = 2b, geparkt.
--}}
@php
    $inquiryTabs = [
        ['label' => 'Meine',           'route' => 'my.inquiry.view'],
        ['label' => 'Alle',            'route' => 'inquiry.view'],
        ['label' => 'Kunden',          'route' => 'inquiry.customer'],
        ['label' => 'Veröffentlichte', 'route' => 'inquiry.published.list'],
        ['label' => 'Junk',            'route' => 'inquiry.junk.list'],
        ['label' => 'Papierkorb',      'route' => 'inquiry.deleted.list'],
    ];
    $currentRoute = Route::currentRouteName();
@endphp

<div class="px-3 px-md-4 pt-3">
    <ul class="nav nav-tabs" role="tablist">
        @foreach ($inquiryTabs as $tab)
            @if (Route::has($tab['route']))
                <li class="nav-item" role="presentation">
                    <a class="nav-link {{ $currentRoute === $tab['route'] ? 'active' : '' }}"
                       href="{{ route($tab['route']) }}">{{ $tab['label'] }}</a>
                </li>
            @endif
        @endforeach
    </ul>
</div>
