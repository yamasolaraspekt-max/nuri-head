@extends('admin.layouts.app')
@section('title')
CHECKLISTE
@endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/daily.css') }}">
 
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
                            <h2 class="content-header-title float-left mb-0">CHECKLISTE</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li>
                                    <li class="breadcrumb-item active">Checkliste
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                 
            </div>
            <div class="content-body"> 
                <div class="row">
                  <div class="col-md-8">
                     <input type="text" id="search" placeholder="Search by status..." class="form-control mb-3"> 
                  </div>
                  <div class="col-md-4">
                   <a type="button" class="btn btn-outline-primary square mr-1 mb-1 waves-effect waves-light" href="{{ url('checklist/create') }}">erstellen</a>
                  </div>
                </div>
                <div id="checklist-container">
                    @include('admin.checklist.partial.index', ['data' => $data, 'sortBy' => $sortBy, 'sortDirection' => $sortDirection]) 
                </div>

                              
                
            </div>
        </div>
    </div>
    <!-- END: Content-->

@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>

  <script>
    $(document).on('click', '.sort-link', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');

        $.ajax({
            url: url,
            type: 'GET',
            success: function (res) {
                $('#checklist-container').html(res.html);

            },
            error: function () {
                alert('Sorting failed. Please try again.');
            }
        });
    });
</script>

 <script>
    // Search
    $('#search').on('keyup', function () {
        $.get("{{ route('checklists.index') }}", { search: $(this).val() }, function (data) {
          $('#checklist-container').html(data.html);
      });

    });

    // Delete
    $(document).on('click', '.delete-btn', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Are you sure?',
            showCancelButton: true,
            confirmButtonText: 'Delete'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/checklists/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function (res) {
                        Swal.fire('Deleted!', res.message, 'success');
                        $('#search').keyup(); // refresh list
                    }
                });
            }
        });
    });
</script>

@endsection