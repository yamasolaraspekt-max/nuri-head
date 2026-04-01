@extends('admin.layouts.app')
@section('title') Arbeitsschritte Details @stop
@section('style')
<style>
    .card-header{
    background:transparent;
}
</style>
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
                                <h2 class="content-header-title float-left mb-0">ARBEITSSCHRITTE</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                          
            <div class="content-body">
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                            <div class="col-md-6 col-12">
                                <div class="row" id="table-hover-animation">
                                    <div class="col-12">
                                        <div class="cardS"  > 
                                            <div class="card-content">
                                                <div class="card-body">  
                                                    <div class="col-3 float-right"> 
                                                        <button type="button" class="btn btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#primary">
                                                        Neue hinzufügen
                                                        </button>
                                                        <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true" style="display: none;">
                                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-header bg-primary white">
                                                                        <h5 class="modal-title" id="myModalLabel160">NEUE PHASE</h5>
                                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                            <span aria-hidden="true">×</span>
                                                                        </button>
                                                                    </div>
                                                                        <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\TaskPhaseController@store')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                @csrf   
                                                                            <div class="modal-body">  
                                                                                    <div class="form-body">
                                                                                        <div class="row"> 
                                                                                            <div class="col-12">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-4">
                                                                                                        <span>Phase Name</span>
                                                                                                    </div>
                                                                                                    <div class="col-md-8">
                                                                                                        <input type="text" id="phase_name" class="form-control" value="{{old('phase_name')}}" name="phase_name" >
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div> 
                                                                                            <div class="col-12">
                                                                                                <div class="form-group row">
                                                                                                    <div class="col-md-4">
                                                                                                        <span>Produkt</span>
                                                                                                    </div>
                                                                                                    <div class="col-md-8">
                                                                                                           <fieldset class="form-group">
                                                                                                                <select class="form-control" name="product_id" id="article"  >
                                                                                                                    @foreach ($articles as $art)
                                                                                                                    <option value="{{ $art->id }}">{{ $art->article_group }}</option>
                                                                                                                    @endforeach 
                                                                                                                </select>
                                                                                                            </fieldset>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div> 
                                                                                        </div> 
                                                                                    </div>  
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-primary waves-effect waves-light" >Einreichen</button>
                                                                            </div>
                                                                        </form> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-8">
                                                        <form action="{{action('App\Http\Controllers\TaskPhaseController@index')}}">
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

                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">ID</th>
                                                                        <th scope="col">Produkt</th> 
                                                                        <th scope="col">Aktion</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($articles as $item)
                                                                    <tr>
                                                                        <th scope="row">{{ $item->id }}</th>
                                                                        <td>
                                                                            <div class="default-collapse collapse-bordered">
                                                                                <div class="cards collapse-header">
                                                                                    <div id="headingCollapse1" class="card-header collapsed" data-toggle="collapse" role="button" data-target="#collapse{{ $item->id }}" aria-expanded="false" aria-controls="collapse1">
                                                                                        <span class="lead collapse-title">
                                                                                            <strong>{{ $item->article_group }}</strong>
                                                                                        </span>
                                                                                    </div>
                                                                                    <div id="collapse{{ $item->id }}" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse" style="">
                                                                                        <div class="card-content">
                                                                                            <div class="card-body">
                                                                                                <div class="row">
                                                                                                  
                                                                                                        <div class="table-responsive">
                                                                                                            <table class="table mb-0"> 
                                                                                                                <tbody>  
                                                                                                                      @foreach ($taskPhases as $product)
                                                                                                                         @if ($product->product_id == $item->id)  
                                                                                                                    <tr>
                                                                                                                        <th scope="row">
                                                                                                                            <a href="{{ url('activities/'.$product->id.'/'.$product->product_id)}}" data-toggle="popover" data-content="Klicken Sie hier, um die Aufgaben von  {{ $product->phase_name }}" data-trigger="hover" data-original-title="PHASE">
                                                                                                                           {{ $product->id }} .{{ $product->phase_name }}
                                                                                                                        </a> 
                                                                                                                        </th>
                                                                                                                        <td>
                                                                                                                            <a class="showTask" data-id="{{ $product->id }}" data-product="{{ $product->product_id}}" data-toggle="popover" data-content="Klicken Sie hier, um die Aufgabendetails in der aktuellen Seitenleiste anzuzeigen" data-trigger="hover" data-original-title="Aufgabe anzeigen">
                                                                                                                                <i class="feather icon-info"></i> Details
                                                                                                                        </a> 
                                                                                                                        </td>

                                                                                                                        <td>  
                                                                                                                          

                                                                                                                            @if(DB::table('user_rolls')
                                                                                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                                                                                ->where('user_rolls.item_id', '=', 'Customer')
                                                                                                                                ->where('user_rolls.is_delete', '=', 'on')
                                                                                                                                ->first())
                                                                                                                                <a class="deleteTask primary"   data-toggle="modal" data-target="#edit{{$product->id}}" >
                                                                                                                                    <i class="feather icon-edit"></i>
                                                                                                                                </a> 
                                                                                                                            @endif 

                                                                                                                                <!-- Modal -->
                                                                                                                                <div class="modal fade text-left" id="edit{{$product->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                                                                        <div class="modal-content">
                                                                                                                                            <div class="modal-header">
                                                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                                                                </button>
                                                                                                                                            </div>
                                                                                                                                            <div class="modal-body">
                                                                                                                                                   <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\TaskPhaseController@update')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                                                                                            @csrf   
                                                                                                                                                        <div class="modal-body">  
                                                                                                                                                                <div class="form-body">
                                                                                                                                                                    <div class="row"> 
                                                                                                                                                                        <div class="col-12">
                                                                                                                                                                            <div class="form-group row">
                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                    <span>Phase Name</span>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="col-md-8">
                                                                                                                                                                                    <input type="hidden" name="id" value="{{$product->id}}">
                                                                                                                                                                                    <input type="text" id="phase_name" class="form-control" value="{{ $product->phase_name }}" name="phase_name" >
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div> 
                                                                                                                                                                        <div class="col-12">
                                                                                                                                                                            <div class="form-group row">
                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                    <span>Produkt</span>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="col-md-8">
                                                                                                                                                                                    <fieldset class="form-group">
                                                                                                                                                                                            <select class="form-control"  name="product_id" >
                                                                                                                                                                                                @foreach ($articles as $art)
                                                                                                                                                                                                <option value="{{ $art->id }}" @if($product->product_id == $art->id) selected @endif>{{ $art->article_group }}</option>
                                                                                                                                                                                                @endforeach 
                                                                                                                                                                                            </select>
                                                                                                                                                                                        </fieldset>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div> 
                                                                                                                                                                    </div> 
                                                                                                                                                                </div>  
                                                                                                                                                        </div>
                                                                                                                                                        <div class="modal-footer">
                                                                                                                                                            <button type="submit" class="btn btn-danger waves-effect waves-light" >Einreichen</button> 
                                                                                                                                                    </form> 
                                                                                                                                            </div> 
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <!-- End Delete Modal -->
                                                                                                                        </td>
                                                                                                                        <td>
                                                                                                                            <a class="showTask"  data-toggle="modal" data-target="#clone{{$product->id}}">
                                                                                                                                <i class="feather icon-copy"></i> Clone
                                                                                                                             </a> 

                                                                                                                             <!-- Modal -->
                                                                                                                                <div class="modal fade text-left" id="clone{{$product->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                                                                        <div class="modal-content">
                                                                                                                                            <div class="modal-header">
                                                                                                                                                Clone
                                                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                    <span aria-hidden="true">&times;</span> 
                                                                                                                                                </button>
                                                                                                                                            </div>
                                                                                                                                            <div class="modal-body">
                                                                                                                                                   <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\TaskPhaseController@clone')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                                                                                            @csrf   
                                                                                                                                                        <div class="modal-body">  
                                                                                                                                                                <div class="form-body">
                                                                                                                                                                    <div class="row"> 
                                                                                                                                                                        <div class="col-12">
                                                                                                                                                                            <div class="form-group row">
                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                    <span>Phase Name</span>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="col-md-8">
                                                                                                                                                                                    <input type="hidden" name="id" value="{{$product->id}}">

                                                                                                                                                                                    <input type="hidden" name="product_id" value="{{$product->product_id}}">
                                                                                                                                                                                    <input type="text" id="phase_name" class="form-control" value="{{ $product->phase_name }}" name="phase_name" >
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div> 
                                                                                                                                                                        <div class="col-12">
                                                                                                                                                                            <div class="form-group row">
                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                    <span>Produkt</span>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="col-md-8">
                                                                                                                                                                                    <fieldset class="form-group">
                                                                                                                                                                                            <select class="form-control"  disabled >
                                                                                                                                                                                                @foreach ($articles as $art)
                                                                                                                                                                                                <option value="{{ $art->id }}" @if($product->product_id == $art->id) selected @endif>{{ $art->article_group }}</option>
                                                                                                                                                                                                @endforeach 
                                                                                                                                                                                            </select>
                                                                                                                                                                                        </fieldset>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div> 

                                                                                                                                                                         <div class="col-12">
                                                                                                                                                                            <div class="form-group row">
                                                                                                                                                                                <div class="col-md-4">
                                                                                                                                                                                    <span>Kopieren nach</span>
                                                                                                                                                                                </div>
                                                                                                                                                                                <div class="col-md-8">
                                                                                                                                                                                    <fieldset class="form-group">
                                                                                                                                                                                            <select class="form-control" name="copy_to" id="copy_to"  >
                                                                                                                                                                                                @foreach ($articles as $art)
                                                                                                                                                                                                <option value="{{ $art->id }}" @if($product->product_id == $art->id) selected @endif>{{ $art->article_group }}</option>
                                                                                                                                                                                                @endforeach 
                                                                                                                                                                                            </select>
                                                                                                                                                                                        </fieldset>
                                                                                                                                                                                </div>
                                                                                                                                                                            </div>
                                                                                                                                                                        </div> 
                                                                                                                                                                    </div> 
                                                                                                                                                                </div>  
                                                                                                                                                        </div>
                                                                                                                                                        <div class="modal-footer">
                                                                                                                                                            <button type="submit" class="btn btn-danger waves-effect waves-light" >Einreichen</button> 
                                                                                                                                                    </form> 
                                                                                                                                            </div> 
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <!-- End Delete Modal -->
                                                                                                                        </td>
                                                                                                                        <td>  
                                                                                                                          

                                                                                                                            @if(DB::table('user_rolls')
                                                                                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                                                                                ->where('user_rolls.item_id', '=', 'Customer')
                                                                                                                                ->where('user_rolls.is_delete', '=', 'on')
                                                                                                                                ->first())
                                                                                                                                <a class="deleteTask danger"   data-toggle="modal" data-target="#delete-pro{{$product->id}}" >
                                                                                                                                    <i class="feather icon-trash"></i> 
                                                                                                                                </a> 
                                                                                                                            @endif 

                                                                                                                                <!-- Modal -->
                                                                                                                                <div class="modal fade text-left" id="delete-pro{{$product->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                                                                        <div class="modal-content">
                                                                                                                                            <div class="modal-header">
                                                                                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                                                                    <span aria-hidden="true">&times;</span>
                                                                                                                                                </button>
                                                                                                                                            </div>
                                                                                                                                            <div class="modal-body">
                                                                                                                                                <h5>Aufzeichnung löschen</h5>
                                                                                                                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                                                                                <p>Die Datensatznummer lautet:{{$product->id}} </p>
                                                                                                                                            </div>
                                                                                                                                            <div class="modal-footer">
                                                                                                                                            <a type="button" href="{{url('/task_phase_destroy').'/'.$product->id}}" class="btn btn-primary">Ja</a>
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <!-- End Delete Modal -->
                                                                                                                        </td>
                                                                                                                        
                                                                                                                    </tr>

                                                                                                                    @endif 
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
                                                                        </td> 

                                                                        <td>
                                                                       


                                                                        <!-- Begin: Edit -->
                                                                            @if(DB::table('user_rolls')
                                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                                ->where('user_rolls.item_id', '=', 'Customer')
                                                                                ->where('user_rolls.is_update', '=', 'on')
                                                                                ->first())
                                                                                    <a type="button" href="{{ url('phase_edit/'.$item->id)}}" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1">
                                                                                    <i class="feather icon-edit"></i>
                                                                                    </a> 
                                                                            @endif
                                                                                    <!-- Begin: Order -->
                                                                                @if(DB::table('user_rolls')
                                                                                ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                                ->where('user_rolls.item_id', '=', 'Customer')
                                                                                ->where('user_rolls.is_update', '=', 'on')
                                                                                ->first())
                                                                                <a type="button" href="{{ url('/task_phase_order/'.$item->id)}}" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1">
                                                                                    <i class="feather icon-move"></i>
                                                                                </a>
                                                                            
                                                                                @endif
 
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
                                <!-- Table head options end -->
                                {{$taskPhases->links()}}
                            </div>
                            <div class="col-md-6" id="activities">
                                <div class="row" id="table-hover-animation">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-content">
                                                <div class="card-body">
                                                    <div class="col-3 float-right">
                                                        <a class="addPhase" data-id="{{ $item->id }}" data-product="{{ $item->product_id }}">
                                                                Neu
                                                            </a>
                                                        <!-- Modal Structure -->
                                                            <div class="modal fade text-left" id="second" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true" style="display: none;">
                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-primary white">
                                                                            <h5 class="modal-title" id="myModalLabel160">NEUE PHASE</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">×</span>
                                                                            </button>
                                                                        </div>
                                                                        <form id="activityForm" class="form form-horizontal">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <div class="form-body">
                                                                                    <div class="row">
                                                                                        <div class="col-12">
                                                                                            <div class="form-group row">
                                                                                                <div class="col-md-4">
                                                                                                    <span>Initial</span>
                                                                                                </div>
                                                                                                <div class="col-md-8">
                                                                                                    <input type="hidden" name="phase_id" id="phase_id">
                                                                                                    <input type="hidden" name="product_id" id="product_id">
                                                                                                    <input type="text" id="initial" class="form-control" name="initial">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12">
                                                                                            <div class="form-group row">
                                                                                                <div class="col-md-4">
                                                                                                    <span>Title</span>
                                                                                                </div>
                                                                                                <div class="col-md-8">
                                                                                                    <input type="text" id="title" class="form-control" name="title">
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-12">
                                                                                            <div class="form-group row">
                                                                                                <div class="col-md-4">
                                                                                                    <span>Description</span>
                                                                                                </div>
                                                                                                <div class="col-md-8">
                                                                                                    <textarea name="description" id="description" class="form-control" col="5" row="10"></textarea>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="submit" class="btn btn-primary waves-effect waves-light">Einreichen</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                    </div>

                                                    <div class="col-8">
                                                        <form action="{{action('App\Http\Controllers\TaskPhaseController@index')}}">
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

                                                    <div class="col-md-12">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped mb-0">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">ID</th>
                                                                        <th scope="col">Phase</th>
                                                                        <th scope="col">Titel</th>
                                                                        <th scope="col">Beschreibung</th>
                                                                        <th scope="col">Aktion</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <!-- Task activities will be loaded here via AJAX -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Table head options end -->
                            </div>
                        </div>  
                    </div> 
                </section>  
            </div>
        </div>
    <!-- END: Content-->
