@extends('admin.layouts.app')
@section('title') Master Set @stop
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
                        <h2 class="content-header-title float-left mb-0">SETS-KONFIGURATION</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">

                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                </li> 
                                <li class="breadcrumb-item"><a href="{{ url('article_group_set') }}">Sets</a>
                                </li>
                                <li class="breadcrumb-item active">  {{  $title->article_group }}[{{ $title->sub_article }}] 
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
                        <div class="card-header">
                            <h4 class="card-title"> </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">

                                <div class="col-9">
                                    <form
                                        action="{{action('App\Http\Controllers\ProductMasterSetController@index', ['article'  => request()->article, 'sub_article'=> request()->sub_article])}}">
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
                                            Neue hinzufügen
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
                                                            action="{{action('App\Http\Controllers\ProductMasterSetController@store')}}"
                                                            class="custom-file-upload" enctype="multipart/form-data">
                                                            @csrf
                                                            <fieldset>
                                                                <div class="row">

                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                                Setname
                                                                            </label>

                                                                            <input type="text" class="form-control"
                                                                                name="setname" required>
                                                                            @if ($errors->has('setname'))<p
                                                                                style="color:red;">
                                                                                {!!$errors->first('setname')!!}</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                                Article Group
                                                                            </label>
                                                                            <select class="form-control"
                                                                                id="sub_article" name="sub_article">
                                                                                @foreach ($sub_article as $art)
                                                                                <option value="{{ $art->id }}">{{
                                                                                    $art->sub_article }}</option>
                                                                                @endforeach
                                                                            </select> 

                                                                            @foreach ($sub_article as $art)
                                                                            <input type="hidden"
                                                                                value="{{ $art->article_group_id }}"
                                                                                name="article_group">
                                                                            @endforeach
                                                                            @if ($errors->has('article_group'))<p
                                                                                style="color:red;">
                                                                                {!!$errors->first('article_group')!!}
                                                                            </p>@endif
                                                                        </div>
                                                                    </div>

                                                                     <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                               Phase
                                                                            </label> 

                                                                              <select class="form-control mt-1"
                                                                                id="phase_id" name="phase_id">
                                                                                @foreach ($phase as $ph)
                                                                                <option value="{{ $ph->id }}">{{
                                                                                    $ph->phase_name }}</option>
                                                                                @endforeach
                                                                            </select> 
                                                                            @if ($errors->has('phase_id'))<p
                                                                                style="color:red;">
                                                                                {!!$errors->first('phase_id')!!}
                                                                            </p>@endif
                                                                        </div>
                                                                    </div>
 
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="Title">
                                                                                Price
                                                                            </label>

                                                                            <input type="number" class="form-control"
                                                                                name="price" value="{{ old('price', 0)}}" required>
                                                                            @if ($errors->has('price'))<p
                                                                                style="color:red;">
                                                                                {!!$errors->first('price')!!}</p>@endif
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
                                                <th scope="col">#</th>
                                                <th scope="col">Phase</th>
                                                <th scope="col">Set Name</th>
                                                <th scope="col">Artikel_Gruppe</th>
                                                <th scope="col">Materialkosten EK</th>
                                                <th scope="col">Anteil Materialkosten %</th>
                                                <th scope="col">Montagekosten EK</th>
                                                <th scope="col">Anteil Lohnkosten %</th>
                                                 <th scope="col">Werkzeugkosten EK</th>
                                                <th scope="col">Anteil Werkzeugkosten %</th>
                                                <th scope="col">Total%</th>
                                                <th scope="col">Total€</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                           @foreach($data as $item)
                                                @php
                                                    $emp = (float) $item->employee_percent;
                                                    $mat = (float) $item->material_percent;
                                                    $ast = (float) ($item->asset_percent ?? 0);
                                                    $totalPct = $emp + $mat + $ast;
                                                @endphp
                                            <tr>
                                                <td>{{ $item->id }}</td>
                                                <td>{{ $item->phase_name }}</td>
                                                <td>
                                                    <a href="{{ url('sets/'.$item->id.'/'.$item->phase_id) }}">{{ $item->setname }}</a><br>
                                                    @if($item->product_parent_name)
                                                        <div class="badge badge-primary mr-1 mb-1">
                                                            <i class="feather icon-copy"></i>
                                                            <a href="{{ url('master_set/'.request()->article.'/'.request()->sub_article.'?search='.$item->product_parent_name) }}">
                                                                <span>Houpt-Set: {{ $item->product_parent_id }} {{ $item->product_parent_name }}</span>
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>{{ $item->article_group }}/{{ $item->sub_article }}</td>

                                                <td>{{ number_format($item->material_price, 2, ',', '.') }}€</td>
                                                <td>{{ $item->material_percent }} %</td>

                                                <td>{{ number_format($item->employee_price, 2, ',', '.') }}€</td>
                                                <td>{{ $item->employee_percent }} %</td>

                                                {{-- NEW: Werkzeug (assets) --}}
                                                <td>{{ number_format($item->asset_price ?? 0, 2, ',', '.') }}€</td>
                                                <td>{{ (float) ($item->asset_percent ?? 0) }} %</td>

                                                <td>{{ $totalPct }} %</td>
                                                <td>{{ number_format($item->price, 2, ',', '.') }}€</td>
                                                <td>

                                                    <!-- Refresh Calculation  -->

                                                    <a type="button" href="{{ url('refresh_master_set/'.$item->id) }}"
                                                        class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1">
                                                        <i class="feather icon-refresh-cw"></i>
                                                    </a>
                                                    <!-- Refresh Calculation  -->

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
                                                                        href="{{url('/master_set_delete').'/'.$item->id}}"
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
                                <!-- Modal -->
                                <div class="modal fade text-left" id="editmodel{{$item->id}}" tabindex="-1"
                                    role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myModalLabel1">Bearbeiten</h4>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form class="form-horizontal" novalidate method="post"
                                                    action="{{action('App\Http\Controllers\ProductMasterSetController@update')}}">
                                                    @csrf

                                                    <fieldset>
                                                        <div class="row">

                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="Title">
                                                                        Set Name
                                                                    </label>
                                                                    <input type="hidden" name="id"
                                                                        value="{{ $item->id }}">
                                                                    <input type="text" class="form-control"
                                                                        name="setname" value="{{ $item->setname }}"
                                                                        required>
                                                                    @if ($errors->has('setname'))<p style="color:red;">
                                                                        {!!$errors->first('setname')!!}</p>@endif
                                                                </div>
                                                            </div>

                                                           
                                                            <input type="hidden" value="{{ $item->article_group_id }}"  name="article_group">
                                                            <input type="hidden" value="{{ $item->sub_article_id }}"  name="sub_article">

                                                             <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="Title">
                                                                        Phase
                                                                    </label> 

                                                                        <select class="form-control mt-1"
                                                                        id="phase_id" name="phase_id">
                                                                        @foreach ($phase as $ph)
                                                                        <option value="{{ $ph->id }}" @if($ph->id == $item->phase_id) selected @endif>{{ $ph->phase_name }}</option>
                                                                        @endforeach
                                                                    </select> 
                                                                    @if ($errors->has('phase_id'))<p
                                                                        style="color:red;">
                                                                        {!!$errors->first('phase_id')!!}
                                                                    </p>@endif
                                                                </div>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="Title">
                                                                        Price
                                                                    </label>

                                                                    <input type="number" class="form-control" name="price"
                                                                        value="{{ old('price', $item->price )}}" required>
                                                                    @if ($errors->has('price'))<p style="color:red;">
                                                                        {!!$errors->first('price')!!}</p>@endif
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
                                        <a type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light primary" href="{{ url('clone_set/'.$item->id) }}"><i class="feather icon-copy primary"></i> Clone</a>                                                
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


<script>
    function refreshMasterSet() {
        const id = "{{ request()->article_group }}"; // Replace with actual value

        fetch(`/refresh-master-set/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('total_price').textContent = data.total + ' €';
                    document.getElementById('employee_price').textContent = data.employee_price + ' €';
                    document.getElementById('material_price').textContent = data.material_price + ' €';

                    document.getElementById('employee_percent').textContent = data.employee_percent + '%';
                    document.getElementById('material_percent').textContent = data.material_percent + '%';
                }
            })
            .catch(error => {
                console.error('Refresh failed:', error);
            });
    }

    setInterval(refreshMasterSet, 30000); // 30 seconds
    document.addEventListener("DOMContentLoaded", refreshMasterSet);
</script>

@endsection