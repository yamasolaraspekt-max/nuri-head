@php
    use Carbon\Carbon;

    $ext = strtolower(pathinfo((string) ($img->image ?? ''), PATHINFO_EXTENSION));
    $fileType = strtolower($img->file_type ?? $ext);

    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)
        || in_array($fileType, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true);

    $isPdf = $ext === 'pdf' || $fileType === 'pdf';
    $isWord = in_array($ext, ['doc', 'docx'], true) || in_array($fileType, ['doc', 'docx'], true);
    $isXls = in_array($ext, ['xls', 'xlsx'], true) || in_array($fileType, ['xls', 'xlsx'], true);

    if (($tab['filter'] ?? null) === 'image' && !$isImage)
        return;
    if (($tab['filter'] ?? null) === 'pdf' && !$isPdf)
        return;
    if (($tab['filter'] ?? null) === 'other' && ($isImage || $isPdf))
        return;

    $docModelType = $img->doc_model_type ?? 'image';

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */
    $fileUrl = ($docModelType === 'offer_attachment')
        ? ($img->custom_file_url ?? '#')
        : route('secure.image', $img->id) . '?v=' . (optional($img->updated_at)->timestamp ?? $img->id);

    $downloadUrl = ($docModelType === 'offer_attachment')
        ? ($img->custom_file_url ?? '#')
        : route('document.download', $img->id);

    $pdfPreviewUrl = $fileUrl . '#toolbar=0&navpanes=0&scrollbar=0&page=1&view=FitH';

    $createdAt = $img->created_at ? Carbon::parse($img->created_at) : null;
    $dateHuman = $createdAt ? $createdAt->format('d.m.Y H:i') : '—';

    $uploaderEmployeeId = $img->uploader_employee_id ?? $img->created_by ?? null;

    $uploaderName = $img->uploader_name
        ?? ($uploaderEmployeeId ? ('Mitarbeiter #' . $uploaderEmployeeId) : '—');

    $stageLabel = $docTypes[$img->stage] ?? ($img->stage ?? '—');

    $safeName = $img->image_name ?: 'Datei';
    $safeStage = $img->stage ?? '';
    $safeArticleGroup = $img->article_group ?? '';

    $displayType = strtoupper($fileType ?: $ext ?: 'DATEI');

    $searchText = trim(
        ($img->image_name ?? '') . ' ' .
        $stageLabel . ' ' .
        $fileType . ' ' .
        $dateHuman . ' ' .
        $uploaderName . ' ' .
        $uploaderEmployeeId
    );
@endphp

@if(($viewType ?? 'grid') === 'grid')
    <div class="docs-masonry-item docs-gallery-item" data-name="{{ e($safeName) }}" data-stage="{{ e($safeStage) }}"
        data-ext="{{ e($fileType) }}" data-type="{{ e($fileType) }}" data-date="{{ optional($createdAt)->toDateString() }}"
        data-search="{{ e($searchText) }}">

        <div class="docs-card">
            <div class="docs-thumb">
                @if($isImage)
                    <a href="{{ $fileUrl }}" class="docs-media js-docs-image-preview" data-docs-open-image
                        data-image-url="{{ $fileUrl }}" data-title="{{ e($safeName) }}" data-meta="{{ e($stageLabel) }}">
                        <img src="{{ $fileUrl }}" class="docs-img" alt="{{ e($safeName) }}" loading="lazy"
                            onerror="this.src='{{ $ph }}'">
                    </a>

                @elseif($isPdf)
                    <button type="button" data-docs-open-pdf data-pdf-url="{{ $fileUrl }}" data-pdf-title="{{ e($safeName) }}"
                        class="docs-media docs-pdf-thumb-btn" title="PDF Vorschau öffnen">

                        <div class="docs-pdf-thumb-frame">
                            <iframe src="{{ $pdfPreviewUrl }}" class="docs-pdf-thumb-iframe" loading="lazy"
                                tabindex="-1"></iframe>

                            <div class="docs-pdf-thumb-fallback">
                                <span class="docs-file-ico text-danger">{!! $icoPdf !!}</span>
                            </div>

                            <div class="docs-pdf-thumb-overlay">
                                <span class="docs-pdf-badge">PDF</span>

                                <span class="docs-pdf-open-label">
                                    {!! $icoEye ?? '' !!}
                                    Vorschau öffnen
                                </span>
                            </div>
                        </div>
                    </button>

                @else
                    <a href="{{ $downloadUrl }}" target="_blank" class="docs-media docs-center text-decoration-none">
                        @if($isWord)
                            <span class="docs-file-ico text-primary">{!! $icoWord !!}</span>
                        @elseif($isXls)
                            <span class="docs-file-ico text-success">{!! $icoXls !!}</span>
                        @else
                            <span class="docs-file-ico text-muted">{!! $icoFile !!}</span>
                        @endif

                        <small>{{ $displayType }}</small>
                    </a>
                @endif

                <div class="docs-card-actions">
                    @if($docModelType !== 'offer_attachment')
                        <button type="button" class="docs-action-btn" data-docs-edit data-id="{{ $img->id }}"
                            data-name="{{ e($safeName) }}" data-stage="{{ e($safeStage) }}"
                            data-article-group="{{ e($safeArticleGroup) }}" title="Bearbeiten">
                            {!! $icoEdit !!}
                        </button>
                    @endif

                    <a href="{{ $downloadUrl }}" class="docs-action-btn" target="_blank" title="Herunterladen">
                        {!! $icoDownload !!}
                    </a>

                    @if($docModelType !== 'offer_attachment')
                        <button type="button" class="docs-action-btn text-danger" data-docs-delete data-id="{{ $img->id }}"
                            title="Löschen">
                            {!! $icoTrash !!}
                        </button>
                    @endif
                </div>
            </div>

            <div class="docs-card-body">
                <div class="docs-title" title="{{ e($safeName) }}">
                    {{ $safeName }}
                </div>

                <div class="docs-meta-stack">
                    <span class="docs-type-badge">{{ $stageLabel }}</span>

                    <small>
                        Typ:
                        <strong>{{ $displayType }}</strong>
                    </small>

                    <small>
                        Hochgeladen:
                        <strong>{{ $dateHuman }}</strong>
                    </small>

                    <small>
                        Von:
                        <strong>{{ $uploaderName }}</strong>

                        @if($uploaderEmployeeId)
                            <span class="text-muted">#{{ $uploaderEmployeeId }}</span>
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="docs-list-row docs-gallery-item" data-name="{{ e($safeName) }}" data-stage="{{ e($safeStage) }}"
        data-ext="{{ e($fileType) }}" data-type="{{ e($fileType) }}" data-date="{{ optional($createdAt)->toDateString() }}"
        data-search="{{ e($searchText) }}">

        <div class="docs-list-thumb">
            @if($isImage)
                <a href="{{ $fileUrl }}" class="js-docs-image-preview" data-docs-open-image data-image-url="{{ $fileUrl }}"
                    data-title="{{ e($safeName) }}" data-meta="{{ e($stageLabel) }}">
                    <img src="{{ $fileUrl }}" alt="{{ e($safeName) }}" class="docs-list-img" loading="lazy"
                        onerror="this.src='{{ $ph }}'">
                </a>

            @elseif($isPdf)
                <button type="button" class="docs-list-pdf-thumb" data-docs-open-pdf data-pdf-url="{{ $fileUrl }}"
                    data-pdf-title="{{ e($safeName) }}" title="PDF Vorschau öffnen">

                    <iframe src="{{ $pdfPreviewUrl }}" class="docs-list-pdf-iframe" loading="lazy" tabindex="-1"></iframe>

                    <span class="docs-list-pdf-fallback">
                        {!! $icoPdf !!}
                    </span>

                    <span class="docs-list-pdf-badge">PDF</span>
                </button>

            @elseif($isWord)
                <span class="docs-list-ico text-primary">{!! $icoWord !!}</span>

            @elseif($isXls)
                <span class="docs-list-ico text-success">{!! $icoXls !!}</span>

            @else
                <span class="docs-list-ico text-muted">{!! $icoFile !!}</span>
            @endif
        </div>

        <div class="docs-list-info">
            <div class="docs-list-title" title="{{ e($safeName) }}">
                {{ $safeName }}
            </div>

            <div class="docs-list-meta-rich">
                <span class="docs-type-badge">{{ $stageLabel }}</span>
                <small>{{ $displayType }}</small>
                <small>{{ $dateHuman }}</small>

                <small>
                    Von:
                    <strong>{{ $uploaderName }}</strong>

                    @if($uploaderEmployeeId)
                        #{{ $uploaderEmployeeId }}
                    @endif
                </small>
            </div>
        </div>

        <div class="docs-list-actions">
            @if($isImage)
                <button type="button" class="docs-action-btn text-primary" data-docs-open-image data-image-url="{{ $fileUrl }}"
                    data-title="{{ e($safeName) }}" data-meta="{{ e($stageLabel) }}" title="Bild Vorschau">
                    {!! $icoEye !!}
                </button>
            @endif

            @if($isPdf)
                <button type="button" class="docs-action-btn text-primary" data-docs-open-pdf data-pdf-url="{{ $fileUrl }}"
                    data-pdf-title="{{ e($safeName) }}" title="PDF Vorschau">
                    {!! $icoEye !!}
                </button>
            @endif

            @if($docModelType !== 'offer_attachment')
                <button type="button" class="docs-action-btn" data-docs-edit data-id="{{ $img->id }}"
                    data-name="{{ e($safeName) }}" data-stage="{{ e($safeStage) }}"
                    data-article-group="{{ e($safeArticleGroup) }}" title="Bearbeiten">
                    {!! $icoEdit !!}
                </button>
            @endif

            <a href="{{ $downloadUrl }}" class="docs-action-btn" target="_blank" title="Herunterladen">
                {!! $icoDownload !!}
            </a>

            @if($docModelType !== 'offer_attachment')
                <button type="button" class="docs-action-btn text-danger" data-docs-delete data-id="{{ $img->id }}"
                    title="Löschen">
                    {!! $icoTrash !!}
                </button>
            @endif
        </div>
    </div>
