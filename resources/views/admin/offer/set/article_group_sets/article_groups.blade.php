@extends('admin.layouts.app')
@section('title') Group Set @stop
@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/pickers/pickadate/pickadate.css')}}">
<link rel="stylesheet" type="text/css"
    href="{{ asset('app-assets/css/plugins/forms/validation/form-validation.css') }}">
    <style>
    .spinner {
    animation: spin 1s linear infinite;
    display: inline-block;
    }
    @keyframes spin {
    100% {
        transform: rotate(360deg);
    }
    }
    </style>

<style>
    #cards:hover {
        background: #8fc73e;
        cursor: pointer;
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
                        <h2 class="content-header-title float-left mb-0">SETS-KONFIGURATION</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                </li> 
                                <li class="breadcrumb-item active">Sets
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                <div class="form-group breadcrum-right">
                    <div class="dropdown">
                        <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i
                                class="feather icon-settings"></i></button>
                        <div class="dropdown-menu dropdown-menu-right"><a class="dropdown-item" href="#">Chat</a><a
                                class="dropdown-item" href="#">Email</a><a class="dropdown-item" href="#">Calendar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <!-- Basic example and Profile cards section start -->
            <section id="basic-examples">
                <div class="row match-height">
                    <div class="col-xl-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-content">

                            
                                <div class="card-body">
                                    <div class="row" id="table-hover-animation">
                                        <div class="col-md-12 col-12 mb-1">
                                            <div class="row">
                                                <form action="{{ route('article.group.set') }}" method="GET">
                                                    <fieldset>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" name="search"
                                                                value="{{ request('search') }}" placeholder="Geben Sie die Details Ihrer Suche ein"
                                                                aria-describedby="button-addon2">
                                                            <div class="input-group-append" id="button-addon2">
                                                                <button class="btn btn-primary waves-effect waves-light" type="submit">
                                                                    <i class="feather icon-search"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </form> 

                                                <div class="d-flex justify-content-start ml-1   gap-1">
                                                    <button class="btn btn-success mr-1" data-toggle="modal" data-target="#articleGroupModal">
                                                        <i class="feather icon-folder-plus"></i> Artikelgruppe erstellen
                                                    </button>
                                                    <button class="btn btn-primary" data-toggle="modal" data-target="#subArticleGroupModal">
                                                        <i class="feather icon-file-plus"></i> Kategorie erstellen
                                                    </button>
                                                </div>
                                            </div>

                                          



                                            <!-- Artikelgruppe Modal -->
                                            <div class="modal fade" id="articleGroupModal" tabindex="-1" role="dialog" aria-labelledby="articleGroupModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <form id="articleGroupForm">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success white">
                                                        <h5 class="modal-title"><i class="feather icon-folder-plus mr-1"></i>Neue Artikelgruppe</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Artikelgruppe</label>
                                                            <input type="text" name="article_group" class="form-control" placeholder="z.B. Heizsysteme" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kurzzeichen</label>
                                                            <input type="text" name="initial" class="form-control" placeholder="z.B. HS" required>
                                                        </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                        <button type="submit" class="btn btn-success">
                                                            <i class="feather icon-save"></i> Speichern
                                                        </button>
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                                </div>


                                            <!-- Unterartikelgruppe Modal -->
                                            <div class="modal fade" id="subArticleGroupModal" tabindex="-1" role="dialog" aria-labelledby="subArticleGroupModalLabel" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <form id="subArticleForm">
                                                    @csrf
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary white">
                                                        <h5 class="modal-title"><i class="feather icon-file-plus mr-1"></i>Neue Kategorie</h5>
                                                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                        </div>
                                                        <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Artikelgruppe wählen</label>
                                                            <select name="article_group_id" class="form-control" required>
                                                            <option value="">-- Gruppe wählen --</option>
                                                            @foreach($article as $group)
                                                                <option value="{{ $group->id }}">{{ $group->article_group }}</option>
                                                            @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Kategorie</label>
                                                            <input type="text" name="sub_article" class="form-control" placeholder="z.B. Thermostat" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Initial</label>
                                                            <input type="text" name="initial" class="form-control" placeholder="z.B. T">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Wert</label>
                                                            <input type="text" name="value" class="form-control">
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Status</label>
                                                            <input type="text" name="status" class="form-control">
                                                        </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">
                                                            <i class="feather icon-save"></i> Speichern
                                                        </button>
                                                        </div>
                                                    </div>
                                                    </form>
                                                </div>
                                                </div>



                                        </div> 
                                    </div>
                             
                                  

                                    <div class="col-12">
                                        @foreach ($article as $item)
                                            <h4 class="primary">{{ $item->article_group }}</h4>
                                            <hr>
                                            <div class="row">
                                                @foreach ($item->subGroups as $sub)
                                                <div class="col-md-2">
                                                    <div class="card" id="cards">
                                                        <a href="{{ url('/master_set/'.$item->id.'/'.$sub->id) }}">
                                                            <div class="card-body text-center">
                                                                <img src="{{ asset('images/icons/folder.png') }}"
                                                                    alt="folder" width="50" class="mb-1 img-fluid">
                                                                <h6 class="card-text" style="font-size: 12px;">
                                                                    {{ $sub->sub_article }} ({{ $sub->master_sets_count }})
                                                                </h6>
                                                            </div>
                                                        </a>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        @endforeach 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Cards Ends -->
                </div>
            </section>
            <!-- // Basic example and Profile cards section end -->


        </div>
    </div>
</div>
<!-- END: Content-->
@stop


@section('script')
<!-- BEGIN: Page Vendor JS-->
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/forms/validation/jqBootstrapValidation.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.js')}}"></script>
<script src="{{ asset('app-assets/vendors/js/pickers/pickadate/picker.date.js')}}"></script>
<!-- END: Page Vendor JS-->
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>

<script>
$(document).ready(function() {
    $('#master').select2();

});
</script>











<script>
$(document).ready(function() {
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
    // Attach click event to the button with class 'show_set'
    $('.show_set').click(function() {
        // Get the data-id of the clicked button
        var setId = $(this).attr('data-id');

        // Toggle visibility of the corresponding div with class 'sets' and matching data-id
        $('.sets[data-id="' + setId + '"]').toggle();
    });
});
</script>


<script>
   $(document).ready(function () {
    function handleFormSubmit(formId, modalId, route, successMsg) {
        const $form = $(formId);
        const $modal = $(modalId);
        const $button = $form.find('button[type="submit"]');

        $form.on('submit', function (e) {
            e.preventDefault();
            $button.prop('disabled', true).html('<i class="feather icon-loader spinner"></i> Speichert...');

            $.ajax({
                url: route,
                method: "POST",
                data: $form.serialize(),
                success: function (res) {
                    toastr.success(successMsg);
                    $modal.modal('hide');
                    $form[0].reset();
                    setTimeout(() => location.reload(), 800);
                },
                error: function (xhr) {
                    toastr.error('Fehler beim Speichern. Bitte überprüfen Sie Ihre Eingaben.');
                },
                complete: function () {
                    $button.prop('disabled', false).html('<i class="feather icon-save"></i> Speichern');
                }
            });
        });
    }

    handleFormSubmit('#articleGroupForm', '#articleGroupModal', "{{ route('article_groups.store') }}", 'Artikelgruppe wurde gespeichert');
    handleFormSubmit('#subArticleForm', '#subArticleGroupModal', "{{ route('sub_article_groups.store') }}", 'Unterartikelgruppe wurde gespeichert');
});

</script>



@endsection