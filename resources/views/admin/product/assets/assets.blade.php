@extends('admin.layouts.app')
@section('title') Vermögensbestand @stop
@section('style')

<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
@endsection
@section('content')

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>

        <div class="content-body"> 
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Vermögensbestand</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <form action="{{action('App\Http\Controllers\AssetsController@index')}}">
                                            <fieldset>
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control"
                                                        placeholder="Search Form" aria-describedby="button-addon2">
                                                    <div class="input-group-append" id="button-addon2">
                                                        <button class="btn btn-primary" type="submit">Go</button>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div> 
                                    <div class="col-md-3 ">
                                        <a type="button" class="btn btn-outline-primary block btn-lg"
                                            href="{{ url('assets_create') }}">
                                            Neue hinzufügen
                                        </a>
                                    </div>

                                    <div class="col-2 ">
                                       
                                        <button button type="button"
                                            class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1"
                                            data-toggle="modal" data-target="#qrcode">
                                            <i class="fa fa-qrcode"></i>
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade text-left" id="qrcode" tabindex="-1" role="dialog"
                                            aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form method="post"
                                                        action="{{ action('App\Http\Controllers\QrCodeController@store') }}">

                                                        @csrf
                                                        <div class="modal-body">
                                                            <h5>Branch</h5>
                                                            <select
                                                                class="select2-customize-result form-control required"
                                                                name="branch" id="branch" style="width:100%">
                                                                @foreach ($branch as $br)
                                                                <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                                                @endforeach
                                                            </select>

                                                            <h5>Menge</h5>
                                                            <input type="number" class="form-control" name="quantity">
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" href=""
                                                                class="btn btn-primary">Drucken</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            
                                </div> 
                            </div> 
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th Scope="col">#</th>
                                            <th Scope="col">Serial/Artikel</th>
                                            <th scope="col">Element</th>
                                            <th scope="col">Art des Kaufs</th>
                                            <th scope="col">Zweig</th>
                                            <th scope="col">Standort</th>
                                            <th scope="col">Menge</th>
                                            <th scope="col">Bild</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Aktion</th>
                                            <th scope="col">Vorgang</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @foreach($content as $item)
                                            <tr>
                                                <th scope="row">
                                                    @if(!is_null($item->parent_id))
                                                        <div class="badge badge-warning mr-1 mb-1"><i class="feather icon-link"></i></div>
                                                    @else
                                                        <div class="badge badge-primary mr-1 mb-1"><i class="feather icon-corner-down-right"></i></div>
                                                    @endif
                                                    {{ $item->id }}
                                                </th>

                                                <th scope="row">
                                                    <div class="badge badge-success mr-0 mb-0">
                                                        <i class="feather icon-hash"></i>
                                                        <span>Serial: {{ $item->serial_no }}</span>
                                                    </div><br>
                                                    <div class="badge badge-primary mr-0 mb-0">
                                                        <i class="feather icon-hash"></i>
                                                        <span>Artikel: {{ $item->article_no }}</span>
                                                    </div>
                                                </th>

                                                <td>
                                                    {{ $item->item }}<br>
                                                    <div class="badge badge-success mr-1 mb-1">
                                                        <span>{{ $item->article_group }}</span>
                                                    </div>
                                                </td>

                                                <td>
                                                    @if($item->purchase_type === 'Ratenzahlung')
                                                        <a href="{{ url('asset_installment_show?search='.$item->item) }}">{{ $item->purchase_type }}</a>
                                                    @elseif($item->purchase_type === 'Leasing')
                                                        <button type="button" class="btn btn-icon mr-1 mb-1" data-toggle="modal" data-target="#leasing-{{ $item->id }}">
                                                            {{ $item->purchase_type }}
                                                        </button>

                                                        {{-- Leasing Modal --}}
                                                        <div class="modal fade text-left" id="leasing-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        {{ $item->item }} | {{ $item->serial_no }}
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">&times;</span>
                                                                        </button>
                                                                    </div>
                                                                    <div class="modal-body" style="text-align:center;">
                                                                        <table class="table">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>Item</th>
                                                                                <th>Leasing von</th>
                                                                                <th>Startdatum</th>
                                                                                <th>Enddatum</th>
                                                                                <th>Preis</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                            <tr>
                                                                                <td>{{ $item->item }}</td>
                                                                                <td>{{ $item->leasing_from }}</td>
                                                                                <td>{{ $item->leasing_date }}</td>
                                                                                <td>{{ $item->leasing_end_date }}</td>
                                                                                <td>{{ $item->leasing_price }}</td>
                                                                            </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                    <div class="modal-footer"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{ $item->purchase_type }}
                                                    @endif
                                                </td>

                                                <td>{{ $item->branch->branch }}</td>
                                                <td>{{ $item->location }}</td>
                                                <td>{{ $item->quantity }}</td>

                                                <td>
                                                    {{-- Image thumb + modal --}}
                                                    <button type="button" class="btn btn-icon mr-1 mb-1" data-toggle="modal" data-target="#image-{{ $item->id }}">
                                                        <div class="avatar mr-1">
                                                            <img
                                                                src="{{ $item->image ? asset('images/asset/'.$item->image) : asset('images/icons/placeholder.svg') }}"
                                                                alt="asset"
                                                                height="32" width="32">
                                                        </div>
                                                    </button>

                                                    <div class="modal fade text-left" id="image-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    {{ $item->item }} | {{ $item->serial_no }}
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body" style="text-align:center;">
                                                                    <img
                                                                        src="{{ $item->image ? asset('images/asset/'.$item->image) : asset('images/icons/placeholder.svg') }}"
                                                                        alt="asset" height="200" width="200">
                                                                    <p class="mt-1">{{ $item->description }}</p>
                                                                </div>
                                                                <div class="modal-footer"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Status: direkt aus a.status (Controller liefert a.*) --}}
                                                <td>{{ $item->status }}</td>

                                                {{-- Aktion (Dropdown) --}}
                                                <td>
                                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1">
                                                        <button type="button"
                                                                class="btn btn-outline-dark dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            <i class="feather icon-align-justify dropdown-icon"></i>
                                                        </button>

                                                        <div class="dropdown-menu">
                                                            {{-- Bearbeiten --}}
                                                            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#edit-{{ $item->id }}">
                                                                <i class="feather icon-edit"></i> Bearbeiten
                                                            </button>

                                                            {{-- Ratenzahlung Details --}}
                                                            <a class="dropdown-item"
                                                            href="{{ url('asset_installment/'.$item->id.'/asset/'.$item->branch) }}">
                                                                <i class="feather icon-circle"></i> Ratenzahlung Details
                                                            </a>

                                                            {{-- Löschen --}}
                                                            <button type="button" class="dropdown-item text-danger" data-toggle="modal" data-target="#delete-{{ $item->id }}">
                                                                <i class="feather icon-trash"></i> Löschen
                                                            </button>

                                                            <button type="button" class="dropdown-item" data-toggle="modal" data-target="#dup-{{ $item->id }}">
                                                                <i class="feather icon-copy"></i> Duplizieren
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- Vorgang (basierend auf 'Verfügbar') --}}
                                                <td>
                                                    @if($item->status === 'Published')
                                                        {{-- Unpublish --}}
                                                        <a href="{{ route('assets.inventory.publish', ['state' => 'unpublish', 'id' => $item->id]) }}"
                                                        class="btn btn-icon btn-danger rounded-circle mr-1 mb-1"
                                                        data-toggle="tooltip" title="Unveröffentlichen">
                                                            <i class="feather icon-x-circle"></i>
                                                        </a>
                                                    @else
                                                        {{-- Publish --}}
                                                        <a href="{{ route('assets.inventory.publish', ['state' => 'publish', 'id' => $item->id]) }}"
                                                        class="btn btn-icon btn-success rounded-circle mr-1 mb-1"
                                                        data-toggle="tooltip" title="Veröffentlichen">
                                                            <i class="feather icon-check-circle"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>

                                            {{-- Delete Modal --}}
                                            <div class="modal fade text-left" id="delete-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Datensatz löschen</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                            <p>Die Record-Nummer lautet: {{ $item->id }}</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            @if(Route::has('assets.destroy'))
                                                                <form method="POST" action="{{ route('assets.destroy', $item->id) }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Ja, löschen</button>
                                                                </form>
                                                            @else
                                                                <a href="{{ url('assets_destroy/'.$item->id) }}" class="btn btn-danger">Ja, löschen</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Duplicate Modal --}}
                                                    <div class="modal fade text-left" id="dup-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Asset duplizieren</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                            <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <form method="POST" action="{{ route('assets.duplicate', $item->id) }}">
                                                            @csrf
                                                            <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Artikel (neuer Name)</label>
                                                                <input type="text" name="item" class="form-control" value="{{ $item->item }} (Kopie)">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Seriennummer (optional)</label>
                                                                <input type="text" name="serial_no" class="form-control" placeholder="leer lassen, wenn unbekannt">
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Artikel-Nummer (optional)</label>
                                                                <input type="text" name="article_no" class="form-control" placeholder="leer lassen, wenn unbekannt">
                                                            </div>
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox" class="custom-control-input" id="copy_image_{{ $item->id }}" name="copy_image" value="1" checked>
                                                                <label class="custom-control-label" for="copy_image_{{ $item->id }}">Bild mitkopieren</label>
                                                            </div>
                                                            <small class="text-muted d-block mt-1">Status wird auf <strong>Verfügbar</strong> gesetzt.</small>
                                                            </div>

                                                            <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">
                                                                <i class="feather icon-copy"></i> Duplizieren
                                                            </button>
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                            </div>
                                                        </form>
                                                        </div>
                                                    </div>
                                                    </div>


                                            {{-- Edit Modal --}}
                                            <div class="modal fade text-left" id="edit-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Bearbeiten</h4>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form class="form-horizontal" novalidate method="post"
                                                            action="{{ action('App\Http\Controllers\AssetsController@update') }}"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <fieldset>
                                                                    <div class="row">
                                                                        <input type="hidden" name="id" value="{{ $item->id }}">

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Seriennummer</label>
                                                                                <input type="text" class="form-control" name="serial_no" value="{{ $item->serial_no }}">
                                                                                @error('serial_no')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Artikel-Nummer</label>
                                                                                <input type="text" class="form-control" name="article_no" value="{{ $item->article_no }}">
                                                                                @error('article_no')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Artikel</label>
                                                                                <input type="text" class="form-control" name="item" value="{{ $item->item }}" required>
                                                                                @error('item')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Kategorie</label>
                                                                                <select class="form-control select2" name="category" style="width:100%">
                                                                                    @foreach (['Office','Kitchen','Furniture','Electronics','Warehouse'] as $cat)
                                                                                        <option value="{{ $cat }}" {{ $item->category === $cat ? 'selected' : '' }}>
                                                                                            {{ $cat }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                @error('category')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Kaufpreis</label>
                                                                                <input type="number" class="form-control" name="purchase_price" value="{{ $item->purchase_price }}">
                                                                                @error('purchase_price')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Kaufdatum</label>
                                                                                <input type="date" class="form-control" name="purchase_date" value="{{ $item->purchase_date }}">
                                                                                @error('purchase_date')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Zweig</label>
                                                                                <select class="form-control select2" name="branch_id" style="width:100%">
                                                                                    @foreach ($branch as $br)
                                                                                        <option value="{{ $br->id }}" {{ (string)$item->branch_id === (string)$br->id ? 'selected' : '' }}>
                                                                                            {{ $br->branch }}
                                                                                        </option>
                                                                                    @endforeach
                                                                                </select>
                                                                                @error('branch_id')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Standort</label>
                                                                                <input type="text" class="form-control" name="location" value="{{ $item->location }}">
                                                                                @error('location')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Menge</label>
                                                                                <input type="number" class="form-control" name="quantity" value="{{ $item->quantity }}">
                                                                                @error('quantity')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label>Verfallsdatum</label>
                                                                                <input type="date" class="form-control" name="expire_date" value="{{ $item->expire_date }}">
                                                                                @error('expire_date')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Beschreibung</label>
                                                                                <textarea class="form-control" name="description">{{ $item->description }}</textarea>
                                                                                @error('description')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label>Foto (optional)</label>
                                                                                <input type="file" class="form-control" name="image">
                                                                                @error('image')<p class="text-danger">{{ $message }}</p>@enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </fieldset>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">Speichern</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach

                                    </tbody>
                                </table> 
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div> 
        {{$content->links()}}
    </div>
</div> 
<!-- END: Content-->
@stop

@section('script')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>

<script>
    function onScanSuccess(decodedText, decodedResult) {
  // handle the scanned code as you like, for example:
  console.log(`Code matched = ${decodedText}`, decodedResult);
  var qrcode = document.getElementById('serial_no');
  qrcode.value = decodedResult.decodedText;
}

function onScanFailure(error) {
  // handle scan failure, usually better to ignore and keep scanning.
  // for example:
  //console.warn(`Code scan error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
  "reader",
  { fps: 10, qrbox: {width: 250, height: 250} },
  /* verbose= */ false);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<script>
    $(document).ready(function(){
    $("#hide_camera").click(function(){
        $("#camera").toggle(); // Use jQuery for both selection and toggling
    });
});

</script>




<script>
    $(document).ready(function() {
        $('#branch').select2();
        $('#parent').select2();
    });

    
</script>


@endsection