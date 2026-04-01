@extends('admin.layouts.app')
@section('title') Set Artikle @stop
@section('style')
<style>
  .kpi-scroll{
    display:flex; flex-wrap:nowrap; gap:1rem;
    overflow-x:auto; -webkit-overflow-scrolling:touch;
    padding-bottom:.25rem;
  }
  .kpi-scroll::-webkit-scrollbar{height:8px}
  .kpi-scroll::-webkit-scrollbar-thumb{background:rgba(0,0,0,.15);border-radius:999px}
  .kpi { min-width: 240px; } /* each card's lane width */
</style>

@endsection
@section('content')

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">SETS-KONFIGURATION</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}"> Dashboard  </a>  </li>
                                <li class="breadcrumb-item"><a href="{{ url('article_group_set') }}"> {{ $title->article_group }} </a> </li>

                                <li class="breadcrumb-item"><a
                                        href="{{ url('master_set/'.$title->article_group_id.'/'.$title->sub_id) }}"> {{ $title->setname }} </a>
                                </li>
                                <li class="breadcrumb-item active">  Set Details</li>

                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">  
                @php
                     $percentage = DB::table('product_master_sets')
                        ->where('id', request()->id)
                        ->select('employee_percent', 'material_percent', 'asset_percent')
                        ->first();
                    $asset_total = DB::table('asset_sets')->where('master_id', request()->id)->sum('total_price');

                    $totalAmount = $employees->sum('total');
                    $totalHours = $employees->sum('work_hour');
                    $workPricePerHour = $totalHours > 0 ? $totalAmount / $totalHours : 0;
                @endphp
               <div class="kpi-scroll">
                <div class="kpi">
                    <div class="card text-center">
                    <div class="card-content"><div class="card-body">
                        <div class="avatar bg-rgba-primary p-50 m-0 mb-1">
                        <div class="avatar-content"><i class="feather icon-package text-primary font-medium-5"></i></div>
                        </div>
                        <h2 class="text-bold-700">{{ $main_product_total }}</h2>
                        <p class="mb-0 line-ellipsis">Hauptprodukte</p>
                    </div></div>
                    </div>
                </div>

                <div class="kpi">
                    <div class="card text-center">
                    <div class="card-content"><div class="card-body">
                        <div class="avatar bg-rgba-success p-50 m-0 mb-1">
                        <div class="avatar-content"><i class="feather icon-layers text-success font-medium-5"></i></div>
                        </div>
                        <h2 class="text-bold-700">{{ $sub_product_total }}</h2>
                        <p class="mb-0 line-ellipsis">Subprodukte</p>
                    </div></div>
                    </div>
                </div>

                <div class="kpi">
                    <div class="card text-center">
                    <div class="card-content"><div class="card-body">
                        <div class="avatar bg-rgba-warning p-50 m-0 mb-1">
                        <div class="avatar-content"><i class="feather icon-users text-warning font-medium-5"></i></div>
                        </div>
                        <h2 class="text-bold-700">{{ $employee_total }}</h2>
                        <p class="mb-0 line-ellipsis">Mitarbeiter</p>
                    </div></div>
                    </div>
                </div>

                <div class="kpi">
                    <div class="card text-center">
                    <div class="card-content"><div class="card-body">
                        <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                        <div class="avatar-content"><i class="feather icon-percent text-danger font-medium-5"></i></div>
                        </div>
                        <h2 class="text-bold-700">{{ $employee_percent }}%</h2>
                        <p class="mb-0 line-ellipsis">Mitarbeiter-Kosten Anteil</p>
                    </div></div>
                    </div>
                </div>

                <div class="kpi">
                    <div class="card text-center">
                    <div class="card-content"><div class="card-body">
                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                        <div class="avatar-content"><i class="feather icon-percent text-info font-medium-5"></i></div>
                        </div>
                        <h2 class="text-bold-700">{{ $material_percent }}%</h2>
                        <p class="mb-0 line-ellipsis">Material-Kosten Anteil</p>
                    </div></div>
                    </div>
                </div>

                <div class="kpi">
                    <div class="card text-center">
                    <div class="card-content"><div class="card-body">
                        <div class="avatar bg-rgba-danger p-50 m-0 mb-1">
                        <div class="avatar-content"><i class="feather icon-percent text-danger font-medium-5"></i></div>
                        </div>
                        <h2 class="text-bold-700" id="asset-percent">{{ $asset_percent }}%</h2>
                        <p class="mb-0 line-ellipsis">Werkzeug-Kostenanteil</p>
                    </div></div>
                    </div>
                </div>

                <div class="kpi">
                    <div class="card text-center">
                    <div class="card-content"><div class="card-body">
                        <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                        <div class="avatar-content"><i class="feather icon-tool text-info font-medium-5"></i></div>
                        </div>
                        <h2 class="text-bold-700" id="asset-total">
                        {{ number_format($asset_total ?? 0, 2, ',', '.') }}€
                        </h2>
                        <p class="mb-0 line-ellipsis">Werkzeug gesamt</p>
                    </div></div>
                    </div>
                </div>
                </div>

            <div class="col-xl-12 col-lg-12">
                <div class="card overflow-hidden"  > 
                    <div class="card-content">
                        <div class="card-body"> 
                            <div class="nav-vertical">
                                <ul class="nav nav-tabs nav-left flex-column" role="tablist" style="height: 77px;">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="baseVerticalLeft-tab1" data-toggle="tab" aria-controls="tabVerticalLeft1" href="#tabVerticalLeft1" role="tab" aria-selected="false">
                                            Material</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="baseVerticalLeft-tab2" data-toggle="tab" aria-controls="tabVerticalLeft2" href="#tabVerticalLeft2" role="tab" aria-selected="false">Zubehör</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="baseVerticalLeft-tab3" data-toggle="tab" aria-controls="tabVerticalLeft3" href="#tabVerticalLeft3" role="tab" aria-selected="true">Mitarbeiter</a>
                                    </li>

                                     <li class="nav-item">
                                        <a class="nav-link" id="baseVerticalLeft-tab4" data-toggle="tab" aria-controls="tabVerticalLeft4" href="#tabVerticalLeft4" role="tab" aria-selected="true">Werkzueg</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tabVerticalLeft1" role="tabpanel" aria-labelledby="baseVerticalLeft-tab1">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Artikel Set        
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 waves-effect waves-light"
                                                        data-toggle="popover" 
                                                        data-content="Liste der Artikel, die in diesem Set vorhanden sind" 
                                                        data-original-title="Artikel Set"  
                                                        data-trigger="hover"
                                                        data-placement="top"><i class="feather icon-info"></i></button>
                                                </h4>   

                                                <a type="button" href="{{ url('add_product/'.request()->id.'/'.request()->phase) }}" 
                                                        class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light">erstellen</a>
 
                                            </div>
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover-animation mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">ID</th>
                                                                    <th scope="col">Art.Nr</th>
                                                                    <th scope="col">Art.name</th>
                                                                    <th scope="col">Menge</th>
                                                                    <th scope="col">ME</th>
                                                                    <th scope="col">Art. Gruppe</th> 
                                                                    <th scope="col">Rabatt (% , €)</th>
                                                                    <th scope="col">EK-Preis</th>
                                                                    <th scope="col">Gesamtpreis</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($data as $item)
                                                                <tr>
                                                                    <td>{{ $item->id }}</td>
                                                                    <td>{{ $item->product_id }}</td>
                                                                    <td>{{ $item->product }}</td>
                                                                    <td>{{ $item->product_count }}</td>
                                                                    <td>{{ $item->measure }}</td>
                                                                    <td>{{ $item->article_group }}</td>
                                                                    <td>
                                                                        {{ $item->discount_group }}%,
                                                                        {{ number_format($item->total_discount, 2, ',', '.') }}€
                                                                    </td>
                                                                    <td>{{ number_format($item->purchase_price, 2, ',', '.') }}€</td>
                                                                    <td>{{ number_format($item->total_value, 2, ',', '.') }}€</td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>
                                                                    <th colspan="3">Gesamt</th>
                                                                    <td><strong>{{ $data->sum('product_count') }}</strong></td>
                                                                    <td colspan="2"></td>
                                                                    <td><strong>{{ number_format($data->sum('total_discount'), 2, ',', '.') }}€</strong></td>
                                                                    <td><strong>{{ number_format($data->sum('subtotal'), 2, ',', '.') }}€</strong></td>
                                                                    <td><strong>{{ number_format($data->sum('total_value'), 2, ',', '.') }}€</strong></td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="tabVerticalLeft2" role="tabpanel" aria-labelledby="baseVerticalLeft-tab2">
                                        <div class="col-md-12 col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h4 class="card-title">Sub-produckt (Unterprodukte)
                                                        <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1   waves-effect waves-light"
                                                            data-toggle="popover" 
                                                            data-content="Liste der Produkte, die vom Masterset abhängig sind" 
                                                            data-original-title="Sub-produckt"  
                                                            data-trigger="hover"
                                                            data-placement="top"><i class="feather icon-info"></i>
                                                        </button>
                                                    </h4> 
                                                    <a type="button" href="{{ url('add_sub_product/'.request()->id.'/'.request()->phase) }}" 
                                                    class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light">erstellen</a>
                                                </div>
                                            
                                                <div class="card-content">
                                                    <div class="card-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover-animation mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th>
                                                                        <th>Art.Nr</th>
                                                                        <th>Art.name</th>
                                                                        <th>Menge</th>
                                                                        <th>ME</th>
                                                                        <th>Art. Gruppe</th> 
                                                                        <th>Rabatt (% , €)</th>
                                                                        <th>EK-Preis</th>
                                                                        <th>Gesamtpreis</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($subProducts as $item)
                                                                    <tr>
                                                                        <td>{{ $item->id }}</td>
                                                                        <td>{{ $item->product_id }}</td>
                                                                        <td>{{ $item->sub_product_name }}</td>
                                                                        <td>{{ $item->product_count }}</td>
                                                                        <td>{{ $item->measure }}</td>
                                                                        <td>{{ $item->article_group }}</td>
                                                                        <td>{{ $item->discount_group }}%, {{ number_format($item->total_discount, 2, ',', '.') }}€</td>
                                                                        <td>{{ number_format($item->purchase_price, 2, ',', '.') }}€</td>
                                                                        <td>{{ number_format($item->total_value, 2, ',', '.') }}€</td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th colspan="3">Gesamt</th>
                                                                        <td><strong>{{ $subProducts->sum('product_count') }}</strong></td>
                                                                        <td colspan="2"></td>
                                                                        <td><strong>{{ number_format($subProducts->sum('total_discount'), 2, ',', '.') }}€</strong></td>
                                                                        <td><strong>{{ number_format($subProducts->sum('subtotal'), 2, ',', '.') }}€</strong></td>
                                                                        <td><strong>{{ number_format($subProducts->sum('total_value'), 2, ',', '.') }}€</strong></td>
                                                                    </tr>
                                                                </tfoot>
                                                            </table> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane " id="tabVerticalLeft3" role="tabpanel" aria-labelledby="baseVerticalLeft-tab3">
                                        <div class="card">
                                            <div class="card-header">
                                                <h4 class="card-title">Mitarbeiterposition
                                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success mr-1 mb-1 waves-effect waves-light"
                                                        data-toggle="popover" 
                                                        data-content="Mitarbeiter, die für dieses Set benötigt werden" 
                                                        data-original-title="Mitarbeiterposition"  
                                                        data-trigger="hover"
                                                        data-placement="top"><i class="feather icon-info"></i>
                                                    </button>
                                                </h4>
                                                <a href="{{ url('refresh_master_set/'.request()->id) }}">
                                                    <div class="badge badge-info">Anteil Materialkosten: {{ $percentage->material_percent }}
                                                        %</div>
                                                </a>
                                                <a type="button" href="{{ url('add_employee_set/'.request()->id.'/'.request()->phase) }}" 
                                                class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light">erstellen</a>

                                            </div>
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover-animation mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">ID</th>
                                                                    <th scope="col">Mitarbeiterposition</th>
                                                                    <th scope="col">Arbeitsstunde</th>
                                                                    <th scope="col">Arbeitspreis/Uhr</th>
                                                                    <th scope="col">Total</th>
                                                                </tr>
                                                            </thead>
                                                             
                                                            <tbody>
                                                                @foreach ($employees as $item)
                                                                    <tr>
                                                                        <th scope="row">{{ $item->id }}</th>
                                                                        <td>{{ $item->position }}</td>
                                                                        <td>{{ $item->work_hour }}</td>
                                                                        <td>
                                                                            @if($item->buying_price == 0 || $item->buying_price === null)
                                                                                {{ number_format($item->sale_price, 2, ',', '.') }}€
                                                                            @else
                                                                                {{ number_format($item->buying_price, 2, ',', '.') }}€
                                                                            @endif
                                                                        </td>
                                                                        <td>{{ number_format($item->total, 2, ',', '.') }}€</td>
                                                                    </tr>
                                                                @endforeach

                                                                <tr>
                                                                    <th colspan="1"></th>
                                                                    <td style="font-weight: bold;">{{ $employees->count() }} Person(en)</td>
                                                                    <td style="font-weight: bold;">{{ $totalHours }}</td>
                                                                    <td style="font-weight: bold;">{{ number_format($workPricePerHour, 2, ',', '.') }}€</td>
                                                                    <td style="font-weight: bold;">{{ number_format($totalAmount, 2, ',', '.') }}€</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                   {{-- Werkzeug tab --}}
                                    <div class="tab-pane" id="tabVerticalLeft4" role="tabpanel" aria-labelledby="baseVerticalLeft-tab4">
                                        <div class="card">
                                            <div class="card-header d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <h4 class="card-title mb-0">Werkzeug</h4>
                                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-flat-success ml-1 waves-effect waves-light"
                                                            data-toggle="popover"
                                                            data-content="Werkzeuge (Assets), die diesem Master-Set zugeordnet sind"
                                                            data-original-title="Werkzeug"
                                                            data-trigger="hover" data-placement="top">
                                                        <i class="feather icon-info"></i>
                                                    </button>
                                                </div>

                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-light-primary mr-1">
                                                        Gesamt Anzahl: <span id="as-total-count">0</span>
                                                    </span>
                                                    <span class="badge badge-light-success mr-2">
                                                        Gesamtpreis: <span id="as-total-price">0,00€</span>
                                                    </span>

                                                    <button id="btn-add-asset" type="button" class="btn btn-outline-primary square mb-0 waves-effect waves-light">
                                                        hinzufügen
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-hover-animation mb-0">
                                                            <thead>
                                                            <tr>
                                                                <th>ID</th>
                                                                <th>Art.Nr</th>
                                                                <th>Seriennr.</th>
                                                                <th>Bezeichnung</th>
                                                                <th>Modell</th>
                                                                <th>Kategorie</th>
                                                                <th>Menge</th>
                                                                <th>EK-Preis</th>
                                                                <th>Gesamtpreis</th>
                                                                <th style="width:120px">Aktionen</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody id="as-rows">
                                                            <tr><td colspan="10" class="text-center text-muted py-3">Lade…</td></tr>
                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <th colspan="6">Gesamt</th>
                                                                <th id="as-foot-count">0</th>
                                                                <th></th>
                                                                <th id="as-foot-price">0,00€</th>
                                                                <th></th>
                                                            </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Add / Edit Modal --}}
                                        <div class="modal fade" id="assetModal" tabindex="-1" role="dialog" aria-labelledby="assetModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <form id="asset-form" class="modal-content" autocomplete="off">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="assetModalLabel">Werkzeug hinzufügen</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="form-row">
                                                            <div class="form-group col-md-8">
                                                                <label for="as-search">Asset suchen</label>
                                                                <input id="as-search" type="text" class="form-control" placeholder="Name, Modell, Seriennr…">
                                                                <small class="text-muted">Nur relevante Assets (nach Artikelgruppe) werden angezeigt.</small>
                                                                <div id="as-results" class="list-group mt-2" style="max-height: 220px; overflow:auto;"></div>
                                                                <input type="hidden" id="as-asset-id">
                                                            </div>
                                                            <div class="form-group col-md-2">
                                                                <label for="as-count">Menge</label>
                                                                <input id="as-count" type="number" min="1" value="1" class="form-control">
                                                            </div>
                                                            <div class="form-group col-md-12">
                                                                <label for="as-name">Interner Name (optional)</label>
                                                                <input id="as-name" type="text" class="form-control" maxlength="255" placeholder="z.B. Bohrer-Set A">
                                                            </div>
                                                        </div>
                                                        <div class="alert alert-secondary mb-0 d-none" id="as-pick-hint"></div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-flat-secondary" data-dismiss="modal">Abbrechen</button>
                                                        <button type="submit" class="btn btn-primary">Speichern</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div> 
    </div> 
