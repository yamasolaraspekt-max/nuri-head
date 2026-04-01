@extends('admin.layouts.app')
@section('title') PRODUKT WP @stop
@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
  /* Small QoL tweaks */
  .table thead th { white-space: nowrap; }
  .input-xs { height: 34px; padding: .25rem .5rem; font-size: .875rem; }
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
            <h2 class="content-header-title float-left mb-0">WP</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/product_details/'.$product_id)}}">{{ $product->product }}</a></li>
                <li class="breadcrumb-item">List</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">
      <div class="row" id="table-hover-animation">
        <div class="col-12">
          <div class="card">
            <div class="card-header align-items-center">
              <h4 class="card-title mb-0">WP KONFIGURATOR</h4>
            </div>

            <div class="card-body">

              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              {{-- Toolbar: search + defaults loader --}}
              <div class="row align-items-end mb-2">
                <div class="col-md-6 mb-1">
                  <label class="mb-50">Liste durchsuchen</label>
                  <div class="input-group">
                    <input id="listSearch" type="text" class="form-control" placeholder="Tippen zum Filtern (Produkt / Typ / Temp / kW)…" />
                    <div class="input-group-append">
                      <button class="btn btn-primary" type="button" id="btnClearSearch" title="Suche löschen">
                        <i class="feather icon-x"></i>
                      </button>
                    </div>
                  </div>
                  <small class="text-muted">Client-seitiges Live-Filtering ohne Reload.</small>
                </div>

                <div class="col-md-6 mb-1">
                  <label class="mb-50">Defaults laden</label>
                  <div class="d-flex gap-1">
                    <select id="variantSelect" class="form-control mr-1">
                      <option value="" disabled selected>Variante wählen…</option>
                      <option value="8er">8er</option>
                      <option value="9er">9er</option>
                      <option value="10er">10er</option>
                      <option value="ALL">Alle Varianten</option>
                    </select>
                    <button id="btnLoadDefaults" class="btn btn-outline-primary">
                      <i class="feather icon-download"></i> Laden
                    </button>
                    <button id="btnClearForm" class="btn btn-outline-secondary ml-1" title="Formular leeren">
                      <i class="feather icon-rotate-ccw"></i>
                    </button>
                  </div>
                  <small class="text-muted">Wählt eine Variante, füllt alle Temperaturpunkte (−20 bis +15&nbsp;°C). Danach nur <strong>Speichern</strong>.</small>
                </div>
              </div>

              {{-- Add / Save form --}}
              <form id="product_form" class="mb-2">
                <table class="table" id="add_d">
                  <thead>
                    <tr>
                      <th>Produkt</th>
                      <th>Typ</th>
                      <th>Außen Temp. in °C</th>
                      <th>Maximale Leistung in kW</th>
                      <th>Minimale Leistung in kW</th>
                      <th style="width: 60px;">Aktion</th>
                    </tr>
                  </thead>
                  <tbody>
                    {{-- rows inserted by JS (defaults or manual add) --}}
                  </tbody>
                </table>

                <div class="d-flex align-items-center mb-2">
                  <button id="btnAddRow" type="button" class="btn btn-outline-primary mr-1">
                    <i class="feather icon-plus"></i> Zeile hinzufügen
                  </button>

                  <a href="{{ url('/product_details/'.$product_id) }}" class="btn btn-outline-warning mr-1">
                    <i class="feather icon-chevrons-left"></i> Zurück
                  </a>

                  <button type="button" class="btn btn-success" id="submit_form">
                    <i class="feather icon-save"></i> Datensatz speichern
                  </button>
                </div>
              </form>

              <hr class="my-2">

              {{-- Existing rows table --}}
              <div class="d-flex justify-content-between align-items-center mb-1">
                <h5 class="mb-0">Gespeicherte Daten</h5>
                <button id="btnReload" class="btn btn-outline-info btn-sm">
                  <i class="feather icon-refresh-ccw"></i> Neu laden
                </button>
              </div>

              <div class="table-responsive">
                <table class="table" id="brand_table">
                  <thead>
                    <tr>
                      <th>Produkt</th>
                      <th>Typ</th>
                      <th>Außen Temp. in °C</th>
                      <th>Maximale Leistung in kW</th>
                      <th>Minimale Leistung in kW</th>
                      <th>Aktion</th>
                    </tr>
                  </thead>
                  <tbody>
                    {{-- filled by fetchProductData() --}}
                  </tbody>
                </table>
              </div>

              {{-- Edit Modal --}}
              <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <form id="edit_form">
                      <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Eintrag bearbeiten</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="product_wp_id" id="product_wp_id">
                        <div class="form-group">
                          <label for="edit_type">Typ</label>
                          <input type="text" class="form-control" id="edit_type" name="type">
                        </div>
                        <div class="form-group">
                          <label for="edit_temp_celsius">Außen Temp. in °C</label>
                          <input type="text" class="form-control decimal-input" id="edit_temp_celsius" name="temp_celsius">
                        </div>
                        <div class="form-group">
                          <label for="edit_max_kw">Maximale Leistung in kW</label>
                          <input type="text" class="form-control decimal-input" id="edit_max_kw" name="max_kw">
                        </div>
                        <div class="form-group">
                          <label for="edit_min_kw">Minimale Leistung in kW</label>
                          <input type="text" class="form-control decimal-input" id="edit_min_kw" name="min_kw">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                        <button type="submit" class="btn btn-primary">Speichern</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

            </div> {{-- /card-body --}}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('script')
