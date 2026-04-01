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
                                     <h4 class="card-title">User Photo</h4>
                                     
                                        <div class="col-md-4">
                                            
                                                <a type="button" class="btn btn-outline-primary block btn-lg" href="{{url('photo_create')}}" >
                                                    Add New
                                                </a>
                                            </div>  
                                         </div>


                         <div class="card-content">
                                <div class="card-body">
                                @if(@Session('delete_msg'))
                                <h5 class="alert alert-success"><i class="fa fa-check"></i> {{Session('delete_msg')}}</h5>
                                @endif
                                @if(@Session('save_msg'))
                                <h5 class="alert alert-success"><i class="fa fa-check"></i> {{Session('save_msg')}}</h5>
                                @endif
                                    <!-- Table with outer spacing -->
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                             
                                                <tr>
                                                    <th>ID</th>
                                                    <th width="250px">Photo</th>
                                                    <th width="150px">Photo Name</th>
                                                    <th width="150px">User</th>
                                                    <th width="200" >Created at</th>




                                                </tr>
                                            </thead>
                                            <tbody>
                                           @if($data)
                                           @foreach($data as $ab)
                                                <tr>
                                                    <th scope="row">{{$ab->id}}</th>
                                                    <td><div class="avatar mr-1 avatar-xl">
                                                             <img src="{{ asset('wp-content/uploads/2015/04/'.$ab->image)}}" alt="avtar img holder">
                                                         </div>
                                                    </td>
                                                    <td>{{$ab->image}}</td>
                                                    <td>{{$ab->user}}</td>
                                                    <td>{{\Carbon\Carbon::parse($ab->created_at)->diffForHumans()}}</td>

                                                    <td>
                                                    
                                                <!-- Delete Modal -->
                                                <button type="button" class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1" data-toggle="modal" data-target="#delete-pro{{$ab->id}}">
                                                <i class="feather icon-trash"></i>
                                                </button>

                                                <!-- Modal -->
                                                <div class="modal fade text-left" id="delete-pro{{$ab->id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <h5>Delete Record</h5>
                                                                <p>Do you really want to delete this record?</p>
                                                                <p>The Recard Number is: {{$ab->id}} </p>
                                                            </div>
                                                            <div class="modal-footer">
                                                              <a type="button" href="{{url('photo_destroy').'/'.$ab->id}}" class="btn btn-primary">Yes</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- End Delete Modal -->

                                        

                                                    </td>
                                                </tr>
                                            @endforeach
                                            @endif   
                                           
                                            </tbody>
                                        </table>
                                        {{$data->links()}}
                                    </div>
                                         
                                                    
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- // Basic Vertical form layout section end -->
                             </div>
                        </div>
                   </div>
            </div>     
            </div>     
        </div>      
    </div>
</div>
@stop