@stop



@section('script')

 <script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.showTask').forEach(function(button) {
        button.addEventListener('click', function() {
            var taskId = this.getAttribute('data-id');
            var taskProduct = this.getAttribute('data-product');
            fetch('/activities_details/' + taskId + '/' + taskProduct)
                .then(response => response.json())
                .then(data => {
                    var content = '';
                    data.forEach(function(activity) {
                        content += `
                            <tr>
                                <th scope="row">${activity.id}</th>
                                <th scope="row">${activity.phase_name}</th>
                                <td>${activity.title}</td>
                                <td>${activity.description}</td>
                                <td>
                                    <!-- Action buttons (edit, delete, etc.) -->
                                </td>
                            </tr>`;
                    });
                    document.querySelector('#activities tbody').innerHTML = content;
                })
                .catch(error => console.error('Error:', error));
        });
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.addPhase').forEach(function(button) {
        button.addEventListener('click', function() {
            var taskId = this.getAttribute('data-id');
            var taskProduct = this.getAttribute('data-product');
            console.log('Button clicked:', { taskId, taskProduct });

            // Open the modal
            $('#second').modal('show');

            // Fetch activity details and fill the form
            fetch('/activities_details/' + taskId + '/' + taskProduct)
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched data:', data); // Debug log

                    // Ensure the data has the expected structure
                    if (data.phase_id && data.product_id) {
                        document.getElementById('phase_id').value = data.phase_id;
                        document.getElementById('product_id').value = data.product_id;
                        document.getElementById('initial').value = data.initial;
                        document.getElementById('title').value = data.title;
                        document.getElementById('description').value = data.description;
                    } else {
                        console.error('Unexpected data structure:', data);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    document.getElementById('activityForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting normally

        const formData = new FormData(this);
        console.log('Form data before submission:', Object.fromEntries(formData.entries())); // Debug log

        fetch('/activities_details', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response data
            console.log('Success:', data);
            // Optionally, you can close the modal and reset the form
            $('#second').modal('hide');
            this.reset();
            // Optionally, you can reload the activities
            document.querySelectorAll('.showTask[data-id="' + data.phase_id + '"]').forEach(button => button.click());
        })
        .catch(error => console.error('Error:', error));
    });
});

</script>



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