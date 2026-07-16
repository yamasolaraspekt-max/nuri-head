{{-- resources/views/admin/product/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'PRODUKT BEARBEITEN')

@section('style')
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css') }}">

  <style>
    :root {
      --pe-card: #ffffff;
              --pe-text: #1f2937;
              --pe-muted: #6b7280;
              --pe-border: #e5e7eb;
              --pe-primary: var(--sa-accent);
              --pe-primary-hover: var(--sa-accent-hover);
              --pe-primary-light: var(--sa-accent-light);
              --pe-blue: #74b2d4;
              --pe-blue-light: #eff6ff;
              --pe-danger: #ef4444;
              --pe-danger-light: #fef2f2;
              --pe-success: #10b981;
              --pe-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
              --pe-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
              --pe-transition: all .2s ease-in-out;
          }

          .pe-wrap {
              font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
              color: var(--pe-text);
              margin: 20px auto;
              padding-right: 79px;
          }

          .pe-header {
              margin-bottom: 18px;
          }

          .pe-titlebar {
              display: flex;
              align-items: flex-end;
              justify-content: space-between;
              gap: 12px;
              flex-wrap: wrap;
          }

          .pe-title {
              font-size: 26px;
              font-weight: 900;
              letter-spacing: -.025em;
              color: #111827;
              text-transform: uppercase;
          }

          .pe-sub {
              font-size: 14px;
              color: var(--pe-muted);
              margin-top: 4px;
          }

          .pe-breadcrumb {
              display: flex;
              align-items: center;
              flex-wrap: wrap;
              gap: 8px;
              margin-top: 10px;
              font-size: 13px;
              color: var(--pe-muted);
          }

          .pe-breadcrumb a {
              color: var(--pe-muted);
              text-decoration: none;
              font-weight: 800;
          }

          .pe-breadcrumb a:hover {
              color: #111827;
          }

          .pe-breadcrumb span.current {
              color: #111827;
              font-weight: 900;
          }

          .pe-card {
              background: #fff;
              border: 1px solid var(--pe-border);
              border-radius: 18px;
              box-shadow: var(--pe-shadow-sm);
              overflow: hidden;
              margin-bottom: 18px;
          }

          .pe-card-header {
              padding: 16px 18px;
              border-bottom: 1px solid var(--pe-border);
              background: #fafafa;
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 12px;
              flex-wrap: wrap;
          }

          .pe-card-title {
              margin: 0;
              font-size: 16px;
              font-weight: 900;
              color: #111827;
              text-transform: uppercase;
          }

          .pe-card-sub {
              font-size: 12px;
              color: var(--pe-muted);
              margin-top: 4px;
              font-weight: 700;
          }

          .pe-card-body {
              padding: 18px;
          }

          .pe-grid {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 18px;
          }

          @media(max-width: 992px) {
              .pe-grid {
                  grid-template-columns: 1fr;
              }
          }

          .pe-form-grid {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 14px;
          }

          .pe-form-grid .full {
              grid-column: 1 / -1;
          }

          @media(max-width: 700px) {
              .pe-form-grid {
                  grid-template-columns: 1fr;
              }

              .pe-form-grid .full {
                  grid-column: auto;
              }
          }

          .pe-form-group {
              min-width: 0;
          }

          .pe-label {
              display: block;
              font-size: 12px;
              font-weight: 900;
              color: var(--pe-muted);
              text-transform: uppercase;
              letter-spacing: .05em;
              margin-bottom: 7px;
          }

          .pe-input,
          .pe-select,
          .pe-textarea {
              width: 100%;
              padding: 11px 12px;
              border-radius: 10px;
              border: 1px solid var(--pe-border);
              background: #fff;
              color: #111827;
              font-size: 14px;
              outline: none;
              transition: var(--pe-transition);
              min-height: 42px;
          }

          .pe-input:focus,
          .pe-select:focus,
          .pe-textarea:focus {
              border-color: var(--pe-primary);
              box-shadow: 0 0 0 3px var(--pe-primary-light);
          }

          .pe-error {
              display: block;
              color: var(--pe-danger);
              font-size: 12px;
              font-weight: 800;
              margin-top: 5px;
          }

          .pe-error-box {
              border: 1px solid #fecaca;
              background: var(--pe-danger-light);
              color: #991b1b;
              border-radius: 14px;
              padding: 14px 16px;
              margin-bottom: 18px;
              font-weight: 800;
          }

          .pe-error-box ul {
              margin: 0;
              padding-left: 18px;
          }

          .pe-btn,
          .pe-btn-soft,
          .pe-btn-blue,
          .pe-btn-success {
              border: none;
              padding: 10px 16px;
              border-radius: 10px;
              font-weight: 900;
              cursor: pointer;
              transition: var(--pe-transition);
              display: inline-flex;
              align-items: center;
              justify-content: center;
              gap: 8px;
              text-decoration: none;
              line-height: 1.2;
              white-space: nowrap;
          }

          .pe-btn {
              background: var(--pe-primary);
              color: #fff;
          }

          .pe-btn:hover {
              background: var(--pe-primary-hover);
              color: #fff;
              text-decoration: none;
          }

          .pe-btn-success {
              background: var(--pe-success);
              color: #fff;
          }

          .pe-btn-success:hover {
              background: #059669;
              color: #fff;
              text-decoration: none;
          }

          .pe-btn-blue {
              background: var(--pe-blue);
              color: #fff;
          }

          .pe-btn-blue:hover {
              background: #559fc7;
              color: #fff;
              text-decoration: none;
          }

          .pe-btn-soft {
              background: #fff;
              color: var(--pe-text);
              border: 1px solid var(--pe-border);
          }

          .pe-btn-soft:hover {
              background: #f9fafb;
              color: var(--pe-text);
              text-decoration: none;
          }

          .pe-brand-row {
              display: flex;
              align-items: flex-start;
              gap: 8px;
          }

          .pe-brand-row .select2-container {
              flex: 1;
          }

          .pe-icon-btn {
              width: 42px;
              height: 42px;
              border-radius: 10px;
              border: 1px solid var(--pe-border);
              background: #fff;
              color: var(--pe-blue);
              display: inline-flex;
              align-items: center;
              justify-content: center;
              cursor: pointer;
              transition: var(--pe-transition);
              flex: 0 0 auto;
          }

          .pe-icon-btn:hover {
              background: var(--pe-blue-light);
              border-color: #c0d8ea;
          }

          .img-flag {
              width: 20px !important;
              height: 20px !important;
              border-radius: 6px;
              object-fit: cover;
              margin-right: 6px;
          }

          .hidden {
              display: none !important;
          }

          .pe-editor {
              min-height: 220px;
              max-height: 320px;
              overflow-y: auto;
              background: #fff;
          }

          .pe-actions {
              display: flex;
              justify-content: flex-end;
              gap: 10px;
              flex-wrap: wrap;
              margin-top: 18px;
          }

          .locked-field .locked-control {
              position: relative;
          }

          .locked-field .locked-select {
              pointer-events: none;
              background: #f3f4f6 !important;
              color: #6b7280 !important;
              border-color: #e5e7eb !important;
              padding-right: 44px;
          }

          .locked-field .locked-control::after {
              content: "";
              position: absolute;
              inset: 0;
              background: rgba(255, 255, 255, .55);
              border-radius: 10px;
              pointer-events: none;
          }

          .locked-field .lock-icon {
              position: absolute;
              right: 12px;
              top: 50%;
              transform: translateY(-50%);
              z-index: 2;
              color: #6b7280;
              pointer-events: none;
          }

          .locked-field .lock-badge {
              font-size: 12px;
              padding: 2px 10px;
              border-radius: 999px;
              border: 1px solid #e5e7eb;
              background: #f9fafb;
              color: #6b7280;
              display: inline-flex;
              align-items: center;
              gap: 6px;
              text-transform: none;
              letter-spacing: 0;
          }

          .locked-field .locked-overlay {
              position: absolute;
              inset: 0;
              z-index: 3;
              border: 0;
              background: transparent;
              cursor: pointer;
          }

          .locked-field:not(.is-locked) .locked-select {
              pointer-events: auto;
              background: #fff !important;
              color: #111827 !important;
          }

          .locked-field:not(.is-locked) .locked-control::after,
          .locked-field:not(.is-locked) .lock-icon,
          .locked-field:not(.is-locked) .lock-badge,
          .locked-field:not(.is-locked) .locked-overlay {
              display: none;
          }

          .select2-container {
              width: 100% !important;
          }

          .select2-container--default .select2-selection--single {
              height: 42px;
              border: 1px solid var(--pe-border);
              border-radius: 10px;
              display: flex;
              align-items: center;
              background: #fff;
          }

          .select2-container--default .select2-selection--single .select2-selection__rendered {
              line-height: 40px;
              color: #111827;
              padding-left: 12px;
              padding-right: 30px;
              font-size: 14px;
              font-weight: 700;
          }

          .select2-container--default .select2-selection--single .select2-selection__arrow {
              height: 40px;
              right: 6px;
          }

          .pe-modal-backdrop {
              position: fixed;
              inset: 0;
              z-index: 9998;
              background: rgba(15, 23, 42, .56);
              display: none;
              align-items: center;
              justify-content: center;
              padding: 18px;
          }

          .pe-modal-backdrop.is-open {
              display: flex;
          }

          .pe-modal {
              width: 100%;
              max-width: 680px;
              background: #fff;
              border-radius: 20px;
              border: 1px solid var(--pe-border);
              box-shadow: var(--pe-shadow);
              overflow: hidden;
              transform: translateY(10px) scale(.98);
              opacity: 0;
              transition: var(--pe-transition);
          }

          .pe-modal-backdrop.is-open .pe-modal {
              transform: translateY(0) scale(1);
              opacity: 1;
          }

          .pe-modal-header {
              padding: 16px 18px;
              border-bottom: 1px solid var(--pe-border);
              background: #fafafa;
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 12px;
          }

          .pe-modal-title {
              margin: 0;
              font-size: 16px;
              font-weight: 900;
              color: #111827;
          }

          .pe-modal-sub {
              font-size: 12px;
              color: var(--pe-muted);
              margin-top: 4px;
              font-weight: 700;
          }

          .pe-modal-close {
              width: 38px;
              height: 38px;
              border-radius: 10px;
              border: 1px solid var(--pe-border);
              background: #fff;
              cursor: pointer;
              color: var(--pe-muted);
              display: inline-flex;
              align-items: center;
              justify-content: center;
          }

          .pe-modal-close:hover {
              background: #f9fafb;
              color: #111827;
          }

          .pe-modal-body {
              padding: 18px;
          }

          .pe-modal-footer {
              padding: 14px 18px;
              border-top: 1px solid var(--pe-border);
              background: #fafafa;
              display: flex;
              justify-content: flex-end;
              gap: 10px;
              flex-wrap: wrap;
          }

          .pe-preview {
              width: 76px;
              height: 76px;
              border-radius: 16px;
              border: 1px solid var(--pe-border);
              background: #fafafa;
              object-fit: cover;
              display: block;
          }

          .pe-upload-row {
              display: flex;
              align-items: center;
              gap: 12px;
          }

          .pe-mini-note {
              font-size: 12px;
              color: var(--pe-muted);
              font-weight: 700;
              margin-top: 6px;
          }

          @media(max-width: 768px) {
              .pe-wrap {
                  padding: 18px;
                  margin: 0;
              }

              .pe-header {
                  margin-top: 70px;
              }

              .pe-title {
                  font-size: 21px;
              }

              .pe-card-body {
                  padding: 14px;
              }
          }
      </style>
@endsection

@section('content')
  <div class="pe-wrap">
      <div class="pe-header">
          <div class="pe-titlebar">
              <div>
                  <div class="pe-title">Produkt bearbeiten</div>
                  <div class="pe-sub">
                      Produktdaten, Hersteller, Artikelgruppe und Beschreibung aktualisieren.
                  </div>

                  <div class="pe-breadcrumb">
                      <a href="{{ url('/') }}">Dashboard</a>
                      <span>›</span>
                      <a href="{{ url('product') }}">Produktliste</a>
                      <span>›</span>
                      <span class="current">Bearbeiten</span>
                  </div>
              </div>

              <a href="{{ url('product') }}" class="pe-btn-soft">
                  <i class="feather icon-arrow-left"></i>
                  Zur Produktliste
              </a>
          </div>
      </div>

      @if ($errors->any())
        <div class="pe-error-box">
            <ul>
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
      @endif

      <form id="productForm" class="form" enctype="multipart/form-data">
          @csrf

          <div class="pe-card">
              <div class="pe-card-header">
                  <div>
                      <h3 class="pe-card-title">Produktinformationen</h3>
                      <div class="pe-card-sub">Stammdaten und Klassifizierung</div>
                  </div>
              </div>

              <div class="pe-card-body">
                  <div class="pe-grid">
                      <div>
                          <div class="pe-form-grid">
                              <div class="pe-form-group">
                                  <label class="pe-label">Herstellernummer</label>
                                  <input type="text" class="pe-input" name="article_no" id="article_no"
                                         value="{{ old('article_no', $product->article_no) }}">
                                  <span class="pe-error" id="article_no-error"></span>
                              </div>

                              <div class="pe-form-group">
                                  <label class="pe-label">EAN</label>
                                  <input type="text" class="pe-input" name="ean" id="ean"
                                         value="{{ old('ean', $product->ean) }}">
                                  <span class="pe-error" id="ean-error"></span>
                              </div>

                              <div class="pe-form-group full">
                                  <label class="pe-label">Hersteller</label>

                                  <div class="pe-brand-row">
                                      <select id="brand" name="brand_id" class="pe-select">
                                          <option selected disabled data-image="{{ asset('logo/logo.png') }}">
                                              Bitte Hersteller wählen
                                          </option>
                                      </select>

                                      <button type="button" class="pe-icon-btn" id="openBrandModalBtn" title="Neuen Hersteller hinzufügen">
                                          <i class="feather icon-plus"></i>
                                      </button>
                                  </div>

                                  <span class="pe-error" id="brand_id-error"></span>
                              </div>

                              <div class="pe-form-group full">
                                  <label class="pe-label">Artikelname / Titel</label>
                                  <input type="text" class="pe-input" name="product" id="product"
                                         value="{{ old('product', $product->product) }}">
                                  <span class="pe-error" id="product-error"></span>
                              </div>

                              <div class="pe-form-group full">
                                  <label class="pe-label">Typbezeichnung / Modell</label>
                                  <input type="text" class="pe-input" name="model" id="model"
                                         value="{{ old('model', $product->model) }}">
                                  <span class="pe-error" id="model-error"></span>
                              </div>

                              <div class="pe-form-group">
                                  <label class="pe-label">Artikelgruppe</label>

                                  @if(!empty($article_groups) && count($article_groups))
                                    <select id="article_group" name="article_group" class="pe-select">
                                        <option disabled selected>Bitte Artikelgruppe wählen</option>
                                        @foreach ($article_groups as $art_group)
                                          <option value="{{ $art_group->id }}"
                                              @selected(old('article_group', $product->article_group) == $art_group->id)>
                                              {{ $art_group->article_group }}
                                          </option>
                                        @endforeach
                                    </select>
                                  @else
                                    <a class="pe-btn-blue" href="{{ route('article_group.info') }}">
                                        Neue Artikelgruppe
                                    </a>
                                  @endif

                                  <span class="pe-error" id="article_group-error"></span>
                              </div>

                              <div class="pe-form-group">
                                  <label class="pe-label">Artikel Kategorie</label>
                                  <select id="sub_article" name="sub_article" class="pe-select">
                                      <option selected disabled>Sub-Artikel wählen</option>
                                  </select>
                                  <span class="pe-error" id="sub_article-error"></span>
                              </div>

                              <div class="pe-form-group">
                                  <label class="pe-label">Mengeneinheit</label>
                                  <select id="measure_unit" name="measure_unit" class="pe-select">
                                      <option value="">Bitte Einheit wählen</option>
                                      @foreach(($measures ?? []) as $m)
                                        <option value="{{ $m->id }}"
                                            @selected(old('measure_unit', $product->measure_unit) == $m->id)>
                                            {{ $m->measurement ?? $m->measure ?? '' }}
                                        </option>
                                      @endforeach
                                  </select>
                                  <span class="pe-error" id="measure_unit-error"></span>
                              </div>

                              <div class="pe-form-group">
                                  <label class="pe-label">Preiseinheit</label>
                                  <input type="text" class="pe-input" name="price_unit" id="price_unit"
                                         value="{{ old('price_unit', $product->price_unit) }}"
                                         placeholder="z. B. €/Stk, €/m²">
                                  <span class="pe-error" id="price_unit-error"></span>
                              </div>

                              <div class="pe-form-group">
                                  <label class="pe-label">Packungseinheit</label>
                                  <input type="text" class="pe-input" name="package_unit" id="package_unit"
                                         value="{{ old('package_unit', $product->package_unit) }}"
                                         placeholder="z. B. 10 Stk/Karton">
                                  <span class="pe-error" id="package_unit-error"></span>
                              </div>

                              <div class="pe-form-group">
                                  <label class="pe-label">Farbe</label>
                                  <input type="text" class="pe-input" name="color" id="color"
                                         value="{{ old('color', $product->color) }}"
                                         placeholder="z. B. Anthrazit">
                                  <span class="pe-error" id="color-error"></span>
                              </div>
                          </div>
                      </div>

                      <div>
                          @php $currentCategory = old('category', $product->category); @endphp

                          <div class="pe-form-group locked-field is-locked" id="categoryLock" title="Sparte ist gesperrt">
                              <label class="pe-label d-flex align-items-center justify-content-between">
                                  <span>Sparte</span>
                                  <span class="lock-badge">
                                      <i class="feather icon-lock"></i>
                                      gesperrt
                                  </span>
                              </label>

                              <div class="locked-control">
                                  <select class="pe-select locked-select" id="category" name="category" aria-disabled="true" tabindex="-1"
                                          onchange="toggleRoofTypeSection()">
                                      <option value="Produkt" @selected($currentCategory === 'Produkt')>Produkt</option>
                                      <option value="Dachziegel" @selected($currentCategory === 'Dachziegel')>Dachziegel</option>
                                      <option value="Ziegel" @selected($currentCategory === 'Ziegel')>Ziegel</option>
                                      <option value="Fenster" @selected($currentCategory === 'Fenster')>Fenster</option>
                                      <option value="Tür" @selected($currentCategory === 'Tür')>Tür</option>
                                  </select>

                                  <span class="lock-icon" aria-hidden="true">
                                      <i class="feather icon-lock"></i>
                                  </span>

                                  <button type="button" class="locked-overlay" aria-label="Sparte entsperren"></button>
                              </div>
                          </div>

                          <div class="pe-form-group mt-2">
                              <label class="pe-label">Beschreibung</label>

                              <div id="editor" class="pe-input pe-editor">
                                  {!! old('short_description', $product->short_description) !!}
                              </div>

                              <textarea name="short_description" id="editor_text" hidden>{!! old('short_description', $product->short_description) !!}</textarea>
                              <span class="pe-error" id="short_description-error"></span>
                          </div>

                          <div class="pe-form-group hidden mt-2" id="roof_type_section">
                              <label class="pe-label">Dachtyp</label>

                              @php $rt = old('roof_type', $product->roof_type); @endphp

                              <select class="pe-select" id="roof_type" name="roof_type">
                                  <option value="Satteldach" @selected($rt === 'Satteldach')>Satteldach</option>
                                  <option value="Flachdach" @selected($rt === 'Flachdach')>Flachdach</option>
                                  <option value="Garage" @selected($rt === 'Garage')>Garage</option>
                                  <option value="Carport" @selected($rt === 'Carport')>Carport</option>
                                  <option value="Pultdach" @selected($rt === 'Pultdach')>Pultdach</option>
                                  <option value="Kombiniertes Pultdach" @selected($rt === 'Kombiniertes Pultdach')>Kombiniertes Pultdach</option>
                                  <option value="Mansarddach" @selected($rt === 'Mansarddach')>Mansarddach</option>
                                  <option value="Walmdach" @selected($rt === 'Walmdach')>Walmdach</option>
                                  <option value="Krüppelwalmdach" @selected($rt === 'Krüppelwalmdach')>Krüppelwalmdach</option>
                                  <option value="Zeltdach" @selected($rt === 'Zeltdach')>Zeltdach</option>
                              </select>
                          </div>

                          <div class="pe-actions">
                              <button type="submit" class="pe-btn" id="saveProductBtn">
                                  <i class="feather icon-save"></i>
                                  Speichern und Weiter
                              </button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </form>
  </div>

  {{-- CUSTOM HERSTELLER MODAL --}}
  <div class="pe-modal-backdrop" id="brandModal" aria-hidden="true">
      <div class="pe-modal" role="dialog" aria-modal="true" aria-labelledby="brandModalTitle">
          <form id="newBrandForm" enctype="multipart/form-data">
              @csrf

              <div class="pe-modal-header">
                  <div>
                      <h3 class="pe-modal-title" id="brandModalTitle">Neuen Hersteller hinzufügen</h3>
                      <div class="pe-modal-sub">Nach dem Speichern wird der Hersteller automatisch ausgewählt.</div>
                  </div>

                  <button type="button" class="pe-modal-close" data-close-brand-modal>
                      <i class="feather icon-x"></i>
                  </button>
              </div>

              <div class="pe-modal-body">
                  <div class="pe-form-grid">
                      <div class="pe-form-group full">
                          <label class="pe-label">Herstellername</label>
                          <input type="text" class="pe-input" name="name" id="brand_name" placeholder="z. B. Viessmann" required>
                          <span class="pe-error" id="brand_name-error"></span>
                      </div>

                      <div class="pe-form-group">
                          <label class="pe-label">Kürzel</label>
                          <input type="text" class="pe-input" name="initial" id="brand_initial" placeholder="z. B. VIE" maxlength="10" required>
                          <span class="pe-error" id="brand_initial-error"></span>
                      </div>

                      <div class="pe-form-group">
                          <label class="pe-label">Artikelgruppe / Zweck</label>
                          <input type="text" class="pe-input" name="purpose" id="brand_purpose" placeholder="z. B. Wärmepumpe" required>
                          <span class="pe-error" id="brand_purpose-error"></span>
                      </div>

                      <div class="pe-form-group full">
                          <label class="pe-label">Logo / Bild</label>

                          <div class="pe-upload-row">
                              <img src="{{ asset('logo/logo.png') }}" alt="Vorschau" class="pe-preview" id="brandImagePreview">

                              <div style="flex:1;">
                                  <input type="file" class="pe-input" name="image" id="brand_image" accept="image/png,image/jpeg,image/jpg">
                                  <div class="pe-mini-note">
                                      Optional. PNG, JPG oder JPEG, maximal 2 MB.
                                  </div>
                                  <span class="pe-error" id="brand_image-error"></span>
                              </div>
                          </div>
                      </div>

                      <input type="hidden" name="status" value="Published">
                  </div>
              </div>

              <div class="pe-modal-footer">
                  <button type="button" class="pe-btn-soft" data-close-brand-modal>
                      Abbrechen
                  </button>

                  <button type="submit" class="pe-btn-success" id="saveBrandBtn">
                      <i class="feather icon-save"></i>
                      Hersteller speichern
                  </button>
              </div>
          </form>
      </div>
  </div>
@endsection

@section('script')
  <script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js') }}"></script>
  <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
  <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
  <script src="{{ asset('js/select2.min.js') }}"></script>

  <script>
      const selectedBrandId = @json($product->brand_id ?? '');
      const selectedSubArticle = @json($product->sub_article ?? '');

      const BRAND_STORE_URL = @json(route('product.store.brand'));
      const BRAND_GET_URL = @json(route('product.create.get.brand'));
      const PRODUCT_UPDATE_URL = @json(route('product.update', $product->id));
      const PRODUCT_LIST_URL = @json(url('/product'));
      const PRODUCT_GET_SUB_ARTICLE_URL = @json(route('product.get.sub.article'));

      const LOGO_FALLBACK = @json(asset('logo/logo.png'));
      const BRAND_IMAGE_BASE = @json(asset('images/brand'));
  </script>

  <script>
      function toggleRoofTypeSection() {
          const category = document.getElementById('category')?.value;
          const roofTypeSection = document.getElementById('roof_type_section');

          if (!roofTypeSection) return;

          if (category === 'Dachziegel') {
              roofTypeSection.classList.remove('hidden');
          } else {
              roofTypeSection.classList.add('hidden');
          }
      }

      function refreshFeather() {
          if (window.feather) {
              window.feather.replace();
          }
      }

      function escapeHtml(value) {
          return String(value ?? '')
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
      }
  </script>

  <script>
      /*
      |--------------------------------------------------------------------------
      | Custom Hersteller Modal
      |--------------------------------------------------------------------------
      */
      (function () {
          'use strict';

          const modal = document.getElementById('brandModal');
          const openBtn = document.getElementById('openBrandModalBtn');

          function openBrandModal() {
              if (!modal) return;

              modal.classList.add('is-open');
              modal.setAttribute('aria-hidden', 'false');
              document.body.style.overflow = 'hidden';

              setTimeout(function () {
                  document.getElementById('brand_name')?.focus();
              }, 80);
          }

          function closeBrandModal() {
              if (!modal) return;

              modal.classList.remove('is-open');
              modal.setAttribute('aria-hidden', 'true');
              document.body.style.overflow = '';
          }

          window.openBrandModal = openBrandModal;
          window.closeBrandModal = closeBrandModal;

          openBtn?.addEventListener('click', openBrandModal);

          document.addEventListener('click', function (event) {
              if (event.target.closest('[data-close-brand-modal]')) {
                  closeBrandModal();
                  return;
              }

              if (event.target === modal) {
                  closeBrandModal();
              }
          });

          document.addEventListener('keydown', function (event) {
              if (event.key === 'Escape' && modal?.classList.contains('is-open')) {
                  closeBrandModal();
              }
          });
      })();
  </script>

  <script>
      /*
      |--------------------------------------------------------------------------
      | Sparte Lock
      |--------------------------------------------------------------------------
      */
      document.addEventListener('click', async function (event) {
          const overlay = event.target.closest('#categoryLock .locked-overlay');
          if (!overlay) return;

          const wrap = document.getElementById('categoryLock');
          const select = document.getElementById('category');

          if (!wrap || !select || !wrap.classList.contains('is-locked')) return;

          let ok = false;

          if (window.Swal && typeof Swal.fire === 'function') {
              const result = await Swal.fire({
                  icon: 'question',
                  title: 'Sparte ändern?',
                  text: 'Wenn Sie bestätigen, wird das Feld entsperrt und Sie können die Sparte ändern.',
                  showCancelButton: true,
                  confirmButtonText: 'Ja, entsperren',
                  cancelButtonText: 'Abbrechen'
              });

              ok = !!result.isConfirmed;
          } else {
              ok = window.confirm('Sparte ändern? Feld entsperren?');
          }

          if (!ok) return;

          wrap.classList.remove('is-locked');
          select.removeAttribute('aria-disabled');
          select.removeAttribute('tabindex');
          select.focus();

          if (window.jQuery && jQuery.fn && jQuery.fn.select2) {
              if (!jQuery(select).hasClass('select2-hidden-accessible')) {
                  jQuery(select).select2({ width: '100%' });
              }
          }
      });
  </script>

  <script>
      /*
      |--------------------------------------------------------------------------
      | Quill + Produkt speichern
      |--------------------------------------------------------------------------
      */
      (function () {
          'use strict';

          function initProductQuill() {
              const editorEl = document.getElementById('editor');
              const textarea = document.getElementById('editor_text');

              if (!editorEl || !textarea || typeof Quill === 'undefined') return null;

              const toolbarOptions = [
                  ['bold', 'italic', 'underline', 'strike'],
                  ['blockquote', 'code-block'],
                  [{ header: 1 }, { header: 2 }],
                  [{ list: 'ordered' }, { list: 'bullet' }],
                  [{ script: 'sub' }, { script: 'super' }],
                  [{ indent: '-1' }, { indent: '+1' }],
                  [{ direction: 'rtl' }],
                  [{ size: ['small', false, 'large', 'huge'] }],
                  [{ header: [1, 2, 3, 4, 5, 6, false] }],
                  [{ color: [] }, { background: [] }],
                  [{ font: [] }],
                  [{ align: [] }],
                  ['link', 'image', 'video', 'formula'],
                  ['clean']
              ];

              const quill = new Quill('#editor', {
                  modules: {
                      toolbar: toolbarOptions
                  },
                  theme: 'snow'
              });

              const sync = function () {
                  textarea.value = quill.root.innerHTML;
              };

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

              $(form).on('submit', function (event) {
                  event.preventDefault();
                  btn.click();
              });

              $(btn).on('click', function (event) {
                  event.preventDefault();

                  $('.pe-error').text('');

                  if (typeof window.__syncProductQuill === 'function') {
                      window.__syncProductQuill();
                  }

                  const formData = new FormData(form);

                  $.ajax({
                      url: PRODUCT_UPDATE_URL,
                      method: 'POST',
                      data: formData,
                      contentType: false,
                      processData: false,
                      headers: {
                          'Accept': 'application/json'
                      },

                      success: function (res) {
                          toastr.success((res && res.message) ? res.message : 'Produkt erfolgreich aktualisiert.');

                          const updatedProductId = @json($product->id);

                          window.location.href = PRODUCT_LIST_URL +
                              '?highlight=' + encodeURIComponent(updatedProductId) +
                              '&hl=updated';
                      },

                      error: function (xhr) {
                          if (xhr.status === 422) {
                              const errors = xhr.responseJSON?.errors || {};

                              Object.keys(errors).forEach(function (field) {
                                  const messages = errors[field] || [];
                                  const id = field.replace(/\./g, '_') + '-error';

                                  $('#' + id).text(messages[0] || '');

                                  messages.forEach(function (message) {
                                      toastr.error(message);
                                  });
                              });

                              return;
                          }

                          const msg = xhr.responseJSON?.error ||
                              xhr.responseJSON?.message ||
                              'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.';

                          toastr.error(msg);
                      }
                  });
              });
          }

          $(document).ready(function () {
              initProductQuill();
              initProductSave();
              toggleRoofTypeSection();
          });
      })();
  </script>

  <script>
      /*
      |--------------------------------------------------------------------------
      | Hersteller Select2 + AJAX speichern + automatisch auswählen
      |--------------------------------------------------------------------------
      */
      (function () {
          'use strict';

          function formatOption(option) {
              if (!option.id) return option.text;

              const image = $(option.element).data('image') || LOGO_FALLBACK;

              return $(
                  '<span>' +
                      '<img src="' + image + '" class="img-flag" alt="">' +
                      escapeHtml(option.text) +
                  '</span>'
              );
          }

          function normalizeBrand(response, fallbackName = null) {
              const brand = response?.brand || response?.data || response?.item || response;

              return {
                  id: brand?.id || brand?.brand_id || null,
                  name: brand?.name || brand?.brand || brand?.brand_name || fallbackName || '',
                  image: brand?.image || brand?.logo || null,
                  status: brand?.status || 'Published'
              };
          }

          function brandImageUrl(image) {
              if (!image) return LOGO_FALLBACK;

              if (
                  String(image).startsWith('http://') ||
                  String(image).startsWith('https://') ||
                  String(image).startsWith('/')
              ) {
                  return image;
              }

              return BRAND_IMAGE_BASE + '/' + image;
          }

          function addAndSelectBrand(brand) {
              if (!brand || !brand.id || !brand.name) return;

              const $brandSelect = $('#brand');
              const imageUrl = brandImageUrl(brand.image);

              let existing = $brandSelect.find('option[value="' + brand.id + '"]');

              if (!existing.length) {
                  const option = new Option(brand.name, brand.id, true, true);
                  $(option).attr('data-image', imageUrl);
                  $brandSelect.append(option);
              } else {
                  existing.text(brand.name);
                  existing.attr('data-image', imageUrl);
              }

              $brandSelect.val(String(brand.id)).trigger('change.select2').trigger('change');
          }

          function loadBrands(selectedId = selectedBrandId) {
              const $brandSelect = $('#brand');

              $brandSelect.empty().append(
                  '<option disabled data-image="' + LOGO_FALLBACK + '">Bitte Hersteller wählen</option>'
              );

              return $.ajax({
                  url: BRAND_GET_URL,
                  method: 'GET',
                  headers: {
                      'Accept': 'application/json'
                  },

                  success: function (response) {
                      if (!Array.isArray(response)) return;

                      response.forEach(function (brand) {
                          if (brand.status && brand.status !== 'Published') return;

                          const option = new Option(brand.name, brand.id, false, false);
                          $(option).attr('data-image', brandImageUrl(brand.image));

                          $brandSelect.append(option);
                      });

                      if (selectedId) {
                          $brandSelect.val(String(selectedId)).trigger('change');
                      }
                  }
              });
          }

          function resetBrandForm() {
              const form = document.getElementById('newBrandForm');
              const preview = document.getElementById('brandImagePreview');

              if (form) form.reset();
              if (preview) preview.src = LOGO_FALLBACK;

              $('#brand_name-error').text('');
              $('#brand_initial-error').text('');
              $('#brand_purpose-error').text('');
              $('#brand_image-error').text('');
          }

          function initBrandImagePreview() {
              const input = document.getElementById('brand_image');
              const preview = document.getElementById('brandImagePreview');

              if (!input || !preview) return;

              input.addEventListener('change', function () {
                  const file = input.files?.[0];

                  if (!file) {
                      preview.src = LOGO_FALLBACK;
                      return;
                  }

                  preview.src = URL.createObjectURL(file);
              });
          }

          function initBrandStore() {
              const form = document.getElementById('newBrandForm');
              const btn = document.getElementById('saveBrandBtn');

              if (!form || !btn) return;

              form.addEventListener('submit', function (event) {
                  event.preventDefault();

                  $('#brand_name-error').text('');
                  $('#brand_initial-error').text('');
                  $('#brand_purpose-error').text('');
                  $('#brand_image-error').text('');

                  const formData = new FormData(form);
                  const fallbackName = formData.get('name');

                  btn.disabled = true;
                  btn.innerHTML = '<i class="feather icon-loader"></i> Speichern...';
                  refreshFeather();

                  $.ajax({
                      url: BRAND_STORE_URL,
                      method: 'POST',
                      data: formData,
                      contentType: false,
                      processData: false,
                      headers: {
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                          'Accept': 'application/json'
                      },

                      success: function (response) {
                          const brand = normalizeBrand(response, fallbackName);

                          if (brand.id) {
                              addAndSelectBrand(brand);
                          } else {
                              loadBrands().then(function () {
                                  const $brandSelect = $('#brand');
                                  const match = $brandSelect.find('option').filter(function () {
                                      return $(this).text().trim().toLowerCase() === String(fallbackName).trim().toLowerCase();
                                  }).last();

                                  if (match.length) {
                                      $brandSelect.val(match.val()).trigger('change');
                                  }
                              });
                          }

                          window.closeBrandModal();
                          resetBrandForm();

                          toastr.success(
                              response?.save_msg ||
                              response?.message ||
                              'Hersteller wurde erfolgreich gespeichert.'
                          );
                      },

                      error: function (xhr) {
                          if (xhr.status === 422) {
                              const errors = xhr.responseJSON?.errors || {};

                              $('#brand_name-error').text(errors.name?.[0] || '');
                              $('#brand_initial-error').text(errors.initial?.[0] || '');
                              $('#brand_purpose-error').text(errors.purpose?.[0] || '');
                              $('#brand_image-error').text(errors.image?.[0] || '');

                              Object.keys(errors).forEach(function (key) {
                                  if (errors[key]?.[0]) {
                                      toastr.error(errors[key][0]);
                                  }
                              });

                              return;
                          }

                          toastr.error(xhr.responseJSON?.message || 'Hersteller konnte nicht gespeichert werden.');
                      },

                      complete: function () {
                          btn.disabled = false;
                          btn.innerHTML = '<i class="feather icon-save"></i> Hersteller speichern';
                          refreshFeather();
                      }
                  });
              });
          }

          $(document).ready(function () {
              const $brandSelect = $('#brand');

              $brandSelect.select2({
                  templateResult: formatOption,
                  templateSelection: formatOption,
                  width: '100%'
              });

              loadBrands(selectedBrandId);
              initBrandImagePreview();
              initBrandStore();
          });
      })();
  </script>

  <script>
      /*
      |--------------------------------------------------------------------------
      | Sub-Artikel laden
      |--------------------------------------------------------------------------
      */
      $(document).ready(function () {
          const groupId = $('#article_group').val();

          if (groupId) {
              loadSubArticles(groupId, selectedSubArticle);
          }

          $('#article_group').on('change', function () {
              loadSubArticles($(this).val(), null);
          });

          function loadSubArticles(groupId, selected) {
              if (!groupId) return;

              $.ajax({
                  url: PRODUCT_GET_SUB_ARTICLE_URL,
                  type: 'GET',
                  data: {
                      article: groupId
                  },
                  headers: {
                      'Accept': 'application/json'
                  },

                  success: function (response) {
                      const $subArticle = $('#sub_article');

                      $subArticle.empty().append('<option disabled selected>Sub-Artikel wählen</option>');

                      $.each(response, function (_key, value) {
                          const option = new Option(value.sub_article, value.id, false, selected == value.id);
                          $subArticle.append(option);
                      });

                      if ($.fn.select2 && !$subArticle.hasClass('select2-hidden-accessible')) {
                          $subArticle.select2({
                              width: '100%'
                          });
                      }

                      if (selected) {
                          $subArticle.val(String(selected)).trigger('change');
                      }
                  }
              });
          }
      });
  </script>

  <script>
      /*
      |--------------------------------------------------------------------------
      | Select2 Init
      |--------------------------------------------------------------------------
      */
      $(document).ready(function () {
          if ($.fn.select2) {
              $('#article_group').select2({ width: '100%' });
              $('#sub_article').select2({ width: '100%' });
              $('#measure_unit').select2({ width: '100%' });
              $('#roof_type').select2({ width: '100%' });
          }

          refreshFeather();
      });
  </script>
@endsection

@push('scripts')
  <script>
      window.GlobalBreadcrumbs = [
          {
              label: 'Dashboard',
              url: "{{ url('/') }}"
          },
          {
              label: 'Produktliste',
              url: "{{ url('product') }}"
          },
          {
              label: 'Bearbeiten',
              url: "{{ url()->current() }}",
              clickable: false
          }
      ];

      if (window.setGlobalBreadcrumbs) {
          window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
      }
  </script>

  
@endpush

