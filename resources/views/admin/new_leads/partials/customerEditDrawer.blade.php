@push('style')
<style>
  :root{
    --accent:#93c21c;
    --accentSoft:#cfe09b;
    --blue:#74b2d4;
    --blueSoft:#c0d8ea;
  }

  /* Drawer shell */
  #customerEditDrawer{
    position:fixed; inset:0;
    z-index:9999;
    pointer-events:none;
  }
  #customerEditDrawer .drawer-overlay{
    position:absolute; inset:0;
    background:rgba(15,23,42,.35);
    opacity:0;
    transition:opacity .2s ease;
  }
  #customerEditDrawer .drawer-panel{
    position:absolute; top:0; right:0;
    width:min(920px, 95vw);
    height:100%;
    background:#fff;
    transform:translateX(105%);
    transition:transform .25s ease;
    padding:14px;
    overflow:auto;
  }

  #customerEditDrawer.open{ pointer-events:auto; }
  #customerEditDrawer.open .drawer-overlay{ opacity:1; }
  #customerEditDrawer.open .drawer-panel{ transform:translateX(0); }

  /* Your card design */
  #customerEditDrawer .drawer-card{
    background:#fff;border:1px solid rgba(0,0,0,.08);border-radius:14px;
    box-shadow:0 12px 30px rgba(0,0,0,.06);
  }
  #customerEditDrawer .drawer-head{
    border-bottom:1px solid rgba(0,0,0,.06);
    background:linear-gradient(90deg, rgba(147,194,28,.10), rgba(116,178,212,.12));
    border-top-left-radius:14px;border-top-right-radius:14px;
  }
  #customerEditDrawer label{font-weight:600;color:#1f2937}
  #customerEditDrawer .btn-accent{
    background:var(--accent);border-color:var(--accent);color:#fff;
  }
  #customerEditDrawer .btn-accent:hover{filter:brightness(.95)}
  #customerEditDrawer .help-chip{
    display:inline-flex;align-items:center;gap:.4rem;
    padding:.25rem .55rem;border-radius:999px;
    background:rgba(192,216,234,.45);color:#0f172a;font-size:.8rem;
    border:1px solid rgba(116,178,212,.35);
  }
  #edit_map{
    height:240px;border-radius:14px;overflow:hidden;
    border:1px solid rgba(116,178,212,.35);
    background:rgba(192,216,234,.25);
  }

  /* select2 sizing */
  .select2-container .select2-selection--single{height:38px}
  .select2-container--default .select2-selection--single .select2-selection__rendered{line-height:36px}
  .select2-container--default .select2-selection--single .select2-selection__arrow{height:36px}
</style>
@endpush


<div id="customerEditDrawer">
  <div class="drawer-overlay" onclick="closeCustomerDrawer()"></div>

  <div class="drawer-panel">
    {{-- YOUR CONTENT --}}
    <div class="drawer-card">
      <div class="drawer-head p-3 d-flex align-items-center justify-content-between">
        <div>
          <div class="fw-bold" style="font-size:1.05rem;">Kundendaten bearbeiten</div>
          <div class="help-chip mt-1">Farben: #93c21c · #cfe09b · #74b2d4 · #c0d8ea</div>
        </div>

        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="closeCustomerDrawer()">Schließen</button>
      </div>

      <div class="p-3">
        <div id="customerEditErrors" class="alert alert-danger" style="display:none"></div>
        <div id="customerEditSuccess" class="alert alert-success" style="display:none"></div>

        <form id="customerEditForm">
          @csrf
          <input type="hidden" id="edit_customer_id" name="id">

          <div class="row g-3">
            <div class="col-lg-7">
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label">Anrede</label>
                  <select class="form-control select2-tags" id="edit_title" name="title"
                          data-placeholder="Anrede auswählen oder eingeben">
                    <option></option>
                    <option value="Frau">Frau</option>
                    <option value="Herr">Herr</option>
                    <option value="An die">An die</option>
                    <option value="An den">An den</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Akademischer Titel</label>
                  <select class="form-control select2-tags" id="edit_academic_title" name="academic_title"
                          data-placeholder="Titel auswählen oder eingeben">
                    <option></option>
                    <option value="Dr.">Dr.</option>
                    <option value="Prof.">Prof.</option>
                    <option value="Prof. Dr.">Prof. Dr.</option>
                    <option value="Dipl.-Ing.">Dipl.-Ing.</option>
                    <option value="Mag.">Mag.</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Filiale (Branch)</label>
                  <select class="form-control" id="edit_branch" name="branch" style="width:100%">
                    <option value="">– Bitte wählen –</option>
                  </select>
                </div>

                <div class="col-md-6">
                  <label class="form-label">Vorname</label>
                  <input class="form-control" id="edit_name" name="name" type="text">
                </div>

                <div class="col-md-6">
                  <label class="form-label">Nachname</label>
                  <input class="form-control" id="edit_lastname" name="lastname" type="text">
                </div>

                <div class="col-md-12">
                  <label class="form-label">Firma</label>
                  <input class="form-control" id="edit_firma" name="firma" type="text">
                </div>

                <div class="col-md-12">
                  <label class="form-label">Adresse suchen</label>
                  <input class="form-control" id="edit_address_search" type="text" placeholder="Adresse eingeben…">
                </div>

                <div class="col-md-8">
                  <label class="form-label">Straße / Nr.</label>
                  <input class="form-control" id="edit_street" name="street" type="text">
                </div>
                <div class="col-md-4">
                  <label class="form-label">PLZ</label>
                  <input class="form-control" id="edit_postcode" name="postcode" type="text">
                </div>

                <div class="col-md-8">
                  <label class="form-label">Ort</label>
                  <input class="form-control" id="edit_city" name="city" type="text">
                </div>

                <div class="col-md-4">
                  <label class="form-label">Quelle (Source)</label>
                  <select name="source" id="edit_source" class="form-control select2-tags"
                          data-placeholder="Quelle auswählen oder eingeben" style="width:100%">
                    <option></option>
                    <option value="Telefonisch">Telefonisch</option>
                    <option value="Persönlich">Persönlich</option>
                    <option value="Mail">Mail</option>
                    <option value="Nachbar">Nachbar</option>
                    <option value="Empfehlung">Empfehlung</option>
                    <option value="Solarrechner">Solarrechner</option>
                    <option value="Herstellerlead">Herstellerlead</option>
                    <option value="Event">Event</option>
                    <option value="Messe">Messe</option>
                    <option value="Hausmesse">Hausmesse</option>
                    <option value="Kunde aus Vergangenheit">Kunde aus Vergangenheit</option>
                  </select>
                </div>

                <div class="col-md-4">
                  <label class="form-label">Telefon</label>
                  <input class="form-control" id="edit_phone" name="phone" type="text">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Mobil</label>
                  <input class="form-control" id="edit_telephone" name="telephone" type="text">
                </div>
                <div class="col-md-4">
                  <label class="form-label">E-Mail</label>
                  <input class="form-control" id="edit_email" name="email" type="email">
                </div>

                <input type="hidden" id="edit_latitude" name="latitude">
                <input type="hidden" id="edit_longitude" name="longitude">
              </div>

              <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-accent">Speichern</button>
                <button type="button" class="btn btn-outline-secondary" onclick="closeCustomerDrawer()">Abbrechen</button>
              </div>
            </div>

            <div class="col-lg-5">
              <label class="form-label">Google Map</label>
              <div id="edit_map"></div>
              <small class="text-muted d-block mt-2">Map aktualisiert sich automatisch nach Adresseingabe.</small>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

 