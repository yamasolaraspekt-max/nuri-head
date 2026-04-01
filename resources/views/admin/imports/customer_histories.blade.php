@extends('admin.layouts.app')

@section('title', 'Import: Customer Histories')

@section('content')
<div class="max-w-4xl mx-auto p-6">
  @if(session('ok'))
    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 text-emerald-800 px-3 py-2">
      {{ session('ok') }}
    </div>
  @endif

  @if($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 text-red-800 px-3 py-2">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
      </ul>
    </div>
  @endif

  <h1 class="text-2xl font-bold mb-4">Import: <span class="font-mono">customer_histories</span></h1>
  <p class="text-slate-600 mb-4">
    Upload a <code>.sql</code> dump like <span class="font-mono">INSERT INTO customer_histories (...) VALUES (...);</span><br>
    The importer will auto-add missing columns (<span class="font-mono">done_reason, plan_time, is_time, d_time</span>) as <em>NULL</em> and fix JSON in <span class="font-mono">has_document</span> and <span class="font-mono">done_history</span>.
  </p>

  <form action="{{ route('admin.imports.customer_histories.store') }}" method="post" enctype="multipart/form-data" class="space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium">SQL file (.sql, .txt)</label>
      <input type="file" name="sql_file" accept=".sql,.txt"
             class="mt-1 block w-full rounded border px-3 py-2" />
      <p class="text-xs text-slate-500 mt-1">Max 20 MB. Alternatively, paste below.</p>
    </div>

    <div>
      <label class="block text-sm font-medium">Or paste SQL here</label>
      <textarea name="sql_text" rows="10" class="mt-1 block w-full rounded border px-3 py-2 font-mono"
        placeholder="INSERT INTO `customer_histories` (`id`,...) VALUES ( ... ),( ... );">{{ old('sql_text') }}</textarea>
    </div>

    <div class="flex items-center gap-6">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="dry_run" value="1" class="rounded border" />
        <span>Dry run (parse & validate only)</span>
      </label>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="upsert" value="1" class="rounded border" checked />
        <span>Upsert on <code>id</code> (update existing)</span>
      </label>
      <label class="inline-flex items-center gap-2">
            <span class="text-sm">Batch size</span>
            <input type="number" name="batch_size" value="1000" min="100" max="5000"
                    class="w-24 rounded border px-2 py-1" />
            </label>
            <p class="text-xs text-slate-500">
            Batch size = how many **rows per DB write**. This is NOT your SQL content.
            </p>
    </div>

    <div class="flex gap-3">
      <button class="px-4 py-2 rounded bg-ink text-white hover:opacity-90">Run Import</button>
      <a href="{{ route('admin.imports.customer_histories.create') }}" class="px-4 py-2 rounded border">Reset</a>
    </div>
  </form>

  @if(session('sample'))
    <div class="mt-8">
      <h2 class="text-lg font-semibold mb-2">Sample (first 3 normalized rows)</h2>
      <pre class="text-sm bg-slate-50 border rounded p-3 overflow-x-auto">{{ json_encode(session('sample'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    </div>
  @endif
</div>
@endsection