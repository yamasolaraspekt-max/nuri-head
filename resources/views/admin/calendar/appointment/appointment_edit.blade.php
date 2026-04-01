@extends('admin.layouts.app')
@section('title')  TERMIN-KALENDER | BEARBEITEN  @stop

@section('style')
<!-- Include stylesheet -->
<!-- <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css')}}"> -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">


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
                                <h2 class="content-header-title float-left mb-0">TERMIN-KALENDER</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                        </li>
                                          <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Bearbeiten</a>
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
                    <div class="col-12">
                        <div class="card"> 
                            <div class="card-content">
                                <div class="card-body">
                                <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\RadiatorInstallationController@index')}}">
                                            <fieldset>
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                    <div class="input-group-append" id="button-addon2">
                                                        <button class="btn btn-primary" type="submit">Go</button>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div> 
                                    
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th scope="col">ID</th>
                                                    <th scope="col">Kunde</th>
                                                    <th scope="col">Ort</th>  
                                                    <th scope="col">Heizkörperkonfiguration</th>
                                                    <th scope="col">Action</th> 
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <form id="appointmentForm" method="post" action="">  
                                                     <div class="row" style="    display: flex; justify-content: flex-end;"> 
                                                        <div class="col-xl-2 col-md-2 col-12 mb-1" style="display: flex;  justify-content: space-around; align-items: baseline;">
                                                                <fieldset class="form-group"> 
                                                                    <div class="custom-dropdown">
                                                                        <select name="calendar_color" id="colorSelect" class="form-control select2"  >
                                                                            <option value="#FF5733" data-color="#FF5733"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#FFBD33" data-color="#FFBD33"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#75FF33" data-color="#75FF33"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#33FF57" data-color="#33FF57"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#33FFBD" data-color="#33FFBD"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#33C7FF" data-color="#33C7FF"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#335BFF" data-color="#335BFF"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#7533FF" data-color="#7533FF"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#BD33FF" data-color="#BD33FF"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#FF33C7" data-color="#FF33C7"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#FF3333" data-color="#FF3333"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#FF7F33" data-color="#FF7F33"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#FFD133" data-color="#FFD133"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#A3FF33" data-color="#A3FF33"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#33FF99" data-color="#33FF99"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#33FFD1" data-color="#33FFD1"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#3385FF" data-color="#3385FF"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#7F33FF" data-color="#7F33FF"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#FF33A3" data-color="#FF33A3"> <i class="feather icon-aperture"></i> </option>
                                                                            <option value="#FF3399" data-color="#FF3399"> <i class="feather icon-aperture"></i> </option>
                                                                        </select>
                                                                    </div>
                                                                </fieldset>
                                                                <fieldset>
                                                                <i class="feather icon-flag" id="priorityIcon" style="cursor: pointer;"></i>
                                                                <input type="checkbox" name="priority" id="priorityCheckbox" style="display: none;">
                                                                </fieldset>
                                                            </div> 

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-xl-6 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Kunde</label>
                                                                    <select name="customer_id" id="customer_id" class="form-control select22" style="width:100% !important;">
                                                                        <option value=""></option>
                                                                        @foreach ($customers as $customer)
                                                                            <option value="{{ $customer->id }}">{{ $customer->title}}.{{ $customer->name }} {{$customer->lastname}} - {{$customer->city}} </option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-xl-6 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="helpInputTop">Gwerke</label>
                                                                    <select name="product_id" id="product_id" class="form-control select22" style="width:100% !important;">
                                                                        <option value=""></option>
                                                                        <!-- Options will be loaded here via AJAX -->
                                                                    </select>
                                                                </fieldset>
                                                                <input type="hidden" name="selectProduct" id="selectProduct">
                                                            </div> 
                
                                                            <div class="tasks col-xl-12 d-flex"> 
                                                            <div class="table-responsive">
                                                                    <table class="table table-striped table-bordered" id="tasksTable">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Phase/Process</th>
                                                                                <th>Aufgaben</th>
                                                                                <th>Verantwortlich</th>
                                                                                <th>Actions</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody> 
                                                                            <tr>
                                                                                <td>
                                                                                    <select name="active[0][phase_id]" id="phase_id" class="form-control select22 phase_id" style="width:100% !important;"> 
                                                                                        <!-- Options will be loaded here via AJAX -->
                                                                                    </select>
                                                                                </td>
                                                                                <td> 
                                                                                    <select name="active[0][activity_id][]" id="activity_id" class="form-control select22 activity_id" style="width:100% !important;" multiple> 
                                                                                        <!-- Options will be loaded here via AJAX -->
                                                                                    </select>  
                                                                                </td>
                                                                                <td> 
                                                                                    <select name="active[0][employee_id][]" id="employee" class="form-control employee_id" style="width:100% !important;" multiple>
                                                                                        @foreach ($employee as $emp)
                                                                                        <option value="{{$emp->id}}" data-image="{{ asset('images/employee/'.$emp->image)}}">
                                                                                            {{$emp->name}} {{ $emp->lastname }}
                                                                                        </option>
                                                                                        @endforeach
                                                                                    </select>   
                                                                                </td>
                                                                                <td>
                                                                                    <button type="button" class="btn btn-flat-danger mr-1 mb-1 waves-effect waves-light add-task" id="add_task">
                                                                                        <i class="feather icon-plus"></i>
                                                                                    </button> 
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table> 
                                                                </div>

                                                            </div> 
                                                            <div class="col-xl-12 col-md-12 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Titel</label>
                                                                    <input type="text" class="form-control"  name="title">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-xl-3 col-md-6 col-12 ">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Startdatum</label>
                                                                    <input type="date" class="form-control"  name="start_date">
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-xl-3 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Enddatum</label>
                                                                    <input type="date" class="form-control"  name="end_date">
                                                                </fieldset>
                                                            </div>
                                                            <div class="col-xl-3 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Startzeit</label>
                                                                    <input type="time" class="form-control"  name="start_time">
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-xl-3 col-md-6 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Endzeit</label>
                                                                    <input type="time" class="form-control"  name="end_time">
                                                                </fieldset>
                                                            </div>

                                                            <div class="col-xl-12 col-md-12 col-12">
                                                                <fieldset class="form-group">
                                                                    <label for="basicInput">Notiz</label>
                                                                    <textarea name="description" id="" class="form-control" rows="4"></textarea>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                </form>
                                                <!-- calendar Modal ends--> 
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table head options end -->
                {{$data->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.success("{{ session('updated_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

        @if(Session::has('not_save'))
        toastr.error("{{ session('not_save') }}");
        @endif


       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>

<script>
$(document).ready(function() {
    $('#brand_id').select2();
    
});
</script>
@endsection