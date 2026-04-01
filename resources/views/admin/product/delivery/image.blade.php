@extends('admin.layouts.app')
@section('title') Lieferschein Foto @stop


@section('content')


    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">


            </div>
                          
            <div class="content-body">
             <!-- Table Hover Animation start -->
             <div class="row" id="table-hover-animation">
                    <div class="col-md-6 col-12 mb-1">
                    <form action="">
                            <fieldset>
                                <div class="input-group">
                               
                                    <input type="text" class="form-control" placeholder="Geben Sie die Details Ihrer Suche ein" aria-describedby="button-addon2" name="search" >
                                    <div class="input-group-append" id="button-addon2">
                                        <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                    </div>
                                    

                                
                                </div>
                            
                            </fieldset>
                        </form>
                        </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Lieferschein Foto - {{ $delivery_note }}</h4>
                            </div>
                           
                            <div class="card-content">
                                    <div class="card-body">
                               
                                        
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <div class="card-body">
                                                        @if (count($errors) > 0)
                                                            <div class="alert alert-danger">
                                                                <ul>
                                                                    @foreach ($errors->all() as $error)
                                                                        <li>{{ $error }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                            <!-- Table with outer spacing -->
                                                            <form novalidate action="{{ action('App\Http\Controllers\DeliveryNoteImageController@store')}}" method="post"  class="custom-file-upload" enctype="multipart/form-data">
                                                             @csrf
                                                            <div class="table-responsive">
                                                            @if(DB::table('user_rolls')
                                                                        ->where('user_rolls.user_id', '=', auth()->user()->id)
                                                                        ->where('user_rolls.item_id', '=', 'Product')
                                                                        ->where('user_rolls.is_add', '=', 'on')
                                                                        ->first())
                                                                <table class="table" id="image">
                                                                
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Lieferschein#</th>
                                                                            <th>Titel</th> 
                                                                            <th>Bild</th> 
                                                                        </tr>
                                                                    </thead>
                                                                    
                                                                    <tbody>
                                                                        <tr>
                                                                            <input type="hidden" name="product[0][delivery_note]" value="{{$delivery_note}}">
                                                                            <td>
                                                                                <input type="text" class="form-control required" disabled value="{{$delivery_note}}"> 
                                                                            </td>
                                                                           
                                                                            <td>
                                                                                <input type="text" class="form-control required" placeholder="Title of Image" name="product[0][title]">
                                                                            </td>
                                                                            <td><input type="file" class="form-control" name="product[0][image]"> </td>
                                                                        </tr>
                                                                        
                                                                    </tbody>
                                                            
                                                                            <div class="col-8">
                                                                            <div class="input-group">
                                                                                <a type="button" href="{{ URL::previous() }}"class="btn btn-outline-warning mr-1 mb-1"><i class="feather icon-chevrons-left"></i> Zurück</a>
                                                                                    <button type="submit" class="btn btn-outline-success mr-1 mb-1"><i class="feather icon-save"></i> Datensatz speichern</button>
                                                                                    <button type="button" class="btn btn-outline-warning mr-1 mb-1" id="add_image"><i class="feather icon-plus"></i> Bild hinzufügen</button>
                                                                                    <a type="button" class="btn btn-success mr-1 mb-1" href="{{ url('delivery_note_details') }}"><i class="fa fa-hourglass "></i> Vorgang abschließen</a>
                                                                                </div>
                                                                                
                                                                          </div>
                                                                    </form>
                                                                </table>
                                                                @endif
                                                                <table class="table" id="brand_table">
                                                                    <thead>
                                                                      
                                                                        <tr>
                                                                            <th>Lieferschein#</th>
                                                                            <th>Titel</th>
                                                                            <th>Bild</th>
                                                                            <th>Action</th>
                                                                        </tr>
                                                                       
                                                                    </thead>
                                                                <tbody>
                                                                        @foreach ($description as $desk)
                                                                            
                                                                  
                                                                        <tr>
                                                                            <td>{{ $delivery_note}}</td>
                                                                            <td>{{ $desk->name}}</td>
                                                                            <td>
                                                                                <!-- Image Modal -->
                                                        <a type="button" class="btn btn-icon btn-icon  mr-1 mb-1" data-toggle="modal" data-target="#image{{$desk->id}}">
                                                            <div class="avatar mr-1 ">
                                                                <img src="{{ asset('images/delivery_note/'.$desk->image) }}" alt="avtar img holder" height="32" width="32">
                                                             </div>
                                                            </a>
    
                                                                <!-- Modal -->
                                                                <div class="modal fade text-left" id="image{{$desk->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                               {{ $delivery_note }}
                                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                    <span aria-hidden="true">&times;</span>
                                                                                </button>
                                                                            </div>
                                                                            <div class="modal-body" style="text-align: center;">
                                                                                <img src="{{ asset('images/delivery_note/'.$desk->image) }}" alt="avtar img holder" height="200" width="200">
                                                                              
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                            
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- End Image Modal -->
                                                                                </td>
                                                                            
                                                    
                                                                            
                                                                        <td>
                                                                        <a type="button" href="{{ route('delivery.note.image.destroy',['id'=>$desk->id] )}}" class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1"><i class="feather icon-trash-2"></i></a>
                                                                        <a type="button" class="btn btn-icon rounded-circle btn-outline-primary mr-1 mb-1"  data-toggle="modal" data-target="#edit{{$desk->id}}"><i class="feather icon-edit"></i></a>
                                                                          <!-- Modal -->
                                                                          <div class="modal fade text-left" id="edit{{$desk->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                                                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                                                            <div class="modal-content">
                                                                                                <div class="modal-header">
                                                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                                                        <span aria-hidden="true">&times;</span>
                                                                                                    </button>
                                                                                                </div>
                                                                                                <form  action="{{ action('App\Http\Controllers\DeliveryNoteImageController@update')}}" method="post"  class="custom-file-upload" enctype="multipart/form-data">
                                                                                                    @csrf
                                                                                                <div class="modal-body" style="text-align: left;">
                                                                                                    <input type="hidden" name="id" value="{{ $desk->id}}">
                                                                                                    <table class="responsible" >
                                                                                                       
                                                                                                        <div class="col-md-12">
                                                                                                            <div class="form-group">
                                                                                                                <label for="Title">
                                                                                                                    Title
                                                                                                                </label>
                                                                                                            
                                                                                                                <input type="hidden" class="form-control"  name="id" value="{{ $desk->id }}" >
                                                                                                                <input type="text" class="form-control"  name="name" value="{{ $desk->name }}" required>
                                                                                                                @if ($errors->has('name'))<p style="color:red;">{!!$errors->first('name')!!}</p>@endif
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        <div class="col-md-12">
                                                                                                            <div class="form-group">
                                                                                                                <label for="Title">
                                                                                                                  Foto
                                                                                                                </label>
                                                                                                            
                                                                                                                <input type="file" class="form-control"  name="image"  >
                                                                                                                @if ($errors->has('image'))<p style="color:red;">{!!$errors->first('image')!!}</p>@endif
                                                                                                            </div>
                                                                                                        </div>

                                                                                                        
                                                                                                    </table>
                                                                                                    <hr>
                                                                                                    
                                                                                                </div>
                                                                                                <div class="modal-footer">
                                                                                                    <input type="submit" class="btn btn-primary">
                                                                                                </div>
                                                                                                </form>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <!-- End Image Modal -->
                                                                    </td>
                                                                        </tr>
                                                                        @endforeach
                                                                        
                                                                    </tbody>
                                                                    </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                       
                                    </div>
                                 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Table head options end -->
                {{$description->links()}}
            </div>
        </div>
    </div>
    <!-- END: Content-->
@stop

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
        var i = 0;
        $('#add_image').click(function(){
            ++i;
            $('#image').append(
              '<tr><input type="hidden" name="product['+i+'][delivery_note]" value="{{$delivery_note}}"><td><input type="text" class="form-control required" disabled value="{{$delivery_note}}"> </td><td><input type="text" class="form-control required" placeholder="Titel des Bildes" name="product['+i+'][title]"></td><td><input type="file" class="form-control" name="product['+i+'][image]"> </td><td><button type="button"  class="btn btn-icon rounded-circle btn-outline-danger mr-1 mb-1" id="add_remove"><i class="fa fa-trash"></i></button></td></tr>'  
              );
        });

        $(document).on('click', '#add_remove', function(){
            $(this).parents('tr').remove();
        })

    </script>

 
<script>

    $('#price_type').change(function(){
        if(this.value=="Fixed"){
            $("#plus").hide() ;
        }else{
            $("#plus").show() ;
        }
    })

    $('#payment').change(function(){
        if(this.value=="Vorous"){
            $("#advance").show();
        }else{
            $("#advance").hide();
        }
    })

    
</script>


@endsection


