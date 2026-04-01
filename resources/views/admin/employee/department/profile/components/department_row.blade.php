@php
    $padding = $level * 20; // Indentation based on hierarchy level
@endphp     

<tr>
    <td style="padding-left: {{ $padding }}px;">
        {{ $department->department_name }}
    </td>
    <td>
        {{ $department->emp_name ? $department->emp_name . ' ' . $department->emp_lastname : 'N/A' }}
    </td> 
    <td>
        {{ number_format($department->total_cost, 2) }} € <!-- Show correct department cost -->
    </td>
</tr>

@if (!empty($department->children))
    @foreach ($department->children as $child)
        @include('admin.employee.department.profile.components.department_row', [
            'department' => $child,
            'level' => $level + 1
        ])
    @endforeach
@endif

@if ($level == 0) 
    <!-- Only show total row at the root level -->
    <tr> 
        <td colspan="2"></td>
        <td style="    border-top: 2px solid #dddddd;"><strong>{{ number_format($department->total_cost_with_children, 2) }} €</strong></td>
    </tr>
@endif
