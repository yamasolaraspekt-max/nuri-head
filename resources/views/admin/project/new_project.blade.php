@extends('admin.layouts.app')

@section('title') NEUE PROJEKT @endsection
@section('style')
<!-- Include stylesheet -->

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
                            <h2 class="content-header-title float-left mb-0">PROJEKT</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active"><a href="#">Neu</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            <div class="content-body"> 
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="card-title mb-1 mt-1 white"><i class="feather icon-plus"></i> Neues Projekt erstellen</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('projects.store') }}">
                            @csrf
                            <div class="row">
                                <!-- Customer Dropdown -->
                                <div class="col-md-6 mb-2">
                                    <label>Kunde</label>
                                    <select name="customer_id" id="customer_id" class="form-control select2" required>
                                        <option value="">-- Wähle Kunde --</option>
                                        @foreach($customers as $cust)
                                            <option value="{{ $cust->id }}">{{ $cust->name }} {{ $cust->lastname }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Product Dropdown -->
                                <div class="col-md-6 mb-2">
                                    <label>Produkt</label>
                                    <select name="product_list_id" id="product_list_id" class="form-control select2" required>
                                        <option value="">-- Wähle Produkt --</option>
                                        <!-- Filled by JS -->
                                    </select>
                                </div>

                                <div class="col-12 mt-3">
                                    <button type="submit" class="btn btn-success btn-block">Projekt Erstellen</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div> 
            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')
 
<script src="{{ asset('js/select2.min.js') }}"></script>


<script>
        $(document).ready(function() {
            $('#product').select2();
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


<script>
    $(document).ready(function() {
    $('.select2').select2();

    $('#customer_id').on('change', function () {
        let customerId = $(this).val();
        $('#product_list_id').html('<option>-- Lade Produkte --</option>');

        $.get(`/get-product-lists/${customerId}`, function (data) {
            $('#product_list_id').html('<option value="">-- Wähle Produkt --</option>');
            data.forEach(d => {
                $('#product_list_id').append(`
                    <option value="${d.id}" 
                        data-product="${d.product_id}"
                        data-alternative="${d.alternative_id}"
                        data-department="${d.department_id ?? ''}"
                        data-service="${d.service_id ?? ''}"
                        data-employee="${d.employee_id ?? ''}"
                        data-service-str="${d.service}">
                        ${d.article_group}
                    </option>
                `);
            });
        });
    });

});

</script>
@endsection