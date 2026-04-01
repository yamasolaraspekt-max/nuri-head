@extends('admin.layouts.app')

@section('title', 'Climate Data Import')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Import Climate Excel File</h4>
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.climate.import.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="file" class="form-label">Excel File (.xlsx)</label>
                    <input
                        type="file"
                        name="file"
                        id="file"
                        class="form-control @error('file') is-invalid @enderror"
                        accept=".xlsx"
                        required
                    >
                    @error('file')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        Upload the climate workbook (Gradtagzahlen-Deutschland.xlsx). The import will run in the background queue.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Queue Climate Import
                </button>
            </form>
        </div>
    </div>
</div>
@endsection