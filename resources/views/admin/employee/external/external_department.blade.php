@extends('admin.layouts.app')
@section('title') Externe Personalabteilungen @stop
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
                                <h2 class="content-header-title float-left mb-0">Externe Personalabteilungen</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="{{ url('/external_personal') }}">Externer Mitarbeiter</a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                          
            <div class="content-body">
             <!-- Table Hover Animation start -->
             <div class="row" id="table-hover-animation">
                    <div class="col-md-6 col-12 mb-1">
                    <form action="">
                            <fieldset>
                                <div class="input-group">
                               
                                    <input type="text" class="form-control" placeholder="Geben Sie die Details Ihrer Suche ein" aria-describedby="button-addon2" name="search" >
                                    <div class="input-group-append" id="button-addon2">
                                        <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                    </div>
                                
                                </div>
                            
                            </fieldset>
                            </form>
                        </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Externe Personalabteilungen</h4>
                            </div>
                           
                            <div class="card-content">
                                    <div class="card-body">
                               
                                         <form novalidate action="{{ action('App\Http\Controllers\ExternalDepartmentsController@store')}}" method="post">
                                            @csrf
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <div class="card-body">
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
                                                            @if(DB::table('user_rolls')
                                                                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                        ->where('user_rolls.item_id', '=', 'Employee')
                                                                        ->where('user_rolls.is_add', '=', 'on')
                                                                        ->first())
                                                                <table class="table" id="add_department">
                                                                
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Hersteller</th>
                                                                            <th>Abteilung</th>
                                                                            <th>Ansprechpartner</th>
                                                                            <th>Position</th>
                                                                            <th>Email</th>
                                                                            <th>Phone</th>
                                                                            <th>Festnetznummer</th>
                                                                            <th>Büro</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    
                                                                    <tbody>
                                                                        <form method="post" >
                                                                        <tr>
                                                                            <input type="hidden" name="brand[0][external_id]" value="{{$external->id}}">
                                                                            <td>
                                                                                <input type="text" class="form-control required" disabled value="{{$external->company_name}}"> 
                                                                                <input type="hidden" name="brand[0][status]" value="Unpublished">
                                                                            </td>
                                                                        
                                                                            <td><input type="text" class="form-control required" placeholder="Abteilung" name="brand[0][department]"></td>
                                                                            <td><input type="text" class="form-control required" placeholder="Gesprächspartner" name="brand[0][name]"></td>
                                                                            <td><input type="text" class="form-control required" placeholder="Position" name="brand[0][position]"></td>
                                                            
                                                                            <td><input type="text" class="form-control required" placeholder="E-Mail" name="brand[0][email]"></td>
                                                                        
                                                                            <td><input type="text" class="form-control required" placeholder="Handynummer" name="brand[0][phone]"></td>
                                                                            <td><input type="text" class="form-control required" placeholder="Festnetznummer" name="brand[0][home]"></td>
                                                                            <td><input type="text" class="form-control required" placeholder="Büro-Telefonnummer" name="brand[0][office]"></td> 
                                                                            <td>
                                                                            <button type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_brand"><i class="feather icon-plus"></i></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                            
                                                                            <div class="col-8">
                                                                            <div class="input-group">
                                                                                    <button type="submit" class="btn btn-outline-success mr-1 mb-1"><i class="feather icon-save"></i> Datensatz speichern</button>
                                                                                    <a type="button" href="{{ url('/external_personal')}}"class="btn btn-outline-warning mr-1 mb-1"><i class="feather icon-chevrons-left"></i> Zurück</a>
                                                                                   
                                                                                </div>
                                                                                
                                                                            </div>
                                                                            </form>
                                                                </table>
                                                                @endif
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Unternehmen/Marke</th>
                                                                            <th>Abteilung</th>
                                                                            <th>Ansprechpartner</th>
                                                                            <th>Position</th>
                                                                            <th>Email</th>
                                                                            <th>Phone</th>
                                                                            <th>Festnetznummer</th>
                                                                            <th>Büro</th>
                                                                            <th>Adress</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                <tbody>
                                                                        @foreach ($department as $br) 
                                                                        <tr>
                                                                        
                                                                            <td>{{ $br->uname}}</td>
                                                                            <td>{{ $br->department}}</td>
                                                                            <td>{{ $br->name}}</td>
                                                                            <td>{{ $br->position}}</td>
                                                                            <td>{{ $br->email}}</td>
                                                                            <td>{{ $br->phone}}</td>
                                                                            <td>{{ $br->home}}</td>
                                                                            <td>{{ $br->office}}</td> 
                                                                          
                                                                        <td>
                                                                        <a type="button" href="{{ route('external.department.delete',['id'=>$br->id] )}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                                        <button type="button"  class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" data-toggle="modal" data-target="#default"><i class="feather icon-edit"></i></button>
                                                                          <!-- Modal -->
                                                                                <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <h4 class="modal-title" id="myModalLabel1">Bearbeiten</h4>
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                    <div class="modal-body">
                                                                                                        <form class="form-horizontal" novalidate method="post" action="{{ route('external.department.update',['id'=>$br->id])}}" class="custom-file-upload" enctype="multipart/form-data">
                                                                                                            @csrf
                                                                                                            <fieldset> 
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Title">
                                                                                                                            Abteilung
                                                                                                                            </label>
                                                                                                                            <input type="hidden" name="status" value="{{ $external->id }}">
                                                                                                                            <input type="hidden" name="status" value="Unpublished">
                                                                                                                            <input type="text" class="form-control"  name="department" value="{{$br->department}}" required>
                                                                                                                            @if ($errors->has('brand_department'))<p style="color:red;">{!!$errors->first('brand_department')!!}</p>@endif
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </fieldset>

                                                                                                            <fieldset> 
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Title">
                                                                                                                            Gesprächspartner
                                                                                                                            </label>
                                                                                                                        
                                                                                                                            <input type="text" class="form-control"  name="name" value="{{$br->name}}" required>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </fieldset>

                                                                                                            <fieldset> 
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Title">
                                                                                                                            Position
                                                                                                                            </label>
                                                                                                                        
                                                                                                                            <input type="text" class="form-control"  name="position" value="{{$br->position}}" required>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </fieldset>

                                                                                                            <fieldset> 
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Title">
                                                                                                                            E-Mail
                                                                                                                            </label>
                                                                                                                        
                                                                                                                            <input type="text" class="form-control"  name="email" value="{{$br->email}}" required>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </fieldset>

                                                                                                            <fieldset> 
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Title">
                                                                                                                            Phone
                                                                                                                            </label>
                                                                                                                        
                                                                                                                            <input type="text" class="form-control"  name="phone" value="{{$br->phone}}" required>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </fieldset>

                                                                                                            <fieldset> 
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Title">
                                                                                                                            Festnetznummer
                                                                                                                            </label>
                                                                                                                        
                                                                                                                            <input type="text" class="form-control"  name="home" value="{{$br->home}}" required>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </fieldset>

                                                                                                            <fieldset> 
                                                                                                                <div class="row">
                                                                                                                    <div class="col-md-12">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Title">
                                                                                                                            Büro-Telefonnummer
                                                                                                                            </label>
                                                                                                                        
                                                                                                                            <input type="text" class="form-control"  name="office" value="{{$br->office}}" required>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </fieldset>
                                                                                                            
                                                                                                            <div class="modal-footer">
                                                                                                                <button type="submit" class="btn btn-primary">Submit</button>
                                                                                                            </div>
                                                                                                        </form>
                                                                                                    </div>
                                                                                                    </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                        <!-- Modal End -->   
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
                </div>
                <!-- Table head options end -->
                {{$department->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
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

<script>
        var i = 0;
        $('#add_brand').click(function(){
            ++i;
            $('#add_department').append(
                '       <tr><input type="hidden" name="brand['+i+'][external_id]" value="{{$external->id}}"> <input type="hidden" name="brand['+i+'][status]" value="Unpublished"><td><input type="text" class="form-control required" disabled value="{{$external->company_name}}"></td><td><input type="text" class="form-control required" placeholder="Abteilung" name="brand['+i+'][department]"></td><td><input type="text" class="form-control required" placeholder="Gesprächspartner" name="brand['+i+'][name]"></td><td><input type="text" class="form-control required" placeholder="Position" name="brand['+i+'][position]"></td><td><input type="text" class="form-control required" placeholder="E-Mail" name="brand['+i+'][email]"></td><td><input type="text" class="form-control required" placeholder="Handynummer" name="brand['+i+'][phone]"></td><td><input type="text" class="form-control required" placeholder="Festnetznummer" name="brand['+i+'][home]"></td><td><input type="text" class="form-control required" placeholder="Büro-Telefonenummer" name="brand['+i+'][office]"></td><td><button type="button"  class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1" id="add_remove"><i class="feather icon-minus-square"></i></button></td></tr> ' 
                );
        });

        $(document).on('click', '#add_remove', function(){
            $(this).parents('tr').remove();
        })

    </script>
@endsection