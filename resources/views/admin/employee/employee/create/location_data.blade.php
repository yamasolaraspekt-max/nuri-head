<table class="table table-striped">
    <thead>
        <tr>
            <th>#</th>
            <th>Postleitzahl Absender</th>
            <th>Postleitzahl Ziel</th>
            <th>Land</th>
            <th>Aktionen</th>
        </tr>
    </thead>
        <tbody>
        @foreach($lists as $item)
        <tr id="row-{{ $item->id }}">
            <td>{{ $item->id }}</td>
            <td>{{ $item->postcode_from }}</td>
            <td>{{ $item->postcode_to }}</td>
            <td>{{ $item->country }}</td>
            <td>
                <button class="btn btn-icon rounded-circle btn-outline-primary waves-effect waves-light   editPostcode" data-id="{{ $item->id }}"><i class="feather icon-edit"></i></button>
                <button class="btn btn-icon rounded-circle btn-outline-danger waves-effect waves-light   deletePostcode" data-id="{{ $item->id }}"><i class="feather icon-trash"></i></button>
            </td>
        </tr>
        @endforeach
        </tbody>
</table>

{{ $lists->links() }}
