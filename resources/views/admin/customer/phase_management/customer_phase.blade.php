@extends('admin.layouts.app')
@section('title') Kundenphasenmanagement @stop
@section('style')
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
                        <h2 class="content-header-title float-left mb-0">Kundenphasenmanagement</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a>
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
                             <div class="col-md-2 col-12 mb-1 float-right">
                                <a class="btn btn-primary waves-effect waves-light" type="button" data-toggle="modal" data-target="#new"><i class="feather icon-plus"></i></a> 
                            </div>
                            <div class="modal fade text-left" id="new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary white">
                                                <h5 class="modal-title" id="myModalLabel160">Kunden-phase</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form method="post" action="{{ route('customer.phase.managment.create')}}">
                                                <input type="hidden" name="product" value="">
                                                <input type="hidden" name="customer_id" value="">
                                                <input type="hidden" name="alternative_id" value="">
                                                <input type="hidden" name="service" value="">
                                                @csrf
                                                <div class="modal-body">
                                                <select name="customer" id="customer_select" class="employee_select form-control">
                                                    <option></option>
                                                        @foreach ($customers as $cust)
                                                            <option value="{{$cust->id}}" data-name="{{$cust->name}} {{$cust->lastname}}" data-service="{{$cust->service}}  " data-customer="{{$cust->id}}" data-alternative="{{$cust->alt_id}}" data-product-name="{{$cust->article_group}}" data-object="{{$cust->object_name}}" data-product="{{ $cust->product }}" data-address="{{$cust->street}} {{$cust->postcode}}, {{$cust->city}}">
                                                                {{$cust->name}} {{$cust->lastname}} - {{$cust->object_name}}  
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light"  >Phase verwalten</button>
                                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal">Abbrechen</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                    <div class="col-12">
                        <div class="card"> 
                            <div class="card-content">
                                    <div class="card-body"> 
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
                                                            <div class="table-responsive"> 
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>KUNDENNAME</th>
                                                                            <th>ADRESSE</th>
                                                                            <th>PRODUKT</th>
                                                                            <th>PHASE/STUFEN</th>
                                                                            <th>AKTION</th>
                                                                        </tr>
                                                                       
                                                                    </thead>
                                                                <tbody>
                                                                        @foreach ($data as $item) 
                                                                            <tr>
                                                                                <td>{{ $item->id }}</td>
                                                                                <td>{{ $item->name }} {{ $item->lastname }}</td>   
                                                                                <td>{{ $item->street }}, {{ $item->postcode }}, {{ $item->city }}</td>   
                                                                                <td>{{ $item->article_group }}</td> 
                                                                                <td>
                                                                                    @php
                                                                                        // Convert the concatenated phase IDs into an array
                                                                                        $phaseIds = explode(',', $item->phases);
                                                                                    @endphp
                                                                                    @foreach ($task_phase as $phase)
                                                                                        @if(in_array($phase->id, $phaseIds) && $phase->product_id == $item->product)
                                                                                            <div class="chip mr-1">
                                                                                                <div class="chip-body">
                                                                                                    <div class="avatar">
                                                                                                        <i class="feather icon-user"></i>
                                                                                                    </div>
                                                                                                    <span class="chip-text">{{ $phase->phase_name }}</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </td>  
                                                                                <td>
                                                                                    <form method="post" action="{{ route('customer.phase.manage.edit')}}">
                                                                                        @csrf
                                                                                            <input type="hidden" name="customer" value="{{$item->id}}">
                                                                                            <input type="hidden" name="alternative_id" value="{{ $item->alt_id}}">
                                                                                            <input type="hidden" name="product" value="{{$item->product}}">
                                                                                            <input type="hidden" name="service" value="{{$item->service}}">

                                                                                        <button type="submit"  class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-edit"></i></a> 
                                                                                    </form>

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

       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>
 
<script>
   $(document).ready(function () {
    $('#customer_select').select2({
        templateResult: formatCustomer,
        templateSelection: formatCustomerSelection,
        width: '100%' // Ensure it uses full width
    });

    // Update hidden inputs on selection change
    $('#customer_select').on('change', function () {
        let selectedOption = $(this).find(':selected');

        // Get data attributes from the selected option
        let product = selectedOption.data('product');
        let customer = selectedOption.data('customer'); 
        let alternative = selectedOption.data('alternative');
        let service = selectedOption.data('service');

        // Update hidden input fields
        $('input[name="product"]').val(product);
        $('input[name="customer_id"]').val(customer);
        $('input[name="alternative_id"]').val(alternative);
        $('input[name="service"]').val(service);
    });

    function formatCustomer(data) {
        if (!data.id) {
            return data.text;
        }

        let name = $(data.element).data('name');
        let object = $(data.element).data('object');
        let address = $(data.element).data('address');
        let productName = $(data.element).data('product-name');

        return $(
            `<div>
                <p class="bold" style="margin: 0; font-weight: bold;">${name} - ${object} - ${productName}</p>
                <small style="margin: 0; color: gray;">${address}</small>
            </div>`
        );
    }

    function formatCustomerSelection(data) {
        if (!data.id) {
            return data.text;
        }

        let name = $(data.element).data('name');
        let object = $(data.element).data('object');
        let productName = $(data.element).data('product-name');
        let service = $(data.element).data('service');

        return `${name} - ${object} - ${productName} - ${service}`;
    }
});


</script>
@endsection