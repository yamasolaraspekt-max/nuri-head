<div class="col-xl-4 col-md-6 col-sm-12">
    <div class="card" style="height: 614.562px;">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title"> Maschinen- und Fahrzeugdetails sowie Kosten </h4>
                <p class="card-text">Einzelheiten zu Kosten, Raten und Ausgaben für Maschinen und Autos von
                <div class="badge badge-primary">{{ $data->branch }}</div>
                </p>
            </div>
            <ul class="list-group list-group-flush">

                @foreach ($machines as $mach)
                <li class="list-group-item">
                    <span class="badge badge-pill bg-primary float-right">{{ number_format( $mach->purchase_price, 2,
                        ',', '.') }}€</span>
                    {{ $mach->name }} {{ $mach->model }} @ {{ $mach->year }} - {{ $mach->article_group }}
                </li>
                @endforeach
                <li class="list-group-item">
                    <span class="badge badge-pill bg-primary float-right">{{ number_format(
                        $machines->sum('purchase_price'), 2, ',', '.') }}€</span>
                    Zwischensumme
                </li>

            </ul>
            <div class="card-body">
                <a href="#" class="card-link">Ausgaben hinzufügen </a>
            </div>
        </div>
    </div>
</div>