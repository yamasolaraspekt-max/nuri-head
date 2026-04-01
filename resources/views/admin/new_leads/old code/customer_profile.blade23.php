@extends('admin.layouts.app')
@section('title')
KUNDE PROFILE
@endsection

@section('style')
 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">
 <!-- In your main Blade layout (e.g. admin.layouts.app or similar) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">

<style>
    .card-img-top.lazy {
    object-fit: cover;
    height: 180px;
    width: 100%;
}
</style>

<style>
.phase-table th,
.phase-table td {
    vertical-align: middle;
    font-size: 14px;
}

.phase-table .bg-success {
    background-color: #cce5b1 !important;
    color: #2c3e50;
}

.phase-table td {
    padding: 8px 10px;
}
</style>


<!-- Kanban  -->
   <style>
        .kanban-board {
        overflow-x: auto;
        padding-bottom: 10px;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .kanban-board .kanban-header,
    .kanban-board .kanban-body {
        display: flex;
        flex-wrap: nowrap;
    }

    .kanban-column {
        width: 320px;
        min-width: 300px;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .kanban-title {
        background: #8fc73e;
        color: white;
        font-weight: bold;
        text-align: center;
        padding: 12px;
        border-radius: 6px 6px 0 0;
        font-size: 15px;
    }

    .kanban-dropzone {
        min-height: 350px;
        background-color: #f9fafb;
        padding: 10px;
        border: 1px dashed #ccc;
        border-top: none;
        border-radius: 0 0 6px 6px;
        transition: background 0.3s ease;
    }

    .kanban-dropzone:hover {
        background-color: #f1f5f9;
    }

    .kanban-card {
    background: #fff;
    border-left: 4px solid transparent; /* dynamically updated in Blade */
    transition:
        border-color 0.2s ease,
        transform 0.2s ease,
        box-shadow 0.2s ease;
    margin-bottom: 10px;
    border-radius: 8px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.07);
    position: relative;
    cursor: grab;
}

/* On hover: slightly lift the card */
    .kanban-card:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }

    /* Optional: fade "junk" cards */
    .kanban-card.junk {
        opacity: 0.6;
        border-left-color: #d1d5db;
        cursor: not-allowed;
    }

    /* Responsive support */
    @media (max-width: 576px) {
        .kanban-card {
            padding: 12px;
        }
    }

    .kanban-card .disable-pointer {
        pointer-events: none;
    }

    .kanban-card .rounded-circle {
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .kanban-card i {
        opacity: 0.6;
        transition: opacity 0.2s ease;
        cursor: pointer;
    }

    .kanban-card i:hover {
        opacity: 1;
    }

    /* Badge in top-right corner */
    .kanban-card .initial-badge {
        position: absolute;
        top: 8px;
        right: 10px;
        background-color: #f1f5f9;
        color: #666;
        font-weight: bold;
        font-size: 11px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Responsive fallback */
    @media (max-width: 576px) {
        .kanban-column {
            width: 90vw;
        }

        .kanban-card {
            padding: 12px;
        }

        .kanban-title {
            font-size: 14px;
        }
    }

   </style>

   <style>

    #note-scroll-wrapper {
        display: flex;
         flex-direction:column !important;
        height: 100%;
        padding-bottom: 60px; /* space for composer */
        scroll-behavior: smooth;
    }

    .note-card .card-body {
    
        background-color: #f1f0f0;
    }

    .note-card .dropdown-menu {
        font-size: 0.875rem;
    }

    .note-card img {
        object-fit: cover;
    }

    #note-container {
        max-height: 80%;
    }


    .modal-body {
        padding: 0;
        overflow: hidden;
    }

    .note-composer {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 500px;
        max-height: 0;
        overflow: hidden;
        padding: 0;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 1050;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .note-composer.open {
        max-height: 300px;
        padding: 1rem;
    }

    .note-composer .submit-wrapper {
        display: none;
    }

    .note-composer.open .submit-wrapper {
        display: block;
    }


    .note-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1049;
    }

    #note-list {
    display: flex;
    flex-direction: column;
    width: 100%;
}


.note-reply {
    transition: opacity 0.4s ease;
}

.reply-container .card {
    background: #f8f9fa;
    border-left: 2px solid #ccc;
    margin-top: 0.5rem;
}

  
.animated-reply {
    animation: pulse 0.4s ease;
}

.reply-wrapper {
    overflow-anchor: auto;
    scroll-margin-bottom: 80px;
}


@keyframes pulse {
    0%   { transform: scale(1); background-color: #e7f6ff; }
    50%  { transform: scale(1.015); background-color: #d4f1ff; }
    100% { transform: scale(1); background-color: inherit; }
}

   </style>

   <style>
    .panel-toggle-btn {
        position: absolute;
        top: -40px;
        z-index: 9999;
        background: white;
        border: 1px solid #ccc;
        padding: 6px 10px;
        border-radius: 4px;
        color: #8fc73e;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
   </style>
 <style>
.delete-btn {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #dc3545;
    border: none;
    color: white;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    font-size: 13px;
    cursor: pointer;
    padding: 0;
    line-height: 20px;
    text-align: center;
}
.fade-in {
    opacity: 1;
}
.fade-out {
    opacity: 0 !important;
    transition: opacity 0.4s ease-out;
}
.card {
    box-shadow: 0 0 !important;
}

.main-fullscreen {
    width: 100% !important;
    flex: 1 1 auto;
    padding: 1rem !important;
}

.main-fullscreen-mode .customerSidebar,
.main-fullscreen-mode .right-panel {
    display: none !important;
}

.main-fullscreen-mode .contentStation {
    width: 100% !important;
    flex: 1 1 auto !important;
    max-width: 100% !important;
    padding: 1rem !important;
}
.contentStation {
    transition: all 0.3s ease;
}


.right-panel.fullscreen {
    position: relative;
    width: 100% !important;
    max-width: 100%;
    flex: 1 1 auto;
    z-index: 999;
    background: #fff;
    transition: all 0.3s ease;
}

.right-panel.fullscreen .note-scroll-wrapper {
    height: calc(100vh - 120px); /* adjust if needed */
    overflow-y: auto;
}


.contentStation.expanded {
    flex: 1 1 auto;
    width: auto !important;
}


.customerSidebar {
  width: 300px;
  flex-shrink: 0;
  transition: width 0.3s ease;
}

.customerSidebar.minimized {
  width: 60px;
  padding: 1rem 0.5rem;
}

.layout {
  overflow: hidden;
}

 

.contentStation {
  flex: 1 1 auto;           /* 👈 ALLOW it to grow */
  min-width: 0;             /* 👈 Prevent overflow */
  background: #ccc;
  transition: all 0.3s ease;
}

.right-panel {
  width: 360px;
  flex-shrink: 0;
  background: #f6f6f6;
  transition: all 0.3s ease;
  border-left: 1px solid #ccc;
}

</style>

<style>
    #deletedNotesModalBody .card-body  {
            background:white !important;
    }

    #deletedNotesModalBody .btn-success  {
            margin-bottom:0 !important;
    }
</style>

<style>
    /* Color when a collapse is expanded */
    .card .collapse.show {
        background-color:rgb(255, 255, 255);
    }

    /* Optional: color the header too when open */
    .card-header.active-stage {
        background-color: #c0d8ea !important;
        color: black !important;
    }

    #phaseSidebar .phase-sidebar-body .card-header  {
        padding:1rem !important;
    }

    #phaseSidebar .phase-sidebar-header h5 { 
            font-size: 24px;
            font-weight: bold;
            color: white;

    }


    .phase-sidebar {
    position: fixed;
    top: 0;
    right: -100%;
    width: 80%;
    max-width: 1370px;
    height: 100%;
    background: #fff;
    box-shadow: -4px 0 8px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
    }

    .phase-sidebar.open {
    right: 0;
    }

    .phase-sidebar-header {
    padding: 1rem;
    background: #2c3e4f;
    display: flex;
    justify-content: space-between;
    align-items: center;
    }

    .phase-sidebar-body {
    padding: 1rem;
    overflow-y: auto;
    flex: 1;
    }

    .close-btn {
    background: transparent;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    }

    #phaseProductInitial { 
        padding: 10px;
        font-size: 20px;
        border-radius: 50%;
        color: #cfe09b;
        font-weight: bold;
        background-color: #ffffff !important;
        width: 50px;
        height: 50px;
        place-content: center;
    }


    .upload-icons,
    .file-icons {
    font-size: 22px;
    color: #cfdf9b;
    transition: 0.2s ease;
    }

    .upload-icons:hover,
    .file-icons:hover {
    color: #dc3545; /* red on hover */
    transform: scale(1.1);
    cursor: pointer;
    }

    .entry-col .badge {
        font-size: 10px;
        padding: 2px 6px;
        margin-bottom: 2px;
    }

    .badge-dark {
    background-color: #343a40;
    color: #fff;
    font-weight: 600;
    font-size: 11px;
}


#next_phase_station button {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}


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
                        <h2 class="content-header-title float-left mb-0">KUNDENPROFIL</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a></li>
                                <li class="breadcrumb-item active">{{ $customer->name }} {{ $customer->lastname }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <!-- Insert the full HTML layout from above here (customer-nav, layout, sidebar, main-content, right-panel) -->
            @include('admin.new_leads.layouts.profile')
        </div>
    </div>
</div>
@endsection

@section('script')

 
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>
<script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"
  async defer></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById('customerSidebar');
        const main = document.getElementById('mainContent');
        let sidebarManuallyExpanded = false;

        // ✅ Force-close all collapses on initial load
        document.querySelectorAll('.product-list, .sub-nav').forEach(el => {
            el.classList.remove('show');
            el.setAttribute('aria-expanded', 'false');
        });

        function collapseAll() {
            document.querySelectorAll('.product-list').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.sub-nav').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
        }

        function minimizeSidebar() {
            sidebar.classList.add('minimized');
            document.querySelector('.contentStation')?.classList.add('expanded');
            collapseAll();
        }

        function expandSidebar() {
            sidebar.classList.remove('minimized');
            document.querySelector('.contentStation')?.classList.remove('expanded');
            sidebarManuallyExpanded = true;
            feather.replace();
        }

        function autoToggleSidebar() {
            if (!sidebarManuallyExpanded) {
                window.innerWidth < 992 ? minimizeSidebar() : expandSidebar();
            }
        }

        autoToggleSidebar();
        window.addEventListener('resize', autoToggleSidebar);

        window.togglecustomerSidebar = () => {
            sidebar.classList.contains('minimized') ? expandSidebar() : minimizeSidebar();
        };

        document.addEventListener('click', function (e) {
            const shouldExpand =
                e.target.closest('.object-header') ||
                e.target.closest('.project-link') ||
                e.target.closest('.sub-nav button') ||
                e.target.closest('.dashboard-btn');

            if (sidebar.classList.contains('minimized') && shouldExpand) {
                expandSidebar();
            }
        });

        window.toggleObject = (id) => {
            const target = document.getElementById(id);
            if (!target) return;

            const wasOpen = target.classList.contains('show');
            document.querySelectorAll('.product-list').forEach(el => {
                if (el.id !== id) el.classList.remove('show');
            });

            target.classList.toggle('show');

            const isNowOpen = target.classList.contains('show');
            console.log(`[Object] ${id} was ${wasOpen ? 'open' : 'closed'} → now ${isNowOpen ? 'open' : 'closed'}`);

            document.querySelectorAll('.sub-nav').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
        };

        window.toggleProduct = (productKey) => {
            const subNav = document.getElementById(productKey);
            const clicked = document.querySelector(`.project-link[data-product-key="${productKey}"]`);
            const parentId = productKey.match(/product(\d+)_\d+/)?.[1];
            const parentObjectList = document.getElementById(`object${parentId}`);

            if (!subNav || !clicked || !parentObjectList) return;

            const wasOpen = subNav.classList.contains('show');

            // Collapse everything
            document.querySelectorAll('.sub-nav').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));

            if (!wasOpen) {
                parentObjectList.classList.add('show');
                subNav.classList.add('show');
                clicked.classList.add('active');
                console.log(`[Product] ${productKey} was closed → now open`);

                // 🔄 Load Notes
                const customerId = clicked.dataset.objectCustomerId;
                const alternativeId = clicked.dataset.objectAlternativeId;
                const productId = clicked.dataset.objectProduct;
                const noteList = document.getElementById('note-list');

                if (noteList && customerId && alternativeId && productId) {
                    noteList.innerHTML = '<div class="text-muted">Lade Notizen...</div>';
                    fetch(`/customer-notes/${customerId}/${alternativeId}/${productId}`)
                        .then(res => res.text())
                        .then(html => {
                            noteList.innerHTML = html;
                            feather.replace();
                            if (typeof initNoteListeners === 'function') initNoteListeners();
                        })
                        .catch(() => {
                            noteList.innerHTML = '<div class="text-danger">Fehler beim Laden.</div>';
                        });
                }
            } else {
                console.log(`[Product] ${productKey} was open → now closed`);
            }
        };

        window.showDashboard = () => {
            const dashboard = document.getElementById('dashboardContent');
            if (main && dashboard) main.innerHTML = dashboard.outerHTML;
        };

        // ✅ Auto-bind click to all project links
        document.querySelectorAll('.project-link[data-product-key]').forEach(link => {
            link.addEventListener('click', function () {
                const productKey = this.dataset.productKey;
                toggleProduct(productKey);
            });
        });
    });
