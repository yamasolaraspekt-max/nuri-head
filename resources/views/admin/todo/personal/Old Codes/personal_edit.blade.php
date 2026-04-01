@extends('admin.layouts.app')
@section('title')
BEARBEITEN
@endsection
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

@endsection
@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Aufgabenliste</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active"><a href="{{ url('/personal_task_view') }}">Bearbeiten</a></li>
                        </ol>
                    </div>
                </div>
            </div>
            <div class="content-body">  
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                             <form method="post" action="{{ route('personal.task.update')}}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $data->id }}">
                                <div class="modal-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <!-- Priority -->
                                            <div class="col-md-6 col-12">
                                                <label for="end_date">Priorität</label>   
                                                <select name="priority" id="" class="form-control">
                                                    <option value="high" @if($data->priority == 'high') selected @endif>Hoch</option>
                                                    <option value="medium" @if($data->priority == 'medium') selected @endif>Mittel</option>
                                                    <option value="low" @if($data->priority == 'low') selected @endif>Niedrig</option>
                                                </select> 
                                            </div>

                                            <!-- Color -->
                                            <div class="col-md-6 col-12">
                                                <label for="end_date">Farbe</label>   
                                                <select name="color" id="color-select" class="form-control" style="width:100%">
                                                <option value="#FF0000" data-color="#FF0000" @if($data->color == '#FF0000') selected @endif>Rot</option>
                                                <option value="#0000FF" data-color="#0000FF" @if($data->color == '#0000FF') selected @endif>Blau</option>
                                                <option value="#008000" data-color="#008000" @if($data->color == '#008000') selected @endif>Grün</option>
                                                <option value="#FFFF00" data-color="#FFFF00" @if($data->color == '#FFFF00') selected @endif>Gelb</option>
                                                <option value="#FFA500" data-color="#FFA500" @if($data->color == '#FFA500') selected @endif>Orange</option>
                                                <option value="#800080" data-color="#800080" @if($data->color == '#800080') selected @endif>Lila</option>
                                                <option value="#FFC0CB" data-color="#FFC0CB" @if($data->color == '#FFC0CB') selected @endif>Pink</option>
                                                <option value="#A52A2A" data-color="#A52A2A" @if($data->color == '#A52A2A') selected @endif>Braun</option>
                                                <option value="#808080" data-color="#808080" @if($data->color == '#808080') selected @endif>Grau</option>
                                                <option value="#FFFFFF" data-color="#FFFFFF" @if($data->color == '#FFFFFF') selected @endif>Weiß</option>
                                                <option value="#000000" data-color="#000000" @if($data->color == '#000000') selected @endif>Schwarz</option>
                                                <option value="#00FFFF" data-color="#00FFFF" @if($data->color == '#00FFFF') selected @endif>Cyan</option>
                                                <option value="#FF00FF" data-color="#FF00FF" @if($data->color == '#FF00FF') selected @endif>Magenta</option>
                                                <option value="#ADD8E6" data-color="#ADD8E6" @if($data->color == '#ADD8E6') selected @endif>Hellblau</option>
                                                <option value="#00008B" data-color="#00008B" @if($data->color == '#00008B') selected @endif>Dunkelblau</option>
                                                <option value="#90EE90" data-color="#90EE90" @if($data->color == '#90EE90') selected @endif>Hellgrün</option>
                                                <option value="#006400" data-color="#006400" @if($data->color == '#006400') selected @endif>Dunkelgrün</option>
                                                <option value="#F5F5DC" data-color="#F5F5DC" @if($data->color == '#F5F5DC') selected @endif>Beige</option>
                                                <option value="#C0C0C0" data-color="#C0C0C0" @if($data->color == '#C0C0C0') selected @endif>Silber</option>
                                                <option value="#FFD700" data-color="#FFD700" @if($data->color == '#FFD700') selected @endif>Gold</option>
                                            </select>

                                            </div>

                                            <!-- Task Title -->
                                            <div class="col-md-12 col-12">
                                                <label for="first-name-task_title">Aufgabentitel</label>  
                                                <input type="text" id="task_title" class="form-control" placeholder="" name="task_title" value="{{ old('task_title', $data->task_title)}}"> 
                                            </div>

                                            <!-- Start Date -->
                                            <div class="col-md-6 col-12">
                                                <label for="first-name-task_title">Startdatum</label>  
                                                <input type="date" id="task_title" class="form-control" placeholder="" name="start_date"  value="{{ old('task_title', $data->start_date)}}"> 
                                            </div>

                                            <!-- End Date -->
                                            <div class="col-md-6 col-12">
                                                <label for="end_date">Enddatum</label>  
                                                <input type="date" id="end_date" class="form-control" placeholder="" name="end_date"  value="{{ old('task_title', $data->end_date)}}"> 
                                            </div>

                                            <!-- Start Time -->
                                                <div class="col-md-6 col-12">
                                                    <label for="start_time">Startzeit</label>  
                                                    <input 
                                                        type="time" 
                                                        id="start_time" 
                                                        class="form-control" 
                                                        name="start_time" 
                                                        value="{{ old('start_time', isset($data->start_time) ? \Carbon\Carbon::createFromFormat('H:i:s', $data->start_time)->format('H:i') : \Carbon\Carbon::now()->format('H:i')) }}"
                                                    > 
                                                </div>

                                                <!-- End Time -->
                                                <div class="col-md-6 col-12">
                                                    <label for="end_time">Endzeit</label>  
                                                    <input 
                                                        type="time" 
                                                        id="end_time" 
                                                        class="form-control" 
                                                        name="end_time" 
                                                        value="{{ old('end_time', isset($data->end_time) ? \Carbon\Carbon::createFromFormat('H:i:s', $data->end_time)->format('H:i') : '') }}"
                                                    > 
                                                </div>



                                            <!-- Assigned To -->
                                            <div class="col-md-12 col-12">
                                                <label for="employee">Zugewiesen an</label> 
                                                <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                                    @foreach ($employees as $emp)
                                                        <option value="{{ $emp->id }}" 
                                                            data-image="{{ asset('images/employee/'.$emp->image) }}"
                                                            @if ($task_employee->pluck('employee_id')->contains($emp->id)) selected @endif>
                                                            {{ $emp->name }} {{ $emp->lastname }}
                                                        </option>
                                                    @endforeach
                                                </select>  
                                            </div>


                                            <!-- Description -->
                                            <div class="col-md-12 col-12">
                                                <label for="last-name-column">Beschreibung</label> 
                                                <textarea name="description" class="form-control" id="" cols="20" rows="5">
                                                    {{ old('description', $data->description)}}
                                                </textarea> 
                                            </div>  

                                            <!-- Key Task Table -->
                                                <div class="col-md-6 col-12">  
                                                    <div class="table-responsive">
                                                        <table class="table" id="key_task"> 
                                                            <thead>
                                                                <tr>
                                                                    <th>Arbeitsschritte</th>
                                                                    <th>Aktion</th> 
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {{-- Preload Existing Key Tasks --}}
                                                               @foreach ($key_task as $key)
                                                                    <tr data-id="{{ $key->id }}">
                                                                        <td class="d-flex">
                                                                           <label for="" class="mt-1 mr-1"> {{$loop->index +1}} </label> <input type="text" name="key[{{ $key->id }}][task]" value="{{ $key->task }}" class="form-control">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-icon btn-danger remove-task">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach


                                                                {{-- Default Row If No Tasks Exist --}}
                                                                @if($key_task->isEmpty())
                                                                <tr> 
                                                                    <td>
                                                                       <input type="text" name="key[0][task]" class="form-control" placeholder="Aufgabe eingeben">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-icon btn-danger remove-task">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button type="button" class="btn btn-icon btn-success add-task mt-2">
                                                        <i class="feather icon-plus"></i> Aufgabe hinzufügen
                                                    </button>
                                                </div>

                                                <!-- Sub Task Table -->
                                                <div class="col-md-6 col-12">  
                                                    <div class="table-responsive">
                                                        <table class="table" id="sub_task"> 
                                                            <thead>
                                                                <tr>
                                                                    <th>Bemerkung</th>
                                                                    <th>Link</th> 
                                                                    <th>Aktion</th> 
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                {{-- Preload Existing Sub Tasks --}}
                                                               <!-- Sub Task Table -->
                                                                    @foreach ($sub_task as $sub)
                                                                    <tr data-id="{{ $sub->id }}">
                                                                        <td>
                                                                            <input type="text" name="sub_task[{{ $sub->id }}][sub]" value="{{ $sub->sub_task_title }}" class="form-control">
                                                                        </td>
                                                                         <td>
                                                                            <input type="text" name="sub_task[{{ $sub->id }}][description]" value="{{ $sub->sub_task_title }}" class="form-control">
                                                                        </td>
                                                                        <td>
                                                                            <button type="button" class="btn btn-icon btn-danger remove-sub-task">
                                                                                <i class="fa fa-trash"></i>
                                                                            </button>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach

                                                                {{-- Default Row If No Sub Tasks Exist --}}
                                                                @if($sub_task->isEmpty())
                                                                <tr> 
                                                                    <td>
                                                                        <input type="text" name="sub_task[0][sub]" class="form-control" placeholder="Bemerkung">
                                                                        <input type="text" name="sub_task[0][description]" class="form-control" placeholder="Link">
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-icon btn-danger remove-sub-task">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <button type="button" class="btn btn-icon btn-success add-sub-task mt-2">
                                                        <i class="feather icon-plus"></i> Bemerkung hinzufügen
                                                    </button>
                                                </div>
                                                
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <a type="button" href="{{ url('personal/task/'.auth()->user()->name ) }}" class="btn btn-primary waves-effect waves-light"> Abbrechen</a>
                                    <button type="submit" class="btn btn-primary waves-effect waves-light" >Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
    $(document).ready(function() {
    $('.employee').select2({
        templateResult: formatEmployee,
        templateSelection: formatEmployee,
        escapeMarkup: function(markup) {
            return markup;
        }
    });
});

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

