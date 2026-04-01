@extends('admin.layouts.app')
@section('title') Task Phases @stop
@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title">Phase Activities and Steps</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('task_phase_details')}}">Phase</a></li>
                        <li class="breadcrumb-item active">{{ $title->phase_name }}</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="content-body">
            <div class="row">
                <div class="col-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">{{ $title->phase_name }}</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">Product</th>
                                                <th scope="col">Phase</th>
                                                <th scope="col">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sortable">
                                            @foreach($data as $item)
                                            <tr data-id="{{ $item->id }}">
                                                <th scope="row">{{ $item->id }}</th>
                                                <td>{{ $item->article_group }}</td>
                                                <td>{{ $item->phase_name }}</td>
                                                <td>Drag to reorder</td>
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
@stop

@section('script')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(function() {
        $("#sortable").sortable({
            update: function(event, ui) {
                var order = $(this).sortable('toArray', { attribute: 'data-id' });
                $.ajax({
                    url: '{{ route("task.phase.updateOrder") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order: order
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            toastr.success("Order updated successfully");
                        }
                    }
                });
            }
        });
    });
</script>
@endsection
