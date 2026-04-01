
<div class="form-group">
    <label for="filterStage">Nur diese Stufe anzeigen:</label>
    <select id="filterStage" class="form-control">
        <option value="">Alle</option>
        <option value="order">Kundenauftrag</option>
        <option value="confirmed_order">Auftragsbestätigung</option>
        <option value="offer">Angebot</option>
    </select>
</div>

<div class="gallery-wrapper">
@forelse($files as $file)
    <div class="gallery-item">
        @if(Str::startsWith($file->file_type, 'image'))
            <a href="{{ asset('uploads/' . $file->image) }}" class="glightbox" data-gallery="gallery">
                <img src="{{ asset('uploads/' . $file->image) }}" alt="{{ $file->image_name }}">
            </a>
        @else
            <a href="{{ asset('uploads/' . $file->image) }}" target="_blank">
                <div class="file-icon d-flex flex-column align-items-center justify-content-center bg-light" style="height: 120px;">
                    <i class="fa fa-file-alt fa-2x text-muted"></i>
                    <small>{{ pathinfo($file->image, PATHINFO_EXTENSION) }}</small>
                </div>
            </a>
        @endif

        <div class="gallery-controls">
            <input type="text" class="form-control form-control-sm rename-input mb-1" value="{{ $file->image_name }}" data-id="{{ $file->id }}">
            <button class="btn btn-sm btn-outline-danger btn-block delete-file" data-id="{{ $file->id }}">Löschen</button>
        </div>
    </div>
@empty
    <div class="text-muted">Keine Dateien gefunden.</div>
@endforelse
</div>
