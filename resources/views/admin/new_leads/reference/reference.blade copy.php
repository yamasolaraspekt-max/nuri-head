@extends('admin.layouts.app')
@section('title')REFERENZE @stop
@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">REFERENZE</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Liste</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                          
            <div class="content-body"> 
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div id="map" style="height: 600px; width: 100%;"></div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-md-6">
                        <label for="address">Adresse</label>
                        <input type="text" id="address" class="form-control" placeholder="Adresse eingeben...">
                    </div>
                    <div class="col-md-3">
                        <label for="radius">Radius (km)</label>
                        <input type="number" id="radius" class="form-control" value="5" min="1">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" onclick="searchNearby()">Suchen</button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h5>Gefundene Einträge: <span id="count">0</span></h5>
                        <ul id="result-list" class="list-group mt-2"></ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>

<script>
let map;
let userLat = 50.1109;
let userLon = 8.6821;

function initMap() {
    geocoder = new google.maps.Geocoder();

    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 50.12, lng: 8.67 },
        zoom: 8,
       
    });

    // Load all customers initially
    fetchNearbyLocations(); // Default loads all
}

function fetchNearbyLocations(lat = null, lon = null, radius = null) {
    const list = document.getElementById("result-list");
    const countElement = document.getElementById("count");

    // Loading state
    countElement.textContent = '...';
    list.innerHTML = '<li class="list-group-item">🔄 Lade Daten...</li>';

    // Build URL
    let url = `/leads-nearby`;
    if (lat && lon && radius) {
        url += `?lat=${lat}&lon=${lon}&radius=${radius}`;
    }

    fetch(url)
        .then(res => {
            if (!res.ok) throw new Error("❌ Serverantwort fehlgeschlagen.");
            return res.json();
        })
        .then(data => {
            if (!Array.isArray(data)) {
                console.error("❌ Unerwartetes JSON-Format:", data);
                return;
            }

            // Clear old circle
            if (window.searchCircle) window.searchCircle.setMap(null);

            // Draw radius circle
            if (lat && lon && radius) {
                window.searchCircle = new google.maps.Circle({
                    strokeColor: "#4285F4",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#4285F4",
                    fillOpacity: 0.15,
                    map,
                    center: { lat, lng: lon },
                    radius: radius * 1000
                });

                map.setCenter({ lat, lng: lon });
                map.setZoom(13);
            }

            list.innerHTML = '';
            countElement.textContent = data.length;

            const markers = [];
            let matchedMarker = null;

            data.forEach(item => {
                if (!item.lat || !item.lon) return;

                const position = {
                    lat: parseFloat(item.lat),
                    lng: parseFloat(item.lon),
                };

                const iconUrl = getMarkerIcon(item.product_statuses || '');
                const marker = new google.maps.Marker({
                    position,
                    map,
                    icon: {
                        url: iconUrl,
                        scaledSize: new google.maps.Size(36, 36),
                        anchor: new google.maps.Point(18, 36),
                    },
                });

                // Build badges
                const products = item.product_statuses
                    ? item.product_statuses.split(',').map(p => {
                        const match = p.match(/(.+?)\s*\((.+?)\)/);
                        const name = match?.[1]?.trim() ?? p.trim();
                        const stage = match?.[2]?.trim() ?? '';
                        const germanStage = translateStatusToGerman(stage);
                        return `<span class="badge bg-info me-1">${name} (${germanStage})</span>`;
                    }).join('')
                    : '<span class="badge bg-secondary">Keine</span>';

                const infoWindow = new google.maps.InfoWindow({
                    content: `
                        <div style="min-width:200px;">
                            <strong>${item.customer_name ?? ''} ${item.customer_lastname ?? ''}</strong><br>
                            <small>${item.full_address ?? 'Keine Adresse'}</small><br>
                            <div class="mt-1"><b>Produkte:</b><br>${products}</div>
                            <a href="/new_lead_profile/${item.customer_id}" target="_blank" class="btn btn-sm btn-outline-primary mt-2 w-100">
                                👤 Profil ansehen
                            </a>
                        </div>
                    `

                });

                marker.addListener("click", () => {
                    infoWindow.open(map, marker);
                });

                markers.push(marker);

                const distanceLabel = (lat && lon)
                    ? `${haversineDistance(lat, lon, item.lat, item.lon).toFixed(2)} km entfernt`
                    : '–';

                const listItem = document.createElement("li");
                listItem.className = "list-group-item";
                listItem.innerHTML = `
                        <strong>${item.customer_name ?? ''} ${item.customer_lastname ?? ''}</strong><br>
                        <small>${item.full_address ?? ''}</small><br>
                        <div><b>Produkte:</b><br>${products}</div>
                        <span class="badge bg-primary mt-1">${distanceLabel}</span><br>
                        <a href="/new_lead_profile/${item.customer_id}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            👤 Profil ansehen
                        </a>
                    `;

                list.appendChild(listItem);

                // Match address marker for animation
                if (lat && lon) {
                    const markerKey = `${position.lat.toFixed(4)},${position.lng.toFixed(4)}`;
                    const searchKey = `${lat.toFixed(4)},${lon.toFixed(4)}`;
                    if (markerKey === searchKey) {
                        matchedMarker = marker;
                    }
                }
            });

            // Animate matched marker
            if (matchedMarker) {
                matchedMarker.setAnimation(google.maps.Animation.BOUNCE);
                map.panTo(matchedMarker.getPosition());
                setTimeout(() => matchedMarker.setAnimation(null), 3000);
            }
        })
        .catch(err => {
            console.error("❌ Fehler beim Abrufen:", err);
            list.innerHTML = '<li class="list-group-item text-danger">❌ Fehler beim Laden der Daten</li>';
            countElement.textContent = '0';
        });
}


function translateStatusToGerman(status) {
    const s = status.toLowerCase();

    if (s.includes("lead")) return "Anfrage";
    if (s.includes("offer")) return "Angebot";
    if (s.includes("deal")) return "Abschluss";
    if (s.includes("project")) return "Projekt";
    if (s.includes("completed")) return "Abgeschlossen";
    if (s.includes("reject") || s.includes("absage")) return "Abgelehnt";

    return status;
}


function getMarkerIcon(statuses) {
    const s = statuses.toLowerCase();
    if (s.includes("lead")) return "/images/pins/blue.png";
    if (s.includes("offer")) return "/images/pins/orange.png";
    if (s.includes("deal")) return "/images/pins/green.png";
    if (s.includes("project")) return "/images/pins/teal.png";
    if (s.includes("completed")) return "/images/pins/gray.png";
    if (s.includes("reject") || s.includes("absage")) return "/images/pins/red.png";
    return "/images/pins/map.png";
}

function searchNearby() {
    const address = document.getElementById('address').value;
    const radius = document.getElementById('radius').value;

    geocodeLatLng(address)
        .then(({ lat, lon }) => {
            userLat = lat;
            userLon = lon;
            fetchNearbyLocations(lat, lon, radius);
        })
        .catch(error => console.error("Geocode fehlgeschlagen:", error));
}

function geocodeLatLng(address) {
    return new Promise((resolve, reject) => {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address }, (results, status) => {
            if (status === "OK" && results[0]) {
                const location = results[0].geometry.location;
                resolve({ lat: location.lat(), lon: location.lng() });
            } else {
                alert("Adresse konnte nicht gefunden werden.");
                reject(status);
            }
        });
    });
}

function haversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = toRad(lat2 - lat1);
    const dLon = toRad(lon2 - lon1);
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function toRad(val) {
    return val * Math.PI / 180;
}
</script>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places&callback=initMap" async defer></script>
<script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>


@endsection