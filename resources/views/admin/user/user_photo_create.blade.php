@extends('admin.layouts.app')
@section('title') {{auth()->user()->name}} Profile Photo @endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
             <div class="content-wrapper">
                <div class="content-header row">
                    <div class="row" id="basic-table">
                        <div class="col-12">
                             <div class="card">
                                <div class="card-header">
                                     <h4 class="card-title">Add New > User Photo</h4>
                                     
                                        <div class="col-md-6">
                                            
                                                
                                            </div>  
                                         </div>


                         <div class="card-content"> 
                                <div class="card-body">
                            
                                    <div class="table-responsive">
                                        <form action="{{action('App\Http\Controllers\UserController@save_photo')}}" method="post" class="custom-file-upload" enctype="multipart/form-data">
                                             @csrf
                                                <span >
                                                <label for="image">Photo</label>
                                                <small><code>Please select the user profile photo</code></small>
                                                </span>
                                                @if($data)
                                                <div class="col-md-30">
                                                        <input type="hidden" name="id" value="{{auth()->user()->id}}">
                                                </div>
                                                  
                                                    <input type="file" name="image" class="form-control">
                                                        @if($errors->has('image'))
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong>{!@errors->first('image')!}</strong>
                                                            </span>
                                                        @endif
                                               
                                               @endif

                                                <div class="col-md-10 float-right">
                                                <button type="submit" class="btn btn-outline-primary block btn-lg" name="submit">Submit</button>
                                                  
                                                </div>
                                            </form>   
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
@stop