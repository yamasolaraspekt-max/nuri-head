<style>
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
    padding:2px 8px;
    border-radius:999px;
    border:1px solid #e5e7eb;
    background:#f9fafb;
    color:#6b7280;
    display:inline-flex;
    align-items:center;
    gap:6px;
  }

  /* overlay button that captures clicks */
  .locked-field .locked-overlay{
    position:absolute;
    inset:0;
    z-index:3;
    border:0;
    background: transparent;
    cursor: pointer;
  }

  /* unlocked: remove lock visuals + allow interaction */
  .locked-field:not(.is-locked) .locked-select{ pointer-events:auto; background:initial !important; color:initial !important; }
  .locked-field:not(.is-locked) .locked-control::after{ display:none; }
  .locked-field:not(.is-locked) .lock-icon,
  .locked-field:not(.is-locked) .lock-badge,
  .locked-field:not(.is-locked) .locked-overlay{ display:none; }
</style>


<div class="card mb-1">
    <div class="card-header">
        <h4 class="card-title mb-0">Produktinformationen</h4>
    </div>

    <div class="card-body row">
        <div class="col-md-6">
            <div class="form-group">
                <label>Herstellernummer *</label>
                <input type="text" class="form-control required-field"  name="article_no" id="article_no">
            </div>

            <div class="form-group">
                <label>EAN</label>
                <input type="text" class="form-control" name="ean" id="ean">
            </div>

            <div class="form-group">
                <label>Hersteller *</label>
                <div class="d-flex align-items-center">
                    <select id="brand" name="brand_id" class="form-control  mr-1" style="flex:1;">
                        <option selected disabled data-image="{{ asset('logo/logo.png') }}">
                            Bitte Hersteller wählen
                        </option>
                    </select>
                    <button type="button" class="btn btn-outline-primary btn-icon js-open-brand-modal" title="Neue Marke hinzufügen">
                        <i class="feather icon-plus"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label>Artikelname/Titel *</label>
                <input type="text" class="form-control required-field " name="product" id="product">
            </div>

            <div class="form-group">
                <label>Tyebezeichnung / Modell *</label>
                <input type="text" class="form-control required-field " name="model" id="model">
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label class="d-flex align-items-center justify-content-between">
                        <span>Artikelgruppe</span>

                        <span class="badge badge-info" data-toggle="tooltip" title="Optional: Wenn gewählt, werden Sub-Artikel geladen.">
                            <i class="feather icon-info mr-1"></i> Info
                        </span>
                    </label>

                    @php
