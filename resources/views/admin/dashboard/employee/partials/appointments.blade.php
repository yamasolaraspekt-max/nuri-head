<div class="space-y-4" x-data="{ openAppointment: null }">
    @forelse ($appointments as $index => $a)
        <div class="bg-white border-l-4 rounded-lg shadow-md" style="border-color: {{ $a->color ?? '#3b82f6' }}">
            <button 
                @click="openAppointment === {{ $index }} ? openAppointment = null : openAppointment = {{ $index }}"
                class="w-full flex justify-between items-center px-4 py-3 text-left hover:bg-blue-50 transition"
            >
                <div>
                    <div class="text-sm font-semibold text-blue-600">
                        📅 {{ $a->name }}
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        {{ \Carbon\Carbon::parse($a->start_date)->format('d. M') }} – 
                        {{ \Carbon\Carbon::parse($a->end_date)->format('d. M Y') }}
                    </div>
                </div>
                <div class="text-xs text-white px-2 py-0.5 rounded-full bg-{{ $a->priority === 'high' ? 'red' : 'gray' }}-500">
                    {{ ucfirst($a->priority ?? 'normal') }}
                </div>
            </button>

            <div x-show="openAppointment === {{ $index }}" x-collapse class="px-4 pb-4 text-sm text-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    <div>
                        🏷️ <strong>Typ:</strong> {{ $a->appointment_type ?? 'N/V' }}<br>
                        🎯 <strong>Status:</strong> {{ $a->status ?? 'N/V' }}<br>
                        🗺️ <strong>Adresse:</strong><br>
                        {{ $a->street }}, {{ $a->postcode }} {{ $a->city }}
                    </div>
                    <div>
                        🔁 <strong>Wiederholung:</strong> {{ $a->repeat ?? 'Keine' }}<br>
                        🧾 <strong>Notiz:</strong><br>
                        <div class="text-xs italic text-gray-500">{{ $a->note ?? '-' }}</div>
                    </div>
                </div>

                @if($a->customer)
                <div class="mt-3 text-xs text-gray-600">
                    👤 <strong>Kunde:</strong> {{ $a->customer->name }} {{ $a->customer->lastname }}<br>
                    📞 {{ $a->customer->phone }} | ✉️ {{ $a->customer->email }}
                </div>
                @endif

                @if($a->branch)
                <div class="mt-2 text-xs text-gray-600">
                    🏢 <strong>Filiale:</strong> {{ $a->branch->name }}
                </div>
                @endif

                <!-- Mitarbeiter -->
                <div class="mt-4">
                    👥 <strong>Mitarbeiter:</strong>
                    <div class="flex -space-x-2 mt-1">
                        @foreach ($a->employees as $person)
                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                 src="{{ $person->image ? asset('images/employee/' . $person->image) : asset('images/default-avatar.png') }}"
                                 title="{{ $person->name }}"
                                 alt="{{ $person->name }}">
                        @endforeach
                    </div>
                </div>

                <!-- Karte -->
                @if ($a->latitude && $a->longitude)
                    <div class="mt-4">
                        <iframe
                            width="100%"
                            height="200"
                            style="border:0; border-radius: 0.5rem"
                            loading="lazy"
                            allowfullscreen
                            src="https://maps.google.com/maps?q={{ $a->latitude }},{{ $a->longitude }}&hl=de&z=15&output=embed">
                        </iframe>
                    </div>
                @endif

                <!-- Link to Details -->
                <div class="mt-4 text-right">
                    <a href="{{ url('appointment_details/' . $a->id) }}"
                    class="text-xs text-blue-600 bg-blue-100 hover:bg-blue-200 px-3 py-1 rounded">
                    Details anzeigen
                    </a>
                </div>

            </div>
        </div>
    @empty
        <div class="text-center text-gray-400 italic py-6">
            <i data-feather="calendar"></i> Keine Termine gefunden.
        </div>
    @endforelse
</div>

<script>
    document.addEventListener('alpine:init', () => {
        feather.replace();
    });
</script>
