@extends('admin.layouts.app')
@section('title') Problem Details @stop

@section('style')
<style>
    .customer_names:hover {
        color:#8fc73f;
    }

    .customer_names {
        color:#5c5c5c;
    }

    .image-body img {
        max-width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 5px;
    }

    .editable-name:focus {
        outline: none;
        background: #fff6e0;
        border: 1px solid #ffc107;
        padding: 3px 5px;
        border-radius: 4px;
    }

    .image {
        transition: transform 0.2s;
    }

    .image:hover {
        transform: scale(1.02);
    }

    .preview-click:hover {
    opacity: 0.8;
    transition: 0.3s;
}


</style>

<style>
    .kanban-container {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 20px;
        padding: 10px;
        width: 100%;
        scroll-behavior: smooth;
    }

    .column {
        flex: 0 0 350px;
        background: #f8f9fa;
        padding: 0;
        border-radius: 8px;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        height: calc(100vh - 160px); /* Adjust based on your header/footer */
        overflow: hidden;
    }

    .column h3 {
        text-align: center;
        background: #95c11f;
        color: white;
        padding: 8px;
        margin: 0;
        font-size: 16px;
        text-transform: uppercase;
        font-weight: bold;
        position: sticky;
        top: 0;
        z-index: 1;
    }

    .column-content {
        overflow-y: auto;
        padding: 10px;
        flex: 1;
    }

    .cards {
        background: white;
        padding: 15px;
        margin: 10px 0;
        border-left: 5px solid #74b2d4;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        cursor: grab;
        user-select: none;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .cards.selected {
        background-color: #d1ecf1;
        border-left: 5px solid #17a2b8;
    }

    .cards .card-header {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        justify-content: space-between;
        font-size: 13px;
        text-transform: uppercase;
    }

    .cards .circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #b0d5f2;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: bold;
        font-size: 11px;
        position: absolute;
        top: 2px;
        right: 3px;
    }

    .card-actions {
        display: flex;
        justify-content: space-around;
        padding-top: 5px;
    }

    .card-actions button {
        border: none;
        background: none;
        cursor: pointer;
        font-size: 18px;
        color: #b0d5f2;
    }

    .card-actions button:hover {
        color: #94c11f;
    }

    .column#pause h3 {
        color: #ffc107;
        background-color: #fff3cd;
    }

    .column#junk h3 {
        color: #dc3545;
        background-color: #f8d7da;
    }

    /* Optional scrollbar styling */
    .column-content::-webkit-scrollbar {
        width: 6px;
    }

    .column-content::-webkit-scrollbar-thumb {
        background-color: rgba(0, 0, 0, 0.1);
        border-radius: 4px;
    }
</style>
<style>
/* --- Dropdown Styles --- */
.custom-dropdown {
  position: relative;
  display: inline-block;
}

.custom-dropdown button {
  background: none;
  border: none;
  cursor: pointer;
  padding: 6px;
  border-radius: 50%;
  transition: background 0.2s;
}

.custom-dropdown button:hover {
  background: rgba(0,0,0,0.08);
}

.custom-dropdown svg {
  width: 20px;
  height: 20px;
  stroke: #555;
}

.custom-dropdown-menu {
  position: absolute;
  right: 0;
  top: 100%; /* always inside parent */
  margin-top: 6px;
  display: none;
  flex-direction: column;
  min-width: 180px;
  background: #fff;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  z-index: 50; /* not too high, so it stays inside tab container */
}

.custom-dropdown-menu.show {
  display: flex;
}

.custom-dropdown-menu a,
.custom-dropdown-menu button {
  display: flex;
  align-items: center;
  padding: 8px 12px;
  background: transparent;
  border: none;
  width: 100%;
  text-align: left;
  font-size: 14px;
  color: #333;
  cursor: pointer;
  text-decoration: none;
  transition: background 0.2s;
}

.custom-dropdown-menu a:hover,
.custom-dropdown-menu button:hover {
  background: #f5f5f5;
}

.custom-dropdown-menu svg {
  width: 16px;
  height: 16px;
  margin-right: 8px;
}

