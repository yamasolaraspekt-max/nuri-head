@extends('admin.layouts.app')

@section('title') Maschine und Autos @endsection
@section('style')
<!-- Include stylesheet -->

<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">

<style>
    body {
      margin: 0;
    }

    .sb-title {
      position: relative;
      top: -12px;
      font-family: Roboto, sans-serif;
      font-weight: 500;
    }

    .sb-title-icon {
      position: relative;
      top: -5px;
    }

    .card-container {
      display: flex;
      height: 500px;
      width: 600px;
    }

    .panel {
      background: white;
      width: 300px;
      padding: 20px;
      display: flex;
      flex-direction: column;
      justify-content: space-around;
    }

    .half-input-container {
      display: flex;
      justify-content: space-between;
    }

    .half-input {
      max-width: 120px;
    }

    .map {
      width: 300px;
    }

    h2 {
      margin: 0;
      font-family: Roboto, sans-serif;
    }

    input {
      height: 30px;
    }

    input {
      border: 0;
      border-bottom: 1px solid black;
      font-size: 14px;
      font-family: Roboto, sans-serif;
      font-style: normal;
      font-weight: normal;
    }

    input:focus::placeholder {
      color: white;
    }
    .leasing{
        display: none;
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
                            <h2 class="content-header-title float-left mb-0">Maschine und Autos</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Bearbeiten</a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            
            </div>
            <div class="content-body">
                <!-- Basic Horizontal form layout section start -->
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                        <div class="col-md-12 col-12">
                
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <form class="form-horizontal" novalidate method="post" action="{{action('App\Http\Controllers\MachineController@update')}}" class="custom-file-upload" enctype="multipart/form-data">
                                            @csrf
                                            <fieldset> 
                                                <div class="row"> 
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Besitzername
                                                            </label>
                                                            <input type="hidden" name="id" value="{{ request()->id }}">
                                                             <input type="text" class="form-control"  name="owner_name" value="{{ $data->owner_name }}" required>
                                                             @if ($errors->has('owner_name'))<p style="color:red;">{!!$errors->first('owner_name')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Eigentümer Kontakt
                                                            </label>
                                                            
                                                             <input type="text" class="form-control"  name="owner_contact" value="{{ $data->owner_contact }}" required>
                                                             @if ($errors->has('owner_contact'))<p style="color:red;">{!!$errors->first('owner_contact')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                            Auto Namen
                                                            </label>
                                                            
                                                            <input type="text" class="form-control"  name="name" value="{{ $data->name }}" required>
                                                            @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                            Model
                                                            </label>
                                                            
                                                            <input type="text" class="form-control"  name="model" value="{{ $data->model }}" required>
                                                            @if ($errors->has('model'))<p style="color:red;">{!!$errors->first('model')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                            Jahr
                                                            </label>
                                                            
                                                            <input type="text" class="form-control"  name="year" value="{{ $data->year }}" required>
                                                            @if ($errors->has('year'))<p style="color:red;">{!!$errors->first('year')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                            Farbe
                                                            </label>
                                                            
                                                            <input type="text" class="form-control"  name="color" value="{{ $data->color }}" required>
                                                            @if ($errors->has('color'))<p style="color:red;">{!!$errors->first('color')!!}</p>@endif
                                                        </div>
                                                    </div> 

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                            EngineTyp
                                                            </label>
                                                            
                                                            <input type="text" class="form-control"  name="engine_type" value="{{ $data->engine_type }}" required>
                                                            @if ($errors->has('engine_type'))<p style="color:red;">{!!$errors->first('engine_type')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                            Mileage
                                                            </label>
                                                            
                                                            <input type="number" class="form-control"  name="mileage" value="{{ $data->mileage }}" required>
                                                            @if ($errors->has('mileage'))<p style="color:red;">{!!$errors->first('mileage')!!}</p>@endif
                                                        </div>
                                                    </div> 

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Artikelgruppe/Gewerk
                                                            </label>
                                                            
                                                            <fieldset class="form-group">
                                                                <select class="select2-customize-result form-control required" name="used_for"  id="used_for"  style="width:100%">
                                                            
                                                                 @foreach ($article_groups as $article)
                                                                 <option value="{{ $article->id }}" @if($article->id == $data->article_group) selected @endif>{{ $article->article_group }}</option> 
                                                                 @endforeach
                                                                
                                                                </select>
                                                            </fieldset>
                                                             @if ($errors->has('used_for'))<p style="color:red;">{!!$errors->first('used_for')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                     <div class="form-group">
                                                         <label for="Title">
                                                             Zweig
                                                         </label>
                                                         
                                                         <fieldset class="form-group">
                                                             <select class="select2-customize-result form-control required" name="branch"  id="branch"  style="width:100%">
                                                                <option value="{{ old('branch') }}">{{ old('branch') }}</option> 

                                                                 @foreach ($branch as $br)
                                                                 <option value="{{ $br->id }}"  @if($br->id == $data->branch_id) selected @endif>{{ $br->branch }}</option>
                                                                 @endforeach
                                                             </select>
                                                         </fieldset>
                                                          @if ($errors->has('branch'))<p style="color:red;">{!!$errors->first('branch')!!}</p>@endif
                                                     </div>
                                                 </div>
 
                                                 <div class="col-md-6">
                                                     <div class="form-group">
                                                         <label for="Title">
                                                             Art des Kaufs
                                                         </label>
                                                         
                                                         <fieldset class="form-group">
                                                             <select class="select2-customize-result form-control required" name="purchase_type"  id="purchase_type"  style="width:100%">
                                                                 <option selected value="{{ $data->purchase_type }}">{{ $data->purchase_type }}</option>
                                                                 <option value="Barzahlung">Barzahlung</option>
                                                                 <option value="Ratenzahlung">Ratenzahlung</option>
                                                                 <option value="Leasing">Leasing</option> 
                                                             </select>
                                                         </fieldset>
                                                          @if ($errors->has('purchase_type'))<p style="color:red;">{!!$errors->first('purchase_type')!!}</p>@endif
                                                     </div>
                                                 </div>

                                                
                                                    <div class="col-md-6 leasing">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Leasing vom
                                                            </label>
                                                            
                                                             <input type="text" class="form-control"  name="leasing_from" value="{{ $data->leasing_form }}" required>
                                                             @if ($errors->has('leasing_from'))<p style="color:red;">{!!$errors->first('leasing_from')!!}</p>@endif
                                                        </div>
                                                    </div>
   
                                                    <div class="col-md-6 leasing">
                                                       <div class="form-group">
                                                           <label for="Title">
                                                               Leasing Datum
                                                           </label>
                                                           
                                                            <input type="date" class="form-control"  name="leasing_date"  value="{{ $data->leasing_date }}" required>
                                                            @if ($errors->has('leasing_date'))<p style="color:red;">{!!$errors->first('leasing_date')!!}</p>@endif
                                                       </div>
                                                   </div>
                                               
                                                   <div class="col-md-6 leasing">
                                                       <div class="form-group">
                                                           <label for="Title">
                                                               Leasing Enddatum
                                                           </label>
                                                           
                                                            <input type="date" class="form-control"  name="leasing_end_date"  value="{{ $data->leasing_end_date }}" required>
                                                            @if ($errors->has('leasing_end_date'))<p style="color:red;">{!!$errors->first('leasing_end_date')!!}</p>@endif
                                                       </div>
                                                   </div>
   
                                                   <div class="col-md-6 leasing">
                                                       <div class="form-group">
                                                           <label for="Title">
                                                               Leasing Prize
                                                           </label>
                                                           
                                                            <input type="number" class="form-control"  value="{{ $data->leasing_price }}" name="leasing_price"  required>
                                                            @if ($errors->has('leasing_price'))<p style="color:red;">{!!$errors->first('leasing_price')!!}</p>@endif
                                                       </div>
                                                   </div>
    
                                                   
                                                
                                                 <div class="col-md-6 kauf">
                                                    <div class="form-group">
                                                        <label for="Title">
                                                            Kaufpreis
                                                        </label>
                                                        
                                                         <input type="number" class="form-control"  name="purchase_price" value="{{ $data->purchase_price }}" required>
                                                         @if ($errors->has('purchase_price'))<p style="color:red;">{!!$errors->first('purchase_price')!!}</p>@endif
                                                    </div>
                                                </div>


                                                    <div class="col-md-6 kauf">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Kaufdatum
                                                            </label>
                                                            
                                                             <input type="date" class="form-control"  name="purchase_date" value="{{ $data->purchase_date }}" required>
                                                             @if ($errors->has('purchase_date'))<p style="color:red;">{!!$errors->first('purchase_date')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6 kauf">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                letztes Servicedatum...
                                                            </label>
                                                            
                                                             <input type="date" class="form-control"  name="last_service_date" value="{{ $data->last_service_date }}" required>
                                                             @if ($errors->has('last_service_date'))<p style="color:red;">{!!$errors->first('last_service_date')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1 kauf">
                                                        <div class="custom-control custom-switch custom-control-inline">
                                                            <input type="checkbox" class="custom-control-input" id="technical_inspection" @if($data->technical_inspection == 0) checked @endif name="technical_inspection">
                                                            <label class="custom-control-label" for="technical_inspection">
                                                            </label>
                                                            <span class="switch-label">TÜV</span>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-5 kauf" style="display: none;"  id="tuv_date">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                TÜV Datum
                                                            </label>
                                                            
                                                             <input type="date" class="form-control"  name="technical_inspection_date"  value="{{ $data->technical_inspection_date }}" required>
                                                             @if ($errors->has('technical_inspection_date'))<p style="color:red;">{!!$errors->first('technical_inspection_date')!!}</p>@endif
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                                Beschreibung
                                                            </label>
                                                            
                                                             <textarea  class="form-control"  name="description"  required>{{ $data->description }}</textarea>
                                                             @if ($errors->has('address'))<p style="color:red;">{!!$errors->first('address')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="Title">
                                                               Foto 
                                                            </label>
                                                           
                                                             <input type="file" class="form-control"  name="image"  required>
                                                             @if ($errors->has('image'))<p style="color:red;">{!!$errors->first('image')!!}</p>@endif
                                                        </div>
                                                    </div>
                                                </div>
                                             </fieldset>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                   
                    </div>
                </section>
                <!-- // Basic Horizontal form layout section end -->

               

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
        // Function to toggle visibility of TÜV date input
        function toggleTuuvDateInput() {
            if ($('#technical_inspection').is(':checked')) {
                $('#tuv_date').show();
            } else {
                $('#tuv_date').hide();
            }
        }
        
        // Bind the function to the change event of the checkbox
        $('#technical_inspection').change(toggleTuuvDateInput);
        
        // Call the function once to ensure correct initial state
        toggleTuuvDateInput();
    });
</script>


<script>
    $(document).ready(function() {
        $('#purchase_type').change(function() {
            var purchase = document.getElementById('purchase_type');
            if (purchase.value == 'Leasing') {
                $('.leasing').show();
                $('.kauf').hide();
            } else {
                $('.leasing').hide();
                $('.kauf').show();
            }
        });
    });
</script>
 
<script>
    $(document).ready(function() {
        $('#branch').select2();
        $('#parent').select2();
        $('#used_for').select2();
    });
 
</script>

@endsection