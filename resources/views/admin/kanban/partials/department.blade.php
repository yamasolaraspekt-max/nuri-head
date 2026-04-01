 <style>
    .department_tables tr td{
        font-size:12px !important;
    }
 </style>
<section id="nav-justified">
    <div class="row">
        <div class="col-sm-12">
            <div class="card overflow-hidden"> 
                <div class="card-content">
                    <div class="card-body"> 
                        <ul class="nav nav-tabs nav-justified" id="myTab2" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab-justified" data-toggle="tab" href="#home-just" role="tab" aria-controls="home-just" aria-selected="true">Abteilung & Jobs</a>
                            </li> 
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content pt-1">
                            <div class="tab-pane active" id="home-just" role="tabpanel" aria-labelledby="home-tab-justified"> 
                                <div class="card" style="margin-bottom:0px">
                                    <div class="card-header">  
                                        <div class="col-md-12">
                                            <div class="button float-right">
                                                <button type="button" class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" data-target="#newDept" data-toggle="modal">erstellen</button> 
                                                <div class="modal fade text-left" id="newDept" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel17">Abteilung dem Mitarbeiter zuweisen</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                        
                                                            <form class="form-horizontal">
                                                                @csrf
                                                                <input type="hidden" name="id" value="{{ request()->id }}">
                                                                <div class="modal-body">
                                                                    <div id="record_table" class="col-12 d-flex flex-wrap p-0">
                                                                        <div class="original-record col-12 d-flex p-0">
                                                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                <label for="department">Abteilung</label>
                                                                                <select class="form-control department-select" name="details[0][department]" style="width:100% !important;">
                                                                                    <option disabled selected>Abteilung auswählen</option>
                                                                                    @foreach ($all_departments as $dept)
                                                                                        <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                <label for="position">Position</label>
                                                                                <select class="form-control position-select" name="details[0][position]" style="width: 100%">
                                                                                    <!-- Options will be loaded dynamically -->
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                <label for="percent">Prozentsatz</label>
                                                                                <input type="number" name="details[0][percent]" class="form-control percent-input" min="1" max="100">
                                                                            </div>

                                                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                <label for="percent">Montage</label>
                                                                                <input type="number" name="details[0][montage_percent]" class="form-control montage-input" min="1" max="100">
                                                                            </div>

                                                                            <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                <label for="percent">Büroanteil</label>
                                                                                <input type="number" name="details[0][office_percent]" class="form-control office-input" min="1" max="100">
                                                                            </div>
 

                                                                            <div class="col-lg-1 col-md-2 col-sm-12">
                                                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary" id="add_record">
                                                                                    <i class="feather icon-plus-square"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                     <div class="col-12 p-0">
                                                                            <div class="col-lg-4 col-md-4 col-sm-12 float-right">
                                                                                <label for="department">Verbleibender Prozentsatz</label> 
                                                                                <input type="number" value="" class="form-control" id="remain_percentage"> 
                                                                            </div>
                                                                        </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                    <button type="button" class="btn btn-primary waves-effect waves-light" id="save_button_dept">speichern</button>
                                                                </div>
                                                            </form> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="card-content">
                                        <div class="card-body" style="padding-bottom: 0px;"> 
                                            <div class="row">
                                                    <div class="col-12">
                                                        <div class="table-responsive">
                                                            <table class="table department_tables">
                                                                <thead>
                                                                    <tr>
                                                                        <th>ID</th> 
                                                                        <th>Abteilung</th>
                                                                        <th>Positionen</th>
                                                                        <th>Kapazität</th>
                                                                        <th>Büroanteil</th> 
                                                                        <th>Bürolohn</th> 
                                                                        <th>Montageanteil</th> 
                                                                        <th>Montagelohn</th> 
                                                                        <th>Stundenanteil</th> 
                                                                        <th>Lohnanteil</th> 
                                                                        <th>Hauptposition</th>
                                                                        <th>Aktion</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                   @foreach ($departments as $dept)
                                                                        @php
                                                                            // Fetch employee positions once
                                                                            $employeePositions = $positions->where('employee_id', $data->id)
                                                                                                            ->where('department_id', $dept->department_id);

                                                                            // Fetch employee salary (only once, before the loop)
                                                                            $salary = DB::table('salaries')
                                                                                        ->where('emp_id', request()->id)
                                                                                        ->value('total_monthly_salary');

                                                                            // Set total working hours per month (40 hours per week * 4 weeks)
                                                                            $total_monthly_hours = (52 * $data->working_hour) / 12; 
                                                                        @endphp

                                                                        @foreach ($employeePositions as $po)
                                                                            @php
                                                                                // Calculate position-specific working hours
                                                                                $position_hours = ($po->percent / 100) * $total_monthly_hours;

                                                                                // Calculate salary contributions based on position percentage
                                                                                if ($salary) {
                                                                                    $office_salary = ($po->office_percent / 100) * $salary;
                                                                                    $montage_salary = ($po->montage_percent / 100) * $salary;
                                                                                    $total_salary = ($po->percent / 100) * $salary;
                                                                                } else {
                                                                                    $office_salary = null;
                                                                                    $montage_salary = null;
                                                                                    $total_salary = null;
                                                                                }
                                                                            @endphp

                                                                            <tr>
                                                                                <th scope="row">{{ $dept->id }}</th> 
                                                                                <td>{{ $dept->department_name }}</td>
                                                                                <td>{{ $po->position }}</td>
                                                                                <td>{{ number_format($po->percent, 0) }}% </td>
                                                                                <td>{{ number_format($po->office_percent, 0) }}% </td>
                                                                                <td>{{ number_format($office_salary, 2, ',', '.') }} €</td>
                                                                                <td>{{ number_format($po->montage_percent, 0) }}% </td>
                                                                                <td>{{ number_format($montage_salary, 2, ',', '.') }} €</td>
                                                                                <td>{{ number_format($position_hours, 2) }}</td> <!-- Correct working hours calculation -->
                                                                                <td>{{ number_format($total_salary, 2, ',', '.') }} €</td> <!-- Correct salary calculation -->
                                                                                <td>{{ $po->main ? 'Ja' : 'Neben' }}</td>

                                                                                <td>
                                                                                    @if($po->main != 'active')
                                                                                        <a type="button" href="{{ url('/employee/department/position/main/'.$po->dp_id.'/'.request()->id) }}"
                                                                                        class="btn btn-icon rounded-circle btn-outline-primary ">
                                                                                            <i class="fa fa-user"></i>
                                                                                        </a> 
                                                                                    @else
                                                                                        <a type="button" href="{{ url('/employee/department/position/main/deactive/'.$po->dp_id.'/'.request()->id) }}"
                                                                                        class="btn btn-icon rounded-circle btn-outline-danger ">
                                                                                            <i class="fa fa-user-times"></i>
                                                                                        </a>  
                                                                                    @endif

                                                                                    <!-- Delete Button -->
                                                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-danger waves-effect waves-light delete-department" 
                                                                                        data-department-id="{{ $dept->department_id }}" 
                                                                                        data-id="{{$po->dp_id }}" 
                                                                                        data-position-id="{{ $po->position_id }}" 
                                                                                        data-employee-id="{{ $dept->employee_id }}">
                                                                                        <i class="feather icon-trash"></i>
                                                                                    </button>

                                                                                    <!-- Edit Button -->
                                                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light edit_department" 
                                                                                        data-toggle="modal" data-target="#deptEdit{{ $dept->id }}">
                                                                                        <i class="feather icon-edit"></i>
                                                                                    </button>


                                                                                     <div class="modal fade text-left" id="deptEdit{{$dept->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <h4 class="modal-title" id="myModalLabel17">Mitarbeiter Abteilung Bearbeiten</h4>
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">×</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                            
                                                                                                <form class="department_edit">
                                                                                                    @csrf
                                                                                                    <input type="hidden" name="id" value="{{ request()->id }}">
                                                                                                    <div class="modal-body">
                                                                                                        <div id="record_table" class="col-12 d-flex flex-wrap p-0">
                                                                                                            <div class="original-record col-12 d-flex p-0">
                                                                                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                                                    <label for="department">Abteilung</label>
                                                                                                                    <select class="form-control department-select" name="details[0][department]" style="width:100% !important;">
                                                                                                                        <option disabled selected>Abteilung auswählen</option>
                                                                                                                        @foreach ($all_departments as $all_dept)
                                                                                                                            <option value="{{ $all_dept->id }}" @if($all_dept->id == $dept->id) selected @endif>{{ $all_dept->department_name }}</option>
                                                                                                                        @endforeach
                                                                                                                    </select>
                                                                                                                </div>

                                                                                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                                                    <label for="position">Position</label>
                                                                                                                    <select class="form-control position-select" name="details[0][position]" style="width: 100%">
                                                                                                                        <!-- Options will be loaded dynamically -->
                                                                                                                    </select>
                                                                                                                </div>

                                                                                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                                                    <label for="percent">Prozentsatz</label>
                                                                                                                    <input type="number" name="details[0][percent]" class="form-control percent-input" min="1" max="100"  value="{{$po->percent}}">
                                                                                                                </div>

                                                                                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                                                    <label for="percent">Montage</label>
                                                                                                                    <input type="number" name="details[0][montage_percent]" class="form-control montage-input" min="1" max="100" value="{{$po->montage_percent}}">
                                                                                                                </div>

                                                                                                                <div class="col-lg-2 col-md-2 col-sm-12">
                                                                                                                    <label for="percent">Büroanteil</label>
                                                                                                                    <input type="number" name="details[0][office_percent]" class="form-control office-input" min="1" max="100" value="{{$po->office_percent}}">
                                                                                                                </div> 
 
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-12 p-0">
                                                                                                                <div class="col-lg-4 col-md-4 col-sm-12 float-right">
                                                                                                                    <label for="department">Verbleibender Prozentsatz</label> 
                                                                                                                    <input type="number" value="" class="form-control" id="remain_percentage"> 
                                                                                                                </div>
                                                                                                            </div>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                                                        <button type="button" class="btn btn-primary waves-effect waves-light" id="update_button_dept">speichern</button>
                                                                                                    </div>
                                                                                                </form> 
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    @endforeach

                                                                    
                                                                   @php
                                                                        // Fetch employee salary
                                                                        $salary = DB::table('salaries')
                                                                                    ->where('emp_id', request()->id)
                                                                                    ->value('total_monthly_salary') ?? 0;

                                                                        // Corrected working hours calculation (weekly to monthly)
                                                                        $total_hour =  (52 * $data->working_hour) / 12; // Weekly hours * 4 = Monthly hours 

                                                                        // Avoid division by zero
                                                                        $total_hour = max($total_hour, 1); // Ensure at least 1 to prevent division by zero

                                                                        // Calculate per-hour salary
                                                                        $per_hour_salary = $salary / $total_hour;

                                                                        // Fetch and sum office, montage, and total percent with COALESCE to prevent NULL values
                                                                        $percent_position = DB::table('department_positions')
                                                                                            ->where('employee_id', request()->id)
                                                                                            ->selectRaw('COALESCE(SUM(office_percent), 0) as total_office_percent, 
                                                                                                        COALESCE(SUM(montage_percent), 0) as total_montage_percent, 
                                                                                                        COALESCE(SUM(percent), 0) as total_percent')
                                                                                            ->first();

                                                                        // Extract values
                                                                        $total_office_percent = $percent_position->total_office_percent;
                                                                        $total_montage_percent = $percent_position->total_montage_percent;
                                                                        $total_percent = $percent_position->total_percent;

                                                                        // Sum total percentage used
                                                                        $total_used_percent = $total_office_percent + $total_montage_percent;

                                                                        // Calculate total salary used based on percentage
                                                                        $used_salary = ($total_used_percent / 100) * ($per_hour_salary * $total_hour);

                                                                        // Calculate office and montage salaries
                                                                        $office_salary = ($total_office_percent / 100) * ($per_hour_salary * $total_hour);
                                                                        $montage_salary = ($total_montage_percent / 100) * ($per_hour_salary * $total_hour);
                                                                    @endphp

                                                                    <tr style="border-top: 4px solid #8fc73e;"> 
                                                                        <td colspan="3">Gesamt</td>
                                                                        <td>{{$total_used_percent}}%</td>
                                                                        <td>{{$total_office_percent}}%</td> 
                                                                         <td>{{ number_format($office_salary, 2, ',', '.') }} €</td>
                                                                        <td>{{$total_montage_percent}}%</td>
                                                                        <td>{{ number_format($montage_salary, 2, ',', '.') }} €</td> 
                                                                        <td>{{ number_format($total_hour, 2, ',', '.') }}</td> <!-- Corrected Total Working Hours -->
                                                                        <td>{{ number_format($used_salary, 2, ',', '.') }} €</td>
                                                                    </tr>
                                                                     
 
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                </div>
                                                 
                                            </div>
                                        </div>
                                    </div> 
                                </div> 
                            </div>
                     
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        // Event listener for department selection
        $('.department-select').on('change', function() {
            var departmentId = $(this).val();
            var empId = {{ request()->id }};
            var $positionsSelect = $(this).closest('tr').find('.position-select');
            loadPositions(departmentId, empId, $positionsSelect);
            updateModalDepartment($(this).find("option:selected").text());
        });

        // Function to load positions based on department
        function loadPositions(departmentId, empId, $positionsSelect) {
            $.ajax({
                url: '/get-position/' + departmentId + '/' + empId,
                type: 'GET',
                success: function(data) {
                    console.log('AJAX response data:', data); // Debug: log the response data
                    if (data.length === 0) {
                        console.log('No positions found for department ID:', departmentId);
                    }
                    $positionsSelect.empty();
                    $.each(data, function(key, value) {
                        $positionsSelect.append('<option value="' + value.id + '">' + value.position + '</option>');
                    });
                    $positionsSelect.select2(); // Reinitialize select2 if you are using it
                    updateModalPosition($positionsSelect.find("option:selected").text());
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error); // Debug: log any AJAX errors
                }
            });
        }

        // Functions to update the modal content
        function updateModalDepartment(departmentName) {
            $('#modal_department').text(departmentName);
        }

        function updateModalPosition(positionName) {
            $('#modal_position').text(positionName);
        }

        // Function to update the modal dates and total days
        function updateModalDates() {
            const startDate = new Date($('#start_date').val());
            const endDate = new Date($('#end_date').val());
            const totalDaysInput = $('#total_days');
            const modalTotalDays = $('#modal_total_days');
            const selectedStartDate = $('#selected_start_date');
            const selectedEndDate = $('#selected_end_date');
            const selectedStartDateValue = $('#selected_start_date_value');
            const selectedEndDateValue = $('#selected_end_date_value');

            if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate)) {
                const timeDifference = endDate.getTime() - startDate.getTime();
                const totalDays = Math.ceil(timeDifference / (1000 * 3600 * 24)) + 1;

                if (totalDays > 0) {
                    totalDaysInput.val(totalDays);
                    modalTotalDays.text(totalDays);
                    selectedStartDate.text(startDate.toLocaleDateString());
                    selectedEndDate.text(endDate.toLocaleDateString());
                    selectedStartDateValue.val(startDate.toISOString().split('T')[0]);
                    selectedEndDateValue.val(endDate.toISOString().split('T')[0]);
                } else {
                    totalDaysInput.val(0);
                    modalTotalDays.text(0);
                    selectedStartDate.text('-');
                    selectedEndDate.text('-');
                    selectedStartDateValue.val('');
                    selectedEndDateValue.val('');
                }
            } else {
                totalDaysInput.val(0);
                modalTotalDays.text(0);
                selectedStartDate.text('-');
                selectedEndDate.text('-');
                selectedStartDateValue.val('');
                selectedEndDateValue.val('');
            }
        }

        // Event listeners for date changes
        $('#start_date').on('change', updateModalDates);
        $('#end_date').on('change', updateModalDates);
    });

    function addrepresent() {
        var x = document.getElementById("representer_table");
        if (x.style.display === "none") {
            x.style.display = "block";
        } else {
            x.style.display = "none";
        }
    }