</script> 

 
</script> 
<!-- Color: start  -->
 <script>
    $(document).ready(function() {
    $('#color-select').select2({
        templateResult: formatColor,
        templateSelection: formatColor,
        escapeMarkup: function(markup) {
            return markup;
        }
    });

    function formatColor(color) {
        if (!color.id) {
            return color.text;
        }

        var colorValue = $(color.element).data('color');
        var colorName = color.text;

        var markup = `
            <div style="display: flex; align-items: center;">
                <span style="width: 15px; height: 15px; background: ${colorValue}; border-radius: 50%; margin-right: 8px;"></span>
                <span>${colorName}</span>
            </div>
        `;

        return markup;
    }
});

 </script>

 <script>
    $('#task-form').submit(function(e) {
    e.preventDefault();

    $.ajax({
        url: "{{ route('personal.task.update') }}",
        method: "POST",
        data: $(this).serialize(),
        success: function(response) {
            toastr.success("Task updated successfully!");
            window.location.href = "{{ route('personal.task.index', auth()->user()->name) }}"; // Redirect to view
        },
        error: function(xhr) {
            toastr.error("Error updating task. Please try again.");
        }
    });
});

 </script>


<script>
$(document).ready(function () {
    let keyTaskIndex = {{ $key_task->count() ? $key_task->count() : 0 }};
    let subTaskIndex = {{ $sub_task->count() ? $sub_task->count() : 0 }};

    // Add Key Task
    $(document).on("click", ".add-task", function () {
        keyTaskIndex++;
        $("#key_task tbody").append(`
            <tr data-id="">
                <td>
                    <input type="text" name="key[${keyTaskIndex}][task]" class="form-control" placeholder="Aufgabe eingeben">
                </td>
                <td>
                    <button type="button" class="btn btn-icon btn-danger remove-task">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });

    // Add Sub Task
    $(document).on("click", ".add-sub-task", function () {
        subTaskIndex++;
        $("#sub_task tbody").append(`
            <tr data-id="">
                <td>
                    <input type="text" name="sub_task[${subTaskIndex}][sub]" class="form-control" placeholder="Aufgabe eingeben">
                </td>
                 <td>
                    <input type="text" name="sub_task[${subTaskIndex}][description]" class="form-control" placeholder="Beschreibung">
                </td>
                <td>
                    <button type="button" class="btn btn-icon btn-danger remove-sub-task">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });

     $(document).ready(function () {
        // Remove Key Task
        $(document).on("click", ".remove-task", function () {
            const row = $(this).closest("tr");
            const taskId = row.data("id");

            if (taskId) {
                console.log(`Deleting Task Key ID: ${taskId}`);
                $.ajax({
                    url: `/personal_task_key_delete/${taskId}`,  // Use GET request
                    method: 'GET',
                    success: function (response) {
                        console.log("Task deleted:", response);
                        if (response.success) {
                            row.remove();  // Remove row on success
                             toastr.success("Task Deleted successfully!");
                        } else {
                             toastr.error("Task not deleted!");
                        }
                    },
                    error: function (xhr) {
                        console.error("Error deleting task:", xhr.responseText); 
                         toastr.error("Failed to delete task. Please try again.");
                    },
                });
            } else {
                console.warn("Task ID not found. Removing row locally.");
                row.remove();  // Remove row if no task ID
            }
        });

        // Remove Sub Task
        $(document).on("click", ".remove-sub-task", function () {
            const row = $(this).closest("tr");
            const subTaskId = row.data("id");

            if (subTaskId) {
                console.log(`Deleting Sub Task ID: ${subTaskId}`);
                $.ajax({
                    url: `/personal_task_sub_delete/${subTaskId}`,  // Use GET request
                    method: 'GET',
                    success: function (response) {
                        console.log("Response:", response);
                        if (response.success) {
                            row.remove();  // Remove row on success
                           toastr.success(" Sub Task Deleted successfully!");
                        } else {
                            alert("Failed to delete the sub-task. Please try again.");
                        }
                    },
                    error: function (xhr) {
                        console.error("Error:", xhr.responseText);
                        alert("Failed to delete the sub-task. Please try again.");
                    },
                });
            } else {
                console.warn("Sub Task ID not found. Removing row locally.");
                row.remove();  // Remove row if no task ID
            }
        });
    });


    // Function to Update Row IDs After Saving
    function updateRowId(row, newId) {
        row.attr("data-id", newId);
    }
});
</script>

@endsection