</script>


<!-- Maximize Toggle Buttons  -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mainContentToggle');
        const layout = document.querySelector('.layout');
        const sidebar = document.getElementById('customerSidebar');
        const rightPanel = document.querySelector('.right-panel');
        const content = document.querySelector('.contentStation');
        const icon = btn.querySelector('i');

        btn.addEventListener('click', () => {
            const isFullscreen = layout.classList.toggle('main-fullscreen-mode');

            // Toggle visibility
            sidebar.style.display = isFullscreen ? 'none' : '';
            rightPanel.style.display = isFullscreen ? 'none' : '';
            content.classList.toggle('main-fullscreen', isFullscreen);

            // Change icon class
            icon.classList.remove('icon-maximize-2', 'icon-minimize-2');
            icon.classList.add(isFullscreen ? 'icon-minimize-2' : 'icon-maximize-2');
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('btnToggleRightPanelFullscreen');
        const rightPanel = document.querySelector('.right-panel');
        const mainContent = document.querySelector('.contentStation');
        const sidebar = document.getElementById('customerSidebar');
        const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', () => {
            const isFullscreen = rightPanel.classList.toggle('fullscreen');

            // Toggle visibility of side and main content
            sidebar.style.display = isFullscreen ? 'none' : '';
            mainContent.style.display = isFullscreen ? 'none' : '';

            // Toggle icon
            icon.classList.remove('icon-maximize-2', 'icon-minimize-2');
            icon.classList.add(isFullscreen ? 'icon-minimize-2' : 'icon-maximize-2');
        });
    });
</script>

<!-- Maximize Toggle Buttons  --> 
<script>
    function loadSectionPartial(customer_id, alternative_id, product_id, section) {
        const url = `/customer/partial/${customer_id}/${alternative_id}/${product_id}/${section}`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Fehler beim Laden des Abschnitts');
                return response.text();
            })
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;

                // Optional: load extra sub-sections (partial wrappers)
            })
            .catch(error => {
                document.getElementById('mainContent').innerHTML =
                    `<div class="alert alert-danger">${error.message}</div>`;
            });
    }
    
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Collapse all object sections and product sections by default
        document.querySelectorAll('.product-list').forEach(el => el.classList.remove('show'));
        document.querySelectorAll('.sub-nav').forEach(el => el.classList.remove('show'));
    });
</script>
 
<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        function updateProgressCounts() {
            const steps = [
                { fields: document.querySelectorAll('#step1 input, #step1 select, #step1 textarea'), total: 8, label: 'step1-count' },
                { fields: document.querySelectorAll('#step2 input, #step2 select, #step2 textarea'), total: 6, label: 'step2-count' },
                { fields: document.querySelectorAll('#step3 input, #step3 select, #step3 textarea'), total: 5, label: 'step3-count' },
                { fields: document.querySelectorAll('#step4 input, #step4 select, #step4 textarea'), total: 3, label: 'step4-count' },
                { fields: document.querySelectorAll('#step5 input, #step5 select, #step5 textarea'), total: 3, label: 'step5-count' },
            ];

            steps.forEach(step => {
                let filled = 0;
                step.fields.forEach(field => {
                    if (field.type === 'checkbox' || field.type === 'radio') {
                        if (field.checked) filled++;
                    } else if (field.value.trim() !== '') {
                        filled++;
                    }
                });
                document.getElementById(step.label).innerText = `(${filled}/${step.total})`;
            });
        }

        // Add event listener to all form fields
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.addEventListener('input', updateProgressCounts);
            el.addEventListener('change', updateProgressCounts);
        });

        updateProgressCounts(); // Initial check on page load
    });
</script> -->


<script>
  function showTab(step) {
      document.querySelectorAll('.tab-pane').forEach((pane, idx) => {
          pane.classList.remove('active');
          if (idx === step - 1) pane.classList.add('active');
      });

      document.querySelectorAll('.wizard-step').forEach((stepEl, idx) => {
          stepEl.classList.remove('active');
          if (idx === step - 1) stepEl.classList.add('active');
      });
  }


    function navigateTab(direction) {
        const steps = document.querySelectorAll('.wizard-step');
        let currentIndex = [...steps].findIndex(step => step.classList.contains('active'));
        let nextIndex = currentIndex + direction;
        if (nextIndex >= 0 && nextIndex < steps.length) {
            showTab(nextIndex + 1);
        }
    }

    function updateProgressCounts() {
        const sections = ['object_data', 'roof_info', 'heating_info', 'energy_usage', 'e_mobility'];

        sections.forEach((section, index) => {
            const wrapper = document.getElementById(`${section}_wrapper`);
            if (!wrapper) return;

            const inputs = wrapper.querySelectorAll('input, select, textarea');
            let total = inputs.length;
            let filled = 0;

            inputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.checked) filled++;
                } else if (input.value && input.value.trim() !== '') {
                    filled++;
                }
            });

            document.getElementById(`step${index + 1}-count`).innerText = `(${filled}/${total})`;
        });
    }
      

    function loadFullAlternativeObject(button) {
        const customerId = button.dataset.customerId;
        const alternativeId = button.dataset.alternativeId;
        const productId = button.dataset.productId;

        const url = `/customer/alternative/partials/${customerId}/${alternativeId}/${productId}/objekt`;

        document.getElementById('mainContent').innerHTML = `<div class="text-center py-4">Lade Objektdaten...</div>`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Fehler beim Laden des Objekts');
                return response.text();
            })
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;

                // Optionally re-run icon replacement if you're using Feather icons
                if (typeof feather !== 'undefined') feather.replace();
            })
            .catch(error => {
                document.getElementById('mainContent').innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
            });
    }


</script>
 
<script> 
    document.addEventListener('DOMContentLoaded', function () {
        const electricCarSelect = document.getElementById('electric_car');
        const electricCarPlan = document.getElementById('electric_car_plan');

        if (electricCarSelect) {
            electricCarSelect.addEventListener('change', function () {
                if (this.value === 'Geplant') {
                    electricCarPlan.style.display = 'block';
                } else {
                    electricCarPlan.style.display = 'none';
                }
            });
        }
    }); 
</script>


<!-- Saving the alternative data : -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('submit', function (e) {
        if (e.target.classList.contains('partial-form')) {
            e.preventDefault();

            const form = e.target;
            const section = form.dataset.section;
            const id = form.dataset.id;

            const formData = new FormData(form);
            formData.append('id', id);

            fetch(`/new_lead_profile/alternative/object/save`, {
              method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Fehler beim Speichern von ' + section);
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Gespeichert',
                    text: `Abschnitt "${section}" erfolgreich gespeichert.`
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: error.message
                });
            });
        }
    });
});
</script>



<!-- Requesting the notes:  -->
 
 

