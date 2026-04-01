@php
    function sortLink($field, $currentSort, $currentDir) {
        $direction = ($currentSort === $field && $currentDir === 'asc') ? 'desc' : 'asc';
        return "?sort_by={$field}&sort_direction={$direction}";
    }

    function sortIcon($field, $currentSort, $currentDir) {
        if ($currentSort === $field) {
            return $currentDir === 'asc' ? '↑' : '↓';
        }
        return '';
    }
@endphp

<table class="table">
    <thead>
        <tr>
            <th><a href="?sort_by=id&sort_direction={{ $sortBy === 'id' && $sortDirection === 'asc' ? 'desc' : 'asc' }}" class="sort-link">ID</a></th>
            <th><a href="?sort_by=list_name&sort_direction={{ $sortBy === 'list_name' && $sortDirection === 'asc' ? 'desc' : 'asc' }}" class="sort-link">Name</a></th>
            <th><a href="?sort_by=article_group&sort_direction={{ $sortBy === 'article_group' && $sortDirection === 'asc' ? 'desc' : 'asc' }}" class="sort-link">Produkt</a></th>
            <th><a href="?sort_by=employee_id&sort_direction={{ $sortBy === 'employee_id' && $sortDirection === 'asc' ? 'desc' : 'asc' }}" class="sort-link">Mitarbeiter</a></th>
            <th>Aufgabenart</th>
            <th>Aktion</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data as $row)
           <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->list_name }}</td>
                <td>{{ $row->product_name ?? '-' }}</td>
                <td>{{ $row->employee_name ?? '-' }}</td>
                <td>{{ $row->default_stage == 'yes' ? 'Hauptaufgabe' : '-' }}</td>
                <td>
                    <!-- 👇 Action Buttons Here -->
                    <button class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light delete-btn" data-id="{{ $row->id }}">
                        <i class="feather icon-trash"></i>
                    </button>

                    <a class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light edit-btn" href="{{ url('/checklist/edit/' . $row->id) }}">
                        <i class="feather icon-edit"></i>
                    </a>

                    <a class="btn btn-icon btn-icon rounded-circle {{ $row->default_stage == 'yes' ? 'btn-warning' : 'btn-primary' }} mr-1 mb-1 waves-effect waves-light"
                        href="{{ url('/checklist/default/' . $row->id) }}">
                        <i class="feather icon-flag"></i>
                    </a>
                </td>
            </tr> 
        @empty
            <tr><td colspan="6" class="text-center">Keine Daten gefunden</td></tr>
        @endforelse
    </tbody>
</table>

<div class="mt-2">
    {{ $data->appends(request()->query())->links() }}
</div>