@endif

<style>
    /* =========================================================
       IMAGE PREVIEW SAFETY
       Important: images must NOT use .glightbox anymore.
    ========================================================= */

    #docsShell .js-docs-image-preview {
        display: block;
        width: 100%;
        height: 100%;
        cursor: zoom-in;
        text-decoration: none !important;
    }

    #docsShell .js-docs-image-preview img {
        pointer-events: none;
    }

    /* =========================================================
       PDF THUMBNAILS
    ========================================================= */

    #docsShell .docs-pdf-thumb-btn {
        position: relative;
        width: 100%;
        height: 190px;
        padding: 0;
        border: 0;
        background: #525659;
        cursor: pointer;
        overflow: hidden;
    }

    #docsShell .docs-pdf-thumb-frame {
        position: relative;
        width: 100%;
        height: 100%;
        background: #525659;
        overflow: hidden;
    }

    #docsShell .docs-pdf-thumb-iframe {
        position: relative;
        z-index: 2;
        width: 128%;
        height: 250px;
        border: 0;
        background: #ffffff;
        transform: scale(.78);
        transform-origin: top left;
        pointer-events: none;
    }

    #docsShell .docs-pdf-thumb-fallback {
        position: absolute;
        inset: 0;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        background: #f8fafc;
    }

    #docsShell .docs-pdf-thumb-overlay {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 8px;
        padding: 10px;
        background: linear-gradient(to bottom,
                rgba(17, 24, 39, .05),
                rgba(17, 24, 39, .58));
        pointer-events: none;
    }

    #docsShell .docs-pdf-badge,
    #docsShell .docs-list-pdf-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 24px;
        padding: 0 9px;
        border-radius: 999px;
        background: #ef4444;
        color: #ffffff;
        font-size: 10px;
        font-weight: 900;
        letter-spacing: .04em;
    }

    #docsShell .docs-pdf-open-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 26px;
        padding: 0 9px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .92);
        color: #111827;
        font-size: 10px;
        font-weight: 900;
    }

    #docsShell .docs-list-pdf-thumb {
        position: relative;
        width: 58px;
        height: 58px;
        padding: 0;
        border: 0;
        border-radius: 14px;
        background: #525659;
        overflow: hidden;
        cursor: pointer;
    }

    #docsShell .docs-list-pdf-iframe {
        position: relative;
        z-index: 2;
        width: 128px;
        height: 160px;
        border: 0;
        background: #ffffff;
        transform: scale(.46);
        transform-origin: top left;
        pointer-events: none;
    }

    #docsShell .docs-list-pdf-fallback {
        position: absolute;
        inset: 0;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        background: #f8fafc;
    }

    #docsShell .docs-list-pdf-fallback svg {
        width: 30px;
        height: 30px;
    }

    #docsShell .docs-list-pdf-badge {
        position: absolute;
        left: 5px;
        bottom: 5px;
        z-index: 3;
        min-height: 18px;
        padding: 0 6px;
        font-size: 8px;
    }
</style>