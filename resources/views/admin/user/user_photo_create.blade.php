@extends('admin.layouts.app')

@section('title')
    {{ auth()->user()->name }} Profile Photo
@endsection

@section('style')
<style>
    .photo-page-wrap {
        padding: 120px 24px 40px;
        background: #f8fafc;
        min-height: 100vh;
    }

    .photo-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 20px 40px rgba(15, 23, 42, .08);
        overflow: hidden;
    }

    .photo-card-header {
        padding: 24px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .photo-title {
        font-size: 22px;
        font-weight: 800;
        color: #111827;
        margin: 0;
    }

    .photo-subtitle {
        color: #6b7280;
        margin-top: 6px;
    }

    .photo-body {
        padding: 24px;
    }

    .photo-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
    }

    @media (max-width: 900px) {
        .photo-grid {
            grid-template-columns: 1fr;
        }
    }

    .current-photo-box,
    .upload-box,
    .crop-box {
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px;
        background: #ffffff;
    }

    .current-photo {
        width: 180px;
        height: 180px;
        border-radius: 999px;
        object-fit: cover;
        border: 4px solid #f3f4f6;
        display: block;
        margin: 0 auto 16px;
    }

    .photo-placeholder {
        width: 180px;
        height: 180px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #6b7280;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 52px;
        font-weight: 800;
        border: 4px solid #e5e7eb;
    }

    .box-title {
        font-size: 16px;
        font-weight: 800;
        color: #111827;
        margin-bottom: 10px;
    }

    .box-help {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 14px;
    }

    .file-input {
        width: 100%;
        border: 1px dashed #cbd5e1;
        border-radius: 14px;
        padding: 16px;
        background: #f8fafc;
    }

    .crop-area {
        margin-top: 18px;
        display: none;
    }

    .crop-frame {
        width: 320px;
        height: 320px;
        max-width: 100%;
        border-radius: 18px;
        overflow: hidden;
        border: 2px dashed #93c21c;
        position: relative;
        background: #f3f4f6;
        margin-bottom: 16px;
    }

    .crop-frame img {
        position: absolute;
        cursor: grab;
        user-select: none;
        max-width: none;
    }

    .preview-row {
        display: flex;
        gap: 20px;
        align-items: center;
        flex-wrap: wrap;
        margin-top: 18px;
    }

    .preview-circle {
        width: 120px;
        height: 120px;
        border-radius: 999px;
        border: 3px solid #e5e7eb;
        object-fit: cover;
        background: #f3f4f6;
    }

    .range-group {
        margin-top: 14px;
    }

    .range-group label {
        display: block;
        font-weight: 700;
        margin-bottom: 6px;
        color: #374151;
    }

    .range-group input {
        width: 100%;
    }

    .btn-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-top: 22px;
    }

    .btn-primary-soft {
        background: #93c21c;
        color: white;
        border: none;
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 800;
    }

    .btn-primary-soft:hover {
        background: #7baa18;
        color: white;
    }

    .btn-secondary-soft {
        background: #f3f4f6;
        color: #111827;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 800;
    }

    .alert-soft-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 18px;
        font-weight: 700;
    }

    .alert-soft-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        border-radius: 14px;
        padding: 12px 16px;
        margin-bottom: 18px;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="photo-page-wrap">
            <div class="photo-card">
                <div class="photo-card-header">
                    <div>
                        <h4 class="photo-title">Profilbild aktualisieren</h4>
                        <div class="photo-subtitle">
                            Das Bild wird automatisch für Benutzer und Mitarbeiter gespeichert.
                        </div>
                    </div>
                </div>

                <div class="photo-body">
                    @if(session('save_msg'))
                        <div class="alert-soft-success">
                            {{ session('save_msg') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert-soft-error">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div class="photo-grid">
                        <div class="current-photo-box">
                            <div class="box-title">Aktuelles Bild</div>

                            @if(!empty($data->image) && file_exists(public_path('images/user/' . $data->image)))
                                <img
                                    src="{{ asset('images/user/' . $data->image) }}"
                                    alt="User photo"
                                    class="current-photo"
                                >
                            @else
                                <div class="photo-placeholder">
                                    {{ strtoupper(substr($data->email ?? 'U', 0, 1)) }}
                                </div>
                            @endif

                            <div class="box-help text-center">
                                Benutzer-ID: {{ $data->id }} <br>
                                Mitarbeiter-ID: {{ auth()->user()->name }}
                            </div>
                        </div>

                        <div class="upload-box">
                            <form
                                action="{{ route('user.photo.save') }}"
                                method="post"
                                enctype="multipart/form-data"
                                id="photoUploadForm"
                            >
                                @csrf

                                <input type="hidden" name="id" value="{{ auth()->id() }}">
                                <input type="hidden" name="cropped_image" id="croppedImageInput">

                                <div class="box-title">Neues Bild auswählen</div>
                                <div class="box-help">
                                    Wähle ein Bild aus, schneide es zu und klicke danach auf Upload.
                                </div>

                                <input
                                    type="file"
                                    name="image"
                                    id="imageInput"
                                    class="form-control file-input"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                >

                                <div class="crop-area" id="cropArea">
                                    <div class="box-title mt-2">Bild zuschneiden</div>

                                    <div class="crop-frame" id="cropFrame">
                                        <img id="cropImage" alt="Crop image">
                                    </div>

                                    <div class="range-group">
                                        <label for="zoomRange">Zoom</label>
                                        <input type="range" id="zoomRange" min="1" max="3" step="0.01" value="1">
                                    </div>

                                    <div class="preview-row">
                                        <div>
                                            <div class="box-title">Vorschau</div>
                                            <img id="previewImage" class="preview-circle" alt="Preview">
                                        </div>

                                        <div class="box-help">
                                            Ausgabegröße: 512 × 512 px <br>
                                            Format: PNG <br>
                                            Wird gespeichert in:
                                            <br>
                                            <code>images/user</code>
                                            <br>
                                            <code>images/employee</code>
                                        </div>
                                    </div>
                                </div>

                                <div class="btn-row">
                                    <button type="submit" class="btn-primary-soft" id="uploadBtn" disabled>
                                        Bild hochladen
                                    </button>

                                    <button type="button" class="btn-secondary-soft" id="resetBtn">
                                        Zurücksetzen
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(() => {
    'use strict';

    const imageInput = document.getElementById('imageInput');
    const cropArea = document.getElementById('cropArea');
    const cropFrame = document.getElementById('cropFrame');
    const cropImage = document.getElementById('cropImage');
    const previewImage = document.getElementById('previewImage');
    const zoomRange = document.getElementById('zoomRange');
    const croppedImageInput = document.getElementById('croppedImageInput');
    const uploadBtn = document.getElementById('uploadBtn');
    const resetBtn = document.getElementById('resetBtn');
    const form = document.getElementById('photoUploadForm');

    let originalImage = new Image();
    let imageLoaded = false;

    let posX = 0;
    let posY = 0;
    let startX = 0;
    let startY = 0;
    let dragging = false;

    let baseWidth = 0;
    let baseHeight = 0;
    let zoom = 1;

    const frameSize = 320;
    const outputSize = 512;

    imageInput.addEventListener('change', handleFileSelect);
    zoomRange.addEventListener('input', handleZoom);
    resetBtn.addEventListener('click', resetCropper);

    cropImage.addEventListener('mousedown', startDrag);
    window.addEventListener('mousemove', onDrag);
    window.addEventListener('mouseup', endDrag);

    cropImage.addEventListener('touchstart', startDragTouch, { passive: false });
    window.addEventListener('touchmove', onDragTouch, { passive: false });
    window.addEventListener('touchend', endDrag);

    form.addEventListener('submit', function (event) {
        if (!imageLoaded || !croppedImageInput.value) {
            event.preventDefault();
            alert('Bitte zuerst ein Bild auswählen und zuschneiden.');
            return false;
        }
    });

    function handleFileSelect(event) {
        const file = event.target.files[0];

        if (!file) {
            resetCropper();
            return;
        }

        if (!file.type.match(/^image\/(png|jpeg|jpg|webp)$/)) {
            alert('Bitte nur JPG, PNG oder WEBP hochladen.');
            resetCropper();
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Das Bild ist zu groß. Maximal 5 MB.');
            resetCropper();
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            originalImage = new Image();

            originalImage.onload = function () {
                imageLoaded = true;
                cropArea.style.display = 'block';
                uploadBtn.disabled = false;
                zoomRange.value = 1;
                zoom = 1;

                setupImage();
                renderCropImage();
                updatePreview();
            };

            originalImage.src = e.target.result;
            cropImage.src = e.target.result;
        };

        reader.readAsDataURL(file);
    }

    function setupImage() {
        const imgRatio = originalImage.width / originalImage.height;

        if (imgRatio >= 1) {
            baseHeight = frameSize;
            baseWidth = frameSize * imgRatio;
        } else {
            baseWidth = frameSize;
            baseHeight = frameSize / imgRatio;
        }

        posX = (frameSize - baseWidth) / 2;
        posY = (frameSize - baseHeight) / 2;

        clampPosition();
    }

    function handleZoom() {
        const previousZoom = zoom;
        zoom = parseFloat(zoomRange.value);

        const centerX = frameSize / 2;
        const centerY = frameSize / 2;

        posX = centerX - ((centerX - posX) / previousZoom) * zoom;
        posY = centerY - ((centerY - posY) / previousZoom) * zoom;

        clampPosition();
        renderCropImage();
        updatePreview();
    }

    function renderCropImage() {
        const displayWidth = baseWidth * zoom;
        const displayHeight = baseHeight * zoom;

        cropImage.style.width = displayWidth + 'px';
        cropImage.style.height = displayHeight + 'px';
        cropImage.style.left = posX + 'px';
        cropImage.style.top = posY + 'px';
    }

    function clampPosition() {
        const displayWidth = baseWidth * zoom;
        const displayHeight = baseHeight * zoom;

        if (displayWidth <= frameSize) {
            posX = (frameSize - displayWidth) / 2;
        } else {
            if (posX > 0) posX = 0;
            if (posX + displayWidth < frameSize) posX = frameSize - displayWidth;
        }

        if (displayHeight <= frameSize) {
            posY = (frameSize - displayHeight) / 2;
        } else {
            if (posY > 0) posY = 0;
            if (posY + displayHeight < frameSize) posY = frameSize - displayHeight;
        }
    }

    function startDrag(event) {
        if (!imageLoaded) return;

        dragging = true;
        startX = event.clientX - posX;
        startY = event.clientY - posY;
        cropImage.style.cursor = 'grabbing';
    }

    function onDrag(event) {
        if (!dragging) return;

        posX = event.clientX - startX;
        posY = event.clientY - startY;

        clampPosition();
        renderCropImage();
        updatePreview();
    }

    function startDragTouch(event) {
        if (!imageLoaded) return;

        event.preventDefault();

        const touch = event.touches[0];
        dragging = true;
        startX = touch.clientX - posX;
        startY = touch.clientY - posY;
    }

    function onDragTouch(event) {
        if (!dragging) return;

        event.preventDefault();

        const touch = event.touches[0];
        posX = touch.clientX - startX;
        posY = touch.clientY - startY;

        clampPosition();
        renderCropImage();
        updatePreview();
    }

    function endDrag() {
        dragging = false;
        cropImage.style.cursor = 'grab';
    }

    function updatePreview() {
        if (!imageLoaded) return;

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        canvas.width = outputSize;
        canvas.height = outputSize;

        const displayWidth = baseWidth * zoom;
        const displayHeight = baseHeight * zoom;

        const sourceX = (-posX / displayWidth) * originalImage.width;
        const sourceY = (-posY / displayHeight) * originalImage.height;
        const sourceSizeW = (frameSize / displayWidth) * originalImage.width;
        const sourceSizeH = (frameSize / displayHeight) * originalImage.height;

        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'high';

        ctx.drawImage(
            originalImage,
            sourceX,
            sourceY,
            sourceSizeW,
            sourceSizeH,
            0,
            0,
            outputSize,
            outputSize
        );

        const dataUrl = canvas.toDataURL('image/png', 0.92);

        previewImage.src = dataUrl;
        croppedImageInput.value = dataUrl;
    }

    function resetCropper() {
        imageInput.value = '';
        croppedImageInput.value = '';
        previewImage.src = '';
        cropImage.src = '';
        cropArea.style.display = 'none';
        uploadBtn.disabled = true;

        imageLoaded = false;
        posX = 0;
        posY = 0;
        zoom = 1;
        zoomRange.value = 1;
    }
})();
</script>
@endsection