<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = '{{ csrf_token() }}';
    const noteList = document.getElementById('note-list');
    const scrollWrapper = document.getElementById('note-scroll-wrapper');
    let offset = 0;
    const searchInput = document.getElementById('searchNote');
 

    // Auto-load first product
    const firstProject = document.querySelector('.project-link');
    if (firstProject) firstProject.click();

    // Handle project selection
    document.querySelectorAll('.project-link').forEach(link => {
        link.addEventListener('click', async function () {
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
            this.classList.add('active');

            const { objectCustomerId, objectAlternativeId, objectProduct } = this.dataset;
            noteList.innerHTML = '<div class="text-muted">Loading notes...</div>';

            try {
                const res = await fetch(`/customer-notes/${objectCustomerId}/${objectAlternativeId}/${objectProduct}`);
                const html = await res.text();
                noteList.innerHTML = html;
                feather.replace();
                initNoteListeners();
 
               // Scroll to top (show latest note first)
                setTimeout(() => {
                    scrollWrapper.scrollTo({ top: 0, behavior: 'smooth' });
                }, 100);

            } catch {
                noteList.innerHTML = '<div class="text-danger">Error loading notes.</div>';
            }
        });
    });

    // Toggle composer visibility
    window.toggleNewNoteArea = () => {
        const composer = document.getElementById('newNoteComposer');
        const backdrop = document.getElementById('noteBackdrop');

        composer.classList.toggle('open');

        if (composer.classList.contains('open')) {
            backdrop.style.display = 'block';
            setTimeout(() => document.getElementById('newNoteText').focus(), 200);
        } else {
            backdrop.style.display = 'none';
        }
    };


    // Submit a new note
        window.submitNote = async () => {
            const active = document.querySelector('.project-link.active');
            if (!active) return alert('No product selected.');

            const input = document.getElementById('newNoteText');
            const composer = document.getElementById('newNoteComposer');
            const text = input.value.trim();

            if (!text) {
                return Swal.fire('Notice', 'Please enter a note.', 'warning');
            }

            const body = {
                customer_id: active.dataset.objectCustomerId,
                alternative_id: active.dataset.objectAlternativeId,
                product_id: active.dataset.objectProduct,
                description: text,
                priority: 'normal',
                color: '#cfe09b'
            };

             

            try {
                const res = await fetch('/customer-notes/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });

                const html = await res.text();
                noteList.insertAdjacentHTML('afterbegin', html);

                feather.replace();
                initNoteListeners();

                input.value = '';
                composer.classList.remove('open');

                

            } catch (err) {
                Swal.fire('Error', 'Failed to save the note.', 'error');
            }
        };

    function isNearBottom(wrapper, threshold = 50) {
        return wrapper.scrollHeight - wrapper.scrollTop - wrapper.clientHeight < threshold;
    }

    searchInput.addEventListener('input', () => {
        const query = searchInput.value.toLowerCase().trim();

        noteList.querySelectorAll('.note-card').forEach(card => {
            const content = card.querySelector('.note-description')?.innerText.toLowerCase() || '';
            const matches = content.includes(query);
            card.style.display = matches ? '' : 'none';
        });
    });

    // Delete note
    window.deleteNote = id => {
        Swal.fire({
            title: 'Are you sure?',
            text: "This note will be deleted!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!'
        }).then(result => {
            if (result.isConfirmed) {
                fetch(`/customer-notes/delete/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrf }
                }).then(() => {
                    document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
                });
            }
        });
    };

    // Reply to note
        window.postReply = (parentId, input) => {
            const text = input.value.trim();
            if (!text) return;

            const btn = input.closest('.input-group').querySelector('button');
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;

            fetch(`/customer-notes/${parentId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = input.closest('.note-card');
                    const wrapper = card.querySelector('.reply-wrapper');

                    // 🆕 Animate-in new reply
                    const temp = document.createElement('div');
                    temp.innerHTML = data.reply;
                    const newReply = temp.firstElementChild;

                    newReply.style.opacity = 0;
                    newReply.style.transition = 'opacity 0.3s ease';

                    wrapper.appendChild(newReply);

                    setTimeout(() => {
                        newReply.style.opacity = 1;
                        newReply.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 10);

                    input.value = '';
                    feather.replace();
                }
            })
            .catch(() => {
                Swal.fire('Fehler', 'Antwort konnte nicht gesendet werden.', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = `Senden`;
            });
        };


        window.deleteReply = function (id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This reply will be permanently deleted.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/customer-notes/reply/${id}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(() => {
                        document.querySelector(`.reply-item[data-id="${id}"]`)?.remove();
                    });
                }
            });
        };


       

        function createReplyContainer(card) {
            const container = document.createElement('div');
            container.className = 'reply-container ms-4 mt-2';

            const cardBody = card.querySelector('.card-body');
            const inputGroup = cardBody.querySelector('.input-group');

            if (inputGroup) {
                cardBody.insertBefore(container, inputGroup);
            } else {
                cardBody.appendChild(container);
            }

            return container;
        }


    // Inline edit logic
    function initNoteListeners() {
        document.querySelectorAll('.inline-edit-description').forEach(input => {
            input.oninput = () => {
                const id = input.dataset.id;
                fetch(`/customer-notes/inline-update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ field: 'description', value: input.value })
                });
            };
        });

        document.addEventListener('click', function (e) {
            const item = e.target.closest('.priority-item');
            if (!item) return;

            const { id, value } = item.dataset;

            fetch(`/customer-notes/inline-update/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ field: 'priority', value })
            });

            document.querySelectorAll(`.priority-item[data-id="${id}"]`).forEach(i =>
                i.classList.remove('active', 'fw-bold', 'text-primary', 'text-primary', 'text-danger')
            );

            item.classList.add('active', 'fw-bold');
            item.classList.add(value === 'low' ? 'text-primary' : value === 'high' ? 'text-danger' : 'text-primary');
        });

        document.querySelectorAll('.inline-edit-color').forEach(input => {
            input.oninput = () => {
                const id = input.dataset.id;
                const value = input.value;
                const card = document.querySelector(`.note-card[data-id="${id}"]`);
                if (card) card.style.borderRightColor = value;

                fetch(`/customer-notes/inline-update/${id}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({ field: 'color', value })
                });
            };
        });
    }

    window.editReply = function (replyId) {
        const card = document.querySelector(`.reply-item[data-id="${replyId}"]`);
        if (!card) return;

        const textDiv = card.querySelector('.reply-text');
        const oldText = textDiv?.textContent?.trim();
        console.log('Found textDiv:', textDiv);
        console.log('Old text:', oldText);

        Swal.fire({
            title: 'Reply bearbeiten',
            input: 'textarea',
            inputLabel: 'Neue Nachricht',
            inputValue: oldText,
            inputAttributes: {
                'aria-label': 'Neue Nachricht eingeben'
            },
            showCancelButton: true,
            confirmButtonText: 'Aktualisieren',
            cancelButtonText: 'Abbrechen',
            showLoaderOnConfirm: true,
            preConfirm: (newText) => {
                if (!newText.trim()) {
                    Swal.showValidationMessage('Antwort darf nicht leer sein.');
                    return false;
                }

                return fetch(`/customer-notes/reply/${replyId}/update`, {
                    method: 'POST', // Use POST if PUT fails on your server
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ description: newText.trim() })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Fehler beim Speichern');
                    return res.json();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed && result.value.success) {
                textDiv.innerHTML = result.value.updated_description;
                Swal.fire('✅ Aktualisiert', '', 'success');
            }
        });
    }

    window.editNote = function (noteId) {
        const card = document.querySelector(`.note-card[data-id="${noteId}"]`);
        if (!card) return;

        const textDiv = card.querySelector('.note-description');
        const oldText = textDiv?.textContent?.trim();

        Swal.fire({
            title: 'Notiz bearbeiten',
            input: 'textarea',
            inputLabel: 'Neue Nachricht',
            inputValue: oldText,
            inputAttributes: {
                'aria-label': 'Neue Nachricht eingeben'
            },
            showCancelButton: true,
            confirmButtonText: 'Aktualisieren',
            cancelButtonText: 'Abbrechen',
            showLoaderOnConfirm: true,
            preConfirm: (newText) => {
                if (!newText.trim()) {
                    Swal.showValidationMessage('Text darf nicht leer sein.');
                    return false;
                }

                return fetch(`/customer-notes/${noteId}/update`, {
                    method: 'POST', // or PUT if you prefer
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ description: newText.trim() })
                })
                .then(res => {
                    if (!res.ok) throw new Error('Fehler beim Speichern');
                    return res.json();
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then(result => {
            if (result.isConfirmed && result.value.success) {
                textDiv.textContent = result.value.updated_description;
                Swal.fire('✅ Aktualisiert', '', 'success');
            }
        });
    };


    // 🗑️ Load deleted child notes for a parent note
window.trashNotes = async function (noteId) {
    const container = document.getElementById(`deletedNotesContainer${noteId}`);
    if (!container) return;

    container.innerHTML = '<div class="text-muted p-2">Gelöschte Notizen werden geladen...</div>';

    try {
        const res = await fetch(`/notes/deleted/${noteId}`);
        const data = await res.json();

        if (!data.length) {
            container.innerHTML = '<div class="text-muted">Keine gelöschten Unter-Notizen.</div>';
            return;
        }

        container.innerHTML = '';
        data.forEach(note => {
            const html = `
                <div class="card p-2 mb-2 border-left-danger  ">
                    <div><strong>${note.description}</strong></div>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-success " onclick="restoreDeletedNote(${note.id})">
                            <i class="feather icon-rotate-ccw"></i> Wiederherstellen
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="permanentlyDeleteNote(${note.id})">
                            <i class="feather icon-trash-2"></i> Endgültig löschen
                        </button>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    } catch {
        container.innerHTML = '<div class="text-danger">Fehler beim Laden der gelöschten Notizen.</div>';
    }
};

// ♻️ Restore soft-deleted note
window.restoreDeletedNote = async function (id) {
    try {
        const res = await fetch(`/notes/restore/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf
            }
        });
        const data = await res.json();

        if (data.success) {
            Swal.fire('Wiederhergestellt', 'Die Notiz wurde erfolgreich wiederhergestellt.', 'success');
            document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
            location.reload(); // Optional: to reload list
        } else {
            Swal.fire('Fehler', 'Konnte die Notiz nicht wiederherstellen.', 'error');
        }
    } catch {
        Swal.fire('Fehler', 'Konnte die Notiz nicht wiederherstellen.', 'error');
    }
};

// 🛡️ Permanently delete with admin authentication
window.permanentlyDeleteNote = function (id) {
    Swal.fire({
        title: 'Administrator-Zugriff erforderlich',
        html: `
            <input type="text" id="adminUser" class="swal2-input" placeholder="Benutzername">
            <input type="password" id="adminPass" class="swal2-input" placeholder="Passwort">
        `,
        confirmButtonText: 'Endgültig löschen',
        focusConfirm: false,
        showCancelButton: true,
        preConfirm: () => {
            const user = document.getElementById('adminUser').value;
            const pass = document.getElementById('adminPass').value;
            if (!user || !pass) {
                Swal.showValidationMessage('Benutzername und Passwort sind erforderlich');
                return false;
            }
            return { user, pass };
        }
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/notes/delete-permanent/${id}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(result.value)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Gelöscht!', data.message, 'success');
                    document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
                    location.reload();
                } else {
                    Swal.fire('Fehlgeschlagen', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Fehler', 'Serverfehler beim Löschen.', 'error');
            });
        }
    });
};

// 🔍 Open modal & load deleted child notes
window.openDeletedNotesModal = async function(noteId) {
    $('#deletedNotesModal').modal('show');
    const container = document.getElementById('deletedNotesModalBody');
    container.innerHTML = '<div class="text-muted">Gelöschte Notizen werden geladen...</div>';

    try {
        const res = await fetch(`/notes/deleted/${noteId}`);
        const data = await res.json();

        if (!data.html) {
            container.innerHTML = '<div class="text-muted">Keine gelöschten Unter-Notizen gefunden.</div>';
            return;
        }

        container.innerHTML = data.html;
        feather.replace();

    } catch (err) {
        container.innerHTML = '<div class="text-danger">Fehler beim Laden der Notizen.</div>';
    }
};


window.loadAllDeletedNotes = async function () {
    $('#noteDeletedModalWrapper').modal('show');
    const container = document.getElementById('noteDeletedModalBody');
    container.innerHTML = '<div class="text-muted">Lade gelöschte Notizen...</div>';

    try {
        const res = await fetch(`/notes/deleted-all`);
        const data = await res.json();

        container.innerHTML = data.html;
        feather.replace();
    } catch {
        container.innerHTML = '<div class="text-danger">Fehler beim Laden der gelöschten Notizen.</div>';
    }
};


    initNoteListeners();
});
</script>

 


<!-- Loading the checklists  -->
<script>
    function loadChecklist(button) {
        const customerId = button.dataset.customerId;
        const alternativeId = button.dataset.alternativeId;
        const productId = button.dataset.productId;
        const leadProductListId = button.dataset.productListId;

        const container = document.getElementById('mainContent');
        container.innerHTML = '<div class="p-3 text-center">Checkliste wird geladen...</div>';

        fetch('/lead-product-checklist/init', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId,
                lead_product_list_id: leadProductListId,
            })
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                container.innerHTML = `<pre style="color:red">${text}</pre>`;
                throw e;
            }
        })
        .then(data => {
            if (data.success) {
                container.innerHTML = `
                    <form id="customerChecklistForm" class="form-preview bg-white p-4">
                        <input type="hidden" name="lead_product_list_id" value="${leadProductListId}">
                        <input type="hidden" name="customer_id" value="${customerId}">
                        <input type="hidden" name="alternative_id" value="${alternativeId}">
                        <input type="hidden" name="product_id" value="${productId}">
                        ${data.html}
                        <button type="submit" class="btn btn-primary mt-3">Speichern</button>
                    </form>
                `;

                attachChecklistEvents();
            } else {
                container.innerHTML = '<div class="alert alert-danger">Fehler beim Laden.</div>';
            }
        })
        .catch(err => {
            console.error('Fetch failed:', err);
            container.innerHTML = '<div class="alert alert-danger">Ein Fehler ist aufgetreten.</div>';
        });
    }

    function attachChecklistEvents() {
        const form = document.querySelector('#customerChecklistForm');
        if (!form) return;

        form.addEventListener('submit', submitChecklist);
        form.addEventListener('input', () => {
            evaluateFormulas();
            updateProgressBars();
        });

        evaluateFormulas();
        updateProgressBars();
    }

    function submitChecklist(e) {
        e.preventDefault();

        const form = document.getElementById('customerChecklistForm');
        const filledValues = {};
        const leadProductListId = form.querySelector('[name="lead_product_list_id"]').value;
        const customerId = form.querySelector('[name="customer_id"]').value;
        const alternativeId = form.querySelector('[name="alternative_id"]').value;
        const productId = form.querySelector('[name="product_id"]').value;

        form.querySelectorAll('input, select, textarea').forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            const name = input.name.replace(/\[\]$/, '');

            if (input.type === 'checkbox') {
                filledValues[name] = input.checked ? 1 : 0;
            } else if (input.name.endsWith('[]')) {
                if (!Array.isArray(filledValues[name])) filledValues[name] = [];
                filledValues[name].push(input.value);
            } else {
                filledValues[name] = input.value;
            }
        });

        fetch('/lead-product-checklist/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                lead_product_list_id: leadProductListId,
                filled_values: filledValues,
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Gespeichert', 'Die Daten wurden erfolgreich gespeichert.', 'success');
            } else {
                Swal.fire('Fehler', data.message || 'Daten konnten nicht gespeichert werden.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Fehler', 'Ein Fehler ist aufgetreten.', 'error');
        });
    }

function evaluateFormulas() {
    const values = {};

    document.querySelectorAll('#customerChecklistForm input, #customerChecklistForm select, #customerChecklistForm textarea')
        .forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            values[input.name] = input.type === 'checkbox' ? (input.checked ? 1 : 0) : input.value;
        });

    document.querySelectorAll('.formula-field').forEach(field => {
        const formula = field.dataset.formula;
        const result = evaluateFormula(formula, values);
        field.value = isNaN(result) ? 'Fehler' : result;
    });
}

function evaluateFormula(formula, values) {
    try {
        const fns = { add, sub, mul, div, round, min, max, toNum };
        const valKeys = Object.keys(values).filter(key => /^[a-zA-Z_][a-zA-Z0-9_]*$/.test(key));
        const valVals = valKeys.map(k => toNum(values[k]));

        const fnKeys = Object.keys(fns);
        const fnVals = Object.values(fns);

        const fn = new Function(...fnKeys, ...valKeys, `return ${formula}`);
        return fn(...fnVals, ...valVals);
    } catch (e) {
        console.warn('Formula error:', formula, e);
        return 'Fehler';
    }
}

function toNum(val) {
    return val === '' || val == null || isNaN(val) ? 0 : Number(val);
}
function add(a, b) { return toNum(a) + toNum(b); }
function sub(a, b) { return toNum(a) - toNum(b); }
function mul(a, b) { return toNum(a) * toNum(b); }
function div(a, b) { const d = toNum(b); return d === 0 ? 0 : toNum(a) / d; }
function round(v, p = 0) { return Math.round(toNum(v) * 10 ** p) / 10 ** p; }
function min(...args) { return Math.min(...args.map(toNum)); }
function max(...args) { return Math.max(...args.map(toNum)); }

function updateProgressBars() {
    document.querySelectorAll('.accordion-section').forEach(section => {
        const inputs = section.querySelectorAll('input, select, textarea');
        let total = 0;
        let filled = 0;

        inputs.forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            total++;
            const isFilled = input.type === 'checkbox' ? input.checked : input.value !== '';
            if (isFilled) filled++;
        });

        const percent = total > 0 ? Math.round((filled / total) * 100) : 0;
        const bar = section.querySelector('.progress-bar');

        if (bar) {
            bar.style.width = percent + '%';
            bar.setAttribute('aria-valuenow', percent);
            bar.innerText = percent + '%';
        }
    });
}
</script>



<!-- Image of customres  -->


<script>
    function loadDocuments(button) {
    const customerId = button.dataset.customerId;
    const alternativeId = button.dataset.alternativeId;
    const productId = button.dataset.productId;
    const productListId = button.dataset.productListId;

    const container = document.getElementById('mainContent');
    container.innerHTML = `<div class="p-3 text-center">Dokumente werden geladen...</div>`;

    fetch('/document/load', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            customer_id: customerId,
            alternative_id: alternativeId,
            product_id: productId,
            product_list_id: productListId
        })
    })
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        // Attach search input listener after content is loaded
            const searchInput = document.getElementById('searchImage');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const keyword = this.value.trim().toLowerCase();

                    document.querySelectorAll('.gallery-item').forEach(el => {
                        const name = el.dataset.name?.toLowerCase() || '';
                        const type = el.dataset.type?.toLowerCase() || '';
                        const dateRaw = el.dataset.date || '';
                        const dateFormatted = new Date(dateRaw).toLocaleDateString('de-DE');
                        const date = dateFormatted.toLowerCase();

                        const fullText = `${name} ${type} ${date}`;
                        el.style.display = fullText.includes(keyword) ? '' : 'none';
                    });
                });
            }

        feather.replace();

        // re-init GLightbox
        GLightbox({ selector: '.glightbox' });

        const stageFilter = document.getElementById('stageFilter');
            if (stageFilter) {
                stageFilter.addEventListener('change', function () {
                    const stage = this.value;
                    document.querySelectorAll('.gallery-item').forEach(el => {
                        el.style.display = !stage || el.dataset.stage === stage ? '' : 'none';
                    });
                });
            }


        // re-init Lazy Loading
        const lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
        if ("IntersectionObserver" in window) {
            let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        let img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove("lazy");
                        lazyImageObserver.unobserve(img);
                    }
                });
            });
            lazyImages.forEach(img => lazyImageObserver.observe(img));
        }

        // re-init Dropzone
        Dropzone.autoDiscover = false;
        new Dropzone("#documentDropzone", {
            paramName: "file",
            maxFilesize: 10,
            acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc,.docx",
            success: function (file, response) {
                loadDocuments(button); // refresh on upload
            }
        });
    })
    .catch(err => {
        container.innerHTML = `<div class="text-danger">Fehler beim Laden der Dokumente</div>`;
    });
}

setTimeout(() => GLightbox({ selector: '.glightbox' }), 100);


function deleteDocument(id, el) {
    Swal.fire({
        title: 'Löschen bestätigen',
        text: 'Willst du dieses Dokument wirklich löschen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/document/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    el.closest('.gallery-item').remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Gelöscht!',
                        text: 'Das Dokument wurde entfernt.',
                        timer: 1000,
                        showConfirmButton: false
                    });
                }
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    GLightbox({
        selector: '.glightbox'
    });
});

function renameDocument(id, newName) {
    fetch('/document/rename', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ id: id, image_name: newName })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Erfolgreich umbenannt!',
                showConfirmButton: false,
                timer: 1000
            });
        } else {
            throw new Error();
        }
    })
    .catch(() => {
        Swal.fire({
            icon: 'error',
            title: 'Fehler beim Umbenennen!',
            text: 'Bitte erneut versuchen.'
        });
    });
}

</script>
 
<script>
document.addEventListener("DOMContentLoaded", function () {
    const lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
    if ("IntersectionObserver" in window) {
        let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    let img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove("lazy");
                    lazyImageObserver.unobserve(img);
                }
            });
        });

        lazyImages.forEach(function (lazyImage) {
            lazyImageObserver.observe(lazyImage);
        });
    }
});
 
</script>
 

<!-- Map view  -->

<script>
    // 🔥 GLOBAL STORE FOR MAP INSTANCES
window.googleMapsInstances = {};

// 👉 INIT MAP ON SIDEBAR OPEN
function openSidebarGallery(triggerEl) {
    const objectId = triggerEl.dataset.alternativeId;
    const address = triggerEl.dataset.address;
    const sidebar = document.getElementById('sidebarGallery' + objectId);
    if (!sidebar) return;

    sidebar.classList.add('active');

    // Load gallery images
    fetch(`/load-images/${objectId}`)
        .then(res => res.json())
        .then(images => {
            const gallery = document.getElementById('galleryImages' + objectId);
            if (!gallery) return;

            gallery.innerHTML = '';

            if (Array.isArray(images) && images.length > 0) {
                images.forEach(img => {
                    appendImageToGallery(objectId, img.image);
                });
                GLightbox({ selector: '.glightbox' });
            } else {
                gallery.innerHTML = '<p class="text-muted">Keine Bilder vorhanden.</p>';
            }
        })
        .catch(err => {
            console.warn("Image loading failed:", err);
        });

    // Delay map init so sidebar is visible
    setTimeout(() => initGoogleMap(objectId, address), 300);
}


function closeSidebarGallery(objectId) {
    document.getElementById('sidebarGallery' + objectId)?.classList.remove('active');
}

// ✅ INIT GOOGLE MAP
function initGoogleMap(objectId, address) {
    const mapDiv = document.getElementById('mapContainer' + objectId);
    if (!mapDiv) return;

    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address }, (results, status) => {
        if (status === 'OK') {
            const map = new google.maps.Map(mapDiv, {
                center: results[0].geometry.location,
                zoom: 18,
                mapTypeId: 'roadmap',
            });
            new google.maps.Marker({ position: results[0].geometry.location, map });

            window.googleMapsInstances[objectId] = map;

            // Handle map type switching
            const select = document.getElementById('screenshotMode' + objectId);
            if (select) {
                select.addEventListener('change', () => {
                    const type = select.value;
                    if (type !== 'streetview') map.setMapTypeId(type);
                });
            }
        } else {
            Swal.fire('Fehler', 'Adresse nicht gefunden.', 'error');
        }
    });
}

// 🎯 TRIGGER SCREENSHOT
function triggerScreenshot(customerId, alternativeId) {
    const mode = document.getElementById('screenshotMode' + alternativeId)?.value || 'roadmap';
    if (mode === 'streetview') {
        captureStreetViewScreenshot(customerId, alternativeId);
    } else {
        captureStaticMapScreenshot(customerId, alternativeId);
    }
}

// 📷 STATIC MAP SCREENSHOT (Google Static Maps API)
function captureStaticMapScreenshot(customerId, alternativeId) {
    const map = window.googleMapsInstances[alternativeId];
    if (!map) return Swal.fire('Fehler', 'Karte nicht geladen.', 'error');

    const center = map.getCenter();
    const zoom = map.getZoom();
    const type = map.getMapTypeId();

    const staticUrl = `https://maps.googleapis.com/maps/api/staticmap?center=${center.lat()},${center.lng()}&zoom=${zoom}&size=800x400&maptype=${type}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;

    fetch(staticUrl)
        .then(res => res.blob())
        .then(blob => uploadScreenshot(blob, customerId, alternativeId, 'map_screenshot.png'));
}

// 📷 STREET VIEW SCREENSHOT (Google Street View API)
function captureStreetViewScreenshot(customerId, alternativeId) {
    const map = window.googleMapsInstances[alternativeId];
    if (!map) return Swal.fire('Fehler', 'Karte nicht geladen.', 'error');

    const svService = new google.maps.StreetViewService();
    const panorama = map.getStreetView();
    const pov = panorama.getPov();
    const position = panorama.getPosition();

    if (!position) {
        Swal.fire('⚠️ Street View nicht aktiv', '', 'warning');
        return;
    }

    const lat = position.lat();
    const lng = position.lng();
    const heading = pov.heading || 0;
    const pitch = pov.pitch || 0;

    const metaUrl = `https://maps.googleapis.com/maps/api/streetview/metadata?location=${lat},${lng}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;
    fetch(metaUrl)
        .then(res => res.json())
        .then(meta => {
            if (meta.status !== 'OK') {
                Swal.fire('Kein Street View Bild verfügbar', '', 'warning');
                return;
            }

            const viewUrl = `https://maps.googleapis.com/maps/api/streetview?size=800x400&location=${lat},${lng}&fov=90&heading=${heading}&pitch=${pitch}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;
            fetch(viewUrl)
                .then(res => res.blob())
                .then(blob => uploadScreenshot(blob, customerId, alternativeId, 'streetview_screenshot.png'));
        });
}

// 📨 UPLOAD SCREENSHOT TO SERVER
function uploadScreenshot(blob, customerId, alternativeId, filename) {
    const formData = new FormData();
    formData.append('image', blob, filename);
    formData.append('customer_id', customerId);
    formData.append('alternative_id', alternativeId);
    formData.append('status', 'screenshot');

    fetch('/save-screenshot', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
        .then(res => res.json())
        .then(result => {
            if (result.success && result.image) {
                Swal.fire('✅ Screenshot gespeichert!', '', 'success');
                appendImageToGallery(alternativeId, result.image);
            } else {
                Swal.fire('Fehler beim Speichern', '', 'error');
            }
        });
}

function appendImageToGallery(alternativeId, imagePath) {
    const gallery = document.getElementById('galleryImages' + alternativeId);
    const fullUrl = `${window.location.origin}/public/uploads/${imagePath}`;

    const wrapper = document.createElement('div');
    wrapper.className = 'screenshot-item d-inline-block position-relative m-1';
    wrapper.style.width = '90px';

    const link = document.createElement('a');
    link.href = fullUrl;
    link.className = 'glightbox';
    link.setAttribute('data-gallery', `object-gallery-${alternativeId}`);
    link.setAttribute('data-title', 'Screenshot');

    const image = document.createElement('img');
    image.src = fullUrl;
    image.className = 'img-thumbnail';
    image.style = 'width: 90px; height: 60px; object-fit: cover;';
    link.appendChild(image);

    const delBtn = document.createElement('button');
    delBtn.className = 'btn btn-sm btn-danger position-absolute';
    delBtn.style = 'top: -5px; right: -5px; padding: 2px 5px; font-size: 12px;';
    delBtn.innerHTML = 'x';
    delBtn.onclick = () => deleteScreenshot(imagePath, wrapper);

    wrapper.appendChild(link);
    wrapper.appendChild(delBtn);
    gallery.appendChild(wrapper);

    GLightbox({ selector: '.glightbox' });
}

function deleteScreenshot(filename, wrapperElement) {
    Swal.fire({
        title: 'Bild löschen?',
        text: 'Dieses Bild wird dauerhaft entfernt.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('/delete-screenshot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ image: filename })
            })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        wrapperElement.classList.add('fade-out');
                        setTimeout(() => wrapperElement.remove(), 400);
                        Swal.fire('✅ Gelöscht!', '', 'success');
                    } else {
                        Swal.fire('❌ Fehler beim Löschen.', '', 'error');
                    }
                });
        }
    });
}


