@extends('admin.layouts.app')
@section('title') Nicht berechtigt @endsection

@section('content')

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body">
                <!-- maintenance -->
                <section class="row flexbox-container">
                    <div class="col-xl-7 col-md-8 col-12 d-flex justify-content-center">
                        <div class="card auth-card bg-transparent shadow-none rounded-0 mb-0 w-100">
                            <div class="card-content">
                                <div class="card-body text-center">
                                    <img src="{{ asset('app-assets/images/pages/not-authorized.png ')}}" class="img-fluid align-self-center" alt="branding logo">
                                    <h1 class="font-large-2 my-2">Sie sind nicht berechtigt! </h1>
                                    <p class="p-2">
                                        Diese Seite ist durch den Administrator geschützt. Wenn Sie die Seite anzeigen möchten, wenden Sie sich bitte an den MIS-Administrator.</br>
                                        <p>
                                            <a href="">
                                                <i class="fa fa-envelope"></i> Kathrin Nuri: k.nuri@solar-aspekt.de 
                                            </a>
                                        </p>

                                        <p>
                                            <a href="">
                                                <i class="fa fa-envelope"></i> Yama Nuri: y.nuri@solar-aspekt.de 
                                            </a>
                                        </p>
                                    </p>
                                    <a class="btn btn-primary btn-lg mt-2" href="{{ url('/') }}">Zurück nach Hause</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- maintenance end -->

            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection