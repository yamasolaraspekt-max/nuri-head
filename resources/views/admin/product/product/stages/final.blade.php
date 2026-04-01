<div class="card">
    <div class="card-header">
        <h4 class="card-title">Abschluss & Zusammenfassung</h4>
    </div>
    <div class="card-body">
        <p>Überprüfen Sie alle Angaben vor dem Abschluss.</p>

        <div id="summary-content" class="row">
            <!-- Links: Produktinfo -->
            <div class="col-md-6">
                <h5>Produktdetails</h5>
                <ul class="list-group">
                    <li class="list-group-item"><strong>Artikelnummer:</strong> <span id="summary-article_no"></span></li>
                    <li class="list-group-item"><strong>Produktname:</strong> <span id="summary-product"></span></li>
                    <li class="list-group-item"><strong>EAN:</strong> <span id="summary-ean"></span></li>
                    <li class="list-group-item"><strong>Kategorie:</strong> <span id="summary-category"></span></li>
                    <li class="list-group-item"><strong>Farbe:</strong> <span id="summary-color"></span></li>
                    <li class="list-group-item"><strong>Modell:</strong> <span id="summary-model"></span></li>
                </ul>
            </div>

            <!-- Rechts: Bild + Beschreibung -->
            <div class="col-md-6">
                <h5>Produktbild</h5>
                <img id="summary-image" src="{{ asset('no-image.png') }}" class="img-fluid mb-2" style="max-height: 200px;">

                <h6>Kurzbeschreibung</h6>
                <p id="summary-description" class="border p-2 bg-light rounded"></p>
            </div>
        </div>

        <div class="text-right mt-3">
            <a href="{{ route('product.info') }}" class="btn btn-success">Fertigstellen & Zurück zur Liste</a>
        </div>
    </div>
</div>
