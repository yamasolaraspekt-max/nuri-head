<div class="space-y-2">
    @forelse($appointments as $appt)
        <div class="border-l-4 border-blue-500 bg-white rounded p-2 shadow">
            <div class="text-sm font-medium">{{ $appt->title }}</div>
            <div class="text-xs text-gray-500">
                {{ $appt->start_date }} bis {{ $appt->end_date }}
            </div>
            <div class="text-xs text-gray-600">
                👥 {{ $appt->employees->pluck('name')->join(', ') }}
            </div>
        </div>
    @empty
        <div class="text-sm text-gray-500">Keine Termine gefunden.</div>
    @endforelse
</div>
