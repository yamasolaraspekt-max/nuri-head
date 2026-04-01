@foreach($structuredDepartments[null] ?? [] as $parent)
    <tr data-id="{{ $parent->id }}" class="department-row">
        <th scope="row">{{ $parent->id }}</th>
        <td>
            @if(isset($structuredDepartments[$parent->id])) <!-- Show toggle only if children exist -->
                <span class="toggle-children" onclick="toggleChildren({{ $parent->id }})" style="cursor: pointer;">
                    <i class="feather icon-chevron-down"></i>
                </span>
            @endif
            <a href="{{ url('/department/profile/'.$parent->id) }}">
            {{ $parent->department_name }}
            </a>
        </td>
        <td>
            @if($parent->status == "Published")
                <div class="badge badge-success">Aktiv</div>
            @else
                <div class="badge badge-danger">Inaktiv</div>
            @endif
        </td>
        <td>
            <button class="btn btn-danger btn-sm" onclick="deleteDepartment({{ $parent->id }})">
                <i class="feather icon-trash"></i>
            </button>
            <button class="btn btn-primary btn-sm" onclick="editDepartment({{ $parent->id }})">
                <i class="feather icon-edit"></i>
            </button>
        </td>
    </tr>

    <!-- Render children recursively -->
    @foreach($structuredDepartments[$parent->id] ?? [] as $child)
        @include('admin.employee.department.partials.child_row', ['child' => $child, 'structuredDepartments' => $structuredDepartments, 'level' => 1])
    @endforeach
@endforeach
