  
                
                <style>
                .leave-sidebar {
                    position: fixed;
                    top: 0;
                    right: -400px;
                    width: 400px;
                    height: 100%;
                    background: #fff;
                    box-shadow: -2px 0 10px rgba(0, 0, 0, 0.15);
                    z-index: 9999;
                    transition: right 0.3s ease-in-out;
                    overflow-y: auto;
                }
                .leave-sidebar.active {
                    right: 0;
                }
                #mentionSuggestions li {
                    padding: 5px 10px;
                    cursor: pointer;
                }
                #mentionSuggestions li:hover {
                    background-color: #f1f1f1;
                }
                .note-item p span.mention {
                    background: #e6f3ff;
                    color: #007bff;
                    font-weight: bold;
                }


                /* Button */
                            .action-toggle {
                            background: transparent;
                            border: none;
                            cursor: pointer;
                            padding: 4px;
                            border-radius: 6px;
                            }
                            .action-toggle:hover {
                            background: rgba(0,0,0,0.05);
                            }

                            /* Menu list */
                            .action-list {
                            position: absolute;
                            display: none;
                            min-width: 180px;
                            margin-top: 6px;
                            padding: 6px 0;
                            background: #fff;
                            border: 1px solid #e2e8f0;
                            border-radius: 8px;
                            box-shadow: 0 4px 14px rgba(0,0,0,0.12);
                            z-index: 999;
                            }
                            .action-list.show { display: block; }

                            /* Items */
                            .action-item {
                            display: flex;
                            align-items: center;
                            padding: 6px 14px;
                            font-size: 14px;
                            color: #374151;
                            text-decoration: none;
                            transition: background 0.2s;
                            }
                            .action-item:hover {
                            background: #f3f4f6;
                            }

                </style>



                @if ($errors->leaveForm->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->leaveForm->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
               <div class="row">
                    <div class="col-12">   
                        @if(DB::table('user_rolls')
                            ->where('user_rolls.user_id', '=', auth()->user()->name)
                            ->where('user_rolls.item_id', '=', 'Employee')
                            ->where('user_rolls.is_add', '=', 'on')
                            ->first())
                            <button type="button" class="btn btn-outline-primary  float-right new_leave"  ></i>Anfrage</button>
                        @endif 
                        <!-- Modal for creating a new leave -->
                        <div class="modal fade" id="new_leave_modal" tabindex="-1" role="dialog" data-emp-id="{{ $data->id }}">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">{{ $data->name }} {{ $data->lastname }}</h4>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form class="form-horizontal" method="POST" action="{{ route('leave.store') }}">
                                        @csrf
                                        <div class="modal-body">
                                            <input type="hidden" name="active_tab" value="leave">
                                            <input type="hidden" name="emp_id" value="{{ $data->id }}">

                                            <div class="form-group">
                                                <label>Jahr</label>
                                                <select name="year" id="yearSelect" class="form-control">
                                                    <option value="">Select Year</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Ab Datum</label>
                                                <input type="date" class="form-control leave_start_date" name="start_date">
                                            </div>

                                            <div class="form-group">
                                                <label>Bis Datum</label>
                                                <input type="date" class="form-control leave_end_date" name="end_date">
                                            </div>

                                            <div class="form-group">
                                                <label>Urlaubstage</label>
                                                <input type="number" class="form-control leave_day" name="leave_day">
                                            </div>

                                            <div class="form-group">
                                                <label>Resturlaubstage</label>
                                                <input type="number" class="form-control remaining_day" name="remaining_day">
                                            </div>

                                            <div class="form-group">
                                                <label>Urlaubstage letztes jahr</label>
                                                <input type="number" class="form-control last_year_remainings" name="last_year_remainings" readonly>
                                            </div>

                                            <div class="form-group">
                                                <label>Eingereichte Urlaubstage</label>
                                                <input type="number" class="form-control leave_duration" name="duration">
                                                <label class="duration_label" style="color:red; display:none;">Die Dauer überschreitet die zulässigen Urlaubstage</label>
                                            </div>

                                            <div class="form-group">
                                                <label>Grund</label>
                                                <select class="form-control" name="reason">
                                                    <option value="Urlaub" selected>Urlaub</option>
                                                    <option value="Freizeitausgleich">Freizeitausgleich</option> 
                                                    <option value="Vorjahresurlaub">Vorjahresurlaub</option> 
                                                    <option value="Elternzeit">Elternzeit</option> 
                                                    <option value="Schulung">Schulung</option> 
                                                    <option value="Schule">Schule</option> 
                                                    <option value="Unbezahte Urlaub">Unbezahte Urlaub</option> 
                                                    <option value="Freigeschtilt">Freigeschtilt</option> 
                                                </select>
                                            </div>

                                            <div class="form-group">
                                                <label>Anfrage an</label>
                                                @php
                                                    $departments_id = DB::table('department_positions')
                                                        ->where('employee_id', $data->id)
                                                        ->where('main', 'active')
                                                        ->pluck('department_id')
                                                        ->first();
                                                @endphp 

                                                <input type="hidden" name="department_id" value="{{ $departments_id ?? '' }}">

                                                <select class="form-control request_to" id="employee_leader_select" name="request_to" data-department="{{ $departments_id ?? '' }}" style="width:100%">
                                                    <!-- Options will be dynamically populated via AJAX -->
                                                </select>
                                            </div>


                                            <div class="form-group">
                                                <label>Beschreibung</label>
                                                <textarea name="description" class="form-control"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary save_button">Speichern</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Abbrechen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div> 
                    </div>
               </div>
                
                <div class="row">
                     <div class="col-12"> 
                            <div class="accordion" id="leaveAccordion">
                                @php
                                    $groupedLeaves = $leaves->groupBy(function($leave) {
                                        return \Carbon\Carbon::parse($leave->start_date)->format('Y'); // Group by year
                                    });
                                @endphp

                                @foreach ($groupedLeaves as $year => $yearLeaves)
                                    <div class="card">
                                        <div class="card-header" id="heading{{ $year }}">
                                            <h5 class="mb-0">
                                                <button class="btn btn-link text-dark font-weight-bold primary" type="button" data-toggle="collapse" data-target="#collapse{{ $year }}" aria-expanded="true" aria-controls="collapse{{ $year }}">
                                                <i class="feather icon-arrow-right"></i> {{ $year }}
                                                </button>
                                            </h5>
                                        </div>

                                        <div id="collapse{{ $year }}" class="collapse show" aria-labelledby="heading{{ $year }}" data-parent="#leaveAccordion">
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table">
                                                        <thead>
                                                            <tr>
                                                                <th>Startdatum</th>
                                                                <th>Enddatum</th> 
                                                                <th>Urlaubstage</th> 
                                                                <th>Grund</th> 
                                                                <th>Beschreibung</th> 
                                                                <th>Status</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($yearLeaves as $leave)
                                                                <tr>
                                                                    <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('d.m.Y') }}</td>
                                                                    <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('d.m.Y') }}</td> 
                                                                    <td>{{ $leave->duration}} Tag(e)</td>
                                                                    <td>{{ $leave->reason }}</td>

                                                                    <td>
                                                                        <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#description{{ $leave->id }}"><i class="fa fa-expand"></i></button>

                                                                        <div class="modal fade" id="description{{ $leave->id }}" tabindex="-1" role="dialog" aria-labelledby="descLabel{{ $leave->id }}" aria-hidden="true">
                                                                            <div class="modal-dialog modal-dialog-scrollable">
                                                                                <div class="modal-content">
                                                                                    <div class="modal-header">
                                                                                        <h4 class="modal-title" id="descLabel{{ $leave->id }}">Beschreibung</h4>
                                                                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                                    </div>
                                                                                    <div class="modal-body">
                                                                                        <p>{{ $leave->description }}</p>
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </td>
        
                                                                    <td>
                                                                    <p>
                                                                            @if($leave->status != "Pending")
                                                                            <div class="badge badge-primary"><i class="fa fa-times-circle"></i> Urlaub abgelaufen</div> 
                                                                            @endif
                                                                    </p>
                                                                    <p>
                                                                            @if($leave->approved == "Yes")
                                                                                <div class="badge badge-success"><i class="feather icon-check-square"></i> Genehmigt</div>
                                                                            @else
                                                                                <div class="badge badge-danger"><i class="fa fa-times-circle"></i> nicht genehmigt</div>
                                                                            @endif
                                                                    </p>
        
                                                                    </td>

                                                                    <td class="text-nowrap">
                                                                        <!-- Wrapper -->
                                                                            <div class="action-menu" data-id="{{ $leave->id }}">
                                                                            <button class="btn btn-sm action-toggle" type="button">
                                                                                <i class="feather icon-more-vertical"></i>
                                                                            </button>

                                                                            <div class="action-list">
                                                                                <!-- Delete -->
                                                                                <a class="action-item text-danger delete-leave" href="javascript:void(0);" data-id="{{ $leave->id }}">
                                                                                <i class="feather icon-trash-2 mr-1"></i> Löschen
                                                                                </a>

                                                                                <!-- Edit -->
                                                                                <a class="action-item text-warning" href="#" data-toggle="modal" data-target="#leave_edit{{ $leave->id }}">
                                                                                <i class="feather icon-edit mr-1"></i> Bearbeiten
                                                                                </a>

                                                                                <!-- Approve -->
                                                                                @if($leave->approved != "Yes")
                                                                                <a class="action-item text-primary" href="{{ url('leave_approve/'.$leave->id) }}">
                                                                                <i class="feather icon-check-square mr-1"></i> Genehmigen
                                                                                </a>
                                                                                @endif

                                                                                <!-- Conflict Check -->
                                                                                <a class="action-item check-leave text-info" href="javascript:void(0);"
                                                                                data-id="{{ $leave->id }}"
                                                                                data-start-date="{{ $leave->start_date }}"
                                                                                data-end-date="{{ $leave->end_date }}"
                                                                                data-employee-id="{{ $leave->emp_id }}">
                                                                                <i class="feather icon-calendar mr-1"></i> Konflikt prüfen
                                                                                </a>

                                                                                <!-- Notes -->
                                                                                <a class="action-item leave-notes text-primary" href="javascript:void(0);" data-id="{{ $leave->id }}">
                                                                                <i class="feather icon-file-text mr-1"></i> Notizen
                                                                                </a>
                                                                            </div>
                                                                            </div>

                                                                    </td>

                                                                </tr>

                                                                <!-- Leave Edit Modal -->
                                                                <div class="modal fade leave_edit" id="leave_edit{{ $leave->id }}" tabindex="-1" role="dialog" aria-labelledby="editLabel{{ $leave->id }}" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-scrollable">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="editLabel{{ $leave->id }}">Bearbeiten</h4>
                                                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                                            </div>
                                                                            <div class="modal-body"> 

                                                                                    <form method="post" action="{{ route('leave.update') }}">
                                                                                    @csrf 

                                                                                    <div class="modal-body"> 
                                                                                        <input type="hidden" name="active_tab" id="active_tab" value="leave">  
                                                                                        <input type="hidden" name="id"   value="{{ $leave->id }}">  
                                                                                        <input type="hidden" name="emp_id"   value="{{ $data->id }}">  
                                                                                        <div class="row">  
                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Ab Datum</label>
                                                                                                    <input type="date" class="form-control required leave_start_date" placeholder="Startdatum" name="start_date" value="{{ old('start_date', $leave->start_date) }}" id="leave_start_date">
                                                                                                    @if ($errors->has('start_date'))<p style="color:red;">{!! $errors->first('start_date') !!}</p>@endif
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="col-md-6">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Bis Datum</label>
                                                                                                    <input type="date" class="form-control required leave_end_date" placeholder="Endtermin" name="end_date" value="{{ old('end_date', $leave->end_date) }}" id="leave_end_date">
                                                                                                    @if ($errors->has('end_date'))<p style="color:red;">{!! $errors->first('end_date') !!}</p>@endif
                                                                                                </div>
                                                                                            </div>

                                                                                                <div class="col-md-6" id="personal">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Urlaubstage</label> 
                                                                                                    <input type="number" class="form-control required leave_day" value="{{ $leave->leave_day }}" id="leave_day" name="leave_day" readonly>
                                                                                                </div>
                                                                                            </div> 

                                                                                            <div class="col-md-6" id="personal_remain">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Verbleibende Tage</label>
                                                                                                    <input type="number" class="form-control required remaining_day" name="remaining_day" id="remaining_day" value="" style="cursor: not-allowed;">
                                                                                                </div>
                                                                                            </div> 

                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">Dauer (Tag)</label>
                                                                                                    <input type="number" class="form-control required leave_duration" placeholder="Dauer" name="duration" value="{{ old('duration', $leave->duration) }}" id="leave_duration">
                                                                                                    @if ($errors->has('duration'))<p style="color:red;">{!! $errors->first('duration') !!}</p>@endif
                                                                                                    <label id="duration_label" style="color:red; display:none;">Die Dauer überschreitet die zulässigen Urlaubstage</label>
                                                                                                </div>
                                                                                            </div> 
                                                                                            <div class="col-md-12">
                                                                                                <label for="Title">Grund</label>
                                                                                                <select class="form-control" name="reason">
                                                                                                    <option selected value="Persönlicher Urlaub" @if($leave->reason == "Persönlicher Urlaub" ) selected @endif >Persönlicher Urlaub</option>
                                                                                                    <option value="Jahresurlaub" @if($leave->reason == "Jahresurlaub" ) selected @endif>Jahresurlaub</option> 
                                                                                                    <option value="Elternzeit" @if($leave->reason == "Elternzeit" ) selected @endif>Elternzeit</option>
                                                                                                    <option value="Trauerurlaub" @if($leave->reason == "Trauerurlaub" ) selected @endif>Trauerurlaub</option> 
                                                                                                </select>
                                                                                            </div> 
                                                                                            <div class="col-md-12">
                                                                                                <label for="Title">Beschreibung</label>
                                                                                                <fieldset class="form-group">
                                                                                                    <textarea name="description" class="form-control" required>{{ old('description', $leave->description) }}</textarea>
                                                                                                </fieldset>
                                                                                            </div>

                                                                                        </div> 
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="submit" class="btn btn-primary">speichern</button>
                                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal" >abbrechen</button>
                                                                                    </div>
                                                                                </form> 
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <!-- End Leave Edit Modal -->
                                                                
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                       </div>
                </div>
                 

                <div id="leaveNotesSidebar" class="leave-sidebar p-3">
                    <!-- Sidebar Header -->
                    <div class="sidebar-header d-flex justify-content-between align-items-center mb-2" style="    background: #8fc73e;">
                        <h5><i class="feather icon-edit-3"></i> Notizen</h5>
                        <button onclick="closeLeaveSidebar()" class="btn btn-sm btn-danger">×</button>
                    </div>

                    <!-- Existing Notes -->
                    <div id="leaveNotesContent" class="mb-3"></div>

                    <!-- New Note Input -->
                    <div class="position-relative">
                        <textarea id="newNoteText" class="form-control mb-2" rows="3" placeholder="Neue Notiz..."></textarea>
                        <ul id="mentionSuggestions" class="list-group position-absolute bg-white border" style="top: 100%; left: 0; width: 100%; z-index: 9999; display: none;"></ul>
                    </div>

                    <!-- Save Button -->
                    <button class="btn btn-primary btn-block mt-2" onclick="saveLeaveNote()">💾 Speichern</button>
                </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- Include SweetAlert -->
 

