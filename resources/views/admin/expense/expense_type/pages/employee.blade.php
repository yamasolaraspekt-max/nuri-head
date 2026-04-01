<div class="col-xl-4 col-md-6 col-sm-12">
    <div class="card" style="height: 614.562px;">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title"> Mitarbeiterkosten des Solaraspekts <div class="badge badge-primary">{{
                        $data->branch }}</div>
                </h4>
                <p class="card-text">Die personen- und mitarbeiterkostenbasierten Mitarbeiter und Abteilungen </p>
            </div>
            <ul class="list-group list-group-flush">

                @foreach ($employees as $emp)
                <li class="list-group-item">
                    <span class="badge badge-pill bg-primary float-right">{{ number_format( $emp->salary, 2, ',', '.')
                        }}€</span>
                    {{ $emp->name }} {{ $emp->lastname }} @ {{ $emp->department_name }} - {{ $emp->position }}
                </li>
                @endforeach
                <li class="list-group-item">
                    <span class="badge badge-pill bg-primary float-right">{{ number_format( $employees->sum('salary'),
                        2, ',', '.') }}€</span>
                    Zwischensumme
                </li>

            </ul>
            <div class="card-body">
                <a href="#" class="card-link">Ausgaben hinzufügen </a>
            </div>
        </div>
    </div>
</div>