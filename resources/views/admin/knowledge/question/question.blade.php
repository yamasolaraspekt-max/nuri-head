@extends('admin.layouts.app')
@section('title')
Knowledge Base
@endsection
@section('style')
<style>
    img {
        width:100% !important;
    }
.ql-syntax {
    font-family: "Georgia", serif !important; /* Classic serif font like in newspapers */
    font-size: 18px !important; /* Slightly larger font size for readability */
    line-height: 1.6 !important; /* Comfortable line spacing */
    color: #333 !important; /* Dark gray text for a newspaper feel */
    background-color: #f9f9f9 !important; /* Light gray background for distinction */
    padding: 20px !important; /* Padding for some space around the text */
    margin: 20px auto !important; /* Center it and give it space */
    border-left: 5px solid #ccc !important; /* Add a left border to emphasize the quote */
    border-radius: 5px !important; /* Slightly rounded corners */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1) !important; /* Subtle shadow for a lifted effect */
    white-space: normal !important; /* Wrap the text to fit within the container */
    word-wrap: break-word !important; /* Break long words to prevent overflow */
    overflow: hidden !important; /* Ensure no overflow */
}


</style>
@endsection

@section('content')
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0">Helfe Center</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li> 
                                    <li class="breadcrumb-item"><a href="{{ url('/knowlege') }}">Helfe Center</a>
                                    </li> 
                                    @php
                                        $title = DB::table('knowledge_categories')
                                                ->where('id', request()->id)
                                                ->first();
                                        $name = $title->title;
                                    @endphp 
                                    <li class="breadcrumb-item active">
                                       {{$name}}
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content-header-right text-md-right col-md-3 col-12 d-md-block d-none">
                    <div class="form-group breadcrum-right">
                        <div class="dropdown">
                            <button class="btn-icon btn btn-primary btn-round btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="feather icon-settings"></i></button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ url('question/create/'.request()->id) }}">Neue</a>
                               </div>
                        </div>
                    </div>
                </div>
            </div>
            @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif 
            <div class="content-body" >
                <!-- Knowledge base question Content  -->
                <section id="knowledge-section">
                    <div class="row">
                        <div class="col-lg-3 col-md-5 col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h4>Verwandte Fragen</h4>
                                    <a href="#" class="knowledge-base-question">
                                        <ul class="list-group list-group-flush mt-1">
                                            @foreach($data as $item)
                                            <li class="list-group-item  question-item" data-id="{{ $item->id }}" >
                                                <div class="questions " style="display: flex !important;  justify-content: space-between;">
                                                    <div class="question">
                                                        {{ $loop->index + 1 }}.{{ $item->question }}
                                                    </div>
                                                     @if(DB::table('user_rolls')
                                                        ->where('user_rolls.user_id', '=', auth()->user()->name)
                                                        ->where('user_rolls.item_id', '=', 'Programmer')
                                                        ->where('user_rolls.is_read', '=', 'on')
                                                        ->first())
                                                    <div class="actions">
                                                        <i class="feather icon-trash delete" data-id="{{$item->id}}"></i>
                                                        <a href="{{url('editQuestion/'.$item->id)}}"><i class="feather icon-edit"></i></a>
                                                    </div>
                                                    @endif
                                                </div>
                                            </li> 
                                            @endforeach
                                        </ul>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-9 col-md-7 col-12">
                            <div class="card">
                                <div class="card-body knowledge_details">
                                    <div class="title mb-2">
                                        <h1 id="question-title"></h1>
                                        <p id="last-updated"></p>
                                    </div>
                                    <p id="question-description"> 
                                    </p>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Knowledge base question Content ends -->

            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('.question-item').on('click', function () {
        let questionId = $(this).data('id');

        // Make an AJAX GET request
        $.ajax({
            url: `/getQuestion/${questionId}`,
            method: 'GET',
            success: function (response) {
                // Update card content
                $('#question-title').text(response.question); // Text content
                $('#last-updated').text(`Last updated on ${new Date(response.updated_at).toLocaleDateString()}`);
                $('#question-description').html(response.description); // HTML content
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error); // Debugging
                console.error('XHR Response:', xhr.responseText); // Debugging
                alert('Error fetching question details. Please try again.');
            }
        });
    });
});


</script>


<script>
    $(document).on('click', '.delete', function() {
        var questionId = $(this).data('id');
        if (confirm('Are you sure you want to delete this question?')) {
            $.ajax({
                url: "{{ route('question.destroy', '') }}/" + questionId,
                type: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    alert(response.success);
                    location.reload(); // Reload the page after deletion
                },
                error: function(xhr) {
                    alert('Error deleting the question');
                }
            });
        }
    });
</script>

@endpush