</div>  
<!-- END: Content-->
@stop

@section('script')
<script>
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif  
        @if(Session::has('delete_msg'))
        toastr.error("{{ session('delete_msg') }}");
        @endif
    }); 
</script>

    <script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>

<script>
(function(){
    const MASTER_ID = {{ (int) ($title->master_id ?? request()->id) }};
    const BASE = "{{ url('asset_sets') }}/" + MASTER_ID;
    const fmtEUR = n => (Number(n||0)).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '€'; 

    const STATIC_TOTALS = {
        main: {{ (float)($main_product_total ?? 0) }},
        sub: {{ (float)($sub_product_total ?? 0) }},
        emp: {{ (float)($employee_total ?? 0) }},
        };

    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    function loadAssets(){
        $('#as-rows').html('<tr><td colspan="10" class="text-center text-muted py-3">Lade…</td></tr>');
        $.getJSON(BASE, function(resp){
            const rows = resp.rows || [];
            if(!rows.length){
                $('#as-rows').html('<tr><td colspan="10" class="text-center text-muted py-4">Keine Werkzeuge im Set.</td></tr>');
            } else {
                const html = rows.map(r => `
                  <tr data-id="${r.id}">
                    <td>${r.id}</td>
                    <td>${r.article_no ?? ''}</td>
                    <td>${r.serial_no ?? ''}</td>
                    <td>${(r.name ?? r.item) ?? ''}</td>
                    <td>${r.model ?? ''}</td>
                    <td>${r.category ?? ''}</td>
                    <td>
                      <div class="input-group input-group-sm" style="max-width:120px">
                        <div class="input-group-prepend">
                          <button class="btn btn-light js-dec" type="button">-</button>
                        </div>
                        <input type="number" class="form-control js-qty" min="1" value="${r.count}">
                        <div class="input-group-append">
                          <button class="btn btn-light js-inc" type="button">+</button>
                        </div>
                      </div>
                    </td>
                    <td>${fmtEUR(r.purchase_price)}</td>
                    <td class="js-row-total">${fmtEUR(r.total_price)}</td>
                    <td><button class="btn btn-sm btn-outline-danger js-del">löschen</button></td>
                  </tr>
                `).join('');
                $('#as-rows').html(html);
            }
            const assetTotal = Number(resp.totals?.total_price || 0);

            $('#asset-total').text(fmtEUR(assetTotal));

            const grand = STATIC_TOTALS.main + STATIC_TOTALS.sub + STATIC_TOTALS.emp + assetTotal;
            const assetPercent = grand > 0 ? Math.round((assetTotal / grand) * 100) : 0;
            $('#asset-percent').text(assetPercent + '%');

            const totalCount = resp.totals?.count ?? 0;
            const totalPrice = resp.totals?.total_price ?? 0;
            $('#as-total-count, #as-foot-count').text(totalCount);
            $('#as-total-price, #as-foot-price').text(fmtEUR(totalPrice));
        });
    }

    // modal
    $('#btn-add-asset').on('click', () => {
        $('#asset-form')[0].reset();
        $('#as-results').empty();
        $('#as-asset-id').val('');
        $('#as-pick-hint').addClass('d-none').text('');
        $('#assetModal').modal('show');
    });

    // tiny HTML esc
    const esc = s => String(s).replace(/[&<>"'`=\/]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c]));

    // search
    let rsTimer = null;
    $('#as-search').on('input', function(){
        const term = $(this).val().trim();
        clearTimeout(rsTimer);
        rsTimer = setTimeout(() => {
            if(!term){ $('#as-results').empty(); return; }
            $.getJSON(`${BASE}/search`, { term }, function(list){
                if(!list.length){
                    $('#as-results').html('<div class="list-group-item text-muted">Keine Treffer</div>');
                    return;
                }
                $('#as-results').html(list.map(a => `
                    <button type="button" class="list-group-item list-group-item-action js-pick"
                        data-id="${a.id}" data-label="${esc(a.label)}" data-price="${a.price}">
                        ${esc(a.label)} <span class="badge badge-light ml-2">${fmtEUR(a.price)}</span>
                    </button>
                `).join(''));
            });
        }, 180);
    });

    // pick asset
    $(document).on('click', '.js-pick', function(){
        $('#as-asset-id').val($(this).data('id'));
        $('#as-pick-hint').removeClass('d-none').text('Ausgewählt: ' + $(this).data('label'));
    });

    // submit add
    $('#asset-form').on('submit', function(e){
        e.preventDefault();
        const asset_id = $('#as-asset-id').val();
        const count    = parseInt($('#as-count').val() || '1', 10);
        const name     = $('#as-name').val().trim() || null;

        if(!asset_id){ toastr.error('Bitte ein Asset auswählen.'); return; }

        const $btn = $(this).find('button[type=submit]').prop('disabled', true);
        $.post(BASE, { asset_id, count, name })
            .done(() => { $('#assetModal').modal('hide'); toastr.success('Werkzeug hinzugefügt.'); loadAssets(); })
            .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Fehler beim Speichern.'))
            .always(() => $btn.prop('disabled', false));
    });

    // qty inc/dec
    $(document).on('click', '.js-inc, .js-dec', function(){
        const $tr = $(this).closest('tr');
        const rowId = $tr.data('id');
        const $qty = $tr.find('.js-qty');
        let v = parseInt($qty.val() || '1', 10);
        v = $(this).hasClass('js-inc') ? v+1 : Math.max(1, v-1);
        $qty.val(v);
        $.ajax({ url: `${BASE}/${rowId}`, method: 'PUT', data: { count: v } })
          .done(loadAssets)
          .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Update fehlgeschlagen.'));
    });

    // manual qty edit (debounced)
    let qTimer = null;
    $(document).on('input', '.js-qty', function(){
        const $tr = $(this).closest('tr');
        const rowId = $tr.data('id');
        let v = Math.max(1, parseInt($(this).val() || '1', 10));
        clearTimeout(qTimer);
        qTimer = setTimeout(() => {
            $.ajax({ url: `${BASE}/${rowId}`, method: 'PUT', data: { count: v } })
              .done(loadAssets)
              .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Update fehlgeschlagen.'));
        }, 250);
    });

    // delete
    $(document).on('click', '.js-del', function(){
        const rowId = $(this).closest('tr').data('id');
        if(!confirm('Dieses Werkzeug aus dem Set entfernen?')) return;
        $.ajax({ url: `${BASE}/${rowId}`, method: 'DELETE' })
          .done(() => { toastr.success('Gelöscht.'); loadAssets(); })
          .fail(xhr => toastr.error(xhr.responseJSON?.message || 'Löschen fehlgeschlagen.'));
    });


    
    // init
    loadAssets();
})();
</script>


@endsection