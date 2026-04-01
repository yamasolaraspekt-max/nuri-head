@extends('admin.layouts.app')
@section('content')

<h1>Upload Weather Stations CSV</h1>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if($errors->any())
        <ul style="color: red;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('weather_stations.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="csv_file">Choose CSV File:</label>
        <input type="file" name="csv_file" id="csv_file" required>
        <button type="submit">Upload</button>
    </form>
@endsection