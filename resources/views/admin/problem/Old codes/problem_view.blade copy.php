@extends('admin.layouts.app')
@section('title') Problem Details @stop

@section('style')
<style>
    .customer_names:hover {
        color:#8fc73f;
    }

    .customer_names {
        color:#5c5c5c;
    }

    .image-body img {
        max-width: 100%;
        height: 150px;
        object-fit: cover;
        border-radius: 5px;
    }

    .editable-name:focus {
        outline: none;
        background: #fff6e0;
        border: 1px solid #ffc107;
        padding: 3px 5px;
        border-radius: 4px;
    }

    .image {
        transition: transform 0.2s;
    }

    .image:hover {
        transform: scale(1.02);
    }

    .preview-click:hover {
    opacity: 0.8;
    transition: 0.3s;
}


</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />

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
                            <h2 class="content-header-title float-left mb-0">FEHLERHANDBUCH</h2>
                            <div class="breadcrumb-wrapper col-12">
                                    <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Dashboard</a></li> 
                                    <li class="breadcrumb-item active"><a >Fehlertyp</a></li>
                                    
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>   
            </div>     
            <div class="content-body">
             <!-- Table Hover Animation start -->
             <div class="row" id="table-hover-animation">
                    <div class="col-12">
                        <div class="card"> 
                            <div class="card-content">
                                <div class="card-body"> 
                                    <div class="col-9">
                                        <form action="{{action('App\Http\Controllers\ProblemController@index')}}">
                                            <fieldset>
                                                <div class="input-group">
                                                    <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                    <div class="input-group-append" id="button-addon2">
                                                        <buttfon class="btn btn-primary" type="submit">Go</buttfon>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
 
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th >#</th>
                                                    <th >Ticket-Nr.</th>
                                                    <th >Kundeninfo</th>
                                                    <th >Problem & Status</th> 
                                                    <th >Produkt</th>
                                                    <th >Meldung</th>
                                                    <th >Zuständig</th>  
                                                    <th >Foto</th>
                                                    <th >Aktion</th>
                                                    <th >#</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($data as $item)
                                                <tr> 
                                                    <th scope="row">{{$item->id}}</th>
                                                    <td>{{$item->ticket_no}} 
                                                        @if($item->repeated)
                                                        <div class="badge badge-pill  badge-warning ">Wiederholtes</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ url('/new_lead_profile/'.$item->cid)}} "  class="customer_names">
                                                            <strong>{{$item->firma}}</strong>
                                                            <strong><p class="m-0 p-0">{{$item->name}} {{$item->lastname}}</p> </strong>
                                                            <p class="m-0 p-0">{{$item->street}} {{$item->postcode}} {{ $item->city}}</p>  
                                                            <p class="m-0 p-0"><i class="feather icon-phone"></i> {{$item->phone}} </p>  
                                                            <p class="m-0 p-0"><i class="feather icon-mail"></i> {{$item->email}} </p>     
                                                        </a>
                                                        Registiert durch: {{ DB::table('employees')->select('name','lastname')->where('id','=', $item->start_user)->pluck('name')->first() }} 

                                                        
                                                    </td>
                                                    <td>
                                                          <p class="m-0 p-0">
                                                               @if($item->status == "offen")
                                                                    <div class="badge badge-pill  badge-danger "
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="Erfasst durch: {{ \App\Models\Employee::find($item->start_user)?->name }} {{ \App\Models\Employee::find($item->start_user)?->lastname }}">
                                                                        Offen
                                                                    </div>

                                                                @elseif($item->status == "process")
                                                                    <div class="badge badge-pill  badge-warning "
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="In Bearbeitung von: {{ \App\Models\Employee::find($item->progress_user)?->name }} {{ \App\Models\Employee::find($item->progress_user)?->lastname }}">
                                                                        In Bearbeitung
                                                                    </div>

                                                                @elseif($item->status == "end")
                                                                    <div class="badge badge-pill  badge-success "
                                                                        data-toggle="tooltip"
                                                                        data-placement="top"
                                                                        title="Abgeschlossen von: {{ \App\Models\Employee::find($item->end_user)?->name }} {{ \App\Models\Employee::find($item->end_user)?->lastname }}">
                                                                        Beendet
                                                                    </div>
                                                                @endif

                                                            </p> 

                                                        <p class="m-0 p-0">
                                                            <a   data-toggle="modal" data-target="#problem{{$item->id}}">
                                                                <div class="badge badge-danger  ">
                                                                    <i class="feather icon-alert-octagon"></i>
                                                                    <span>Problembeschreibung</span>
                                                                </div>
                                                            </a>
                                                               @if($item->solution)
                                                              <a  data-toggle="modal" data-target="#solution{{$item->id}}">
                                                                <div class="badge badge-primary  ">
                                                                    <i class="feather icon-alert-octagon"></i>
                                                                    <span>Lösungsbeschreibung</span>
                                                                </div>
                                                            </a>
                                                            @endif
                                                        </p> 
                                                        <small> 
                                                            <p class="m-0 p-0"><i class="fa fa-hourglass-half" ></i> Offen seit: {{ \Carbon\Carbon::parse($item->date)->diffForHumans() }}</p>
                                                            <p class="m-0 p-0"> <i class="feather icon-clock"></i> Latzte Änderung: {{ \Carbon\Carbon::parse($item->updated_at) }}</p> 
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <p> {{ $item->product}}</p> 
                                                        
                                                            @foreach ($error as $proE)
                                                                @if($proE->problem_id==$item->id)
                                                                <a href="{{ url('error?search='.$proE->error_code) }}">
                                                                    <div class="badge badge-pill  badge-warning mr-1  ">{{ $proE->error_code }} - {{ $proE->problem_types }}</div>
                                                                </a>
                                                                @endif
                                                            @endforeach

                                                                 <!-- Modal -->
                                                            <div class="modal fade text-left" id="problem{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            Problembeschreibung
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <a href="{{ url('/new_lead_profile/'.$item->cid)}} "  class="customer_names">
                                                                                <strong>{{$item->firma}}</strong>
                                                                                <strong><p class="m-0 p-0">{{$item->name}} {{$item->lastname}}</p> </strong>
                                                                                <p class="m-0 p-0">{{$item->street}} {{$item->postcode}} {{ $item->city}}</p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-phone"></i> {{$item->phone}} </p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-mail"></i> {{$item->email}} </p>     
                                                                            </a>
                                                                            <p><code>Ticket Nr.: {{ $item->ticket_no}}</code> <code>Verfasser: {{ $item->fname}} {{ $item->flastname}}</code>
                                                                                 <code>Produkt: {{ $item->product}} </code>  <code>Erstellt am: {{ \Carbon\Carbon::parse($item->date)->isoFormat('DD.MM.YYYY')}} </code>
                                                                            </p>
                                                                              @foreach ($error as $proE)
                                                                                    @if($proE->problem_id==$item->id)
                                                                                    <a href="{{ url('error?search='.$proE->error_code) }}">
                                                                                        <div class="badge badge-pill  badge-warning mr-1  ">{{ $proE->error_code }} - {{ $proE->problem_types }}</div>
                                                                                    </a>
                                                                                    @endif
                                                                                @endforeach
                                                                            <hr/> 
                                                                            <p>{!! $item->problem !!}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="modal fade text-left" id="solution{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                             Lösungsbeschreibung
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">

                                                                             <a href="{{ url('/new_lead_profile/'.$item->cid)}} "  class="customer_names">
                                                                                <strong>{{$item->firma}}</strong>
                                                                                <strong><p class="m-0 p-0">{{$item->name}} {{$item->lastname}}</p> </strong>
                                                                                <p class="m-0 p-0">{{$item->street}} {{$item->postcode}} {{ $item->city}}</p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-phone"></i> {{$item->phone}} </p>  
                                                                                <p class="m-0 p-0"><i class="feather icon-mail"></i> {{$item->email}} </p>     
                                                                            </a>
                                                                            <p><code>Ticket Nr.: {{ $item->ticket_no}}</code> <code>Verfasser: {{ $item->fname}} {{ $item->flastname}}</code>
                                                                                 <code>Produkt: {{ $item->product}} </code>  <code>Erstellt am: {{ \Carbon\Carbon::parse($item->date)->isoFormat('DD.MM.YYYY')}} </code>
                                                                                  <code>Status: {{ $item->status}}</code>
                                                                                  <code>Beendet von: {{ $item->end_user}}</code>
                                                                            </p>
                                                              
                                                                            @if($item->solution)
                                                                            <p>{!! $item->solution !!}</p>
                                                                            @else
                                                                            <p>Das Problem ist noch nicht gelöst</p>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        
                                                    </td>

                                                    <td>
                                                        <small>
                                                              <p  class="m-0 p-0">Verfasser: {{ $item->fname}} {{ $item->flastname}}</p>
                                                        </small>
                                                       <small>
                                                        <p class="m-0 p-0">Erstellt am: 
                                                            {{ $item->date ? \Carbon\Carbon::parse($item->date)->isoFormat('DD.MM.YYYY') : '-' }}
                                                        </p>
                                                        @if($item->date)
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-danger mr-1">
                                                                    {{ \Carbon\Carbon::parse($item->date)->diffForHumans() }}
                                                                </div>
                                                            </p>
                                                        @endif
                                                    </small>

                                                    <small>
                                                        <p class="m-0 p-0">Prozessdatum: 
                                                            {{ $item->progress_date ? \Carbon\Carbon::parse($item->progress_date)->isoFormat('DD.MM.YYYY') : '-' }}
                                                        </p>
                                                        @if($item->progress_date)
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->progress_date)->diffForHumans() }}
                                                                </div>
                                                            </p>
                                                        @endif
                                                    </small>

                                                    <small>
                                                        <p class="m-0 p-0">Ticket-Enddatum: 
                                                            {{ $item->end_date ? \Carbon\Carbon::parse($item->end_date)->isoFormat('DD.MM.YYYY') : '-' }}
                                                        </p>

                                                        @if($item->end_date)
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->end_date)->diffForHumans() }}
                                                                </div>
                                                            </p>
                                                            <p class="m-0 p-0">
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->end_date)->diffInDays(\Carbon\Carbon::parse($item->date)) }} Tage
                                                                </div>
                                                                <div class="badge badge-pill badge-warning">
                                                                    {{ \Carbon\Carbon::parse($item->end_date)->diffInHours(\Carbon\Carbon::parse($item->date)) }} Std
                                                                </div>
                                                            </p>
                                                        @endif
                                                    </small>

                                                      
                                                    </td>
                                            
                                                    <td> 
                                                       @php
                                                            $maxVisible = 4;
                                                            $responsiblesForItem = $responsible->where('problem_id', $item->id);
                                                            $visibleResponsibles = $responsiblesForItem->take($maxVisible);
                                                            $hiddenResponsibles = $responsiblesForItem->slice($maxVisible);
                                                        @endphp

                                                        <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                                            @foreach ($visibleResponsibles as $resp)
                                                                <li data-toggle="tooltip" data-popup="tooltip-custom" data-placement="bottom"
                                                                    data-original-title="{{ $resp->rname }} {{ $resp->rlastname }}" class="avatar pull-up">
                                                                    <img class="media-object rounded-circle"
                                                                        src="{{ asset('images/employee/'.$resp->rimage) }}"
                                                                        alt="Avatar" height="30" width="30">
                                                                </li>
                                                            @endforeach

                                                            @if ($hiddenResponsibles->count())
                                                                <li class=" ">
                                                                    <a href="javascript:void(0);" onclick="showResponsibleModal({{ $item->id }})"
                                                                    class=""
                                                                    style="width: 40px;height: 28px;display: flex;align-items: center;justify-content: center;padding: 20px;border: 1px solid;border-radius: 50%;">
                                                                        +{{ $hiddenResponsibles->count() }}
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
  

                                                        <div class="modal fade" id="responsibleModal" tabindex="-1" role="dialog" aria-labelledby="responsibleModalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-lg" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title">Verantwortliche Personen</h5>
                                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <div class="modal-body">
                                                                            <table class="table table-hover table-striped" id="responsibleTable">
                                                                                <thead>
                                                                                    <tr>
                                                                                        <th>Bild</th>
                                                                                        <th>Name</th>
                                                                                    </tr>
                                                                                </thead>
                                                                                <tbody id="responsibleTableBody">
                                                                                    {{-- dynamically filled --}}
                                                                                </tbody>
                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                    </td>

                                                    <td>
                                                       <button type="button" class="btn btn-outline-primary square  waves-effect waves-light open-gallery"
                                                            data-id="{{ $item->id }}">
                                                        Gallarie
                                                    </button> 
                                                    </td>
   

                                                     <td style="width: 50px;">
                                                        <div class="btn-group dropup dropdown-icon-wrapper "> 
                                                            <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light"
                                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="feather icon-more-vertical dropdown-icon"></i>
                                                            </button>

                                                            <div class="dropdown-menu">

                                                                {{-- Edit --}}
                                                                @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Problem')->where('is_add', 'on')->first())
                                                                <a class="dropdown-item" href="{{ url('problem_edit/'.$item->id) }}">
                                                                    <i class="feather icon-edit"></i> Bearbeiten
                                                                </a>
                                                                @endif

                                                                {{-- Comments --}}
                                                                <a class="dropdown-item" href="{{ url('/problem_comment/'.$item->id.'/'.$item->ticket_no) }}">
                                                                    <i class="fa fa-comments-o"></i> Kommentare
                                                                </a>

                                                                {{-- Delete --}}
                                                                @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Problem')->where('is_delete', 'on')->first())
                                                                <button class="dropdown-item" data-toggle="modal" data-target="#delete-pro{{ $item->id }}">
                                                                    <i class="feather icon-trash"></i> Löschen
                                                                </button>
                                                                @endif
 

                                                            </div>
                                                        </div>

                                                        {{-- Delete Modal --}}
                                                        <div class="modal fade" id="delete-pro{{ $item->id }}" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog modal-dialog-scrollable">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Aufzeichnung löschen</h5>
                                                                        <button type="button" class="btn-close" data-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                        <p>Datensatznummer: {{ $item->id }}</p>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <a href="{{ url('/problem_destroy/'.$item->id) }}" class="btn btn-danger">Ja, löschen</a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
 
                                                    </td>



                                                    @if(DB::table('user_rolls')->where('user_id', auth()->user()->name)->where('item_id', 'Problem')->where('is_update', 'on')->first())
                                                        <td>
                                                            @if($item->status == 'offen')
                                                                <a href="{{ url('/problem_progress/'.$item->id) }}" class="btn btn-sm btn-primary mb-1">
                                                                    <i class="feather icon-alert-circle"></i> in Klärung
                                                                </a>
                                                                <a href="{{ url('/problem_close/'.$item->id) }}" class="btn btn-sm btn-success mb-1">
                                                                    <i class="feather icon-check"></i> Beendet
                                                                </a>
                                                            @elseif($item->status == 'process')
                                                                <a href="{{ url('/problem_open/'.$item->id) }}" class="btn btn-sm btn-danger mb-1">
                                                                    <i class="feather icon-slash"></i> Offen
                                                                </a>
                                                                <a href="{{ url('/problem_close/'.$item->id) }}" class="btn btn-sm btn-success mb-1">
                                                                    <i class="feather icon-check"></i> Beendet
                                                                </a>
                                                            @elseif($item->status == 'end')
                                                                <a href="{{ url('/problem_progress/'.$item->id) }}" class="btn btn-sm btn-primary mb-1">
                                                                    <i class="feather icon-alert-circle"></i> in Klärung
                                                                </a>
                                                                <a href="{{ url('/problem_open/'.$item->id) }}" class="btn btn-sm btn-danger mb-1">
                                                                    <i class="feather icon-slash"></i> Offen
                                                                </a>
                                                            @endif
                                                        </td>
                                                        @endif



                                                </tr>
                                               
                                                @endforeach
                                   
                                            </tbody>
                                        </table>

                                        <!-- Image Modal: start  -->
                                        <div class="modal fade" id="galleryModal" tabindex="-1" role="dialog">
                                            <div class="modal-dialog modal-xl" role="document">
                                                <div class="modal-content p-2">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Ticket Gallery</h5>
                                                        <button type="button" class="btn btn-outline-primary round  waves-effect waves-light" data-dismiss="modal">X</button>
                                                    </div>
                                                    <div class="modal-body">
                                                        
                                                        <form id="dropzoneForm" class="dropzone" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        <input type="hidden" name="ticket_id" id="ticket_id">
                                                        <input type="hidden" name="stage" value="upload">
                                                        </form>

                                                        <div class="row mt-3" id="gallery">
                                                        <!-- Loaded via AJAX -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Preview Modal -->
                                        <div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                                <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Preview</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-center" id="previewContent">
                                                    <!-- Dynamic content goes here -->
                                                </div>
                                                </div>
                                            </div>
                                            </div>

                                       <!-- Image Modal: end  -->
                                    </div>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table head options end -->
                {{$data->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   
    $(document).ready(function(){
        @if(Session::has('update_msg'))
        toastr.warning("{{ session('update_msg') }}");
        @endif
        @if(Session::has('save_msg'))
        toastr.success("{{ session('save_msg') }}");
        @endif

       
  
  @if(Session::has('delete_msg'))
  toastr.error("{{ session('delete_msg') }}");
  @endif
    });
    

</script>


<!-- Dynamic showing the respoinsbiel in modal Start -->
<script>
    const allResponsibles = @json($responsible);

    function showResponsibleModal(problemId) {
        const filtered = allResponsibles.filter(r => r.problem_id === problemId);
        const tbody = $('#responsibleTableBody');
        tbody.empty();

        filtered.forEach(person => {
            const row = `
                <tr>
                    <td><img src="/images/employee/${person.rimage}" alt="Avatar" class="rounded-circle" width="40" height="40"></td>
                    <td>${person.rname} ${person.rlastname}</td>
                </tr>
            `;
            tbody.append(row);
        });

        $('#responsibleModal').modal('show');
    }
</script>
<!-- Dynamic showing the respoinsbiel in modal end -->



<script>
    Dropzone.autoDiscover = false;
    let dz;

    $(document).on('click', '.open-gallery', function () {
        let ticketId = $(this).data('id');
        $('#ticket_id').val(ticketId);
        $('#galleryModal').modal('show');

        if (dz) dz.destroy();

        dz = new Dropzone("#dropzoneForm", {
            url: "{{ route('ticket.image.upload') }}",
            paramName: "file",
            maxFilesize: 2,
            acceptedFiles: 'image/*,application/pdf',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function () {
                loadGallery(ticketId);
            }
        });

        loadGallery(ticketId);
    });

    function loadGallery(ticketId) {
        $.get(`/ticket-image/list/${ticketId}`, function (data) {
            let html = '';
            data.forEach(file => {
                let isImage = file.file_type && file.file_type.startsWith('image');

                let preview = isImage
                    ? `<img src="/storage/${file.image}" class="img-thumbnail mb-2 preview-click"
                            data-src="/storage/${file.image}" data-type="image"
                            style="max-width:100%; height:150px; cursor:pointer;">`
                    : `<div class="text-center preview-click"
                            data-src="/storage/${file.image}" data-type="pdf"
                            style="cursor:pointer;">
                            <i class="fa fa-file-pdf fa-3x text-danger mb-2"></i>
                            <div>${file.image_name}</div>
                    </div>`;

                html += `
                <div class="col-md-3 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            ${preview}
                            <div class="mt-2">
                                <div contenteditable="true"
                                    class="editable-name"
                                    data-id="${file.id}"
                                    id="name-${file.id}"
                                    style="cursor: text; border-bottom: 1px dashed #ccc; display: inline-block; width: 100%;">
                                    ${file.image_name}
                                </div>
                                <button class="btn btn-sm btn-danger mt-2" onclick="deleteFile(${file.id})">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>`;
            });

            $('#gallery').html(html);
        });
    }


    $(document).on('click', '.preview-click', function () {
        const src = $(this).data('src');
        const type = $(this).data('type');

        let content = '';
        if (type === 'image') {
            content = `<img src="${src}" class="img-fluid" style="max-height:80vh;">`;
        } else if (type === 'pdf') {
            content = `<iframe src="${src}" frameborder="0" style="width:100%; height:80vh;"></iframe>`;
        }

        $('#previewContent').html(content);
        $('#previewModal').modal('show');
    });




    function deleteFile(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will delete this image permanently!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!'
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/ticket-image/delete/${id}`,
                    type: 'DELETE',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function () {
                        $('#name-' + id).parent().remove();
                        Swal.fire('Deleted!', '', 'success');
                    }
                });
            }
        });
    }

    // Save rename on Enter or Tab
        $(document).on('keypress', '.editable-name', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault(); // Prevent new line
                $(this).blur();     // Trigger save
            }
        });

    // Rename on blur
    $(document).on('blur', '.editable-name', function () {
        const id = $(this).data('id');
        const newName = $(this).text().trim();

        if (!newName) {
            $(this).text('Unnamed File');
            return;
        }

        $.post(`/ticket-image/rename/${id}`, {
            _token: '{{ csrf_token() }}',
            name: newName
        }).done(() => {
            console.log('Renamed successfully!');
        }).fail(() => {
            alert('Rename failed. Please try again.');
        });
    });
</script>

 
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
 


@endsection