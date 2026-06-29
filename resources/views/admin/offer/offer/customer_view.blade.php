@extends('admin.layouts.app')
@section('title') Angebot @endsection
@section('style')
<style>
    .kanban-board { display: flex; flex-wrap: nowrap; overflow-x: auto; gap: 1rem; }
    .kanban-column { flex: 0 0 300px; background-color: #f8f9fa; border-radius: 8px; padding: 1rem; min-height: 500px; }
    .kanban-card { background-color: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); cursor: grab; }
    .card-meta { font-size: 0.875rem; color: #6c757d; }
    .card-icons i { cursor: pointer; margin-right: 0.5rem; color: #6c757d; }
    .panel { position: fixed; top: 0; right: -100%; width: 80%; height: 100%; background: #fff; box-shadow: -2px 0 5px rgba(0,0,0,0.1); z-index: 1050; overflow-y: auto; transition: right 0.3s ease-in-out; }
    .panel.active { right: 0; }
    .panel-header { padding: 1rem; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
    .progress {
        height:2.357rem !important;
    }

    .card-icons i:hover {
        color:#8fc73e;
        font-size:20px;
    }


    .badge {
        display: inline-block;
        padding: 0.25em 0.4em;
        font-size: 75%;
        font-weight: 600;
        line-height: 1;
        color: #fff;
        background-color: red;
        border-radius: 10px;
        position: absolute;
        top: -8px;
        right: -10px;
    }


    .card-icons { display: inline-flex; gap: .5rem; align-items: center; }

        .icon-with-badge {
        position: relative;
        display: inline-flex;
        vertical-align: middle;
        }

        .icon-with-badge .comment-badge {
        position: absolute;
        top: -6px;             /* tweak to taste */
        right: -6px;           /* tweak to taste */
        min-width: 16px;
        height: 16px;
        line-height: 16px;
        padding: 0 .3rem;
        border-radius: 999px;
        font-size: 10px;
        pointer-events: none;  /* badge won't steal clicks */
        }


          /* position the comment badge on the mail icon */
        .icon-with-badge{position:relative;display:inline-block}
        .icon-with-badge .comment-badge{
            position:absolute;top:-6px;right:-8px;font-size:.7rem;line-height:1
        }

        /* lightweight card menu */
        .kanban-menu{
            position:absolute;top:125%;right:-123px;min-width:240px;
            background:#fff;border:1px solid #e5e7eb;border-radius:12px;
            box-shadow:0 20px 40px rgba(0,0,0,.12);padding:6px;display:none;z-index:1000
        }
        .kanban-menu.is-open{display:block}
        .kanban-menu .menu-item{
            display:flex;align-items:center;gap:.55rem;padding:.55rem .65rem;
            border-radius:10px;cursor:pointer;text-decoration:none;color:#111827
        }
        .kanban-menu .menu-item:hover{background:#f3f4f6}
        .kanban-menu .meta{font-size:.75rem;color:#6b7280;padding:.35rem .65rem .5rem}


  </style>

<meta name="csrf-token" content="{{ csrf_token() }}">
 <!-- In your main Blade layout (e.g. admin.layouts.app or similar) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

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
                            <h2 class="content-header-title float-left mb-0">Verkauf Phase</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">liste</a>
                                    </li> 
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
      
            </div>
            <div class="content-body"> 
            <section id="basic-tabs-components">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card overflow-hidden"> 
                                <div class="card-content">
                                    <div class="row">
                                        <div class="row mb-2 text-white">
                                            <div class="col-md-2">
                                                <div class="card text-center" style="background-color: #93c21c;">
                                                    <div class="card-body">
                                                    <h5 class="card-title">Gesamt Angebot</h5>
                                                    <div style="height: 150px;"><canvas id="approvedChart"></canvas></div>
                                                    <p class="card-text fs-3">60%</p>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="col-md-2">
                                                <div class="card text-center" style="background-color: #93c21c;">
                                                    <div class="card-body">
                                                    <h5 class="card-title">Zusage</h5>
                                                    <div style="height: 150px;"><canvas id="approvedChart"></canvas></div>
                                                    <p class="card-text fs-3">60%</p>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="col-md-2">
                                                <div class="card text-center" style="background-color: #f0ad4e;">
                                                    <div class="card-body">
                                                    <h5 class="card-title">Offen</h5>
                                                    <div style="height: 150px;"><canvas id="openChart"></canvas></div>
                                                    <p class="card-text fs-3">30%</p>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="col-md-2">
                                                <div class="card text-center" style="background-color: #d9534f;">
                                                    <div class="card-body">
                                                    <h5 class="card-title">Junk</h5>
                                                    <div style="height: 150px;"><canvas id="rejectedChart"></canvas></div>
                                                    <p class="card-text fs-3">10%</p>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="col-md-2">
                                                <div class="card text-center" style="background-color: #d9534f;">
                                                    <div class="card-body">
                                                    <h5 class="card-title">Absage</h5>
                                                    <div style="height: 150px;"><canvas id="rejectedChart"></canvas></div>
                                                    <p class="card-text fs-3">10%</p>
                                                    </div>
                                                </div>
                                                </div>

                                                <div class="col-md-2">
                                                <div class="card text-center" style="background-color: #d9534f;">
                                                    <div class="card-body">
                                                    <h5 class="card-title">Pausiert</h5>
                                                    <div style="height: 150px;"><canvas id="rejectedChart"></canvas></div>
                                                    <p class="card-text fs-3">10%</p>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>

                                            <div class="row mt-1">
                                                
                                            </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 col-12 mb-1 p-2">
                                            <fieldset>
                                                <div class="input-group"> 
                                                    <input type="text" class="form-control" placeholder="Suchen..." aria-label="Amount">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary waves-effect waves-light" id="add_new_offer" type="button" data-toggle="modal" data-target="#offerModal">Erstellen</button>
                                                    </div>
                                                </div>
                                            </fieldset>   
                                        </div> 
                                    </div>
                                    <div class="card-body">
                                         <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">Kanban</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link " id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false">Liste</a>
                                            </li> 
                                            
                                        </ul>
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                @include('admin.offer.offer.kanban')
                                            </div>
                                            <div class="tab-pane " id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                                <p>Pudding candy canes sugar plum cookie chocolate cake powder croissant. Carrot cake tiramisu danish
                                                    candy cake muffin croissant tart dessert. Tiramisu caramels candy canes chocolate cake sweet roll
                                                    liquorice icing cupcake.</p>
                                            </div> 
                                        
                                           
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>


    <div class="modal fade" id="offerModal" tabindex="-1" role="dialog" aria-labelledby="offerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="offerModalLabel">Neues Angebot</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <form id="offerForm">
            <!-- Customer -->
            <div class="form-group">
                <label for="customerSelect">Kunde</label>
                <select class="form-control selectable" id="customerSelect" style="width:100% !important">
                <option value="">Bitte Kunde wählen...</option>
                </select>
            </div>

            <!-- Product selection -->
            <div class="form-group">
                <label for="productSelect">Produkte (mit Objektadresse)</label>
                <select class="form-control selectable" id="productSelect" style="width:100% !important">
                <option value="">Bitte Kunde zuerst wählen...</option>
                </select>
            </div>

            <!-- Assigned employee -->
            <div class="form-group">
                <label for="createdForSelect">Zuständiger Mitarbeiter</label>
                <select class="form-control selectable" id="createdForSelect" style="width:100% !important">
                <option value="">Bitte Mitarbeiter wählen...</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                @endforeach
                </select>
            </div>

            <!-- Progress & Missing -->
            <div id="productDetails" class="mt-4 d-none">
                <h6>Informationsstand:</h6>
                <div class="progress mb-2">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%">0%</div>
                </div>
                <div id="missingFieldsBox">
                <h6>Fehlende Felder:</h6>
                <ul id="missingFieldsList" class="small pl-3"></ul>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">speichern</button>
            <button type="button" class="btn btn-danger mr-1 mb-1 waves-effect waves-light" data-dismiss="modal">abbrechen</button>
            </form>
        </div>
        </div>
    </div>
    </div>

@endsection

@section('script')
 
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
  const chartConfig = (color, value) => ({
    type: 'doughnut',
    data: { datasets: [{ data: [value, 100 - value], backgroundColor: [color, '#e9ecef'], borderWidth: 0 }] },
    options: { cutout: '80%', plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false }
  });
  new Chart(document.getElementById('approvedChart'), chartConfig('#93c21c', 60));
  new Chart(document.getElementById('openChart'), chartConfig('#f0ad4e', 30));
  new Chart(document.getElementById('rejectedChart'), chartConfig('#d9534f', 10));


 
</script>

 
 
<script>
  // ✅ Define these first so they are globally accessible
  function openPanel(panelId) {
    closeAllPanels();
    document.getElementById(panelId).classList.add('active');
  }

  function closePanel(panelId) {
    document.getElementById(panelId).classList.remove('active');
  }

  function closeAllPanels() {
    document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  }

    
</script>

  <!-- Select2 + jQuery -->
<!-- Select2 + jQuery -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

    $(document).on('keydown', '.select2-search__field', function(e){
    e.stopPropagation(); // prevent global handlers from hijacking typed keys
    });
document.addEventListener('DOMContentLoaded', function () {
    const customerSelect = document.getElementById('customerSelect');
    const productSelect  = document.getElementById('productSelect');
    const progressBar    = document.getElementById('progressBar');
    const missingList    = document.getElementById('missingFieldsList');
    const productDetails = document.getElementById('productDetails');

    const $customer = $('#customerSelect');
    const $product  = $('#productSelect');

    // INIT Select2
    $customer.select2({
        placeholder: 'Bitte Kunde wählen...',
        allowClear: true,
        width: '100%',
         dropdownParent: $('#offerModal') 
        // tags: true, tokenSeparators: [',',' ']   // (optional) enable tag creation
    });

    $product.select2({
        placeholder: 'Bitte wählen...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#offerModal') // <-- set to your real modal if used 
    });

    // Load customers
    fetch('/api/customers-with-products')
        .then(res => res.json())
        .then(data => {
            $customer.empty().append(new Option('Bitte Kunde wählen...', '', true, false));
            data.forEach(c => {
                $customer.append(new Option(`${c.name} ${c.lastname}`, c.id, false, false));
            });
            $customer.trigger('change.select2');
        })
        .catch(() => alert('Kunden konnten nicht geladen werden.'));

    // Load products for selected customer
    $customer.on('change', function () {
        const customerId = this.value;

        // reset UI
        $product.empty()
                .append(new Option('Lade Produkte...', '', true, false))
                .trigger('change.select2');
        productDetails.classList.add('d-none');
        progressBar.style.width = '0%';
        progressBar.innerText = '';
        missingList.innerHTML = '';

        if (!customerId) return;

        fetch(`/api/customer-products/${customerId}`)
            .then(res => res.json())
            .then(data => {
                $product.empty().append(new Option('Bitte wählen...', '', true, false));

                if (!Array.isArray(data) || data.length === 0) {
                    $product.append(new Option('Keine Produkte gefunden', '', true, false));
                    $product.trigger('change.select2');
                    return;
                }

                data.forEach(item => {
                    const text = `${item.product_name} – ${item.full_address}`;
                    const opt  = new Option(text, item.alternative_id, false, false);
                    // carry metadata for later
                    opt.setAttribute('data-product', item.product_id);
                    opt.setAttribute('data-service', item.service);
                    opt.setAttribute('data-service-id', item.service_id);
                    opt.setAttribute('data-department-id', item.department_id);
                    opt.setAttribute('data-fill', item.fill_percent ?? 0);
                    productSelect.add(opt);
                });

                $product.val('').trigger('change.select2'); // refresh dropdown
            })
            .catch(() => alert('Produkte konnten nicht geladen werden.'));
    });

    // Show progress bar and missing fields
    $product.on('change', function () {
        const selected = this.selectedOptions && this.selectedOptions[0];
        if (!selected || !selected.value) {
            productDetails.classList.add('d-none');
            return;
        }

        const percent       = selected.dataset.fill || 0;
        const alternativeId = selected.value;

        progressBar.style.width = `${percent}%`;
        progressBar.innerText   = `${percent}%`;
        productDetails.classList.remove('d-none');

        fetch(`/api/object-progress/${alternativeId}`)
            .then(res => res.json())
            .then(data => {
                missingList.innerHTML = '';
                if (!data || !Array.isArray(data.missing) || data.missing.length === 0) {
                    missingList.innerHTML = '<li>Alle erforderlichen Felder sind ausgefüllt.</li>';
                } else {
                    data.missing.forEach(field => {
                        missingList.innerHTML += `<li>${field}</li>`;
                    });
                }
            })
            .catch(() => {
                missingList.innerHTML = '<li>Fehler beim Laden der fehlenden Felder.</li>';
            });
    });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // --------- DOM refs ----------
  const customerSelect   = document.getElementById('customerSelect');
  const productSelect    = document.getElementById('productSelect');
  const createdForSelect = document.getElementById('createdForSelect');
  let fileGallery = null;

  const columns = {
    neu:             document.querySelector('.kanban-column:nth-child(1)'),
    in_verhandlung:  document.querySelector('.kanban-column:nth-child(2)'),
    abgeschlossen:   document.querySelector('.kanban-column:nth-child(3)'),
    genehmigt:       document.querySelector('.kanban-column:nth-child(4)'),
    junk:            document.querySelector('.kanban-column:nth-child(5)'),
  };

  function clearKanbanCards() {
    Object.values(columns).forEach(col => col.querySelectorAll('.kanban-card').forEach(card => card.remove()));
  }

  // --------- helpers ----------
  const HEATPUMP_KEYWORDS = ['wp','wärmepumpe','warmpumpe','heatpump','heat pump','heat-pump'];

  const productCache = new Map();
  async function fetchProduct(productId) {
    if (!productId) return null;
    if (productCache.has(productId)) return productCache.get(productId);

    const res = await fetch(`/offer/product/type/${productId}`);
    if (!res.ok) throw new Error('Failed to load product');
    const data = await res.json();

    const normalized = {
      id: data.id,
      name: (data.article_group || data.article_groups || data.name || '').toString(),
      initial: (data.initial || '').toString()
    };
    productCache.set(productId, normalized);
    return normalized;
  }

  async function isHeatpumpByProductId(productId) {
    const p = await fetchProduct(productId);
    if (!p) return false;
    const name = p.name.toLowerCase();
    const initial = p.initial.toLowerCase();
    return initial === 'wp' || HEATPUMP_KEYWORDS.some(k => name.includes(k));
  }

  function escapeHtml(s) {
    return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  function closeAllMenus() {
    document.querySelectorAll('.kanban-menu').forEach(m => { m.style.display = 'none'; m.classList.remove('is-open'); });
  }

  let outsideMenuListenerAttached = false;
  function ensureOutsideMenuCloser() {
    if (outsideMenuListenerAttached) return;
    document.addEventListener('click', () => closeAllMenus());
    outsideMenuListenerAttached = true;
  }

  // --------- Kanban render ----------
  function loadKanbanCards() {
    fetch('/api/offers/kanban')
      .then(res => res.json())
      .then(data => {
        data.forEach(offer => {
          const card = document.createElement('div');
          card.className = 'kanban-card';
          card.setAttribute('draggable', 'true');
          card.id = 'card-' + offer.id;
          card.dataset.offerId = offer.id;
          card.dataset.customerId = offer.customer_id;
          card.dataset.alternativeId = offer.alternative_id;
          card.dataset.productId = offer.product_id;

          card.innerHTML = `
            <h6>Kunde: ${escapeHtml(offer.customer)}</h6>
            <p class="card-meta">Produkt: ${escapeHtml(offer.product)}</p>
            <p class="card-meta">Angebotspreis: ${escapeHtml(offer.price)}</p>
            <div class="d-flex justify-content-between align-items-center">
              <div class="card-icons"></div>
              <img src="${offer.employee ? '/images/employee/' + offer.employee : '/images/default-avatar.png'}"
                   class="rounded-circle" alt="${escapeHtml(offer.employee_name)}" width="32">
            </div>`;

          const iconContainer = card.querySelector('.card-icons');

          // 📁 Folder Icon
          const folderIcon = document.createElement('i');
          folderIcon.className = 'feather icon-folder';
          folderIcon.dataset.customerId = offer.customer_id;
          folderIcon.dataset.alternativeId = offer.alternative_id;
          folderIcon.dataset.productId = offer.product_id;
          folderIcon.addEventListener('click', function (e) {
            e.stopPropagation();
            if (this.dataset.customerId && this.dataset.alternativeId && this.dataset.productId) {
              openFilePanel(this.dataset.customerId, this.dataset.alternativeId, this.dataset.productId);
            } else {
              Swal.fire('Fehler', 'Daten für Datei-Upload fehlen!', 'error');
            }
          });
          iconContainer.appendChild(folderIcon);

          // ✉️ 👁 ⚙️ Icons
          ['mail', 'eye', 'settings'].forEach(icon => {
            const i = document.createElement('i');
            i.className = `feather icon-${icon}`;

            if (icon === 'mail') {
              const onMailClick = () => {
                closeAllMenus();
                openPanel('commentPanel');
                loadComments(offer.customer_id, offer.alternative_id, offer.product_id);
              };

              i.title = offer.comment_count > 0 ? `${offer.comment_count} Kommentar(e)` : 'Keine Kommentare';

              if (offer.comment_count > 0) {
                const wrapper = document.createElement('span');
                wrapper.className = 'icon-with-badge';
                wrapper.addEventListener('click', onMailClick);

                const badge = document.createElement('span');
                badge.className = 'badge badge-pill badge-danger comment-badge';
                badge.textContent = offer.comment_count > 99 ? '99+' : String(offer.comment_count);

                wrapper.appendChild(i);
                wrapper.appendChild(badge);
                iconContainer.appendChild(wrapper);
                return;
              } else {
                i.onclick = onMailClick;
              }
            } else if (icon === 'eye') {
              i.onclick = () => { closeAllMenus(); openPanel('detailPanel'); };
            } else if (icon === 'settings') {
              // anchor wrapper
              const wrap = document.createElement('span');
              wrap.style.position = 'relative';
              wrap.style.display  = 'inline-block';

              // build per-card menu
              const menu = document.createElement('div');
              menu.className = 'kanban-menu';
              menu.style.display = 'none';
              menu.innerHTML = `
                <a class="menu-item js-open-config">
                  <i class="feather icon-sliders"></i>
                  <span>Angebot Konfiguration</span>
                </a>

                  <a class="menu-item js-open-offer-folder">
                    <i class="feather icon-sliders"></i>
                    <span>Angebot Ordner</span>
                  </a>
                <div class="meta" style="font-size:.8rem;color:#6b7280;padding:.35rem .65rem .5rem">
                  <div><b>Angebot-ID:</b> ${offer.id}</div>
                  <div><b>Produkt-ID:</b> ${escapeHtml(offer.product_id)}</div>
                  <div><b>Kunde-ID:</b> ${escapeHtml(offer.customer_id)}</div>
                  <div><b>Alternative-ID:</b> ${escapeHtml(offer.alternative_id)}</div>
                </div>
              `;

              // config click
              menu.querySelector('.js-open-config').addEventListener('click', async (e) => {
                e.stopPropagation();
                menu.style.display = 'none';
                try {
                  const isHP = await isHeatpumpByProductId(offer.product_id);
                  if (isHP) {
                    window.location.href = `/offer/wp/config/${offer.id}`;
                  } else {
                    Swal.fire({
                      title: 'Konfiguration bald verfügbar',
                      html: `
                        <div class="text-left" style="font-size:.9rem">
                          Produkt: <b>${escapeHtml(offer.product || '')}</b><br>
                          Angebot-ID: ${offer.id}<br>
                          Produkt-ID: ${escapeHtml(offer.product_id)}<br>
                          Kunde-ID: ${escapeHtml(offer.customer_id)}<br>
                          Alternative-ID: ${escapeHtml(offer.alternative_id)}
                        </div>
                        <div style="margin-top:.5rem">Dieses Produkt wird demnächst unterstützt.</div>
                      `,
                      icon: 'info',
                      confirmButtonText: 'OK'
                    });
                  }
                } catch (err) {
                  console.error(err);
                  Swal.fire('Fehler', 'Produktinformationen konnten nicht geladen werden.', 'error');
                }
              });

              // offer folders click
              menu.querySelector('.js-open-offer-folder').addEventListener('click', (e) => {
                e.stopPropagation();
                menu.style.display = 'none';
                window.location.href = `/admin/offers/${offer.id}/folders`;
              });


              // toggle open/close
              i.addEventListener('click', (e) => {
                e.stopPropagation();
                const open = menu.style.display === 'block';
                closeAllMenus();
                menu.style.display = open ? 'none' : 'block';
                ensureOutsideMenuCloser();
              });

              wrap.appendChild(i);
              wrap.appendChild(menu);
              iconContainer.appendChild(wrap);
              return; // do not append i again
            }

            iconContainer.appendChild(i);
          });

          // drop card into column
          const column = columns[offer.status] || columns['neu'];
          column.appendChild(card);
        });
      });
  }

  // --------- initial load ----------
  loadKanbanCards();

  // --------- Save Offer ----------
  const offerForm = document.getElementById('offerForm');
  if (offerForm) {
    offerForm.addEventListener('submit', function (e) {
      e.preventDefault();

      if (!customerSelect || !productSelect || !createdForSelect) {
        Swal.fire('Fehler', 'Pflichtfelder fehlen.', 'error');
        return;
      }

      const selected = productSelect.options[productSelect.selectedIndex];
      const offerData = {
        customer_id:    customerSelect.value,
        alternative_id: selected.value,
        product_id:     selected.dataset.product,
        service_id:     selected.dataset.serviceId,
        department_id:  selected.dataset.departmentId,
        service:        selected.dataset.service,
        created_for:    createdForSelect.value,
      };

      fetch('/offers/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(offerData)
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          $('#offerModal').modal('hide');
          Swal.fire('Erfolg', 'Angebot gespeichert!', 'success');
          clearKanbanCards();
          loadKanbanCards();
        } else {
          Swal.fire('Fehler', data.message || 'Speichern fehlgeschlagen.', 'error');
        }
      })
      .catch(err => {
        console.error('Fehler:', err);
        Swal.fire('Fehler', 'Technischer Fehler beim Speichern.', 'error');
      });
    });
  }
}); // DOMContentLoaded

// --------- File Manager ----------
let dz = null;
let dzCustomerId = null, dzAlternativeId = null, dzProductId = null;

function openFilePanel(customerId, alternativeId, productId) {
  dzCustomerId = customerId;
  dzAlternativeId = alternativeId;
  dzProductId = productId;

  openPanel('filePanel');
  initDropzone();

  const gallery = document.getElementById('file-gallery');
  gallery.innerHTML = '<div class="col-12 text-center text-muted"><div class="spinner-border"></div><p class="mt-2">Lade Dateien...</p></div>';

  fetch(`/offer-file-manager/${customerId}/${alternativeId}/${productId}`)
    .then(res => res.text())
    .then(html => {
      gallery.innerHTML = html;
      if (typeof GLightbox !== 'undefined') {
        if (window.fileGallery) fileGallery.destroy();
        window.fileGallery = GLightbox({ selector: '.glightbox' });
      }
    });
}

function initDropzone() {
  if (typeof Dropzone === 'undefined') return;

  // destroy previous instances
  if (Dropzone.instances.length > 0) {
    Dropzone.instances.forEach(d => d.destroy());
  }

  dz = new Dropzone("#file-dropzone", {
    url: "{{ route('file.upload') }}",
    paramName: "file",
    maxFilesize: 10,
    timeout: 30000,
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    sending: function (file, xhr, formData) {
      formData.append("customer_id", dzCustomerId);
      formData.append("alternative_id", dzAlternativeId);
      formData.append("product_id", dzProductId);
    },
    success: function () {
      openFilePanel(dzCustomerId, dzAlternativeId, dzProductId);
    },
    error: function (file, response) {
      console.error('Upload error:', response);
      Swal.fire('Fehler', 'Upload fehlgeschlagen.', 'error');
    }
  });
}

function deleteFile(id) {
  if (confirm('Datei wirklich löschen?')) {
    fetch(`/offer-file-manager/delete/${id}`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => document.getElementById('file-' + id).remove());
  }
}

function renameFile(id, newName) {
  fetch(`/offer-file-manager/rename/${id}`, {
    method: 'PUT',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ name: newName })
  });
}

// --------- Comments ----------
function openCommentPanel(customerId, alternativeId, productId) {
  openPanel('commentPanel');

  const form = document.getElementById('newCommentForm');
  const commentsList = document.getElementById('commentsList');

  form.customer_id.value = customerId;
  form.alternative_id.value = alternativeId;
  form.product_id.value = productId;

  commentsList.innerHTML = `
    <div class="text-center text-muted">
      <div class="spinner-border"></div>
      <p class="mt-2">Kommentare werden geladen...</p>
    </div>`;

  fetch(`/offer-comments/${customerId}/${alternativeId}/${productId}`)
    .then(res => res.text())
    .then(html => {
      commentsList.innerHTML = html;
      bindReplyForms(customerId, alternativeId, productId);
    });

  form.onsubmit = function (e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch('/offer-comments/store', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: formData
    })
    .then(r => r.text())
    .then(html => {
      commentsList.insertAdjacentHTML('afterbegin', html);
      form.reset();
      updateCommentBadge(customerId, alternativeId, productId);
      bindReplyForms(customerId, alternativeId, productId);
    });
  };
}

function bindReplyForms(customerId, alternativeId, productId) {
  document.querySelectorAll('.replyForm').forEach(replyForm => {
    const input = replyForm.querySelector('.replyInput');
    const parentId = replyForm.dataset.parentId;

    replyForm.onsubmit = function (e) {
      e.preventDefault();
      const comment = input.value.trim();
      if (!comment) return;

      const data = new FormData();
      data.append('customer_id', customerId);
      data.append('alternative_id', alternativeId);
      data.append('product_id', productId);
      data.append('comment', comment);
      data.append('parent_id', parentId);

      fetch('/offer-comments/store', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: data
      })
      .then(r => r.text())
      .then(html => {
        let replyContainer = replyForm.closest('.media').querySelector(`.reply-children[data-parent="${parentId}"]`);

        if (!replyContainer) {
          replyContainer = document.createElement('div');
          replyContainer.className = 'reply-children ml-4 mt-2';
          replyContainer.dataset.parent = parentId;
          replyForm.closest('.media').appendChild(replyContainer);
        }

        replyContainer.insertAdjacentHTML('beforeend', html);
        input.value = '';
        updateCommentBadge(customerId, alternativeId, productId);
        bindReplyForms(customerId, alternativeId, productId);
      });
    };
  });
}