<script>
   document.addEventListener("DOMContentLoaded", function () {
    
    function submitLeaveRequest() {
        const modal = document.querySelector("#new_leave_modal");
        const form = modal.querySelector("form.form-horizontal");
        const saveButton = modal.querySelector(".save_button");

        saveButton.addEventListener("click", function (event) {
            event.preventDefault(); // Prevent default form submission

            let formData = new FormData(form);
            let empId = formData.get("emp_id");
            let startDate = formData.get("start_date");
            let endDate = formData.get("end_date");
            let requestTo = formData.get("request_to");

            if (!startDate || !endDate || !requestTo) {
                Swal.fire({
                    title: "Fehlende Angaben",
                    text: "Bitte füllen Sie alle erforderlichen Felder aus!",
                    icon: "warning",
                    confirmButtonText: "Okay"
                });
                return;
            }

            // Convert form data to JSON object
            let jsonData = {};
            formData.forEach((value, key) => {
                jsonData[key] = value;
            });

            console.log("📡 Sending AJAX request:", jsonData);

            fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server Error: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("✅ Leave request saved:", data);
                
                if (data.success) {
                    // ✅ Store active tab in SESSION via AJAX
                    fetch("{{ route('setActiveTab') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({ active_tab: "leave" }) // Store "leave" in session
                    })
                    .then(() => {
                        Swal.fire({
                            title: "Erfolgreich!",
                            text: "Ihr Urlaubsantrag wurde erfolgreich eingereicht.",
                            icon: "success", 
                        }).then(() => {
                            $("#new_leave_modal").modal("hide"); // Close modal
                            form.reset(); // Reset form fields
                            location.reload(); // ✅ Reload the page
                        });
                    });
                }
            })
            .catch(error => {
                console.error("❌ Error saving leave request:", error);
                Swal.fire({
                    title: "Fehler!",
                    text: "Es gab ein Problem beim Speichern des Urlaubsantrags.",
                    icon: "error",
                    confirmButtonText: "Okay"
                });
            });
        });
    }

    submitLeaveRequest(); // Initialize AJAX form submission
});


