
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Produktliste</h5>
            <button class="btn btn-primary" onclick="window.CustomerProducts.openAdd()">➕ Produkt hinzufügen</button>
        </div>

        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-bordered mb-0 table-hover" id="productTable">
                <thead style="position: sticky; top: 0; background-color: #fff; z-index: 5; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
                    <tr>  
                        <th>Anzahl</th>
                        <th>Produktname</th>
                        <th>Hersteller</th> 
                        <th>Installiert am</th>
                        <th>Kaufdatum</th>
                        <th>Details</th>
                        <th>Installiert von</th>
                        <th>Abteilung</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @foreach ($products as $product)
                        <tr data-id="{{ $product->id }}" style="cursor: pointer;">
                            <td>
                                <p class="m-0 p-0">{{ $product->product_count }}</p> 
                            </td>
                            <td>
                                <p class="m-0 p-0">{{ $product->product_name }}</p>
                                <small><p class="m-0 p-0">S.Nr: {{$product->serial_number ?? '-'}}</p></small>
                            </td>
                            <td>{{ $product->manufacturer }}</td> 
                            <td>
                                <p class="m-0 p-0">{{ $product->installation_date ?? 'unbekannt' }}</p>
                                <small><p class="m-0 p-0">{{ $product->installation_location ?? 'Installationsort unbekannt' }}</p></small>
                            </td>
                            <td>
                                <p class="m-0 p-0">{{ $product->purchase_date ?? '—' }}</p>
                                <small><p class="m-0 p-0">{{ $product->purchased_from_us == 1 ? 'Ja' : 'Nein' }}</p></small>
                            </td>
                            <td>
                                <small>
                                    <p class="m-0 p-0"> <strong>Rechnung/Referenz:</strong> {{ $product->invoice_reference ?? 'unbekannt' }} </p>
                                    <p class="m-0 p-0"> <strong>Garantie bis:</strong> {{ $product->warranty_until ?? 'unbekannt' }} </p>
                                    <p class="m-0 p-0"> <strong>Gewährleistung bis:</strong> {{ $product->guarantee_until ?? 'unbekannt' }} </p>
                                    <p class="m-0 p-0"> <strong>Bild vorhanden:</strong> {{ $product->image_available == 1 ? 'Ja' : 'Nein' }} </p> 
                                </small>
                            </td>
                            <td>{{ $product->installed_by ?? '—' }}</td>
                            <td>{{ $product->department_name ?? '—' }}</td>
                            <td class="text-right">
                                <!-- Stop propagation on buttons so row click doesn't trigger -->
                                <button class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light action-btn" onclick="event.stopPropagation(); editProduct({{ $product->id }})"><i class="feather icon-edit"></i></button>
                                <button class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light action-btn" onclick="event.stopPropagation(); deleteProduct({{ $product->id }})"><i class="feather icon-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>