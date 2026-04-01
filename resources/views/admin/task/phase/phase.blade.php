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
                                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">HOME</a>
                                        </li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                          
            <div class="content-body">
                <section id="basic-horizontal-layouts">
                    <div class="row match-height">
                            <div class="col-md-12 col-12">
                                <div class="row" id="table-hover-animation">
                                    <div class="col-12">
                                        <div class="card"  > 
                                            <div class="card-content">
                                                <div class="card-body">   
                                                    <div class="col-8 mb-2">
                                                        <form action="{{action('App\Http\Controllers\TaskPhaseController@index')}}">
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
                                                    <div class="col-md-12">
                                                         <div class="row">
                                                            @foreach ($articles as $art) 
                                                            <div class="col-xl-2 col-md-4 col-sm-6">
                                                                <div class="card text-center">
                                                                    <div class="card-content">
                                                                        <a href="{{ url('task_phase_details/'.$art->id) }}">
                                                                        <div class="card-body cards" >
                                                                            <div class="avatar bg-rgba-info p-50 m-0 mb-1">
                                                                                <div class="avatar-content">
                                                                                    <img src="{{ asset('images/articles/'.$art->image)  }}" alt="" style="width: 37px;">
                                                                                </div>
                                                                            </div>
                                                                            <h6 class="text-bold-500">{{$art->article_group}}</h6> 
                                                                        </div>
                                                                        </a>
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
                                {{$taskPhases->links()}}
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