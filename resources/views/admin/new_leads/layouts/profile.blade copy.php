<style>
  
/* Global Resets */
body, html {
    margin: 0;
    padding: 0;
    height: 100%;
}

.container-flex {
    display: flex;
    height: 100vh;
    overflow: hidden;
}


/* Layout Container */
.customer-wrapper {
  display: flex;
  flex-direction: column;
  height: 100vh;
  overflow: hidden;
}

/* Top Nav */
.customer-nav {
  background-color: #93c119;
  padding: 1rem 1.5rem;
  margin-bottom: 0.4rem; 
  color: white;
  font-size: 14px;
}

.customer-navs {
  background-color: #2c3e4f;
  padding: 1rem 1.5rem;
  margin-bottom: 0.4rem; 
  color: white;
  font-size: 14px;
}

.customer-nav .text-uppercase {
  letter-spacing: 0.5px;
  font-size: 20px;
  font-weight: bold;
}

.customer-nav .row {
  display: flex;
  flex-wrap: nowrap;
  align-items: stretch;
  overflow-x: auto;
}

.customer-nav .col {
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.customer-nav .inner-col {
  height: 100%;
  padding-left: 1rem;
  padding-right: 1rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.customer-nav .inner-col.border-start {
  border-left: 1px solid white;
}



.customer-nav-top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.customer-nav-title {
  font-weight: bold;
  font-size: 1.1rem;
  color: #202020;
}
.customer-nav-icons {
  display: flex;
  gap: 1rem;
  align-items: center;
  color: #2d3e4f;
}
.customer-nav-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 2rem;
  font-size: 0.9rem;
  color: #333;
}
.customer-nav-tabs {
  margin-top: 0.25rem;
}

/* Flex Main Layout */
.layout {
  display: flex;
  flex-direction: row;
  flex-wrap: nowrap;
  width: 100%;
  height: 100%;
}


/* Sidebar Left */
.customerSidebar {
  width: 300px;
  background-color: #2d3e4f;
  color: #fff;
  padding: 1rem;
  overflow-y: auto;
  height: 99%;
  flex-shrink: 0;
  transition: width 0.3s ease;
}
.customerSidebar.minimized {
  width: 60px;
  padding: 1rem 0.3rem;
}
.customerSidebar.minimized .text,
.customerSidebar.minimized .sub-nav,
.customerSidebar.minimized .object-address,
.customerSidebar.minimized .customer-summary {
  display: none !important;
}

/* Sidebar Scrollbar */
.customerSidebar::-webkit-scrollbar {
  width: 6px;
}
.customerSidebar::-webkit-scrollbar-thumb {
  background-color: #666;
  border-radius: 4px;
}
.customerSidebar::-webkit-scrollbar-track {
  background: transparent;
}

/* Minimize/Dashboard Buttons */
.minimize-btn,
.dashboard-btn {
  background: none;
  border: none;
  color: #fff;
  font-size: 1rem;
  width: 100%;
  margin-bottom: 1rem;
  text-align: left;
  cursor: pointer;
}
.minimize-btn:hover,
.dashboard-btn:hover {
  color: #0d6efd;
}

/* Object & Address */
.object-header {
  cursor: pointer;
  padding: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border-radius: 0.3rem;
  transition: background-color 0.2s ease;
}
.object-header:hover {
  background-color: #3a4b5d;
}
.object-address {
  font-size: 0.8rem;
  margin-left: 2rem;
  margin-bottom: 1rem;
  border-bottom: 1px solid #fff;
}

/* Product Link */
.project-link {
  cursor: pointer;
  background: #fff;
  color: #000;
  margin-bottom: 0.5rem;
  padding: 0.5rem 1rem;
  border-radius: 0.4rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.project-link:hover {
  background-color: #e2e6ea;
}

/* Status Badges */
.status-badge {
  font-size: 0.75rem;
  padding: 0.2rem 0.5rem;
  border-radius: 0.4rem;
  color: #fff;
}
.bg-planung { background-color: #95c120; }
.bg-lead    { background-color: #74b2d4; }
.bg-stopp   { background-color: #ff5733; }

/* Sub Navigation */
.sub-nav {
  display: none;
  margin-left: 1rem;
}
.sub-nav.show {
  display: block;
}
.sub-nav button {
  background: none;
  border: none;
  padding: 0.4rem 0.5rem;
  color: #fff;
  width: 100%;
  text-align: left;
  font-size: 0.9rem;
  cursor: pointer;
}
.sub-nav button:hover {
  background-color: #3a4b5d;
  border-radius: 0.3rem;
}

.contentStation {
  background: #bfbfbf;
}
.contentStation {
  flex: 1;
  min-width: 0; /* 🛠 ensures it shrinks correctly */
  overflow: hidden;
  position: relative;
}

.right-panel {
  width: 350px;
  flex-shrink: 0;
  background: #f1f0f0;
  border-left: 1px solid #ccc;
  overflow: hidden;
}

/* Main Content */
.main-content {
  flex: 1;
  overflow-y: auto;
  flex-grow: 1; 
  padding: 1rem;
  background:rgb(191 191 191);
  transition: all 0.3s ease;

}

/* Right Panel */
 

.panel-controls {
  position: relative;
  z-index: 1000;
}

.floating-show-btn {
  position: fixed;
  top: 110px;
  z-index: 1000;
  background: #fff;
  border: 1px solid #ccc;
  padding: 6px 10px;
  border-radius: 5px;
  color: #8fc73e;
  /* box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); */
}

.floating-show-btn.start {
  left: 10px;
}

.floating-show-btn.end {
  right: 10px;
}

.main-hidden {
  display: none !important;
}

 

 

.right-fullscreen {
  width: 100% !important;
  position: relative;
  z-index: 999;
  background: #ececec;
}

.right-hidden {
  display: none !important;
}

.main-hidden,
.sidebar-hidden {
  display: none !important;
}

 

.badge-danger {
    background-color: #dc3545 !important;
}

.badge-primary {
    background-color: #007bff !important;
}
 
.collapse {
  transition: height 0.3s ease, opacity 0.3s ease;
  overflow: hidden;
}


  </style>
 
<style>
  .sidebar-gallery {
    position: fixed;
    top: 0;
    right: -200%;
    width: 80%;
    height: 100%;
    background: #fff;
    /* box-shadow: -2px 0 5px rgba(0,0,0,0.2); */
    padding: 10px;
    z-index: 9999;
    overflow-y: auto;
    transition: right 0.3s ease-in-out;
}
.sidebar-gallery.active {
    right: 0;
}
.sidebar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.gallery-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}



</style>


<!-- Task Style  -->
 <style>
.new_task {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0, 0, 0, 0.3);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}
.new_task.active {
    display: flex !important;
}

 
 </style>


<style>
.scroll-wrapper {
    max-height: 80vh;
    overflow-y: auto;
    padding-right: 8px;

    /* Hide scrollbar for all browsers */
    scrollbar-width: none;           /* Firefox */
    -ms-overflow-style: none;        /* IE/Edge */
}
.scroll-wrapper::-webkit-scrollbar {
    display: none;                   /* Chrome/Safari/Opera */
}

.nav-section-btn.active {
    background-color: #e6f4ea;
    color: #155724;
    border: 1px solid #c3e6cb;
}


</style>
<style>
.time-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #eef2ff;
    color: #1e293b;
    padding: 3px 9px;
    border-radius: 999px;
    border: 1px solid #cbd5f5;
    cursor: pointer;
    font-size: 11px;
    font-weight: 600;
    transition: all .18s ease;
    margin-bottom: 4px;
}
.time-badge:hover {
    background: #e0f2fe;
    border-color: #60a5fa;
    color: #0f172a;
    box-shadow: 0 1px 4px rgba(15,23,42,.18);
}
.time-badge i {
    width: 14px;
    height: 14px;
}
</style>

    <div class="customer-wrapper">
        <div class="customer-nav">
            <div class="row text-white align-items-stretch text-nowrap">
                <!-- Column 1: Name -->
                <div class="col d-flex flex-column justify-content-center">
                    <div class="inner-col">
                        <div class="fw-bold text-uppercase">{{$customer->title}}</div>
                        <div class="fw-bold text-uppercase">{{ $customer->name }} {{ $customer->lastname }}</div>
                        @if($customer->firma)
                        <div class="fw-bold text-uppercase" style="font-size:11px">Firma: {{ $customer->firma }}</div> 
                        @endif
                        <small><div>{{ \Carbon\Carbon::parse($customer->created_at)->isoFormat('DD.MM.YYYY') }} - {{ \Carbon\Carbon::parse($customer->created_at)->diffForHumans() }}</div></small>
                    </div>
                </div>

               

                <!-- Column 2: Address -->
                <div class="col d-flex flex-column justify-content-center">
                    <div class="inner-col border-start">
                        <div>{{ $customer->street }}</div>
                        <div>{{ $customer->postcode }} {{ $customer->city }}</div>
                    </div>
                </div>

                <!-- Column 3: Contact -->
                <div class="col d-flex flex-column justify-content-center">
                    <div class="inner-col border-start">
                        <div>{{ $customer->email }}</div>
                        <div>{{ $customer->phone }}</div>
                        @if (!empty($customer->telephone))
                            <div>{{ $customer->telephone }}</div>
                        @endif
                    </div>
                </div>

                <!-- Column 4: Source -->
                <div class="col d-flex flex-column justify-content-center">
                    <div class="inner-col border-start">
                        <div><strong>Quelle:</strong> {{ $customer->source }}</div>
                    </div>
                </div>

                <!-- Column 5: Notes -->
                <div class="col d-flex flex-column justify-content-center">
                    <div class="inner-col border-start p-2"
                        style="
                            max-height: 80px;
                            overflow-y: auto;
                            overflow-x: hidden;
                            word-wrap: break-word;
                            white-space: pre-wrap;
                            font-size: 14px;
                            text-align: left;
                            cursor: pointer;"
                        onclick="showFullNote(this)"
                        data-note="{{ $customer->info ?? 'Notizen hier' }}">
                        {{ $customer->info ?? 'Notizen hier' }}
                    </div>
                </div>



                         <!-- Column 2: Deal and Offer -->
                  <div class="col d-flex flex-column justify-content-center">
                    <div class="inner-col border-start">
                        <div><strong>Umsatz </strong></div>
                        <div>Gesamt:  - EUR</div>
                        <div>Letzter: -  EUR</div>
                        <div>Datum: -</div>
                    </div>
                </div>
            </div>
        </div>

  
      <div class="layout">
        <div class="customerSidebar" id="customerSidebar"> 
          <button class="minimize-btn" onclick="togglecustomerSidebar()">
              <i data-feather="chevrons-left"></i>
          </button>
            <button class="dashboard-btn"
                    onclick="showDashboard(this)"
                    data-customer-id="{{ request()->id }}"
                    data-alternative-id="{{ $alternative->first()->id }}">
                <i data-feather="grid"></i> <span class="text">Dashboard</span>
            </button>

            @foreach ($alternative as $key => $object)
              <div class="object-section">
                {{-- Object Header --}}
                <div class="object-header d-flex justify-content-between align-items-center" onclick="toggleObject('object{{ $key }}')">
                    <div class="d-flex align-items-center">
                        <i data-feather="home" class="mr-2"></i>
                        <div class="d-flex flex-column">
                            <span class="text font-weight-bold">{{ $object->object_name ?? 'Object' }}</span>
                            <small class="text-muted">
                                {{ $object->street }} {{ $object->postcode }} {{ $object->city }}
                            </small>
                        </div>
                    </div>

                    <!-- Picture placeholder -->

                     @php
                            $firstImage   = $screenshots->where('alternative_id', $object->id)->first();
                            $fullAddress  = trim($object->street . ', ' . $object->postcode . ' ' . $object->city);
                        @endphp

                        @if ($firstImage && !empty($firstImage->image))
                            {{-- Use the secure-image-byFilename route --}}
                            <img src="{{ route('secure.image.byFilename', ['filename' => $firstImage->image]) }}"
                                alt="{{ $firstImage->image_name ?? 'Screenshot' }}"
                                style="width: 100px; height: auto; object-fit: cover; cursor: pointer;"
                                onclick="openSidebarGallery(this)"
                                data-customer-id="{{ $customer->id }}"
                                data-alternative-id="{{ $object->id }}"
                                data-address="{{ $fullAddress }}">
                        @else
                            {{-- Placeholder image --}}
                            <img src="{{ asset('images/icons/placeholder.svg') }}"
                                alt="Object Image"
                                style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%; cursor: pointer;"
                                onclick="openSidebarGallery(this)"
                                data-customer-id="{{ $customer->id }}"
                                data-alternative-id="{{ $object->id }}"
                                data-address="{{ $fullAddress }}">
                        @endif




                    {{-- Sidebar --}}
                    <div id="sidebarGallery{{ $object->id }}" class="sidebar-gallery p-3">
                      {{-- Header --}}
                      <div class="sidebar-header d-flex justify-content-between align-items-center mb-3">
                          <div>
                              <strong>{{ $object->street }} {{ $object->postcode }} {{ $object->city }}</strong>
                          </div>
                          <button onclick="closeSidebarGallery({{ $object->id }})" class="btn btn-sm btn-outline-secondary" title="Schließen">
                              &times;
                          </button>
                      </div>

                      {{-- Gallery Section --}}
                      <div class="gallery-wrapper mb-3" id="galleryImages{{ $object->id }}">
                          <span class="text-muted">📂 Bilder werden geladen...</span>
                      </div>

                      {{-- Screenshot Mode Select --}}
                      <div class="form-group mb-3">
                          <label for="screenshotMode{{ $object->id }}" class="font-weight-bold">Ansichtsmodus wählen:</label>
                          <select id="screenshotMode{{ $object->id }}" class="form-control form-control-sm">
                              <option value="satellite">Satellit</option>
                              <option value="roadmap">Karte</option>
                              <option value="terrain">Gelände</option>
                              <option value="streetview">Street View</option>
                          </select>
                      </div>

                      {{-- Google Map Container --}}
                      <div id="mapScreenshotWrapper{{ $object->id }}" class="mb-3">
                          <div id="mapContainer{{ $object->id }}"
                              class="google-map border"
                              style="width: 100%; height: 300px; background: #f9f9f9; border-radius: 6px; overflow: hidden;">
                          </div>
                      </div>

                      {{-- Screenshot Button --}}
                      <div class="text-right">
                          <button class="btn btn-sm btn-primary"
                                  onclick="triggerScreenshot({{ $customer->id }}, {{ $object->id }})">
                              📷 Screenshot speichern
                          </button>
                      </div>
                  </div>

                </div>  

                {{-- Collapsible Product List --}}
                <div id="object{{ $key }}" class="product-list" style="padding: 20px; display: none;">
                    @foreach ($products->where('alternative_id', $object->id) as $i => $product)
                        @php
                            $productId = "product{$key}_{$i}";
                            $cid = $product->customer_id;
                            $aid = $product->alternative_id;
                            $pid = $product->product_id;
                            $pl_id = $product->p_id;
                            $serviceId = $product->service_id;
                        @endphp

                        {{-- Product Row --}}
                        <div class="project-link"
                            data-product-key="{{ $productId }}"
                            data-object-customer-id="{{ $cid }}"
                            data-object-alternative-id="{{ $aid }}"
                            data-object-product="{{ $pid }}">

                            <div class="product-details">
                                <div class="product">
                                    <span class="text">{{ $product->article_group }}</span>
                                </div>
                                <small>
                                    <span class="text">{{ $product->department_name }}</span> -
                                    <span class="text">
                                        {{
                                            [
                                                'complete' => 'Komplettlösung',
                                                'montage' => 'Montage',
                                                'product' => 'Produkt',
                                                'plan' => 'Planung',
                                                'maintenance' => 'Wartung',
                                                'repair' => 'Reparatur',
                                                'emergency' => 'Notdienst',
                                                'others' => 'Sonstiges',
                                            ][$product->phase_section] ?? ucfirst($product->phase_section)
                                        }}
                                    </span>
                                </small>
                            </div>

                            {{-- RIGHT: status + price + time --}}
                            <div class="d-flex flex-column align-items-end justify-content-center project-meta">
                                <span class="status-badge bg-planung mb-50">
                                    {{ [
                                        'lead'      => 'Lead',
                                        'inquiry'   => 'Anfrage',
                                        'deal'      => 'Auftrag',
                                        'project'   => 'Montage',
                                        'ticket'    => 'Ticket',
                                        'pause'     => 'Pausiert',
                                        'completed' => 'Abschluss',
                                        'junk'      => 'Junk',
                                        'offer'     => 'Angebot',
                                        'accept'    => 'Offen',
                                    ][$product->status] ?? ucfirst($product->status) }}
                                </span>

                                {{-- PROJECT TIME BADGE --}}
                                <button type="button"
                                        class="time-badge project-time-trigger mb-50"
                                        data-customer-id="{{ $cid }}"
                                        data-alternative-id="{{ $aid }}"
                                        data-product-id="{{ $pid }}">
                                    <i data-feather="clock" class="mr-25"></i>
                                    <span class="time-label">Projektzeit</span>
                                </button>

                                <button type="button"
                                        class="price-badge price-edit-trigger"
                                        data-pl-id="{{ $product->p_list_id ?? $product->p_id ?? '' }}"
                                        data-current-price="{{ $product->price ?? 0 }}">

                                    <span class="price-label">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 12v7a2 2 0 0 1-2 2h-7l-7-7 9-9 7 7z"></path>
                                            <circle cx="12" cy="12" r="1"></circle>
                                        </svg>
                                    </span>
                                    <span class="price-value">
                                        {{ number_format($product->price ?? 0, 2, ',', '.') }} €
                                    </span>
                                </button>
                            </div>

                            <span class="status-badge bg-planung">
                                {{
                                    [
                                        'lead' => 'Lead',
                                        'inquiry' => 'Anfrage',
                                        'deal' => 'Auftrag',
                                        'project' => 'Montage',
                                        'ticket' => 'Ticket',
                                        'pause' => 'Pausiert',
                                        'completed' => 'Abschluss',
                                        'junk' => 'Junk',
                                        'offer' => 'Angebot',
                                        'accept' => 'Offen',
                                    ][$product->status] ?? ucfirst($product->status)
                                }}
                            </span>
                        </div>

                        {{-- Sub Nav Section (Hidden by default) --}}
                        <div id="{{ $productId }}" class="sub-nav" style="display: none;">
                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadkanban({{ $cid }}, {{ $aid }}, {{ $pid }}, 'kanban')">
                                <i data-feather="columns"></i> Kanban
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadFullAlternativeObject(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}">
                                <i data-feather="layers"></i> Objektdaten
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadDocuments(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="file-plus"></i> Bilder & Dokumente
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadDocuments(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="file-plus"></i> Nachbarschaft
                            </button>
                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadChecklist(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="check-square"></i> Checkliste
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadTask(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}" data-product-list-id="{{ $pl_id }}">
                                <i data-feather="clipboard"></i> Aufgaben
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'angebote')">
                                <i data-feather="file-text"></i> Angebote
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'auftraege')">
                                <i data-feather="briefcase"></i> Auftrag
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'projekte')">
                                <i data-feather="settings"></i> Montage
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'rechnungen')">
                                <i data-feather="file-invoice"></i> Rechnungen
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); leadProduct(this)"
                                    data-customer-id="{{ $cid }}" data-alternative-id="{{ $aid }}" data-product-id="{{ $pid }}">
                                <i data-feather="shopping-bag"></i> Produkt
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadCalendar({{ $cid }}, {{ $aid }}, {{ $pid }})">
                                <i data-feather="calendar"></i> Kalendar
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); LoadCustomerTicket({{ $cid }}, {{ $aid }}, {{ $pid }}, 'tickets')">
                                <i data-feather="tag"></i> Tickets
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'bewertungen')">
                                <i data-feather="star"></i> Bewertungen
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadSectionPartial({{ $cid }}, {{ $aid }}, {{ $pid }}, 'historie')">
                                <i data-feather="book-open"></i> Historie
                            </button>

                            <button class="nav-section-btn" onclick="setActiveSubNav(this); loadStages({{ $cid }}, {{ $aid }}, {{ $pid }}, {{ $serviceId }})">
                                <i data-feather="git-branch"></i> Arbeitsprozess
                            </button>
                        </div>


                    @endforeach
                </div>
            </div> 
            @endforeach

        </div>  
        <div class="contentStation   position-relative p-0 pt-2 m-0">
            <button id="mainContentToggle"  
                  style="    position: absolute;
                              right: 0px;
                              top: -1px;
                              background: white;"
                  class="btn btn-icon btn-icon  btn-flat-primary  waves-effect waves-light">
                  <i class="feather icon-maximize-2"></i>

            </button> 
            <div class="main-content" >  
                <div class="main" id="mainContent">
                    @include('admin.new_leads.layouts.dashboard') 
                </div>
            </div>
        </div>  
        <div class="right-panel  d-flex flex-column p-0">
            <div style="border-bottom: 1px solid #ddd; flex-shrink: 0;">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 style="font-size: 1.1rem; font-weight: bold; color: #94c11f;" class="mb-0 mr-1 ml-1" id="note_title">NOTIZEN</h4>
                    <div class="search d-flex align-items-center">
                        <fieldset class="form-group position-relative mb-0">
                            <input type="text" class="form-control" id="searchNote" placeholder="Suchen">
                            <div class="form-control-position">
                                <i class="feather icon-search"></i>
                            </div>
                        </fieldset>
                        <div class="btn-group" role="group">
                            <button id="toggleNewNoteBtn" onclick="toggleNewNoteArea()" 
                                class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light" 
                                title="Neue Notiz">
                                <i class="feather icon-plus"></i>
                            </button>

                            <button id="btnToggleRightPanelFullscreen" 
                                class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light" 
                                title="Vollbild umschalten">
                                <i class="feather icon-maximize-2"></i>
                            </button>

                            <button id="noteDeletedModal" onclick="loadAllDeletedNotes()" 
                                class="btn btn-icon rounded-circle btn-outline-danger waves-effect waves-light" 
                                title="Gelöschte Notizen anzeigen">
                                <i class="feather icon-trash-2"></i>
                            </button>
                        </div>


                    </div>
                </div>
            </div>

            <div id="note-scroll-wrapper " class="flex-grow-1 overflow-auto p-0 scroll-wrapper">
                <div id="note-list" class="scroll-wrapper"></div>
            </div>

            <div id="noteBackdrop" class="note-backdrop" onclick="toggleNewNoteArea()" style="display: none;"></div>

                <div id="newNoteComposer" class="note-composer">
                    <textarea id="newNoteText" class="form-control my-2" rows="3" placeholder="Write a new note..."></textarea>

                    <!-- ✅ Hidden fields: dynamically filled from dataset -->
                    <input type="hidden" id="noteType" name="type" value="">
                    <input type="hidden" id="noteProductId" name="product_id" value="">

                    <button onclick="submitNote()" class="btn btn-success float-end mb-2">
                        <i class="feather icon-send me-1"></i> Send
                    </button>
                </div>

        </div> 
      </div>
  </div>

      
  {{-- PROJECT TIME DRAWER --}}
    <div id="projectTimeBackdrop" class="ph-backdrop">
        <div class="ph-drawer">
            <div class="ph-header">
                <div>
                    <div class="ph-title">Projektzeit</div>
                    <div class="ph-subtitle">
                        <span id="ptProductTitle"></span>
                    </div>
                </div>
                <button type="button" class="ph-close-btn" id="ptCloseBtn" aria-label="Schließen">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="ph-meta-strip">
                <div>
                    Geplante Zeit:
                    <span id="ptBaseTime" class="ph-pill"></span>
                </div>
                <div>
                    Erweiterungen:
                    <span id="ptExtraTime" class="ph-pill"></span>
                </div>
                <div>
                    Gesamtbudget:
                    <span id="ptTotalBudget" class="ph-pill"></span>
                </div>
                <div>
                    Verbraucht:
                    <span id="ptUsedTime" class="ph-pill"></span>
                </div>
                <div>
                    Rest:
                    <span id="ptRemainingTime" class="ph-pill"></span>
                </div>
            </div>

            <div class="ph-body" id="ptBody">
                <div class="mb-1 text-xs text-muted">
                    Zeitraum: <span id="ptDurationLabel"></span>
                </div>

                {{-- TIMELINE --}}
                <div id="ptTimeline" class="mb-1">
                    <!-- filled by JS -->
                </div>

                <hr class="my-1">

                {{-- REQUEST MORE TIME FORM --}}
                <div class="mt-1">
                    <h6 class="mb-50">Mehr Zeit anfragen</h6>
                    <form id="ptRequestForm">
                        @csrf
                        <input type="hidden" name="customer_id" id="ptCustomerId">
                        <input type="hidden" name="alternative_id" id="ptAlternativeId">
                        <input type="hidden" name="product_id" id="ptProductId">
                        <input type="hidden" name="section_id" id="ptSectionId">

                        <div class="form-row">
                            <div class="col-4">
                                <label for="ptExtraHours">Stunden</label>
                                <input type="number" min="0" class="form-control form-control-sm" id="ptExtraHours" name="extra_hours" value="1">
                            </div>
                            <div class="col-4">
                                <label for="ptExtraMinutes">Minuten</label>
                                <input type="number" min="0" max="59" class="form-control form-control-sm" id="ptExtraMinutes" name="extra_minutes" value="0">
                            </div>
                        </div>

                        <div class="form-group mt-50">
                            <label for="ptReason">Begründung</label>
                            <textarea id="ptReason" name="reason" rows="2" class="form-control form-control-sm"
                                    placeholder="Warum wird mehr Zeit benötigt?"></textarea>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="feather icon-clock mr-25"></i> Anfrage senden
                        </button>

                        <div id="ptRequestMessage" class="mt-50 text-xs text-muted"></div>
                    </form>
                </div>

                <hr class="my-1">

                {{-- REQUEST HISTORY --}}
                <div class="mt-1">
                    <h6 class="mb-50">Zeit-Historie</h6>
                    <div id="ptRequestHistory"></div>
                </div>
            </div>
        </div>
    </div>



  <div id="phaseSidebar" class="phase-sidebar"> 
    <div class="phase-sidebar-body"
        data-customer-id=""
      data-alternative-id=""
      data-product-id=""
      data-service-id=""> 

      <p>Lade...</p>
    </div>
  </div>


