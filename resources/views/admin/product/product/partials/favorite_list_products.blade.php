@if($items->count())
    <div class="table-responsive">
        <table class="table table-sm table-hover fav-products-table">
            <thead>
                <tr>
                    <th style="width:30px;">
                        <input type="checkbox" id="fav-products-check-all">
                    </th>
                    <th>Art.Nr.</th>
                    <th>Produkt</th>
                    <th>Hersteller</th>
                    <th>Kategorie</th>
                    <th>Status</th>
                    <th>Hinzugefügt</th>
                    <th>von</th>
                    <th style="width:60px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $row)
                    <tr data-item-id="{{ $row->id }}" data-product-id="{{ $row->product_id }}">
                        <td>
                            <input type="checkbox" class="fav-product-check">
                        </td>
                        <td>{{ $row->article_no ?: '–' }}</td>
                        <td>
                            <a href="{{ url('/product_details/'.$row->product_id) }}">
                                {{ \Illuminate\Support\Str::limit($row->product, 45) }}
                            </a>
                        </td>
                        <td>
                            @if($row->brand_name)
                                <span class="fav-badge-brand">{{ $row->brand_name }}</span>
                            @else
                                <span class="text-muted" style="font-size:.7rem;">–</span>
                            @endif
                        </td>
                        <td>{{ $row->category ?: '–' }}</td>
                        <td>
                            @if($row->status === 'Published')
                                <span class="badge badge-light-success">Aktiv</span>
                            @else
                                <span class="badge badge-light-secondary">Inaktiv</span>
                            @endif
                        </td>
                        <td>
                            {{ optional($row->added_at)->format('d.m.Y') ?? '–' }}
                        </td>
                        <td>
                            @if($row->emp_name)
                                {{ $row->emp_name }} {{ $row->emp_lastname }}
                            @else
                                <span class="text-muted" style="font-size:.7rem;">–</span>
                            @endif
                        </td>
                        <td class="text-right">
                            <button type="button"
                                    class="btn btn-sm btn-outline-danger fav-product-remove"
                                    title="Aus Ordner entfernen">
                                <i class="feather icon-x"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="fav-products-empty">
        In diesem Ordner sind noch keine Produkte hinterlegt.
    </div>
@endif
