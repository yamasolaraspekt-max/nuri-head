@extends('admin.layouts.app')
@section('title') Verfügbarkeit @stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
.modal-open-scroll-lock {
    overflow: hidden;
}

.pac-container {
    z-index: 20000 !important;
    position: fixed !important; /* ✅ better than absolute inside modal context */
}


.new_task {
    display: none;
    /* Hidden by default */
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    /* Center the div */
    background: #e7e6e6;
    z-index: 10;
    width: 30% !important;
    /* Default width */
    max-width: 3-% !important;
    max-height: 85vh;
    /* Ensures it doesn't go beyond 80% of viewport height */
    overflow-y: auto;
    /* Enables scrolling inside */

}



/* Ensure modal content area scrolls separately */
.new_task .modal-body {
    max-height: 85vh;
    /* Limit body height */
    overflow-y: auto;
    /* Enable scrolling */
    padding: 15px;
}

/* Sticky Header & Close Button */
.new_task .modal-header {
    position: sticky;
    top: 0;
    background: white;
    z-index: 10;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

.new_task .modal-footer {
    position: sticky;
    bottom: 0;
    background: #e7e6e6 !important;
    z-index: 10;
    padding: 10px;
    border-top: 1px solid #ddd;
}

/* Responsive styles for mobile */
@media (max-width: 768px) {
    .new_task {
        width: 90% !important;
        /* 90% width on mobile */
        max-width: 90% !important;
    }
}


.new_task_close {
    position: absolute;
    z-index: 4;
    left: -135px;
    top: 16%;
}



</style>
@endsection
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row"> 
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-left mb-0">Verfügbarkeit</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Mitarbeiter</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body"> 
            <div class="container">
                <h2 class="mb-4">Upload DATANORM File</h2>

                <form action="{{ route('datanorm.parse') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="datanorm_file" class="form-control" required  >
                    </div>
                    <button class="btn btn-primary">Upload and Parse</button>
                </form>

                @if(isset($parsedData))
                    <h4 class="mt-5">Parsed JSON Output:</h4>
                    <pre class="bg-light p-3 rounded">{{ json_encode($parsedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_key') }}&libraries=places"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
 

 

@endsection
