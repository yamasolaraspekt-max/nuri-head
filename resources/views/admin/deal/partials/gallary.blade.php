<div class="form-group">
    <label for="filterStage">Nur diese Stufe anzeigen:</label>
    <select id="filterStage" class="form-control">
        <option value="">Alle</option>
        <option value="order">Kundenauftrag</option>
        <option value="confirmed_order">Auftragsbestätigung</option>
        <option value="offer">Angebot</option>
        <option value="offer_folder">Auftrag Ordner</option>
    </select>
</div>

<div class="gallery-wrapper">
    @forelse(($images ?? collect())->filter() as $file)
        @if(!$file)
            @continue
        @endif

        @php
            $source = $file->source ?? 'image';

            $searchText = strtolower(trim(
                ($file->image_name ?? '') . ' ' .
                ($file->file_type ?? '') . ' ' .
                ($file->stage ?? '') . ' ' .
                ($file->status ?? '') . ' ' .
                ($source ?? '')
            ));
        @endphp

        <div
            class="gallery-item"
            data-search="{{ $searchText }}"
            data-stage="{{ strtolower($file->stage ?? '') }}"
            data-type="{{ strtolower($file->file_type ?? '') }}"
            data-name="{{ strtolower($file->image_name ?? '') }}"
            data-source="{{ $source }}"
        >
            @if(!empty($file->is_image) && !empty($file->url))
                <a href="{{ $file->url }}" class="glightbox" data-gallery="deal-gallery">
                    <img src="{{ $file->url }}" alt="{{ $file->image_name ?? 'Datei' }}">
                </a>
            @elseif(!empty($file->is_pdf) && !empty($file->url))
                <a href="{{ $file->url }}" target="_blank" class="file-icon d-flex flex-column align-items-center justify-content-center bg-light" style="height:120px;">
                    <i class="fa fa-file-pdf-o fa-2x text-danger mb-1"></i>
                    <small>PDF</small>
                </a>
            @else
                <a href="{{ $file->download_url ?? '#' }}" class="file-icon d-flex flex-column align-items-center justify-content-center bg-light" style="height:120px;">
                    <i class="fa fa-file-o fa-2x text-muted mb-1"></i>
                    <small>{{ strtoupper($file->file_type ?? 'FILE') }}</small>
                </a>
            @endif

            <div class="gallery-controls">
                <input
                    type="text"
                    class="form-control form-control-sm rename-input mb-2"
                    value="{{ $file->image_name ?? '' }}"
                    data-id="{{ $file->id ?? '' }}"
                    data-source="{{ $source }}"
                >

                <div class="d-flex align-items-center justify-content-between" style="gap:8px;">
                    <small class="text-muted">
                        {{ strtoupper($file->file_type ?? 'FILE') }}

                        @if(!empty($file->stage))
                            · {{ $file->stage }}
                        @endif

                        @if($source === 'attachment')
                            · Ordner
                        @else
                            · {{ $file->status ?? 'order' }}
                        @endif
                    </small>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger delete-file"
                        data-id="{{ $file->id ?? '' }}"
                        data-source="{{ $source }}"
                        @if(empty($file->id)) disabled @endif
                    >
                        Löschen
                    </button>
                </div>

                @if(!empty($file->download_url))
                    <a href="{{ $file->download_url }}" class="btn btn-sm btn-outline-primary btn-block mt-2">
                        Download
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="text-muted w-100">Keine Dateien gefunden.</div>
    @endforelse
</div>