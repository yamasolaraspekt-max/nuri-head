@push('style')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
<script src="{{ asset('js/dropzone.min.js') }}"></script>
 
@endpush


    <div class="col-12">
         @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
                
        <div class="row">
            @if(DB::table('user_rolls')
            ->where('user_rolls.user_id', '=', auth()->user()->name)
            ->where('user_rolls.item_id', '=', 'Employee')
            ->where('user_rolls.is_add', '=', 'on')
            ->first())
            <button type="button" class="btn btn-outline-primary mr-1 mb-1 float-right" data-toggle="modal" data-target="#add_car"> erstellen</button>
            @endif
            <!-- Leave Save Model: Start -->
            <div class="modal fade text-left" id="add_car" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel1">{{ $data->name }} {{ $data->lastname }}</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmployeeLicenseController@store')}}" class="custom-file-upload" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">  
                                <div class="row"> 
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="Title">
                                            Mitarbeitername
                                                </label>
                                            <input disabled type="text" class="form-control required" value="{{$data->name}} {{$data->lastname}}">
                                            <input  type="hidden" class="form-control required" name="emp_id" value="{{$data->id}}"> 
                                        </div>  
                                    </div> 
                                </div> 
                                <div class="row">
                                    <div class="col-lg-12 col-md-12" id="type_table">
                                        <div class="row">
                                            <div class="col-md-6" style="display: block;">
                                                <div class="form-group">
                                                    <label for="Title">Lizenzgrad</label>
                                                    <select data-placeholder="Wählen Sie eine Lizenz..." class="select2-icons form-control type" id="type" name="type[0][type]"  style="width:100%">
                                                        <optgroup label="Führerscheinklassen in Deutschland"> 
                                                            <option value="AM">AM – Kleinkrafträder (bis 45 km/h)</option>
                                                            <option value="A1">A1 – Leichtkrafträder (bis 125 cm³, max. 11 kW)</option>
                                                            <option value="A2">A2 – Motorräder mit max. 35 kW</option>
                                                            <option value="A">A – Alle Motorräder ohne Einschränkung</option>
                                                            <option value="B">B – PKW (bis 3,5 t, max. 9 Sitze)</option>
                                                            <option value="B96">B96 – PKW mit Anhänger über 750 kg (bis 4,25 t)</option>
                                                            <option value="BE">BE – PKW mit schwerem Anhänger (bis 3,5 t Anhängergewicht)</option>
                                                            <option value="C1">C1 – LKW bis 7,5 t</option>
                                                            <option value="C1E">C1E – LKW bis 7,5 t mit Anhänger über 750 kg</option>
                                                            <option value="C">C – LKW über 3,5 t ohne Begrenzung</option>
                                                            <option value="CE">CE – LKW mit schwerem Anhänger</option>
                                                            <option value="D1">D1 – Busse bis 16 Sitzplätze</option>
                                                            <option value="D1E">D1E – D1-Busse mit Anhänger</option>
                                                            <option value="D">D – Busse mit mehr als 16 Sitzplätzen</option>
                                                            <option value="DE">DE – D-Busse mit Anhänger</option>
                                                            <option value="L">L – Landwirtschaftliche Fahrzeuge (bis 40 km/h)</option>
                                                            <option value="T">T – Traktoren und landwirtschaftliche Fahrzeuge (bis 60 km/h)</option>
                                                        </optgroup>
                                                    </select>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="Title">Grade</label>
                                                    <input type="text" class="form-control required" name="type[0][grade]">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 mt-1 waves-effect waves-light" id="add_type">
                                                    <i class="feather icon-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row"> 
                                    <div class="col-md-6"  >
                                        <div class="form-group">
                                            <label for="Title">
                                                Amtliches Kennzeichen
                                                </label>
                                            <input  type="string" class="form-control required" value="" name="license_no">
                                        </div>
                                    </div>

                                    <div class="col-md-6" id="personal_remain">
                                        <div class="form-group">
                                            <label for="Title">
                                                Verfallsdatum
                                                </label>
                                            <input  type="date"  class="form-control required" name="expiry_date" value="{{ old('expiry_date') }}" id="expiry_date" value="">
                                        </div>
                                    </div>
                                </div> 
                                 
                            </div> 
                            <div class="modal-footer">
                                <button type="button" data-dismiss="modal" class="btn btn-danger">abbrechen</button>
                                <button type="submit" class="btn btn-primary">speichern</button> 
                            </div>
                        </form>
                    </div>
                </div> 
            </div> 

             <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light" data-toggle="modal" data-target="#large">UPLOAD</button> 
                <div class="modal fade text-left" id="large" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" id="myModalLabel17">Upload Files</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form   action="{{ url('upload.files') }}"  method="post"  class="dropzone" id="file-upload" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                                
                                    <input type="hidden" name="employee_id" value="{{$data->id}}"> 
                                    <input type="hidden" name="type" value="car"> 
                                    @csrf
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Done</button>
                            </div>
                        </div>
                    </div>
                </div> 
 

                 <div class="modal fade" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body" id="filePreviewContent">
                                <!-- Content dynamically loaded -->
                            </div>
                        </div>
                    </div>
                </div>  
        </div>  

        <div class="row">
            <table class="table" id="">
                <thead>
                    <tr>
                        <th>Mitarbeitername</th>
                        <th>Lizenzgrad</th>
                        <th>Amtliches Kennzeichen</th>
                        <th>Verfallsdatum</th> 
                        <th>Foto</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($license as $lice)
                    <tr> 
                        <td>{{ $data->name }} {{ $data->lastname }}</td>
                        <td>
                            @foreach ($employee_license as $license )
                            <div class="badge badge-success mr-1 mb-1">
                                <i class="fa fa-times-circle"></i >{{ $license->type }}
                            </div>
                            @endforeach 
                        </td>
                        
                        <td>{{ $lice->license_no }}</td>
                        
                        <td>{{ $lice->expiry_date }}
                            <br>
                            @if($lice->expiry_date== \Carbon\Carbon::parse(now())->isoFormat('DD.MM.YYY'))
                            <div class="badge badge-danger mr-1 mb-1">
                                <i class="fa fa-times-circle"></i >Die Lizenz ist abgelaufen
                            </div>
                            @endif
                        </td>
                   
                        

                        <td>
                            {{ $lice->status }}<br>
                            @if($lice->status!=Null)
                            <div class="badge badge-danger mr-1 mb-1">
                                <i class="fa fa-times-circle"></i > {{ $lice->duration }}
                            </div>
                            <div class="badge badge-danger mr-1 mb-1">
                                {{ $lice->suspend_date }}
                            </div>
                            @else
                            <div class="badge badge-success mr-1 mb-1">
                                <i class="fa fa-check"></i >Aktiv
                            </div>
                            @endif
                        </td>
                    
                        <td>
                            <!-- Delete Modal -->
                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-licese{{$lice->id}}">
                            <i class="feather icon-trash"></i>
                            </button>

                            <!-- Modal -->
                            <div class="modal fade text-left" id="delete-licese{{$lice->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">                                       
                                            <h5>Datensatz löschen</h5>
                                            <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                            <p>Die Datensatznummer lautet: {{$lice->id}} </p>
                                        </div>
                                        <div class="modal-footer">
                                            <a type="button"   class="btn btn-primary" data-dismiss="modal">abbrechen</a>
                                            <a type="button" href="{{url('/license_destroy').'/'.$lice->id}}" class="btn btn-danger">Löschen</a>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                                    <!-- licenseEdit Start -->
                            <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#licenseEdit{{$lice->id}}"><i class="feather icon-edit"></i></button>
                                <div class="modal fade text-left" id="licenseEdit{{$lice->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel17">Large Modal</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmployeeLicenseController@update')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                @csrf 
                                                <div class="modal-body"> 
                                                    <div class="row"> 
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="Title">  Mitarbeitername  </label>
                                                                <input disabled type="text" class="form-control required" value="{{$data->name}} {{$data->lastname}}">
                                                                <input  type="hidden" class="form-control required" name="emp_id" value="{{$data->id}}">
                                                                <input  type="hidden" class="form-control required" name="id" value="{{$lice->id}}"> 
                                                            </div> 
                                                        </div>

                                                        <div class="col-md-6" id="sick">
                                                            <div class="form-group">
                                                                <label for="Title">
                                                                    Amtliches Kennzeichen
                                                                    </label>
                                                                <input  type="string" class="form-control required"name="license_no" value="{{ $lice->license_no }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6" id="sick">
                                                            <div class="form-group">
                                                                <label for="Title">
                                                                    Amtliches Kennzeichen
                                                                    </label>
                                                                <input  type="string" class="form-control required"name="license_no" value="{{ $lice->license_no }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6" id="personal_remain">
                                                            <div class="form-group">
                                                                <label for="Title">
                                                                    Verfallsdatum
                                                                    </label>
                                                                <input  type="date"  class="form-control required" name="expiry_date" value="{{ $lice->expiry_date }}" id="expiry_date">
                                                            </div>
                                                        </div>

                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="Title">
                                                                    Lizenzfoto
                                                                </label>
                                                                <input type="file" name="image" class="form-control">
                                                                @if ($errors->has('image'))<p style="color:red;">{!!$errors->first('image')!!}</p>@endif
                                                            </div>
                                                        </div>
                                                    </div> 
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light" >speichern</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>  
                                    <!-- End licenseEdit -->

                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#suspend{{$lice->id}}">
                                    <i class="feather icon-alert-octagon "></i>
                                </button> 
                                    
                                <div class="modal fade text-left" id="suspend{{$lice->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Suspendierter Status
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span> 
                                                </button>
                                            </div>
                                            <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmployeeLicenseController@suspend')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">                                       
                                                    <div class="col-md-12" id="sick">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Suspendiertes Datum
                                                                </label>
                                                                <input type="hidden" name="id" value="{{ $lice->id }}">
                                                            <input  type="date" class="form-control required"name="suspend_date">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12" id="sick">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Ausgesetzte Dauer
                                                                </label>
                                                    
                                                            <input  type="string" class="form-control required"name="duration">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button"  class="btn btn-danger" data-dismiss="modal">abbrechen</button>
                                                    <button type="submit"  class="btn btn-primary">spiechern</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div> 
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>    
</div> 