<!-- Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Neues Produkt hinzufügen</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">X</button>
            </div>
            <div class="modal-body">
                <div class="row g-2 p-2">
                    <div class="col-md-12">
                        <label for="product_id">Produkt auswählen</label>
                        <select name="products" id="customer_product_info" class="form-control select2" style="width: 100%;">
                            {{-- Options will be loaded dynamically --}}
                        </select>

                    </div>


                    <div class="col-md-6">
                        <label for="manufacturer">Hersteller</label>
                        <input type="text" id="manufacturer_note" name="manufacturer_note" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="serial_number">Seriennummer</label>
                        <input type="text" id="serial_number" name="serial_number" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="installation_date">Installationsdatum</label>
                        <input type="date" id="installation_date" name="installation_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="installation_location">Installationsort</label>
                        <input type="text" id="installation_location"  name="installation_location" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="purchased_from_us">Bei uns gekauft</label>
                        <select id="purchased_from_us"  name="purchased_from_us"class="form-control">
                            <option value="1">Ja</option>
                            <option value="0" selected>Nein</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="purchase_date">Kaufdatum</label>
                        <input type="date" id="purchase_date" name="purchase_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="invoice_reference">Rechnung/Referenz</label>
                        <input type="text" id="invoice_reference" name="invoice_reference" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="warranty_until">Garantie bis</label>
                        <input type="date" id="warranty_until" name="warranty_until" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="guarantee_until">Gewährleistung bis</label>
                        <input type="date" id="guarantee_until" name="guarantee_until" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="image_available">Bild vorhanden</label>
                        <select id="image_available" name="image_available" class="form-control">
                            <option value="1">Ja</option>
                            <option value="0" selected>Nein</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="installed_by">Installiert von</label>
                        <input type="text" id="installed_by" name="installed_by" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="department">Abteilung</label>
                        <select id="department_id" name="department_id" class="form-control">
                            
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="notes">Notizen</label>
                        <textarea id="notes_note" name="notes" class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Schließen</button>
                <button type="button" class="btn btn-primary" onclick="addProduct()">Speichern</button>
            </div>
        </div>
    </div>