</script>
@endsection




@push('scripts')


 

<script>
function loadkanban(customerId, alternativeId, productId, employeeId) {
    const container = document.getElementById('mainContent');
    container.innerHTML = '<div class="p-3 text-center">Kanban wird geladen...</div>';

    fetch(`/customer/process/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}`)
        .then(response => response.json())
        .then(data => {
            fetch('/customer/process/kanban/view', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ leads: data })
            })
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                feather.replace();
                initKanbanSortable(); // ✅ required here

            });
        })
        .catch(err => {
            container.innerHTML = `<div class="alert alert-danger">Fehler beim Laden des Kanban-Boards.</div>`;
            console.error(err);
        });
}
 
function initKanbanSortable(onStageChangeSuccess = () => {}) {
    const dropzones = document.querySelectorAll('.kanban-dropzone');
    console.log('[Kanban Init] Found', dropzones.length, 'dropzones');

    const borderColors = {
        lead: '#fcd34d',
        offer: '#93c5fd',
        deal: '#34d399',
        project: '#60a5fa',
        ticket: '#fca5a5',
        completed: '#a3e635',
        junk: '#d1d5db'
    };

    dropzones.forEach((zone, index) => {
        console.log(`[Kanban] Initializing Sortable on dropzone ${index + 1}`);

        new Sortable(zone, {
            group: 'kanban',
            animation: 150,
            onAdd: function (evt) {
                const el = evt.item;
                const newStage = evt.to.closest('.kanban-column')?.dataset.stage;

                const customerId     = el.dataset.customerId;
                const alternativeId  = el.dataset.alternativeId;
                const productId      = el.dataset.productId;
                const employeeId     = el.dataset.employeeId || 0;
                const service        = el.dataset.service;
                const serviceId      = el.dataset.serviceId || 0;
                const departmentId   = el.dataset.departmentId || 0;

                if (!customerId || !alternativeId || !productId || !service || !newStage) {
                    console.error('[Kanban Error] Missing required data');
                    return;
                }

                const url = `/lead-product/change-stage/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/${newStage}/${serviceId}/${departmentId}`;

                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // ✅ Animate color change
                        const newColor = borderColors[newStage] || '#ccc';
                        el.style.transition = 'border-color 0.3s ease';
                        el.style.borderLeftColor = newColor;

                        // ✅ Update the stage text inside the card, if available
                        const stageLabel = el.querySelector('.kanban-stage-label');
                        if (stageLabel) {
                            const stageMap = {
                                lead: 'Kunde',
                                offer: 'Angebot',
                                deal: 'Auftrag',
                                project: 'Montage',
                                completed: 'Abgeschlossen',
                                ticket: 'Ticket',
                                junk: 'Junk'
                            };
                            stageLabel.textContent = stageMap[newStage] || newStage;
                        }

                        // ✅ SweetAlert success
                        Swal.fire({
                            icon: 'success',
                            title: 'Erfolg',
                            text: 'Status wurde erfolgreich geändert',
                            timer: 1200,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });

                        // ✅ Callback for further refresh if needed
                        onStageChangeSuccess(el, newStage);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Fehler',
                            text: 'Status konnte nicht aktualisiert werden'
                        });
                    }
                })
                .catch(err => {
                    console.error('[Kanban] AJAX error', err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Serverfehler',
                        text: 'Die Änderung konnte nicht gespeichert werden'
                    });
                });
            }
        });
    });

    function capitalize(text) {
        return text.charAt(0).toUpperCase() + text.slice(1);
    }
}