</script>

 

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#select_employee').forEach(function(button) {
        button.addEventListener('click', function() {
            var row = button.closest('tr');
            var representativeId = row.getAttribute('data-id');
            var representativeName = row.getAttribute('data-name');
            var representativeLastname = row.getAttribute('data-lastname');

            document.getElementById('representative_id').value = representativeId;
            document.getElementById('representative_name').value = representativeName + ' ' + representativeLastname;
        });
    });
});
</script>



<!-- Adding Department  -->
  
<script>
    function loadPositions($positionsSelect) {
        $.ajax({
            url: '{{ route("get.position") }}', // Fetch all positions
            type: 'GET',
            success: function(data) {
                $positionsSelect.empty().append('<option disabled selected>Position auswählen</option>');

                // Get already selected positions
                var selectedPositions = [];
                $('.position-select').each(function() {
                    var val = $(this).val();
                    if (val) {
                        selectedPositions.push(val);
                    }
                });

                // Filter out already selected positions
                $.each(data, function(key, value) {
                    if (!selectedPositions.includes(value.id.toString())) {
                        $positionsSelect.append('<option value="' + value.id + '">' + value.position + '</option>');
                    }
                });

                $positionsSelect.select2(); // Reinitialize select2
            },
            error: function() {
                console.error('Fehler beim Laden der Positionen.');
            }
        });
    }

    var initialRemainingPercentage = 100; // Default value (will be updated from API)

    function fetchRemainingPercentage() {
        let employeeId = $('input[name="id"]').val();
        if (!employeeId) return;

        $.ajax({
            url: '{{ route("get.dept.remaining.percentage", ":id") }}'.replace(':id', employeeId),
            type: 'GET',
            success: function(response) {
                if (response && response.remaining_percentage !== undefined) {
                    console.log("Fetched remaining percentage:", response.remaining_percentage);
                    initialRemainingPercentage = response.remaining_percentage; // Store initial value
                    $('#remain_percentage').val(initialRemainingPercentage);
                } else {
                    console.warn('No remaining percentage found in API response.');
                    $('#remain_percentage').val(''); 
                }
            },
            error: function() {
                console.error('Fehler beim Abrufen des verbleibenden Prozentsatzes.');
                $('#remain_percentage').val(''); 
            }
        });
    }

     

