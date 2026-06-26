<div class="space-y-4" x-data="{ openTask: null }">
    @forelse ($tasks as $index => $task)
        <div class="bg-white border rounded-lg shadow-md">
            <!-- Header Button -->
            <button 
                @click="openTask === {{ $index }} ? openTask = null : openTask = {{ $index }}"
                class="w-full flex justify-between items-center px-4 py-3 text-left hover:bg-blue-50 transition"
            >
                <div class="text-sm font-semibold text-blue-600">
                    <i data-feather="check-circle" class="inline w-4 h-4 mr-1"></i>
                    {{ $task['task_title'] }}
                </div>
                <div class="text-xs text-gray-500">
                    📅 {{ \Carbon\Carbon::parse($task['start_date'])->format('d. M') }} – {{ \Carbon\Carbon::parse($task['due_date'])->format('d. M Y') }}
                </div>
            </button>

            <!-- Collapsible Content -->
            <div x-show="openTask === {{ $index }}" x-collapse class="px-4 pb-4 text-sm text-gray-700">
                <div class="mt-2 space-y-1">
                    👥 <strong>Mitarbeiter:</strong> {{ $task['employee_count'] }}
                    <div class="flex -space-x-2 mt-1">
                        @foreach ($task['employees'] as $person)
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                 src="{{ $person['photo'] }}"
                                 title="{{ $person['name'] }}"
                                 alt="{{ $person['name'] }}">
                        @endforeach
                    </div>
                    🎯 <strong>Status:</strong> 
                    {{ $task['status'] === 'completed' ? 'Abgeschlossen' : 'Offen' }}<br>
                    ⚡ <strong>Priorität:</strong> 
                    {{ $task['priority'] === 'high' ? 'Hoch' : ($task['priority'] === 'normal' ? 'Normal' : 'Niedrig') }}
                </div>

                <!-- Task Keys -->
                <div class="mt-4">
                    <strong class="block mb-1">🧩 Teilaufgaben</strong>
                    @forelse ($task['task_keys'] as $key)
                        <div class="border-t pt-2 text-xs text-gray-600">
                            <div><strong>Aufgabe:</strong> {{ $key->task }}</div>
                            <div>⏱️ {{ $key->duration ?? 'N/V' }} Stunden</div>
                            <div>📌 {{ $key->status ?? 'Kein Status' }}</div>
                            <div>👤 {{ $key->doneBy->name ?? 'Unbekannt' }}</div>
                            <div>📅 {{ \Carbon\Carbon::parse($key->done_date ?? now())->format('d. M Y') }}</div>
                        </div>
                    @empty
                        <div class="italic text-gray-400">Keine Teilaufgaben vorhanden.</div>
                    @endforelse
                </div>

                <!-- Link to Details -->
                <div class="mt-4 text-right">
                    <a href="{{ url('personal_task_details/'.$task['id']) }}"
                       class="text-xs text-blue-600 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded">
                       Details anzeigen
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-400 italic py-6">
            <i data-feather="inbox"></i> Keine Aufgaben gefunden.
        </div>
    @endforelse
</div>

<script>
    document.addEventListener('alpine:init', () => {
        feather.replace();
    });
</script>