function loadComments(customerId, alternativeId, productId) {
  fetch(`/offer-comments/${customerId}/${alternativeId}/${productId}`)
    .then(res => res.text())
    .then(html => {
      const commentsList = document.getElementById('commentsList');
      const form = document.getElementById('newCommentForm');

      commentsList.innerHTML = html;

      form.customer_id.value = customerId;
      form.alternative_id.value = alternativeId;
      form.product_id.value = productId;

      form.onsubmit = function (e) {
        e.preventDefault();
        const formData = new FormData(form);

        fetch('/offer-comments/store', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: formData
        })
        .then(r => r.text())
        .then(html => {
          commentsList.insertAdjacentHTML('afterbegin', html);
          form.reset();
          updateCommentBadge(customerId, alternativeId, productId);
          bindReplyForms(customerId, alternativeId, productId);
        });
      };

      bindReplyForms(customerId, alternativeId, productId);
    });
}

function updateCommentBadge(customerId, alternativeId, productId) {
  fetch(`/api/comments/count/${customerId}/${alternativeId}/${productId}`)
    .then(res => res.json())
    .then(data => {
      document.querySelectorAll('.kanban-card').forEach(card => {
        if (
          card.dataset.customerId == customerId &&
          card.dataset.alternativeId == alternativeId &&
          card.dataset.productId == productId
        ) {
          const badge = card.querySelector('.comment-badge');
          if (badge) badge.textContent = data.count;
        }
      });
    });
}

