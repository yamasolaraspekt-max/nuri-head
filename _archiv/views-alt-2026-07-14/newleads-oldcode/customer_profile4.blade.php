<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/feather-icons"></script>
  <style>
    body { height: 100vh; overflow: hidden; display: flex; flex-direction: column; }
    .customer-nav {
      background-color: #2d3e4f;
      padding: 0.5rem 1rem;
      color: #fff;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    .customer-nav-top {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .customer-nav-title {
      font-weight: bold;
      font-size: 1rem;
    }
    .customer-nav-icons {
      display: flex;
      gap: 1rem;
      align-items: center;
    }
    .customer-nav-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 2rem;
      color: #dee2e6;
      font-size: 0.9rem;
    }
    .customer-nav-tabs {
      margin-top: 0.5rem;
    }
    .layout { display: flex; flex: 1; overflow: hidden; }
    .customerSidebar { background-color: #2d3e4f; height: 100%; overflow-y: auto; padding: 1rem; width: 300px; color: #fff; transition: width 0.3s ease; }
    .customerSidebar.minimized { width: 60px; padding: 1rem 0.3rem; }
    .customerSidebar.minimized .text, .customerSidebar.minimized .sub-nav, .customerSidebar.minimized .customer-address, .customerSidebar.minimized .object-address, .customerSidebar.minimized .customer-summary { display: none !important; }
    .main-content { flex: 1; padding: 1rem; overflow-y: auto; transition: all 0.3s ease; background-color: #fff; }
    .customerSidebar.minimized + .main-content { margin-left: 60px; }
    .right-panel { width: 25%; background-color: #f1f3f5; padding: 1rem; overflow-y: auto; }
    .project-link { cursor: pointer; background: #fff; margin-bottom: 0.5rem; padding: 0.5rem 1rem; border-radius: 0.4rem; display: flex; justify-content: space-between; align-items: center; color: #000; }
    .project-link:hover { background-color: #e2e6ea; }
    .status-badge { font-size: 0.75rem; padding: 0.2rem 0.5rem; border-radius: 0.4rem; }
    .bg-lead { background-color: #74b2d4; color: #fff; }
    .bg-planung { background-color: #95c120; color: #fff; }
    .bg-stopp { background-color: #ff5733; color: #fff; }
    .sub-nav { display: none; margin-left: 1rem; }
    .sub-nav.show { display: block; }
    .sub-nav button { background: none; border: none; padding: 0.4rem 0.5rem; width: 100%; text-align: left; color: #fff; font-size: 0.9rem; }
    .sub-nav button:hover { background-color: #3a4b5d; border-radius: 0.3rem; }
    .object-header { cursor: pointer; padding: 0.5rem; display: flex; align-items: center; gap: 0.5rem; border-radius: 0.3rem; transition: background-color 0.2s ease; }
    .object-header:hover { background-color: #3a4b5d; }
    .object-address { font-size: 0.75rem; color: #dee2e6; margin-left: 2rem; }
    .minimize-btn { background: none; border: none; color: #fff; font-size: 1rem; margin-bottom: 1rem; }
    .minimize-btn:hover { color: #0d6efd; }
    .dashboard-btn { display: block; margin-bottom: 1rem; background: none; border: none; color: #fff; font-size: 1rem; text-align: left; width: 100%; }
    .dashboard-btn:hover { color: #0d6efd; }
  </style>
</head>
<body>

  <div class="customer-nav">
    <div class="customer-nav-top">
      <div class="customer-nav-title">TEST TEST</div>
      <div class="customer-nav-icons">
        <i data-feather="bell"></i>
        <i data-feather="calendar"></i>
        <i data-feather="mail"></i>
      </div>
    </div>
    <div class="customer-nav-info">
      <div><i data-feather="external-link"></i></div>
      <div>
        Beerenpfad 6<br>
        61350 Bad Homburg vor der Höhe
      </div>
      <div>
        2342<br>
        234
      </div>
      <div>Quelle:</div>
      <div></div>
    </div>
    <ul class="nav nav-tabs customer-nav-tabs">
      <li class="nav-item">
        <a class="nav-link active" aria-current="page" href="#">Notizen</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Details</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">Verlauf</a>
      </li>
    </ul>
  </div>


  <div class="layout">  
      <div class="customerSidebar" id="customerSidebar">
        <button class="minimize-btn" onclick="togglecustomerSidebar()">
          <i data-feather="chevrons-left"></i>
        </button>
        <button class="dashboard-btn" onclick="showDashboard()">
          <i data-feather="grid"></i> <span class="text">Dashboard</span>
        </button>
        <div class="customer-summary">
          <strong>Max Müller</strong>
          <div class="customer-address">max@example.com<br>Hauptstraße 1, 12345 Berlin</div>
        </div>
        <div class="object-section">
          <div class="object-header" onclick="toggleObject('object1')">
            <i data-feather="home"></i><span class="text">Einfamilienhaus</span>
          </div>
          <div class="object-address">Musterstraße 10, Berlin</div>
          <div id="object1" class="product-list">
            <div class="project-link" onclick="toggleProduct('product1')">
              <span><i data-feather="sun"></i><span class="text"> Photovoltaik</span></span>
              <span class="status-badge bg-planung">Planung</span>
            </div>
            <div id="product1" class="sub-nav show">
              <button><i data-feather="message-circle"></i> Kommunikation</button>
              <button><i data-feather="user"></i> Profil</button>
              <button><i data-feather="tool"></i> Projekte</button>
              <button><i data-feather="file-text"></i> Rechnungen</button>
              <button><i data-feather="shopping-cart"></i> Produkte</button>
              <button><i data-feather="dollar-sign"></i> Umsatz</button>
              <button><i data-feather="credit-card"></i> Bonität</button>
              <button><i data-feather="settings"></i> Wartung</button>
              <button><i data-feather="alert-triangle"></i> Notdienst</button>
              <button><i data-feather="bookmark"></i> Tickets</button>
              <button><i data-feather="briefcase"></i> Dienstleistungen</button>
              <button><i data-feather="file"></i> Angebote</button>
              <button><i data-feather="package"></i> Aufträge</button>
              <button><i data-feather="calendar"></i> Kalender</button>
              <button><i data-feather="star"></i> Bewertungen</button>
              <button><i data-feather="book-open"></i> Historie</button>
            </div>
          </div>
        </div>
      </div>
 
      <div class="main-content" id="mainContent">
        <h3>Customer Overview</h3>
        <div id="dashboardContent">
          <div class="row g-4">
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Projekte</h5>
                  <p class="card-text">3 Aktive, 2 Abgeschlossen</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Umsatz</h5>
                  <p class="card-text">Gesamt: 80.000 €</p>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Zeitleiste</h5>
                  <p class="card-text">Start: 01.01.2024<br>Ende: 15.04.2025</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> 

      <div class="right-panel">
        <h5>Umsatz Übersicht</h5>
        <p><small><i data-feather="home"></i> Einfamilienhaus</small></p>
        <div>
          <button class="btn btn-sm btn-outline-secondary mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#progressPanel">Zeige Fortschritte</button>
          <div id="progressPanel" class="collapse">
            <div class="mb-2">
              Photovoltaik <div class="progress"><div class="progress-bar" style="width: 65%">65%</div></div>
            </div>
            <div class="mb-2">
              Wärmepumpe <div class="progress"><div class="progress-bar bg-danger" style="width: 30%">30%</div></div>
            </div>
            <div class="mb-2">
              Batterie <div class="progress"><div class="progress-bar bg-info" style="width: 50%">50%</div></div>
            </div>
          </div>
        </div>
      </div> 
</div>


  <script>
    feather.replace();
    function togglecustomerSidebar() {
      const customerSidebar = document.getElementById('customerSidebar');
      const main = document.getElementById('mainContent');
      customerSidebar.classList.toggle('minimized');
      main.classList.toggle('expanded');
    }
    function toggleObject(id) {
      const target = document.getElementById(id);
      const isVisible = target.style.display === 'block';
      document.querySelectorAll('.product-list').forEach(el => el.style.display = 'none');
      target.style.display = isVisible ? 'none' : 'block';
      document.getElementById('customerSidebar').classList.remove('minimized');
    }
    function toggleProduct(id) {
      const target = document.getElementById(id);
      const isVisible = target.classList.contains('show');
      document.querySelectorAll('.sub-nav').forEach(el => el.classList.remove('show'));
      if (!isVisible) target.classList.add('show');
    }
    function showDashboard() {
      const main = document.getElementById('mainContent');
      const dashboard = document.getElementById('dashboardContent');
      if (dashboard) main.innerHTML = dashboard.outerHTML;
    }
  </script>
</body>
</html>
