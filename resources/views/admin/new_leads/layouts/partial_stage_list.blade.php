@foreach($stages as $stage)
    <div class="border p-2 mb-2">
        <strong>{{ $stage->stage }}</strong>
        <span class="badge badge-secondary">Sort: {{ $stage->sort_order }}</span>
    </div>
@endforeach
