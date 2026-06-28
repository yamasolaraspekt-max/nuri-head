@extends('admin.layouts.app')
@section('title')
Knowledge Base 
@endsection

@section('style') 
<style>
    .knowledge-base-bg {
  background: url({{ asset('app-assets/images/pages/knowledge-base-cover.jpg') }}) no-repeat;
  background-size: cover; }

.knowledge-base-category .list-group-item, .knowledge-base-question .list-group-item {
  padding: 0.5rem 0; }
  .knowledge-base-category .list-group-item:hover, .knowledge-base-question .list-group-item:hover {
    background-color: transparent; }

.article-question li {
  margin-bottom: 0.5rem;
  display: flex; }

.article-question i {
  font-size: 1.2rem;
  top: 2px;
  position: relative;
  margin: 0 0.5rem; }

@media only screen and (min-width: 992px) {
  .knowledge-base-bg .card-body {
    padding: 8rem !important; } }

@media only screen and (min-width: 768px) and (max-width: 991px) {
  .knowledge-base-bg .card-body {
    padding: 6rem !important; } }

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
                            <h2 class="content-header-title float-left mb-0">HELFE CENTER</h2>
                            <div class="breadcrumb-wrapper col-12">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a>
                                    </li> 
                                    <li class="breadcrumb-item active">Helfe Center
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
                                <a class="dropdown-item" data-toggle="modal" data-target="#primary">New Section</a> 
                            </div>
                                <div class="modal fade text-left" id="primary" tabindex="-1" role="dialog" aria-labelledby="myModalLabel160" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary white">
                                                <h5 class="modal-title" id="myModalLabel160">Knowledge Base</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">×</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('knowledge.store') }}" method="post" enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                <div class="card-body">
                                                        <form class="needs-validation" novalidate="">
                                                            <div class="form-row">
                                                                <div class="col-md-12 col-12 mb-1">
                                                                    <label for="title">Title</label>
                                                                    <input type="text" class="form-control" name="title" placeholder="" value="Mark" required=""> 
                                                                       

                                                                </div>
                                                                <div class="col-md-12 col-12 mb-1">
                                                                    <label for="title">Description</label>
                                                                   <textarea name="description" id="" cols="30" rows="10" class="form-control"></textarea>
                                                        

                                                                </div>

                                                                <div class="col-md-12 col-12 mb-1">
                                                                    <label for="title">ICON</label>
                                                                     <input type="file" name="icon" id="" class="form-control"> 
                                                                </div> 
                                                            </div> 
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary waves-effect waves-light" >Accept</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Knowledge base Jumbotron -->
                <section id="knowledge-base-search">
                    <div class="row">
                        <div class="col-12">
                            <div class="card knowledge-base-bg white">
                                <div class="card-content">
                                    <div class="card-body p-sm-4 p-2">
                                        <h1 class="white">Dedizierte Quelle, die in der Anwendung verwendet wird</h1>
                                        <p class="card-text mb-2">
                                            Ausführliches Handbuch und Hilfecenter von Solar Aspekt
                                        </p>
                                        <form id="search_form">
                                            @csrf
                                            <fieldset class="form-group position-relative has-icon-left mb-0">
                                                <input type="text" class="form-control form-control-lg" id="searchbar" name="search" placeholder="Search Topic or Keyword">
                                                <div class="form-control-position">
                                                    <i class="feather icon-search px-1"></i>
                                                </div>
                                            </fieldset>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Knowledge base Jumbotron ends -->
                <!-- Knowledge base -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif 
                <section id="knowledge_section">
                    <div class="row search-content-info">
                        @foreach($data as $item)
                        <div class="col-md-4 col-sm-6 col-12 search-content">
                            <div class="card"> 
                            @if(auth()->user()->hasPermission('Programmer', 'read'))
                                <div>
                                    <div class="btn-group dropdown dropdown-icon-wrapper mr-1 mb-1"> 
                                        <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-menu dropdown-icon"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            
                                           <span class="dropdown-item">
                                                <i class="feather icon-trash delete" data-id="{{$item->id}}"></i>
                                            </span>
                                            <span class="dropdown-item">
                                                <i class="feather icon-edit edit" data-id="{{$item->id}}" data-title="{{$item->title}}" data-description="{{$item->description}}" data-icon="{{$item->icon}}"></i>
                                            </span>

                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="card-body text-center">
                                    <a href="{{ url('question/'.$item->id) }}">
                                        <img src="{{ asset('images/knowledge/'.$item->icon ) }}" class="mx-auto mb-2" width="180" alt="knowledge-base-image">
                                        <h4>{{ strtoupper($item->title) }}</h4>

                                        <small class="text-dark">{{ $item->description }}</small>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach 
                    </div>
                </section>

                <section id="search_section" style="display: none;">
                    <div class="row">
                        <div class="col-md-12 col-12 order-2 order-md-1">
                            <div class="card">
                                <div class="card-content">
                                    <!-- Search Results -->
                                    <div id="search-results" class="card-body pb-0">
                                        <ul id="search-list" class="media-list p-0">
                                            <!-- Dynamic search results will be inserted here -->
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Search Pagination -->
                            <div class="search-pagination">
                                <ul id="pagination" class="pagination pagination-separate pagination-round pagination-flat justify-content-center">
                                    <!-- Dynamic pagination links will be inserted here -->
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- Knowledge base ends -->


                    <!-- Edit Modal -->
                    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form id="editForm" action="{{ route('knowledge.update', ':id') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="card-body">
                                            <div class="form-row">
                                                <div class="col-md-12 col-12 mb-1">
                                                    <label for="title">Title</label>
                                                    <input type="text" class="form-control" name="title" id="editTitle" required="">
                                                </div>
                                                <div class="col-md-12 col-12 mb-1">
                                                    <label for="description">Description</label>
                                                    <textarea name="description" id="editDescription" cols="30" rows="10" class="form-control"></textarea>
                                                </div>
                                                <div class="col-md-12 col-12 mb-1">
                                                    <label for="icon">ICON</label>
                                                    <input type="file" name="icon" id="editIcon" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">Save Changes</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade text-left" id="search_result" tabindex="-1" role="dialog" aria-labelledby="myModalLabel16" aria-hidden="true" style="display: none;">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel16">Search Result</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                              
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-primary waves-effect waves-light" data-dismiss="modal">Accept</button>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
    // Handle delete action
    $(document).on('click', '.delete', function () {
        let recordId = $(this).data('id');
        if (confirm('Are you sure you want to delete this record?')) {
            $.ajax({
                url: `/knowledge/${recordId}`, // Adjust the route to your delete endpoint
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    alert(response.message); // Display success message
                    location.reload(); // Reload the page or remove the row dynamically
                },
                error: function (xhr) {
                    alert('Error deleting record. Please try again.');
                }
            });
        }
    });

    // Handle edit action
    $(document).on('click', '.edit', function () {
        let recordId = $(this).data('id');
        let title = $(this).data('title');
        let description = $(this).data('description');

        // Populate the modal fields
        $('#editTitle').val(title);
        $('#editDescription').val(description);

        // Update form action
        let updateUrl = $('#editForm').attr('action').replace(':id', recordId);
        $('#editForm').attr('action', updateUrl);

        // Show the modal
        $('#editModal').modal('show');
    });
});

