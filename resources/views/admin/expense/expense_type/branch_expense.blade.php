@extends('admin.layouts.app')
@section('title') Ausgabenarten @stop
@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
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
                            <h2 class="content-header-title float-left mb-0">Ausgabenarten </h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a> 
                                    </li>
                                    <li class="breadcrumb-item active"><a href=" ">Zweigstellenkosten</a> 
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

                                    <input type="text" class="form-control"
                                        placeholder="Geben Sie die Details Ihrer Suche ein" aria-describedby="button-addon2"
                                        name="search">
                                    <div class="input-group-append" id="button-addon2">
                                        <button class="btn btn-primary waves-effect waves-light" type="button"><i
                                                class="feather icon-search"></i></button>
                                    </div>

                                </div>
                            </fieldset>
                        </form>
                    </div>
                    <div class="col-md-2 mb-1">

                        <button type="button" class="btn btn-outline-primary block btn-lg" data-toggle="modal"
                            data-target="#default">
                            Neue hinzufügen
                        </button>
                        <!-- Modal -->
                        <div class="modal fade text-left" id="default" tabindex="-1" role="dialog"
                            aria-labelledby="myModalLabel1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel1">Neu</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <form class="form-horizontal" novalidate method="post"
                                            action="{{action('App\Http\Controllers\BranchExpenseController@store')}}"
                                            class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <fieldset>
                                                <div class="row"> 

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Zweig
                                                            </label>

                                                            <select name="branch_id" class="form-control">
                                                                @foreach ($branches as $br)
                                                                <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                                                @endforeach
                                                            </select>
                                                            @if ($errors->has('branch_id'))<p style="color:red;">
                                                                {!!$errors->first('branch_id')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">Jahr</label>
                                                            <select name="year" class="form-control year" id="year" style="width:100% !important;">
                                                                <option value=""> </option>
                                                                @for ($year = date('Y'); $year >= 1992; $year--)
                                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                                @endfor
                                                            </select>
                                                            @if ($errors->has('year'))
                                                                <p style="color:red;">{!! $errors->first('year') !!}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Einreichen</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal End -->

            </div>
        </div>

        <div class="col-12"> 
            <div class="row">
                @foreach ($branches as $item)
                <div class="col-md-2 col-md-2 col-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-start pb-0" style="    align-self: center;"> 
                            <a href="{{ url('/expense_details/'.$item->id) }}" data-toggle="modal" data-target="#year{{$item->id}}"> 
                                <div>
                                    <i class="feather icon-folder" style="font-size: 124px;"></i> 
                                    <h4 class="text-bold-700 mb-0">{{ $item->branch }}</h4>
                                    <p>
                                    <div class="badge badge-primary">
                                            @php
                                                // Calculate the sum of 'total' for the current branch
                                                $sum = $data->where('branch_id', $item->id)->sum('total');
                                            @endphp
                                            
                                            @if ($sum > 0) <!-- Show the total if it's greater than 0 -->
                                                <h4>Total: {{ number_format($sum, 2, ',', '.') }}€</h4>
                                            @else
                                                <h4>Total: 0.00€</h4>
                                            @endif 
                                    </div>
                                    </p>
                                </div>
                            </a> 
                        
                        </div>
                        <div class="modal fade text-left" id="year{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18" style="display: none;" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel18"><i class="feather icon-folder"></i> {{ $item->branch }}</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Zweig</th>
                                                        <th>Jahr</th>
                                                        <th>Kosten</th> 
                                                        <th>Aktion</th> 
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($data as $year) 
                                                    @if($year->branch_id == $item->id)
                                                    <tr>
                                                        <th scope="row"><a href="{{ url('/expense_details/'.$year->id.'/'.$year->branch_id.'/'.$year->year)}}"> {{$item->branch}}</a> </th>
                                                        <td>{{ $year->year }}</td> 
                                                        <td>{{ number_format($year->total, 2, ',', '.') }}€</td> 
                                                        <td>
                                                            <div class="btn-group dropdown-menu-right dropdown-icon-wrapper mr-0 mb-0">
                                                                    <button type="button"
                                                                        class="btn  dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="feather icon-align-justify dropdown-icon"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu" x-placement="top-start"
                                                                        style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(79px, -233px, 0px);">


                                                                        @if(DB::table('user_rolls')
                                                                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                                        ->where('user_rolls.item_id', '=', 'Product')
                                                                        ->where('user_rolls.is_update', '=', 'on')
                                                                        ->first())

                                                                        <!-- Begin: Edit -->
                                                                    <a    data-toggle="modal" data-target="#edit{{ $year->id }}">
                                                                        <span class="dropdown-item">
                                                                            <i class="feather icon-edit"></i> Bearbeiten
                                                                        </span>
                                                                    </a>
 

                                                                        <a href="javascript:void(0)" class="delete-btn" data-id="{{ $year->id }}">
                                                                            <span class="dropdown-item">
                                                                                <i class="feather icon-trash"></i> Löschen
                                                                            </span>
                                                                        </a>
                                                                        
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                        </td>
                                                    </tr> 

                                                    <!-- Modal -->
                                                    <div class="modal fade" id="edit{{$year->id}}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                            <div class="modal-header bg-success white">
                                                                <h5 class="modal-title" id="editModalLabel">Branch Expense Bearbeiten</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <form id="editForm">
                                                                 @csrf <!-- This will include the CSRF token -->
   
                                                                <div class="modal-body">
                                                                <input type="hidden" name="id" id="expenseId" value="{{$year->id}}">
                                                                <div class="form-group">
                                                                    <label for="branch_id">Filiale</label>
                                                                    <select name="branch_id" id="branch_id" class="form-control" required>
                                                                        @foreach ($branches as $br)
                                                                            <option value="{{ $br->id }}" @if($br->id == $year->branch_id) selected @endif>{{ $br->branch }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="year">Jahr</label>
                                                                    <select name="year" class="form-control year" id="year" style="width:100% !important;"> 
                                                                     @for ($years = date('Y'); $years >= 1992; $years--)
                                                                        <option value="{{ $years }}" @if($years == $year->year) selected @endif>{{ $years }}</option>
                                                                    @endfor 
                                                                    </select> 
                                                                </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                                                                <button type="submit" class="btn btn-primary">Speichern</button>
                                                                </div>
                                                            </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Accept</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                @endforeach

            </div>
        </div> 
    </div>
</div> 
<!-- END: Content-->

    

@stop

@section('script')
  <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
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

        $('.year').select2();
    });
    

