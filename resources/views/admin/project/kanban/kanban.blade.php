 
 
 
           


 
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script>
    const emp_src = "{{ asset('images/employee/') }}";

    const projectStageNames = {
        "new": "Neu",
        "plan": "Planung",
        "process": "Prozess",
        "completed": "Abgeschlossen",
        "junk": "Junk",
        "pause": "Pausiert"
    };

    document.addEventListener("DOMContentLoaded", function () {
        loadProjectKanban();

        document.getElementById("searchButton").addEventListener("click", function () {
            let query = document.getElementById("searchInput").value.trim();
            if (query === "") {
                loadProjectKanban();
            } else {
                searchProjectKanban(query);
            }
        });

        document.getElementById("searchInput").addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                let query = this.value.trim();
                if (query === "") {
                    loadProjectKanban();
                } else {
                    searchProjectKanban(query);
                }
            }
        });
    });

    function loadProjectKanban() {
        fetch('{{ route("project.get.list") }}')
            .then(res => res.json())
            .then(data => renderProjectKanban(data))
            .catch(err => console.error("Fehler beim Laden der Projekte:", err));
    }

    function searchProjectKanban(query) {
        fetch(`/project/search/status?search=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => renderProjectKanban(data))
            .catch(err => console.error("Fehler bei der Suche:", err));
    }

    function allowDrop(event) {
        event.preventDefault();
    }


    function renderProjectKanban(data) {
        let kanbanBoard = document.getElementById("kanban");
        kanbanBoard.innerHTML = "";

        Object.keys(projectStageNames).forEach(stageKey => {
            let stageColumn = document.createElement("div");
            stageColumn.className = "column";
            stageColumn.id = stageKey;
            stageColumn.setAttribute("ondrop", "drop(event)");
            stageColumn.setAttribute("ondragover", "allowDrop(event)");
            stageColumn.innerHTML = `<h3>${projectStageNames[stageKey]}</h3><div class="column-content"></div>`;
            kanbanBoard.appendChild(stageColumn);
        });

        data.forEach(project => {
            let updatedDate = new Date(project.updated_at).toLocaleDateString("de-DE", {
                day: "2-digit", month: "2-digit", year: "numeric"
            });

            let employee = project.employee && project.employee.employee_id
                ? {
                    employee_id: project.employee.employee_id,
                    name: project.employee.name,
                    lastname: project.employee.lastname,
                    image: project.employee.image
                }
                : null;

            let stage = project.stage.toLowerCase();
            if (!projectStageNames[stage]) stage = "new";

            addCard(
                stage,
                project.initial,
                `${project.customer_name} ${project.customer_lastname}`,
                `Email: ${project.email}`,
                `<i class="feather icon-calendar warning"></i> ${updatedDate}`,
                `${project.street}, ${project.postcode}, ${project.city}`,
                project.customer_id,
                project.alternative_id,
                project.product_id,
                project.service,
                employee,
                project.project_id
            );
        });

        document.querySelectorAll(".column").forEach(col => {
            if (!col.querySelector(".card")) {
                col.innerHTML += `<small>Keine Daten</small>`;
            }
        });
    }

    function addCard(columnId, product, customerName, customerDetails, date, address, customerId, alternativeId, productId, service, employee, projectId) {
        let column = document.getElementById(columnId);
        if (!column) return;

        let card = document.createElement("div");
        card.className = "card";
        card.id = "card-" + Math.random().toString(36).substr(2, 9);
        card.draggable = true;
        card.ondragstart = drag;
        card.onclick = (event) => selectCard(event, card);

        let employee_id = employee && employee.employee_id ? employee.employee_id : 0;
        card.setAttribute("data-customer-id", customerId);
        card.setAttribute("data-alternative-id", alternativeId);
        card.setAttribute("data-product-id", productId);
        card.setAttribute("data-service", service);
        card.setAttribute("data-employee-id", employee_id);
        card.setAttribute("data-lead-product-id", projectId);

        let employeeHtml = employee && employee.image
            ? `<ul class="list-unstyled users-list m-0 d-flex align-items-center">
                    <li class="avatar pull-up" data-toggle="tooltip" title="${employee.name} ${employee.lastname}">
                        <img class="media-object rounded-circle" src="${emp_src}/${employee.image}" alt="${employee.name}" height="30" width="30">
                    </li>
               </ul>`
            : `<small>Kein Mitarbeiter zugewiesen</small>`;

        card.innerHTML = `
            <div class="card-header">
                <strong>${customerName}</strong>
                <div class='circle'>${product}</div>
            </div>
            <div>
                <small>${customerDetails}</small><br>
                <small>${date}</small><br>
                <small>${address}</small>
            </div>
            <div class="employeeList">${employeeHtml}</div>
            <div class='card-actions'>
                <button onclick="visitProfile('${customerId}')"><i class="feather icon-eye"></i></button>
                <button onclick="editCard('${card.id}')"><i class="feather icon-edit"></i></button>
                <button onclick="deleteCard('${card.id}')"><i class="feather icon-trash"></i></button>
            </div>
        `;

        column.querySelector(".column-content").appendChild(card);
    }

    function selectCard(event, card) {
        if (event.ctrlKey || event.metaKey) {
            card.classList.toggle("selected");
        } else {
            document.querySelectorAll(".card.selected").forEach(c => c.classList.remove("selected"));
            card.classList.add("selected");
        }
    }

    function drag(event) {
        event.dataTransfer.setData("text", event.target.id);
    }

   function drop(event) {
        event.preventDefault();
        let cardId = event.dataTransfer.getData("text");
        let card = document.getElementById(cardId);
        let column = event.target.closest(".column");

        if (card && column) {
            const newStatus = column.id;
            const projectId = card.getAttribute("data-lead-product-id");

            // Move the card visually
            column.querySelector(".column-content").appendChild(card);
            card.classList.remove("selected");

            // Send new status to backend
            fetch("{{ route('project.change.status') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                },
                body: JSON.stringify({
                    project_id: projectId,
                    new_status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    Swal.fire("Fehler", data.message || "Status konnte nicht geändert werden.", "error");
                }
            })
            .catch(err => {
                console.error("Fehler beim Aktualisieren des Status:", err);
                Swal.fire("Fehler", "Beim Ändern des Status ist ein Fehler aufgetreten.", "error");
            });
        }
    }


    function visitProfile(customerId) {
        window.location.href = `/new_lead_profile/${customerId}`;
    }

    function editCard(cardId) {
        let card = document.getElementById(cardId);
        if (!card) return;
        let customerId = card.getAttribute("data-customer-id");
        let alternativeId = card.getAttribute("data-alternative-id");
        window.location.href = `/new_lead_edit/${customerId}/${alternativeId}`;
    }

    function deleteCard(cardId) {
        let card = document.getElementById(cardId);
        if (!card) return;
        let projectId = card.getAttribute("data-lead-product-id");

        Swal.fire({
            title: "Bist du sicher?",
            text: "Projekt wird dauerhaft gelöscht.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ja, löschen",
            cancelButtonText: "Abbrechen"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `/delete_lead_product/${projectId}`;
            }
        });
    }
</script>


<script>
  function visitProfile(customerId) {
    document.getElementById("project-profile").classList.add("active");
    document.querySelector(".project-profile-overlay").classList.add("active");
  }

  function closeSidebar() {
    document.getElementById("project-profile").classList.remove("active", "fullscreen");
    document.querySelector(".project-profile-overlay").classList.remove("active");
  }

  function toggleMaximizeSidebar() {
    document.getElementById("project-profile").classList.toggle("fullscreen");
  }
</script>
 