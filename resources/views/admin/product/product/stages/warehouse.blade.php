<div class="card">
    <div class="card-header"><h4 class="card-title">Lagerinformationen</h4></div>
    <div class="card-body">
        <p>Tragen Sie hier Seriennummer, Lagerort, Menge etc. ein.</p>

        <!-- Inventory Form -->
        <form id="inventoryForm">
            @csrf
             

            <div class="form-row align-items-end">
                <div class="form-group col-md-3">
                    <label>Seriennummer</label>
                    <input type="text" name="serial_no" class="form-control" required id="inventory_serial_no">
                </div>
                <div class="form-group col-md-3">
                    <label>Artikelnummer</label>
                    <input type="text" name="article_no" class="form-control" id="inventory_article_no">
                </div>
                <div class="form-group col-md-3">
                    <label>EAN</label>
                    <input type="text" name="ean" class="form-control" id="inventory_ean">
                </div>
                <div class="form-group col-md-3">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-outline-primary btn-block" id="reloadProductData">
                        <i class="feather icon-refresh-ccw"></i> Daten laden
                    </button>
                </div>
            </div>


            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Handbuchnummer</label>
                    <input type="text" name="manual_no" class="form-control">
                </div>
                <div class="form-group col-md-4">
                    <label>Lagerort</label>
                    <input type="text" name="location" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Regal</label>
                    <input type="text" name="shelf" class="form-control">
                </div>
                <div class="form-group col-md-2">
                    <label>Reihe</label>
                    <input type="text" name="row" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Menge</label>
                    <input type="number" name="quantity" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Zuständiger Mitarbeiter</label>
                    <select name="responsible_id" class="form-control" id="responsibleSelect">
                        <option selected disabled>Mitarbeiter wählen</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3 align-self-end">
                    <button type="submit" class="btn btn-success btn-block">Speichern</button>
                </div>
            </div>
        </form>

        <hr>

        <!-- Inventory Table -->
        <table class="table table-striped mt-2" id="inventoryTable">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Seriennummer</th>
                    <th>Artikelnummer</th>
                    <th>EAN</th>
                    <th>Lagerort</th>
                    <th>Menge</th>
                    <th>Aktion</th>
                </tr>
            </thead>
            <tbody>
                <!-- AJAX content will be loaded here -->
            </tbody>
        </table>
    </div>
</div>
