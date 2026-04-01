@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Passwort zurücksetzen</h1>

    @if (session('status'))
        <div class="mb-4 text-green-700 bg-green-100 p-3 rounded">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label class="block text-sm font-medium mb-1">E-Mail</label>
        <input name="email" type="email" value="{{ old('email') }}" required autofocus
               class="w-full border rounded px-3 py-2 @error('email') border-red-500 @enderror">

        @error('email')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror

        <button type="submit"
                class="mt-4 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Link zum Zurücksetzen senden
        </button>
    </form>
</div>
@endsection