.ticket-board-card,
.ticket-board-card .card-content {
    overflow: visible !important;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />

@endsection
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
                            <h2 class="content-header-title float-left mb-0">TICKET</h2>
                            <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li> 
                                    <li class="breadcrumb-item active"><a >details</a></li>
                                    
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
                            <div class="card ticket-board-card"> 
                                <div class="card-content">
                                    <div class="card-body"> 
                                       <ul class="nav nav-tabs" role="tablist">
                                            <li class="nav-item">
                                                {{-- Board is NOT active by default --}}
                                                <a class="nav-link" id="home-tab" data-toggle="tab"
                                                href="#home" aria-controls="home" role="tab" aria-selected="false">
                                                    Board
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                {{-- Liste IS active by default --}}
                                                <a class="nav-link active" id="profile-tab" data-toggle="tab"
                                                href="#profile" aria-controls="profile" role="tab" aria-selected="true">
                                                    Liste
                                                </a>
                                            </li>
                                        </ul>

                                        <div class="tab-content">
                                            {{-- Board pane, not active by default --}}
                                            <div class="tab-pane" id="home" aria-labelledby="home-tab" role="tabpanel">
                                                @include('admin.problem.pages.kanban')
                                            </div>

                                            {{-- Liste pane, active by default --}}
                                            <div class="tab-pane active" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                                                @include('admin.problem.pages.view')
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
    <!-- END: Content-->
@stop

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.warning("{{ session('update_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>


<!-- Dynamic showing the respoinsbiel in modal Start -->
<script>
    const allResponsibles = @json($responsible);

    function showResponsibleModal(problemId) {
        const filtered = allResponsibles.filter(r => r.problem_id === problemId);
        const tbody = $('#responsibleTableBody');
        tbody.empty();

        filtered.forEach(person => {
            const row = `
                <tr>
                    <td><img src="/images/employee/${person.rimage}" alt="Avatar" class="rounded-circle" width="40" height="40"></td>
                    <td>${person.rname} ${person.rlastname}</td>
                </tr>
            `;
            tbody.append(row);
        });

        $('#responsibleModal').modal('show');
    }
</script>
<!-- Dynamic showing the respoinsbiel in modal end -->



<script>
    Dropzone.autoDiscover = false;
    let dz;

    $(document).on('click', '.open-gallery', function () {
        let ticketId = $(this).data('id');
        $('#ticket_id').val(ticketId);
        $('#galleryModal').modal('show');

        if (dz) dz.destroy();

        dz = new Dropzone("#dropzoneForm", {
            url: "{{ route('ticket.image.upload') }}",
            paramName: "file",
            maxFilesize: 2,
            acceptedFiles: 'image/*,application/pdf',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function () {
                loadGallery(ticketId);
            }
        });

        loadGallery(ticketId);
    });

    function loadGallery(ticketId) {
        $.get(`/ticket-image/list/${ticketId}`, function (data) {
            let html = '';
            data.forEach(file => {
                let isImage = file.file_type && file.file_type.startsWith('image');

                let preview = isImage
                    ? `<img src="/storage/${file.image}" class="img-thumbnail mb-2 preview-click"
                            data-src="/storage/${file.image}" data-type="image"
                            style="max-width:100%; height:150px; cursor:pointer;">`
                    : `<div class="text-center preview-click"
                            data-src="/storage/${file.image}" data-type="pdf"
                            style="cursor:pointer;">
                            <i class="fa fa-file-pdf fa-3x text-danger mb-2"></i>
                            <div>${file.image_name}</div>
                    </div>`;

                html += `
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            ${preview}
                            <div class="mt-2">
                                <div contenteditable="true"
                                    class="editable-name"
                                    data-id="${file.id}"
                                    id="name-${file.id}"
                                    style="cursor: text; border-bottom: 1px dashed #ccc; display: inline-block; width: 100%;">
                                    ${file.image_name}
                                </div>
                                <button class="btn btn-sm btn-danger mt-2" onclick="deleteFile(${file.id})">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });

            $('#gallery').html(html);
        });
    }


    $(document).on('click', '.preview-click', function () {
        const src = $(this).data('src');
        const type = $(this).data('type');

        let content = '';
        if (type === 'image') {
            content = `<img src="${src}" class="img-fluid" style="max-height:80vh;">`;
        } else if (type === 'pdf') {
            content = `<iframe src="${src}" frameborder="0" style="width:100%; height:80vh;"></iframe>`;
        }

        $('#previewContent').html(content);
        $('#previewModal').modal('show');
    });




    function deleteFile(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will delete this image permanently!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/ticket-image/delete/${id}`,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function () {
                        $('#name-' + id).parent().remove();
                        Swal.fire('Deleted!', '', 'success');
                    }
                });
            }
        });
    }

    // Save rename on Enter or Tab
        $(document).on('keypress', '.editable-name', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent new line
                $(this).blur();     // Trigger save
            }
        });

    // Rename on blur
    $(document).on('blur', '.editable-name', function () {
        const id = $(this).data('id');
        const newName = $(this).text().trim();

        if (!newName) {
            $(this).text('Unnamed File');
            return;
        }

        $.post(`/ticket-image/rename/${id}`, {
            _token: '{{ csrf_token() }}',
            name: newName
        }).done(() => {
            console.log('Renamed successfully!');
        }).fail(() => {
            alert('Rename failed. Please try again.');
        });
    });
