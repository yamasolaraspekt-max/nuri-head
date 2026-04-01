{{-- Steps (keys) --}}
@if($keys->count())
    <table class="tp-keys-table">
        <thead>
        <tr>
            <th style="width:40px;">#</th>
            <th>Schritt</th>
            <th>Zugewiesen</th>
            <th>Plan / Ist</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($keys as $index => $key)
            @php
                // Normalize employee_id into a clean numeric array
                $assignedIds = [];

                if (is_array($key->employee_id)) {
                    $assignedIds = $key->employee_id;
                } elseif (is_string($key->employee_id) && $key->employee_id !== '') {
                    $assignedIds = json_decode($key->employee_id, true) ?: [];
                }

                $assignedIds = array_values(
                    array_unique(
                        array_filter(array_map('intval', (array) $assignedIds))
                    )
                );

                $assignedEmps = $assignedIds
                    ? \App\Models\Employee::whereIn('id', $assignedIds)->get()
                    : collect();

                $isCompleted  = (int) $key->is_completed === 1;
            @endphp
            <tr class="js-key-toggle-row"
                data-key-id="{{ $key->id }}"
                data-completed="{{ $isCompleted ? '1' : '0' }}"
                data-employee-ids='@json($assignedIds)'>
                <td>
                    <button type="button"
                            class="btn btn-sm {{ $isCompleted ? 'btn-success' : 'btn-outline-secondary' }} js-key-toggle"
                            title="{{ $isCompleted ? 'Als nicht erledigt markieren' : 'Als erledigt markieren' }}">
                        <i data-feather="{{ $isCompleted ? 'check-square' : 'square' }}"
                           style="width:14px;height:14px;"></i>
                    </button>
                </td>
                <td>
                    <div class="{{ $isCompleted ? 'tp-key-done' : '' }}">
                        <strong>{{ $key->task ?? 'Ohne Titel' }}</strong><br>
                        <span style="font-size:.76rem;color:#6b7280;">
                            {{ $key->key_description }}
                        </span>
                    </div>
                </td>
                <td>
                    @if($assignedEmps->count())
                        @foreach($assignedEmps as $emp)
                            <div class="tp-avatar-ring"
                                 title="{{ $emp->name }} {{ $emp->lastname }}">
                                @if($emp->image)
                                    <img src="{{ asset('images/employee/'.$emp->image) }}"
                                         style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ mb_substr($emp->name,0,1) }}{{ mb_substr($emp->lastname,0,1) }}
                                @endif
                            </div>
                        @endforeach
                    @else
                        <span style="font-size:.75rem;color:#9ca3af;">Kein Mitarbeiter</span>
                    @endif
                </td>
                <td style="font-size:.76rem;">
                    <div>
                        <strong>Plan:</strong>
                        {{ $key->duration ?? '—' }} Std.
                    </div>
                    <div>
                        <strong>Ist:</strong>
                        @if($key->total_time)
                            {{ $key->total_time }} Std.
                        @else
                            —
                        @endif
                    </div>
                </td>
                <td style="font-size:.76rem;">
                    @if($isCompleted)
                        <span class="tp-badge-status">
                            <i data-feather="check-circle" style="width:12px;height:12px;"></i>
                            erledigt
                        </span>
                        @if($key->done_date)
                            <div style="color:#6b7280;">
                                {{ \Carbon\Carbon::parse($key->done_date)->format('d.m.Y') }}
                            </div>
                        @endif
                        @if($key->reason)
                            <div style="color:#9ca3af;">
                                {{ \Illuminate\Support\Str::limit($key->reason, 60) }}
                            </div>
                        @endif
                    @else
                        <span class="tp-badge-status" style="background:#fee2e2;color:#991b1b;">
                            <i data-feather="clock" style="width:12px;height:12px;"></i>
                            offen
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div style="font-size:.8rem;color:#9ca3af;">Keine Aufgabenschritte definiert.</div>
@endif