<script>
  // Toasts from session
  $(document).ready(function(){
    @if(Session::has('updated_msg')) toastr.success(@json(session('updated_msg'))); @endif
    @if(Session::has('save_msg'))    toastr.success(@json(session('save_msg')));   @endif
    @if(Session::has('delete_msg'))  toastr.error(@json(session('delete_msg')));   @endif
  });
</script>

<script>
/** =========================
 *  CONFIG: defaults, helpers
 *  ========================= */
const PRODUCT_ID   = @json($product_id);
const PRODUCT_NAME = @json($product->product);

/** Richer default profile set. Adjust as needed. */
const DEFAULT_PROFILES = {
  product: "Heatpump",
  variants: [
    {
      model: "8er",
      performance: [
        { aussen_temp_c: -20, max_kw: 7.8,  min_kw: 1.8 },
        { aussen_temp_c: -15, max_kw: 8.5,  min_kw: 2.0 },
        { aussen_temp_c: -10, max_kw: 8.8,  min_kw: 2.1 },
        { aussen_temp_c:  -7, max_kw: 8.9,  min_kw: 2.2 },
        { aussen_temp_c:  -2, max_kw: 9.0,  min_kw: 2.4 },
        { aussen_temp_c:   0, max_kw: 9.2,  min_kw: 2.5 },
        { aussen_temp_c:   2, max_kw: 9.6,  min_kw: 2.7 },
        { aussen_temp_c:   7, max_kw: 10.0, min_kw: 3.0 },
        { aussen_temp_c:  10, max_kw: 10.3, min_kw: 3.2 },
        { aussen_temp_c:  15, max_kw: 10.7, min_kw: 3.5 },
      ]
    },
    {
      model: "9er",
      performance: [
        { aussen_temp_c: -20, max_kw: 8.6,  min_kw: 2.2 },
        { aussen_temp_c: -15, max_kw: 9.5,  min_kw: 2.5 },
        { aussen_temp_c: -10, max_kw: 9.9,  min_kw: 2.7 },
        { aussen_temp_c:  -7, max_kw: 10.2, min_kw: 2.8 },
        { aussen_temp_c:  -2, max_kw: 10.4, min_kw: 3.0 },
        { aussen_temp_c:   0, max_kw: 10.5, min_kw: 3.0 },
        { aussen_temp_c:   2, max_kw: 10.9, min_kw: 3.2 },
        { aussen_temp_c:   7, max_kw: 11.5, min_kw: 3.5 },
        { aussen_temp_c:  10, max_kw: 11.8, min_kw: 3.7 },
        { aussen_temp_c:  15, max_kw: 12.2, min_kw: 4.0 },
      ]
    },
    {
      model: "10er",
      performance: [
        { aussen_temp_c: -20, max_kw: 9.6,  min_kw: 2.6 },
        { aussen_temp_c: -15, max_kw: 10.5, min_kw: 3.0 },
        { aussen_temp_c: -10, max_kw: 10.9, min_kw: 3.2 },
        { aussen_temp_c:  -7, max_kw: 11.2, min_kw: 3.3 },
        { aussen_temp_c:  -2, max_kw: 11.4, min_kw: 3.4 },
        { aussen_temp_c:   0, max_kw: 11.5, min_kw: 3.5 },
        { aussen_temp_c:   2, max_kw: 11.9, min_kw: 3.7 },
        { aussen_temp_c:   7, max_kw: 12.5, min_kw: 4.0 },
        { aussen_temp_c:  10, max_kw: 12.9, min_kw: 4.2 },
        { aussen_temp_c:  15, max_kw: 13.3, min_kw: 4.5 },
      ]
    }
  ]
};