</script>

 
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
 

<script>
    $(document).ready(function() {
        $('#source').select2({
            tags: true,
            placeholder: "Quelle auswählen oder neue eingeben",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Keine Ergebnisse gefunden";
                }
            }
        });
    });
</script>

<script>
const emp_src = "{{ asset('images/employee/') }}";
const stageNames = {
    "offen": "Offen",
    "process": "in Bearbeitung",
    "pause": "Pause",
    "end": "Abgeschlossen",
    "junk": "Junk"
};

let selectedCards = new Set();

function allowDrop(event) {
    event.preventDefault();
}

function selectCard(event, card) {
    if (event.ctrlKey || event.metaKey) {
        card.classList.toggle("selected");
        selectedCards.has(card.id) ? selectedCards.delete(card.id) : selectedCards.add(card.id);
    } else {
        document.querySelectorAll(".card.selected").forEach(c => c.classList.remove("selected"));
        selectedCards.clear();
        card.classList.add("selected");
        selectedCards.add(card.id);
    }
}

function drag(event) {
    let selectedArray = Array.from(selectedCards);
    if (!selectedCards.has(event.target.id)) selectedArray = [event.target.id];
    event.dataTransfer.setData("text", JSON.stringify(selectedArray));
}

function drop(event) {
    event.preventDefault();
    let cardIds = JSON.parse(event.dataTransfer.getData("text"));
    let column = event.target.closest(".column");
    let stage = column.id;

    cardIds.forEach(cardId => {
        let card = document.getElementById(cardId);
        if (card) {
            column.appendChild(card);
            card.classList.remove("selected");
            selectedCards.delete(cardId);
            let ticket_id = card.getAttribute("data-ticket-id");

            Swal.fire({
                title: "Ticket verschieben?",
                text: `Möchten Sie dieses Ticket nach "${stageNames[stage]}" verschieben?`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ja",
                cancelButtonText: "Nein"
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/ticket/kanban/update/${ticket_id}/${stage}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire("Erfolg!", "Ticket wurde verschoben.", "success");
                                loadKanban(); // Reload after move
                            } else {
                                Swal.fire("Fehler", data.message || "Aktion fehlgeschlagen.", "error");
                            }
                        })
                        .catch(error => {
                            console.error("Update-Fehler:", error);
                            Swal.fire("Fehler", "Serverfehler beim Aktualisieren.", "error");
                        });
                }
            });
        }
    });
}


