@extends('admin.layouts.app')
@section('title')
Tagesbericht
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">
<style>
    #print-area {
      text-align: center;
      margin-top: 40px;
    }

    @media print {
      body * {
        visibility: hidden;
      }
      #print-area, #print-area * {
        visibility: visible;
      }
      #print-area {
        position: absolute;
        top: 40px;
        left: 0;
        right: 0;
      }
    }

    #year_table {
        justify-content: center;
    }

    #qrcode canvas {
        height:500px !important;
        width:500px !important;
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
                            <h2 class="content-header-title float-left mb-0">TAGESBERICHT</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">QRCODE
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('work.place.index') }}">Arbeitsplatz</a> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body"> 
         
                <div class="row">
                   <div class="col-4"> 
                        <select id="type" onchange="generateQRCode()" class="form-control">
                            <option value="Car">Car</option>
                            <option value="Office">Office</option>
                            <option value="Customer">Customer</option>
                        </select>
                   </div>
                    <button class="btn btn-primary mr-1 mb-1 waves-effect waves-light" onclick="window.print()"  >🖨️ Print QR Code</button>
                </div>
                <div class="row" id="year_table"> 
                    <div id="print-area">
                        <div id="qrcode"></div>
                    </div> 

                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

 
  <script>
    function generateQRCode() {
      const type = document.getElementById('type').value;
      const qrUrl = `{{ url('/employee/qr/code/reader') }}/${type}`;
      const qrcodeDiv = document.getElementById('qrcode');
      qrcodeDiv.innerHTML = "";

      QRCode.toCanvas(document.createElement('canvas'), qrUrl, function (err, canvas) {
        if (err) {
          console.error(err);
          return;
        }
        qrcodeDiv.appendChild(canvas);
      });
    }

    // Generate initial QR on page load
    window.onload = generateQRCode;
  </script>
@endsection