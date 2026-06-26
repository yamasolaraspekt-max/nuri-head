@php
    $padding = $level * 22;

    $head = $employeeData[$child->department_head] ?? null;
    $rep  = $employeeData[$child->head_representative] ?? null;

    $headName = $head ? $head->name . ' ' . $head->lastname : '–';
    $repName  = $rep  ? $rep->name  . ' ' . $rep->lastname  : '–';

    $stats = $departmentStats[$child->id] ?? null;

    $customersTotal  = $stats['customers_total']  ?? 0;
    $customersOpen   = $stats['customers_open']   ?? 0;
    $customersZusage = $stats['customers_zusage'] ?? 0;
    $customersTicket = $stats['customers_ticket'] ?? 0;
    $customersAbsage = $stats['customers_absage'] ?? 0;

    $salaryCost = $stats['salary_cost'] ?? 0.0;

    $hasChildren = !empty($structuredDepartments[$child->id]);
@endphp

<div data-id="{{ $child->id }}"
     data-parent="{{ $child->parent_id }}"
     class="dp-row parent-{{ $child->parent_id }}"
     style="display: {{ $level == 0 ? 'block' : 'none' }}">

    <div class="dp-row-inner">
        <div class="dp-cell">
            <div class="dp-cell-title">ID</div>
            <span class="dp-id-badge">#{{ $child->id }}</span>
        </div>

        <div class="dp-cell" style="padding-left: {{ $padding }}px;">
            <div class="dp-cell-title">Abteilung</div>
            <div class="dp-main">
                <div class="dp-name">
                    @if($hasChildren)
                        <span class="toggle-children" onclick="toggleChildren({{ $child->id }})">
                            <i class="feather icon-chevron-down"></i>
                        </span>
                    @endif

                    @if($level > 0)
                        <span style="color:#9ca3af;">↳</span>
                    @endif

                    <a href="{{ url('/department/profile/'.$child->id) }}">{{ $child->department_name }}</a>
                </div>
                <div class="dp-subline">Hierarchieebene {{ $level + 1 }}</div>
            </div>
        </div>

        <div class="dp-cell">
            <div class="dp-cell-title">Abteilungsleiter</div>
            @if($head)
                <div class="dp-person head">{{ $headName }}</div>
            @else
                <button class="dp-assign-btn change_employee" data-department-id="{{ $child->id }}">
                    + Abteilungsleiter
                </button>
            @endif
        </div>

        <div class="dp-cell">
            <div class="dp-cell-title">Stellvertretung</div>
            @if($rep)
                <div class="dp-person rep">{{ $repName }}</div>
            @else
                <button class="dp-assign-btn change_representative" data-department-id="{{ $child->id }}">
                    + Stellvertretung
                </button>
            @endif
        </div>

        <div class="dp-cell">
            <div class="dp-cell-title">Kunden</div>
            <div class="dp-customer-main">{{ $customersTotal }}</div>
            <div class="dp-customer-sub">
                Offen: {{ $customersOpen }} | Zusage: {{ $customersZusage }}<br>
                Ticket: {{ $customersTicket }} | Absage: {{ $customersAbsage }}
            </div>
        </div>

        <div class="dp-cell">
            <div class="dp-cell-title">Kosten (Monat)</div>
            <div class="dp-cost">{{ number_format($salaryCost, 2, ',', '.') }} €</div>
        </div>

        <div class="dp-cell">
            <div class="dp-cell-title">Status</div>
            @if($child->status == "Published")
                <span class="dp-status-pill green">Aktiv</span>
            @else
                <span class="dp-status-pill red">Inaktiv</span>
            @endif
        </div>

        <div class="dp-cell dept-actions-cell">
            <div class="dp-cell-title">Aktionen</div>

            <div class="dept-actions-wrapper">
                <button type="button" class="dept-actions-trigger" data-department-id="{{ $child->id }}">
                    <i class="feather icon-more-vertical"></i>
                </button>

                <div class="dept-actions-menu">
                    <button type="button" class="dept-actions-item" onclick="editDepartment({{ $child->id }})">
                        <i class="feather icon-edit"></i>
                        <span>Bearbeiten</span>
                    </button>

                    <button type="button" class="dept-actions-item" onclick="deleteDepartment({{ $child->id }})">
                        <i class="feather icon-trash"></i>
                        <span>Löschen</span>
                    </button>

                    <button type="button" class="dept-actions-item" onclick="assignDepartmentHead({{ $child->id }})">
                        <i class="feather icon-user"></i>
                        <span>Abteilungsleiter</span>
                    </button>

                    <button type="button" class="dept-actions-item" onclick="assignDepartmentRepresentative({{ $child->id }})">
                        <i class="feather icon-user-plus"></i>
                        <span>Stellvertretung</span>
                    </button>

                    <button type="button" class="dept-actions-item" onclick="chooseEmployeeForDepartment({{ $child->id }})">
                        <i class="feather icon-user-plus"></i>
                        <span>Mitarbeiter zuweisen</span>
                    </button>

                    <button type="button" class="dept-actions-item" onclick="viewDepartmentEmployeesFromMenu({{ $child->id }})">
                        <i class="feather icon-users"></i>
                        <span>Mitarbeiterliste</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($hasChildren)
    @foreach($structuredDepartments[$child->id] as $subChild)
        @include('admin.employee.department.partials.child_row', [
            'child'                 => $subChild,
            'structuredDepartments' => $structuredDepartments,
            'employeeData'          => $employeeData,
            'departmentStats'       => $departmentStats,
            'level'                 => $level + 1,
        ])
    @endforeach
@endif