// Only fetch percentage when modal is opened
        $(document).on('shown.bs.modal', '#editDepartmentModal', function() {  
            fetchRemainingPercentage();
            updateRemainingPercentage();
        });

        $(document).on('input', '.percent-input', function() {
            updateRemainingPercentage();
        });

        $(document).on('click', '.remove_record', function() {
            $(this).closest('.added-row').remove();
            updateRemainingPercentage();
        });

    function updateMontageOfficeInputs(percentInput) {
        let percent = parseFloat($(percentInput).val()) || 0;
        let parentDiv = $(percentInput).closest('.original-record');

        let montageInput = parentDiv.find('.montage-input');
        let officeInput = parentDiv.find('.office-input');

        let halfValue = (percent / 2).toFixed(0); // Divide evenly
        montageInput.val(halfValue);
        officeInput.val(halfValue);
    }

    function balanceMontageOfficeInputs(changedInput) {
        let parentDiv = $(changedInput).closest('.original-record');
        let percent = parseFloat(parentDiv.find('.percent-input').val()) || 0;

        let montageInput = parentDiv.find('.montage-input');
        let officeInput = parentDiv.find('.office-input');

        let changedValue = parseFloat($(changedInput).val()) || 0;
        let remaining = percent - changedValue;

        if (remaining < 0) {
            remaining = 0;
            changedValue = percent;
        }

        if ($(changedInput).hasClass('montage-input')) {
            officeInput.val(remaining);
        } else {
            montageInput.val(remaining);
        }
    }

    $(document).ready(function() {
    var rowIndex = $('.original-record').length;

    fetchRemainingPercentage(); 
    loadPositions($('.position-select'));

    $('#add_record').click(function() {
        let remainingPercentage = parseFloat($('#remain_percentage').val());

        // ✅ Check if "department" tab is active before showing alert
      $$(document).ready(function() {
        // Ensure modal is correctly handled
        $('#newDept').modal({ show: false });

        // ✅ Fetch percentage ONLY when modal is shown
        $('#newDept').on('show.bs.modal', function() {
            console.log("Modal is opening... Fetching remaining percentage.");
            
            // Fetch remaining percentage ONLY when modal is opened
            fetchRemainingPercentage();

            setTimeout(() => {
                updateRemainingPercentage(); // Ensure percentage updates after API call

                let remainingPercentage = parseFloat($('#remain_percentage').val()) || 0;
                console.log("Remaining percentage:", remainingPercentage);

                // ✅ Show Swal warning ONLY when modal is open & remaining percentage is 0
                if (remainingPercentage <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Achtung!',
                        text: 'Sie haben bereits 100% der Arbeitszeit zugewiesen.',
                        confirmButtonText: 'OK'
                    });
                }
            }, 500); // Small delay ensures API data is received before checking
        });

        // ✅ Reset input when modal is closed
        $('#newDept').on('hidden.bs.modal', function() {
            $('#remain_percentage').val(''); // Reset value when closed
        });

        // ✅ Prevent fetching data on page load
        $(document).on('click', '[data-target="#newDept"]', function() {
            $('#newDept').modal('show');
        });

        // ✅ Update remaining percentage when input changes
        $(document).on('input', '.percent-input', function() {
            updateRemainingPercentage();
        });

        // ✅ Remove record and update percentage
        $(document).on('click', '.remove_record', function() {
            $(this).closest('.added-row').remove();
            updateRemainingPercentage();
        });
    });




        let newRow = `
            <div class="original-record col-12 d-flex p-0 added-row" style="background: #eeeeee; padding: 10px; margin-bottom: 10px;">
                <div class="col-lg-2 col-md-2 col-sm-12">
                    <label for="department">Abteilung</label>
                    <select class="form-control department-select" name="details[${rowIndex}][department]" style="width:100% !important;">
                        <option disabled selected>Abteilung auswählen</option>
                        @foreach ($all_departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12">
                    <label for="position">Position</label>
                    <select class="form-control position-select" name="details[${rowIndex}][position]" style="width: 100%">
                        <option disabled selected>Bitte Abteilung wählen</option>
                    </select>
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12">
                    <label for="percent">Prozentsatz</label>
                    <input type="number" name="details[${rowIndex}][percent]" class="form-control percent-input" min="1" max="${remainingPercentage}">
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12">
                    <label for="montage_percent">Montage</label>
                    <input type="number" name="details[${rowIndex}][montage_percent]" class="form-control montage-input" min="1" max="100">
                </div>

                <div class="col-lg-2 col-md-2 col-sm-12">
                    <label for="office_percent">Büroanteil</label>
                    <input type="number" name="details[${rowIndex}][office_percent]" class="form-control office-input" min="1" max="100">
                </div>

                <div class="col-lg-1 col-md-2 col-sm-12">
                    <button type="button" class="btn btn-icon rounded-circle btn-outline-danger mt-4 remove_record"><i class="feather icon-minus-square"></i></button>
                </div>
            </div>`;

        $('#record_table').append(newRow);

        loadPositions($('.position-select').last());

        $(document).on('input', '.percent-input', function() {
            updateMontageOfficeInputs(this);
            updateRemainingPercentage();
        });

        $(document).on('input', '.montage-input, .office-input', function() {
            balanceMontageOfficeInputs(this);
        });

        rowIndex++;
    });

    $(document).on('click', '.remove_record', function() {
        $(this).closest('.added-row').remove();
        updateRemainingPercentage();
    });

    $(document).on('input', '.percent-input', function() {
        updateMontageOfficeInputs(this);
        updateRemainingPercentage();
    });

    $(document).on('input', '.montage-input, .office-input', function() {
        balanceMontageOfficeInputs(this);
    });
});


     

    $(document).ready(function() {
        $(document).on('click', 'edit_department', function() {
            let modal = $($(this).data("target")); // Get the modal
            let employeeId = modal.find('input[name="id"]').val(); // Employee ID
            let departmentSelect = modal.find('.department-select');
            let positionSelect = modal.find('.position-select');

            // Fetch departments & positions when modal opens
            $.ajax({
                    url: '{{ route("get.departments.positions") }}',
                    type: 'GET',
                    data: { employee_id: employeeId },
                    success: function(response) {
                        console.log("AJAX Response:", response); // Debugging: Log the response

                        if (!response.success) {
                            console.error("Error loading data:", response.message);
                            Swal.fire({
                                icon: 'error',
                                title: 'Fehler!',
                                text: 'Abteilungen und Positionen konnten nicht geladen werden!',
                            });
                            return;
                        }

                        let departmentSelect = modal.find('.department-select');
                        let positionSelect = modal.find('.position-select');

                        departmentSelect.empty().append('<option disabled selected>Abteilung auswählen</option>');
                        $.each(response.departments, function(index, department) {
                            let selected = (department.id == response.selected_department) ? "selected" : "";
                            departmentSelect.append(`<option value="${department.id}" ${selected}>${department.name}</option>`);
                        });

                        positionSelect.empty().append('<option disabled selected>Position auswählen</option>');
                        $.each(response.positions, function(index, position) {
                            let selected = (position.id == response.selected_position) ? "selected" : "";
                            positionSelect.append(`<option value="${position.id}" ${selected}>${position.name}</option>`);
                        });

                        departmentSelect.select2(); // Apply select2
                        positionSelect.select2();
                    },
                    error: function(xhr) {
                        console.error("Fehler beim Laden der Abteilungen & Positionen:", xhr.responseText || xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Fehler!',
                            text: 'Daten konnten nicht geladen werden!',
                        });
                    }
                });

        });
    });

    $(document).on('click', '#update_button_dept', function(event) {
        event.preventDefault();

        let modal = $(this).closest('.modal');
        let form = modal.find('.department_edit');
        let formData = form.serialize();
        let modalId = modal.attr('id');

        $.ajax({
            url: '{{ route("emp.update.department") }}', // API route
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#update_button_dept').prop('disabled', true); // Disable button
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Erfolgreich!',
                        text: 'Die Abteilung wurde erfolgreich aktualisiert.',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Update UI dynamically
                    let updatedDepartment = response.updated_department;
                    $(`button[data-target="#${modalId}"]`).closest('tr').find('.department-name').text(updatedDepartment.department_name);

                    modal.modal('hide'); // Close modal
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Fehler!',
                        text: response.message || 'Aktualisierung fehlgeschlagen.',
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Etwas ist schief gelaufen!';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).join(', ');
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Fehler!',
                    text: errorMessage,
                });
            },
            complete: function() {
                $('#update_button_dept').prop('disabled', false); // Enable button
            }
        });
    });




