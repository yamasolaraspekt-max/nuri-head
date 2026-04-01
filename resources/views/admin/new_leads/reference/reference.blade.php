@extends('admin.layouts.app')

@section('title', 'REFERENZEN')

@once
@push('style')
<style>
  :root {
    --app-bg: #f3f4f6;
    --card-bg: #ffffff;
    --text-main: #1f2937;
    --text-muted: #6b7280;
    --border: #e5e7eb;
    
    /* Custom Palette */
    --primary: #74b2d4;
    --primary-hover: #5a99bd;
    --primary-light: #e3effb;
    --accent: #93c21c;
    --accent-hover: #7baa18;
    --accent-light: #cfe09b;
    --blue-muted: #c0d8ea;

    --success: #10b981;
    --success-light: #ecfdf5;
    --warning: #f59e0b;
    --warning-light: #fffbeb;
    --danger: #ef4444;
    --danger-light: #fef2f2;
    
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow: 0 10px 25px -10px rgb(0 0 0 / 0.15), 0 4px 8px -4px rgb(0 0 0 / 0.08);
    --radius: 14px;
    --transition: all 0.2s ease-in-out;
  }

  /* Enterprise Wrap */
  .oc-wrap {
      font-family: Inter, system-ui, -apple-system, sans-serif;
      color: var(--text-main);
      max-width: 1600px;
      margin: 0 auto;
      padding: 43px;
  }

  .oc-header { margin-bottom: 24px; margin-top:90px; }
  .oc-titlebar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
  }
  .oc-title { font-size: 26px; font-weight: 800; letter-spacing: -0.025em; color: #111827; }
  .oc-sub { font-size: 14px; color: var(--text-muted); margin-top: 4px; }

  /* KPI Grid */
  .oc-analytics {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
  }

  .oc-stat {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 18px;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 14px;
    transition: var(--transition);
  }
  .oc-stat:hover { border-color: var(--primary); transform: translateY(-2px); box-shadow: var(--shadow); }

  .oc-stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    font-size: 20px;
  }
  .oc-stat-icon.primary { background: var(--primary-light); color: var(--primary); }
  .oc-stat-icon.accent { background: var(--accent-light); color: var(--accent); }
  .oc-stat-icon.muted { background: #f3f4f6; color: var(--blue-muted); }

  .oc-stat-meta { min-width: 0; }
  .oc-stat-label { font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }
  .oc-stat-value { font-size: 26px; font-weight: 900; color: #111827; line-height: 1.1; margin-top: 4px; }

  /* Toolbar */
  .oc-toolbar {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 16px 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: flex-end;
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
  }

  .oc-filter-block { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 180px; }
  .oc-filter-block.large { flex: 2; min-width: 280px; }
  .oc-filter-block.small { flex: 0.5; min-width: 100px; }
  .oc-filter-label { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }

  .oc-input, .oc-select {
    background: #f9fafb;
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 14px;
    outline: none;
    transition: var(--transition);
    width: 100%;
    color: var(--text-main);
  }
  .oc-input:focus, .oc-select:focus { background: #fff; border-color: var(--primary); box-shadow: 0 0 0 3px var(--primary-light); }

  .oc-btn {
    background: var(--primary);
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 800;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    height: 42px;
  }
  .oc-btn:hover { background: var(--primary-hover); color: #fff; }
  
  .oc-btn-accent { background: var(--accent); }
  .oc-btn-accent:hover { background: var(--accent-hover); }

  /* Map Container */
  .oc-map-card {
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 24px;
    position: relative;
  }
  #map { width: 100%; height: 650px; } /* BIGGER MAP */

  /* List & Cards */
  .oc-card { background: #fff; border: 1px solid var(--border); border-radius: 16px; box-shadow: var(--shadow-sm); overflow: hidden; margin-bottom: 24px; }
  .oc-card-header { padding: 18px 20px; border-bottom: 1px solid var(--border); font-weight: 800; font-size: 16px; background: #fafafa; display: flex; justify-content: space-between; align-items: center;}

  .oc-list { display: flex; flex-direction: column; gap: 0; }
  .oc-item-row {
    padding: 16px 20px;
    display: grid;
    gap: 16px;
    align-items: center;
    grid-template-columns: 50px 2fr 2fr 1.5fr 150px;
    border-bottom: 1px solid var(--border);
    transition: var(--transition);
    cursor: pointer;
  }
  .oc-item-row:hover { background: var(--primary-light); }
  .oc-item-row:last-child { border-bottom: none; }
  
  .oc-cell-title { font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 4px; display: none; }
  @media(max-width: 1024px) { 
      .oc-item-row { grid-template-columns: 1fr; gap: 8px; } 
      .oc-cell-title { display: block; }
  }

  .oc-ttl { font-weight: 800; font-size: 15px; margin-bottom: 4px; color: #111827; }
  .oc-subt { font-size: 13px; color: var(--text-muted); }

  /* Badges */
  .oc-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    background: #f3f4f6;
    color: var(--text-muted);
    margin-right: 6px;
    margin-bottom: 6px;
    border: 1px solid var(--border);
  }
  .oc-badge.primary { background: var(--primary-light); color: var(--primary-hover); border-color: var(--blue-muted); }
  .oc-badge.accent { background: var(--accent-light); color: var(--accent-hover); border-color: var(--accent); }

  /* InfoWindow Resets */
  .gm-style .gm-style-iw-c { border-radius: 12px; padding: 0; box-shadow: var(--shadow); border: 1px solid var(--border); }
  .gm-style .gm-style-iw-d { overflow: hidden !important; }
  .gm-style-iw-chr { display: none; }
  .custom-info-window { padding: 16px; min-width: 260px; font-family: Inter, sans-serif; }
  .custom-info-window h4 { margin: 0 0 4px 0; font-size: 16px; font-weight: 800; color: var(--text-main); }
  .custom-info-window p { margin: 0 0 12px 0; font-size: 13px; color: var(--text-muted); }
  .custom-info-window .btn { display: block; width: 100%; text-align: center; background: var(--primary); color: #fff; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 700; font-size: 13px;}
  
  /* Charts */
  .chart-wrapper { height: 300px; width: 100%; padding: 20px; }
</style>
@endpush
@endonce

@section('content')
@php
    $t = $totals ?? ['customers'=>0,'offers'=>0,'deals'=>0,'projects'=>0,'tickets'=>0,'products'=>0];
@endphp

<div class="oc-wrap">
  <div class="oc-header">
    <div class="oc-titlebar">
      <div>
        <div class="oc-title">REFERENZEN & KARTE</div>
        <div class="oc-sub">Analysieren und filtern Sie Leads, Projekte und Verkäufe im Umkreis.</div>
      </div>
    </div>
  </div>

  <div class="oc-analytics">
    <div class="oc-stat">
      <div class="oc-stat-icon primary"><i class="feather icon-users"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Kunden</div>
        <div class="oc-stat-value" id="kpi-customers">{{ number_format($t['customers']) }}</div>
      </div>
    </div>
    <div class="oc-stat">
      <div class="oc-stat-icon primary"><i class="feather icon-file-text"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Angebote</div>
        <div class="oc-stat-value" id="kpi-offers">{{ number_format($t['offers']) }}</div>
      </div>
    </div>
    <div class="oc-stat">
      <div class="oc-stat-icon accent"><i class="feather icon-check-circle"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Abschlüsse</div>
        <div class="oc-stat-value" id="kpi-deals">{{ number_format($t['deals']) }}</div>
      </div>
    </div>
    <div class="oc-stat">
      <div class="oc-stat-icon accent"><i class="feather icon-briefcase"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Projekte</div>
        <div class="oc-stat-value" id="kpi-projects">{{ number_format($t['projects']) }}</div>
      </div>
    </div>
    <div class="oc-stat">
      <div class="oc-stat-icon muted"><i class="feather icon-box"></i></div>
      <div class="oc-stat-meta">
        <div class="oc-stat-label">Produkte</div>
        <div class="oc-stat-value" id="kpi-products">{{ number_format($t['products']) }}</div>
      </div>
    </div>
  </div>

  <div class="oc-toolbar">
    <div class="oc-filter-block large">
      <label class="oc-filter-label">Adresse / Standort</label>
      <input type="text" id="address" class="oc-input" placeholder="Zentrums-Adresse eingeben...">
    </div>
    <div class="oc-filter-block small">
      <label class="oc-filter-label">Radius (km)</label>
      <input type="number" id="radius" class="oc-input" value="10" min="1">
    </div>
    <div class="oc-filter-block">
      <label class="oc-filter-label">Status Filter</label>
      <select id="filter-status" class="oc-select">
        <option value="">Alle Status</option>
        <option value="offer">Angebot</option>
        <option value="deal">Abschluss</option>
        <option value="project">Projekt</option>
      </select>
    </div>
    <div class="oc-filter-block">
      <label class="oc-filter-label">Produkt Filter</label>
      <select id="filter-product" class="oc-select">
        <option value="">Alle Produkte</option>
        </select>
    </div>
    <div class="oc-filter-block small">
      <button type="button" class="oc-btn" onclick="executeSearch()">
        <i class="feather icon-search"></i> Suchen
      </button>
    </div>
  </div>

  <div class="oc-map-card">
    <div id="map"></div>
  </div>

  <div class="row">
      <div class="col-lg-8">
        <div class="oc-card">
            <div class="oc-card-header">
                <span>Gefundene Einträge (<span id="count">0</span>)</span>
                <input type="text" id="liveSearch" class="oc-input" placeholder="In Liste suchen..." style="width: 250px; padding: 6px 12px;">
            </div>
            <div class="oc-list" id="result-list">
                <div class="text-center p-4 text-muted">Lade Daten...</div>
            </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="oc-card">
            <div class="oc-card-header">Top Produkte (Auswahl)</div>
            <div class="chart-wrapper">
                <canvas id="topProductsBarChart"></canvas>
            </div>
        </div>
        <div class="oc-card mt-3">
            <div class="oc-card-header">Status Verteilung</div>
            <div class="chart-wrapper">
                <canvas id="stagePieChart"></canvas>
            </div>
        </div>
      </div>
  </div>

</div>
@endsection

@once
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script> 
    // Theme Colors
    const colorPrimary = '#74b2d4';
    const colorAccent = '#93c21c';

    // Global State
    window.allMarkers = [];
    window.lastResults = [];
    let map, geocoder, autocomplete, searchCircle, currentInfoWindow;
    let userLat = 50.1109, userLon = 8.6821; // Default Frankfurt
    let productChart, stageChart;
    
    // Declare the variable here, but don't assign it yet
    let singleMarkerSVG; 

    function initMap() {
        // Initialize the SVG here, AFTER the Google Maps API has fully loaded
        singleMarkerSVG = {
            path: "M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z",
            fillColor: colorPrimary,
            fillOpacity: 1,
            strokeWeight: 2,
            strokeColor: "#ffffff",
            scale: 1.6,
            anchor: new google.maps.Point(12, 22),
        };

        geocoder = new google.maps.Geocoder();
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: userLat, lng: userLon },
            zoom: 11,
            styles: [
                { featureType: "poi", stylers: [{ visibility: "off" }] },
                { featureType: "transit", stylers: [{ visibility: "off" }] }
            ],
            mapTypeControl: false,
            streetViewControl: false,
            gestureHandling: 'greedy'
        });

        const input = document.getElementById('address');
        if (input && google.maps.places) {
            autocomplete = new google.maps.places.Autocomplete(input, {
                fields: ['geometry', 'formatted_address'],
                componentRestrictions: { country: 'DE' }
            });
            autocomplete.addListener('place_changed', executeSearch);
        }

        // Add UI listeners
        document.getElementById('filter-status').addEventListener('change', applyFilters);
        document.getElementById('filter-product').addEventListener('change', applyFilters);
        document.getElementById('liveSearch').addEventListener('input', applyFilters);
        document.getElementById('address').addEventListener('keydown', e => { if(e.key === 'Enter') executeSearch(); });

        fetchData();
    }

    function executeSearch() {
        const addressVal = document.getElementById('address').value.trim();
        const radius = parseFloat(document.getElementById('radius').value || '10');

        if (!addressVal) {
            fetchData(); // Load all if empty
            if(searchCircle) searchCircle.setMap(null);
            return;
        }

        geocoder.geocode({ address: addressVal, componentRestrictions: { country: 'DE' } }, (results, status) => {
            if (status === 'OK' && results[0]) {
                const loc = results[0].geometry.location;
                userLat = loc.lat();
                userLon = loc.lng();
                
                // Draw circle
                if(!searchCircle) {
                    searchCircle = new google.maps.Circle({
                        strokeColor: colorPrimary, strokeOpacity: 0.8, strokeWeight: 2,
                        fillColor: colorPrimary, fillOpacity: 0.1,
                        clickable: false
                    });
                }
                searchCircle.setCenter(loc);
                searchCircle.setRadius(radius * 1000); // km to meters
                searchCircle.setMap(map);
                
                map.panTo(loc);
                map.fitBounds(searchCircle.getBounds());

                fetchData(userLat, userLon, radius);
            } else {
                alert('Adresse nicht gefunden.');
            }
        });
    }

    function fetchData(lat = null, lon = null, radius = null) {
        document.getElementById('result-list').innerHTML = '<div class="text-center p-4 text-muted">Lade Daten...</div>';
        
        let url = '/leads-nearby';
        if (lat && lon && radius) url += `?lat=${lat}&lon=${lon}&radius=${radius}`;

        fetch(url).then(r => r.json()).then(data => {
            window.lastResults = Array.isArray(data) ? data : [];
            populateProductsDropdown(window.lastResults);
            buildMarkersAndList(window.lastResults);
        }).catch(err => {
            console.error(err);
            document.getElementById('result-list').innerHTML = '<div class="text-center p-4 text-danger">Fehler beim Laden.</div>';
        });
    }

    function buildMarkersAndList(items) {
        // Clear old
        window.allMarkers.forEach(m => m.marker.setMap(null));
        window.allMarkers = [];
        if(currentInfoWindow) currentInfoWindow.close();

        items.forEach(item => {
            if (!item.lat || !item.lon) return;
            const position = { lat: parseFloat(item.lat), lng: parseFloat(item.lon) };

            const marker = new google.maps.Marker({
                position, 
                map, 
                icon: singleMarkerSVG
            });

            const contentStr = `
                <div class="custom-info-window">
                    <h4>${item.customer_name || ''} ${item.customer_lastname || ''}</h4>
                    <p><i class="feather icon-map-pin"></i> ${item.full_address || 'Keine Adresse'}</p>
                    <div style="margin-bottom:12px;">${buildBadges(item.product_statuses)}</div>
                    <a href="/new_lead_profile/${item.customer_id}" target="_blank" class="btn">Kundenprofil öffnen</a>
                </div>
            `;
            const infoWindow = new google.maps.InfoWindow({ content: contentStr });

            marker.addListener('click', () => {
                if(currentInfoWindow) currentInfoWindow.close();
                infoWindow.open(map, marker);
                currentInfoWindow = infoWindow;
            });

            window.allMarkers.push({ marker, item, infoWindow });
        });

        applyFilters(); // Renders the list & charts based on current filter states
    }

    function applyFilters() {
        const textFilter = document.getElementById('liveSearch').value.toLowerCase();
        const statusFilter = document.getElementById('filter-status').value.toLowerCase();
        const productFilter = document.getElementById('filter-product').value.toLowerCase();
        
        const listEl = document.getElementById('result-list');
        listEl.innerHTML = '';
        
        let visibleItems = [];
        
        window.allMarkers.forEach(obj => {
            const { item, marker, infoWindow } = obj;
            const strSearch = `${item.customer_name} ${item.customer_lastname} ${item.full_address}`.toLowerCase();
            const strStatus = (item.product_statuses || '').toLowerCase();

            let matchText = !textFilter || strSearch.includes(textFilter);
            let matchStatus = !statusFilter || strStatus.includes(`(${statusFilter})`);
            let matchProduct = !productFilter || strStatus.includes(productFilter);

            let isVisible = matchText && matchStatus && matchProduct;

            marker.setMap(isVisible ? map : null);
            if (!isVisible && currentInfoWindow === infoWindow) {
                currentInfoWindow.close();
            }

            if (isVisible) {
                visibleItems.push(item);
                
                // Build List Item
                const row = document.createElement('div');
                row.className = 'oc-item-row';
                row.innerHTML = `
                    <div class="oc-cell"><div class="oc-stat-icon muted" style="width:40px;height:40px;"><i class="feather icon-user"></i></div></div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Kunde</div>
                        <div class="oc-ttl">${item.customer_name || ''} ${item.customer_lastname || ''}</div>
                        <div class="oc-subt">#${item.customer_id}</div>
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Adresse</div>
                        <div class="oc-subt"><i class="feather icon-map-pin"></i> ${item.full_address || '—'}</div>
                    </div>
                    <div class="oc-cell">
                        <div class="oc-cell-title">Produkte</div>
                        ${buildBadges(item.product_statuses)}
                    </div>
                    <div class="oc-cell" style="text-align:right;">
                        <a href="/new_lead_profile/${item.customer_id}" target="_blank" class="oc-btn oc-btn-accent" style="height:34px;font-size:12px;padding:0 12px;">Profil</a>
                    </div>
                `;
                
                row.addEventListener('click', (e) => {
                    if(e.target.tagName === 'A') return;
                    map.panTo(marker.getPosition());
                    map.setZoom(15);
                    if(currentInfoWindow) currentInfoWindow.close();
                    infoWindow.open(map, marker);
                    currentInfoWindow = infoWindow;
                });
                
                listEl.appendChild(row);
            }
        });

        if(visibleItems.length === 0) {
            listEl.innerHTML = '<div class="text-center p-4 text-muted">Keine Ergebnisse für diese Filter.</div>';
        }

        document.getElementById('count').innerText = visibleItems.length;
        updateCharts(visibleItems);
    }

    // Chart logic & helpers
    function updateCharts(items) {
        const products = {};
        const stages = { 'Angebot': 0, 'Abschluss': 0, 'Projekt': 0, 'Anfrage/Sonstige': 0 };

        items.forEach(it => {
            if(!it.product_statuses) return;
            it.product_statuses.split(',').forEach(p => {
                const match = p.trim().match(/(.+?)\s*\((.+?)\)/);
                if(match) {
                    const name = match[1].trim();
                    const st = match[2].toLowerCase();
                    products[name] = (products[name] || 0) + 1;
                    
                    if(st.includes('offer')) stages['Angebot']++;
                    else if(st.includes('deal')) stages['Abschluss']++;
                    else if(st.includes('project')) stages['Projekt']++;
                    else stages['Anfrage/Sonstige']++;
                }
            });
        });

        // Top 5 Products
        const sortedProducts = Object.entries(products).sort((a,b)=>b[1]-a[1]).slice(0,5);

        if(productChart) productChart.destroy();
        productChart = new Chart(document.getElementById('topProductsBarChart'), {
            type: 'bar',
            data: {
                labels: sortedProducts.map(x=>x[0]),
                datasets: [{ data: sortedProducts.map(x=>x[1]), backgroundColor: colorPrimary, borderRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });

        if(stageChart) stageChart.destroy();
        stageChart = new Chart(document.getElementById('stagePieChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stages),
                datasets: [{ data: Object.values(stages), backgroundColor: [colorPrimary, colorAccent, '#c0d8ea', '#f3f4f6'] }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
        });
    }

    function populateProductsDropdown(items) {
        const select = document.getElementById('filter-product');
        const set = new Set();
        items.forEach(it => {
            if(!it.product_statuses) return;
            it.product_statuses.split(',').forEach(p => {
                const match = p.trim().match(/(.+?)\s*\(/);
                if(match) set.add(match[1].trim());
            });
        });
        
        select.innerHTML = '<option value="">Alle Produkte</option>';
        Array.from(set).sort().forEach(p => {
            const opt = document.createElement('option');
            opt.value = p.toLowerCase();
            opt.innerText = p;
            select.appendChild(opt);
        });
    }

    function buildBadges(raw) {
        if (!raw) return '<span class="oc-badge">Keine</span>';
        return raw.split(',').map(s => {
            const tok = s.trim();
            const st = tok.toLowerCase();
            let c = 'primary';
            if(st.includes('deal') || st.includes('project')) c = 'accent';
            return `<span class="oc-badge ${c}">${tok.replace(/offer/i,'Angebot').replace(/deal/i,'Abschluss').replace(/project/i,'Projekt')}</span>`;
        }).join('');
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places&callback=initMap" async defer></script>
@endpush
@endonce