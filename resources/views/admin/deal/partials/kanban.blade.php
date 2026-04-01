
<style>
    .kanban-controls select,
    .kanban-controls input {
        min-width: 180px;
        margin-right: 8px;
        white-space: nowrap;
    }

    .kanban-controls {
        overflow-x: auto;
        white-space: nowrap;
    }
</style>

<div class="kanban-controls d-flex flex-nowrap align-items-center mb-3">
    <input type="text" id="kanban-search" class="form-control" placeholder="Suchen...">

    <select id="kanban-filter" class="form-control">
        <option value="all">Alle Angebote</option>
        <option value="my">Meine Angebote</option>
    </select>
 
    <select id="kanban-product" class="form-control">
        <option value="">Alle Produkte</option>
        @foreach($products as $product)
            <option value="{{ $product->id }}">{{ $product->article_group }}</option>
        @endforeach
    </select>
</div>

<div class="kanban-board" id="kanbanBoard">
    @foreach(['open', 'confirm', 'inconfirm', 'pause', 'cancel'] as $status)
        @php
            $statusTranslations = [
                'open' => 'Offen',
                'confirm' => 'Geprüft',
                'inconfirm' => 'Unbestätigt',
                'pause' => 'Pausiert',
                'cancel' => 'Absage'
            ];
            $statusColors = [
                'open' => 'bg-warning',
                'confirm' => 'bg-success',
                'inconfirm' => 'bg-danger',
                'pause' => 'bg-warning',
                'cancel' => 'bg-danger'
            ];
        @endphp

        <div class="kanban-column" data-status="{{ $status }}">
            <div class="card shadow-sm mb-0">
                <div class="card-header text-white {{ $statusColors[$status] }} p-1">
                    <strong>{{ $statusTranslations[$status] }}</strong>
                </div>

                <div class="card-body kanban-list" data-status="{{ $status }}">
                    @foreach($data->where('status', $status) as $item)
                        <div class="kanban-item card p-1 shadow-sm mb-2" data-id="{{ $item->id }}" data-emp-id="{{ $item->employee_id }}" style="    border: 2px dotted #bbbbbb; border-left: 4px solid #73b1d4;" >
                            <div class="mb-2">
                                <strong>Kunde: {{ $item->name }} {{ $item->lastname }}</strong>
                            </div>
                            <div class="text-muted mb-1">Produkt: {{ $item->article_group ?? '—' }}</div>
                            <div class="text-muted mb-2">Auftragssumme: {{ $item->price ?? 'auf Anfrage' }}</div>

                            <div class="d-flex justify-content-between align-items-end">
                                <div class="d-flex align-items-center icon-toolbar">
                                    <a href="{{ url('new_lead_profile/'.$item->customer_id) }}" class="icon-action" title="Kunde ansehen">
                                        <i class="fa fa-folder-open"></i>
                                    </a>

                                    <button type="button" class="icon-action open-notes-sidebar"
                                        data-customer-id="{{ $item->customer_id }}"
                                        data-alternative-id="{{ $item->alternative_id }}"
                                        data-product-id="{{ $item->product_id }}"
                                        title="Notizen">
                                        <i class="fa fa-sticky-note-o text-warning"></i>
                                    </button>

                                    <button type="button" class="icon-action open-upload-sidebar"
                                        data-customer-id="{{ $item->customer_id }}"
                                        data-alternative-id="{{ $item->alternative_id }}"
                                        data-product-id="{{ $item->product_id }}"
                                        data-item-id="{{ $item->id }}"
                                        title="Bilder">
                                        <i class="fa fa-picture-o text-primary"></i>
                                    </button>

                                    <button class="icon-action" title="Details">
                                        <i class="fa fa-cog text-secondary"></i>
                                    </button>
                                </div>

                                <div>
                                    <img src="{{ asset('images/employee/' . ($item->emp_image ?: 'default.png')) }}"
                                        class="rounded-circle" width="40" height="40"
                                        title="{{ $item->emp_name }} {{ $item->emp_lastname }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach
</div>