</script>





<script>
    $(document).ready(function() {
    $(".select-representer").on("click", function() {
        var repId = $(this).data("id");
        var repName = $(this).data("name");

        $("#representative_name").val(repName);
        $("#representative_id").val(repId);

        $("#representerListModal").modal("hide"); // Close modal after selection
    });
});

</script>


<!-- deleting department :start -->
 <script>
   document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".delete-department").forEach((button) => {
        button.addEventListener("click", function () {
            let departmentId = this.getAttribute("data-department-id");
            let employeeId = this.getAttribute("data-employee-id");
            let positionId = this.getAttribute("data-position-id"); // This must be the specific position ID
            let Id = this.getAttribute("data-id"); // This must be the specific position ID

            Swal.fire({
                title: "Sind Sie sicher?",
                text: "Möchten Sie diese Abteilung und ihre Position wirklich löschen?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ja, löschen!",
                cancelButtonText: "Abbrechen"
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/employee/delete/department/${Id}/${departmentId}/${employeeId}/${positionId}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                            "Content-Type": "application/json",
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === "success") {
                                Swal.fire({
                                    title: "Gelöscht!",
                                    text: "Die Abteilung und die zugehörige Position wurden erfolgreich gelöscht.",
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                setTimeout(() => {
                                    location.reload();
                                }, 1500);
                            } else {
                                Swal.fire({
                                    title: "Fehler!",
                                    text: data.message || "Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.",
                                    icon: "error"
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: "Fehler!",
                                text: "Etwas ist schief gelaufen. Bitte versuchen Sie es erneut.",
                                icon: "error"
                            });
                            console.error("Error deleting department:", error);
                        });
                }
            });
        });
    });
});


 </script>
