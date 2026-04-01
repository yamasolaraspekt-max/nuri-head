@extends('admin.layouts.app')
@section('title') Group Set @stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css"
    href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}">

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
                        <h2 class="content-header-title float-left mb-0">Group Set</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/')}}"> Dashboard</a> </li>
                                <li class="breadcrumb-item active"> Liste </li>
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
                             
                                    <div class="row align-items-center">
                                        <div class="col-md-9">
                                            <form action="{{ action('App\Http\Controllers\GroupSetController@index') }}">
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                    <div class="input-group-append">
                                                        <button class="btn btn-primary" type="submit">Go</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="col-md-3 text-right">
                                            <button type="button" class="btn btn-outline-primary btn-block" data-toggle="modal" data-target="#default">
                                                Neue hinzufügen
                                            </button>
                                        </div>
                                    </div>


                                        <!-- Modal -->
                                    <div class="modal fade text-left" id="default" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form-horizontal" method="post" action="{{ action('App\Http\Controllers\GroupSetController@store') }}" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="form-group">
                                                            <label>Gruppensatzname</label>
                                                            <input type="text" class="form-control" name="group_set" required>
                                                            @error('group_set')<p class="text-danger">{{ $message }}</p>@enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Sets</label>
                                                            <select class="form-control" name="master_set[]" id="master" multiple style="width:100%">
                                                                @foreach ($masters as $master)
                                                                    <option value="{{ $master->id }}">
                                                                        {{ $master->setname }} - {{ number_format($master->price, 2, ',', '.') }}€
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('master_set')<p class="text-danger">{{ $message }}</p>@enderror
                                                        </div>

                                                        <div class="form-group">
                                                            <label>Beschreibung</label>
                                                            <textarea name="content" class="form-control"></textarea>
                                                            @error('content')<p class="text-danger">{{ $message }}</p>@enderror
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">Einreichen</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                               
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Set Name</th>
                                                <th scope="col">Artikel_Gruppe</th>
                                                <th scope="col">Materialkosten EK</th>
                                                <th scope="col">Anteil Materialkosten % </th>
                                                <th scope="col">Montagekosten EK </th>
                                                <th scope="col">Anteil Lohnkosten % </th>
                                                <th scope="col">Total %</th>
                                                <th scope="col">Total €</th>
                                                <th scope="col">Text</th>
                                                <th scope="col">Aktion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $item)
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td><a href="{{ url('sets/'.$item->id) }}">{{ $item->group_set }}</a>
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light show_set"
                                                        data-id="{{$item->id}}">
                                                        <i class="feather icon-folder"></i> Sets anzeigen
                                                    </button>
                                                    <div class="sets" data-id="{{$item->id}}" style="display:none">
                                                        @foreach ($sets as $set)
                                                        @if($set->group_set_id == $item->id)
                                                        <a href="{{ url('sets/'.$set->master_id.'/'.$set->phase_id)}}">
                                                            <div class="card" style="    border: 1px solid #a2a0a0;">
                                                                <div class="card-header mx-auto pb-0">
                                                                    <div class="row m-0">
                                                                        <div class="col-sm-12 text-center">
                                                                            <h4>{{ $set->setname }}</h4>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-content">
                                                                    <div class="card-body text-center mx-auto">
                                                                        <div
                                                                            class="d-flex justify-content-between mt-2">
                                                                            <div class="uploads">
                                                                                <p
                                                                                    class="font-weight-bold font-medium-2 mb-0">
                                                                                    {{ number_format( $set->material_price, 2, ',', '.') }}€
                                                                                </p>
                                                                                <span>Material</span>
                                                                            </div>
                                                                            <div class="followers">
                                                                                <p
                                                                                    class="font-weight-bold font-medium-2 mb-0">
                                                                                    {{ number_format( $set->employee_price, 2, ',', '.') }}€
                                                                                </p>
                                                                                <span>Montage</span>
                                                                            </div>
                                                                            <div class="following">
                                                                                <p
                                                                                    class="font-weight-bold font-medium-2 mb-0">
                                                                                    {{ number_format( $set->price, 2, ',', '.') }}€
                                                                                </p>
                                                                                <span>Total</span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        @endif
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td> {{ number_format( $item->material_price, 2, ',', '.') }}€</td>
                                                <td> {{ number_format( $item->material_percent, 2, ',', '.') }}%</td>
                                                <td> {{ number_format( $item->employee_price, 2, ',', '.') }}€</td>
                                                <td> {{ number_format( $item->employee_percent, 2, ',', '.') }}%</td>
                                                <td>{{ number_format( $item->total, 2, ',', '.') }}€</td>
                                                        @php
                                                        $total_percent = 0;
                                                        $percent=DB::table('group_sets')->select('material_percent',
                                                        'employee_percent')->where('id', $item->id)->first();
                                                        $total_percent = $percent->material_percent +
                                                        $percent->employee_percent;
                                                        @endphp
                                                <td>{{ number_format( $total_percent, 2, ',', '.') }}%</td>
                                                <td>
                                                    <!-- Delete Modal -->
                                                    <button type="button"
                                                        class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1"
                                                        data-toggle="modal" data-target="#content{{$item->id}}">
                                                        <i class="feather icon-file-text"></i>
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade text-left" id="content{{$item->id}}"
                                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable"
                                                            role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    {{ $item->group_set }}
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p>{!! $item->content !!}</p>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Delete Modal -->
                                                </td>
                                                <td>

                                                    <!-- Delete Modal -->
                                                    <button type="button"
                                                        class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1"
                                                        data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                                        <i class="feather icon-trash"></i>
                                                    </button>

                                                    <!-- Modal -->
                                                    <div class="modal fade text-left" id="delete-pro{{$item->id}}"
                                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable"
                                                            role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <h5>Aufzeichnung löschen</h5>
                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?
                                                                    </p>
                                                                    <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a type="button"
                                                                        href="{{url('/group_set_delete').'/'.$item->id}}"
                                                                        class="btn btn-primary">Ja</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Delete Modal -->


                                                    <!-- Begin: Edit -->
                                                    <button type="button"
                                                        class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1"
                                                        data-toggle="modal" data-target="#editmodel{{$item->id}}">
                                                        <i class="feather icon-edit"></i>
                                                    </button>
                                                    <!-- Modal -->
                                                    <div class="modal fade text-left" id="editmodel{{$item->id}}"
                                                        tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-scrollable"
                                                            role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title" id="myModalLabel1">
                                                                        Bearbeiten</h4>
                                                                    <button type="button" class="close"
                                                                        data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form class="form-horizontal" novalidate
                                                                        method="post"
                                                                        action="{{action('App\Http\Controllers\ProductMasterSetController@update')}}">
                                                                        @csrf

                                                                        <fieldset>
                                                                            <div class="row">



                                                                            </div>
                                                                        </fieldset>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Einreichen</button>

                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- End Edit Modal -->

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
            <!-- Table head options end -->
            {{$data->links()}}
        </div>
    </div>
</div>
<!-- END: Content-->
@stop


@section('script')
<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<!-- END: Page Vendor JS-->
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>

<script>
$(document).ready(function() {
    $('#master').select2();

});
</script>











<script>
$(document).ready(function() {
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
$(document).ready(function() {
    // Attach click event to the button with class 'show_set'
    $('.show_set').click(function() {
        // Get the data-id of the clicked button
        var setId = $(this).attr('data-id');

        // Toggle visibility of the corresponding div with class 'sets' and matching data-id
        $('.sets[data-id="' + setId + '"]').toggle();
    });
});
</script>


@endsection