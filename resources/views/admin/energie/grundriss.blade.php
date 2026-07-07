@extends('admin.layouts.app')

@section('title', 'Grundriss-Editor')

{{--
    Grundriss-Editor – Projektliste (Strang Energie).
    Server-gerendert: Blade + Bootstrap/Vuexy. KEIN Alpine (CLAUDE.md).
--}}

@php
    $fmt = fn ($v, $d = 2) => $v === null ? '—' : number_format((float) $v, $d, ',', '.');
@endphp

@section('content')
<section class="energie-grundriss">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="content-header-title mb-0">Grundriss-Editor</h2>
                <p class="text-muted mb-0">
                    Raum zeichnen → Wände &amp; Öffnungen setzen → Heizlast nach DIN EN 12831 vorschauen.
                </p>
            </div>
            <a href="{{ route('energie.grundriss.editor') }}" class="btn btn-primary">
                <i class="feather icon-plus"></i> Neuer Grundriss
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h4 class="card-title">Bestehende Grundriss-Projekte</h4></div>
        <div class="card-body">
            @if ($projekte->isEmpty())
                <p class="text-muted mb-0">
                    Noch keine Grundriss-Projekte. Lege mit „Neuer Grundriss" das erste an.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Geschoss</th>
                                <th class="text-end">Grundfläche (m²)</th>
                                <th class="text-end">Höhe (mm)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projekte as $p)
                                @php $raum = $p->raeume->first(); $g = $raum?->geometrie; @endphp
                                <tr>
                                    <td>{{ $p->id }}</td>
                                    <td>{{ $p->name }}</td>
                                    <td>{{ $g?->geschoss ?? '—' }}</td>
                                    <td class="text-end">{{ $fmt($raum?->grundflaeche_m2) }}</td>
                                    <td class="text-end">{{ $g?->hoehe_mm ? number_format($g->hoehe_mm, 0, ',', '.') : '—' }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('energie.grundriss.editor', $p->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="feather icon-edit-2"></i> Bearbeiten
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</section>
@endsection
