@extends('admin.layouts.app')

@section('title', 'Wechselrichter-Auslegung')

{{--
    Wechselrichter-String-Auslegung (Strang Energie) — erste sichtbare Energie-Fläche.
    Server-gerendert: Blade + Bootstrap/Vuexy + minimal jQuery. KEIN Alpine (CLAUDE.md).
    Rechnung liegt serverseitig im InverterSizingService (portierter Kern); hier nur Auswahl + Anzeige.
--}}

@php
    $ampelMap = [
        'gruen' => ['class' => 'badge-success', 'text' => 'Grün — Auslegung plausibel', 'kurz' => 'Grün'],
        'gelb' => ['class' => 'badge-warning', 'text' => 'Gelb — mit Einschränkungen', 'kurz' => 'Gelb'],
        'rot' => ['class' => 'badge-danger', 'text' => 'Rot — nicht zulässig', 'kurz' => 'Rot'],
    ];
    $ampelFallback = ['class' => 'badge-secondary', 'text' => '—', 'kurz' => '—'];
@endphp

@section('content')
<section class="energie-wr-auslegung">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Wechselrichter-Auslegung</h2>
            <p class="text-muted mb-0">
                String-Vorauslegung nach Spannungsfenster, Strömen, DC-Überdimensionierung und Netzregeln
                (VDE-AR-N 4105). Vorauslegung auf Basis hinterlegter Datenblattwerte — Endauslegung durch
                qualifizierte Elektrofachkraft.
            </p>
        </div>
    </div>

    @if (!empty($fehler))
        <div class="alert alert-danger">{{ $fehler }}</div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- Eingabe --}}
        <div class="col-lg-5 col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Konfiguration</h4></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('energie.wr-auslegung.berechnen') }}">
                        @csrf

                        <div class="form-group">
                            <label for="module_index">PV-Modul</label>
                            <select class="form-control energie-select" id="module_index" name="module_index" required>
                                <option value="">— PV-Modul wählen —</option>
                                @foreach ($moduleOptions as $opt)
                                    <option value="{{ $opt['index'] }}" @selected(($eingabe['module_index'] ?? null) === $opt['index'])>{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="inverter_index">Wechselrichter</label>
                            <select class="form-control energie-select" id="inverter_index" name="inverter_index" required>
                                <option value="">— Wechselrichter wählen —</option>
                                @foreach ($inverterOptions as $opt)
                                    <option value="{{ $opt['index'] }}" @selected(($eingabe['inverter_index'] ?? null) === $opt['index'])>{{ $opt['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="form-group col-6">
                                <label for="module_gesamt">Module gesamt</label>
                                <input type="number" class="form-control" id="module_gesamt" name="module_gesamt"
                                    min="1" value="{{ $eingabe['module_gesamt'] ?? 20 }}" required>
                            </div>
                            <div class="form-group col-6">
                                <label for="parallel_strings">Parallel-Strings <small class="text-muted">(optional)</small></label>
                                <input type="number" class="form-control" id="parallel_strings" name="parallel_strings"
                                    min="1" value="{{ $eingabe['parallel_strings'] ?? 1 }}">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Auslegen</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Ergebnis --}}
        <div class="col-lg-7 col-12">
            @if (!empty($ergebnis))
                @php $a = $ampelMap[$ergebnis['ampel']] ?? $ampelFallback; @endphp

                {{-- Kundenfertiges Dokument / PDF: postet die aktuelle Auswahl an die Dokument-Route,
                     öffnet das eigenständige Druck-/PDF-Layout in einem neuen Tab. --}}
                <form method="POST" action="{{ route('energie.wr-auslegung.dokument') }}" target="_blank" class="mb-1">
                    @csrf
                    @foreach (['module_index', 'inverter_index', 'module_gesamt', 'parallel_strings'] as $feld)
                        <input type="hidden" name="{{ $feld }}" value="{{ $eingabe[$feld] ?? '' }}">
                    @endforeach
                    <button type="submit" class="btn btn-outline-primary btn-block">📄 Als Dokument / PDF öffnen</button>
                </form>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0">Auslegungs-Ergebnis</h4>
                        <span class="badge {{ $a['class'] }}">{{ $a['text'] }}</span>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-1">
                            <dt class="col-sm-4">PV-Modul</dt>
                            <dd class="col-sm-8">{{ $ergebnis['modul_label'] }}</dd>
                            <dt class="col-sm-4">Wechselrichter</dt>
                            <dd class="col-sm-8">{{ $ergebnis['wr_label'] }}</dd>
                            <dt class="col-sm-4">Module gesamt</dt>
                            <dd class="col-sm-8">{{ $ergebnis['module_gesamt'] }} Stück (Parallel-Strings: {{ $ergebnis['parallel_strings'] }})</dd>
                            <dt class="col-sm-4">DC-Leistung</dt>
                            <dd class="col-sm-8">{{ number_format($ergebnis['p_dc_w'], 0, ',', '.') }} Wp</dd>
                            <dt class="col-sm-4">DC/AC-Verhältnis</dt>
                            <dd class="col-sm-8">{{ number_format($ergebnis['ratio'], 3, ',', '.') }}</dd>
                            <dt class="col-sm-4">Spannungsfenster</dt>
                            <dd class="col-sm-8">
                                @if ($ergebnis['gueltig'])
                                    <span class="text-success">gültig</span>
                                @else
                                    <span class="text-danger">ungültig (n_min &gt; n_max — kein gültiger String)</span>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h4 class="card-title">Prüfregeln</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Regel</th>
                                        <th>Detail</th>
                                        <th>Grenze</th>
                                        <th>Norm</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($ergebnis['regeln'] as $regel)
                                        @php $ra = $ampelMap[$regel['status']] ?? $ampelFallback; @endphp
                                        <tr>
                                            <td><span class="badge {{ $ra['class'] }}">{{ $ra['kurz'] }}</span></td>
                                            <td>{{ $regel['titel'] }}</td>
                                            <td><small>{{ $regel['wert_text'] }}</small></td>
                                            <td><small class="text-muted">{{ $regel['grenze_text'] }}</small></td>
                                            <td><small class="text-muted">{{ $regel['norm'] }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-muted">
                        Bitte PV-Modul und Wechselrichter wählen, Modulzahl eingeben und „Auslegen" klicken.
                        Das Ergebnis (Ampel + Prüfregeln) erscheint hier.
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Optionale Verschönerung der Selects via Select2 (global in ticket geladen). Reine Kosmetik,
    // das Ergebnis ist server-gerendert und funktioniert ohne JS.
    $(function () {
        if ($.fn && $.fn.select2) {
            $('.energie-select').select2({ width: '100%' });
        }
    });
</script>
@endpush
