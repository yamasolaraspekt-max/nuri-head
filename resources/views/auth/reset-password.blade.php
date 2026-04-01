{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-xl font-semibold mb-4">Neues Passwort setzen</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-700 bg-red-100 p-3 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ old('email', $email) }}">

        <label class="block text-sm font-medium mb-1">Neues Passwort</label>
        <input name="password" type="password" required
               class="w-full border rounded px-3 py-2">

        <label class="block text-sm font-medium mb-1 mt-3">Passwort bestätigen</label>
        <input name="password_confirmation" type="password" required
               class="w-full border rounded px-3 py-2">

        <button type="submit"
                class="mt-4 w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
            Passwort speichern
        </button>
    </form>
</div>
@endsection
