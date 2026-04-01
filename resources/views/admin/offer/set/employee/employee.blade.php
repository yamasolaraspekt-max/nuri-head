@extends('admin.layouts.app')
@section('title') Set Mitarbeiter @stop
@section('style')
<!-- Include stylesheet -->
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}"> 
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
                            <h2 class="content-header-title float-left mb-0">Set Mitarbeiter</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('article_group_set') }}"> {{ $title->article_group }}</a>
                                    </li>

                                    <li class="breadcrumb-item"><a href="{{ url('master_set/'.$title->article_group_id.'/'.$title->sub_id) }}"> {{ $title->sub_article }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('master_set/'.$title->article_group_id.'/'.$title->sub_id) }}">{{ $title->setname }}</a>
                                    </li>
                                    <li class="breadcrumb-item"><a href="{{ url('sets/'.request()->master) }}">Set Mitarbeiter </a>
                                    </li>
                                
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                          
            <div class="content-body"> 

            <div class="row align-items-center mb-2">
                <div class="col-md-9">
                    <form action="{{ action('App\Http\Controllers\EmployeeSetController@index', ['master' => request()->master, 'phase' => request()->phase]) }}">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search Form" aria-describedby="button-addon2">
                            <div class="input-group-append" id="button-addon2">
                                <button class="btn btn-primary" type="submit">Go</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-3 text-right">
                    <a type="button" class="btn btn-outline-secondary  " href="{{ url('/sets/' . request()->master . '/' . request()->phase) }}">
                        Zurück
                    </a>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-pro">Position erstellen</button>

                        <!-- Modal -->
                        <div class="modal fade text-left" id="add-pro" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        Position zum Satz hinzufügen
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>

                                    <form method="POST" action="{{ action('App\Http\Controllers\EmployeeSetController@store') }}">
                                        @csrf
                                        <div class="modal-body row">
                                        <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                                        <input type="hidden" name="product_id" value="{{ $title->article_group_id }}">
                                        <input type="hidden" name="phase_id" id="phase_id" value="{{ request()->phase }}">
                                        <input type="hidden" name="buying_price" id="buying_price_hidden">

                                        <div class="form-group col-md-6">
                                            <label>Position</label>
                                            <select class="form-control" name="position_id" required id="position" style="width:100%">
                                            <option value="">-- wählen --</option>
                                            @foreach($positions as $option)
                                                <option value="{{ $option->id }}">{{ $option->position }}</option>
                                            @endforeach
                                            </select>
                                        </div>
        
                                        <div class="form-group col-md-6">
                                            <label>Arbeitsstunden</label>
                                            <input type="text" class="form-control" name="work_hour" required>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Verkaufspreis (€)</label>
                                            <input type="text" class="form-control" name="sale_price" id="sale_price" required>
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label>Einkaufspreis (€)</label>
                                            <input type="text" class="form-control" id="buying_price" readonly>
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Aufgaben (wird automatisch geladen)</label>
                                            <select name="activity_id[]" id="activity" class="form-control" multiple  style="width:100%;"></select>
                                        </div>
                                        </div>

                                        <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Einreichen</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                </div>
            </div>


                <div class="row" id="table-hover-animation">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Mitarbeiterposition</th>
                                    <th scope="col">Lohn</th>
                                    <th scope="col">Arbeitsstunde</th>
                                    <th scope="col">Grade</th>
                                    <th scope="col">Aufgaben</th>
                                    <th scope="col">Arbeitspreis/Uhr</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->position }}</td>
                                    <td>{{ number_format($item->sale_price, 2, ',', '.') }} €</td>
                                    <td>{{ $item->work_hour }} Std</td>
                                    <td>
                                            <!-- Grade Modal -->
                                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#grade-pro{{$item->id}}">
                                            <i class="feather icon-bar-chart-2"></i>
                                            </button>

                                            <!--Grad Modal -->
                                            <div class="modal fade text-left" id="grade-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        {{ $item->position }}
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table" id="">
                                                                <thead>
                                                                    <tr style=" background: #8fc73e;   color: white;  ">
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Gewerk</th>
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Beratung</th>
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Planung</th>
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Kalkulation</th>
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Montage</th>
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Projektierung</th>
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Bauleitung</th>
                                                                    </tr>
                                                                </thead>
                                                            <tbody>
                                                                    @foreach ($skills as $skil)
                                                                    <tr>
                                                                    
                                                                        <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">{{ $skil->article_group }}</td>
                                                                        <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                            <div class="fonticon-wrap">
                                                                            @for ($i = 1; $i <=  $skil->advice; $i++)
                                                                            <i class="fa fa-star" style="color:gold"></i>
                                                                            @endfor
                                                                            </div>
                                                                        </td>

                                                                        <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                            <div class="fonticon-wrap">
                                                                            @for ($i = 1; $i <=  $skil->plan; $i++)
                                                                            <i class="fa fa-star" style="color:gold"></i>
                                                                            @endfor
                                                                            </div>
                                                                        </td>

                                                                        <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                            <div class="fonticon-wrap">
                                                                            @for ($i = 1; $i <= $skil->calculation; $i++)
                                                                            <i class="fa fa-star" style="color:gold"></i>
                                                                            @endfor
                                                                            </div> 
                                                                        </td>

                                                                        <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                            <div class="fonticon-wrap">
                                                                            @for ($i = 1; $i <= $skil->montage; $i++)
                                                                            <i class="fa fa-star" style="color:gold"></i>
                                                                            @endfor
                                                                            </div>
                                                                        </td>

                                                                        <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                            <div class="fonticon-wrap">
                                                                            @for ($i = 1; $i <= $skil->project_planing; $i++)
                                                                            <i class="fa fa-star" style="color:gold"></i>
                                                                            @endfor
                                                                            </div>
                                                                        </td>
                                                                        <td style="border: 1px; border-style: solid; padding: 0 0 0 4px;">
                                                                        <div class="fonticon-wrap">
                                                                            @for ($i = 1; $i <= $skil->site_management; $i++)
                                                                            <i class="fa fa-star" style="color:gold"></i>
                                                                            @endfor
                                                                            </div>
                                                                        </td> 
                                                                    </tr> 
                                                                    @endforeach
                                                            </tbody>
                                                            </table>
                                                        
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" href="{{url('/add_employee_set_delete').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                    </td>
                                    
                                    <td>
                                            <button type="button" class="btn btn-outline-primary waves-effect waves-light" data-toggle="modal" data-target="#task{{$item->id}}">
                                            Aufgaben
                                            </button> 
                                            <div class="modal fade text-left" id="task{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                        {{ $item->position }}
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <table class="table" id="">
                                                                <thead>
                                                                    <tr style=" background: #8fc73e;   color: white;  ">
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Aufgebe</th>
                                                                        <th style="border: 1px; border-style: solid; padding: 3px 0 0 4px;">Bescheribung</th> 
                                                                    </tr>
                                                                </thead>
                                                            <tbody> 
                                                                    @foreach ($activity as $active)
                                                                    @if($active->employee_set_id == $item->id)
                                                                        <tr> 
                                                                            <td>{{$active->title}}</td>
                                                                            <td>{{$active->description}}</td> 
                                                                        </tr>
                                                                    @endif
                                                                    @endforeach  
                                                            </tbody>
                                                            </table>
                                                        
                                                        </div>
                                                        <div class="modal-footer">
                                                            <a type="button" href="{{url('/add_employee_set_delete').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> 
                                    </td>
                                    <td>
                                        
                                            <a data-toggle="modal" data-target="#price-pro{{$item->id}}">
                                                <div class="chip chip-primary mr-1">
                                                    <div class="chip-body">
                                                        <span class="chip-text">Gehalt zuweisen</span>
                                                    </div>
                                                </div>
                                            </a> 
                                     
                                            <div class="modal fade text-left" id="price-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmployeeSetController@buying_price')}}">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="Title">Arbeitspreis</label>
                                                                        <fieldset>
                                                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                                                <input type="checkbox" class="toggleCheckbox" data-id="{{$item->id}}" checked name="has_salary">
                                                                                <span class="vs-checkbox">
                                                                                    <span class="vs-checkbox--check">
                                                                                        <i class="vs-icon feather icon-check"></i>
                                                                                    </span>
                                                                                </span>
                                                                                <span>Verwenden Sie das Gehalt</span>
                                                                            </div>
                                                                        </fieldset>
                                                                        <input type="hidden" name="id" value="{{ $item->id }}">
                                                                        <input type="hidden" name="master_id" value="{{ request()->master }}">
                                                                        <input type="text" class="form-control" name="buying_price" id="buy{{$item->id}}" data-id="{{$item->id}}" placeholder="Buying Price">
                                                                        <input type="text" class="form-control" value="{{$item->sale_price}}" name="sale_price" id="sale{{$item->id}}" data-id="{{$item->id}}" placeholder="Sale Price" style="display:none;">
                                                                    </div>
                                                                </div>
                                                        </div>  
                                                        <div class="modal-footer">
                                                        <button type="submit" class="btn btn-primary">Einreichen</button>
                                                        </div>
                                                    </form>
                                                    </div>
                                                </div>
                                            </div>
                                  
                                            
                                        {{ number_format( $item->buying_price, 2, ',', '.') }}€
                                        
                                    </td>

                                    <td>
                                        {{ number_format( $item->total, 2, ',', '.') }}€
                                    
                                    </td>
                                    
                                    <td>

                                <!-- Delete Modal -->
                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$item->id}}">
                                <i class="feather icon-trash"></i>
                                </button>

                                <!-- Modal -->
                                <div class="modal fade text-left" id="delete-pro{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h5>Aufzeichnung löschen</h5>
                                                <p>Möchten Sie diesen Datensatz wirklich löschen?</p>
                                                <p>Die Datensatznummer lautet:{{$item->id}} </p>
                                            </div>
                                            <div class="modal-footer">
                                                <a type="button" href="{{url('/add_employee_set_delete').'/'.$item->id}}" class="btn btn-primary">Ja</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Delete Modal -->



                            <button type="button" class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1"  data-toggle="modal" data-target="#price{{$item->id}}">
                                <i class="feather icon-edit"></i>
                                </button>

                                    <!-- Modal -->
                                    <div class="modal fade text-left" id="price{{$item->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\EmployeeSetController@update')}}">
                                                    @csrf
                                                    <div class="modal-body">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Arbeitsstunde
                                                            </label>
                                                            <input type="text" class="form-control" value="{{ $item->work_hour }}"  name="work_hour" >
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                            Arbeitspreis
                                                            </label>
                                                
                                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                                            <input type="hidden" name="master_id" value="{{ request()->master }}">
                                                            <input type="text" class="form-control"  name="buying_price" value="{{ $item->buying_price }}">
                                                        </div>
                                                    </div>
                                                </div>  
                                                <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Einreichen</button>
                                                </div>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <!-- End Delete Modal -->
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
    <!-- END: Content-->
