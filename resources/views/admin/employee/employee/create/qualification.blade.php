                                              
                                                    

    <section id="nav-filled">
        <div class="row">
            <div class="col-sm-12">
                <div class="card overflow-hidden"> 
                    <div class="card-content">
                        <div class="card-body"> 
                            </p>
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="qualification-tab" data-toggle="tab" href="#qualification_fill" role="tab" aria-controls="qualification_fill" aria-selected="true">QUALIFIKATION</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="further_education_tab" data-toggle="tab" href="#furtherEducation_fill" role="tab" aria-controls="furtherEducation_fill" aria-selected="false">WEITERBILDUNG</a>
                                </li>
                        
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content pt-1">
                                <div class="tab-pane active" id="qualification_fill" role="tabpanel" aria-labelledby="qualification-tab">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="card-body" style="padding-top: 0px;">
                                                    @if ($errors->qualificationForm->any())
                                                        <div class="alert alert-danger">
                                                            <ul>
                                                                @foreach ($errors->qualificationForm->all() as $error)
                                                                    <li>{{ $error }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif  
                                                    <button type="button" class="btn btn-outline-primary waves-effect waves-light float-right mb-2" data-toggle="modal" data-target="#addEducationModal">
                                                        Erstellen
                                                    </button>
                                                    <div class="modal fade text-left" id="addEducationModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document" style="max-width: 1122px !important;">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="myModalLabel17">Universitätsprogramme</h4>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <form id="qualification_form" class="custom-file-upload" enctype="multipart/form-data">
                                                                    @csrf
                                                                    <input type="hidden" name="active_tab" id="active_tab" value="qualification">
                                                                    <div class="modal-body">
                                                                        <div class="table-responsive">
                                                                            <table class="table" id="qualification_table">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Degree</th>
                                                                                        <th>Major</th>
                                                                                        <th>Institution</th>
                                                                                        <th>Startjahr</th>
                                                                                        <th>Abschlussdatum</th>
                                                                                        <th>Grade</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                    <tr>
                                                                                        <input type="hidden" name="qual[0][emp_id]" value="{{$data->id}}">
                                                                                        <td><input type="text" class="form-control required" placeholder="Degree" name="qual[0][degree]"></td>
                                                                                        <td><input type="text" class="form-control required" placeholder="Major" name="qual[0][major]"></td>
                                                                                        <td><input type="text" class="form-control required" placeholder="Institution" name="qual[0][institution]"></td>
                                                                                        <td><input type="date" class="form-control required" placeholder="Startjahr" name="qual[0][q_start_year]"></td>
                                                                                        <td><input type="date" class="form-control required" placeholder="Abschlussdatum" name="qual[0][q_end_year]"></td>
                                                                                        <td><input type="text" class="form-control" placeholder="Grade" name="qual[0][grade]"></td>
                                                                                        <td>
                                                                                            <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_qualification">
                                                                                                <i class="feather icon-plus"></i>
                                                                                            </button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                                                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Speichern</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                        
                                                    <table class="table" id="a">
                                                        <thead>
                                                            <tr>
                                                                <th>Degree/Fakultät</th>
                                                                <th>Wesentlich</th>
                                                                <th>Institution</th>
                                                                <th>Startjahr</th>
                                                                <th>Abschlussdatum</th>
                                                                <th>Grade</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($qualifications as $qualification)
                                                            <tr>
                                                            
                                                                <td>{{ $qualification->degree }}</td>
                                                                <td>{{ $qualification->major }}</td>
                                                                <td>{{ $qualification->institution }}</td>
                                                                <td>{{ $qualification->q_start_year }}</td>
                                                                <td>{{ $qualification->q_end_year }}</td>
                                                                <td>{{ $qualification->grade }}</td> 
                                                                <td>
                                                                    <form id="delete-form-{{ $qualification->id }}" action="{{ route('emp.qualification.delete', $qualification->id) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="button" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1 delete-button" data-id="{{ $qualification->id }}">
                                                                            <i class="feather icon-trash-2"></i>
                                                                        </button>
                                                                    </form>    
                                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#q_edit{{$qualification->id}}"><i class="feather icon-edit"></i></button>
                                                                        <!-- Qualification Edit Model: Start -->

                                                                    <div class="modal fade text-left" id="q_edit{{$qualification->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  " role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header">
                                                                                    <h4 class="modal-title" id="myModalLabel17">{{ $qualification->degree }}</h4>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <form class="form-horizontal qualification_edit" id="qualification_edit" novalidate method="post" action="{{ action('App\Http\Controllers\QualificationController@update') }}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                            @csrf
                                                                                    <div class="modal-body"> 
                                                                                        <input type="hidden" name="active_tab" id="active_tab" value="qualification">  
                                                                                        <div class="row"> 
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">
                                                                                                    Fakultät
                                                                                                    </label>
                                                                                                    <input type="hidden" name="emp_id" value="{{$data->id}}">
                                                                                                    <input type="hidden" name="id" value="{{$qualification->id}}">
                                                                                                    <input type="text" class="form-control"  name="degree"  value="{{ $qualification->degree}}"  required>
                                                                                                    @if ($errors->has('degree'))<p style="color:red;">{!!$errors->first('degree')!!}</p>@endif
                                                                                                </div>
                                                                                            </div> 
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">
                                                                                                    Wesentlich
                                                                                                    </label>
                                                                                                
                                                                                                    <input type="text" class="form-control"  name="major"  value="{{ $qualification->major}}" required>
                                                                                                    @if ($errors->has('major'))<p style="color:red;">{!!$errors->first('major')!!}</p>@endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">
                                                                                                    Institution
                                                                                                    </label>
                                                                                                
                                                                                                    <input type="text" class="form-control"  name="institution"  value="{{ $qualification->institution}}" required>
                                                                                                    @if ($errors->has('institution'))<p style="color:red;">{!!$errors->first('institution')!!}</p>@endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">
                                                                                                    Startjahr
                                                                                                    </label> 
                                                                                                    <input type="date" class="form-control"  name="q_start_year"  value="{{ $qualification->q_start_year}}" required>
                                                                                                    @if ($errors->has('q_start_year'))<p style="color:red;">{!!$errors->first('q_start_year')!!}</p>@endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">
                                                                                                    Abschlussdatum
                                                                                                    </label> 
                                                                                                    <input type="date" class="form-control"  name="q_end_year"  value="{{ $qualification->q_end_year}}" required>
                                                                                                    @if ($errors->has('q_end_year'))<p style="color:red;">{!!$errors->first('q_end_year')!!}</p>@endif
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="Title">
                                                                                                    Grad
                                                                                                    </label>
                                                                                                
                                                                                                    <input type="text" class="form-control"  name="grade"  value="{{ $qualification->grade}}" required>
                                                                                                    @if ($errors->has('grade'))<p style="color:red;">{!!$errors->first('grade')!!}</p>@endif
                                                                                                </div>
                                                                                            </div> 
                                                                                        </div>  
                                                                                    </div>
                                                                                    <div class="modal-footer">
                                                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                                        <button type="submit" class="btn btn-primary waves-effect waves-light"  >speichern</button>
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
                                        </div>
                                    </div> 
                                </div>
                                <div class="tab-pane" id="furtherEducation_fill" role="tabpanel" aria-labelledby="furtherEducation_fill">
                                    <div class="row"> 
                                        <div class="col-12">
                                            <div class="form-group">
                                                <div class="card-body" style="padding-top: 0px;">
                                                @if (count($errors) > 0)
                                                    <div class="alert alert-danger">
                                                        <ul>
                                                            @foreach ($errors->all() as $error)
                                                                <li>{{ $error }}</li>
                                                            @endforeach
                                                        </ul>
                                                    </div>
                                                @endif
                                                
                                                <button type="button" class="btn btn-outline-primary waves-effect waves-light float-right" data-toggle="modal" data-target="#add_new_further">
                                                    Erstellen
                                                </button>
                                                <div class="modal fade text-left" id="add_new_further" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document" style="max-width:1174px !important;">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title" id="myModalLabel17">AUSBILDUNG & WEITERBILDUNG</h4>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">×</span>
                                                                </button>
                                                            </div>
                                                                <form id="create_further_education" action="{{ route('f.education.store')}}" method="post"  class="custom-file-upload" enctype="multipart/form-data" >
                                                                    @csrf
                                                                    <div class="modal-body"> 
                                                                        <input type="hidden" name="active_tab" id="active_tab" value="qualification">
                                                                        <div class="table-responsive"> 
                                                                            <table class="table" id="f_education_table"  > 
                                                                                <thead>
                                                                                    <tr> 
                                                                                        <th>Kurs</th>
                                                                                        <th>Major</th>
                                                                                        <th>Institution</th>
                                                                                        <th>Jahr</th>
                                                                                        <th>Fähigkeiten</th>
                                                                                        <th>Beschreibung</th>
                                                                                        <th>Action</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                    
                                                                                <tbody>
                                                                                    <tr>
                                                                                    <input type="hidden" name="fe[0][emp_id]" value="{{$data->id}}" > 

                                                                                        <td><input type="text" class="form-control required" placeholder="Kurs..." name="fe[0][course]"></td>
                                                                                    
                                                                                        <td><input type="text" class="form-control required" placeholder="Major..." name="fe[0][major]"></td>
                                                                        
                                                                                        <td><input type="text" class="form-control required" placeholder="Institution..." name="fe[0][institution]"></td>
                                                                                    
                                                                                        <td><input type="date" class="form-control required" placeholder="Jahr..." name="fe[0][year]"></td>
                                                                                        <td><input type="text" class="form-control required" placeholder="Fähigkeiten..." name="fe[0][skill]"></td>
                                                                                        <td><textarea class="form-control" placeholder="Beschreibung...." name="fe[0][description]"></textarea></td> 
                                                                                        <td>
                                                                                        <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_f_education"><i class="feather icon-plus"></i></button>
                                                                                        </td>
                                                                                    </tr>
                                                                                </tbody>  
                                                                            </table>
                                                                        </div> 
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">abbrechen</button>
                                                                        <button type="submit" class="btn btn-primary waves-effect waves-light"  >speichern</button>
                                                                    </div>
                                                                </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            
                                                <table class="table" id="a">
                                                    <thead>
                                                        <tr>
                                                            <th>Kurs</th>
                                                            <th>Major</th>
                                                            <th>Institution</th>
                                                            <th>Jahr</th>
                                                            <th>Fähigkeiten</th>
                                                            <th>Beschreibung</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($feducation as $fedu)
                                                        <tr>
                                                        
                                                            <td>{{ $fedu->course }}</td>
                                                            <td>{{ $fedu->major }}</td>
                                                            <td>{{ $fedu->institution }}</td>
                                                            <td>{{ $fedu->year }}</td>
                                                            <td>{{ $fedu->skill }}</td>
                                                            <td>{{ $fedu->description }}</td>
                                                        
                                                            <td>
                                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-danger delete-button-f" data-id="{{ $fedu->id }}">
                                                                    <i class="feather icon-trash-2"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#fe_edit{{$fedu->id}}"><i class="feather icon-edit"></i></button> 
                                                                <div class="modal fade text-left" id="fe_edit{{$fedu->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel17" style="display: none;" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable  " role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h4 class="modal-title" id="myModalLabel17">Bearbeiten - {{ $fedu->course}}</h4>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">×</span>
                                                                                </button>
                                                                            </div>
                                                                            <form class="furtherEducation" novalidate method="POST" action="{{ route('f.education.update')}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                @csrf 
                                                                                <div class="modal-body">
                                                                                    <div class="row"> 
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Fakultät
                                                                                                </label>
                                                                                                <input type="hidden" name="emp_id" value="{{$data->id}}">
                                                                                                <input type="hidden" name="id" value="{{$fedu->id}}">
                                                                                                <input type="text" class="form-control"  name="course"  value="{{ $fedu->course}}"  required>
                                                                                                @if ($errors->has('degree'))<p style="color:red;">{!!$errors->first('degree')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Wesentlich
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="major"  value="{{ $fedu->major}}" required>
                                                                                                @if ($errors->has('major'))<p style="color:red;">{!!$errors->first('major')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Institution
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="institution"  value="{{ $fedu->institution}}" required>
                                                                                                @if ($errors->has('institution'))<p style="color:red;">{!!$errors->first('institution')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Jahr
                                                                                                </label>
                                                                                            
                                                                                                <input type="date" class="form-control"  name="year"  value="{{ $fedu->year}}" required>
                                                                                                @if ($errors->has('year'))<p style="color:red;">{!!$errors->first('year')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Fähigkeiten
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="skill"  value="{{ $fedu->skill}}" required>
                                                                                                @if ($errors->has('skill'))<p style="color:red;">{!!$errors->first('skill')!!}</p>@endif
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-md-12">
                                                                                            <div class="form-group">
                                                                                                <label for="Title">
                                                                                                Beschreibung
                                                                                                </label>
                                                                                            
                                                                                                <input type="text" class="form-control"  name="description"  value="{{ $fedu->description}}" required>
                                                                                                @if ($errors->has('description'))<p style="color:red;">{!!$errors->first('description')!!}</p>@endif
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
                                                            </td>
                                                        </tr> 
                                                        @endforeach
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
    </section>
                                        
                                                    

                                                        


<script>
    document.getElementById('add_qualification').addEventListener('click', function () {
        let i = document.querySelectorAll('#qualification_table tbody tr').length;
        let qualificationTable = document.getElementById('qualification_table').getElementsByTagName('tbody')[0];

        let newRow = document.createElement('tr');
            newRow.innerHTML = `
                <input type="hidden" name="qual[${i}][emp_id]" value="{{$data->id}}">
                <td><input type="text" class="form-control" placeholder="Degree" name="qual[${i}][degree]"></td>
                <td><input type="text" class="form-control" placeholder="Major" name="qual[${i}][major]"></td>
                <td><input type="text" class="form-control" placeholder="Institution" name="qual[${i}][institution]"></td>
                <td><input type="date" class="form-control" placeholder="Startjahr" name="qual[${i}][q_start_year]"></td>
                <td><input type="date" class="form-control" placeholder="Abschlussdatum" name="qual[${i}][q_end_year]"></td>
                <td><input type="text" class="form-control" placeholder="Grade" name="qual[${i}][grade]"></td>
                <td><button type="button" class="btn btn-icon rounded-circle btn-outline-primary remove_qualification"><i class="feather icon-minus-square"></i></button></td>
            `;

            qualificationTable.appendChild(newRow);
        });

        document.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove_qualification')) {
                event.target.closest('tr').remove();
            }
        });

    document.getElementById('qualification_form').addEventListener('submit', function (e) {
    e.preventDefault();

    let formData = new FormData(this);

    // Store active tab in sessionStorage
    sessionStorage.setItem('active_tab', document.getElementById('active_tab').value);

    fetch('{{ route("emp.qualification") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                title: 'Erfolg!',
                text: data.message,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();  // Reload page after success
            });
        } else {
            let errorMessages = '';
            for (let key in data.errors) {
                errorMessages += `${data.errors[key][0]}<br>`;
            }
            Swal.fire('Fehler!', errorMessages, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Fehler!', 'Etwas ist schief gelaufen.', 'error');
    });
});