<!-- deleting department :end -->
<script>
   $('#save_button_dept').click(function() {
    var structuredData = {
        _token: $('input[name="_token"]').val(), // CSRF Token
        id: $('input[name="id"]').val(),
        details: [] // Initialize details array
    };

    $('.original-record').each(function() {
        var department = $(this).find('.department-select').val();
        var position = $(this).find('.position-select').val();
        var percent = $(this).find('.percent-input').val();
        var montagePercent = $(this).find('.montage-input').val();
        var officePercent = $(this).find('.office-input').val();

        if (department && position && percent) {
            structuredData.details.push({
                department: department,
                position: position,
                percent: percent,
                montage_percent: montagePercent, // Include montage percent
                office_percent: officePercent   // Include office percent
            });
        }
    });

    console.log(structuredData); // Debugging: Check if `details` is structured correctly

    $.ajax({
        url: '{{ route("emp.add.department") }}',
        type: 'POST',
        data: JSON.stringify(structuredData),  // Send data as JSON
        contentType: 'application/json',       // Ensure JSON format
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Erfolgreich gespeichert!',
                text: response.message,
                confirmButtonText: 'OK'
            }).then(() => {
                location.reload(); // Reload the page after success
            });
        },
        error: function(error) {
            console.log(error);
            Swal.fire({
                icon: 'error',
                title: 'Fehler!',
                text: 'Es gab ein Problem beim Speichern.',
                confirmButtonText: 'OK'
            });
        }
    });
});

</script>

 
