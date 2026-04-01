@extends('admin.layouts.app')
@section('title') Tempeature @stop
@section('content')

<!-- BEGIN: Content-->
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>

        <div class="content-body">
            <!-- Table Hover Animation start -->
            <div class="row" id="table-hover-animation">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tempeature</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">


                                <div class="col-9">
                                    <form action="{{action('App\Http\Controllers\TemperatureController@index')}}">
                                        <fieldset>
                                            <div class="input-group">
                                                <input type="text" name="search" class="form-control"
                                                    placeholder="Search Form" aria-describedby="button-addon2">
                                                <div class="input-group-append" id="button-addon2">
                                                    <button class="btn btn-primary" type="submit">Go</button>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>

                                <div class="col-md-3 float-right">
                                    <div class="card-body">
                                        <button type="button" class="btn btn-outline-primary block btn-lg"
                                            data-toggle="modal" data-target="#default">
                                            Add New
                                        </button>
                                        <!-- Modal -->
                                        <div class="modal fade text-left" id="default" tabindex="-1" role="dialog"
                                            aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form class="form-horizontal" novalidate method="post"
                                                            action="{{action('App\Http\Controllers\TemperatureController@store')}}"
                                                            class="custom-file-upload" enctype="multipart/form-data">
                                                            @csrf
                                                            <fieldset>
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                                Postleitzahl
                                                                            </label>

                                                                            <input type="text" class="form-control"  name="postcode" required>
                                                                                @if ($errors->has('postcode'))
                                                                                <p  style="color:red;"> {!!$errors->first('postcode')!!} </p>
                                                                                @endif
                                                                            <label for="Title">
                                                                                Stadt
                                                                            </label>
                                                                            <input type="text" class="form-control" name="city" required>
                                                                            @if ($errors->has('city'))
                                                                            <p style="color:red;"> {!!$errors->first('city')!!} </p>
                                                                            @endif
                                                                            <label for="Title">
                                                                                    Normaußentemperatur
                                                                                </label>
                                                                            <input type="text" class="form-control" name="outside_temp" required>
                                                                            @if ($errors->has('outside_temp'))
                                                                            <p style="color:red;"> {!!$errors->first('outside_temp')!!} </p>
                                                                            @endif
                                                                            <label for="Title">
                                                                                Datum
                                                                            </label>
                                                                            <input type="date" class="form-control" name="date" required>
                                                                            @if ($errors->has('date'))
                                                                            <p style="color:red;"> {!!$errors->first('date')!!} </p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </fieldset>
                                                            <div class="modal-footer">
                                                                <button type="submit"
                                                                    class="btn btn-primary">Einreichen</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Modal End -->


                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Datum</th>
                                                <th scope="col">Postleitzahl</th>
                                                <th scope="col">Stadt</th>
                                                <th scope="col">Normaußentemperatur</th>
                                                <th scope="col">Ackion</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data as $item)
                                            <tr>
                                                <th scope="row">{{$item->id}}</th>
                                                <td>{{ \Carbon\Carbon::parse($item->date)->isoFormat("MMM") }} - {{ \Carbon\Carbon::parse($item->date)->isoFormat('YY.MM.DD') }}</td>
                                                <td>{{ $item->postcode }}</td>
                                                <td>{{ $item->city }}</td>
                                                <td>{{ $item->outside_temp }}</td>
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
                                                                    <h5>Datensatz löschen</h5>
                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?
                                                                    </p>
                                                                    <p>Die Datensatznummer lautet: {{$item->id}} </p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <a type="button"
                                                                        href="{{url('/temp_destroy').'/'.$item->id}}"
                                                                        class="btn btn-primary">Ja</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                </div>
                                <!-- End Delete Modal -->


                                <!-- Begin: Edit -->
                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1"
                                    data-toggle="modal" data-target="#editmodel{{$item->id}}">
                                    <i class="feather icon-edit"></i>
                                </button>

                                <a type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" href="{{ url('temp_duplicate/'.$item->id) }}" >
                                        <i class="feather icon-copy"></i>
                                </a>
                                <!-- Modal -->
                                <div class="modal fade text-left" id="editmodel{{$item->id}}" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel1">Edit</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal" novalidate method="post"
                                                    action="{{action('App\Http\Controllers\TemperatureController@update')}}">
                                                    @csrf

                                                    <fieldset>
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="Title">
                                                                        Postleitzahl
                                                                    </label>
                                                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                                                    <input type="text" class="form-control" name="postcode" required value="{{ $item->postcode }}">
                                                                    @if ($errors->has('postcode'))
                                                                    <p style="color:red;"> {!!$errors->first('postcode')!!} </p>
                                                                    @endif
                                                                    <label for="Title">
                                                                        Stadt
                                                                    </label>
                                                                    <input type="text" class="form-control" name="city" required value="{{ $item->city }}">
                                                                    @if ($errors->has('city'))
                                                                    <p style="color:red;"> {!!$errors->first('city')!!} </p>
                                                                    @endif
                                                                    <label for="Title">
                                                                        Normaußentemperatur
                                                                    </label>
                                                                    <input type="text" class="form-control" name="outside_temp" required value="{{ $item->outside_temp }}">
                                                                    @if ($errors->has('outside_temp'))
                                                                    <p style="color:red;"> {!!$errors->first('outside_temp')!!} </p> @endif

                                                                    <label for="Title">
                                                                            Datum
                                                                        </label>
                                                                        <input type="date" class="form-control" name="date" required value="{{ $item->date }}">
                                                                        @if ($errors->has('date'))
                                                                        <p style="color:red;"> {!!$errors->first('date')!!} </p> @endif
                                                                </div>
                                                            </div>

                                                        </div>

                                                    </fieldset>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Einreichen</button>

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
@endsection