</script>

<script>  
document.addEventListener("DOMContentLoaded", function () {
    
    function populateYearDropdown() {
        const yearSelect = document.getElementById("yearSelect");
        const currentYear = new Date().getFullYear();
        yearSelect.innerHTML = ""; 
        
        for (let i = currentYear - 5; i <= currentYear + 1; i++) {
            let option = document.createElement("option");
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
        }
        yearSelect.value = currentYear;
    }

    function calculateWorkingDays(startDate, endDate) {
        let start = new Date(startDate);
        let end = new Date(endDate);
        let count = 0;

        while (start <= end) {
            let dayOfWeek = start.getDay();
            if (dayOfWeek !== 0 && dayOfWeek !== 6) {
                count++;
            }
            start.setDate(start.getDate() + 1);
        }
        return count;
    }

    function updateDurationAndRemainingDays(modal) {
        if (!modal) {
            console.error("❌ Modal not found.");
            return;
        }

        const startDateInput = modal.querySelector(".leave_start_date");
        const endDateInput = modal.querySelector(".leave_end_date");
        const durationInput = modal.querySelector(".leave_duration");
        const leaveDayInput = modal.querySelector(".leave_day");
        const remainingDayInput = modal.querySelector(".remaining_day");
        const durationLabel = modal.querySelector("#duration_label");
        const saveButton = modal.querySelector(".save_button");

        if (!startDateInput || !endDateInput || !leaveDayInput || !remainingDayInput || !durationInput) {
            console.error("❌ One or more form fields not found in modal.");
            return;
        }

        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        let leaveDays = parseInt(leaveDayInput.value) || 0;

        if (startDate && endDate) {
            const workingDays = calculateWorkingDays(startDate, endDate);
            durationInput.value = workingDays;

            console.log("🚀 Checking Leave Request:");
            console.log("Total Leave Days:", leaveDays);
            console.log("Requested Duration:", workingDays);

            // ✅ Proper calculation: remaining leave days = leave_days - requested duration
            let remainingDays = leaveDays - workingDays;
            remainingDayInput.value = remainingDays >= 0 ? remainingDays : 0;

            // ✅ Ensure `durationLabel` and `saveButton` exist before modifying them
            if (durationLabel) {
                durationLabel.style.display = workingDays > leaveDays ? "block" : "none";
            }
            if (saveButton) {
                saveButton.style.display = workingDays > leaveDays ? "none" : "block";
            }

            // ✅ Show alert if leave request is too large
            if (workingDays > leaveDays) {
                Swal.fire({
                    title: "Achtung!",
                    text: "Sie haben mehr Urlaubstage als erlaubt beantragt. Zusätzliche Tage werden von Ihrem Gehalt abgezogen!",
                    icon: "warning",
                    confirmButtonText: "Verstanden"
                });
            }
        }
    }

    function fetchRemainingLeaveDays(empId, year, modal) {
        if (!empId) {
            console.error("❌ Employee ID is missing.");
            return;
        }

        console.log(`📡 Fetching remaining leave days for empId: ${empId}, Year: ${year}`);

        fetch(`/employee/remaining/days/${empId}?year=${year}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server Error: ${response.status} ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("✅ API Response:", data);

                if (data.error) {
                    console.error("❌ Error fetching leave data:", data.error);
                    return;
                }

                const leaveDayInput = modal.querySelector(".leave_day");
                const remainingDayInput = modal.querySelector(".remaining_day");

                if (!leaveDayInput || !remainingDayInput) {
                    console.error("❌ Form fields not found in modal.");
                    return;
                }

                // ✅ Update leave days and remaining days in edit modal
                leaveDayInput.value = data.total_leave_days || 0;
                remainingDayInput.value = data.remaining_days || 0;

                console.log("📌 Updated Form Fields:");
                console.log("Total Leave Days:", leaveDayInput.value);
                console.log("Remaining Days:", remainingDayInput.value);
            })
            .catch(error => {
                console.error("❌ Error fetching leave data:", error);
            });
    }

    function openLeaveModal() {
        const modal = document.querySelector("#new_leave_modal");

        if (!modal) {
            console.error("❌ Modal #new_leave_modal not found.");
            return;
        }

        console.log("✅ Opening modal: #new_leave_modal");

        $("#new_leave_modal").modal("show");
    }

    document.querySelectorAll(".new_leave").forEach(button => {
        button.addEventListener("click", function () {
            const modal = document.querySelector("#new_leave_modal");
            const empId = modal.getAttribute("data-emp-id");
            const year = document.getElementById("yearSelect").value;
            if (!empId || !year) {
                console.error("❌ Employee ID or Year is missing.");
                return;
            }
            fetchRemainingLeaveDays(empId, year, modal);
            openLeaveModal();
        });
    });

    document.querySelectorAll(".modal").forEach(modal => {
        modal.addEventListener("change", function (event) {
            if (
                event.target.classList.contains("leave_start_date") || 
                event.target.classList.contains("leave_end_date")
            ) {
                updateDurationAndRemainingDays(modal);
            }
        });
    });

    // ✅ Handle edit modal opening event - FIXED REMAINING DAYS IN EDIT MODAL
    document.querySelectorAll(".leave_edit").forEach(modal => {
        modal.addEventListener("show.bs.modal", function () {
            const empId = modal.getAttribute("data-emp-id");
            const year = new Date().getFullYear();
            if (empId) {
                fetchRemainingLeaveDays(empId, year, modal);
            }
        });

        modal.querySelectorAll(".leave_start_date, .leave_end_date").forEach(input => {
            input.addEventListener("change", function () {
                updateDurationAndRemainingDays(modal);
            });
        });
    });

    populateYearDropdown();
});

</script>


<script>
    $(document).ready(function () {
        $('.delete-leave').on('click', function () {
            var leaveId = $(this).data('id');
            var row = $(this).closest('tr');

            Swal.fire({
                title: "Bist du sicher?",
                text: "Diese Aktion kann nicht rückgängig gemacht werden!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ja, löschen!",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/leave_delete/" + leaveId,
                        type: "DELETE",
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            Swal.fire({
                                title: "Gelöscht!",
                                text: response.message,
                                icon: "success",
                                timer: 2000,
                                showConfirmButton: false
                            });

                            row.fadeOut(500, function () {
                                $(this).remove();
                            });
                        },
                        error: function () {
                            Swal.fire({
                                title: "Fehler!",
                                text: "Etwas ist schief gelaufen. Bitte versuche es erneut.",
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<script>
   const path_image = "{{ asset('images/employee/')}}";

</script>
 
 <script>
   $(document).ready(function () {


    // Initialize Select2
    $('#employee_leader_select').select2({
        placeholder: "Abteilungsleiter",
        allowClear: true,
        width: '100%',
        templateResult: formatEmployee,
        templateSelection: formatEmployee
    });

    // Only run the department check when the modal is opened
    $('#new_leave_modal').on('shown.bs.modal', function () {
        let department_id = $('#employee_leader_select').attr('data-department');

        if (!department_id) {
            Swal.fire({
                title: "Warnung!",
                text: "Bitte geben Sie die Hauptfunktion dieses Mitarbeiters und die Abteilung an.",
                icon: "warning"
            });
            return; // Stop execution if no department is assigned
        }

        checkDepartmentLeader(department_id);
    });

    function checkDepartmentLeader(department_id) {
        $.ajax({
            url: `/getDepartment/leader/${department_id}`,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.length === 0) {
                    Swal.fire({
                        title: "Konfiguration erforderlich!",
                        text: "Bitte konfigurieren Sie einen Abteilungsleiter, bevor Sie mit diesem Abschnitt fortfahren.",
                        icon: "error",
                        confirmButtonText: "Zur Abteilungsorganisation",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "/department_organization";
                        }
                    });
                } else {
                    // Populate select with department leader data
                    $('#employee_leader_select').empty().append('<option></option>');

                    $.each(response, function (key, employee) {
                        let imageUrl = employee.image ? `${path_image}/${employee.image}` : '/default-avatar.png';
                        let newOption = new Option(
                            `${employee.name} ${employee.lastname}`,
                            employee.emp_id,
                            false,
                            false
                        );
                        $(newOption).attr('data-img', imageUrl);
                        $('#employee_leader_select').append(newOption);
                    });

                    $('#employee_leader_select').trigger('change');
                }
            },
            error: function (xhr) {
                console.error("Error fetching department leader:", xhr.responseText);
            }
        });
    }

    // Select2 custom rendering to show image
    function formatEmployee(employee) {
        if (!employee.id) {
            return employee.text;
        }
        var imageUrl = $(employee.element).attr("data-img") || "/default-avatar.png";
        var $employee = $(
            `<span><img src="${imageUrl}" class="rounded-circle" width="30" height="30" style="margin-right:10px;"> ${employee.text}</span>`
        );
        return $employee;
    }
});

 </script>


<script>
   $(document).on('click', '.check-leave', function () {
    const employeeId = $(this).data('employee-id');
    const startDate = $(this).data('start-date');
    const endDate = $(this).data('end-date');

    $.ajax({
        url: `/check/department-holidays/${employeeId}/${startDate}/${endDate}`,
        type: 'GET',
        success: function (data) {
            let html = `
            <div class="row" style="display:flex; gap:10px;">
                <!-- Conflict Column -->
                <div class="col" style="flex:1; max-height:420px; overflow-y:auto;">
                    <h6 class="text-danger"><strong>${data.conflict_count}</strong> im Urlaub</h6>
                    <ul class="list-group">`;

            data.conflicts.forEach(item => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex align-items-start">
                            <img src="/images/employee/${item.image}" class="rounded-circle mr-2" width="50" height="50">
                            <div>
                                <strong>${item.name} ${item.lastname}</strong><br>
                                <small>${item.position} – ${item.department_name}</small><br>
                                <small>📅 ${item.start_date} → ${item.end_date}</small><br>
                                <span class="badge badge-${getStatusColor(item.status)}">${item.status}</span>
                            </div>
                        </div>
                    </li>`;
            });

            html += `</ul></div>`;

            // Present Column
            html += `
                <div class="col" style="flex:1; max-height:420px; overflow-y:auto;">
                    <h6 class="text-success"><strong>${data.present_count}</strong> anwesend</h6>
                    <ul class="list-group">`;

            data.present.forEach(item => {
                html += `
                    <li class="list-group-item">
                        <div class="d-flex align-items-start">
                            <img src="/images/employee/${item.image}" class="rounded-circle mr-2" width="50" height="50">
                            <div>
                                <strong>${item.name} ${item.lastname}</strong><br>`;
                item.departments.forEach(dep => {
                    html += `<small>${dep.position} – ${dep.department_name}</small><br>`;
                });
                html += `</div></div></li>`;
            });

            html += `</ul></div>`;

            // Calendar
            html += `
                <div class="col text-center" style="flex:1;">
                    <h6 class="mb-2">Kalender</h6>
                    <div id="leave-calendar"></div>
                </div>
            </div>`;

            Swal.fire({
                title: 'Abteilungsübersicht',
                html: html,
                width: '95%',
                didOpen: () => {
                    new Litepicker({
                        element: document.getElementById('leave-calendar'),
                        inlineMode: true,
                        singleMode: false,
                        showTooltip: false,
                        startDate: startDate,
                        endDate: endDate,
                        numberOfMonths: 1,
                        numberOfColumns: 1
                    });
                },
                confirmButtonText: 'Schließen'
            });
        },
        error: function () {
            Swal.fire('Fehler', 'Daten konnten nicht geladen werden.', 'error');
        }
    });

    function getStatusColor(status) {
        switch(status.toLowerCase()) {
            case 'approved': return 'success';
            case 'pending': return 'warning';
            case 'rejected': return 'danger';
            default: return 'secondary';
        }
    }
});


