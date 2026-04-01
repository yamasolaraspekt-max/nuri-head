<div class="card-content">
    <div class="table-responsive mt-1">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th colspan="3">Aufgabe</th>
                    <th>Verfasser</th>
                    <th>Zugewiesen</th>
                    <th>Erinnerung</th>
                    <th>Wiederholung</th>
                    <th>Status</th>
                    <th>Aktion</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $repeatMap = [
                        'minute'    => 'Minütlich',
                        'hourly'    => 'Stündlich',
                        'daily'     => 'Täglich',
                        'weekly'    => 'Wöchentlich',
                        'monthly'   => 'Monatlich',
                        'quarterly' => 'Vierteljährlich',
                        'yearly'    => 'Jährlich',
                    ];

                    $progressStatusMap = [
                        'new'        => 'Neu',
                        'start'      => 'Starten',
                        'on_going'   => 'Im Prozess',
                        'on_review'  => 'Kurz vor Abschluss',
                        'pending'    => 'Ausstehend',
                        'completed'  => 'Vollendet',
                        'pause'      => 'Pause',
                        'cancel'     => 'Abbrechen',
                        'reviewing'  => 'Überprüfung',
                    ];

                    $currentEmployeeId = auth()->user()->name;   // employee_id in user()->name
                    $isRejectedTab     = isset($data_type) && $data_type === 'rejected';
                @endphp

                @foreach ($data as $item)
                    @php
                        // Fortschritt der Aufgabenschritte
                        $taskKeys = DB::table('personal_task_keys')
                            ->selectRaw('
                                SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_count,
                                COUNT(*) as total_count
                            ')
                            ->where('personal_task_id', $item->id)
                            ->first();

                        $check_if_completed = $taskKeys
                            && $taskKeys->total_count > 0
                            && $taskKeys->completed_count == $taskKeys->total_count;

                        // Glocke → wenn irgendein Mitarbeiter zugeordnet ist
                        $isOutdated = DB::table('employees_personal_tasks')
                            ->where('task_id', $item->id)
                            ->exists();

                        // eindeutige Mitarbeiterzeile
                        $uniqueEmployees = $task_employee
                            ->where('task_id', $item->id)
                            ->unique('employee_id');

                        // aktueller Mitarbeiter-Satz
                        $currentUserTask = $task_employee
                            ->where('task_id', $item->id)
                            ->where('employee_id', $currentEmployeeId)
                            ->first();

                        $isRejectedByMe = $currentUserTask && $currentUserTask->status === 'reject';
                    @endphp

                    {{-- abgelehnte Tasks nicht in anderen Tabs anzeigen --}}
                    @if($isRejectedByMe && !$isRejectedTab)
                        @continue
                    @endif

                    <tr style="border-bottom:10px solid #f8f8f8;">
                        {{-- ID + Icons --}}
                        <td style="border-left:9px solid {{ $item->color }}; padding-left:11px;">
                            <div class="icons d-flex align-items-center" style="gap:10px;">
                                <div class="number">{{ $item->id }}</div>

                                <div class="complete_check">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" {{ $check_if_completed ? 'checked' : '' }} disabled>
                                        <span class="vs-checkbox vs-checkbox-sm">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <div class="ring">
                                    @if($isOutdated)
                                        <i class="feather icon-bell warning out-date"></i>
                                    @endif
                                </div>

                                <div class="lock">
                                    @if($item->public != 'on')
                                        <i class="feather icon-lock danger"></i>
                                    @else
                                        <i class="feather icon-unlock primary"></i>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Taskdetails --}}
                        <td colspan="3">
                            <a href="{{ url('personal_task_details/'.$item->id) }}"
                               class="text-white text-decoration-none">
                                <p class="task mb-1 {{ $check_if_completed ? 'mark-complete' : '' }}">
                                    {{ $item->task_title }}
                                </p>

                                @if($item->description)
                                    <p class="task_description p-0 m-0 mb-1">
                                        {{ Str::limit($item->description, 140, '...') }}
                                    </p>
                                @endif

                                <div class="description_details d-flex flex-wrap align-items-center">
                                    <span class="pr-2 task_date d-inline-flex align-items-center">
                                        <i class="feather icon-calendar mr-1"></i>
                                        {{ \Carbon\Carbon::parse($item->due_date)->format('d.m.Y') }}
                                    </span>

                                    <span class="pr-2 task_date d-inline-flex align-items-center">
                                        <i class="feather icon-clock mr-1"></i>
                                        {{ \Carbon\Carbon::parse($item->due_time)->format('H:i') }}
                                    </span>

                                    <span class="pr-2 task_date d-inline-flex align-items-center">
                                        <i class="feather icon-hash mr-1"></i>
                                        {{ $item->task_id }}
                                    </span>

                                    <span class="pr-2 task_date d-inline-flex align-items-center">
                                        @switch($item->priority)
                                            @case('medium')
                                                <i class="fa fa-battery-half mr-1"></i> Mittel
                                                @break
                                            @case('high')
                                                <i class="fa fa-battery-full mr-1"></i> Hoch
                                                @break
                                            @case('very high')
                                                <i class="fa fa-fire text-danger mr-1"></i> Sehr Wichtig
                                                @break
                                            @default
                                                <i class="fa fa-battery-empty mr-1"></i> Normal
                                        @endswitch
                                    </span>

                                    {{-- Kunden-Badge --}}
                                    @if($item->is_customer == 1)
                                        @php
                                            $product = DB::table('lead_product_lists as lpl')
                                                ->join('new_leads as nl', 'nl.id', '=', 'lpl.customer_id')
                                                ->join('lead_alternative_adds as laa', 'laa.id', '=', 'lpl.alternative_id')
                                                ->leftJoin('article_groups as ag', 'ag.id', '=', 'lpl.product_id')
                                                ->leftJoin('departments as d', 'd.id', '=', 'lpl.department_id')
                                                ->leftJoin('employees as e', 'e.id', '=', 'lpl.employee_id')
                                                ->where('nl.id', $item->customer_id)
                                                ->select(
                                                    'nl.name as customer_name',
                                                    'nl.lastname as customer_lastname',
                                                    'laa.object_name',
                                                    'ag.article_group',
                                                    'd.department_name',
                                                    'e.name as employee_name',
                                                    'e.lastname as employee_lastname'
                                                )
                                                ->first();
                                        @endphp

                                        @if($product)
                                            <span class="pr-2 task_date d-inline-flex align-items-center">
                                                <a href="{{ url('new_lead_profile/'.$item->customer_id) }}"
                                                   class="text-white">
                                                    <span class="badge badge-primary customer-popover"
                                                          data-toggle="popover"
                                                          data-html="true"
                                                          data-placement="bottom"
                                                          data-trigger="hover"
                                                          title="{{ $product->customer_name }} {{ $product->customer_lastname }}"
                                                          data-content="
                                                              <strong><i class='fa fa-home'></i> Objekt:</strong> {{ $product->object_name }}<br>
                                                              <strong><i class='fa fa-tag'></i> Produkt:</strong> {{ $product->article_group }}<br>
                                                              <strong><i class='fa fa-cubes'></i> Abteilung:</strong> {{ $product->department_name }}<br>
                                                              <strong><i class='fa fa-user'></i> Zuständig:</strong> {{ $product->employee_name }} {{ $product->employee_lastname }}
                                                          ">
                                                        <i class="feather icon-user mr-1"></i>
                                                        {{ $product->customer_name }} {{ $product->customer_lastname }}
                                                    </span>
                                                </a>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </a>
                        </td>

                        {{-- Verfasser --}}
                        <td>
                            <div class="avatar mr-1">
                                <img src="{{ asset('images/employee/'.$item->cimage) }}"
                                     alt="author"
                                     height="32"
                                     width="32">
                            </div>
                        </td>

                        {{-- Zugewiesen --}}
                        <td class="p-1">
                            <ul class="list-unstyled users-list m-0 d-flex align-items-center">
                                @foreach ($uniqueEmployees as $t_emp)
                                    @php
                                        $gender_icon = $t_emp->gender === 'Male'
                                            ? asset('images/gender/male.png')
                                            : asset('images/gender/female.png');

                                        $profile_image = !empty($t_emp->image)
                                            ? asset('images/employee/'.$t_emp->image)
                                            : $gender_icon;

                                        $statusClass = $t_emp->status === 'send'
                                            ? 'send_request'
                                            : ($t_emp->status === 'accept'
                                                ? 'accept_request'
                                                : 'reject_request');
                                    @endphp

                                    <li data-toggle="tooltip"
                                        data-popup="tooltip-custom"
                                        data-placement="bottom"
                                        data-original-title="{{ $t_emp->name }} {{ $t_emp->lastname }}"
                                        class="avatar pull-up">
                                        <img class="media-object rounded-circle {{ $statusClass }} @if($t_emp->status!='accept') change-btn @endif"
                                             src="{{ $profile_image }}"
                                             alt="{{ $t_emp->name }} {{ $t_emp->lastname }}"
                                             height="30"
                                             width="30"
                                             @if($t_emp->status!='accept')
                                                 data-task-id="{{ $t_emp->task_id }}"
                                                 data-old-employee-id="{{ $t_emp->employee_id }}"
                                                 data-toggle="modal"
                                                 data-target="#addEmployeeModal"
                                                 style="cursor:pointer"
                                             @endif
                                        >
                                    </li>
                                @endforeach
                            </ul>
                        </td>

                        {{-- Erinnerung --}}
                        <td>
                            @if($item->reminder_date || $item->reminder_time)
                                <small class="no-reminder-icon" data-id="{{ $item->id }}">
                                    {{ $item->reminder_date }} {{ $item->reminder_time }}
                                </small>
                            @else
                                -
                            @endif
                        </td>

                        {{-- Wiederholung --}}
                        <td>
                            @if($item->repeat)
                                <small class="no-repeat-icon" data-id="{{ $item->id }}">
                                    {{ $repeatMap[$item->repeat] ?? 'Unklar' }}
                                </small>
                            @else
                                -
                            @endif
                        </td>

                        {{-- Status + Info bei Ablehnung --}}
                        <td>
                            <small class="status" data-id="{{ $item->id }}">
                                {{ $progressStatusMap[$item->task_status] ?? 'Unklar' }}
                            </small>

                            @if($isRejectedByMe)
                                <div class="mt-25 text-danger" style="font-size: 11px;">
                                    <i class="feather icon-x-circle mr-25"></i>
                                    Von Ihnen abgelehnt
                                    @if($currentUserTask && $currentUserTask->change_date)
                                        <br>
                                        <span>
                                            {{ \Carbon\Carbon::parse($currentUserTask->change_date)->format('d.m.Y') }}
                                        </span>
                                    @endif
                                    @if($currentUserTask && $currentUserTask->reason)
                                        <br>
                                        <span class="font-italic">
                                            {{ $currentUserTask->reason }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </td>

                        {{-- Aktionen --}}
                        <td>
                            <div class="sa-dropdown" data-align="left">
                                <button type="button"
                                        class="sa-dropbtn"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                        aria-controls="menu-{{ $item->id }}"
                                        data-menu-trigger
                                        title="Menü">
                                    <i class="feather icon-menu"></i>
                                </button>

                                <div id="menu-{{ $item->id }}"
                                    class="sa-menu appointment_menu hidden"
                                    role="menu"
                                    tabindex="-1">

                                    {{-- Bearbeiten --}}
                                    <span class="sa-menu-item">
                                        <i class="feather icon-edit"></i>
                                        <a class="sa-menu-link"
                                        href="{{ url('personal_task/'.$item->id.'/edit') }}"
                                        role="menuitem">
                                            Bearbeiten
                                        </a>
                                    </span>

                                    {{-- Ablehnen / Neuen Mitarbeiter anfragen --}}
                                    @if($currentUserTask)
                                        <span class="sa-menu-item">
                                            <a class="sa-menu-link accept-request-btn"
                                            data-task-id="{{ $item->id }}"
                                            data-employee-id="{{ $currentEmployeeId }}"
                                            role="menuitem">
                                                Aufgabe ablehnen
                                            </a>
                                        </span>

                                        @if($currentUserTask->status === 'reject')
                                            <span class="sa-menu-item">
                                                <a class="sa-menu-link change-btn"
                                                data-task-id="{{ $item->id }}"
                                                data-employee-id="{{ $currentEmployeeId }}"
                                                data-old-employee-id="{{ $currentUserTask->employee_id }}"
                                                role="menuitem">
                                                    Neuen Mitarbeiter anfragen
                                                </a>
                                            </span>
                                        @endif
                                    @endif

                                    {{-- Löschen / Wiederherstellen (für neuen JS-Handler: data-id + data-button) --}}
                                    @if(is_null($item->deleted_at))
                                        <span class="sa-menu-item">
                                            <a href="#"
                                            class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-button="delete"
                                            role="menuitem">
                                                Löschen
                                            </a>
                                        </span>
                                    @else
                                        <span class="sa-menu-item">
                                            <a href="#"
                                            class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-button="recovery"
                                            role="menuitem">
                                                Wiederherstellen
                                            </a>
                                        </span>
                                    @endif

                                    {{-- Pause / Start --}}
                                    @if($item->task_status !== 'pause')
                                        <span class="sa-menu-item">
                                            <a class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-status="pause"
                                            role="menuitem">
                                                Pausieren
                                            </a>
                                        </span>
                                    @else
                                        <span class="sa-menu-item">
                                            <a class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-status="start"
                                            role="menuitem">
                                                Starten
                                            </a>
                                        </span>
                                    @endif

                                    {{-- Stornieren / Start --}}
                                    @if($item->task_status !== 'cancel')
                                        <span class="sa-menu-item">
                                            <a class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-status="cancel"
                                            role="menuitem">
                                                Stornierung
                                            </a>
                                        </span>
                                    @else
                                        <span class="sa-menu-item">
                                            <a class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-status="start"
                                            role="menuitem">
                                                Starten
                                            </a>
                                        </span>
                                    @endif

                                    {{-- Erledigt / Start --}}
                                    @if($item->task_status !== 'completed')
                                        <span class="sa-menu-item">
                                            <a class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-status="completed"
                                            role="menuitem">
                                                Erledigt
                                            </a>
                                        </span>
                                    @else
                                        <span class="sa-menu-item">
                                            <a class="sa-menu-link"
                                            data-id="{{ $item->id }}"
                                            data-status="start"
                                            role="menuitem">
                                                Starten
                                            </a>
                                        </span>
                                    @endif

                                    @if(!in_array($item->task_status, ['cancel', 'pause']))
                                        <div class="sa-menu-divider" role="separator"></div>

                                        {{-- Erinnerung --}}
                                        @if($item->reminder_date)
                                            <span class="sa-menu-item">
                                                <a class="sa-menu-link reminder"
                                                data-id="{{ $item->id }}"
                                                data-button="no_reminder"
                                                role="menuitem">
                                                    Erinnerung abbrechen
                                                </a>
                                            </span>
                                        @else
                                            <span class="sa-menu-item">
                                                <a class="sa-menu-link reminder"
                                                data-id="{{ $item->id }}"
                                                data-button="add_reminder"
                                                role="menuitem">
                                                    Erinnerung
                                                </a>
                                            </span>
                                        @endif

                                        {{-- Wiederholen --}}
                                        @if($item->repeat)
                                            <span class="sa-menu-item">
                                                <a class="sa-menu-link repeat"
                                                data-id="{{ $item->id }}"
                                                data-button="no_repeat"
                                                role="menuitem">
                                                    Wiederholen abbrechen
                                                </a>
                                            </span>
                                        @else
                                            <span class="sa-menu-item">
                                                <a class="sa-menu-link repeat"
                                                data-id="{{ $item->id }}"
                                                data-button="repeat"
                                                role="menuitem">
                                                    Wiederholen
                                                </a>
                                            </span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </td>


                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $data->links() }}
    </div>
</div>