function addCard(
        stage,
        product,
        name,
        emailInfo,
        updatedAt,
        address,
        customerId,
        alternativeId,
        productId,
        employees,
        ticketId,
        errors = []
    ) {
        const columnContent = document.getElementById(`content-${stage}`);
        if (!columnContent) return;

        const card = document.createElement("div");
        card.className = "cards";
        card.id = "cards-" + Math.random().toString(36).substr(2, 9);
        card.draggable = true;
        card.ondragstart = drag;
        card.onclick = (e) => selectCard(e, card);

        card.setAttribute("data-customer-id", customerId);
        card.setAttribute("data-alternative-id", alternativeId);
        card.setAttribute("data-product-id", productId);
        card.setAttribute("data-employee-id", employees?.[0]?.employee_id || 0);
        card.setAttribute("data-ticket-id", ticketId);

        // 🔹 Employee avatars
        let employeeHtml = "";
        if (Array.isArray(employees) && employees.length > 0) {
            employeeHtml += `<ul class="list-unstyled users-list m-0 d-flex align-items-center">`;
            employees.forEach(emp => {
                employeeHtml += `
                    <li class="avatar pull-up mr-1" title="${emp.name} ${emp.lastname}">
                        <img class="media-object rounded-circle" src="${emp_src}/${emp.image}" 
                            alt="${emp.name}" height="30" width="30">
                    </li>`;
            });
            employeeHtml += `</ul>`;
        } else {
            employeeHtml = `<small class="text-muted">Kein Mitarbeiter</small>`;
        }

        // 🔹 Error badges with preview
        let errorHtml = "";
        if (Array.isArray(errors) && errors.length > 0) {
            errorHtml += `<div class="mt-1 d-flex flex-wrap">`;
            errors.forEach(err => {
                const fullHtml = `
                    <div style="font-size: 13px;">
                        <strong>Fehlercode:</strong> ${err.error_code}<br>
                        <strong>Typ:</strong> ${err.problem_types}<br>
                        <strong>Produkt:</strong> ${err.product}<br>
                        <strong>Artikel:</strong> ${err.article_name}<br><hr>
                        <strong>Grund:</strong><br>${err.reason}<br><hr>
                        <strong>Lösung:</strong><br>${err.solution}
                    </div>
                `.replace(/\n/g, '').replace(/'/g, "&apos;");

                errorHtml += `
                    <span class="badge badge-danger mr-50 mb-50 custom-error-tooltip"
                        data-preview-html='${fullHtml}'>
                        ${err.error_code}  
                    </span>`;
            });
            errorHtml += `</div>`;
        }

        // 🔹 Build card HTML
        card.innerHTML = `
            <div class="card-header d-flex justify-content-between">
                <strong>${name}</strong>
                <div class="badge badge-info">${product}</div>
            </div>
            <div class="card-body">
                <small>${emailInfo}</small><br>
                <small>${updatedAt}</small><br>
                <small>${address}</small>
            </div>
            <div class="employeeList mt-1">${employeeHtml}</div>
            <div class="errorList mt-1">${errorHtml}</div>
        `;

        columnContent.appendChild(card);
    }

 

function renderKanban(data) {
    const kanban = document.getElementById("kanban");
    kanban.innerHTML = "";

    Object.keys(stageNames).forEach(stageKey => {
        let column = document.createElement("div");
        column.className = "column";
        column.id = stageKey;
        column.setAttribute("ondrop", "drop(event)");
        column.setAttribute("ondragover", "allowDrop(event)");
        column.innerHTML = `
                <h3>${stageNames[stageKey]}</h3>
                <div class="column-content" id="content-${stageKey}"></div>
            `;

        kanban.appendChild(column);
    });

    data.forEach(ticket => {
        let stage = ticket.stage?.toLowerCase() || "offen"; 
       
        if (!stageNames[stage]) stage = "offen";

        addCard(
            stage,
            ticket.product,
            `${ticket.customer_name} ${ticket.customer_lastname}`,
            `Email: ${ticket.email ?? ''}`,
            `<i class="feather icon-calendar"></i> ${new Date(ticket.updated_at).toLocaleDateString("de-DE")}`,
            `${ticket.street}, ${ticket.postcode}, ${ticket.city ?? ''}`,
            ticket.customer_id,
            ticket.alternative_id,
            ticket.product_id,
            ticket.employees,
            ticket.lead_product_id,
            ticket.errors ?? [] // 👈 Include errors
        );
    });

    // Re-initialize tooltips
    setTimeout(() => {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
            new bootstrap.Tooltip(el);
        });
    }, 300);
}

function loadKanban() {
    fetch("/ticket/kanban/get")
        .then(response => response.json())
        .then(renderKanban)
        .catch(error => console.error("Fehler beim Laden:", error));
}