function decCommaToDot(v){ return String(v ?? '').replace(',', '.'); }
function toFixedTrim(n){ return Number(n).toString(); } // keep as-is (DB can format)

/** Generates a TR element for a data row */
function makeRow(idx, model, perf){
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <input type="hidden" name="d[${idx}][product_id]" value="${PRODUCT_ID}">
    <td><input type="text" class="form-control input-xs" value="${PRODUCT_NAME}" disabled></td>
    <td><input type="text" class="form-control input-xs" name="d[${idx}][type]" value="${model}" placeholder="Typ"></td>
    <td><input type="text" class="form-control input-xs decimal-input" name="d[${idx}][temp_celsius]" value="${toFixedTrim(perf.aussen_temp_c)}" placeholder="Außen Temp. in °C"></td>
    <td><input type="text" class="form-control input-xs decimal-input" name="d[${idx}][max_kw]" value="${toFixedTrim(perf.max_kw)}" placeholder="Max kW"></td>
    <td><input type="text" class="form-control input-xs decimal-input" name="d[${idx}][min_kw]" value="${toFixedTrim(perf.min_kw)}" placeholder="Min kW"></td>
    <td class="text-nowrap">
      <button type="button" class="btn btn-icon btn-outline-danger btn-sm" data-action="remove-row" title="Zeile entfernen">
        <i class="feather icon-trash"></i>
      </button>
    </td>
  `;
  return tr;
}

</script>

<script>
/** =========================
 *  PAGE LOGIC
 *  ========================= */
$(function(){

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  const $tbody = $('#add_d tbody');
  let rowIndex = 0;

  function clearFormRows(){
    $tbody.empty();
    rowIndex = 0;
  }

  function addEmptyRow(){
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <input type="hidden" name="d[${rowIndex}][product_id]" value="${PRODUCT_ID}">
      <td><input type="text" class="form-control input-xs" value="${PRODUCT_NAME}" disabled></td>
      <td><input type="text" class="form-control input-xs" name="d[${rowIndex}][type]" placeholder="Typ"></td>
      <td><input type="text" class="form-control input-xs decimal-input" name="d[${rowIndex}][temp_celsius]" placeholder="Außen Temp. in °C"></td>
      <td><input type="text" class="form-control input-xs decimal-input" name="d[${rowIndex}][max_kw]" placeholder="Max kW"></td>
      <td><input type="text" class="form-control input-xs decimal-input" name="d[${rowIndex}][min_kw]" placeholder="Min kW"></td>
      <td class="text-nowrap">
        <button type="button" class="btn btn-icon btn-outline-danger btn-sm" data-action="remove-row" title="Zeile entfernen">
          <i class="feather icon-trash"></i>
        </button>
      </td>
    `;
    $tbody.append(tr);
    rowIndex++;
  }

  function loadVariant(model){
    const variant = DEFAULT_PROFILES.variants.find(v => v.model === model);
    if(!variant){ toastr.error('Variante nicht gefunden.'); return; }
    variant.performance.forEach(p=>{
      const tr = makeRow(rowIndex, model, p);
      $tbody.append(tr);
      rowIndex++;
    });
  }

  function loadAllVariants(){
    DEFAULT_PROFILES.variants.forEach(v=>{
      v.performance.forEach(p=>{
        const tr = makeRow(rowIndex, v.model, p);
        $tbody.append(tr);
        rowIndex++;
      });
    });
  }

  // Initial: one empty row for quick manual entry
  addEmptyRow();

  // Toolbar handlers
  $('#btnAddRow').on('click', addEmptyRow);
  $('#btnClearForm').on('click', ()=>{ clearFormRows(); addEmptyRow(); });

  $('#btnLoadDefaults').on('click', function(){
    const val = $('#variantSelect').val();
    if(!val){ toastr.info('Bitte eine Variante wählen.'); return; }
    clearFormRows();
    if(val === 'ALL'){ loadAllVariants(); }
    else { loadVariant(val); }
    toastr.success('Defaults eingefügt. Jetzt speichern, um zu persistieren.');
  });

  // Remove row (event delegation)
  $tbody.on('click', '[data-action="remove-row"]', function(){
    $(this).closest('tr').remove();
  });

  // Decimal comma → dot conversion before submit
  function normalizeDecimalsIn($root){
    $root.find('.decimal-input').each(function(){
      $(this).val(decCommaToDot($(this).val()));
    });
  }

  // Submit (SAVE)
  $('#submit_form').on('click', function(e){
    e.preventDefault();
    normalizeDecimalsIn($('#product_form'));
    const payload = $('#product_form').serialize();

    $.post({ url: @json(route('product_wp.save')), data: payload })
      .done(function(resp){
        toastr.success('Record has been saved successfully!');
        fetchProductData();
        clearFormRows(); addEmptyRow();
      })
      .fail(function(xhr){
        toastr.error('Fehler beim Speichern.');
        console.error(xhr.responseText);
      });
  });

  // Search filter (client-side)
  const $listSearch = $('#listSearch');
  const $brandTableBody = $('#brand_table tbody');

  function filterList(){
    const q = $listSearch.val().toLowerCase().trim();
    if(!q){
      $brandTableBody.find('tr').show();
      return;
    }
    $brandTableBody.find('tr').each(function(){
      const text = $(this).text().toLowerCase();
      $(this).toggle(text.indexOf(q) !== -1);
    });
  }

  $listSearch.on('input', filterList);
  $('#btnClearSearch').on('click', function(){
    $listSearch.val('');
    filterList();
  });

  function escapeHtml(str) {
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
  // Fetch and render existing rows
  function fetchProductData(){
    $.get(@json(route('product_wp.get', $product_id)))
      .done(function(response){
        // Accept either {data: [...] } or plain array [...]
        const rows = Array.isArray(response) ? response : (response.data ?? []);
        $brandTableBody.empty();
            rows.forEach(item=>{
                const tr = `
                    <tr>
                    <td>${item.product}</td>
                    <td>${item.type}</td>
                    <td>${item.temp_celsius}</td>
                    <td>${item.max_kw}</td>
                    <td>${item.min_kw}</td>
                    <td class="text-nowrap">
                        <button type="button" class="btn btn-icon btn-outline-primary btn-sm editBtn"
                                data-id="${item.id}"
                                data-type="${item.type}"
                                data-temp="${item.temp_celsius}"
                                data-max="${item.max_kw}"
                                data-min="${item.min_kw}">
                        <i class="feather icon-edit"></i>
                        </button>
                        <button type="button" class="btn btn-icon btn-outline-danger btn-sm deleteBtn" data-id="${item.id}">
                        <i class="feather icon-trash"></i>
                        </button>
                    </td>
                    </tr>`;
                $brandTableBody.append(tr);
                });

        filterList(); // re-apply filter if active
      })
      .fail(function(xhr){
        toastr.error('Fehler beim Laden der Daten.');
        console.error(xhr.responseText);
      });
  }

  // Initial load + manual reload button
  fetchProductData();
  $('#btnReload').on('click', fetchProductData);

  // Edit modal open
  $(document).on('click', '.editBtn', function(){
    const $btn = $(this);
    $('#product_wp_id').val($btn.data('id'));
    $('#edit_type').val($btn.data('type'));
    $('#edit_temp_celsius').val($btn.data('temp'));
    $('#edit_max_kw').val($btn.data('max'));
    $('#edit_min_kw').val($btn.data('min'));
    $('#editModal').modal('show');
  });

  // Edit submit
  $('#edit_form').on('submit', function(e){
    e.preventDefault();
    normalizeDecimalsIn($('#edit_form'));
    const id = $('#product_wp_id').val();
    const payload = $(this).serialize();

    $.post(`/product_wp/${id}/update`, payload)
      .done(function(resp){
        $('#editModal').modal('hide');
        toastr.success(resp?.success ?? 'Aktualisiert.');
        fetchProductData();
      })
      .fail(function(xhr){
        toastr.error('Fehler beim Aktualisieren.');
        console.error(xhr.responseText);
      });
  });

  // Delete
  $(document).on('click', '.deleteBtn', function(){
    const id = $(this).data('id');
    Swal.fire({
      title: 'Sicher löschen?',
      text: 'Dieser Vorgang kann nicht rückgängig gemacht werden.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Ja, löschen!'
    }).then(result=>{
      if(result.isConfirmed){
        $.ajax({ url: `/product_wp/${id}/delete`, type: 'DELETE' })
          .done(function(resp){
            toastr.success(resp?.success ?? 'Gelöscht.');
            fetchProductData();
          })
          .fail(function(xhr){
            toastr.error('Fehler beim Löschen.');
            console.error(xhr.responseText);
          });
      }
    });
  });

});
</script>
@endsection
