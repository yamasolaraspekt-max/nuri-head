@extends('admin.layouts.guest')

@section('title', 'Shared AI Chat')

@section('content')
<div class="max-w-4xl mx-auto py-8">
  <h1 class="text-2xl font-semibold mb-4">
    Shared AI Chat — Customer #{{ $chat->customer_id }}
  </h1>

  <div class="space-y-3">
    @foreach($chat->messages as $m)
      <div class="rounded-2xl px-4 py-3 shadow-sm 
                  {{ $m->role==='user' ? 'bg-slate-900 text-white' : 'bg-white border' }}">
        <div class="text-xs opacity-70">{{ $m->role }}</div>
        <div class="prose prose-sm max-w-none">
          {!! nl2br(e($m->content)) !!}
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection
