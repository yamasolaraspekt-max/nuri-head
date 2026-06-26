<div class="row">
    <div class="col-12 mb-1 float-right">
        <form action="{{ route('deal.invoice')}}" method="GET">
            <fieldset>
                <div class="input-group mb-1">
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Suche..." aria-describedby="button-addon2">
                    
                    <select name="invoice_type" class="form-control ml-1" style="max-width: 150px;">
                        <option value="">Alle Type</option>
                        <option value="open" {{ request('invoice_type') == 'open' ? 'selected' : '' }}>Abschlag</option>
                        <option value="confirm" {{ request('invoice_type') == 'confirm' ? 'selected' : '' }}>Schlussrechnung</option>
                        <option value="inconfirm" {{ request('invoice_type') == 'inconfirm' ? 'selected' : '' }}>Unbestätigt</option>
                    </select>

                    <select name="filter" id="filter" class="form-control ml-1">
                        <option value="all" {{ request('filter') == 'all' ? 'selected' : '' }}>Alle Angebote</option>
                        <option value="my" {{ request('filter', 'my') == 'my' ? 'selected' : '' }}>Meine Angebote</option>
                    </select>

                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">Go</button>
                    </div>
                </div>
            </fieldset>
        </form> 
    </div>  
</div>
<div class="table-responsive">
    <table class="table table-hover animation-table">
        <thead class="thead-light">
            <tr>
                <th>Rechnungsnr.</th>
                <th>Typ</th>
                <th>Kunde</th> 
                <th>Betrag</th>
                <th>Fällig am</th>  
                <th>Offenbetrag</th>
                <th>Status</th>
                <th>Aktionen</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number ?? '-' }}</td>
                    <td>{{ $invoice->invoice_type ?? '-' }}</td>
                    <td>
                        <p class="m-0"><strong style="font-size: 15px;">{{ $invoice->customer_name }} {{ $invoice->customer_lastname }}</strong></p>
                        <p class="m-0"><i class="fa fa-cubes"></i> {{ $invoice->article_group }}</p>
                        <p class="m-0"><i class="fa fa-map-pin"></i> {{ $invoice->postcode }} {{ $invoice->city }}</p>
                        <p class="m-0"><i class="fa fa-users"></i> {{ $invoice->emp_name }} {{ $invoice->emp_lastname }}</p>
                    </td>   
                    <td>{{ number_format($invoice->invoice_amount, 2) }} €</td>
                    <td>
                        @if($invoice->open_amount > 0)
                            <span class="text-danger">{{ number_format($invoice->open_amount, 2) }} €</span>
                        @else
                            <span class="text-success">Bezahlt</span>
                        @endif
                    </td>
                    <td>
                        @if($invoice->status === 'canceled')
                            <span class="badge badge-danger">Storniert</span>
                        @elseif($invoice->status === 'paid')
                            <span class="badge badge-success">Bezahlt</span>
                        @elseif($invoice->status === 'due')
                            <span class="badge badge-warning">Fällig</span>
                        @else
                            <span class="badge badge-secondary">Offen</span>
                        @endif
                    </td>
                    <td>{{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('d.m.Y') : '-' }}</td>
                 
                    <td>
                        <a href="#" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a>
                        <a href="#" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>
                        <a href="#" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">Keine Rechnungen gefunden.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-center mt-2">
        {{ $data->links() }}
    </div>
</div>
