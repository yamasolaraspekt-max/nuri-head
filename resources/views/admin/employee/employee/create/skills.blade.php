            
            
            <div class="card-body"> 
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-fill" id="myTab" role="tablist"> 
                    <li class="nav-item">
                        <a class="nav-link active" id="messages-tab-fill" data-toggle="tab" href="#messages-fill" role="tab" aria-controls="messages-fill" aria-selected="false">Fähigkeiten</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="settings-tab-fill" data-toggle="tab" href="#settings-fill" role="tab" aria-controls="settings-fill" aria-selected="false">Andere Fähigkeiten</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content pt-1">
                    
                    <div class="tab-pane active" id="messages-fill" role="tabpanel" aria-labelledby="messages-tab-fill">
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
                                        
                                        <!-- Table with outer spacing -->
                                        <div class="table-responsive">  

                                                <form novalidate action="{{ route('skills.save')}}" method="post" class="custom-file-upload" enctype="multipart/form-data">
                                                    @csrf
                                                    <table class="table" id="skill_table" style="background:#0c4da2; color:white; display:none;"> 
                                                        <thead>
                                                            <tr>
                                                                <th>Mitarbeitername</th>
                                                                <th>Gewerk</th>
                                                                <th>Beratung</th>
                                                                <th>Planung</th>
                                                                <th>Kalkulation</th>
                                                                <th>Montage</th>
                                                                <th>Projektierung</th>
                                                                <th>Bauleitung</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead> 
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="skill[0][emp_id]" value="{{$data->id}}">
                                                                    <input disabled type="text" class="form-control required" value="{{$data->name}} {{$data->lastname}}">
                                                                </td>
                                                                <td> 
                                                                    <select class="form-control" name="skill[0][product_id]">
                                                                        @foreach ($products as $product)
                                                                            <option value="{{$product->id}}">{{ $product->article_group }}</option> 
                                                                        @endforeach 
                                                                    </select>
                                                                </td>
                                                                <td><select class="form-control" name="skill[0][advice]">@for ($i = 1; $i <= 5; $i++)<option value="{{$i}}">{{$i}}</option>@endfor</select></td>
                                                                <td><select class="form-control" name="skill[0][plan]">@for ($i = 1; $i <= 5; $i++)<option value="{{$i}}">{{$i}}</option>@endfor</select></td>
                                                                <td><select class="form-control" name="skill[0][calculation]">@for ($i = 1; $i <= 5; $i++)<option value="{{$i}}">{{$i}}</option>@endfor</select></td>
                                                                <td><select class="form-control" name="skill[0][montage]">@for ($i = 1; $i <= 5; $i++)<option value="{{$i}}">{{$i}}</option>@endfor</select></td>
                                                                <td><select class="form-control" name="skill[0][project_planing]">@for ($i = 1; $i <= 5; $i++)<option value="{{$i}}">{{$i}}</option>@endfor</select></td>
                                                                <td><select class="form-control" name="skill[0][site_management]">@for ($i = 1; $i <= 5; $i++)<option value="{{$i}}">{{$i}}</option>@endfor</select></td>
                                                                <td>
                                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_skill">
                                                                        <i class="feather icon-plus"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        </tbody> 
                                                    </table> 

                                                    <!-- Move buttons outside the table -->
                                                    <div class="col-8 mt-2">
                                                        <div class="input-group">
                                                            <button type="submit" class="btn btn-outline-primary mr-1 mb-1">
                                                                <i class="feather icon-save"></i> Datensatz speichern
                                                            </button>
                                                            <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1">
                                                                <i id="button" class="fa fa-chevron-down"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>  
                                        </div>
                                        
                                        <!-- Display Existing Skills -->
                                        <div class="table-responsive"> 
                                            <table class="table" id="existing_skill_table">
                                                <thead>
                                                    <tr>
                                                        <th>Mitarbeitername</th>
                                                        <th>Gewerk</th>
                                                        <th>Beratung</th>
                                                        <th>Planung</th>
                                                        <th>Kalkulation</th>
                                                        <th>Montage</th>
                                                        <th>Projektierung</th>
                                                        <th>Bauleitung</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($skills as $skil)
                                                        <tr> 
                                                            <td>{{ $data->name }} {{ $data->lastname }}</td>
                                                            <td>{{ $skil->article_group }}</td>
                                                            @foreach (['advice', 'plan', 'calculation', 'montage', 'project_planing', 'site_management'] as $field)
                                                                <td>
                                                                    <div class="fonticon-wrap">
                                                                        @for ($i = 1; $i <= $skil->$field; $i++)
                                                                            <i class="fa fa-star" style="color:gold"></i>
                                                                        @endfor
                                                                    </div>
                                                                </td>
                                                            @endforeach
                                                            <td>
                                                                <a href="{{ url('skill_delete/'.$skil->id)}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1">
                                                                    <i class="feather icon-trash-2"></i>
                                                                </a>
                                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#skill_edit{{$skil->id}}">
                                                                    <i class="feather icon-edit"></i>
                                                                </button>
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
                    <div class="tab-pane" id="settings-fill" role="tabpanel" aria-labelledby="settings-tab-fill">
                        <div class="table-responsive"> 
                                <form novalidate action="{{ route('skills.save')}}" method="post" class="custom-file-upload" enctype="multipart/form-data">
                                    @csrf
                                    <table class="table" id="other_skill_table" style="background:#0c4da2; color:white; display:none;">
                                        <thead>
                                            <tr>
                                                <th>Mitarbeitername</th>
                                                <th>Fähigkeiten</th>
                                                <th>Kompetenz</th>
                                                <th>Erfahrung</th>
                                                <th>Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="oskill[0][emp_id]" value="{{$data->id}}">
                                                    <input disabled type="text" class="form-control required" value="{{$data->name}} {{$data->lastname}}">
                                                </td>
                                                <td><input type="text" class="form-control required" name="oskill[0][skills]"></td>
                                                <td> 
                                                    <select class="form-control" name="oskill[0][proficiency]">
                                                        <option selected value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" class="form-control required" name="oskill[0][year_experience]"></td>
                                                <td>
                                                    <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_other_skill">
                                                        <i class="feather icon-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- Move buttons outside the table -->
                                    <div class="col-8 mt-2">
                                        <div class="input-group">
                                            <button type="submit" class="btn btn-outline-primary mr-1 mb-1">
                                                <i class="feather icon-save"></i> Datensatz speichern
                                            </button>
                                            <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1">
                                                <i id="button" class="fa fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form> 
                        </div>

                        <!-- Display Existing Other Skills -->
                        <div class="table-responsive">
                            <table class="table" id="existing_other_skills_table">
                                <thead>
                                    <tr>
                                        <th>Mitarbeitername</th>
                                        <th>Fähigkeiten</th>
                                        <th>Kompetenz</th>
                                        <th>Erfahrung</th>
                                        <th>Aktion</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($otherskill as $oskill)
                                        <tr> 
                                            <td>{{ $data->name }} {{ $data->lastname }}</td>
                                            <td>{{ $oskill->skills }}</td>
                                            <td>
                                                <div class="fonticon-wrap">
                                                    @for ($i = 1; $i <= $oskill->proficiency; $i++)
                                                        <i class="fa fa-star" style="color:gold"></i>
                                                    @endfor
                                                </div>
                                            </td>
                                            <td>{{ $oskill->year_experience }}</td>
                                            <td>
                                                <a type="button" href="{{ url('other_skill_delete/'.$oskill->id)}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1">
                                                    <i class="feather icon-trash-2"></i>
                                                </a>
                                                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#skill_edit{{$oskill->id}}">
                                                    <i class="feather icon-edit"></i>
                                                </button>                                                                                                    
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>


                                                    
    <script>

        function addSkill() {
        var x = document.getElementById("skill_table");
        if (x.style.display === "none") {
        x.style.display = "block";
        } else {
        x.style.display = "none";
        }
        }

        function addOtherSkill() {
        var x = document.getElementById("other_skill_table");
        if (x.style.display === "none") {
        x.style.display = "block";
        } else {
        x.style.display = "none";
        }
        }
    </script>

    <script>
    var os = 0;

    document.getElementById('add_other_skill').addEventListener('click', function() {
        ++os;
        var otherSkillTable = document.getElementById('other_skill_table');

        var newRow = document.createElement('tr'); // Ensure that the row is created properly
        newRow.innerHTML = `
            <input type="hidden" name="oskill[${os}][emp_id]" value="{{$data->id}}">
            <td><input disabled type="text" class="form-control required" value="{{$data->name}} {{$data->lastname}}"></td>
            <td><input type="text" class="form-control required" name="oskill[${os}][skills]"></td>
            <td>
                <select class="form-control" name="oskill[${os}][proficiency]">
                    <option selected value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select>
            </td>
            <td><input type="text" class="form-control required" name="oskill[${os}][year_experience]"></td>
            <td>
                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 remove_other_skill">
                    <i class="feather icon-minus-square"></i>
                </button>
            </td>
        `;

        otherSkillTable.appendChild(newRow);
    });

    document.addEventListener('click', function(event) {
        if (event.target.closest('.remove_other_skill')) {
            event.target.closest('tr').remove();
        }
    });
    </script>

 <script>
    var s = 0;

    document.getElementById('add_skill').addEventListener('click', function(){
        ++s;
        var skillTable = document.getElementById('skill_table');

        var newRow = document.createElement('tr'); // Ensure <tr> is properly created
        newRow.innerHTML = `
            <input type="hidden" name="skill[${s}][emp_id]" value="{{$data->id}}">
            <td><input disabled type="text" class="form-control required" value="{{$data->name}} {{$data->lastname}}"></td>
            <td>
                <select class="form-control" name="skill[${s}][product_id]">
                    @foreach ($products as $product)
                        <option value="{{$product->id}}">{{$product->article_group}}</option>
                    @endforeach
                </select>
            </td>
            <td><select class="form-control" name="skill[${s}][advice]">
                <option value="1">1</option><option value="2">2</option>
                <option value="3">3</option><option value="4">4</option><option value="5">5</option>
            </select></td>
            <td><select class="form-control" name="skill[${s}][plan]">
                <option value="1">1</option><option value="2">2</option>
                <option value="3">3</option><option value="4">4</option><option value="5">5</option>
            </select></td>
            <td><select class="form-control" name="skill[${s}][calculation]">
                <option value="1">1</option><option value="2">2</option>
                <option value="3">3</option><option value="4">4</option><option value="5">5</option>
            </select></td>
            <td><select class="form-control" name="skill[${s}][montage]">
                <option value="1">1</option><option value="2">2</option>
                <option value="3">3</option><option value="4">4</option><option value="5">5</option>
            </select></td>
            <td><select class="form-control" name="skill[${s}][project_planing]">
                <option value="1">1</option><option value="2">2</option>
                <option value="3">3</option><option value="4">4</option><option value="5">5</option>
            </select></td>
            <td><select class="form-control" name="skill[${s}][site_management]">
                <option value="1">1</option><option value="2">2</option>
                <option value="3">3</option><option value="4">4</option><option value="5">5</option>
            </select></td>
            <td>
                <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1 remove_skill">
                    <i class="feather icon-minus-square"></i>
                </button>
            </td>
        `;

        skillTable.appendChild(newRow);
    });

    document.addEventListener('click', function(event) {
        if (event.target.closest('.remove_skill')) {
            event.target.closest('tr').remove();
        }
    });
</script>