</script>

    
<!-- Ticket systems  -->
 
 

 <script> 
const context = document.getElementById('filterContext');
const customer_id = context.dataset.customer;
const alternative_id = context.dataset.alternative;
const product_id = context.dataset.product;

function LoadCustomerTicket(customerId, alternativeId, productId, tab) {
    const container = document.getElementById("mainContent");
    container.innerHTML = '<div class="p-4 text-center">Tickets werden geladen...</div>';

    fetch("/customer/tickets/load", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            customer_id: customerId,
            alternative_id: alternativeId,
            product_id: productId,
            tab: tab
        })
    })
    .then(res => res.text())
    .then(html => {
        container.innerHTML = html;
        feather.replace();
        initKanbanSortable(); // ✅ re-initialize sortable
    });
}
    function filterTickets() {
        const date = document.getElementById('filterDate').value;
        const status = document.getElementById('filterStatus').value;
        const employee = document.getElementById('filterEmployee').value;

        // ✅ use correctly defined vars
        const context = document.getElementById('filterContext');
        const customer_id = context.dataset.customer;
        const alternative_id = context.dataset.alternative;
        const product_id = context.dataset.product;

        fetch('/customer/tickets/load', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id,
                alternative_id,
                product_id,
                date,
                status,
                employee
            })
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('mainContent').innerHTML = html;
            feather.replace();
            initKanbanSortable();
        });
    }


function initKanbanSortable() {
    document.querySelectorAll('.kanban-dropzone').forEach(zone => {
        new Sortable(zone, {
            group: 'tickets',
            animation: 150,
            onAdd: function (evt) {
                const ticketId = evt.item.dataset.id;
                const newStatus = evt.to.closest('.kanban-column').dataset.status;

                fetch('/ticket/status/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ id: ticketId, status: newStatus })
                }).then(res => res.json())
                  .then(res => {
                      if (res.success) {
                          console.log(`Ticket #${ticketId} → ${newStatus}`);
                      }
                  });
            }
        });
    });
} 
// Optional: Call this once if Kanban is rendered on page load
document.addEventListener('DOMContentLoaded', initKanbanSortable);
</script>



<!-- Loading customer info Product  -->
<script>
let currentCid = null;
let currentAid = null;
let currentPid = null;

// 🔄 On product button click
function leadProduct(button) {
    const $btn = $(button);
    currentCid = $btn.data('customer-id');
    currentAid = $btn.data('alternative-id');
    currentPid = $btn.data('product-id');

    Swal.fire({
        title: 'Loading...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    $.ajax({
        url: `/lead-product/${currentCid}/${currentAid}/${currentPid}`,
        method: 'GET',
        success: function (response) {
            $('#mainContent').html(response);
            Swal.close();
            feather.replace();

            setTimeout(() => {
                const $modal = $('#addProductModal');
                const $productSelect = $('#customer_product_info');
                const $departmentSelect = $('#department_id');

                fetch(`/customer/load/product/${currentPid}`)
                    .then(res => res.json())
                    .then(data => {
                        // 1️⃣ Populate Product Select
                        $productSelect.empty();

                        (data.products || []).forEach(product => {
                            const image = product.images[0]?.image ? `/uploads/${product.images[0].image}` : '/images/icons/placeholder.svg';
                            const brand = product.brand?.name || '';
                            const description = product.short_description || '';

                            $productSelect.append(new Option(product.product, product.id, false, false))
                                .find(`option[value="${product.id}"]`)
                                .attr('data-brand', brand)
                                .attr('data-description', description)
                                .attr('data-image', image);
                        });

                        $productSelect.select2({
                            templateResult: formatProduct,
                            templateSelection: formatProductSelection,
                            allowClear: true,
                            dropdownParent: $modal
                        });

                        $productSelect.on('change', function () {
                            const selected = this.options[this.selectedIndex];
                            $('#manufacturer_note').val(selected.getAttribute('data-brand') || '');
                            $('#notes_note').val(selected.getAttribute('data-description') || '');
                        });

                        // 2️⃣ Populate Departments
                        $departmentSelect.empty().append(`<option value=""></option>`);
                        (data.departments || []).forEach(dept => {
                            $departmentSelect.append(new Option(dept.department_name, dept.id));
                        });

                        // 3️⃣ Show modal
                        // if (!$modal.hasClass('show')) {
                        //     $modal.modal('show');
                        // }
                    })
                    .catch(error => {
                        console.error('❌ Fehler beim Laden:', error);
                        Swal.fire('Fehler', 'Produktdaten konnten nicht geladen werden.', 'error');
                    });
            }, 300);
        },
        error: function () {
            Swal.fire('Error', 'Failed to load product info.', 'error');
        }
    });
}

// 🖼️ Select2 format with product image
function formatProduct(state) {
    if (!state.id) return state.text;
    const image = $(state.element).data('image');
    return $(`<span><img src="${image}" style="width:30px;height:30px;object-fit:cover;margin-right:8px"> ${state.text}</span>`);
}

function formatProductSelection(state) {
    return state.text;
}
</script>
 
 
<script>
function addProduct() {
    const selectedProduct = $('#customer_product_info option:selected');
    const data = {
        _token: '{{ csrf_token() }}',
        customer_id: currentCid,
        alternative_id: currentAid,
        product_id: $('#customer_product_info').val(),
        product_name: selectedProduct.text(),
        manufacturer: $('#manufacturer_note').val(),
        serial_number: $('#serial_number').val(),
        installation_date: $('#installation_date').val(),
        installation_location: $('#installation_location').val(),
        purchased_from_us: $('#purchased_from_us').val(),
        purchase_date: $('#purchase_date').val(),
        invoice_reference: $('#invoice_reference').val(),
        warranty_until: $('#warranty_until').val(),
        guarantee_until: $('#guarantee_until').val(),
        image_available: $('#image_available').val(),
        installed_by: $('#installed_by').val(),
        department_id: $('#department_id').val(),
        notes: $('#notes_note').val()
    };

    $.post('/lead-product/store', data, function (response) {
        $('#addProductModal').modal('hide');
        Swal.fire('Erfolgreich', 'Produkt hinzugefügt', 'success');
        appendRow(response);
    }).fail(() => {
        Swal.fire('Fehler', 'Produkt konnte nicht gespeichert werden', 'error');
    });
}

function appendRow(product) {
    const row = `
        <tr data-id="${product.id}">
            <td>${product.product_name}</td>
            <td>${product.manufacturer ?? '—'}</td>
            <td>${product.serial_number ?? '—'}</td>
            <td>${product.installation_date ?? '—'}</td>
            <td>${product.purchase_date ?? '—'}</td>
            <td>${product.installed_by ?? '—'}</td>
            <td>${product.department_name ?? '—'}</td>
            <td>
                <button class="btn btn-sm btn-outline-secondary" onclick="editProduct(${product.id})">✏️</button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.id})">🗑️</button>
            </td>
        </tr>
    `;
    $('#productTableBody').append(row);
}

function deleteProduct(id) {
    Swal.fire({
        title: 'Löschen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/lead-product/delete/${id}`,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: () => {
                    $(`tr[data-id="${id}"]`).remove();
                    Swal.fire('Gelöscht', 'Eintrag wurde entfernt', 'success');
                },
                error: () => {
                    Swal.fire('Fehler', 'Konnte nicht gelöscht werden', 'error');
                }
            });
        }
    });
}

