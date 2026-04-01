{{-- resources/views/admin/product/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title') PRODUCT DETAILS @endsection

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

<style>
  .img-flag{ width:20px !important; }
  .hidden{ display:none !important; }

  /* ===== Locked Sparte ===== */
  .locked-field .locked-control{ position: relative; }

  .locked-field .locked-select{
    pointer-events: none;
    background: #f3f4f6 !important;
    color: #6b7280 !important;
    border-color: #e5e7eb !important;
    padding-right: 44px;
  }
  .locked-field .locked-control::after{
    content:"";
    position:absolute;
    inset:0;
    background: rgba(255,255,255,.55);
    border-radius: .25rem;
    pointer-events:none;
  }
  .locked-field .lock-icon{
    position:absolute;
    right:12px;
    top:50%;
    transform: translateY(-50%);
    z-index:2;
    color:#6b7280;
    pointer-events:none;
  }
  .locked-field .lock-badge{
    font-size:12px;
    padding:2px 10px;
    border-radius:999px;
    border:1px solid #e5e7eb;
    background:#f9fafb;
    color:#6b7280;
    display:inline-flex;
    align-items:center;
    gap:6px;
  }
  .locked-field .locked-overlay{
    position:absolute;
    inset:0;
    z-index:3;
    border:0;
    background: transparent;
    cursor: pointer;
  }
  .locked-field:not(.is-locked) .locked-select{ pointer-events:auto; background:initial !important; color:initial !important; }
  .locked-field:not(.is-locked) .locked-control::after{ display:none; }
  .locked-field:not(.is-locked) .lock-icon,
  .locked-field:not(.is-locked) .lock-badge,
  .locked-field:not(.is-locked) .locked-overlay{ display:none; }
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
            <h2 class="content-header-title float-left mb-0">Produkt</h2>
            <div class="breadcrumb-wrapper col-12">
              <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ url('/product') }}">{{ $product->product }}</a></li>
                <li class="breadcrumb-item active">Bearbeiten</li>
              </ol>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="content-body">
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
          </ul>
        </div>
      @endif

      <div class="col-md-6">
        <form id="productForm" class="form" enctype="multipart/form-data">
        @csrf

        <div class="card mb-1">
          <div class="card-header">
            <h4 class="card-title mb-0">Produktinformationen</h4>
          </div>

          <div class="card-body row">
            {{-- LEFT --}}
            <div class="col-md-6">
              <div class="form-group">
                <label>Herstellernummer</label>
                <input type="text" class="form-control" name="article_no" id="article_no"
                       value="{{ old('article_no', $product->article_no) }}">
                <span class="text-danger" id="article_no-error"></span>
              </div>

              <div class="form-group">
                <label>EAN</label>
                <input type="text" class="form-control" name="ean" id="ean"
                       value="{{ old('ean', $product->ean) }}">
                <span class="text-danger" id="ean-error"></span>
              </div>

              <div class="form-group">
                <label>Hersteller</label>
                <div class="d-flex align-items-center">
                  <select id="brand" name="brand_id" class="form-control mr-1" style="flex:1;">
                    <option selected disabled data-image="{{ asset('logo/logo.png') }}">Bitte Hersteller wählen</option>
                  </select>

                  <button type="button"
                          class="btn btn-outline-primary btn-icon"
                          data-toggle="modal"
                          data-target="#new_brand"
                          title="Neue Marke hinzufügen">
                    <i class="feather icon-plus"></i>
                  </button>
                </div>
                <span class="text-danger" id="brand_id-error"></span>
              </div>

              <div class="form-group">
                <label>Artikelname/Titel</label>
                <input type="text" class="form-control" name="product" id="product"
                       value="{{ old('product', $product->product) }}">
                <span class="text-danger" id="product-error"></span>
              </div>

              <div class="form-group">
                <label>Typbezeichnung / Modell</label>
                <input type="text" class="form-control" name="model" id="model"
                       value="{{ old('model', $product->model) }}">
                <span class="text-danger" id="model-error"></span>
              </div>

              <div class="form-group row">
                <div class="col-md-6">
                  <label>Artikelgruppe</label>
                  @if(!empty($article_groups) && count($article_groups))
                    <select id="article_group" name="article_group" class="form-control">
                      <option disabled selected>Bitte Artikelgruppe wählen</option>
                      @foreach ($article_groups as $art_group)
                        <option value="{{ $art_group->id }}"
                          @selected(old('article_group', $product->article_group) == $art_group->id)>
                          {{ $art_group->article_group }}
                        </option>
                      @endforeach
                    </select>
                  @else
                    <a class="btn btn-primary btn-block" href="{{ route('article_group.info') }}">Neu Artikelgruppe</a>
                  @endif
                  <span class="text-danger" id="article_group-error"></span>
                </div>

                <div class="col-md-6">
                  <label>Artikel Kategorie</label>
                  <select id="sub_article" name="sub_article" class="form-control">
                    <option selected disabled>Sub-Artikel wählen</option>
                  </select>
                  <span class="text-danger" id="sub_article-error"></span>
                </div>
              </div>

              {{-- ✅ NEW: MEASURE / PRICE UNIT / PACKAGE UNIT / COLOR (UI fields) --}}
              <div class="form-group row">
                <div class="col-md-6">
                  <label>Mengeneinheit</label>
                  <select id="measure_unit" name="measure_unit" class="form-control">
                    <option value="">Bitte Einheit wählen</option>
                    @foreach(($measures ?? []) as $m)
                      <option value="{{ $m->id }}" @selected(old('measure_unit', $product->measure_unit) == $m->id)>
                        {{ $m->measurement ?? $m->measure ?? '' }}
                      </option>
                    @endforeach
                  </select>
                  <span class="text-danger" id="measure_unit-error"></span>
                </div>

                <div class="col-md-6">
                  <label>Preiseinheit</label>
                  <input type="text" class="form-control" name="price_unit" id="price_unit"
                         value="{{ old('price_unit', $product->price_unit) }}" placeholder="z.B. €/Stk, €/m²">
                  <span class="text-danger" id="price_unit-error"></span>
                </div>
              </div>

              <div class="form-group row">
                <div class="col-md-6">
                  <label>Packungseinheit</label>
                  <input type="text" class="form-control" name="package_unit" id="package_unit"
                         value="{{ old('package_unit', $product->package_unit) }}" placeholder="z.B. 10 Stk/Karton">
                  <span class="text-danger" id="package_unit-error"></span>
                </div>

                <div class="col-md-6">
                  <label>Farbe</label>
                  <input type="text" class="form-control" name="color" id="color"
                         value="{{ old('color', $product->color) }}" placeholder="z.B. Anthrazit">
                  <span class="text-danger" id="color-error"></span>
                </div>
              </div>
              {{-- ✅ END NEW FIELDS --}}
            </div>

            {{-- RIGHT --}}
            <div class="col-md-6">
              @php $currentCategory = old('category', $product->category); @endphp
              <div class="form-group locked-field is-locked" id="categoryLock" title="Sparte ist gesperrt">
                <label class="d-flex align-items-center justify-content-between">
                  <span>Sparte</span>
                  <span class="lock-badge">
                    <i class="feather icon-lock mr-25"></i> gesperrt
                  </span>
                </label>

                <div class="locked-control">
                  <select class="form-control locked-select"
                          id="category"
                          name="category"
                          aria-disabled="true"
                          tabindex="-1"
                          onchange="toggleRoofTypeSection()">
                    <option value="Produkt"    @selected($currentCategory === 'Produkt')>Produkt</option>
                    <option value="Dachziegel" @selected($currentCategory === 'Dachziegel')>Dachziegel</option>
                    <option value="Ziegel"     @selected($currentCategory === 'Ziegel')>Ziegel</option>
                    <option value="Fenster"    @selected($currentCategory === 'Fenster')>Fenster</option>
                    <option value="Tür"        @selected($currentCategory === 'Tür')>Tür</option>
                  </select>

                  <span class="lock-icon" aria-hidden="true"><i class="feather icon-lock"></i></span>
                  <button type="button" class="locked-overlay" aria-label="Sparte entsperren"></button>
                </div>
              </div>

              <div class="form-group">
                <label>Beschreibung</label>
                <div id="editor" class="form-control" style="height: 220px; overflow-y:auto;">
                  {!! old('short_description', $product->short_description) !!}
                </div>
                <textarea name="short_description" id="editor_text" hidden>{!! old('short_description', $product->short_description) !!}</textarea>
                <span class="text-danger" id="short_description-error"></span>
              </div>

              <div class="form-group hidden" id="roof_type_section">
                <label>Dachtyp</label>
                <select class="form-control" id="roof_type" name="roof_type">
                  @php $rt = old('roof_type', $product->roof_type); @endphp
                  <option value="Satteldach"            @selected($rt==='Satteldach')>Satteldach</option>
                  <option value="Flachdach"             @selected($rt==='Flachdach')>Flachdach</option>
                  <option value="Garage"                @selected($rt==='Garage')>Garage</option>
                  <option value="Carport"               @selected($rt==='Carport')>Carport</option>
                  <option value="Pultdach"              @selected($rt==='Pultdach')>Pultdach</option>
                  <option value="Kombiniertes Pultdach" @selected($rt==='Kombiniertes Pultdach')>Kombiniertes Pultdach</option>
                  <option value="Mansarddach"           @selected($rt==='Mansarddach')>Mansarddach</option>
                  <option value="Walmdach"              @selected($rt==='Walmdach')>Walmdach</option>
                  <option value="Krüppelwalmdach"       @selected($rt==='Krüppelwalmdach')>Krüppelwalmdach</option>
                  <option value="Zeltdach"              @selected($rt==='Zeltdach')>Zeltdach</option>
                </select>
              </div>

              <div class="d-flex justify-content-end mt-2">
                <button type="submit" class="btn btn-primary" id="saveProductBtn">
                  <i class="fa fa-save"></i> Speichern und Weiter
                </button>
              </div>
            </div>
          </div>
        </div>

      </form>
      </div>

      @include('admin.product.product.stages.brand_modal')
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
  const selectedBrandId = "{{ $product->brand_id ?? '' }}";
  const selectedSubArticle = "{{ $product->sub_article ?? '' }}";
