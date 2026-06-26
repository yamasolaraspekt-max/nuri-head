<!DOCTYPE html>
<html>
<head>
  <title>Solar Panel Rooftop Planner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body, html {
      height: 100%;
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .container {
      display: flex;
      height: 100vh;
    }
    #map {
      flex: 0 0 75%;
      height: 100%;
    }
    .sidebar {
      flex: 0 0 25%;
      padding: 20px;
      background: #f4f4f4;
    }
    .sidebar h2 {
      margin-bottom: 10px;
    }
    .info p {
      margin: 6px 0;
    }
    #install-btn {
      margin-top: 15px;
      padding: 10px;
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      width: 100%;
    }
    #search-box {
      margin-bottom: 10px;
    }
    #search-box input {
      width: 100%;
      padding: 10px;
      font-size: 16px;
    }
  </style>
</head>
<body>
  <div class="container">
    <div id="map"></div>
    <div class="sidebar">
      <div id="search-box">
        <input id="autocomplete" type="text" placeholder="Enter postcode or address" />
      </div>
      <h2>Solar Panel Rooftop Planner</h2>
      <div class="info">
        <p><strong>Roof Area:</strong> <span id="roof-area">0</span> m²</p>
        <p><strong>Number of Panels:</strong> <span id="panel-count">0</span></p>
        <p><strong>Solar Radiation:</strong> <span id="solar-radiation">--</span> kWh/m²/day</p>
        <p><strong>Estimated Output:</strong> <span id="estimated-output">--</span> kW/day</p>
        <button id="install-btn">Simulate Installation</button>
      </div>
    </div>
  </div>

  <script>
    let map, drawingManager, roofPolygon, autocomplete;
    let panelRects = [], panelStrings = [];

    function initMap() {
      map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 50.1109, lng: 8.6821 },
        zoom: 18,
        mapTypeId: 'satellite'
      });

      autocomplete = new google.maps.places.Autocomplete(document.getElementById('autocomplete'));
      autocomplete.bindTo("bounds", map);
      autocomplete.addListener("place_changed", () => {
        const place = autocomplete.getPlace();
        if (!place.geometry || !place.geometry.location) return;
        map.setCenter(place.geometry.location);
        map.setZoom(20);
      });

      drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: true,
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: ['polygon']
        },
        polygonOptions: {
          fillColor: '#ffa500',
          fillOpacity: 0.3,
          strokeWeight: 2,
          clickable: false,
          editable: false,
          zIndex: 1
        }
      });
      drawingManager.setMap(map);

      google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
        if (roofPolygon) roofPolygon.setMap(null);
        panelRects.forEach(r => r.setMap(null));
        panelStrings.forEach(s => s.setMap(null));

        roofPolygon = event.overlay;
        const path = roofPolygon.getPath();
        const area = google.maps.geometry.spherical.computeArea(path).toFixed(1);
        document.getElementById('roof-area').textContent = area;

        const panelCount = Math.floor(area / 1.6);
        document.getElementById('panel-count').textContent = panelCount;

        const center = path.getArray()[0];
        fetchSolarData(center.lat(), center.lng(), panelCount);

        placePanelsInsidePolygon(path, panelCount);
      });
    }

    function placePanelsInsidePolygon(path, panelCount) {
      panelRects = [];
      panelStrings = [];
      const bounds = new google.maps.LatLngBounds();
      path.forEach(p => bounds.extend(p));

      const sw = bounds.getSouthWest();
      const ne = bounds.getNorthEast();
      const latStep = (ne.lat() - sw.lat()) / 10;
      const lngStep = (ne.lng() - sw.lng()) / 5;

      let currentLat = sw.lat() + latStep;
      let panelsPlaced = 0;
      const connectors = [];

      while (currentLat + latStep < ne.lat() && panelsPlaced < panelCount) {
        let currentLng = sw.lng() + lngStep;
        while (currentLng + lngStep < ne.lng() && panelsPlaced < panelCount) {
          const rectBounds = {
            north: currentLat + latStep / 2,
            south: currentLat - latStep / 2,
            east: currentLng + lngStep / 2,
            west: currentLng - lngStep / 2
          };

          const center = new google.maps.LatLng((rectBounds.north + rectBounds.south)/2, (rectBounds.east + rectBounds.west)/2);
          if (google.maps.geometry.poly.containsLocation(center, roofPolygon)) {
            const panelRect = new google.maps.Rectangle({
              bounds: rectBounds,
              map: map,
              fillColor: 'yellow',
              fillOpacity: 0.8,
              strokeWeight: 1
            });
            panelRects.push(panelRect);
            connectors.push(center);
            panelsPlaced++;
          }
          currentLng += lngStep * 1.5;
        }
        currentLat += latStep * 1.5;
      }

      for (let i = 0; i < connectors.length - 1; i++) {
        const line = new google.maps.Polyline({
          path: [connectors[i], connectors[i+1]],
          geodesic: true,
          strokeColor: '#0000ff',
          strokeOpacity: 0.7,
          strokeWeight: 2,
          map: map
        });
        panelStrings.push(line);
      }
    }

    function fetchSolarData(lat, lon, panelCount) {
      const url = `https://power.larc.nasa.gov/api/temporal/daily/point?parameters=ALLSKY_SFC_SW_DWN&community=RE&longitude=${lon}&latitude=${lat}&format=JSON`;

      fetch(url)
        .then(response => response.json())
        .then(data => {
          const dailyData = data.properties.parameter.ALLSKY_SFC_SW_DWN;
          const values = Object.values(dailyData);
          const avg = (values.reduce((a, b) => a + b, 0) / values.length).toFixed(1);
          document.getElementById('solar-radiation').textContent = avg;

          const estimatedOutput = (panelCount * 0.25 * avg).toFixed(1);
          document.getElementById('estimated-output').textContent = estimatedOutput;
        });
    }
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=drawing,geometry,places&callback=initMap" async defer></script>
</body>
</html>
