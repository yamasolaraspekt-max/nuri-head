 
@foreach($structuredDepartments[null] ?? [] as $root)
    @include('admin.employee.department.partials.child_row', [
        'child'                 => $root,
        'structuredDepartments' => $structuredDepartments,
        'employeeData'          => $employeeData,
        'level'                 => 0,
    ])
@endforeach