function searchKanban(search) {
    fetch(`/ticket/kanban/search?search=${encodeURIComponent(search)}`)
        .then(response => response.json())
        .then(renderKanban)
        .catch(error => console.error("Fehler bei Suche:", error));
}

document.addEventListener("DOMContentLoaded", () => {
    loadKanban();

    document.getElementById("searchButton").addEventListener("click", () => {
        let query = document.getElementById("searchInput").value.trim();
        query ? searchKanban(query) : loadKanban();
    });

    document.getElementById("searchInput").addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            e.preventDefault();
            let query = e.target.value.trim();
            query ? searchKanban(query) : loadKanban();
        }
    });
});
</script>
 
<!-- Tooltip Script  -->
<script>
let ctrlPressed = false;
let isDragging = false;
let currentTooltip = null;
let offsetX = 0;
let offsetY = 0;

// Track CTRL key for scrolling
document.addEventListener('keydown', e => {
    if (e.key === 'Control') ctrlPressed = true;
});
document.addEventListener('keyup', e => {
    if (e.key === 'Control') ctrlPressed = false;
});

// Handle wheel scroll inside tooltip
document.addEventListener('DOMContentLoaded', () => {
    const previewBox = document.getElementById('errorPreviewBox');

    if (previewBox) {
        previewBox.addEventListener('wheel', function (e) {
            const isOverflowing = this.scrollHeight > this.clientHeight;
            if (!ctrlPressed && isOverflowing) {
                e.preventDefault();
                this.scrollTop += Math.sign(e.deltaY) * 30;
            }
        }, { passive: false });
    }
});

// Click handling to show/hide tooltip
document.addEventListener('click', function (e) {
    const target = e.target.closest('.custom-error-tooltip');
    const previewBox = document.getElementById('errorPreviewBox');

    if (isDragging) return;

    if (target) {
        e.stopPropagation();
        const html = target.getAttribute('data-preview-html');

        if (currentTooltip === target) {
            previewBox.style.display = 'none';
            currentTooltip = null;
        } else {
            currentTooltip = target;
            previewBox.innerHTML = html;
            previewBox.style.left = e.pageX + 15 + 'px';
            previewBox.style.top = e.pageY + 15 + 'px';
            previewBox.style.display = 'block';
        }
    } else {
        document.getElementById('errorPreviewBox').style.display = 'none';
        currentTooltip = null;
    }
});

// Enable dragging the tooltip
document.addEventListener('mousedown', function (e) {
    const previewBox = document.getElementById('errorPreviewBox');
    if (e.target.closest('#errorPreviewBox')) {
        isDragging = true;
        offsetX = e.clientX - previewBox.offsetLeft;
        offsetY = e.clientY - previewBox.offsetTop;
        previewBox.style.cursor = 'move';
    }
});

document.addEventListener('mousemove', function (e) {
    const previewBox = document.getElementById('errorPreviewBox');
    if (isDragging && previewBox.style.display === 'block') {
        e.preventDefault();
        previewBox.style.left = (e.clientX - offsetX) + 'px';
        previewBox.style.top = (e.clientY - offsetY) + 'px';
    }
});

document.addEventListener('mouseup', function () {
    setTimeout(() => { isDragging = false; }, 100);
    const previewBox = document.getElementById('errorPreviewBox');
    previewBox.style.cursor = 'default';
});
</script>


<!-- Serach function  -->

<script>
    function filterByDate() {
        const date = prompt('Bitte geben Sie ein Datum ein (YYYY-MM-DD):');
        if (date) {
            window.location.href = `{{ route('problem.view') }}?filter=date&date=${date}`;
        }
    }
</script>


<script>
function toggleMenu(btn) {
  const menu = btn.nextElementSibling;

  // Close all other open dropdowns
  document.querySelectorAll(".custom-dropdown-menu.show")
    .forEach(m => { if(m !== menu) m.classList.remove("show"); });

  // Toggle current menu
  menu.classList.toggle("show");

  // Click-away listener
  document.addEventListener("click", function handler(e) {
    if (!btn.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.remove("show");
      document.removeEventListener("click", handler);
    }
  });
}
</script>
@endsection