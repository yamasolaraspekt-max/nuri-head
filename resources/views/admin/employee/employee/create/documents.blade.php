 


    <div class="row">
        <div class="col-md-12">
        <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light float-right" data-toggle="modal" data-target="#uploadDocument">DOKUMENT HOCHLADEN</button> 
            <div class="modal fade text-left" id="uploadDocument" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="myModalLabel17">DOKUMENTE</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form   action="{{ url('upload.files') }}"  method="post"  class="dropzone" id="document-upload" enctype="multipart/form-data" style="background: transparent; border: 1px dashed #8fc73e; border-radius: 20px;">
                            
                                <input type="hidden" name="employee_id" value="{{$data->id}}"> 
                                <label for="">Dokument Typ</label>
                                <select name="type" id="" class="form-control">
                                    <option value="car">Lizenz</option>
                                    <option value="qualification">Qualifikation</option>
                                    <option value="personal" selected>Persönliches Dokument</option>
                                    <option value="sick">Krankmeldung</option>
                                    <option value="contracts">Vertrag</option>
                                </select>
                                @csrf
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Done</button>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table text-nowrap" > 
                 <thead>
                    <tr>
                        <th>Dokument</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Aktion</th>
                    </tr>
                 </thead>
                 <tbody id="documentTable"></tbody>
            </table>
                                            

            <!-- Modal -->
            <div class="modal fade" id="documentModal" tabindex="-1" role="dialog" aria-labelledby="fileModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="fileModalLabel">File Preview</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body" id="filePreviewDocument">
                            <!-- Content dynamically loaded -->
                        </div>
                    </div>
                </div>
            </div>  
        </div>
    </div> 
 

@push('scripts')
    

    
<script>
$(document).ready(function () {
    const emp_id = {{ request()->id }};  // Get employee ID from request
    fetchFiles(emp_id);  // Load files when the page loads
});

 

    // Fetch and display files for the selected employee (type = 'car')
   function fetchFiles(emp_id) {
    $.ajax({
        url: `/employee_document/${emp_id}`,
        method: 'GET',
        beforeSend: function() {
            $('#documentTable').html('<tr><td colspan="3">Laden...</td></tr>'); // Show loading message
        },
        success: function (response) {
            console.log("📂 Documents Loaded:", response);  // Debug log

            if (!response.data || response.data.length === 0) {
                $('#documentTable').html(`<tr><td colspan="3">Keine Dokumente hochgeladen.</td></tr>`);
                return;
            }

            let tableContent = '';
            response.data.forEach(file => {
                tableContent += ` 
                    <tr id="row_${file.id}">
                        <td>
                            <p>
                                <i class="feather icon-file primary" 
                                   style="font-size: 28px; cursor:pointer;" 
                                   onclick="openFileModal('${file.file_path}', '${file.image_name}', '${file.file_type}')"></i>
                            </p>
                            <span>${file.file_type}</span>
                        </td>
                        <td>
                            <p id="image_name_${file.id}" 
                               ondblclick="makeEditable(${file.id}, '${file.image_name}')">
                                ${file.image_name}
                            </p>
                        </td>
                        <td>
                            ${file.type}
                        </td>
                        <td>
                            <button type="button" class="btn btn-icon btn-danger mr-1 mb-1 waves-effect waves-light" 
                                    onclick="deleteFile(${file.id}, ${emp_id})">
                                <i class="feather icon-trash"></i>
                            </button>
                        </td>
                    </tr>`;
            });

            $('#documentTable').html(tableContent);  // Fill table with content
        },
        error: function (xhr, status, error) {
            console.error("🚨 AJAX Error:", error);
            console.error("❌ Response:", xhr.responseText);
            $('#documentTable').html('<tr><td colspan="3">Fehler beim Laden der Dateien.</td></tr>');
        }
    });
}



            Dropzone.autoDiscover = false;

        const ducumentFiles = new Dropzone("#document-upload", {
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


    // Open file preview modal
    function openFileModal(filePath, fileName, fileType) {
        let fileContent;

        if (['jpg', 'jpeg', 'png'].includes(fileType.toLowerCase())) {
            fileContent = `<img src="${filePath}" alt="${fileName}" class="img-fluid">`;
        } else {
            fileContent = `<iframe src="${filePath}" width="100%" height="500px"></iframe>`;
        }

        $('#filePreviewDocument').html(fileContent);
        $('#documentModal').modal('show');
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