@push('scripts')
<script>
        $(document).ready(function() {
            $('#edit_grade').select2();
            $('.type').select2(); 
        });

        

        document.getElementById('add_type').addEventListener('click', function() {
            var typeTable = document.getElementById('type_table');
            var rowCount = typeTable.getElementsByClassName('row').length;
            var newRow = document.createElement('div');
            newRow.className = 'row';
            newRow.innerHTML = `
                <div class="col-md-6" style="display: block;">
                    <div class="form-group">
                        <label for="Title">Lizenzgrad</label>
                       <select data-placeholder="Wählen Sie eine Lizenz..." class="select2-icons form-control type" id="type" name="type[${rowCount}][type]"  style="width:100%">
                            <optgroup label="Führerscheinklassen in Deutschland"> 
                                <option value="AM">AM – Kleinkrafträder (bis 45 km/h)</option>
                                <option value="A1">A1 – Leichtkrafträder (bis 125 cm³, max. 11 kW)</option>
                                <option value="A2">A2 – Motorräder mit max. 35 kW</option>
                                <option value="A">A – Alle Motorräder ohne Einschränkung</option>
                                <option value="B">B – PKW (bis 3,5 t, max. 9 Sitze)</option>
                                <option value="B96">B96 – PKW mit Anhänger über 750 kg (bis 4,25 t)</option>
                                <option value="BE">BE – PKW mit schwerem Anhänger (bis 3,5 t Anhängergewicht)</option>
                                <option value="C1">C1 – LKW bis 7,5 t</option>
                                <option value="C1E">C1E – LKW bis 7,5 t mit Anhänger über 750 kg</option>
                                <option value="C">C – LKW über 3,5 t ohne Begrenzung</option>
                                <option value="CE">CE – LKW mit schwerem Anhänger</option>
                                <option value="D1">D1 – Busse bis 16 Sitzplätze</option>
                                <option value="D1E">D1E – D1-Busse mit Anhänger</option>
                                <option value="D">D – Busse mit mehr als 16 Sitzplätzen</option>
                                <option value="DE">DE – D-Busse mit Anhänger</option>
                                <option value="L">L – Landwirtschaftliche Fahrzeuge (bis 40 km/h)</option>
                                <option value="T">T – Traktoren und landwirtschaftliche Fahrzeuge (bis 60 km/h)</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="Title">Grade</label>
                        <input type="text" class="form-control required" name="type[${rowCount}][grade]">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 mt-1 waves-effect waves-light remove_type">
                        <i class="feather icon-minus"></i>
                    </button>
                </div>
            `;
            typeTable.appendChild(newRow);

            // Initialize Select2 for the new element
            initializeSelect2();
        });

        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove_type') || event.target.closest('.remove_type')) {
                event.target.closest('.row').remove();
            }
        });

 