@stop

@section('script')

<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    $('#product').select2({
        placeholder: "Produkt auswählen",
        allowClear: true
    });

    $('#measure').select2({
        placeholder: "Maß auswählen",
        allowClear: true
    });

    $('#activity').select2({
        placeholder: "Aktivität auswählen",
        allowClear: true
    });

    $('#position').select2({
        placeholder: "Position auswählen",
        allowClear: true
    });
});
</script>


<script>
    document.querySelectorAll('.toggleCheckbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const itemId = this.getAttribute('data-id');
            const buyInput = document.querySelector(`#buy${itemId}`);
            const saleInput = document.querySelector(`#sale${itemId}`);
            
            if (this.checked) {
                // Show the "Buying Price" and hide the "Sale Price"
                buyInput.style.display = 'block';
                saleInput.style.display = 'none';
            } else {
                // Show the "Sale Price" and hide the "Buying Price"
                buyInput.style.display = 'none';
                saleInput.style.display = 'block';
            }
        });
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  const productId = "{{ $title->article_group_id }}";
  const phaseId = "{{ request()->phase }}";

  // Load activities via AJAX
  fetch(`/api/load-activities/${productId}/${phaseId}`)
    .then(res => res.json())
    .then(data => {
      const select = document.getElementById('activity');
      select.innerHTML = '';

      data.forEach(activity => {
        const opt = document.createElement('option');
        opt.value = activity.id;
        opt.innerText = `${activity.title}: ${activity.description}`;
        select.appendChild(opt);
      });
    });
});
</script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    $('#add-pro').on('shown.bs.modal', function () {
        const workHourInput = document.querySelector("input[name='work_hour']");
        const salePriceInput = document.getElementById("sale_price");
        const buyingPriceDisplay = document.getElementById("buying_price");
        const buyingPriceHidden = document.getElementById("buying_price_hidden");

        function updateBuyingPrice() {
            const hours = parseFloat(workHourInput.value);
            const price = parseFloat(salePriceInput.value);
            if (!isNaN(hours) && !isNaN(price)) {
                const total = (hours * price).toFixed(2);
                buyingPriceDisplay.value = total;
                buyingPriceHidden.value = total;
            } else {
                buyingPriceDisplay.value = '';
                buyingPriceHidden.value = '';
            }
        }

        function fetchSalary(positionId) {
            if (positionId) {
                fetch(`/position-salary/${positionId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success && data.salary) {
                            const formattedSalary = parseFloat(data.salary).toFixed(2);
                            salePriceInput.value = formattedSalary;

                            Swal.fire({
                                title: '💰 Gehalt gefunden',
                                text: `Stundenlohn für diese Position: ${formattedSalary} €`,
                                icon: 'info',
                                confirmButtonText: 'OK',
                                timer: 2500,
                                timerProgressBar: true
                            });
                        } else {
                            salePriceInput.value = '';
                        }
                        updateBuyingPrice();
                    })
                    .catch(() => {
                        salePriceInput.value = '';
                        updateBuyingPrice();
                    });
            } else {
                salePriceInput.value = '';
                updateBuyingPrice();
            }
        }

        // Select2-aware change handler
        $('#position').off('change').on('change', function () {
            const positionId = $(this).val();
            fetchSalary(positionId);
        });

        workHourInput.addEventListener("input", updateBuyingPrice);
        salePriceInput.addEventListener("input", updateBuyingPrice);
    });
});
</script>



@endsection