function editProduct(id) {
    const row = $(`tr[data-id="${id}"]`);
    const currentName = row.find('td:first').text();

    Swal.fire({
        title: 'Produktname bearbeiten',
        input: 'text',
        inputValue: currentName,
        showCancelButton: true,
        confirmButtonText: 'Aktualisieren'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/lead-product/update/${id}`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_name: result.value
                },
                success: () => {
                    row.find('td:first').text(result.value);
                    Swal.fire('Aktualisiert', 'Produktname geändert', 'success');
                },
                error: () => {
                    Swal.fire('Fehler', 'Konnte nicht aktualisiert werden', 'error');
                }
            });
        }
    });
}
</script>


<script>
    console.log('✅ addNewRoofEditProfile is defined here');

    let roofIndex = {{ isset($roofs) ? count($roofs) : 0 }};

    function addNewRoofEditProfile() {
        console.log('📦 Called addNewRoofEditProfile');
        fetch(`/admin/roofs/partial-edit-profile/${roofIndex}`)
            .then(res => res.text())
            .then(html => {
                const wrapper = document.getElementById('roof-wrapper');
                const newDiv = document.createElement('div');
                newDiv.innerHTML = html;
                wrapper.appendChild(newDiv);
                roofIndex++;
            })
            .catch(err => console.error('Fehler beim Laden des neuen Daches:', err));
    }
</script>

 
<!-- Customer Product List  -->
<script>
    const PLACEHOLDER_IMAGE = "{{ asset('images/icons/placeholder.svg') }}";
    const EMPLOYEE_IMAGE = "{{ asset('images/employee/') }}";
</script>
 
<script>
document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById('dashboard');
    if (!container) return console.error("❌ #dashboard not found.");

    const customerId = container.dataset.id;
    if (!customerId) {
        container.innerHTML = "<p class='text-danger'>❗ No customer ID provided.</p>";
        return;
    }

    const queryParams = new URLSearchParams({
        customer_id: customerId,
        product_id: window.selectedProductId || '',
        service_id: window.selectedServiceId || '',
        stage: window.selectedStage || '',
        alternative_id: window.selectedAltId || ''
    });


    const services = {
        complete: 'Komplettlösung',
        montage: 'Montage',
        product: 'Produkt',
        plan: 'Planung',
        maintenance: 'Wartung',
        repair: 'Reparatur',
        emergency: 'Notdienst',
        others: 'Sonstiges'
    };

    const realizations = {
        soon: 'Schnellstmöglich',
        3: '3 Monate',
        6: '6 Monate',
        other: 'Sonstiges'
    };


    const interests = {
        intent: 'Kaufabsicht',
        interest: 'Kaufinteresse',
        option: 'Kaufoption'
    };

    fetch(`/api/objects-with-products?${queryParams}`)
        .then(res => res.json())
        .then(data => {
            container.innerHTML = '';
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = "<p class='text-warning'>⚠️ Keine Objekte gefunden.</p>";
                return;
            }

            data.forEach(object => {
                const block = document.createElement('div');
                block.className = 'house-block mb-4';

                const header = document.createElement('div');
                header.className = 'house-header d-flex align-items-center p-1 border';
                header.innerHTML = `
                    <div>
                        <div class="font-weight-bold primary">${object.object_name || 'Objekt'}</div>
                        <div class="text-muted">${object.street || ''} </div>
                        <div class="text-muted">${object.postcode || ''} ${object.city || ''}</div>
                    </div>
                    <div class="house-img ml-2">
                        <img src="${object.screenshot_image?.src || '/images/icons/placeholder.svg'}"
                             style="width: 100px; cursor: pointer;"
                             onclick="openSidebarGallery(this)"
                             data-customer-id="${object.screenshot_image?.customer_id || ''}"
                             data-alternative-id="${object.screenshot_image?.alternative_id || ''}"
                             data-address="${object.screenshot_image?.address || ''}">
                    </div>`;
                block.appendChild(header);

                const table = document.createElement('table');
                table.className = 'table table-bordered table-hover mt-2';
                const tbody = document.createElement('tbody');

                (object.products || []).forEach(prod => {
                    const latest = prod.recent_done || {};
                    const currentPhaseName = latest.phase_name || null;
                    const currentPhaseId = latest.phase_id || null;
                    const currentActivityId = latest.activity_id || null;

                    let doneCount = 0, totalCount = 0;
                    if (Array.isArray(prod.phase_progress)) {
                        const current = prod.phase_progress.find(p => p.phase_name === currentPhaseName);
                        if (current) {
                            doneCount = parseInt(current.done || 0);
                            totalCount = parseInt(current.total || 0);
                        }
                    }

                    const progressPercent = totalCount > 0 ? Math.round((doneCount / totalCount) * 100) : 0;
                    const stageKey = (prod.stage_history || []).at(-1)?.stage || prod.stage || '';
                    const translatedStage = translateStage(stageKey);

                    const carouselDivId = `next_phase_station_${prod.product_id}_${currentPhaseId}`;

                    const rowGroup = document.createElement('tbody');
                    rowGroup.innerHTML = `
                        <tr>
                            <th>Produkt</th>
                            <th>Phase</th>
                            <th>Schritt</th>
                            <th>Aufgabe</th>
                            <th>Zuständig</th>
                            <th>Erledigt</th>
                            <th>Datum</th>
                            <th>Next Step</th>
                            <th class="text-center">Aktionen</th>
                        </tr>
                        <tr> 
                                <td class="text-center align-middle">
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="d-flex align-items-center position-relative mb-1">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                                style="width: 40px; height: 40px; font-weight: bold; font-size: 14px;">
                                                ${prod.initial || '—'}
                                            </div>
                                            ${prod.employee?.image ? `
                                                <img src="${EMPLOYEE_IMAGE}/${prod.employee.image}" class="rounded-circle border border-white position-absolute"
                                                    style="width: 24px; height: 24px; bottom: -5px; right: -10px; object-fit: cover;" />
                                            ` : ''}
                                        </div>

                                        <div class="text-muted small d-flex flex-column align-items-center text-center">
                                            <span>
                                                <i class="feather icon-tag text-danger"></i>
                                                ${services[prod.service] || prod.service || '–'} –
                                                <i class="feather icon-box text-muted"></i>
                                                ${prod.department?.name || 'Keine Abteilung'}
                                            </span>
                                            <span>
                                                <i class="feather icon-heart text-primary"></i>
                                                    ${interests[prod.interest] || prod.interest || '–'}
                                                <i class="feather icon-calendar text-primary"></i>
                                                ${realizations[prod.realization_time] || prod.realization_time || '–'}
                                            </span>
                                        </div>

                                        <div class="d-flex gap-1 mt-1">
                                            <div class="progress" style="height: 8px; width: 80px;" title="${doneCount} von ${totalCount} erledigt">
                                                <div class="progress-bar bg-${progressPercent === 100 ? 'success' : progressPercent === 0 ? 'secondary' : 'warning'}"
                                                    style="width: ${progressPercent}%"></div>
                                            </div>
                                            <div class="small text-muted" style="min-width: 40px;">${doneCount}/${totalCount}</div>
                                        </div>
                                    </div>
                                </td> 

                            <td>
                                <div class="d-flex align-items-center mb-1">
                                    <button class="btn btn-sm btn-icon text-primary mr-1" title="Phase ändern"
                                            onclick="changeProductStage(${prod.product_id}, ${object.customer_id}, ${object.id}, ${prod.service_id})">
                                        <i class="feather icon-edit-2"></i>
                                    </button>
                                    <span>${translatedStage}</span>
                                </div>
                            </td>
                            <td>${latest.phase_name || '—'}</td>
                            <td>${latest.activity_title || '—'}</td>
                             <td>${employeeBadge(latest.done_by_name, latest.done_by_image)}</td>
                            <td>${employeeBadge(latest.marked_by_name, latest.marked_by_image)}</td>
                           
                            <td>${latest.changed_at ? new Date(latest.changed_at).toLocaleDateString('de-DE') : '—'}</td>
                            <td>
                                 <div id="${carouselDivId}"
                                    class="activity-carousel-loader"
                                    data-product-id="${prod.product_id}"
                                    data-phase-id="${currentPhaseId || ''}"
                                    data-activity-id="${currentActivityId || ''}">
                                    ⏳
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-icon rounded-circle btn-warning open-phase-modal"
                                        data-product-id="${prod.product_id}"
                                        data-stage="${stageKey}"
                                        data-service-id="${prod.service_id}"
                                        data-customer-id="${object.customer_id}"
                                        data-alternative-id="${object.id}">
                                    <i class="feather icon-eye"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="bg-white border-top-0 py-2">
                                <div class="d-flex flex-column small text-black px-2">

                                    ${prod.old_stage ? `
                                        <div>
                                            <i class="feather icon-corner-up-left text-warning"></i>
                                            <strong>Letzter Status:</strong> ${translateStage(prod.old_stage)}
                                        </div>
                                    ` : ''}

                                    ${latest.is_done == '0' ? `
                                        <div>
                                            <i class="feather icon-alert-triangle text-danger"></i>
                                            <strong>Aktivität aktiv:</strong> Diese Aufgabe wurde noch nicht erledigt oder bestätigt.
                                        </div>
                                    ` : ''}

                                </div>
                            </td>
                        </tr>`;

                    tbody.appendChild(rowGroup);

                    // 👉 Load carousel content for this row
                    if (currentPhaseId && currentActivityId) {
                        const carouselDivId = `next_phase_station_${prod.product_id}_${currentPhaseId}`;
                        fetch(`/activity/carousel?phase_id=${currentPhaseId}&activity_id=${currentActivityId}&product_id=${prod.product_id}`)
                        .then(res => res.text())
                            .then(html => {
                                document.getElementById(carouselDivId).innerHTML = html;
                            })
                            .catch(() => {
                                document.getElementById(carouselDivId).innerHTML = '<span class="text-danger">Fehler</span>';
                            });
                    }

                });
                table.appendChild(tbody);

                const tableWrapper = document.createElement('div');
                tableWrapper.className = 'table-responsive';
                tableWrapper.appendChild(table);

                block.appendChild(tableWrapper);

                container.appendChild(block);
            });

            feather.replace();
        })
        .catch(err => {
            console.error("❌ Fetch failed:", err);
            container.innerHTML = "<p class='text-danger'>🚨 Fehler beim Laden des Dashboards.</p>";
        });

    function translateStage(stage) {
        const map = {
            offer: 'Angebot', deal: 'Auftrag', project: 'Montage', complete: 'Abschluss',
            completed: 'Abschluss', ticket: 'Ticket', evaluation: 'Auswertung',
            archive: 'Archiv', lead: 'Lead', pause: 'Pause', junk: 'Junk'
        };
        return map[stage] || stage;
    }

    function employeeBadge(name, image) {
        if (!name) return '';
        return `<div class="d-flex align-items-center" title="${name}">
                    <img src="/images/employee/${image || 'default.png'}" class="rounded-circle mr-1"
                         style="width: 28px; height: 28px; object-fit: cover;" alt="${name}">
                </div>`;
    }

    // 🔁 Carousel loader
    window.loadActivityCarousel = function (phaseId, activityId, productId) {
        const containerId = `next_phase_station_${productId}_${phaseId}`;
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '⏳';
        fetch(`/activity/carousel?phase_id=${phaseId}&activity_id=${activityId}&product_id=${productId}`)
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(() => {
                container.innerHTML = '<span class="text-danger">Fehler</span>';
            });
    }

});


function parseJSON(json) {
        try {
            return typeof json === 'string' ? JSON.parse(json) : json;
        } catch (err) {
            console.warn("⚠️ JSON parse error:", json);
            return [];
        }
    }


    function getInitials(fullName) {
        if (!fullName) return '–';
        const parts = fullName.trim().split(' ');
        return (parts[0]?.charAt(0) || '') + (parts[1]?.charAt(0) || '');
    }

    function translateStage(stage) {
        const map = {
            offer: 'Angebot',
            deal: 'Auftrag',
            project: 'Montage',
            complete: 'Abschluss',
            completed: 'Abschluss',
            ticket: 'Ticket',
            evaluation: 'Auswertung',
            archive: 'Archiv',
            lead: 'Lead',
            pause: 'Pause',
            junk: 'Junk'
        };
        return map[stage] || stage;
    }

    // 🔄 Sidebar modal trigger
    $(document).on('click', '.open-phase-modal', function () {
        const $el = $(this);
        const sidebar = document.getElementById('phaseSidebar');
        const sidebarBody = sidebar.querySelector('.phase-sidebar-body');

        const productId = $el.data('product-id');
        const serviceId = $el.data('service-id');
        const stage = $el.data('stage');
        const customerId = $el.data('customer-id');
        const alternativeId = $el.data('alternative-id');
        const productInitial = $el.closest('.entry-row').find('.icon').text().trim() || '—';

        $('#phaseProductInitial').text(productInitial);
        sidebarBody.dataset.customerId = customerId;
        sidebarBody.dataset.alternativeId = alternativeId;
        sidebarBody.dataset.productId = productId;
        sidebarBody.dataset.serviceId = serviceId;

        sidebar.classList.add('open');
        sidebarBody.innerHTML = '<p>Lade...</p>';

        $.get('/modal/history', {
            product_id: productId,
            service_id: serviceId,
            stage,
            customer_id: customerId,
            alternative_id: alternativeId
        })
            .done(response => {
                    sidebarBody.innerHTML = response;

                    // 👇 Expand current stage group
                    const selector = `[data-toggle="collapse"][data-target="#collapse-${stage}"]`;
                    const toggleEl = sidebarBody.querySelector(selector);
                    const collapseId = toggleEl?.getAttribute('data-target');

                    if (collapseId) {
                        const content = sidebarBody.querySelector(collapseId);
                        if (content && !content.classList.contains('show')) {
                            $(content).collapse('show');
                        }
                    }

                    // ✅ Now run validation/locking logic after content loads
                    initActivityValidation();
                })
            .fail(() => {
                sidebarBody.innerHTML = '<p class="text-danger">❌ Fehler beim Laden der Phasen.</p>';
            });
    });


    function initActivityValidation() {
        const currentUserId = '{{ auth()->user()->name }}';

        $('.history-checkbox').off('change').on('change', function (e) {
            const checkbox = this;
            const selectedDoneBy = $(checkbox).closest('tr').find('.done-by-select').val();

            if (!selectedDoneBy || selectedDoneBy !== currentUserId) {
                e.preventDefault();
                checkbox.checked = false;

                Swal.fire({
                    icon: 'warning',
                    title: 'Nicht erlaubt',
                    text: 'Nur der zugewiesene Mitarbeiter darf diese Aufgabe als erledigt markieren.',
                });
            }
        });

        $('.done-by-select').each(function () {
            const select = $(this);
            const row = select.closest('tr');
            const checkbox = row.find('.history-checkbox');

            if (checkbox.prop('checked')) {
                select.prop('disabled', true);

                if (!select.next('.unlock-icon').length) {
                    const lock = $('<i class="feather icon-lock ml-1 text-danger cursor-pointer unlock-icon" title="Entsperren?"></i>');
                    select.after(lock);

                    lock.on('click', function () {
                        Swal.fire({
                            title: 'Passwort erforderlich',
                            input: 'password',
                            inputLabel: 'Gib dein Passwort ein',
                            inputAttributes: {
                                autocapitalize: 'off',
                                autocomplete: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Bestätigen',
                            showLoaderOnConfirm: true,
                            preConfirm: (password) => {
                                return fetch('/verify-unlock', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                    },
                                    body: JSON.stringify({
                                        password,
                                        required_role: 'Customer'
                                    })
                                }).then(res => {
                                    if (!res.ok) throw new Error('Fehlgeschlagen');
                                    return res.json();
                                });
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed && result.value.success) {
                                Swal.fire({
                                    title: 'Entsperrt!',
                                    icon: 'success'
                                });
                                select.prop('disabled', false);
                                lock.remove();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Nicht erlaubt',
                                    text: result.value.message || 'Zugriff verweigert.',
                                });
                            }
                        });
                    });
                }
            }
        });

        $('[data-toggle="tooltip"]').tooltip();
    }


    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closePhaseSidebar();
        }
    });

    function closePhaseSidebar() {
        document.getElementById('phaseSidebar').classList.remove('open');
        setTimeout(() => {
            document.dispatchEvent(new Event("DOMContentLoaded"));
        }, 300);
    }


    $(document).on('click', '.change_stage', function () {
        const $btn = $(this);
        const customer_id = $btn.data('customer-id');
        const alternative_id = $btn.data('alternative-id');
        const product_id = $btn.data('product-id');
        const stage = $btn.data('stage');
        const service = $btn.data('service');
        const service_id = $btn.data('service-id');
        const employee_id = $btn.data('employee-id');
        const department_id = $btn.data('department-id');

        Swal.fire({
            title: 'Notiz zur Phase: ' + stage.toUpperCase(),
            html: `<div id="quillEditor" style="height: 200px;"></div>`,
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            didOpen: () => {
                const quill = new Quill('#quillEditor', { theme: 'snow' });
                window.currentQuill = quill;
            },
            preConfirm: () => {
                const description = window.currentQuill.root.innerHTML;

                return fetch(`/lead/kanban/${customer_id}/${alternative_id}/${product_id}/${employee_id}/${service}/${stage}/${service_id}/${department_id}`)
                    .then(response => {
                        if (!response.ok) throw new Error("Backend-Fehler beim Statuswechsel");
                        return response.json();
                    })
                    .then(response => {
                        if (!response.success) throw new Error(response.message || 'Fehler beim Speichern');
                        return $.ajax({
                            url: '/ajax/save-customer-note',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                customer_id,
                                alternative_id,
                                product_id,
                                stage,
                                description,
                            }
                        });
                    })
                    .then(res => {
                        if (!res.success) throw new Error(res.message || 'Notiz konnte nicht gespeichert werden.');
                        Swal.fire('Erfolgreich!', 'Phase und Notiz gespeichert.', 'success').then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.showValidationMessage(`Fehler: ${err.message}`);
                    });
            }
        });
    });
</script>


 

<script>
function changeProductStage(productId, customerId, alternativeId, serviceId) {
    Swal.fire({
        title: 'Phase wirklich ändern?',
        text: 'Ihre Aufgabenhistorie wird anschließend entsprechend Ihrer letzten Aktivität dargestellt.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, weiter',
        cancelButtonText: 'Abbrechen'
    }).then(confirmRes => {
        if (!confirmRes.isConfirmed) return;

        Swal.fire({
            title: 'Phase auswählen',
            input: 'select',
            inputOptions: {
                'lead': 'Lead',
                'offer': 'Angebot',
                'deal': 'Auftrag',
                'project': 'Montage',
                'completed': 'Abgeschlossen',
                'pause': 'Pause',
                'junk': 'Junk'
            },
            inputPlaceholder: 'Neue Phase wählen...',
            showCancelButton: true,
            confirmButtonText: 'Weiter',
        }).then(result => {
            if (!result.isConfirmed || !result.value) return;

            const selectedStage = result.value;

            Swal.fire({
                title: 'Notiz zur Phase',
                html: '<div id="quill-editor" style="height:200px;"></div>',
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                didOpen: () => {
                    window.quill = new Quill('#quill-editor', { theme: 'snow' });
                },
                preConfirm: () => {
                    return window.quill.root.innerHTML;
                }
            }).then(({ isConfirmed, value: note }) => {
                if (!isConfirmed || !note) return;

                const employeeId = window.currentEmployeeId || 0;
                const service = 'complete';
                const departmentId = 0;

                const encodedNote = encodeURIComponent(note);

                const url = `/lead/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/${selectedStage}/${serviceId}/${departmentId}?note=${encodedNote}`;

                fetch(url)
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            Swal.fire('Gespeichert!', res.message, 'success');
                        } else {
                            Swal.fire('Fehler', res.message || 'Unbekannter Fehler', 'error');
                        }
                    })
                    .catch(() => {
                        Swal.fire('Fehler', 'Verbindungsfehler beim Speichern.', 'error');
                    });
            });
        });
    });
}
</script>

 


<script>
document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.querySelector('.phase-sidebar-body');
    if (!sidebar) return;

    // ✅ Reusable save function
    function sendHistoryUpdate(data, row) {
        fetch("{{ route('ajax.save.customer.history') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const initialsCell = row.querySelector('.mark-by-cell');
                if (initialsCell) initialsCell.innerText = res.initials;

                const dateInput = row.querySelector('input[type="date"]');
                if (dateInput && !dateInput.value && res.done_date) {
                    dateInput.value = res.done_date;
                }

                row.classList.add('table-success');
                setTimeout(() => row.classList.remove('table-success'), 2000);
            } else {
                alert("⚠️ Fehler beim Speichern");
            }
        })
        .catch(() => {
            alert("⚠️ AJAX Fehler");
        });
    }

    // ✅ General row-wide change listener (for checkbox, date, note)
    sidebar.addEventListener('change', function (e) {
        const target = e.target;
        const row = target.closest('tr');
        if (!row) return;

        const checkbox = row.querySelector('.history-checkbox');
        const dateInput = row.querySelector('input[type="date"]');
        const notesTextarea = row.querySelector('textarea');
        const doneBySelect = row.querySelector('.done-by-select');

        const data = {
            activity_id: checkbox.dataset.activityId,
            phase_id: checkbox.dataset.phaseId,
            customer_id: sidebar.dataset.customerId,
            alternative_id: sidebar.dataset.alternativeId,
            product_id: sidebar.dataset.productId,
            service_id: sidebar.dataset.serviceId,
            is_done: checkbox.checked ? 1 : 0,
            done_date: dateInput?.value || null,
            notes: notesTextarea?.value?.trim() || null,
            done_by: doneBySelect?.value || null
        };

        sendHistoryUpdate(data, row);
    });

    // ✅ Manual blur on note field (optional)
    sidebar.querySelectorAll('.note-textarea').forEach(textarea => {
        textarea.addEventListener('blur', function () {
            const row = this.closest('tr');
            const payload = {
                activity_id: this.dataset.activityId,
                phase_id: this.dataset.phaseId,
                customer_id: sidebar.dataset.customerId,
                alternative_id: sidebar.dataset.alternativeId,
                product_id: sidebar.dataset.productId,
                service_id: sidebar.dataset.serviceId,
                notes: this.value
            };
            sendHistoryUpdate(payload, row);
        });
    });

   

});

function closePhaseSidebar() {
    const sidebar = document.querySelector('#phaseSidebar');
    if (sidebar) sidebar.classList.remove('open');
}


</script>
 


<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({
            html: true,
            container: 'body' // Prevents clipping inside modals or containers
        });
    });

</script>


<script>
function uploadActivityFile(input) {
    const form = input.closest('form');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            input.closest('label').classList.add('selected');

            Swal.fire({
                icon: 'success',
                title: 'Upload Erfolgreich',
                text: '📎 ' + data.filename,
                timer: 2000,
                showConfirmButton: false
            });

            // Optional: reload sidebar or part of UI
            // if (typeof reloadSidebar === 'function') reloadSidebar();

        } else {
            Swal.fire({
                icon: 'error',
                title: 'Fehler',
                text: data.message || 'Upload fehlgeschlagen.'
            });
        }
    })
    .catch(err => {
        console.error("Upload error", err);
        Swal.fire({
            icon: 'error',
            title: 'Upload-Fehler',
            text: err.message || 'Ein unerwarteter Fehler ist aufgetreten.'
        });
    });
}
</script> 


<script>
function loadTask(button) {
    const customerId     = button.getAttribute('data-customer-id');
    const alternativeId  = button.getAttribute('data-alternative-id');
    const productId      = button.getAttribute('data-product-id');
    const productListId  = button.getAttribute('data-product-list-id');

    // Store context globally
    lastTaskContext = { customerId, alternativeId, productId, productListId };

    const container = document.getElementById("mainContent");
    container.innerHTML = `<div class="text-center p-3"><span class="spinner-border text-primary"></span></div>`;

    fetch(`/load/task/view?customer_id=${customerId}&alternative_id=${alternativeId}&product_id=${productId}&product_list_id=${productListId}`)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;

            const createTaskBtn = container.querySelector('.create_new_task');
            if (createTaskBtn) {
                createTaskBtn.setAttribute('data-customer-id', customerId);
                createTaskBtn.setAttribute('data-alternative-id', alternativeId);
                createTaskBtn.setAttribute('data-product-id', productId);
                createTaskBtn.setAttribute('data-product-list-id', productListId);
            }

            initSortable();
        })
        .catch(error => {
            console.error(error);
            container.innerHTML = `<div class="alert alert-danger">Fehler beim Laden der Aufgaben</div>`;
        });
}

function loadTaskData(customerId, alternativeId, productId, productListId) {
    const container = document.getElementById("mainContent");
    container.innerHTML = `<div class="text-center p-3"><span class="spinner-border text-primary"></span></div>`;

    fetch(`/load/task/view?customer_id=${customerId}&alternative_id=${alternativeId}&product_id=${productId}&product_list_id=${productListId}`)
        .then(response => response.text())
        .then(html => {
            container.innerHTML = html;

            const createTaskBtn = container.querySelector('.create_new_task');
            if (createTaskBtn) {
                createTaskBtn.setAttribute('data-customer-id', customerId);
                createTaskBtn.setAttribute('data-alternative-id', alternativeId);
                createTaskBtn.setAttribute('data-product-id', productId);
                createTaskBtn.setAttribute('data-product-list-id', productListId);
            }

            initSortable();
        })
        .catch(error => {
            console.error(error);
            container.innerHTML = `<div class="alert alert-danger">Fehler beim Laden der Aufgaben</div>`;
        });
}

$(document).ready(function () {
    function validateForm() {
        let errors = [];
        let taskTitle = $('#task_title').val().trim();
        let dueDate = $('#due_date').val().trim();

        if (!taskTitle) errors.push('Bitte geben Sie einen Aufgabentitel ein.');
        if (!dueDate) errors.push('Bitte wählen Sie ein Fälligkeitsdatum.');

        return errors;
    }

    function submitTaskForm(closeAfterSave) {
        let errors = validateForm();
        if (errors.length > 0) {
            Swal.fire({ icon: 'warning', title: 'Formular ungültig', html: errors.join('<br>') });
            return;
        }

        let formData = $('#task_form').serialize();

        $.ajax({
            type: 'POST',
            url: "{{ route('personal.task.customer.store') }}",
            data: formData,
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Erfolgreich gespeichert!',
                    text: 'Die Aufgabe wurde erfolgreich gespeichert.',
                }).then(() => {
                    $('.new_task').removeClass('active');

                    const { customerId, alternativeId, productId, productListId } = lastTaskContext;
                    loadTaskData(customerId, alternativeId, productId, productListId);
                });

                // Optionally reset form if not closing modal
                if (!closeAfterSave) {
                    $('#task_form')[0].reset();
                }
            },

            error: function (xhr) {
                let errorMsg = xhr.responseJSON?.message || 'Ein Fehler ist aufgetreten.';
                Swal.fire({ icon: 'error', title: 'Fehler', text: errorMsg });
            }
        });
    }

    $('.save-task-close').on('click', function () {
        submitTaskForm(true);
    });

    $('.save-task-continue').on('click', function () {
        submitTaskForm(false);
    });
});

// 🔄 Enable drag-and-drop after content is loaded
function initSortable() {
    document.querySelectorAll('.kanban-column').forEach(col => {
        new Sortable(col, {
            group: 'kanban',
            animation: 150,
            onEnd: function (evt) {
                const taskId = evt.item.dataset.taskId;
                const newStatus = evt.to.dataset.status;

                fetch(`/personal_task/update_status/${taskId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(res => res.json())
                .then(data => console.log('✅ Status aktualisiert:', data));
            }
        });
    });
}

