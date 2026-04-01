@extends('admin.layouts.app')
@section('title') Arbeitsschritte Details @stop
@section('style')
<style>
    .card-header{
    background:transparent;
}

.cards{
        background: #f5f5f5;
    border-radius: 20px;
    margin: 5px;
}
.cards:hover{
       background: #8fc73f;
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
                                <h2 class="content-header-title float-left mb-0">ARBEITSSCHRITTE</h2>
                                <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item"><a href="{{ url('/task_phase') }}">Arbeitsphase</a>
                                        </li>
                                        <li class="breadcrumb-item"><a  href="{{ url('/task_phase_details/'.request()->product) }}">{{ DB::table('article_groups')->where('id', request()->product)->value('article_group') }}</a>
                                        </li>
                                        <li class="breadcrumb-item active"><a  >Leistungen</a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                          
            <div class="content-body">
                <section id="basic-horizontal-layouts">
                    <div class="row">
                       <div class="col-md-12">
                         <button type="button" class="btn btn-outline-primary waves-effect waves-light float-right  mb-2" data-toggle="modal" data-target="#primary">
                            Neue hinzufügen
                            </button>
                            <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true" style="display: none;">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header bg-primary white">
                                            <h5 class="modal-title" id="myModalLabel160">NEUE PHASE</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">×</span>
                                            </button>
                                        </div>
                                        <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\PhaseSectionController@store')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                                @csrf   
                                            <div class="modal-body">  
                                                    <div class="form-body">
                                                        <div class="row"> 
                                                            <div class="col-12">
                                                                <div class="form-group row">
                                                                    <div class="col-md-4">
                                                                        <span>Phase Name</span>
                                                                    </div>
                                                                    <div class="col-md-8">
                                                                        <input type="text" id="phase_section" class="form-control" value="{{old('phase_section')}}" name="phase_section" >
                                                                        <input type="hidden" name="product_id" value="{{ request()->product }}">
                                                                    </div>
                                                                </div>
                                                            </div>  
                                                        </div> 
                                                    </div>  
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal" >abbrechen</button>
                                                <button type="submit" class="btn btn-primary waves-effect waves-light" >speichern</button>
                                            </div>
                                        </form> 
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="row match-height">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="col-md-12 col-12">
                                <div class="row" id="table-hover-animation">
                                    <div class="col-12">
                                        <div class="card"  > 
                                            <div class="card-content">
                                                <div class="card-body">    
                                                    <div class="col-md-12">
                                                         <div class="row">
                                                            @foreach ($data as $art) 
                                                            <div class="col-xl-2 col-md-4 col-sm-6">
                                                                <div class="card text-center">
                                                                    <div class="card-content">
                                                                      @if(!in_array($art->phase_section, ['complete', 'plan', 'montage', 'maintenance', 'repair', 'others','product']))
                                                                        <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1" style="position: absolute; right: -14px;"> 
                                                                            <button type="button" class="btn btn-warning dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                <i class="feather icon-menu dropdown-icon"></i>
                                                                            </button>
                                                                            <div class="dropdown-menu">
                                                                                <a data-toggle="modal" data-target="#update{{$art->id}}">
                                                                                    <span class="dropdown-item">
                                                                                        <i class="feather icon-edit"></i>
                                                                                    </span> 
                                                                                </a>
                                                                                <a data-toggle="modal" data-target="#delete{{$art->id}}">
                                                                                    <span class="dropdown-item">
                                                                                        <i class="feather icon-trash"></i>
                                                                                    </span>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    @endif

                                                                        <a href="{{ url('phase_management/'.$art->product_id.'/'.$art->id) }}">
                                                                        <div class="card-body cards" >
                                                                            <div class="avatar bg-rgba-secondary p-50 m-0 mb-1">
                                                                                <div class="avatar-content black">
                                                                                   {{ $loop->index }}
                                                                                </div>
                                                                            </div>
                                                                            <h6 class="text-bold-500">
                                                                                @if($art->phase_section == 'complete')
                                                                                Komplettlösung  
                                                                                @elseif($art->phase_section == 'montage')
                                                                                Montage
                                                                                @elseif($art->phase_section == 'product')
                                                                                Produkt
                                                                                @elseif($art->phase_section == 'plan')
                                                                                Planung
                                                                                @elseif($art->phase_section == 'maintenance')
                                                                                Wartung
                                                                                @elseif($art->phase_section == 'repair')
                                                                                Reparatur
                                                                                @elseif($art->phase_section == 'others') 
                                                                                Sonstiges
                                                                                @else
                                                                                {{ $art->phase_section }}
                                                                                @endif
                                                                            </h6> 
                                                                        </div> 
                                                                        </a> 
                                                                    </div>
                                                                </div>
                                                            </div>
                                                             <div class="modal fade" id="delete{{$art->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-danger white">
                                                                                <h5 class="modal-title" id="myModalLabel120">{{ $art->phase_section }}</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">×</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <h5>Aufzeichnung löschen</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <a type="button" href="{{url('/task_section_delete').'/'.$art->id}}" class="btn btn-danger">Ja</a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                 <div class="modal fade text-left" id="update{{$art->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true" style="display: none;">
                                                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header bg-primary white">
                                                                                <h5 class="modal-title" id="myModalLabel160">NEUE PHASE</h5>
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">×</span>
                                                                                </button>
                                                                            </div>
                                                                            <form class="form form-horizontal" method="post" action="{{ action('App\Http\Controllers\PhaseSectionController@update')}}"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                    @csrf   
                                                                                <div class="modal-body">  
                                                                                        <div class="form-body">
                                                                                            <div class="row"> 
                                                                                                <div class="col-12">
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-md-4">
                                                                                                            <span>Phase Name</span>
                                                                                                        </div>
                                                                                                        <div class="col-md-8">
                                                                                                            <input type="text" id="phase_section" class="form-control" value="{{$art->phase_section}}" name="phase_section" >
                                                                                                            <input type="hidden" name="product_id" value="{{ request()->article }}">
                                                                                                            <input type="hidden" name="id" value="{{ $art->id }}">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div> 
                                                                                                
                                                                                            </div> 
                                                                                        </div>  
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-dismiss="modal" >abbrechen</button>
                                                                                    <button type="submit" class="btn btn-primary waves-effect waves-light" >speichern</button>
                                                                                </div>
                                                                            </form> 
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                               
                                                        </div>
                                                    </div> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Table head options end -->
                  
                            </div>
                          
                        </div>  
                    </div> 
                </section>  
            </div>
        </div>
    <!-- END: Content-->
@stop



@section('script')

 <script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.showTask').forEach(function(button) {
        button.addEventListener('click', function() {
            var taskId = this.getAttribute('data-id');
            var taskProduct = this.getAttribute('data-product');
            fetch('/activities_details/' + taskId + '/' + taskProduct)
                .then(response => response.json())
                .then(data => {
                    var content = '';
                    data.forEach(function(activity) {
                        content += `
                            <tr>
                                <th scope="row">${activity.id}</th>
                                <th scope="row">${activity.phase_name}</th>
                                <td>${activity.title}</td>
                                <td>${activity.description}</td>
                                <td>
                                    <!-- Action buttons (edit, delete, etc.) -->
                                </td>
                            </tr>`;
                    });
                    document.querySelector('#activities tbody').innerHTML = content;
                })
                .catch(error => console.error('Error:', error));
        });
    });
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.addPhase').forEach(function(button) {
        button.addEventListener('click', function() {
            var taskId = this.getAttribute('data-id');
            var taskProduct = this.getAttribute('data-product');
            console.log('Button clicked:', { taskId, taskProduct });

            // Open the modal
            $('#second').modal('show');

            // Fetch activity details and fill the form
            fetch('/activities_details/' + taskId + '/' + taskProduct)
                .then(response => response.json())
                .then(data => {
                    console.log('Fetched data:', data); // Debug log

                    // Ensure the data has the expected structure
                    if (data.phase_id && data.product_id) {
                        document.getElementById('phase_id').value = data.phase_id;
                        document.getElementById('product_id').value = data.product_id;
                        document.getElementById('initial').value = data.initial;
                        document.getElementById('title').value = data.title;
                        document.getElementById('description').value = data.description;
                    } else {
                        console.error('Unexpected data structure:', data);
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    document.getElementById('activityForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent form from submitting normally

        const formData = new FormData(this);
        console.log('Form data before submission:', Object.fromEntries(formData.entries())); // Debug log

        fetch('/activities_details', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            // Handle the response data
            console.log('Success:', data);
            // Optionally, you can close the modal and reset the form
            $('#second').modal('hide');
            this.reset();
            // Optionally, you can reload the activities
            document.querySelectorAll('.showTask[data-id="' + data.phase_id + '"]').forEach(button => button.click());
        })
        .catch(error => console.error('Error:', error));
    });
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



 
@endsection