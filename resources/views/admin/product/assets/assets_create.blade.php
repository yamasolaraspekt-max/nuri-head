@extends('admin.layouts.app')

@section('title') Vermögensbestand @endsection

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">
<style>
  body { margin:0; }
  .leasing{ display:none; }
  /* Map block (optional) */
  #gmp-map { width:100%; height:260px; border-radius:12px; border:1px solid #e5e7eb; }
</style>
@endsection

@section('content')
<div class="app-content content">
  <div class="content-overlay"></div>
  <div class="header-navbar-shadow"></div>
  <div class="content-wrapper">
    <div class="content-header row">
      <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
          <div class="col-12">
            <h2 class="content-header-title float-left mb-0">Vermögensbestand</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">Kunden</a></li>
                <li class="breadcrumb-item"><a href="#">Neu</a></li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">
      <section id="basic-horizontal-layouts">
        <div class="row match-height">
          <div class="col-md-12 col-12">
            @if ($errors->any())
              <div class="alert alert-danger">
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
              </div>
            @endif

            <div class="card">
              <div class="card-content">
                <div class="card-body">
                  <form class="form-horizontal custom-file-upload" novalidate method="post"
                        action="{{ action('App\Http\Controllers\AssetsController@store') }}"
                        enctype="multipart/form-data">
                    @csrf

                    <fieldset>
                      <div class="row">
                        {{-- QR / Seriennummer --}}
                        <div class="col-md-12">
                          <fieldset>
                            <div class="input-group">
                              <input id="serial_no" name="serial_no" type="text" class="form-control"
                                     value="{{ old('serial_no') }}" placeholder="QRCODE...">
                              <div class="input-group-append">
                                <button class="btn btn-primary waves-effect waves-light"
                                        type="button" data-toggle="modal" data-target="#qrModal">
                                  <i class="fa fa-camera"></i>
                                </button>
                              </div>
                            </div>
                          </fieldset>

                          {{-- QR Modal --}}
                          <div class="modal fade text-left" id="qrModal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                              <div class="modal-content">
                                <div class="modal-header bg-dark white">
                                  <h5 class="modal-title">QR Code Scanner</h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                  <div id="reader" width="600px"></div>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-dark waves-effect waves-light" data-dismiss="modal">OK</button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>

                        {{-- Artikelnummer --}}
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Artikel-Nummer</label>
                            <input type="text" class="form-control" name="article_no" value="{{ old('article_no') }}">
                            @error('article_no')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Artikel Name / Modell --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Artikel Namen</label>
                            <input type="text" class="form-control" name="item" value="{{ old('item') }}" required>
                            @error('item')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Artikel Modell</label>
                            <input type="text" class="form-control" name="model" value="{{ old('model') }}" required>
                            @error('model')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Kategorie --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Kategorie</label>
                            <select class="form-control select2" name="category" id="category" style="width:100%" required>
                              <option value="" disabled {{ old('category') ? '' : 'selected' }}>Bitte wählen…</option>
                              @foreach (['Office'=>'Büro','Kitchen'=>'Küche','Furniture'=>'Möbel','Electronics'=>'Elektronik','Warehouse'=>'Lager','Auto'=>'Auto','Machine'=>'Maschine'] as $val=>$label)
                                <option value="{{ $val }}" {{ old('category')===$val?'selected':'' }}>{{ $label }}</option>
                              @endforeach
                            </select>
                            @error('category')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Artikelgruppe / Gewerk --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Artikelgruppe / Gewerk</label>
                            <select class="form-control select2" name="used_for" id="used_for" style="width:100%" required>
                              <option value="" disabled {{ old('used_for') ? '' : 'selected' }}>Bitte wählen…</option>
                              @foreach ($article_groups as $article)
                                <option value="{{ $article->id }}" {{ (string)old('used_for')===(string)$article->id ? 'selected':'' }}>
                                  {{ $article->article_group }}
                                </option>
                              @endforeach
                            </select>
                            @error('used_for')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Zweig / Branch --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Zweig</label>
                            <select class="form-control select2" name="branch_id" id="branch_id" style="width:100%" required>
                              <option value="" disabled {{ old('branch_id') ? '' : 'selected' }}>Bitte wählen…</option>
                              @foreach ($branch as $br)
                                <option value="{{ $br->id }}" {{ (string)old('branch_id')===(string)$br->id ? 'selected':'' }}>
                                  {{ $br->branch }}
                                </option>
                              @endforeach
                            </select>
                            @error('branch_id')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Parent --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Primärer Link / Übergeordnetes Element</label>
                            <select class="form-control select2" name="parent_id" id="parent_id" style="width:100%">
                              <option value="0" {{ old('parent_id')==='0'?'selected':'' }}>Kein Elternteil</option>
                              @foreach ($assets as $ass)
                                <option value="{{ $ass->id }}" {{ (string)old('parent_id')===(string)$ass->id ? 'selected':'' }}>
                                  {{ $ass->item }} {{ $ass->model }}
                                </option>
                              @endforeach
                            </select>
                            @error('parent_id')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Kaufart --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Art des Kaufs</label>
                            <select class="form-control" name="purchase_type" id="purchase_type" required>
                              @php $pt = old('purchase_type'); @endphp
                              <option value="" disabled {{ $pt ? '' : 'selected' }}>Bitte wählen…</option>
                              <option value="Barzahlung"  {{ $pt==='Barzahlung'  ? 'selected':'' }}>Barzahlung</option>
                              <option value="Ratenzahlung"{{ $pt==='Ratenzahlung'? 'selected':'' }}>Ratenzahlung</option>
                              <option value="Leasing"     {{ $pt==='Leasing'     ? 'selected':'' }}>Leasing</option>
                            </select>
                            @error('purchase_type')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Leasing Felder --}}
                        <div class="col-md-6 leasing">
                          <div class="form-group">
                            <label>Leasing vom</label>
                            <input type="text" class="form-control leasing-input" name="leasing_from" value="{{ old('leasing_from') }}">
                            @error('leasing_from')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                        <div class="col-md-6 leasing">
                          <div class="form-group">
                            <label>Leasing Datum</label>
                            <input type="date" class="form-control leasing-input" name="leasing_date" value="{{ old('leasing_date') }}">
                            @error('leasing_date')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                        <div class="col-md-6 leasing">
                          <div class="form-group">
                            <label>Leasing Enddatum</label>
                            <input type="date" class="form-control leasing-input" name="leasing_end_date" value="{{ old('leasing_end_date') }}">
                            @error('leasing_end_date')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                        <div class="col-md-6 leasing">
                          <div class="form-group">
                            <label>Leasing Preis</label>
                            <input type="number" step="0.01" class="form-control leasing-input" name="leasing_price" value="{{ old('leasing_price') }}">
                            @error('leasing_price')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Kauf Felder --}}
                        <div class="col-md-6 kauf">
                          <div class="form-group">
                            <label>Kaufpreis</label>
                            <input type="number" step="0.01" class="form-control kauf-input" name="purchase_price" value="{{ old('purchase_price') }}">
                            @error('purchase_price')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                        <div class="col-md-6 kauf">
                          <div class="form-group">
                            <label>Kaufdatum</label>
                            <input type="date" class="form-control kauf-input" name="purchase_date" value="{{ old('purchase_date') }}">
                            @error('purchase_date')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Standort + Map --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Standort</label>
                            <input id="location-input" type="text" class="form-control" name="location" value="{{ old('location') }}">
                            @error('location')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                       
                        {{-- Menge / Verfallsdatum --}}
                        <div class="col-md-6">
                          <div class="form-group">
                            <label>Menge</label>
                            <input type="number" class="form-control" name="quantity" value="{{ old('quantity') }}" min="1" required>
                            @error('quantity')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                        <div class="col-md-6 kauf">
                          <div class="form-group">
                            <label>Verfallsdatum</label>
                            <input type="date" class="form-control kauf-input" name="expire_date" value="{{ old('expire_date') }}">
                            @error('expire_date')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Beschreibung --}}
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Beschreibung</label>
                            <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                            @error('description')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>

                        {{-- Bild --}}
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>Foto</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            @error('image')<p class="text-danger">{{ $message }}</p>@enderror
                          </div>
                        </div>
                      </div>
                    </fieldset>

                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Speichern</button>
                    </div>
                  </form>
                </div> <!-- card-body -->
              </div>
            </div>

          </div>
        </div>
      </section>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>

