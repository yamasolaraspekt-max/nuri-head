@if(!empty($monthly) && $annual_total)
    <hr>
    <h6 class="text-primary">Standort: {{ $pv['inputs']['location']['latitude'] }}, {{ $pv['inputs']['location']['longitude'] }}</h6>
    <p><strong>Höhe:</strong> {{ $pv['inputs']['location']['elevation'] }} m</p>

    <div class="alert alert-info">
        <strong>Jährliche PV-Energieproduktion:</strong> {{ number_format($annual_total, 2, ',', '.') }} kWh
        @if($variation)
            <br><strong>Jährliche Schwankung:</strong> {{ number_format($variation['rel'], 2, ',', '.') }}% ({{ number_format($variation['abs'], 2, ',', '.') }} kWh)
        @endif
    </div>

    <canvas id="monthlyChart" height="140" class="mb-4"></canvas>

    <button class="btn btn-outline-primary btn-sm mb-2" type="button" data-toggle="collapse" data-target="#pvTable">Monatswerte anzeigen/verbergen</button>
    <div id="pvTable" class="collapse">
        <table class="table table-sm table-bordered text-center">
            <thead><tr><th>Monat</th><th>kWh</th><th>%</th></tr></thead>
            <tbody>
            @foreach($monthly as $i => $month)
                @php
                    $kWh = $month['E_m'];
                    $percent = $annual_total ? ($kWh / $annual_total) * 100 : 0;
                @endphp
                <tr>
                    <td>{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }} - {{ $monthNames[$i] }}</td>
                    <td>{{ number_format($kWh, 2, ',', '.') }}</td>
                    <td>{{ number_format($percent, 2, ',', '.') }}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(!empty($shs))
    <hr><h5 class="text-success">Stand-Alone Batterie Analyse</h5>
    @php
        $missingEnergy = $shs['outputs']['missing_energy'] ?? null;
        $emptyDays = $shs['outputs']['empty_days_percentage'] ?? 100;
    @endphp

    <div class="alert alert-warning">
        <strong>Tage mit leerer Batterie:</strong> {{ $emptyDays }}%
        @if($missingEnergy !== null)
            <br><strong>Fehlende Energie:</strong> {{ number_format($missingEnergy, 2, ',', '.') }} Wh/Tag
        @else
            <br><strong>Fehlende Energie:</strong> Nicht verfügbar
        @endif
    </div>

    <canvas id="shsChart" height="140" class="mb-4"></canvas>
    <button class="btn btn-outline-success btn-sm mb-2" type="button" data-toggle="collapse" data-target="#shsTable">Monatswerte anzeigen/verbergen</button>
    <div id="shsTable" class="collapse">
        <table class="table table-sm table-bordered text-center">
            <thead><tr><th>Monat</th><th>Erzeugung (Wh/Tag)</th><th>Batterieleerstand (%)</th></tr></thead>
            <tbody>
            @foreach($shs['outputs']['monthly'] ?? [] as $i => $m)
                <tr>
                    <td>{{ $monthNames[$i] }}</td>
                    <td>{{ number_format($m['E_d'], 2, ',', '.') }}</td>
                    <td>{{ number_format($m['f_e'], 2, ',', '.') }}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(!empty($mr['outputs']['monthly']))
    <hr><h5 class="text-warning">Monatliche Globalstrahlung (H(h)_m)</h5>
    <canvas id="mrChart" height="160" class="mb-3"></canvas>

    <button class="btn btn-outline-warning btn-sm mb-2" type="button" data-toggle="collapse" data-target="#mrTable">Monatswerte anzeigen/verbergen</button>
    <div id="mrTable" class="collapse">
        <table class="table table-sm table-bordered text-center">
            <thead><tr><th>Jahr</th><th>Monat</th><th>Strahlung (kWh/m²)</th></tr></thead>
            <tbody>
            @foreach($mr['outputs']['monthly'] as $row)
                <tr>
                    <td>{{ $row['year'] }}</td>
                    <td>{{ $monthNames[$row['month'] - 1] }}</td>
                    <td>{{ number_format($row['H(h)_m'], 2, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endif

@if(!empty($dr))
    <hr>
    <h5 class="text-danger">Stündliche Strahlungsdaten – März</h5>

    <canvas id="drChart" height="140" class="mb-3"></canvas>
    <button class="btn btn-outline-danger btn-sm mb-2" type="button" data-toggle="collapse" data-target="#drTable">Stundenwerte anzeigen/verbergen</button>
    <div id="drTable" class="collapse">
        <table class="table table-sm table-bordered text-center">
            <thead>
                <tr>
                    <th>Uhrzeit (UTC)</th>
                    <th>Global (G(i))</th>
                    <th>Direkt (Gb(i))</th>
                    <th>Diffus (Gd(i))</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dr as $row)
                    <tr>
                        <td>{{ $row['time'] }}</td>
                        <td>{{ number_format($row['G(i)'], 2, ',', '.') }}</td>
                        <td>{{ number_format($row['Gb(i)'], 2, ',', '.') }}</td>
                        <td>{{ number_format($row['Gd(i)'], 2, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
