@extends('admin.layouts.app')

@section('title') Ratenzahlungen @endsection
@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
    body {
      margin: 0;
    }

    .sb-title {
      position: relative;
      top: -12px;
      font-family: Roboto, sans-serif;
      font-weight: 500;
    }

    .sb-title-icon {
      position: relative;
      top: -5px;
    }

    .card-container {
      display: flex;
      height: 500px;
      width: 600px;
    }

    .panel {
      background: white;
      width: 300px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-around;
    }

    .half-input-container {
      display: flex;
      justify-content: space-between;
    }

    .half-input {
      max-width: 120px;
    }

    .map {
      width: 300px;
    }

    h2 {
      margin: 0;
      font-family: Roboto, sans-serif;
    }

    input {
      height: 30px;
    }

    input {
      border: 0;
      border-bottom: 1px solid black;
      font-size: 14px;
      font-family: Roboto, sans-serif;
      font-style: normal;
      font-weight: normal;
    }

    input:focus::placeholder {
      color: white;
    } 
    .kauf{
        display: none;
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
                            <h2 class="content-header-title float-left mb-0">Ratenzahlungen</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('customer_details') }}">Raten</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="#">Neu</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-12 col-12">
                
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
                                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\AssetInstallmentController@update')}}" class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <fieldset> 
                                                <div class="row">  
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                              Artikel
                                                            </label>
                                                            <input type="hidden" name="id" value="{{ request()->id }}">
                                                             <input type="hidden" class="form-control"  name="asset_id" value="{{ $asset->id }}" required>
                                                             <input type="text" class="form-control"  name="asset_name" value="{{ $asset->item }}" disabled  required> 
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Ratenzahlungs-ID
                                                            </label> 
                                                             <input type="text" class="form-control"  name="installment_id" value="{{ $data->installment_id }}" required>
                                                             @if ($errors->has('installment_id'))<p style="color:red;">{!!$errors->first('installment_id')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                     <div class="form-group">
                                                         <label for="Title">
                                                            Gekauft bei
                                                         </label>
                                                         
                                                          <input type="text" class="form-control"  name="purchased_from" value="{{ $data->purchased_from }}" required>
                                                          @if ($errors->has('purchased_from'))<p style="color:red;">{!!$errors->first('purchased_from')!!}</p>@endif
                                                     </div>
                                                 </div>

                                                 <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Preis pro Monat
                                                        </label>
                                                        
                                                         <input type="text" class="form-control currency-input"   name="price_per_month" id="price_per_month" value="{{ $data->price_per_month }}" required>
                                                         @if ($errors->has('price_per_month'))<p style="color:red;">{!!$errors->first('price_per_month')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Strafe
                                                        </label>
                                                        
                                                         <input type="text" class="form-control currency-input" id="fines" name="fines" value="{{ $data->fines }}" required>
                                                         @if ($errors->has('fines'))<p style="color:red;">{!!$errors->first('fines')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Ratenzahlungsdauer - Monate(n)
                                                        </label>
                                                        
                                                         <input type="number" class="form-control"  name="installment_duration" id="installment_duration" value="{{ $data->installment_duration }}" required>
                                                         @if ($errors->has('installment_duration'))<p style="color:red;">{!!$errors->first('installment_duration')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                           Total    
                                                        </label>
                                                        
                                                         <input type="text" class="form-control currency-input"  name="total" id="total" value="{{ $data->total }}" required>
                                                         @if ($errors->has('total'))<p style="color:red;">{!!$errors->first('total')!!}</p>@endif
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Fälligkeitsdatum
                                                        </label>
                                                        
                                                         <input type="date" class="form-control"  name="due_date" value="{{ $data->due_date }}" required>
                                                         @if ($errors->has('due_date'))<p style="color:red;">{!!$errors->first('due_date')!!}</p>@endif
                                                    </div>
                                                </div> 
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Bezahlt von
                                                            </label>
                                                            
                                                            <fieldset class="form-group">
                                                                <select class="select2-customize-result form-control required" name="paid_by"  id="paid_by"  style="width:100%">
                                                                 @foreach ($employees as $emp)
                                                                 <option value="{{ $emp->id }}" @if($emp->id == $data->paid_by) selected @endif>{{ $emp->name }} {{ $emp->lastname }}</option>
                                                                     
                                                                 @endforeach
                                                                 
                                                                </select>
                                                            </fieldset>
                                                             @if ($errors->has('paid_by'))<p style="color:red;">{!!$errors->first('paid_by')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Bezahlverfahren
                                                            </label>
                                                            
                                                            <fieldset class="form-group">
                                                                <select class="select2-customize-result form-control required" name="payment_method"  id="payment_method"  style="width:100%">
                                                                 <option value="{{ $data->payment_method }}">{{ $data->payment_method }}</option>
                                                                 <option value="Cash">Bargeld</option>
                                                                 <option value="Credit Card">Kreditkarte</option>
                                                                 <option value="Debit Card">EC-Karte / Bankkarte / Debitkarte</option>
                                                                 <option value="Bank Transfer">Überweisung</option>
                                                                 <option value="PayPal">PayPal</option>
                                                                 <option value="Apple Pay">Apple Pay</option>
                                                                 <option value="Google Pay">Google Pay</option>
                                                                 <option value="Cryptocurrency">Kryptowährung</option>
                                                                 <option value="Installment">Ratenzahlung</option>
                                                                 <option value="Leasing">Leasing</option>
                                                                 <option value="Check">Scheck</option>
                                                                 <option value="Money Order">Geldanweisung</option>
                                                                 <option value="Direct Deposit">Direkte Einzahlung</option>
                                                                 <option value="Mobile Payment">Mobile Zahlung</option>
                                                                 <option value="Contactless Payment">Kontaktlose Zahlung</option>
                                                                 
                                                             
                                                                </select>
                                                            </fieldset>
                                                             @if ($errors->has('payment_method'))<p style="color:red;">{!!$errors->first('payment_method')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Versicherer
                                                            </label>
                                                            
                                                            <div class="custom-control custom-switch custom-switch-success mr-2 mb-1"> 
                                                                <input type="checkbox" class="custom-control-input" id="customSwitch4"  @if($data->insurance_provider) checked @endif>
                                                                <label class="custom-control-label" for="customSwitch4"></label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5 kauf">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Versicherer
                                                            </label>
                                                            
                                                             <input type="text" class="form-control"  name="insurance_provider"   value="{{ old('insurance_provider') }}" required>
                                                             @if ($errors->has('insurance_provider'))<p style="color:red;">{!!$errors->first('insurance_provider')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 kauf">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Versicherungsbetrag
                                                            </label>
                                                            
                                                             <input type="text" class="form-control currency-input"  name="insurance_amount"    id="insurance_amount" value="{{ $data->insurance_amount }}" required>
                                                             @if ($errors->has('insurance_amount'))<p style="color:red;">{!!$errors->first('insurance_amount')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                     <div class="col-md-6 kauf">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Eingezahlt (Monate/Jahr)
                                                            </label>
                                                            
                                                            <fieldset class="form-group">
                                                                <select class="select2-customize-result form-control required" name="insurance_payment_month"  id="insurance_payment_month"  style="width:100%">
                                                                 <option value="{{ $data->insurance_payment_month }}" selected>{{ $data->insurance_payment_month }}</option>
                                                                 <option value="3 Monaten">3 Monaten</option>
                                                                 <option value="6 Monaten">6 Monaten</option>
                                                                 <option value="Jahr">Jahr</option>  
                                                                </select>
                                                            </fieldset>
                                                             @if ($errors->has('insurance_payment_month'))<p style="color:red;">{!!$errors->first('insurance_payment_month')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 kauf">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Versicherungsablaufdatum
                                                            </label>
                                                            
                                                             <input type="date" class="form-control"  name="insurance_expiry_date"   value="{{ $data->insurance_expiry_date }}" required>
                                                             @if ($errors->has('insurance_expiry_date'))<p style="color:red;">{!!$errors->first('insurance_expiry_date')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Beschreibung
                                                            </label>
                                                            
                                                             <textarea  class="form-control"  name="notes"  required>{{ $data->notes }}</textarea>
                                                             @if ($errors->has('notes'))<p style="color:red;">{!!$errors->first('notes')!!}</p>@endif
                                                        </div>
                                                    </div> 
                                                </div>
                                             </fieldset>
                                        </div>
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
                </section>
                <!-- // Basic Horizontal form layout section end -->

               

            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

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

<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script> 
    
<script>
    function onScanSuccess(decodedText, decodedResult) {
  // handle the scanned code as you like, for example:
  console.log(`Code matched = ${decodedText}`, decodedResult);
  var qrcode = document.getElementById('serial_no');
  qrcode.value = decodedResult.decodedText;
}

function onScanFailure(error) {
  // handle scan failure, usually better to ignore and keep scanning.
  // for example:
  //console.warn(`Code scan error = ${error}`);
}

let html5QrcodeScanner = new Html5QrcodeScanner(
  "reader",
  { fps: 10, qrbox: {width: 250, height: 250} },
  /* verbose= */ false);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
</script>

<script>

$(document).ready(function(){
    $("#hide_camera").click(function(){
        $("#camera").toggle(); // Use jQuery for both selection and toggling
    });
});

</script>

<script>
$(document).ready(function() {
    $('#customSwitch4').on('change', function() {
        if ($(this).is(':checked')) {
            $('.kauf').show(); // Show the elements with class 'kauf'
        } else {
            $('.kauf').hide(); // Hide the elements with class 'kauf'
        }
    });
});
</script> 




<script>
    $(document).ready(function() {
        $('#branch').select2();
        $('#parent').select2();
        $('#paid_by').select2(); 
    });

    
</script>

{{-- Formating the input to show the Euro --}}
{{-- <script>
   $(document).ready(function() {
    const input = $('.currency-input');

    input.on('focus', function() {
        // Format the number when the input is focused
        const inputValue = $(this).val();
        if (inputValue !== '') {
            $(this).val(formatNumber(parseFloat(inputValue)));
        }
    });

    input.on('change', function() {
        // Format the number when the input value changes
        const inputValue = $(this).val();
        if (inputValue !== '') {
            $(this).val(formatNumber(parseFloat(inputValue)));
        }
    });

    function formatNumber(number) {
        // Format the number to have comma as the decimal separator and period as the thousands separator
        const formattedNumber = number.toLocaleString('de-DE', {
            style: 'decimal',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        return formattedNumber + ' €'; // Add the Euro symbol
    }
});

    </script> --}}
<script>
  $(document).ready(function() {
    $('#price_per_month, #installment_duration, #insurance_amount, #fine, #insurance_payment_month').on('input', function() {
        // Get the values from inputs
        const pricePerMonth = parseFloat($('#price_per_month').val().replace(/[^\d,.]/g, '').replace(',', '.'));
        const duration = parseFloat($('#installment_duration').val());
        const insurance = $('#insurance_amount').val() ? parseFloat($('#insurance_amount').val().replace(/[^\d,.]/g, '').replace(',', '.')) : 0;
        const fines = $('#fine').val() ? parseFloat($('#fine').val().replace(/[^\d,.]/g, '').replace(',', '.')) : 0;
        const payment = $('#insurance_payment_month').val(); // Get the payment frequency

        // Initialize total variable
        let total = 0;

        // Check if any of the inputs are not valid numbers
        if (isNaN(pricePerMonth) || isNaN(duration) || isNaN(insurance) || isNaN(fines)) {
            $('#total').val('');
            return;
        }

        // Calculate the total based on the presence and value of the payment frequency
        if (!payment) { // If payment frequency is not selected
            total = (pricePerMonth * duration) - fines; // Skip adding insurance
        } else {
            // Determine the multiplier for insurance based on the payment frequency
            let insuranceMultiplier = 0;
            switch(payment) {
                case "3 Monaten":
                    insuranceMultiplier = 3;
                    break;
                case "6 Monaten":
                    insuranceMultiplier = 6;
                    break;
                case "Jahr":
                    insuranceMultiplier = 12;
                    break;
                default:
                    insuranceMultiplier = 0; // No insurance to be added
                    break;
            }
            total = (pricePerMonth * duration) + (insurance * insuranceMultiplier) - fines;
        }

        // Display the total in the "total" input field, formatted to two decimal places
        $('#total').val(total.toFixed(2));
    });
});


    </script>
    
    
    




@endsection