<script>
  // Toastr messages (keys aligned)
  $(function(){
    @if(session('save_msg'))   toastr.success(@json(session('save_msg'))); @endif
    @if(session('update_msg')) toastr.success(@json(session('update_msg'))); @endif
    @if(session('delete_msg')) toastr.error(@json(session('delete_msg')));  @endif
  });

  // QR scanner
  function onScanSuccess(decodedText) {
    document.getElementById('serial_no').value = decodedText;
    $('#qrModal').modal('hide');
  }
  function onScanFailure(error) { /* ignore noisy errors */ }
  $(function(){
    const scanner = new Html5QrcodeScanner("reader", { fps:10, qrbox:{width:250,height:250} }, false);
    $('#qrModal').on('shown.bs.modal', () => scanner.render(onScanSuccess, onScanFailure));
    $('#qrModal').on('hidden.bs.modal', () => {
      try { scanner.clear(); } catch(e) {}
      $("#reader").empty();
    });
  });

  // Select2
  $(function(){
    $('#branch_id,#parent_id,#used_for,#category').select2({ width: '100%' });
  });

  // Purchase type toggling + required attributes
  function applyPurchaseUI(){
    const val = $('#purchase_type').val();
    const isLeasing = (val === 'Leasing');
    $('.leasing').toggle(isLeasing);
    $('.kauf').toggle(!isLeasing);

    // flip requireds
    $('.leasing-input').prop('required', isLeasing);
    $('.kauf-input').prop('required', !isLeasing);
  }
  $(function(){
    $('#purchase_type').on('change', applyPurchaseUI);
    applyPurchaseUI(); // initial
  });

  // Google Maps + Places (Autocomplete)
  window.initMap = function(){
    const locInput = document.getElementById('location-input');
    if (!locInput) return;

    const map = new google.maps.Map(document.getElementById('gmp-map'), {
      center: { lat: 52.5200, lng: 13.4050 }, // Berlin default
      zoom: 14,
      mapTypeControl: false
    });

    const marker = new google.maps.marker.AdvancedMarkerElement({ map });

    const ac = new google.maps.places.Autocomplete(locInput, {
      fields: ['address_components','geometry','name'],
      types: ['address'],
    });

    ac.addListener('place_changed', () => {
      const place = ac.getPlace();
      if (!place || !place.geometry) return;
      map.setCenter(place.geometry.location);
      marker.position = place.geometry.location;
    });
  };
</script>

{{-- Load Google Maps JS properly. Move key to env and echo from server --}}
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo &libraries=places,marker&v=weekly&callback=initMap" async defer></script>
@endsection