</script>   

<script>
    $(document).ready(function () {
        let timer = null;

        $('#searchbar').on('keyup', function () {
            clearTimeout(timer); // Clear the timer to delay the AJAX request

            let query = $(this).val();

            // Delay the search to avoid too many requests
            timer = setTimeout(() => {
                if (query.trim() !== '') {
                    // Hide knowledge_section and show search_section
                    $('#knowledge_section').hide();
                    $('#search_section').show();

                    // AJAX request
                    $.ajax({
                        url: '{{ route("search.question") }}',
                        method: 'GET',
                        data: { search: query },
                        success: function (response) {
                            // Clear previous results
                            $('#search-list').empty();

                            // Function to truncate text
                            function truncateText(text, maxLength) {
                                if (text.length > maxLength) {
                                    return text.substring(0, maxLength) + '...';
                                }
                                return text;
                            }

                            // Display search results
                            response.data.forEach((item, index) => {
                                const truncatedDescription = truncateText($(item.description).text(), 50); // Text only for truncation

                                $('#search-list').append(`
                                    <li class="media d-sm-flex d-block">
                                        <div class="media-body pl-sm-50 pl-0">
                                            <a href="javascript:void(0);" 
                                               class="search-title" 
                                               data-question="${item.question}" 
                                               data-index="${index}" 
                                               data-toggle="modal" 
                                               data-target="#search_result">
                                                <h3 class="text-bold-400 mb-0">
                                                    ${item.question}
                                                </h3> 
                                            </a>
                                            <p>${truncatedDescription}</p>
                                            <div class="hidden-description" id="description-${index}" style="display:none;">
                                                ${item.description}
                                            </div>
                                        </div>
                                    </li>
                                `);
                            });

                            // Attach click event to dynamically generated titles
                            $('.search-title').on('click', function () {
                                const question = $(this).data('question');
                                const index = $(this).data('index');
                                const description = $(`#description-${index}`).html();

                                $('#search_result .modal-body').html(`
                                    <h4>${question}</h4>
                                    <p>${description}</p>
                                `);
                            });
                        },
                        error: function () {
                            alert('Error fetching search results. Please try again.');
                        }
                    });
                } else {
                    // Show knowledge_section and hide search_section when the input is empty
                    $('#knowledge_section').show();
                    $('#search_section').hide();
                }
            }, 300); // Delay of 300ms
        });
    });
</script>





@endsection