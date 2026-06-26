@extends('admin.layouts.app')

@section('title') ANGEBOT @stop

@section('style')
 <style>
    .opens {
        border-color: #e53060;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .actives {
        border-color: #92b532;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .inactives {
        border-color: #78a7cc;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }

    .project_ends {
        border-color: #213985;
        background: white;
        padding: 6px;
        border-style: solid;
        height: 110px !important;
        width: 110px !important;
        margin-right: 11px;
    }
    .project_cancel {
        background: white;
        padding: 6px;
         border-style: solid;
         border-color:#b1aaaa;
         height: 110px !important;
         width: 110px !important;
         margin-right: 11px;
    }
    .inner_size {
        height: 90px !important;
    }
   .articles {
    background: #b1aaaa;
    border-radius: 50%;
    height: 50px !important;
    width: 50px !important;
    margin-right: 11px;
    display: grid;
    align-items: center;
    text-align: center;
    cursor: pointer;
}
.articles input[type="radio"] {
    display: none;
}
.articles label {
    font-size: 20px !important;
    cursor: pointer;
    display: grid;
    align-items: center;
    height: 50px;
    width: 50px;
    margin: 0;
    padding: 0;
    border-radius: 50%; /* Ensure label maintains border-radius */
}
.articles input[type="radio"]:checked + label {
    background: #92b532;
    color: white;
    border-radius: 50%; /* Maintain border-radius when selected */
}
.article_text {
    color: #b1aaaa;
}
.article_text p {
    font-size: 15px !important;
}
 
    .scrollable-container {
        display: flex;
        flex-wrap: nowrap;
        justify-content: space-evenly;
        overflow-x: auto;
        width: 100%;
        padding: 10px 0;
    }

    .scrollable-container::-webkit-scrollbar {
        height: 8px;
    }

    .scrollable-container::-webkit-scrollbar-thumb {
        background-color: #888;
        border-radius: 10px;
    }

    .scrollable-container::-webkit-scrollbar-thumb:hover {
        background-color: #555;
    }

    .products {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        margin: 0 2px !important;
        flex-direction: column;
    }

    .card {
        min-width: 150px;
        margin: 0 10px;
    }

    .inner_size {
        padding: 20px;
    }
    .modal-backdrop {
    z-index: 1040 !important;
    }
    .modal {
        z-index: 1050 !important;
    }
    .modal-backdrop {
    position: absolute;
    }
    
    .openli {
        color: white;background: #e53060;display: flex;padding: 6px 6px 6px 6px;
    }
     .activeli {
        color: white;background:#92b532;display: flex;padding: 6px 6px 6px 6px;
    }
     .inactiveli {
        color: white;background: #78a7cc;display: flex;padding: 6px 6px 6px 6px;
    }
     .endedli {
        color: white;background: #213985;display: flex;padding: 6px 6px 6px 6px;
    }
     .cancelli {
        color: white;background: #7e7d7d;display: flex;padding: 6px 6px 6px 6px;
    }
    .sumli {
          color: white;background: #782567;display: flex;padding: 6px 6px 6px 6px;
    }
    .openli1 {
            display: flex;
    align-content: center;
    border: 1px #e53060;
    border-style: solid;
    }
     .activeli1 {
            display: flex;
    align-content: center;
    border: 1px #92b532;
    border-style: solid;
    }
     .inactiveli1 {
            display: flex;
    align-content: center;
    border: 1px #78a7cc;
    border-style: solid;
    }
     .endedli1 {
            display: flex;
    align-content: center;
    border: 1px #213985;
    border-style: solid;
    }
     .cancelli1 {
            display: flex;
    align-content: center;
    border: 1px #7e7d7d;
    border-style: solid;
    }

    .sumli1 {
            display: flex;
    align-content: center;
    border: 1px #782567;
    border-style: solid;
    }
    .simpleli {
        display: flex;padding: 6px 6px 6px 6px;
    }

    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 0; }
        100% { opacity: 1; }
    }

    .blink {
        animation: blink 1s infinite;
    }
    .bolders {
            font-size: 15px;
            font-weight: bolder;
            width: 167px;
        }
    
