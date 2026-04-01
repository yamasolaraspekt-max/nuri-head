@extends('admin.layouts.app')

@section('title') To do's @endsection

@section('style')  
<link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-todo.css') }}">
<link rel="stylesheet" href="{{ asset('app-assets/css/pages/app-todo.min.css') }}">
<style>
    .app-content {
    padding: 20px;
}

.sidebar-left {
    border-right: 1px solid #ddd;
}

.todo-item {
    cursor: pointer;
}

.todo-item:hover {
    background-color: #f8f9fa;
}

.no-results {
    text-align: center;
    padding: 20px;
    color: #999;
}

</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
            <div class="content-wrapper">
                <div class="content-header row">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">ANFRAGELISTE</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/employee_dashboard') }}">Home</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
                <div class="content-body">  
                        <div class="row content-area-wrapper">
                            <div class="col-md-3 sidebar-left">
                                <div class="card sidebar">
                                    <div class="card-body todo-sidebar d-flex flex-column">
                                        <button type="button" class="btn btn-primary btn-block mb-4" data-toggle="modal" data-target="#addTaskModal">Add Task</button>
                                        <div class="sidebar-menu-list">
                                            <div class="list-group">
                                                <a href="#" class="list-group-item list-group-item-action active">
                                                    <i class="feather icon-mail mr-2"></i> All
                                                </a>
                                            </div>
                                            <hr>
                                            <h5>Filters</h5>
                                            <div class="list-group">
                                                <a href="#" class="list-group-item list-group-item-action">
                                                    <i class="feather icon-star mr-2"></i> Starred
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action">
                                                    <i class="feather icon-info mr-2"></i> Important
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action">
                                                    <i class="feather icon-check mr-2"></i> Completed
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action">
                                                    <i class="feather icon-trash mr-2"></i> Trashed
                                                </a>
                                            </div>
                                            <hr>
                                            <h5>Labels</h5>
                                            <div class="list-group">
                                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <span class="badge badge-primary mr-2"></span> Frontend
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <span class="badge badge-warning mr-2"></span> Backend
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <span class="badge badge-success mr-2"></span> Doc
                                                </a>
                                                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <span class="badge badge-danger mr-2"></span> Bug
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9 content-right">
                                <div class="card content-wrapper">
                                    <div class="card-body">
                                        <div class="todo-app-list-wrapper">
                                            <div class="input-group mb-4">
                                                <input type="text" class="form-control" id="todo-search" placeholder="Search..">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i class="feather icon-search"></i></span>
                                                </div>
                                            </div>
                                            <div class="todo-task-list list-group">
                                                <ul class="list-group list-group-flush">
                                                    <!-- Example Task Item -->
                                                    <li class="list-group-item">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="d-flex align-items-center">
                                                                <input type="checkbox" class="mr-3">
                                                                <h6 class="mb-0">Fix Responsiveness 💻</h6>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge badge-primary">Frontend</span>
                                                                <a href="#" class="ml-2"><i class="feather icon-info"></i></a>
                                                                <a href="#" class="ml-2"><i class="feather icon-star"></i></a>
                                                                <a href="#" class="ml-2"><i class="feather icon-trash"></i></a>
                                                            </div>
                                                        </div>
                                                        <p class="mb-0 mt-2 text-muted">Jelly topping toffee bear claw. Sesame snaps lollipop macaroon croissant cheesecake pastry cupcake.</p>
                                                    </li>
                                                    <!-- Add more tasks similarly -->
                                                </ul>
                                                <div class="no-results d-none">
                                                    <h5>No Items Found</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal for Add Task -->
                    <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-sm" role="document">
                            <div class="modal-content">
                                <form id="form-add-todo" class="todo-input">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Add Task</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <input type="text" class="form-control" placeholder="Title">
                                        </div>
                                        <div class="form-group">
                                            <textarea class="form-control" rows="3" placeholder="Add description"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Add Task</button>
                                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> 
            </div>
</div>

@endsection

@section('script')
    <script src="{{ asset('app-assets/js/scripts/pages/app-todo.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
@endsection
