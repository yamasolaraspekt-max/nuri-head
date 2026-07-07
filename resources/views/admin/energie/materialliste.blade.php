@extends('admin.layouts.app')

@section('title', 'Materialliste')

{{--
    Materialliste — Read-only Referenz-Browse (Strang Energie).
    Server-gerendert: Blade + Bootstrap/Vuexy, KEIN Alpine (CLAUDE.md).
    Zeigt den Referenzkatalog aus wberechnung: Baustoffe/Dämmstoffe (λ) und
    wiederverwendbare Bauteilaufbauten (U-Wert). Reine Anzeige, keine Bearbeitung.
--}}

@php
    $fmtLambda = fn ($v) => $v === null ? '—' : number_format((float) $v, 3, ',', '.');
    $fmtU = fn ($v) => $v === null ? '—' : number_format((float) $v, 3, ',', '.');
    $typLabel = function ($typ) {
        if ($typ === null) {
            return '—';
        }
        $raw = $typ instanceof \App\Enums\KonstruktionTyp ? $typ->value : (string) $typ;

        return \Illuminate\Support\Str::of($raw)->replace('_', ' ')->title()->value();
    };
@endphp

@section('content')
<section class="energie-materialliste">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Materialliste</h2>
            <p class="text-muted mb-0">
                Referenzkatalog der U-Wert-Berechnung: Baustoffe/Dämmstoffe mit Bemessungs-Wärme­leit­fähigkeit
                λ (W/mK) und wiederverwendbare Bauteilaufbauten mit berechnetem U-Wert (W/m²K). Reine Anzeige —
                die Pflege dieser Referenzdaten läuft über den Katalog-Import, nicht über diese Seite.
            </p>
        </div>
    </div>

    {{-- ==================== MATERIALIEN ==================== --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Materialien</h4>
            <span class="badge badge-light-primary">{{ $materialien->count() }}</span>
        </div>
        <div class="card-body">
            <p class="text-muted">Baustoffe und Dämmstoffe mit Bemessungs-Wärmeleitfähigkeit λ.</p>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Kategorie</th>
                            <th class="text-right">λ (W/mK)</th>
                            <th>Quelle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($materialien as $material)
                            <tr>
                                <td>{{ $material->name }}</td>
                                <td>{{ $material->kategorie ?? '—' }}</td>
                                <td class="text-right">{{ $fmtLambda($material->lambda_w_mk) }}</td>
                                <td>{{ $material->quelle ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Keine Materialien im Referenzkatalog.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ==================== KONSTRUKTIONEN ==================== --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Konstruktionen</h4>
            <span class="badge badge-light-primary">{{ $konstruktionen->count() }}</span>
        </div>
        <div class="card-body">
            <p class="text-muted">Wiederverwendbare Bauteilaufbauten mit berechnetem U-Wert (DIN EN ISO 6946).</p>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Typ</th>
                            <th class="text-right">U-Wert (W/m²K)</th>
                            <th>Quelle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($konstruktionen as $konstruktion)
                            <tr>
                                <td>{{ $konstruktion->name }}</td>
                                <td>{{ $typLabel($konstruktion->typ) }}</td>
                                <td class="text-right">{{ $fmtU($konstruktion->u_wert_berechnet) }}</td>
                                <td>{{ $konstruktion->quelle ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Keine Konstruktionen im Referenzkatalog.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</section>
@endsection