</script>


<script>
    $(document).ready(function(){
        $('.delete-btn').on('click', function(e){
            e.preventDefault();

            var id = $(this).data('id');
            var url = "{{ url('branch_expense_delete') }}/" + id;
            
            Swal.fire({
                title: 'Bist du sicher?',
                text: "Möchtest du diese Daten wirklich löschen?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'GET',
                        success: function(response) {
                            Swal.fire(
                                'Gelöscht!',
                                'Die Daten wurden erfolgreich gelöscht.',
                                'success'
                            ).then(() => {
                                location.reload();  // Reload the page after success
                            });
                        },
                        error: function(response) {
                            Swal.fire(
                                'Fehler!',
                                'Es gab ein Problem beim Löschen der Daten.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });
</script>


<script>
    $(document).ready(function() {
        // When the edit button is clicked, open the modal and load the data
        $('.edit-btn').on('click', function() {
            var id = $(this).data('id');
            // Use AJAX to get the data for the selected branch expense
            $.get("{{ url('branch.expense.update') }}/" + id, function(data) {
                $('#expenseId').val(data.id);
                $('#branch_id').val(data.branch_id);
                $('#year').val(data.year);
                $('#editModal').modal('show');
            });
        });

        // Handle form submission via AJAX
        $('#editForm').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();
            var id = $('#expenseId').val();

            $.ajax({
                url: "{{ route('branch.expense.update') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    Swal.fire({
                        title: 'Erfolgreich!',
                        text: 'Die Daten wurden erfolgreich aktualisiert.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        $('#editModal').modal('hide');
                        location.reload(); // Reload the page after success
                    });
                },
                error: function(response) {
                    Swal.fire(
                        'Fehler!',
                        'Es gab ein Problem beim Aktualisieren der Daten.',
                        'error'
                    );
                }
            });
        });
    });
</script>



@endsection