</div>
 



<div class="modal fade" id="noteDeletedModalWrapper" tabindex="-1" role="dialog" aria-labelledby="noteDeletedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="noteDeletedModalLabel">Gelöschte Notizen</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Schließen">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="noteDeletedModalBody">
        <div class="text-muted">Lade gelöschte Notizen...</div>
      </div>
    </div>
  </div>
</div>

 
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="taskModalLabel">Aufgaben erstellen</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body p-0">
          <form  id="task_form">
                @csrf
                <div class="modal-body p-0">
                    <div class="card p-1">
                        <div class="form-body">
                            <div class="row">  

                            <div class="col-md-12"> 
                            </div> 
                                <div class="col-md-8 col-12">
                                    <label for="task_title">Aufgabentitel</label>
                                    <input type="text" id="task_title" class="form-control" name="task_title">
                                      <input type="hidden" name="customer_id" id="select_customer_id">
                                          <input type="hidden" name="alternative_id" id="select_alternative_id">
                                          <input type="hidden" name="product_id" id="select_product_id">
                                          <input type="hidden" name="is_customer"  value="1">
                                </div>

                                
                                <div class="col-4 d-flex">
                                    <input type="hidden" name="color" id="color" value="#8fc73e">
                                    <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1 mt-2" id="color_drop_down">
                                        <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                            <i class="fa fa-square" id="colorIcon" style="color: #8fc73e;"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <span class="dropdown-item" data-value="#8fc73e">
                                                <i class="fa fa-square" style="color: #8fc73e;"></i> Grün
                                            </span>
                                            <span class="dropdown-item" data-value="#ff0000">
                                                <i class="fa fa-square" style="color: #ff0000;"></i> Rot
                                            </span>
                                            <span class="dropdown-item" data-value="#0000ff">
                                                <i class="fa fa-square" style="color: #0000ff;"></i> Blau
                                            </span>
                                            <span class="dropdown-item" data-value="#ffff00">
                                                <i class="fa fa-square" style="color: #ffff00;"></i> Gelb
                                            </span>
                                            <span class="dropdown-item" data-value="#ff00ff">
                                                <i class="fa fa-square" style="color: #ff00ff;"></i> Magenta
                                            </span>
                                            <span class="dropdown-item" data-value="#00ffff">
                                                <i class="fa fa-square" style="color: #00ffff;"></i> Cyan
                                            </span>
                                            <span class="dropdown-item" data-value="#000000">
                                                <i class="fa fa-square" style="color: #000000;"></i> Schwarz
                                            </span>
                                            <span class="dropdown-item" data-value="#ffffff">
                                                <i class="fa fa-square" style="color: #ffffff; border: 1px solid #ccc;"></i> Weiß
                                            </span>
                                            <span class="dropdown-item" data-value="#808080">
                                                <i class="fa fa-square" style="color: #808080;"></i> Grau
                                            </span>
                                            <span class="dropdown-item" data-value="#ffa500">
                                                <i class="fa fa-square" style="color: #ffa500;"></i> Orange
                                            </span>
                                            <span class="dropdown-item" data-value="#800080">
                                                <i class="fa fa-square" style="color: #800080;"></i> Lila
                                            </span>
                                            <span class="dropdown-item" data-value="#8b4513">
                                                <i class="fa fa-square" style="color: #8b4513;"></i> Braun
                                            </span>
                                            <span class="dropdown-item" data-value="#4682b4">
                                                <i class="fa fa-square" style="color: #4682b4;"></i> Stahlblau
                                            </span>
                                            <span class="dropdown-item" data-value="#5f9ea0">
                                                <i class="fa fa-square" style="color: #5f9ea0;"></i> Kadettenblau
                                            </span>
                                            <span class="dropdown-item" data-value="#d2691e">
                                                <i class="fa fa-square" style="color: #d2691e;"></i> Schokoladenbraun
                                            </span>
                                            <span class="dropdown-item" data-value="#2e8b57">
                                                <i class="fa fa-square" style="color: #2e8b57;"></i> Seegrün
                                            </span>
                                            <span class="dropdown-item" data-value="#dc143c">
                                                <i class="fa fa-square" style="color: #dc143c;"></i> Karmesinrot
                                            </span>
                                            <span class="dropdown-item" data-value="#7fffd4">
                                                <i class="fa fa-square" style="color: #7fffd4;"></i> Aquamarin
                                            </span>
                                            <span class="dropdown-item" data-value="#9932cc">
                                                <i class="fa fa-square" style="color: #9932cc;"></i> Dunkles Lila
                                            </span>
                                            <span class="dropdown-item" data-value="#ff6347">
                                                <i class="fa fa-square" style="color: #ff6347;"></i> Tomate
                                            </span>
                                        </div>
                                    </div> 

                                    <div class="custom-control custom-switch mr-2 mb-1">
                                        <p class="mb-0">Öffentlich</p>
                                        <input type="checkbox" class="custom-control-input" id="customSwitch10" name="public" checked>
                                        <label class="custom-control-label" for="customSwitch10">
                                            <span class="switch-icon-left"><i class="feather icon-check"></i></span>
                                            <span class="switch-icon-right"><i class="feather icon-lock"></i></span>
                                        </label>
                                    </div>


                                    
                                </div> 

                                <div class="col-md-12 col-12">
                                    <label for="description">Beschreibung</label>
                                    <textarea name="description" class="form-control" rows="1"></textarea>
                                </div>
    
                              
                                <div class="col-md-12  time_management">
                                    <div class="row d-flex"  > 
                                        <div class="col-md-3 col-12">
                                            <label for="end_date">Fälligkeitsdatum</label>
                                            <input type="date" id="due_date" class="form-control" name="due_date">
                                                <input type="hidden" name="same_id" value="same">
                                            <input type="hidden" id="start_date" class="form-control" name="start_date"  value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div> 

                                            <div class="col-md-3 col-12">
                                            <label for="end_date">Fälligkeitsuhrzeit</label>
                                            <input type="time" id="due_time" class="form-control" name="due_time">  
                                        </div>  

                                            <div class="col-md-3 col-12">
                                            <label for="end_time">Gesamt Tage</label>
                                            <input type="integer" id="total_day" class="form-control" name="total_day" >
                                        </div> 
                                        <div class="col-md-3 col-12">
                                            <label for="end_time">Gesamtstunden</label>
                                            <input type="integer" id="total_time" class="form-control" name="total_time" >
                                        </div> 
                                    </div> 

                                    <div class="row mt-1" id="task_employee_section">
                                        <div class="col-md-12 col-12">
                                            <label for="employee">Zugewiesen an</label>
                                            <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}" data-image="{{asset('images/employee/'.$emp->image) }}" data-checked="false">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div>

                                    <div class="row mt-1"  >
                                        <div class="col-md-12 col-12">
                                            <label for="controller">Kontroller</label>
                                            <select name="controller[]" id="controller" class="employee" multiple style="width:100%">
                                                @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}" data-image="{{asset('images/employee/'.$emp->image) }}" data-checked="false">{{ $emp->name }} {{ $emp->lastname }}</option>
                                                @endforeach
                                            </select>
                                        </div> 
                                    </div>
                                </div>   

                                <div class="col-md-12 col-12">
                                    <div class="card collapse-header" role="button" data-toggle="collapse" data-target="#collapseTaskKeys" aria-expanded="false" aria-controls="collapseTaskKeys">
                                        <div class="card-header bg-primary text-white p-2 mt-2">
                                            <strong><i class="feather icon-list"></i> Aufgabenschritte</strong>
                                        </div>
                                    </div>
                                    <div id="collapseTaskKeys" class="collapse">
                                        <div class="card-body border p-1 mt-0">
                                            <div class="table-responsive">
                                                <table class="table" id="key_task">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Aufgabenschritte</th>
                                                            <th style="width:28px">Dauer <br><small><code id="key_total_time">23 Stunden</code></small></th>
                                                            <th>Zugewiesen</th>
                                                            <th>Beschreibung</th> 
                                                            <th>Aktion</th> 
                                                        </tr>
                                                    </thead>
                                                    <tbody> 
                                                        <tr>
                                                            <td>1</td>
                                                            <td><input type="text" name="key[0][task]" class="form-control"></td>
                                                            <td><input type="integer" name="key[0][duration]" class="form-control"></td>
                                                            <td>
                                                                <select name="key[0][employee_id][]" multiple style="width:100%">
                                                                    @foreach ($employees as $employee)
                                                                        <option value="{{ $employee->id}}" data-image="{{ asset('images/employee/'.$employee->image) }}">{{ $employee->name}} {{$employee->lastname}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td><textarea name="key[0][key_description]" class="form-control"></textarea></td>
                                                            <td>
                                                                <button type="button" class="btn btn-icon btn-primary add-task-steps"><i class="fa fa-plus"></i></button>
                                                                <button type="button" class="btn btn-icon btn-primary remove-task-steps"><i class="fa fa-minus"></i></button>
                                                            </td>
                                                        </tr>  
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sub Task Table -->
                                
                            </div>
                        </div>
                    </div> 
                    <div class="modal-footer"> 
                        <button type="button" class="btn btn-danger mr-1 waves-effect waves-light close_task_window" data-dismiss="modal" ><i class="feather icon-x"></i> abbrechen</button>
                        <button type="button" class="btn btn-primary save-task-close"><i class="feather icon-save"></i> speichern</button> 
                    </div>
            </form>
      </div>
    </div>
  </div>
</div>



 