</script>

<script>
let currentLeaveId = null;
let employeesList = [];

// Fetch employee usernames once
fetch('/get-employee-usernames')
    .then(res => res.json())
    .then(data => employeesList = data);

// -------------------------------
// 🟢 OPEN + CLOSE SIDEBAR
// -------------------------------
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.leave-notes').forEach(btn => {
        btn.addEventListener('click', function () {
            currentLeaveId = this.dataset.id;
            document.getElementById('leaveNotesSidebar').classList.add('active');
            loadLeaveNotes();
        });
    });
});

function closeLeaveSidebar() {
    document.getElementById('leaveNotesSidebar').classList.remove('active');
    currentLeaveId = null;
}

// -------------------------------
// 📦 LOAD NOTES
// -------------------------------
function loadLeaveNotes() {
    fetch(`/leaves/${currentLeaveId}/notes`)
        .then(res => res.json())
        .then(notes => renderLeaveNotes(notes))
        .catch(err => console.error('Fehler beim Laden der Notizen:', err));
}

// -------------------------------
// ✏️ RENDER NOTES
// -------------------------------
function renderLeaveNotes(notes) {
    const content = document.getElementById('leaveNotesContent');
    content.innerHTML = '';

    if (!Array.isArray(notes)) notes = [];

    const baseUrl = window.location.origin; // 🔥 get the domain like https://example.com

    notes.forEach((note, index) => {
        const image = note.image ? `${baseUrl}/images/employee/${note.image}` : `${baseUrl}/images/gender/male.png`;

        content.innerHTML += `
            <div class="note-item border p-2 mb-2 d-flex">
                <img src="${image}" 
                    alt="${note.employee}" 
                    class="rounded-circle mr-2" 
                    style="width: 40px; height: 40px; object-fit: cover;">
                <div class="flex-grow-1">
                    <small><strong>${note.employee}</strong> - ${note.date}</small>
                    <p class="mb-1">${note.text}</p>
                    <button class="btn btn-sm btn-warning" onclick="editLeaveNote(${index})"><i class="feather icon-edit"></i></button>
                    <button class="btn btn-sm btn-danger" onclick="deleteLeaveNote(${index})"><i class="feather icon-trash"></i></button>
                </div>
            </div>`;
    });
}

