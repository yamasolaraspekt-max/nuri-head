@foreach($data as $item)
<!-- Description Modal -->
<div class="modal fade" id="description{{ $item->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Beschreibung</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <table class="table table-hover">
                    <thead><tr><th>Title</th><th>Value</th></tr></thead>
                    <tbody>
                        @foreach ($product_description->where('master_set_id', $item->id) as $des)
                            <tr>
                                <td>{{ $des->title }}</td>
                                <td>{{ $des->value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="delete-pro{{ $item->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Löschen bestätigen</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p>Möchten Sie diesen Datensatz wirklich löschen?<br>ID: {{ $item->id }}</p>
            </div>
            <div class="modal-footer">
                <a href="{{ url('/add_product_delete/'.$item->id) }}" class="btn btn-danger">Ja, löschen</a>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editmodel{{ $item->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Produkt bearbeiten</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="{{ action('App\Http\Controllers\ProductMasterSetController@update') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="master_set_id" value="{{ request()->master }}">
                    <div class="form-group">
                        <label>Produkt</label>
                        <select class="form-control" name="product_id">
                            @foreach ($product as $pro)
                                <option value="{{ $pro->id }}" @if($pro->id == $item->product_id) selected @endif>{{ $pro->product }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Produktanzahl</label>
                        <input type="number" class="form-control" name="product_count" value="{{ $item->product_count }}" required>
                    </div>
                    <div class="form-group">
                        <label>Maßeinheit</label>
                        <select class="form-control" name="measure_unit">
                            @foreach ($measure as $me)
                                <option value="{{ $me->id }}" @if($me->id == $item->measure_unit) selected @endif>{{ $me->measure_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Product Description Modal -->
<div class="modal fade" id="add_product{{ $item->id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Beschreibung hinzufügen</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="{{ action('App\Http\Controllers\AddProductToSetController@add') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="master_set" value="{{ $item->master_set_id }}">
                    <input type="hidden" name="product_set" value="{{ $item->id }}">
                    <div class="form-group">
                        <label>Titel</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Value</label>
                        <input type="text" class="form-control" name="value" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Einreichen</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