document.getElementById('commentFilter')?.addEventListener('change', function () {
  const filter = this.value;
  document.querySelectorAll('.kanban-card').forEach(card => {
    const count = parseInt(card.querySelector('.comment-badge')?.innerText || 0);
    if (filter === 'with-comments' && count === 0) {
      card.style.display = 'none';
    } else if (filter === 'no-comments' && count > 0) {
      card.style.display = 'none';
    } else {
      card.style.display = '';
    }
  });
});

function editComment(id, oldComment) {
  const newComment = prompt("Kommentar bearbeiten:", oldComment);
  if (newComment && newComment !== oldComment) {
    fetch(`/offer-comments/${id}/update`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ comment: newComment })
    }).then(() => {
      document.getElementById(`comment-text-${id}`).innerText = newComment;
    });
  }
}

function deleteComment(id) {
  if (confirm("Willst du diesen Kommentar löschen?")) {
    fetch(`/offer-comments/${id}/delete`, {
      method: 'DELETE',
      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    }).then(() => document.getElementById(`comment-text-${id}`).closest('.media').remove());
  }
}
</script>




<script>
document.addEventListener("DOMContentLoaded", () => {
  const columns = {
    neu: document.querySelector('.kanban-column:nth-child(1)'),
    in_verhandlung: document.querySelector('.kanban-column:nth-child(2)'),
    abgeschlossen: document.querySelector('.kanban-column:nth-child(3)'),
    genehmigt: document.querySelector('.kanban-column:nth-child(4)'),
    junk: document.querySelector('.kanban-column:nth-child(5)'),
  };

  // Allow drop
  Object.entries(columns).forEach(([status, col]) => {
    col.addEventListener('dragover', e => e.preventDefault());

    col.addEventListener('drop', function (e) {
      e.preventDefault();
      const cardId = e.dataTransfer.getData("text/plain");
      const card = document.getElementById(cardId);

      if (card) {
        this.appendChild(card);

        const offerId = cardId.replace('card-', '');

        fetch('/offers/update-status', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
          },
          body: JSON.stringify({
            id: offerId,
            status: status
          })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire('Aktualisiert', 'Status wurde geändert.', 'success');
          } else {
            Swal.fire('Fehler', 'Status konnte nicht geändert werden.', 'error');
          }
        })
        .catch(err => {
          console.error('Fehler:', err);
          Swal.fire('Fehler', 'Serverfehler.', 'error');
        });
      }
    });
  });

  // Enable dragging
  document.addEventListener('dragstart', function (e) {
    if (e.target.classList.contains('kanban-card')) {
      e.dataTransfer.setData('text/plain', e.target.id);
    }
  });
});
</script>


 
@endsection