</style>



 <style>
    .circle {
      width: 35px;
      height: 35px;
      background-color: #7DC242;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 1.2rem;
    }
    .line {
         width: 9px;
            height: 4px;
            background-color: #7DC242;
            margin-left: -3px;
            margin-right: -2px;
            position: relative;
            top: 2px;
    }
    .profile {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #7DC242;
    }

    .profile-s {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #f4a459;
    }
    .profile-r {
      width: 35px;
      height: 35px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #ea5455;
    }
    .text {
      font-size: 10px;
      font-weight: 500;
      color: #555;
      text-align: center;
      margin-top: 10px;
    }
  </style>
@endsection

@section('content')

<!-- BEGIN: Content-->
<div class="app-content"> 
            <div class="content-wrapper"> 

                <div class="content-body">
                <!-- Table Hover Animation start -->
                    <div class="row" id="table-hover-animation">
                        <div class="col-12">
                            <div class="cards">
                                <div class="card-content">
                                    <div class="card-body">   
                                        <!-- Colors Section --> 
                                  
                                 
                                        <!-- Search Section -->
                                        <div class="row mt-6" style="margin-top:100px;">
                                            <div class="container d-flex"> 
                                                <div class="col-9">
                                                    <form action="{{ action('App\Http\Controllers\OffersController@index') }}">
                                                        <fieldset>
                                                            <div class="input-group">
                                                                <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                                                                <div class="input-group-append" id="button-addon2">
                                                                    <button class="btn btn-primary" type="submit">Go</button>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </form>
                                                </div> 
                                            </div>
                                        </div>

                                    
                                        <!-- Contents Details of Customer -->
                                        <div class="row">
                                              <div class="col-md-12" style=" justify-content: center !important;">
                                                <h4 class="text-bold-700 mt-2 mb-2" style="    text-align: center; color: #b1aaaa;" >PLANUNG-LISTE</h4> 
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <thead>
                                                         <tr style="background:#cfe09a; "> 
                                                            <th style="width: 45px;" >ID</th> 
                                                            <th  class="bolders ">DATUM</th> 
                                                            <th  class="bolders ">NAME</th> 
                                                            <th  class="bolders ">KONTAKT</th>
                                                            <th  class="bolders ">NOTIZ</th>
                                                            <th  class="bolders ">GEWERKE</th> 
                                                            <th style="width:20px !important" >   <span data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">STATUS  </span> 
                                                                    <div class="dropdown-menu">
                                                                        <span><label for="">Filtern nach</label></span>
                                                                         <span class="dropdown-item">
                                                                           <a  href="{{ url('/lead_qualified_sort') }}" ><i class="fa fa-circle primary" ></i> QUALIFIZIERT</a> 
                                                                        </span>
                                                                       
                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_not_qualified_sort') }}" ><i class="fa fa-circle warning" ></i> ERFORDERLICHE INFORMATIONEN</a>  
                                                                        </span>

                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_incomplete_sort') }}" ><i class="fa fa-circle danger" ></i> NICHT QUALIFIZIERT</a>  
                                                                        </span> 

                                                                        <span class="dropdown-item">
                                                                             <a  href="{{ url('/lead_junk_sort') }}" ><i class="fa fa-power-off danger" ></i> JUNKS</a>  
                                                                        </span> 
                                                                    </div> 
                                                            </th>
                                                            <th>VERFASSER</th>
                                                            <th>TOOLS</th>
                                                            <th width="2">BEARBEITEN</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($data as $item)    
                                                            <tr style="background:white;border-bottom: 13px solid #f8f8f8;" class="mb-2"> 
                                                                <th scope="row">{{ $item->id }}</th>
                                                                
                                                                <td>
                                                                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD.MM.YY') }} <br>
                                                                    <code> <strong> 
                                                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}                                   
                                                                    </strong></code>  
                                                                </td>
                                                                <td><a href="{{url('new_lead_profile/'.$item->id.'/'.$item->postcode.'/'.$item->address_no )}}">
                                                                        {{ $item->name }}  {{ $item->lastname }} <br>
                                                                        <small>
                                                                            <i class="feather icon-map-pin"></i> {{ $item->street }} <br>
                                                                                {{ $item->postcode }} <br>
                                                                                {{ $item->city }}
                                                                        </small>
                                                                    </a>
                                                                </td>
                                                                    
                                                                <td>
                                                                    <p class="mb-0" ><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                    <p class="mb-0" ><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                </td> 
                                                                <td>
                                                                    @if($item->note)
                                                                    <!-- Button to open modal -->
                                                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1" data-toggle="modal" data-target="#note{{$item->id}}">
                                                                        <i class="fa fa-sticky-note-o"></i>
                                                                    </button>
                                                                    @else
                                                                    <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" >
                                                                        <i class="fa fa-sticky-note-o"></i>
                                                                    </button>
                                                                    @endif
                                                                    <!-- Modal -->
                                                                    <div class="modal fade" id="note{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-primary white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">Notizen</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <div class="col-md-10"> 
                                                                                        <h1>{{ $item->title }} {{$item->name}} {{ $item->lastname}}</h1> 
                                                                                        <p>{{ $item->street}}<br>{{ $item->postcode }} 
                                                                                        </p>
                                                                                        <p style="margin:0; line-height:0px"><i class="feather icon-phone-call" ></i> {{ $item->telephone }}</p>
                                                                                        <p style="margin:0; line-height:0px"><i class="feather icon-smartphone" ></i> {{ $item->phone }}</p>
                                                                                        <p style="margin:0; line-height:0px"><i class="feather icon-mail" ></i> {{ $item->email }}</p>
                                                                                    </div>
                                                                                    <hr>
                                                                                    <h1 class="mb-2">Notizen</h1>
                                                                                    <div class="col-md-12">
                                                                                        <p>{{ $item->note }}</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <!-- Modal footer (optional) -->
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td> 
                                                                <td>
                                                                    <div style="justify-items: center;display: flex;align-items: center;justify-content: flex-start;flex-wrap: nowrap;">
                                                             
 
                                                                            @php
                                                                                $services = [
                                                                                    'complete' => 'Komplettlösung',
                                                                                    'montage' => 'Montage',
                                                                                    'product' => 'Produkt',
                                                                                    'plan' => 'Planung',
                                                                                    'maintenance' => 'Wartung',
                                                                                    'repair' => 'Reparatur',
                                                                                    'others' => 'Sonstiges',
                                                                                ]; 
                                                                                $service = $services[$item->service] ?? $item->service;  
                                                                            @endphp
                                                                
 
                                                                                 @php
                                                                                        // Determine the default image based on gender
                                                                                        $defaultImage = $item->gender === "Male" 
                                                                                            ? asset('images/gender/male.png') 
                                                                                            : asset('images/gender/female.png');

                                                                                        // Determine the actual image to use
                                                                                        $employeeImage = file_exists('images/employee/'.$item->emp_image) && $item->emp_image 
                                                                                            ? asset('images/employee/'.$item->emp_image) 
                                                                                            : $defaultImage;
                                                                                    @endphp 

                                                                                    <div class="d-flex flex-column align-items-center mr-1">
                                                                                        <div class="d-flex align-items-center">
                                                                                            <div class="circle">{{ $item->initial }}</div>
                                                                                            <div class="line"></div> 
                                                                                            <div class="image" data-toggle="tooltip" 
                                                                                                data-original-title="{{ $item->emp_name && $item->emp_lastname ? $item->emp_name . ' ' . $item->emp_lastname : 'Nicht zugewiesen' }}">
                                                                                                <img src="{{ $employeeImage }}" alt="Profile"  
                                                                                                
                                                                                                class="profile">
                                                                                            </div> 
                                                                                        </div>
                                                                                    <div class="text">{{ $service }}</div>
                                                                                </div>
                                                                </td>  
                                                                
                                                                <td>
                                                                    <div class="badge badge-primary">@if($item->status=="new") Neue @endif</div>
                                                                </td>
                                                                @php
                                                                    $employee = DB::table('employees')->where('id', $item->contact_person)->select('name', 'lastname', 'image')->first();
                                                                    $c_image = $employee->image;
                                                                    $c_name = $employee->name;
                                                                    $c_lastname = $employee->lastname;
                                                                @endphp     
                                                                <td style="width:20px">
                                                                    <div class="image">
                                                                        <div class="avatar mr-1 ">
                                                                            <img src="{{ asset('images/employee/'.$c_image)}}" alt="avtar img holder" height="32" width="32" data-toggle="tooltip" data-placement="top" title data-original-tiitle="{{ $c_name }} {{ $c_lastname}}">
                                                                        </div>
                                                                        <div class="text">
                                                                            <span class="font-weight-bold"></span>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-outline-primary mr-1 mb-1 waves-effect waves-light" disabled=""><i class="feather icon-settings"></i>
                                                                        Planung Tools
                                                                    </button>
                                                                </td>
                                                                <td>

                                                                <div class="btn-group dropup dropdown-icon-wrapper mr-1 mb-1"> 
                                                                    <button type="button" class="btn   dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="feather icon-menu dropdown-icon"></i>
                                                                    </button>
                                                                    <div class="dropdown-menu"> 
                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_delete', '=', 'on')->first())
                                                                            @if($item->status!="Junk")
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#junk{{$item->id}}"><i class="fa fa-power-off danger" ></i> Junk</a>
                                                                            </span>
                                                                            @else
                                                                             <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="danger" data-target="#unjunk{{$item->id}}"><i class="fa fa-power-off primary" ></i>Un-Junk</a>
                                                                            </span>
                                                                            @endif
                                                                        @endif

                                                                        @if(DB::table('user_rolls')->where('user_rolls.user_id', '=', auth()->user()->name)->where('user_rolls.item_id', '=', 'Customer')->where('user_rolls.is_update', '=', 'on')->first())
                                                                            
                                                                            <span class="dropdown-item">
                                                                                <a data-toggle="modal" class="primary" data-target="#junk{{$item->id}}"><i class="feather icon-fast-forward primary" ></i>Überspringen</a>
                                                                            </span>
                                                                            
                                                                        @endif
                                                        
                                                                    </div>
                                                                </div>
                                                                
                                                                    <!-- Delete Modal -->
                                                                    <div class="modal fade" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-danger white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">Daten Löschen</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <h5>Aufzeichnung löschen</h5>
                                                                                    <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}}. {{ $item->name }} {{ $item->lastname }} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/new_lead_delete').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Delete Modal -->
                                                                    <div class="modal fade" id="junk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-danger white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <h5>Junk record</h5>
                                                                                    <p>Möchten Sie diese Anfrage als Junk festlegen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/lead_junk').'/'.$item->id}}" class="btn btn-danger">Ja</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                           <!-- Unjunk Modal -->
                                                                    <div class="modal fade" id="unjunk{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel120" aria-hidden="true" data-backdrop="false">
                                                                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                                                            <div class="modal-content">
                                                                                <div class="modal-header bg-primary white">
                                                                                    <h5 class="modal-title" id="myModalLabel120">{{ $item->name }} {{ $item->lastname }}</h5>
                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                        <span aria-hidden="true">×</span>
                                                                                    </button>
                                                                                </div>
                                                                                <div class="modal-body">
                                                                                    <h5>Junk record</h5>
                                                                                    <p>Möchten Sie die Junk-Anfrage wiederherstellen?</p>
                                                                                    <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                                                                </div>
                                                                                <div class="modal-footer">
                                                                                    <a type="button" href="{{url('/lead_unjunk').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>  
                                                                </td> 
                                                            </tr>  
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        {{$data->links()}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Table head options end -->
                </div>
            </div>
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

<script>
$(document).ready(function() {
    $('.articles input[type="radio"]').on('change', function() {
        // Reset styles for all labels
        $('.articles input[type="radio"] + label').css({
            'background': '#b1aaaa',
            'color': 'inherit',
            'border-radius': '50%'
        });

        // Apply styles for the selected label
        if (this.checked) {
            $(this).next('label').css({
                'background': '#92b532',
                'color': 'white',
                'border-radius': '50%'
            });

            // Send AJAX request
            let articleGroup = $(this).val();
            $.ajax({
                url: '/customer_details', // Your endpoint for searching article group
                method: 'GET',
                data: { search: articleGroup, is_ajax: true },
                success: function(response) {
                    // Handle the response here
                    console.log(response);
                    // Update the page content based on the response
                    $('#results').html(response); // Assuming 'results' is the id of the element where you want to display the results
                },
                error: function(error) {
                    // Handle the error here
                    console.error(error);
                }
            });
        }
    });
});
</script>

<script>
document.getElementById('colaps').addEventListener('click', function() {
    var section = document.getElementById('upper_view');
    var icon = this.querySelector('i');
    
    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        icon.classList.remove('feather', 'icon-chevron-down');
        icon.classList.add('feather', 'icon-chevron-up');
    } else {
        section.style.display = 'none';
        icon.classList.remove('feather', 'icon-chevron-up');
        icon.classList.add('feather', 'icon-chevron-down');
    }
});
</script>

<script src="{{ asset('app-assets/js/scripts/popover/popover.js')}}"></script>
 
 
@endsection