</script>

<script>
  function toggleRoofTypeSection() {
    var category = document.getElementById("category")?.value;
    var roofTypeSection = document.getElementById("roof_type_section");
    if (!roofTypeSection) return;

    if (category === "Dachziegel") roofTypeSection.classList.remove("hidden");
    else roofTypeSection.classList.add("hidden");
  }
</script>

<script>
  // ===== Sparte lock -> SweetAlert confirm -> unlock =====
  document.addEventListener('click', async function (e) {
    const overlay = e.target.closest('#categoryLock .locked-overlay');
    if (!overlay) return;

    const wrap = document.getElementById('categoryLock');
    const select = document.getElementById('category');
    if (!wrap || !select || !wrap.classList.contains('is-locked')) return;

    let ok = false;
    if (window.Swal && typeof Swal.fire === 'function') {
      const res = await Swal.fire({
        icon: 'question',
        title: 'Sparte ändern?',
        text: 'Wenn du bestätigst, wird das Feld entsperrt und du kannst die Sparte ändern.',
        showCancelButton: true,
        confirmButtonText: 'Ja, entsperren',
        cancelButtonText: 'Abbrechen'
      });
      ok = !!res.isConfirmed;
    } else {
      ok = window.confirm('Sparte ändern? Feld entsperren?');
    }
    if (!ok) return;

    wrap.classList.remove('is-locked');
    select.removeAttribute('aria-disabled');
    select.removeAttribute('tabindex');
    select.focus();

    if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
      if (!jQuery(select).hasClass('select2-hidden-accessible')) jQuery(select).select2();
    }
  });
