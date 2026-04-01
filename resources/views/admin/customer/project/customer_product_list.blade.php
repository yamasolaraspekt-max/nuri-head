@extends('admin.layouts.app')
@section('title') Projectmanagment @stop
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
                    </div>
                    <style>
                        #cards:hover{
                            background: #8fc73e;
                            cursor: pointer;
                        }
                    </style>
                            <div class="row">
                                @foreach ($data as $item)
                                <div class="col-md-1 col-md-2">
                                    <div class="card"  id="cards">
                                        <a href="{{ url('/customer_profile/'.$item->id) }}">
                                        <div class="card-body" style="    text-align: center;">
                                          
                                            <img src="{{ asset('images/icons/folder.png') }}" alt="element 02" width="70" class="mb-1 img-fluid">
                                            <h6 style="    font-size: 12px;" class="card-text">{{ $item->article_group }} - #{{ $item->product_count}}</h6>
                                            <p class="card-text" style="    font-size: 10px;" >{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</p>
                                        </div>
                                        </a>
                                       
                                        
                                     </div>
                                   
                                </div>
                                @endforeach

                            </div>
           
                {{$data->links()}}
            </div>
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


@endsection