// 🔍 Search filter
function filterTasks() {
    const term = document.getElementById('taskSearchInput').value.toLowerCase();
    document.querySelectorAll('.task-card').forEach(card => {
        const title = card.dataset.title || '';
        card.style.display = title.includes(term) ? 'block' : 'none';
    });
}

// 💬 Toggle comment input
function toggleTaskNote(taskId) {
    const wrapper = document.getElementById(`task-note-wrapper-${taskId}`);
    if (wrapper.style.display === 'none') {
        wrapper.style.display = 'block';
        loadTaskNotes(taskId);
    } else {
        wrapper.style.display = 'none';
    }
}

function submitTaskNote(event, taskId) {
    event.preventDefault();
    const form = event.target;
    const comment = form.comment.value;

    fetch('/ajax/task_note/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ task_id: taskId, comment: comment })
    })
    .then(res => res.json())
    .then(() => {
        form.reset();
        loadTaskNotes(taskId);
    });
}

function loadTaskNotes(taskId) {
    fetch(`/ajax/task_note/list/${taskId}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById(`comment-list-${taskId}`).innerHTML = html;
        });
}

// ✅ Call initSortable on page load in case it's not inside loadTask
document.addEventListener('DOMContentLoaded', () => {
    initSortable();
});


</script>
 

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = document.querySelector('.new_task');

    // 🔁 Use event delegation to catch future .create_new_task buttons
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.create_new_task');
        if (!button) return;

        const customerId    = button.dataset.customerId || '';
        const alternativeId = button.dataset.alternativeId || '';
        const productId     = button.dataset.productId || '';

        // ✅ Fill the hidden input fields inside the form
        document.getElementById('select_customer_id').value = customerId;
        document.getElementById('select_alternative_id').value = alternativeId;
        document.getElementById('select_product_id').value = productId;

        // ✅ Show modal
        modal.classList.add('active');
    });

    // ❌ Close the modal
    document.body.addEventListener('click', function (e) {
        if (e.target.closest('.close_task_window')) {
            modal.classList.remove('active');
        }
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const collapse = document.getElementById('collapseTaskKeys');
    const topEmployeeSection = document.getElementById('task_employee_section');

    $('#collapseTaskKeys').on('show.bs.collapse', function () {
        topEmployeeSection.style.display = 'none';
    });

    $('#collapseTaskKeys').on('hide.bs.collapse', function () {
        topEmployeeSection.style.display = 'block';
    });
});
</script>


<script>
    $(document).ready(function () {
        
        

        // Initialize select2 for existing rows
        initSelect2();  

        // Initialize Select2 for dynamically added rows
        function initSelect2() {
            $('.employee').select2({
                templateResult: formatEmployee,
                templateSelection: formatEmployee,
                escapeMarkup: function (markup) {
                    return markup;
                },
            });
        }

        // Employee formatting for Select2
        function formatEmployee(employee) {
            if (!employee.id) {
                return employee.text;
            }

            const imageUrl = $(employee.element).data('image');
            const employeeName = employee.text;

            const markup = `
                <div style="display: flex; align-items: center;">
                    <img src="${imageUrl}" style="width: 20px; height: 20px; border-radius: 50%; margin-right: 10px;">
                    <span>${employeeName}</span>
                </div>
            `;

            return markup;
        }

      
    });
</script>


 

<script>
    document.addEventListener("DOMContentLoaded", function () {
        let startDateInput = document.getElementById("start_date");
        let dueDateInput = document.getElementById("due_date");
        let dueTimeInput = document.getElementById("due_time");
        let totalDayInput = document.getElementById("total_day");
        let totalTimeInput = document.getElementById("total_time");

        // Make sure employeeOptions is defined globally (inject from backend)
        let employeeOptions = window.employeeOptions || [];

        function calculateTotalDaysAndHours() {
            let startDate = new Date(startDateInput.value);
            let dueDate = new Date(dueDateInput.value);

            if (!startDateInput.value || !dueDateInput.value || isNaN(startDate) || isNaN(dueDate)) {
                totalDayInput.value = "";
                totalTimeInput.value = "";
                return;
            }

            let workHoursPerDay = 24;
            let totalDays = 0;
            let totalWorkingHours = 0;
            let tempDate = new Date(startDate);

            while (tempDate <= dueDate) {
                let day = tempDate.getDay();
                if (day !== 0 && day !== 6) {
                    totalDays++;
                    totalWorkingHours += workHoursPerDay;
                }
                tempDate.setDate(tempDate.getDate() + 1);
            }

            if (dueTimeInput.value) {
                let [dueHour, dueMinute] = dueTimeInput.value.split(":").map(Number);
                let remainingHours = dueHour + (dueMinute > 0 ? 1 : 0);
                let lastDay = new Date(dueDate);
                let lastDayOfWeek = lastDay.getDay();

                while (lastDayOfWeek === 0 || lastDayOfWeek === 6) {
                    lastDay.setDate(lastDay.getDate() + 1);
                    lastDayOfWeek = lastDay.getDay();
                }

                totalWorkingHours -= workHoursPerDay;
                totalWorkingHours += remainingHours;
            }

            totalDayInput.value = totalDays;
            totalTimeInput.value = totalWorkingHours;

            updateTotalDuration();
        }

        function updateTotalDuration() {
            let total = 0;
            $('.task-duration').each(function () {
                let val = parseInt($(this).val()) || 0;
                total += val;
            });

            let allowed = parseInt($('#total_time').val()) || 0;
            let diff = allowed - total;

            $('#key_total_time').text(diff >= 0 ? `${diff} Std` : `Überschreitung um ${Math.abs(diff)} Std!`);

            if (total > allowed) {
                Swal.fire({
                    icon: "error",
                    title: "⚠ Zeitüberschreitung!",
                    text: `Die gesamte Dauer der Aufgaben beträgt ${total} Stunden, überschreitet jedoch die geplanten ${allowed} Stunden.`,
                });
            }
        }

        function initSelect2WithImages(selector) {
            $(selector).select2({
                templateResult: formatEmployeeOption,
                templateSelection: formatEmployeeSelection,
                escapeMarkup: m => m
            });
        }

        function formatEmployeeOption(option) {
            if (!option.id) return option.text;
            let img = $(option.element).data('image') || '/images/gender/male.png';
            return `
                <div class="d-flex align-items-center">
                    <img src="${img}" class="rounded-circle me-1" style="width: 28px; height: 28px; object-fit: cover;">
                    <span>${option.text}</span>
                </div>
            `;
        }

        function formatEmployeeSelection(option) {
            return option.text;
        }

        $(document).ready(function () {
            let keyTaskIndex = $('#key_task tbody tr').length;

            initSelect2WithImages('select[name^="key"][name$="[employee_id][]"]');

            $(document).on('click', '.add-task-steps', function () {
                keyTaskIndex++;
                let rowCount = $('#key_task tbody tr').length;

                let employeeOptionsHtml = employeeOptions.map(emp => {
                    return `<option value="${emp.id}" data-image="${emp.image}">${emp.name} ${emp.lastname}</option>`;
                }).join('');

                let newRow = `
                    <tr>
                        <td>${rowCount + 1}</td>
                        <td><input type="text" name="key[${keyTaskIndex}][task]" class="form-control"></td>
                        <td><input type="number" name="key[${keyTaskIndex}][duration]" class="form-control task-duration"></td>
                        <td>
                            <select name="key[${keyTaskIndex}][employee_id][]" class="form-control employee-select" multiple style="width:100%">
                                ${employeeOptionsHtml}
                            </select>
                        </td>
                        <td><textarea name="key[${keyTaskIndex}][key_description]" class="form-control"></textarea></td>
                        <td>
                            <button type="button" class="btn btn-icon btn-primary add-task-steps"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-icon btn-danger remove-task-steps"><i class="fa fa-minus"></i></button>
                        </td>
                    </tr>
                `;

                let $newRow = $(newRow);
                $('#key_task tbody').append($newRow);
                initSelect2WithImages($newRow.find('.employee-select'));
                updateTotalDuration();

            });

            $(document).on('click', '.remove-task-steps', function () {
                if ($('#key_task tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateRowIndexes();
                    updateTotalDuration();
                } else {
                    Swal.fire({
                        icon: "warning",
                        title: "Achtung",
                        text: "Es muss mindestens ein Aufgabenschritt vorhanden sein.",
                    });
                }
            });

            $(document).on('input', '.task-duration', updateTotalDuration);

            function updateRowIndexes() {
                $('#key_task tbody tr').each(function (index) {
                    $(this).find('td:first').text(index + 1);
                    $(this).find('input, textarea, select').each(function () {
                        let name = $(this).attr('name');
                        if (name) {
                            name = name.replace(/\[\d+]/, `[${index}]`);
                            $(this).attr('name', name);
                        }
                    });
                });
            }

            startDateInput.addEventListener("change", calculateTotalDaysAndHours);
            dueDateInput.addEventListener("change", calculateTotalDaysAndHours);
            dueTimeInput.addEventListener("change", calculateTotalDaysAndHours);

            updateTotalDuration();
        });
    });
</script>

 

<!-- Duplicate time area: end  -->
  
    <!-- Priority Script  -->
<script>
    $(document).ready(function () {
        // Add click event listener to each dropdown-item
        $('#color_drop_down .dropdown-item').on('click', function () {
            // Get the selected color value from the data-value attribute
            const selectedColor = $(this).data('value');

            // Update the hidden input value
            $('#color').val(selectedColor);

            // Update the icon's color
            $('#colorIcon').css('color', selectedColor);
        });

          // Add click event listener to each dropdown-item
            $('#priority_select .dropdown-item').on('click', function () {
                // Get the selected priority value from the data-value attribute
                const selectedPriority = $(this).data('value');

                // Get the selected icon's HTML
                const selectedIcon = $(this).html();

                // Update the hidden input value
                $('input[name="priority"]').val(selectedPriority);

                // Update the button's icon
                $('#priority_select button').html(selectedIcon);
            });
        
    });


</script>

    <!-- Priority Script end  -->



<!-- Deadline Script Toggle: start  -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Get elements
        
        const repeatedButton = document.getElementById('repeated');
        const repeatedArea = document.querySelector('.repeated_area');
        const reminderButton = document.getElementById('reminder_check');
        const reminderArea = document.querySelector('.reminder_area');
        const addCalendarButton = document.getElementById('add_calendar');
        const addCalendarArea = document.getElementById('add_calendar_area');

        

        // Toggle repeated area
        repeatedButton.addEventListener('change', function () {
            if (this.checked) {
                repeatedArea.style.display = 'table-row';
            } else {
                repeatedArea.style.display = 'none';
            }
        });

        // Toggle reminder area
        reminderButton.addEventListener('change', function () {
            if (this.checked) {
                reminderArea.style.display = 'table-row';
            } else {
                reminderArea.style.display = 'none';
            }
        });

 
        // Initially hide all areas
       
        repeatedArea.style.display = 'none';
        reminderArea.style.display = 'none'; 
    });
</script>
 
<!-- Deadline Script Toggle: end  -->
<script>
$(document).ready(function () {
    const $select = $('#customerLeadProductSelect');
    const $switch = $('#customerSwitch');
    const $container = $('#customerSelectContainer');

    // Initialize Select2
    $select.select2({
        placeholder: 'Kunde suchen...',
        ajax: {
            url: '{{ route("lead.product.list.ajax") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { q: params.term };
            },
            processResults: function (data) {
                return {
                    results: data.results.map(function (item) {
                        return {
                            id: item.id,
                            text: item.text,
                            html: item.html,
                            alternative_id: item.alternative_id,
                            product_id: item.product_id
                        };
                    })
                };
            },
            cache: true
        },
        templateResult: function (data) {
            if (data.loading) return data.text;
            return $(data.html);
        },
        templateSelection: function (data) {
            // Fill hidden fields when selected
            if (data.alternative_id) {
                $('#select_alternative_id').val(data.alternative_id);
            }
            if (data.product_id) {
                $('#select_product_id').val(data.product_id);
            }
            return data.text;
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    // Show/hide section based on switch
    $switch.on('change', function () {
        if ($(this).is(':checked')) {
            $container.slideDown();
            $(this).val(1);
        } else {
            $container.slideUp();
            $select.val(null).trigger('change');
            $('#select_alternative_id').val('');
            $('#select_product_id').val('');
            $(this).val(0);
        }
    });

    // Trigger correct state on page load
    if ($switch.is(':checked')) {
        $container.show();
    } else {
        $container.hide();
    }
});
</script>


@endpush