</script>

<script>
  // =========================
  // Quill + Save (AJAX)
  // =========================
  (function () {
    function initProductQuill() {
      const editorEl = document.getElementById('editor');
      const textarea = document.getElementById('editor_text');
      if (!editorEl || !textarea) return null;

      const toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],
        ['blockquote', 'code-block'],
        [{ 'header': 1 }, { 'header': 2 }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'script': 'sub' }, { 'script': 'super' }],
        [{ 'indent': '-1' }, { 'indent': '+1' }],
        [{ 'direction': 'rtl' }],
        [{ 'size': ['small', false, 'large', 'huge'] }],
        [{ 'header': [1,2,3,4,5,6,false] }],
        [{ 'color': [] }, { 'background': [] }],
        [{ 'font': [] }],
        [{ 'align': [] }],
        ['link', 'image', 'video', 'formula'],
        ['clean']
      ];

      const quill = new Quill('#editor', { modules: { toolbar: toolbarOptions }, theme: 'snow' });

      const sync = () => { textarea.value = quill.root.innerHTML; };
      sync();
      quill.on('text-change', sync);

      window.__productQuill = quill;
      window.__syncProductQuill = sync;
      return quill;
    }

    function initProductSave() {
      const btn = document.getElementById('saveProductBtn');
      const form = document.getElementById('productForm');
      if (!btn || !form) return;

      $(form).on('submit', function (e) { e.preventDefault(); btn.click(); });

      $(btn).on('click', function (e) {
        e.preventDefault();
        $('.text-danger').text('');

        if (typeof window.__syncProductQuill === 'function') window.__syncProductQuill();

        const formData = new FormData(form);

        $.ajax({
          url: "{{ route('product.update', $product->id) }}",
          method: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function (res) {
            toastr.success((res && res.message) ? res.message : 'Produkt erfolgreich aktualisiert!');
            const UPDATED_PRODUCT_ID = @json($product->id);
            window.location.href = "{{ url('/product') }}" + "?highlight=" + encodeURIComponent(UPDATED_PRODUCT_ID) + "&hl=updated";
          },
          error: function (xhr) {
            if (xhr.status === 422) {
              const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : {};
              Object.keys(errors).forEach(function (field) {
                const messages = errors[field] || [];
                const id = field.replace(/\./g, '_') + '-error';
                $('#' + id).text(messages[0] || '');
                messages.forEach(function (m) { toastr.error(m); });
              });
              return;
            }
            const msg = (xhr.responseJSON && (xhr.responseJSON.error || xhr.responseJSON.message))
              ? (xhr.responseJSON.error || xhr.responseJSON.message)
              : 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';
            toastr.error(msg);
          }
        });
      });
    }

    $(document).ready(function () {
      initProductQuill();
      initProductSave();
      if (typeof toggleRoofTypeSection === 'function') toggleRoofTypeSection();
    });
  })();
