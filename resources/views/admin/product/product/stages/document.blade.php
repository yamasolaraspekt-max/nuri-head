<div class="card">
    <div class="card-header"><h4 class="card-title">Produktdokumente</h4></div>
    <div class="card-body">
        <form action="{{ route('product_documents.upload') }}"
              method="POST"
              enctype="multipart/form-data"
              class="dropzone"
              id="productDocumentsDropzone">
            @csrf
            <input type="hidden" id="hiddenDocProductId" name="product_id">
        </form>

        <div id="product-doc-gallery" class="row mt-3"></div>

        <template id="doc-template">
            <div class="col-md-4 mb-3 doc-card" data-id="">
                <div class="card">
                    <div class="card-body">
                        <input type="text" class="form-control form-control-sm doc-title-input mb-1" />
                        <a href="#" target="_blank" class="doc-link btn btn-sm btn-outline-primary btn-block mb-1">Anzeigen</a>
                        <button class="btn btn-sm btn-success btn-block save-doc-name">Speichern</button>
                        <button class="btn btn-sm btn-danger btn-block delete-doc-btn">Löschen</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