// -------------------------------
// 💾 SAVE NOTE
// -------------------------------
function saveLeaveNote() {
    const text = document.getElementById('newNoteText').value;
    if (!text.trim()) return;

    fetch(`/leaves/${currentLeaveId}/notes/store`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ text })
    })
    .then(res => res.json())
    .then(data => {
        document.getElementById('newNoteText').value = '';
        renderLeaveNotes(data.notes);
    });
}

// -------------------------------
// ❌ DELETE NOTE
// -------------------------------
function deleteLeaveNote(index) {
    Swal.fire({
        title: 'Löschen?',
        text: 'Diese Notiz wirklich entfernen?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ja, löschen',
        cancelButtonText: 'Abbrechen'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(`/leaves/${currentLeaveId}/notes/delete/${index}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => renderLeaveNotes(data.notes));
        }
    });
}

// -------------------------------
// 📝 EDIT NOTE
// -------------------------------
function editLeaveNote(index) {
    const newText = prompt("Neue Notiz eingeben:");
    if (!newText) return;

    fetch(`/leaves/${currentLeaveId}/notes/update/${index}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ text: newText })
    })
    .then(res => res.json())
    .then(data => renderLeaveNotes(data.notes));
}

// -------------------------------
// 🧠 @MENTION AUTOCOMPLETE
// -------------------------------
document.getElementById('newNoteText').addEventListener('input', function () {
    const textarea = this;
    const value = textarea.value;
    const caretPos = textarea.selectionStart;
    const mentionMatch = value.substring(0, caretPos).match(/@([\w\.]*)$/);
    const suggestionBox = document.getElementById('mentionSuggestions');

    if (mentionMatch) {
        const searchTerm = mentionMatch[1].toLowerCase();
        const matches = employeesList.filter(name => name.toLowerCase().includes(searchTerm)).slice(0, 5);

        suggestionBox.innerHTML = '';
        matches.forEach(name => {
            const li = document.createElement('li');
            li.textContent = name;
            li.className = 'list-group-item';
            li.onclick = () => {
                const newVal = value.substring(0, caretPos - mentionMatch[0].length) + `@${name} ` + value.substring(caretPos);
                textarea.value = newVal;
                textarea.focus();
                suggestionBox.style.display = 'none';
            };
            suggestionBox.appendChild(li);
        });

        const rect = textarea.getBoundingClientRect();
        suggestionBox.style.top = `${rect.top + window.scrollY + textarea.offsetHeight}px`;
        suggestionBox.style.left = `${rect.left + 10}px`;
        suggestionBox.style.display = 'block';
    } else {
        suggestionBox.style.display = 'none';
    }
});
</script>


<script>
    $(document).ready(function() {
  // Toggle menu
  $(document).on('click', '.action-toggle', function(e) {
    e.stopPropagation();
    const $menu = $(this).siblings('.action-list');
    $('.action-list').not($menu).removeClass('show'); // close others
    $menu.toggleClass('show');
  });

  // Close on outside click
  $(document).on('click', function() {
    $('.action-list').removeClass('show');
  });

  // Example actions
  $(document).on('click', '.delete-leave', function() {
    const id = $(this).data('id');
    alert('Delete leave with ID: ' + id);
    $('.action-list').removeClass('show');
  });

  $(document).on('click', '.check-leave', function() {
    const id = $(this).data('id');
    const start = $(this).data('start-date');
    const end = $(this).data('end-date');
    alert(`Checking conflict for leave ${id} (${start} → ${end})`);
    $('.action-list').removeClass('show');
  });

  $(document).on('click', '.leave-notes', function() {
    const id = $(this).data('id');
    alert('Show notes for leave ID: ' + id);
    $('.action-list').removeClass('show');
  });
});

</script>

@endpush