</script>
                
  



    
<script>
    $(document).ready(function () {
        // Get the employee ID from PHP
        const emp_id = {{ $data->id }}; 

        // Load files when the "Dokument" button is clicked
        $('#carModal').on('show.bs.modal', function () {
            fetchFiles(emp_id);
        });
    });

              
            Dropzone.autoDiscover = false;

        const licenseUpload = new Dropzone("#file-upload", {
            url: "/employee_upload", // Replace with your actual upload endpoint
            method: "POST",
            paramName: "file",
            maxFilesize: 2, // Max file size in MB
            acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.txt",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            addRemoveLinks: true,
            init: function () {
                this.on("success", function (file, response) {
                    console.log("File uploaded successfully:", response);
                });
                this.on("error", function (file, errorMessage) {
                    console.error("File upload error:", errorMessage);
                });
            },
        });

    // Fetch and display files for the selected employee (type = 'car')
    $(document).ready(function () {
    const emp_id = {{ $data->id }}; // Ensure employee ID is correctly set

    // Load files when the modal opens
    $('#carModal').on('show.bs.modal', function (event) {
        fetchFiles(emp_id);
    });
});

    // Fetch and display files for the selected employee (type = 'car')
   function fetchFiles(emp_id) {
        $.ajax({
            url: `/employee_license_get/${emp_id}/car`, // Fetch only car-related documents
            method: 'GET',
            beforeSend: function () {
                $('#licenseTable').html('<tr><td colspan="3">Laden...</td></tr>'); // Show loading message
            },
            success: function (response) {
                console.log("📂 Full Response:", response); // Debugging
                console.log("🔹 Data Array:", response.data); // Ensure the array exists

                let tableContent = '';

                if (!response.data || response.data.length === 0) {
                    tableContent = `<tr><td colspan="3">Keine Dokumente hochgeladen.</td></tr>`;
                } else {
                    response.data.forEach((file, index) => {
                        tableContent += `
                            <tr id="row_${file.id}">
                                <td>
                                    <i class="feather icon-file primary" 
                                    style="font-size: 28px; cursor:pointer;" 
                                    onclick="openFileModal('${file.file_path}', '${file.image_name}', '${file.file_type}')">
                                    </i>
                                    <span>${file.file_type}</span>
                                </td>
                                <td>
                                    <p id="image_name_${file.id}" 
                                    ondblclick="makeEditable(${file.id}, '${file.image_name}')">
                                        ${file.image_name}
                                    </p>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-icon btn-danger mr-1 mb-1 waves-effect waves-light" 
                                            onclick="deleteFile(${file.id}, ${emp_id})">
                                        <i class="feather icon-trash"></i>
                                    </button>
                                </td>
                            </tr>`;
                    });
                }

                $('#licenseTable tbody').html(tableContent);  // Ensure content is inside <tbody>
                console.log("✅ Table Updated Successfully"); // Debugging confirmation
            },
            error: function (xhr, status, error) {
                console.error("🚨 AJAX Error:", error);
                console.error("❌ Response:", xhr.responseText);
                $('#licenseTable tbody').html('<tr><td colspan="3">Fehler beim Laden der Dateien.</td></tr>');
            }
        });
    }



    // Open file preview modal
    function openFileModal(filePath, fileName, fileType) {
        let fileContent;

        if (['jpg', 'jpeg', 'png'].includes(fileType.toLowerCase())) {
            fileContent = `<img src="${filePath}" alt="${fileName}" class="img-fluid">`;
        } else {
            fileContent = `<iframe src="${filePath}" width="100%" height="500px"></iframe>`;
        }

        $('#filePreviewContent').html(fileContent);
        $('#fileModal').modal('show');
    }

    // Make file name editable
    function makeEditable(fileId, currentName) {
        const element = $(`#image_name_${fileId}`);
        const input = `
            <input type="text" id="edit_image_name_${fileId}" 
                   value="${currentName}" 
                   class="form-control"
                   onblur="updateFileName(${fileId})"
                   onkeydown="checkForEnter(event, ${fileId})">
        `;
        element.html(input);
        $(`#edit_image_name_${fileId}`).focus();
    }

    // Check for Enter key during editing
    function checkForEnter(event, fileId) {
        if (event.key === "Enter") {
            updateFileName(fileId);
        }
    }

    // Update file name
    function updateFileName(fileId) {
        const newFileName = $(`#edit_image_name_${fileId}`).val();

        $.ajax({
            url: '/employee_image_name',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: fileId,
                image_name: newFileName
            },
            success: function (response) {
                toastr.success('Dateiname erfolgreich aktualisiert!');
                fetchFiles({{ $data->id }}); // Refresh table
            },
            error: function (xhr, status, error) {
                console.error(`Fehler beim Aktualisieren der Datei: ${error}`);
                alert('Fehler beim Umbenennen der Datei.');
            }
        });
    }

    // Delete file
    function deleteFile(fileId, emp_id) {
        if (confirm('Sind Sie sicher, dass Sie diese Datei löschen möchten?')) {
            $.ajax({
                url: `/employee_image_destroy/${fileId}`,
                method: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function () {
                    toastr.error('Datei erfolgreich gelöscht!');
                    fetchFiles(emp_id); // Reload files after deletion
                },
                error: function (xhr, status, error) {
                    console.error(`Fehler beim Löschen der Datei: ${error}`);
                    alert('Fehler beim Löschen der Datei.');
                }
            });
        }
    }
</script>

@endpush