</script>

<script>
  // ===== Brand select (ajax load) =====
  function formatOption(option) {
    if (!option.id) return option.text;
    return $('<span><img src="' + $(option.element).data('image') + '" class="img-flag" /> ' + option.text + '</span>');
  }

  $(document).ready(function () {
    const brandSelect = $('#brand');
    brandSelect.select2({ templateResult: formatOption, templateSelection: formatOption });

    const defaultOption = `<option disabled data-image="{{ asset('logo/logo.png') }}">Bitte Hersteller wählen</option>`;
    brandSelect.empty().append(defaultOption);

    $.ajax({
      url: "{{ route('product.create.get.brand') }}",
      method: 'GET',
      success: function (response) {
        if (Array.isArray(response)) {
          response.forEach(brand => {
            if (brand.status === "Published") {
              const selected = (brand.id == selectedBrandId) ? 'selected' : '';
              const option = `<option value="${brand.id}" ${selected} data-image="{{ asset('images/brand') }}/${brand.image}">${brand.name}</option>`;
              brandSelect.append(option);
            }
          });
          if (selectedBrandId) brandSelect.val(selectedBrandId).trigger('change');
        }
      }
    });
  });
</script>

<script>
  // ===== Sub-articles load =====
  $(document).ready(function () {
    const groupId = $('#article_group').val();
    if (groupId) loadSubArticles(groupId, selectedSubArticle);

    $('#article_group').change(function () {
      loadSubArticles($(this).val(), null);
    });

    function loadSubArticles(groupId, selected) {
      if (!groupId) return;

      $.ajax({
        url: '{{ route("product.get.sub.article") }}',
        type: 'GET',
        data: { article: groupId },
        success: function (response) {
          $('#sub_article').empty().append('<option disabled selected>Sub-Artikel wählen</option>');
          $.each(response, function (_key, value) {
            let isSelected = (selected == value.id) ? 'selected' : '';
            $('#sub_article').append('<option value="' + value.id + '" ' + isSelected + '>' + value.sub_article + '</option>');
          });

          if ($.fn.select2 && !$('#sub_article').hasClass('select2-hidden-accessible')) $('#sub_article').select2();
        }
      });
    }
  });
</script>

<script>
  // ===== Select2 init =====
  $(document).ready(function () {
    if ($.fn.select2) {
      $('#article_group').select2();
      $('#sub_article').select2();
      $('#measure_unit').select2();   // ✅ NEW
      // category remains locked until unlock
    }
  });
</script>
@endsection
