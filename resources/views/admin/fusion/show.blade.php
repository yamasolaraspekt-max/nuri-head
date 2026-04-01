@extends('admin.layouts.app')
@section('title') VIEW LEAD #{{ $submission->id }} @stop

@section('style')
    <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <h2 class="content-header-title float-left mb-0">WEBSITE LEAD #{{ $submission->id }}</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">HOME</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('fusion.forms.index') }}">WEBSITE LEADS</a></li>
                        <li class="breadcrumb-item active">VIEW</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Lead Details</h4>
                </div>
                <div class="card-body">
                    <p><strong>Submitted At:</strong> {{ $submission->created_at }}</p>
                    <pre>{{ json_encode($submission->form_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    <a href="{{ route('fusion.forms.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script') 
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
@endsection
