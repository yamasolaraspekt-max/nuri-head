@foreach($data as $item)
    <div class="kanban-item card mb-2 p-2" data-id="{{ $item->id }}" data-emp-id="{{ $item->employee_id }}">
        <div class="d-flex justify-content-between">
            <strong>#{{ $item->id }} - {{ $item->name }} {{ $item->lastname }}</strong>
            <span>{{ \Carbon\Carbon::parse($item->created_at)->isoFormat('DD.MM.YY') }}</span>
        </div>
        <small class="text-muted">{{ $item->city }}</small>
        <div class="d-flex align-items-center mt-2">
            <img src="{{ asset('images/employee/' . ($item->emp_image ?: 'default.png')) }}" class="rounded-circle mr-2" width="32" height="32">
            <div>{{ $item->emp_name }} {{ $item->emp_lastname }}</div>
        </div>
        <div class="mt-2">
            <a href="{{ url('new_lead_profile/'.$item->customer_id) }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-user"></i></a>
            <button class="btn btn-sm btn-outline-warning open-notes-sidebar" data-customer-id="{{ $item->customer_id }}" data-alternative-id="{{ $item->alternative_id }}" data-product-id="{{ $item->product_id }}">
                <i class="fa fa-sticky-note"></i>
            </button>
            <button class="btn btn-sm btn-outline-primary open-upload-modal" data-toggle="modal" data-target="#upload{{ $item->id }}" data-customer-id="{{ $item->customer_id }}" data-alternative-id="{{ $item->alternative_id }}" data-product-id="{{ $item->product_id }}">
                <i class="fa fa-picture-o"></i>
            </button>
        </div>
    </div>
@endforeach
