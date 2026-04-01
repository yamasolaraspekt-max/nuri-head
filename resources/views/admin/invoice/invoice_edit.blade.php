@extends('admin.layouts.app')

@section('title') Rechnung bearbaiten @endsection
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
                            <h2 class="content-header-title float-left mb-0"> Rechnung</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html">Bearbaiten</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts" onmouseover="purchase()">
                    <div class="row match-height">
                        <div class="col-md-6 col-12">
                
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\InvoiceController@update')}}"   class="custom-file-upload" enctype="multipart/form-data">
                                        @csrf    
                                        <div class="form-body">
                                                <div class="row">

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Rechnung Nummer</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="hidden" name="id" value={{ request()->id }}>
                                                                <input type="text" id="first-name" class="form-control" value="{{ $data->invoice_no }}" name="invoice_no"  required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Rechnung Datum</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="date" id="first-name" class="form-control" value="{{ $data->invoice_date }}" name="invoice_date" required>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Gekauft für</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="purchase_for" onchange="purchase()" id="purchase_for"  required>
                                                                        <option value="Lagerhaus" @if($data->purchase_for == "Logerhaus") selected @endif>Lagerhaus</option>
                                                                        <option value="Kunden"  @if($data->purchase_for == "Kunden") selected @endif > Kunden</option>
                                                                        <option value="Personal"@if($data->purchase_for == "Personal") selected @endif> Personal</option>
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12" id="customer" style="display:none" >
                                                        <div class="form-group row">
                                                            <div class="col-md-4" >
                                                                <span>Kunden</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="customer" id="custom" required style="width:100%">
                                                                        <option selected value="none">Wählen Sie Kunde aus</option>
                                                                        @foreach ($customer as $cus)
                                                                        
                                                                        <option value="{{ $cus->id }}" @if($data->customer_id==$cus->id) selected @endif>{{ $cus->name }} {{ $cus->lastname }} - {{ $cus->city }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12" id="employee" style="display:none" >
                                                        <div class="form-group row">
                                                            <div class="col-md-4" >
                                                                <span>Mitarbeitername </span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="employee" id="empl" required style="width:100%">
                                                                        <option selected value="none">Wählen Sie Mitarbeiter aus</option>
                                                                        @foreach ($employee as $empl)
                                                                        <option value="{{ $empl->id }}"  @if($data->employee_id==$empl->id) selected @endif>{{ $empl->name }} {{ $empl->lastname }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Unternehmen</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <fieldset class="form-group">
                                                                    <select class="select2-customize-result form-control" name="company" id="company"  required>
                                                                        @foreach ($company as $com)
                                                                        <option value="{{ $com->id }}" @if($data->company==$com->id)? selected : "" @endif>{{ $com->name }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </fieldset>
                                                            </div>
                                                        </div>
                                                    </div>

                                                  
                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Erworben von</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <fieldset class="form-group">
                                                                <select class="select2-customize-result form-control" name="purchased_by" id="purchased_by"  required>
                                                                    @foreach ($employee as $purchase)
                                                                    <option value="{{ $purchase->id }}" @if($data->purchased_by==$purchase->id)? selected : "" @endif>{{ $purchase->name }} {{ $purchase->lastname }}</option>
                                                                    @endforeach
                                                                    
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>


                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Beobachtet von</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                            <fieldset class="form-group">
                                                                <select class="select2-customize-result form-control"  name="edited_by" id="edited_by"  required >
                                                                    @foreach ($employee as $edit)
                                                                    <option value="{{ $edit->id }}" @if($data->edited_by==$edit->id)? selected : "" @endif>{{ $edit->name }} {{ $edit->lastname }}</option>
                                                                    @endforeach
                                                                    
                                                                </select>
                                                             </fieldset>
                                                        </div>
                                                    </div>
                                                </div>


                                                    <div class="col-12">
                                                        <div class="form-group row">
                                                            <div class="col-md-4">
                                                                <span>Rechnung <code>PDF</code></span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <input type="file" id="first-name" class="form-control" value="{{old('pdf')}}" name="pdf" >
                                                            </div>
                                                        </div>
                                                    </div>

                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        </div>
                            <div class="col-md-6 col-12">
                            
                               
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic Horizontal form layout section end -->

               

            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')
 
<script src="{{ asset('js/select2.min.js') }}"></script>


<script>
        $(document).ready(function() {
            $('#company').select2();
        });

        $(document).ready(function() {
            $('#purchased_by').select2();
        });

        $(document).ready(function() {
            $('#edited_by').select2();
        });

        $(document).ready(function() {
            $('#empl').select2();
        });

        $(document).ready(function() {
            $('#custom').select2();
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

       @if(Session::has('not_save'))
       toastr.danger("{{ session('not_save') }}");
       @endif

      
            
    @if(Session::has('delete_msg'))
    toastr.error("{{ session('delete_msg') }}");
    @endif
    });
    

</script>

<script>
  function purchase() {
  var purchase = document.getElementById("purchase_for");
  var customer = document.getElementById("customer");
  var emp = document.getElementById("employee");

  if (purchase.value === "Kunden") {
    customer.style.display = "block";
    customer.required = true; // Set 'required' property directly on DOM element
    emp.style.display = "none";
    emp.required = false; // Set 'required' property directly on DOM element
  } else if (purchase.value === "Personal") {
    customer.style.display = "none";
    customer.required = false; // Set 'required' property directly on DOM element
    emp.style.display = "block";
    emp.required = true; // Set 'required' property directly on DOM element
  } else {
    customer.style.display = "none";
    customer.required = false; // Set 'required' property directly on DOM element
    emp.style.display = "none";
    emp.required = false; // Set 'required' property directly on DOM element
  }
}
    </script>
@endsection