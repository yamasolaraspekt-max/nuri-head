<form action="{{ url('/product_images/upload') }}"
      method="POST"
      enctype="multipart/form-data"
      class="dropzone"
      id="productImagesDropzone">
    @csrf
    <input type="hidden" name="product_id" id="hiddenProductId">
</form>

<div id="product-image-gallery" class="row mt-3"></div>

<template id="image-template">
    <div class="col-md-3 mb-3 image-card" data-id="">
        <div class="card">
            <img src="" class="card-img-top" alt="Produktbild">
            <div class="card-body p-2">
                <input type="text" class="form-control form-control-sm image-name-input" placeholder="Bildname">
                <button class="btn btn-sm btn-primary btn-block mt-1 save-name-btn">Speichern</button>
                <button class="btn btn-sm btn-danger btn-block mt-1 delete-image-btn">Löschen</button>
            </div>
        </div>
    </div>
</template>
