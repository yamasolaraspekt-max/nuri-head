<div class="contentTicket p-2 bg-white">
     

    <div id="filterContext"
        data-customer="{{ $customer_id }}"
        data-alternative="{{ $alternative_id }}"
        data-product="{{ $product_id }}">
    </div>

    <ul class="nav nav-tabs mb-3" id="ticketTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="tab-list" data-toggle="tab" href="#list-view" role="tab" aria-controls="list-view" aria-selected="true">
                Listenansicht
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="tab-kanban" data-toggle="tab" href="#kanban-view" role="tab" aria-controls="kanban-view" aria-selected="false">
                Kanbanansicht
            </a>
        </li>
    </ul>
    <div class="tab-content" id="ticketTabContent">
        <div class="tab-pane fade show active" id="list-view" role="tabpanel" aria-labelledby="tab-list">
        <!-- Original List View -->
            <div class="ticket-dashboard">
                <div class="row mb-4">
                    <div class="col-12">
                        <h3 class="fw-bold">Tickets</h3>
                    </div>
                </div>
                <div class="row g-2 mb-4">
                    <div class="col-md-3">
                        <input type="date" id="filterDate" class="form-control" placeholder="Datum">
                    </div>
                    <div class="col-md-3">
                        <select id="filterStatus" class="form-control">
                            <option value="">Alle Stati</option>
                            <option value="offen">Neu</option>
                            <option value="process">In Bearbeitung</option>
                            <option value="end">Abgeschlossen</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filterEmployee" class="form-control">
                            <option value="">Alle Mitarbeiter</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button onclick="filterTickets()" class="btn btn-primary w-100">Filtern</button>
                    </div>
                </div>

                <div class="row">
                    @forelse ($tickets as $ticket)
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-4" style="border-left: 5px solid #60a5fa">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title mb-1">Ticket Nr. #{{ $ticket->ticket_no }}</h5>
                                            <p class="text-muted mb-1">
                                                <strong>Kunde:</strong> {{ $ticket->customer->name }} {{ $ticket->customer->lastname }}<br>
                                                <strong>Produkt:</strong> {{ $ticket->product->article_group ?? '-' }}<br>
                                                <strong>Fehlercode:</strong> {{ $ticket->error_code ?? 'Nicht angegeben' }}
                                            </p>
                                            <p class="text-muted small">
                                                <strong>Erstellt:</strong> {{ $ticket->created_at->format('d.m.Y') }}<br>
                                                <strong>Status:</strong> <span class="badge bg-secondary">{{ ucfirst($ticket->status) }}</span>
                                            </p>

                                            {{-- ✅ Ticket tasks --}}
                                            @if ($ticket->ticket_tasks->count())
                                                <ul class="list-group mt-2">
                                                    @foreach ($ticket->ticket_tasks as $task)
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong>{{ $task->title }}</strong> <br>
                                                                <small>Status: {{ $task->status }} | Bis: {{ $task->due_date }}</small>
                                                            </div>
                                                            <span class="badge bg-secondary">{{ $task->priority }}</span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif

                                            {{-- ✅ Assigned employees --}}
                                            @if ($ticket->employees->count())
                                                <div class="d-flex flex-wrap align-items-center mt-3 gap-2">
                                                    @foreach ($ticket->employees as $emp)
                                                        <div class="text-center" style="width: 48px;">
                                                            <img src="{{ asset('images/employee/' . $emp->image) }}"
                                                                class="rounded-circle border"
                                                                style="width: 40px; height: 40px; object-fit: cover;"
                                                                title="{{ $emp->name }} {{ $emp->lastname }}">
                                                            <small class="d-block text-muted" style="font-size: 10px;">{{ $emp->lastname }}</small>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif

                                            {{-- ✅ Assignment form --}}
                                            <form method="POST" action="{{ route('ticket.assign', $ticket->id) }}" class="d-flex gap-2 mt-3">
                                                @csrf
                                                <select name="responsible" class="form-select form-select-sm">
                                                    @foreach($employees as $emp)
                                                        <option value="{{ $emp->id }}" @selected($ticket->responsible == $emp->id)>
                                                            {{ $emp->name }} {{ $emp->lastname }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="btn btn-success btn-sm">Zuweisen</button>
                                            </form>
                                        </div>

                                        <div>
                                            <a href="{{ route('problem.profile', $ticket->id) }}" class="btn btn-outline-primary btn-sm">Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning">Keine Tickets für diesen Kunden/Alternative/Produkt gefunden.</div>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
        <p class="text-muted">{{ $tickets->count() }} Tickets gefunden</p>

        <div class="tab-pane fade" id="kanban-view" role="tabpanel" aria-labelledby="tab-kanban">
            <div class="row kanban-board">
                @foreach (['offen' => 'Neu', 'process' => 'In Bearbeitung', 'end' => 'Abgeschlossen'] as $status => $label)
                    <div class="col-md-4">
                        <div class="kanban-column" data-status="{{ $status }}">
                            <h5 class="text-center text-muted">{{ $label }}</h5>
                            <div class="kanban-dropzone p-2 border rounded min-vh-50 bg-light">
                                @foreach ($tickets->where('status', $status) as $ticket)
                                    <div class="kanban-card p-2 mb-2 bg-white shadow-sm rounded" data-id="{{ $ticket->id }}">
                                        <div><strong>#{{ $ticket->ticket_no }}</strong></div>
                                        <div>{{ $ticket->customer->name }} {{ $ticket->customer->lastname }}</div>
                                        <small>{{ $ticket->product->article_group }}</small>

                                        @if ($ticket->employees->count())
                                            <div class="d-flex flex-wrap mt-2 gap-1">
                                                @foreach ($ticket->employees as $emp)
                                                    <img src="{{ asset('images/employee/' . $emp->image) }}"
                                                        class="rounded-circle border"
                                                        style="width: 28px; height: 28px; object-fit: cover;"
                                                        title="{{ $emp->name }} {{ $emp->lastname }}">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        
    </div>
</div>