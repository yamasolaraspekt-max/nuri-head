<div class="space-y-4" x-data="{ open: '' }">
    @php
        $sections = [
            'tasks' => [
                'data' => $tasks ?? [],
                'title' => 'Aufgaben',
                'color' => 'blue',
                'icon' => 'check-square',
                'route' => 'personal_task_details',
            ],
            'appointments' => [
                'data' => $appointments ?? [],
                'title' => 'Termine',
                'color' => 'green',
                'icon' => 'calendar',
                'route' => 'appointment_details',
            ],
            'problems' => [
                'data' => $problems ?? [],
                'title' => 'Probleme',
                'color' => 'red',
                'icon' => 'alert-triangle',
                'route' => 'problem/profile',
            ],
        ];

    @endphp


    @foreach ($sections as $key => $section)
        @php
            $first = collect($section['data'])->first();
            $last = collect($section['data'])->last();
            $startDate = $first['start_date'] ?? $first['date'] ?? $first['updated_at'] ?? now();
            $endDate = $last['end_date'] ?? $last['date'] ?? $last['updated_at'] ?? now();
        @endphp

        <div class="border rounded-xl  bg-white">
            <button @click="open === '{{ $key }}' ? open = '' : open = '{{ $key }}'"
                class="w-full flex justify-between items-center px-4 py-1 bg-{{ $section['color'] }}-50 hover:bg-{{ $section['color'] }}-100 transition-all">
                <div class="flex items-center gap-2 font-semibold text-{{ $section['color'] }}-600">
                    <i data-feather="{{ $section['icon'] }}"></i>
                    {{ $section['title'] }} ({{ count($section['data']) }})
                </div>
                <div class="text-sm text-gray-600">
                    @if(count($section['data']) > 0)
                        <i class="feather icon-calendar "></i> {{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
                    @else
                        Keine Daten
                    @endif
                </div>
            </button>

            <div x-show="open === '{{ $key }}'" x-collapse class="p-4 grid grid-cols-3 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($section['data'] as $item)
                    <div class="border p-1 rounded-md   bg-white text-sm relative" x-data="{ openModal: false }">
                        @if ($key === 'tasks')
                            <div class="font-semibold text-blue flex items-center justify-between">
                                <span>{{ $item['task_title'] }}</span>
 
                                @if (!empty($item['id']))
                                    <a class="text-xs bg-blue-100 text-blue px-2 py-0.5 rounded hover:bg-blue-200"
                                    href="{{ url('personal_task_details/'.$item['id']) }}">Details</a>
                                @else
                                    <span class="text-xs text-gray-400">Keine ID</span>
                                @endif

                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span><i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item['start_date'])->format('d. M') }} – {{ \Carbon\Carbon::parse($item['due_date'])->format('d. M Y') }}</span>
                                <span class="bg-blue-500 text-white px-2 rounded-full">
                                    {{ $item['priority'] == 'high' ? 'Hoch' : ($item['priority'] == 'normal' ? 'Normal' : 'Niedrig') }}
                                </span>
                            </div>

                            @if (!empty($item['employees']))
                                <div class="text-xs text-gray-500 mt-1 mb-1">Zugewiesen:</div>
                                <div class="flex -space-x-2 overflow-hidden mb-2">
                                    @foreach ($item['employees'] as $person)
                                        <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                            src="{{ $person['photo'] }}"
                                            title="{{ $person['name'] }}"
                                            alt="{{ $person['name'] }}">
                                    @endforeach
                                </div>
                            @endif

                            <div class="text-right text-xs mt-1 text-blue font-medium">
                                {{ $item['status'] != 'completed' ? 'Ausstehend'  : 'Vollendet'}}
                            </div> 
 
                            
                            @elseif ($key === 'appointments')
                                <div class="font-semibold text-green-600 flex justify-between items-center">
                                    <span>{{ $item['name'] ?? '-' }}</span>
                                    <span class="w-3 h-3 rounded-full inline-block" style="background-color: {{ $item['color'] ?? '#3b82f6' }}"></span>
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="feather icon-calendar"></i> {{ \Carbon\Carbon::parse($item['date'] ?? now())->format('d. M Y') }} – 
                                    <i class="feather icon-clock"></i> {{ $item['time'] ? \Carbon\Carbon::parse($item['time'])->format('H:i') : '-' }}
                                </div>

                                @if (!empty($item['employees']))
                                    <div class="text-xs text-gray-500 mt-2"><i class="feather icon-user"></i> <strong>Mitarbeiter:</strong></div>
                                    <div class="flex -space-x-2 mt-1 mb-2">
                                        @foreach ($item['employees'] as $person)
                                            <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                                src="{{ $person['photo'] }}"
                                                title="{{ $person['name'] }}"
                                                alt="{{ $person['name'] }}">
                                        @endforeach
                                    </div>
                                @endif

                                @if (!empty($item['address']))
                                    <div class="text-xs text-gray-500 mt-2">
                                        <i class="feather icon-map-pin"></i> <strong>Adresse:</strong>
                                        <a href="https://www.google.com/maps?q={{ $item['latitude'] }},{{ $item['longitude'] }}"
                                        target="_blank"
                                        class="text-blue hover:underline">
                                        {{ $item['address'] }}
                                        </a>
                                    </div>
                                @endif


                                <!-- ✅ Status + Button aligned -->
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-xs bg-green-500 text-white px-2 py-0.5 rounded-full">
                                        {{ $item['status'] ?? 'Offen' }}
                                    </span>
                                    @if (!empty($item['id']))
                                        <a href="{{ url($section['route'] . '/' . $item['id']) }}">Details</a>
                                    @else
                                        <span class="text-xs text-gray-400">Keine ID</span>
                                    @endif

                                </div>


                            @elseif ($key === 'problems')
                                    <div class="font-semibold text-red-600 flex justify-between items-center">
                                        <span>Ticket: #{{ $item['ticket_no'] }}</span>
                                        <span class="text-xs bg-red-100 text-red-600 px-2 rounded">
                                            {{ $item['priority'] ?? 'Normal' }}
                                        </span>
                                    </div>

                                    <div class="text-xs text-gray-500 mt-1"><i class="feather icon-setting"></i> {{ $item['article'] ?? '-' }}</div>

                                    @if (!empty($item['employees']))
                                        <div class="text-xs text-gray-500 mt-2"><i class="feather icon-user"></i> <strong>Mitarbeiter:</strong></div>
                                        <div class="flex -space-x-2 mt-1 mb-2">
                                            @foreach ($item['employees'] as $person)
                                                <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white"
                                                    src="{{ $person['photo'] }}"
                                                    title="{{ $person['name'] }}"
                                                    alt="{{ $person['name'] }}">
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex justify-between items-center mt-2">
                                        <span class="text-xs bg-red-500 text-white px-2 py-0.5 rounded-full">
                                            {{ $item['status'] ?? 'Offen' }}
                                        </span>
                                        @if (!empty($item['id']))
                                            <a href="{{ url($section['route'] . '/' . $item['id']) }}">Details</a>
                                        @else
                                            <span class="text-xs text-gray-400">Keine ID</span>
                                        @endif

                                    </div>
                                @endif
 
                          



                    </div>
                @empty
                    <div class="col-span-full text-center text-gray-400 italic text-sm py-4">
                        <i data-feather="inbox"></i> Keine {{ $section['title'] }} gefunden.
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach

</div>

<script>
    document.addEventListener('alpine:init', () => {
        feather.replace();
    });
</script>