// Attach submit event listener to dynamically update the form using AJAX
document.querySelectorAll('.qualification_edit').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        // Store active tab before the reload
        sessionStorage.setItem('active_tab', document.getElementById('active_tab').value);

        fetch(this.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Erfolg!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Close the modal programmatically
                    $(`#q_edit${form.querySelector('input[name="id"]').value}`).modal('hide');
                    location.reload();  // Reload the page after success
                });
            } else {
                let errorMessages = '';
                for (let key in data.errors) {
                    errorMessages += `${data.errors[key][0]}<br>`;
                }
                Swal.fire('Fehler!', errorMessages, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Fehler!', 'Etwas ist schief gelaufen.', 'error');
        });
    });
});

 
// Set the active tab after reload
window.addEventListener('load', function () {
    let activeTab = sessionStorage.getItem('active_tab');
    if (activeTab) {
        document.getElementById('active_tab').value = activeTab;
        sessionStorage.removeItem('active_tab');  // Clear storage after setting tab
    }
});

</script>


<script>
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function () {
            let qualificationId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Bist du sicher?',
                text: 'Diese Aktion kann nicht rückgängig gemacht werden!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${qualificationId}`).submit();
                }
            });
        });
    });
</script>




<script>
    // Handle dynamic row addition
    document.getElementById('add_f_education').addEventListener('click', function () {
        let i = document.querySelectorAll('#f_education_table tbody tr').length;
        let newRow = document.createElement('tr');
        newRow.innerHTML = `
            <input type="hidden" name="fe[${i}][emp_id]" value="{{$data->id}}">
            <td><input type="text" class="form-control required" placeholder="Kurs..." name="fe[${i}][course]"></td>
            <td><input type="text" class="form-control required" placeholder="Major..." name="fe[${i}][major]"></td>
            <td><input type="text" class="form-control required" placeholder="Institution..." name="fe[${i}][institution]"></td>
            <td><input type="date" class="form-control required" placeholder="Jahr..." name="fe[${i}][year]"></td>
            <td><input type="text" class="form-control required" placeholder="Fähigkeiten..." name="fe[${i}][skill]"></td>
            <td><textarea class="form-control" placeholder="Beschreibung...." name="fe[${i}][description]"></textarea></td>
            <td><button type="button" class="btn btn-icon rounded-circle btn-outline-danger remove-row"><i class="feather icon-minus"></i></button></td>
        `;
        document.querySelector('#f_education_table tbody').appendChild(newRow);
    });

    // Remove row dynamically
    document.addEventListener('click', function (e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
        }
    });

    // Handle form submission via AJAX
    document.getElementById('create_further_education').addEventListener('submit', function (e) {
        e.preventDefault();  // Prevent default form submission

        let formData = new FormData(this);

        fetch("{{ route('f.education.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Erfolg!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();  // Reload page after success
                });
            } else {
                let errorMessages = '';
                for (let key in data.errors) {
                    errorMessages += `${data.errors[key][0]}<br>`;
                }
                Swal.fire('Fehler!', errorMessages, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Fehler!', 'Etwas ist schief gelaufen.', 'error');
        });
    });

    document.querySelectorAll('.furtherEducation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();  // Prevent default form submission

            let formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: 'Erfolg!',
                        text: data.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();  // Reload the page to reflect updates
                    });
                } else {
                    let errorMessages = '';
                    for (let key in data.errors) {
                        errorMessages += `${data.errors[key][0]}<br>`;
                    }
                    Swal.fire('Fehler!', errorMessages, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Fehler!', 'Etwas ist schief gelaufen.', 'error');
            });
        });
    });


    document.querySelectorAll('.delete-button-f').forEach(button => {
        button.addEventListener('click', function () {
            let feduId = this.getAttribute('data-id');

            Swal.fire({
                title: 'Bist du sicher?',
                text: 'Diese Aktion kann nicht rückgängig gemacht werden!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/f_education_delete/${feduId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire('Gelöscht!', data.message, 'success').then(() => {
                                location.reload();  // Reload the page to reflect deletion
                            });
                        } else {
                            Swal.fire('Fehler!', 'Etwas ist schief gelaufen.', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Fehler!', 'Etwas ist schief gelaufen.', 'error');
                    });
                }
            });
        });
    });


</script>