$selectedArticleGroup = old('article_group', $product->article_group_id ?? $product->article_group ?? null);
$selectedSubArticle = old('sub_article', $product->sub_article_id ?? $product->sub_article ?? null);
                    @endphp

                    @if(count($article_groups))
                        <select id="article_group" name="article_group" class="form-control">
                            <option value="" {{ empty($selectedArticleGroup) ? 'selected' : '' }}>— optional —</option>
                            @foreach ($article_groups as $art_group)
                                <option value="{{ $art_group->id }}" {{ (string) $selectedArticleGroup === (string) $art_group->id ? 'selected' : '' }}>
                                    {{ $art_group->article_group }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <a class="btn btn-primary btn-block" href="{{ route('article_group.info') }}">
                            Neu Artikelgruppe
                        </a>
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="d-flex align-items-center justify-content-between">
                        <span>Artikel Kategorie</span>

                        <span class="badge badge-info" data-toggle="tooltip" title="Optional: Abhängig von der gewählten Artikelgruppe.">
                            <i class="feather icon-info mr-1"></i> Info
                        </span>
                    </label>

                    <select id="sub_article" name="sub_article" class="form-control">
                        <option value="" {{ empty($selectedSubArticle) ? 'selected' : '' }}>— optional —</option>

                        @if(!empty($selectedArticleGroup) && !empty($sub_article_groups) && count($sub_article_groups))
                            @foreach($sub_article_groups as $sub)
                                <option value="{{ $sub->id }}" {{ (string) $selectedSubArticle === (string) $sub->id ? 'selected' : '' }}>
                                    {{ $sub->sub_article_group ?? $sub->name ?? $sub->title ?? ('#' . $sub->id) }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            {{-- Enable tooltips (Bootstrap 4/5) --}}
            <script>
            document.addEventListener('DOMContentLoaded', () => {
            if (window.$ && $.fn.tooltip) $('[data-toggle="tooltip"]').tooltip(); // BS4
            if (window.bootstrap?.Tooltip) {
                document.querySelectorAll('[data-toggle="tooltip"]').forEach(el => new bootstrap.Tooltip(el)); // BS5
            }
            });
            </script>

        </div>

        {{-- RIGHT COLUMN --}}
        <div class="col-md-6">
            <div class="form-group locked-field is-locked required-wrap" id="categoryLock" title="Sparte ist gesperrt">
                <label class="d-flex align-items-center justify-content-between">
                    <span>Sparte</span>
                    <span class="lock-badge">
                        <i class="feather icon-lock mr-25"></i> gesperrt
                    </span>
                </label>

                <div class="locked-control">
                    <select class="form-control locked-select required-field" id="category" name="category" aria-disabled="true" tabindex="-1">
                        <option value="Produkt" selected>Produkt</option>
                        <option value="Dachziegel">Dachziegel</option>
                        <option value="Ziegel">Ziegel</option>
                        <option value="Fenster">Fenster</option>
                        <option value="Tür">Tür</option>
                    </select>

                    <span class="lock-icon" aria-hidden="true">
                        <i class="feather icon-lock"></i>
                    </span>

                    {{-- CLICK CAPTURE (shows SweetAlert) --}}
                    <button type="button" class="locked-overlay" aria-label="Sparte entsperren"></button>
                </div>
            </div>



            <div class="form-group">
                <label>Beschreibung</label>
                <div id="editor" class="form-control" style="height: 150px; overflow-y:auto;"></div>
                <textarea name="short_description" id="editor_text" hidden></textarea>
            </div>
            <div class="form-group" id="roofTypeGroup" style="display:none;">
                <label>Dachtyp</label>
                <select class="form-control" id="roof_type" name="roof_type">
                    <option value="Satteldach">Satteldach</option>
                    <option value="Flachdach">Flachdach</option>
                    <option value="Garage">Garage</option>
                    <option value="Carport">Carport</option>
                    <option value="Pultdach">Pultdach</option>
                    <option value="Mansarddach">Mansarddach</option>
                    <option value="Walmdach">Walmdach</option>
                    <option value="Krüppelwalmdach">Krüppelwalmdach</option>
                    <option value="Zeltdach">Zeltdach</option>
                </select>
            </div>

            {{-- Units --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Mengeneinheit</label>
                            <select class="form-control select2" name="measure_unit" id="measure_unit">
                                <option value="">Bitte wählen</option>
                                @foreach($measures as $measure)
                                    <option value="{{ $measure->id }}">{{ $measure->measure }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Preiseinheit</label>
                            <input type="text" class="form-control" name="price_unit" id="price_unit" placeholder="z. B. /Stück, /m², /Paket">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Packungseinheit</label>
                            <input type="text" class="form-control" name="package_unit" id="package_unit" placeholder="z. B. Karton, Palette, Box">
                        </div>
                    </div>
                </div>





            <div class="form-group hidden">
                <label>Farbe</label>
                <select class="form-control" name="color" id="color">
                    <option value="">Bitte wählen</option>
                    <option value="Weiß">Weiß</option>
                    <option value="Schwarz">Schwarz</option>
                    <option value="Grau">Grau</option>
                    <option value="Braun">Braun</option>
                    <option value="Beige">Beige</option>
                    <option value="Gold">Gold</option>
                    <option value="Blau">Blau</option>
                    <option value="Gelb">Gelb</option>
                    <option value="Lila">Lila</option>
                    <option value="Silver">Silver</option>
                </select>
            </div>

           
        </div>

       
    </div>
</div>

@include('admin.product.product.stages.brand_modal')


@push('scripts')
<script>
document.addEventListener('click', async function (e) {
  const overlay = e.target.closest('#categoryLock .locked-overlay');
  if (!overlay) return;

  const wrap = document.getElementById('categoryLock');
  const select = document.getElementById('category');
  if (!wrap || !select || !wrap.classList.contains('is-locked')) return;

  // SweetAlert2 confirm (fallback to native confirm)
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

  // unlock
  wrap.classList.remove('is-locked');
  select.removeAttribute('aria-disabled');
  select.removeAttribute('tabindex');
  select.focus();
});
</script>

    
@endpush