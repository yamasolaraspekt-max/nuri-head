<div class="table-responsive mt-3">
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>Produkt</th>
                <th>Lieferant</th>
                <th>UPV</th>
                <th>Rabbat-Gruppe</th>
                <th>Einkaufspreis</th>
                <th>Verfügbarkeit</th>
                <th>Datum</th>
                <th>Status</th>
                <th>Aktion</th>
                <th>Veröffentlichen</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($distributor_price as $dist)
                @if ($distributor->id == request()->id)
                    <tr>
                        <td>{{ $dist->product }}</td>
                        <td>{{ $dist->name }}</td>
                        <td>{{ number_format($dist->price, 2, ',', '.') }}€</td>
                        <td>{{ $dist->discount_group }} - {{ $dist->discount }}%</td>
                        <td>{{ number_format($dist->purchase_price, 2, ',', '.') }}€</td>
                        <td>{{ $dist->availability }}</td>
                        <td>{{ $dist->price_date }}</td>
                        <td>
                            <span class="badge badge-{{ $dist->status == 'Published' ? 'success' : 'danger' }}">
                                {{ $dist->status == 'Published' ? 'Veröffentlicht' : 'Nicht Veröffentlicht' }}
                            </span>
                        </td>
                        <td>
                            <!-- Delete -->
                            <button class="btn btn-sm btn-outline-danger" data-toggle="modal" data-target="#delete-pro{{ $dist->id }}">
                                <i class="feather icon-trash"></i>
                            </button>

                            @include('admin.product.modals.delete_price', ['id' => $dist->id])

                            <!-- Edit -->
                            <button class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#edit-pro{{ $dist->id }}">
                                <i class="feather icon-edit"></i>
                            </button>

                            @include('admin.product.modals.edit_price', [
                                'dist' => $dist,
                                'product_id' => $dist->product_id,
                                'distributor' => $distributor,
                                'discount' => $discount
                            ])
                        </td>
                        <td>
                            @if ($dist->status == 'Unpublished' || $dist->status == '')
                                <a href="{{ url('/distributor_price_publish/'.$dist->id) }}" class="btn btn-sm btn-success">
                                    <i class="feather icon-check"></i>
                                </a>
                            @else
                                <a href="{{ url('/distributor_price_unpublish/'.$dist->id) }}" class="btn btn-sm btn-danger">
                                    <i class="feather icon-x"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</div>
