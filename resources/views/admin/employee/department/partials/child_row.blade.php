@php
    $padding = $level * 20;

    // $employeeData is a collection/array keyed by employee_id
    $head = $employeeData[$child->department_head] ?? null;
    $rep  = $employeeData[$child->head_representative] ?? null;

    $headName = $head ? $head->name . ' ' . $head->lastname : '–';
    $repName  = $rep  ? $rep->name  . ' ' . $rep->lastname  : '–';

    // NEW: department stats (customers + salary cost)
    $stats = $departmentStats[$child->id] ?? null;

    $customersTotal  = $stats['customers_total']  ?? 0;
    $customersOpen   = $stats['customers_open']   ?? 0;
    $customersZusage = $stats['customers_zusage'] ?? 0;
    $customersTicket = $stats['customers_ticket'] ?? 0;
    $customersAbsage = $stats['customers_absage'] ?? 0;

    $salaryCost = $stats['salary_cost'] ?? 0.0;
@endphp

<tr data-id="{{ $child->id }}"
    data-parent="{{ $child->parent_id }}"
    class="department-row parent-{{ $child->parent_id }}"
    style="display: {{ $level == 0 ? 'table-row' : 'none' }}">

    {{-- ID --}}
    <th scope="row">{{ $child->id }}</th>

    {{-- Department name + hierarchy indent + toggle --}}
    <td style="padding-left: {{ $padding }}px;">
        @if(isset($structuredDepartments[$child->id]))
            <span class="toggle-children"
                  onclick="toggleChildren({{ $child->id }})"
                  style="cursor:pointer;">
                <i class="feather icon-chevron-down"></i>
            </span>
        @endif

        <a href="{{ url('/department/profile/'.$child->id) }}">
            {{ $child->department_name }}
        </a>
    </td>

    {{-- Head of department --}}
    <td>
        <div class="department-head-name">
            @if($head)
                {{ $headName }}
            @else
                <button class="btn btn-sm btn-outline-primary change_employee"
                        data-department-id="{{ $child->id }}">
                    + Abteilungsleiter
                </button>
            @endif
        </div>
    </td>

    {{-- Representative --}}
    <td>
        <div class="department-rep-name">
            @if($rep)
                {{ $repName }}
            @else
                <button class="btn btn-sm btn-outline-primary change_representative"
                        data-department-id="{{ $child->id }}">
                    + Stellvertretung
                </button>
            @endif
        </div>
    </td>

    {{-- NEW: Customers per department --}}
    <td>
        <div class="dept-customers-main">
            {{ $customersTotal }}
        </div>
        <div class="dept-customers-breakdown">
            Offen: {{ $customersOpen }}
            &nbsp;|&nbsp; Zusage: {{ $customersZusage }}<br>
            Ticket: {{ $customersTicket }}
            &nbsp;|&nbsp; Absage: {{ $customersAbsage }}
        </div>
    </td>

    {{-- NEW: Monthly salary cost per department --}}
    <td class="dept-cost-cell">
        {{ number_format($salaryCost, 2, ',', '.') }}&nbsp;€
    </td>

    {{-- Status --}}
    <td>
        @if($child->status == "Published")
            <div class="badge badge-primary">Aktiv</div>
        @else
            <div class="badge badge-danger">Inaktiv</div>
        @endif
    </td>

    {{-- Actions --}}
     
        {{-- Actions --}}
    <td class="dept-actions-cell">
        <div class="dept-actions-wrapper">
            {{-- Trigger button (3 dots) --}}
            <button type="button"
                    class="dept-actions-trigger"
                    data-department-id="{{ $child->id }}">
                <i class="feather icon-more-vertical"></i>
            </button>

            {{-- Custom JS menu --}}
            <div class="dept-actions-menu">
                <button type="button"
                        class="dept-actions-item"
                        onclick="editDepartment({{ $child->id }})">
                    <i class="feather icon-edit"></i>
                    <span>Bearbeiten</span>
                </button>

                <button type="button"
                        class="dept-actions-item"
                        onclick="deleteDepartment({{ $child->id }})">
                    <i class="feather icon-trash"></i>
                    <span>Löschen</span>
                </button>

                <button type="button"
                        class="dept-actions-item"
                        onclick="assignDepartmentHead({{ $child->id }})">
                    <i class="feather icon-user"></i>
                    <span>Abteilungsleiter</span>
                </button>

                <button type="button"
                        class="dept-actions-item"
                        onclick="assignDepartmentRepresentative({{ $child->id }})">
                    <i class="feather icon-user-plus"></i>
                    <span>Stellvertretung</span>
                </button>

                <button type="button"
                        class="dept-actions-item"
                        onclick="chooseEmployeeForDepartment({{ $child->id }})">
                    <i class="feather icon-user-plus"></i>
                    <span>Mitarbeiter zuweisen</span>
                </button>

                <button type="button"
                        class="dept-actions-item"
                        onclick="viewDepartmentEmployeesFromMenu({{ $child->id }})">
                    <i class="feather icon-users"></i>
                    <span>Mitarbeiterliste</span>
                </button>
            </div>
        </div>
    </td>

</tr>

@if (!empty($structuredDepartments[$child->id]))
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
