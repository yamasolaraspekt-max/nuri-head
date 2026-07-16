@php
use Carbon\Carbon;

$icoUpload = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5"/><path d="M12 3v12"/><path d="M7 8l5-5 5 5"/></svg>';
$icoDownload = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5"/><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/></svg>';
$icoTrash = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>';
$icoEdit = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';
$icoEye = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
$icoGrid = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="8" height="8"/><rect x="13" y="3" width="8" height="8"/><rect x="3" y="13" width="8" height="8"/><rect x="13" y="13" width="8" height="8"/></svg>';
$icoList = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>';

$icoPdf = '<svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg>';
$icoWord = '<svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 12l1.5 6L11 14l1.5 4L14 12"/></svg>';
$icoXls = '<svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h8"/><path d="M8 9h4"/></svg>';
$icoFile = '<svg viewBox="0 0 24 24" width="36" height="36" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>';

$ctxCustomerId = $documentContext['customer_id'] ?? request('customer_id');
$ctxAlternativeId = $documentContext['alternative_id'] ?? request('alternative_id');
$ctxProductId = $documentContext['product_id'] ?? request('product_id');
$ctxProductListId = $documentContext['product_list_id'] ?? request('product_list_id');

$customerDisplayName =
    $documentCustomer->full_name
    ?? $documentCustomer->name
    ?? $documentCustomer->customer_name
    ?? trim(($documentCustomer->first_name ?? '') . ' ' . ($documentCustomer->last_name ?? ''))
    ?: ('Kunde #' . ($ctxCustomerId ?: '—'));

$productDisplayName =
    $documentProduct->display_name
    ?? $documentProduct->article_group
    ?? $documentProduct->name
    ?? $documentProduct->title
    ?? ('Produkt #' . ($ctxProductId ?: '—'));

$ph = asset('images/icons/placeholder.svg');

$docTypes = [
    'customer' => 'Kunde',
    'montage' => 'Montage',
    'Reklamation' => 'Reklamation',
    'Rechnung' => 'Rechnung',
    'Auftrag' => 'Auftrag',
    'AuftragBeshtitgung' => 'Auftragsbestätigung',
    'Angebot' => 'Angebot',
    'Wartung' => 'Wartung',
    'Ticket' => 'Ticket',
    'end' => 'Abgeschlossen',
    'Other' => 'Sonstiges',
];

$tabs = [
    'allTab' => ['label' => 'Alle Dateien', 'filter' => null],
    'imagesTab' => ['label' => 'Bilder', 'filter' => 'image'],
    'pdfTab' => ['label' => 'PDFs', 'filter' => 'pdf'],
    'othersTab' => ['label' => 'Sonstiges', 'filter' => 'other'],
];

$safeImages = collect($images ?? []);

$docsRoutes = [
    'load' => url('/document/load'),
    'upload' => route('document.upload'),
    'deleteBase' => url('/document/delete'),
    'downloadBase' => url('/document/download'),
    'updateDetails' => route('document.updateDetails'),
];

$docsRoutesJson = e(json_encode($docsRoutes));
$docTypesJson = e(json_encode($docTypes));
@endphp

<div class="docs-shell"
     id="docsShell"
     data-view="grid"
     data-customer-id="{{ $ctxCustomerId }}"
     data-alternative-id="{{ $ctxAlternativeId }}"
     data-product-id="{{ $ctxProductId }}"
     data-product-list-id="{{ $ctxProductListId }}"
     data-routes="{{ $docsRoutesJson }}"
     data-doc-types="{{ $docTypesJson }}">

    <div class="docs-context-card">
        <div class="docs-context-left">
            <div class="docs-context-icon">{!! $icoUpload !!}</div>

            <div class="docs-context-main">
                <div class="docs-context-kicker">Bilder & Dokumente</div>
                <div class="docs-context-title">{{ $customerDisplayName }}</div>
                <div class="docs-context-sub">
                    <span>Produkt: <strong>{{ $productDisplayName }}</strong></span>
                    <span>•</span>
                    <span>Kunde #{{ $ctxCustomerId ?: '—' }}</span>
                    <span>•</span>
                    <span>Alternative #{{ $ctxAlternativeId ?: '—' }}</span>
                </div>
            </div>
        </div>

        <div class="docs-context-right">
            <span class="docs-context-pill">{{ $safeImages->count() }} Dateien</span>
        </div>
    </div>

    <div class="docs-head">
        <div class="docs-header-top">
            <ul class="docs-tabs" id="galleryTabs" role="tablist">
                @foreach($tabs as $tabId => $tab)
                    <li class="docs-tab-item">
                        <a class="docs-tab-link {{ $loop->first ? 'active' : '' }}"
                           href="#{{ $tabId }}"
                           data-tab="#{{ $tabId }}"
                           role="tab">
                            {{ $tab['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="docs-view-toggle">
                <button type="button" class="docs-view-btn is-active" data-view="grid" title="Gitter">
                    {!! $icoGrid !!}
                </button>

                <button type="button" class="docs-view-btn" data-view="list" title="Liste">
                    {!! $icoList !!}
                </button>
            </div>
        </div>

        <div class="docs-header-actions">
            <div class="docs-action-item">
                <label class="docs-filter-label">Suche</label>
                <input type="text"
                       id="searchImage"
                       class="form-control docs-search-input"
                       placeholder="Name, Typ, Mitarbeiter oder Datum suchen...">
            </div>

            <div class="docs-action-item">
                <label class="docs-filter-label">Typ</label>
                <select id="stageFilter" class="form-control docs-select">
                    <option value="">Alle Typen</option>
                    @foreach($docTypes as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <div class="docs-new-upload-action docs-new-upload-action--fixed">
                <button type="button"
                        class="docs-new-image-btn"
                        id="btnOpenDocumentUpload"
                        data-docs-toggle-upload>
                    {!! $icoUpload !!}
                    Neue Datei / Neues Bild
                </button>
            </div>
        </div>

        <div class="docs-upload-panel" id="docsUploadPanel" style="display:none;">
            <form action="{{ route('document.upload') }}"
                  method="POST"
                  class="docs-upload-card"
                  id="documentUploadForm"
                  enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="customer_id" value="{{ $ctxCustomerId }}">
                <input type="hidden" name="alternative_id" value="{{ $ctxAlternativeId }}">
                <input type="hidden" name="product_id" id="image_product" value="{{ $ctxProductId }}">

                <div class="docs-upload-top">
                    <div>
                        <div class="docs-upload-title">Neue Datei hochladen</div>
                        <div class="docs-upload-sub">Datei auswählen, Typ setzen und dann hochladen.</div>
                    </div>

                    <button type="button"
                            class="docs-upload-close"
                            id="btnCloseDocumentUpload"
                            data-docs-close-upload
                            title="Upload schließen">
                        &times;
                    </button>
                </div>

                <div class="docs-upload-grid">
                    <label class="docs-upload-drop"
                           id="docsUploadDropArea"
                           for="documentUploadFile">
                        {!! $icoUpload !!}
                        <span>Datei auswählen</span>
                        <small>JPG, PNG, WEBP, GIF, PDF, DOC, DOCX, XLS, XLSX</small>
                    </label>

                    <input type="file"
                           id="documentUploadFile"
                           name="file"
                           accept=".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx,.xls,.xlsx"
                           style="display:none;">

                    <div class="docs-upload-fields">
                        <label class="docs-field-label">
                            Bild-/Dokumenttyp <span>*</span>
                        </label>

                        <select name="stage"
                                class="form-control form-control-sm dz-type-select"
                                id="dzStageSelect"
                                required>
                            <option value="">Typ auswählen</option>
                            @foreach($docTypes as $key => $val)
                                <option value="{{ $key }}">{{ $val }}</option>
                            @endforeach
                        </select>

                        <button type="button" class="docs-upload-btn" id="docsUploadSubmitBtn">
                            {!! $icoUpload !!}
                            Hochladen
                        </button>
                    </div>
                </div>

                <div class="docs-upload-progress" id="docsUploadProgress" style="display:none;">
                    <div class="docs-upload-progress-top">
                        <span id="docsUploadProgressText">Upload wird vorbereitet...</span>
                        <strong id="docsUploadProgressPercent">0%</strong>
                    </div>

                    <div class="docs-upload-progress-track">
                        <div class="docs-upload-progress-bar" id="docsUploadProgressBar"></div>
                    </div>

                    <small id="docsUploadProgressMeta" class="docs-upload-progress-meta"></small>
                </div>
            </form>
        </div>
    </div>

    <div class="docs-body">
        <div class="docs-tab-content" id="galleryTabsContent">
            @foreach($tabs as $tabId => $tab)
                <div class="docs-tab-pane {{ $loop->first ? 'show active' : '' }}"
                     id="{{ $tabId }}"
                     role="tabpanel">

                    <div class="docs-view-grid">
                        <div class="docs-masonry">
                            @forelse($safeImages as $img)
                                @include('admin.new_leads.layouts.partials.document-card', [
            'img' => $img,
            'tab' => $tab,
            'docTypes' => $docTypes,
            'ph' => $ph,
            'icoPdf' => $icoPdf,
            'icoWord' => $icoWord,
            'icoXls' => $icoXls,
            'icoFile' => $icoFile,
            'icoEdit' => $icoEdit,
            'icoEye' => $icoEye,
            'icoDownload' => $icoDownload,
            'icoTrash' => $icoTrash,
            'viewType' => 'grid',
        ])
                            @empty
                                <div class="docs-empty">
                                    <div class="docs-empty-title">Noch keine Dateien vorhanden</div>
                                    <div class="docs-empty-sub">Klicke auf „Neue Datei / Neues Bild“, um eine Datei hochzuladen.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="docs-view-list">
                        <div class="docs-list">
                            @forelse($safeImages as $img)
                                @include('admin.new_leads.layouts.partials.document-card', [
            'img' => $img,
            'tab' => $tab,
            'docTypes' => $docTypes,
            'ph' => $ph,
            'icoPdf' => $icoPdf,
            'icoWord' => $icoWord,
            'icoXls' => $icoXls,
            'icoFile' => $icoFile,
            'icoEdit' => $icoEdit,
            'icoEye' => $icoEye,
            'icoDownload' => $icoDownload,
            'icoTrash' => $icoTrash,
            'viewType' => 'list',
        ])
                            @empty
                                <div class="docs-empty">
                                    <div class="docs-empty-title">Noch keine Dateien vorhanden</div>
                                    <div class="docs-empty-sub">Klicke auf „Neue Datei / Neues Bild“, um eine Datei hochzuladen.</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="docs-pdf-modal" id="pdfViewerModal" aria-hidden="true">
        <div class="docs-pdf-backdrop" data-docs-close-pdf></div>

        <div class="docs-pdf-dialog" role="dialog" aria-modal="true">
            <div class="docs-pdf-header">
                <h5 id="pdfViewerTitle">PDF Vorschau</h5>

                <button type="button"
                        class="docs-pdf-close"
                        data-docs-close-pdf
                        aria-label="Schließen">
                    &times;
                </button>
            </div>

            <div class="docs-pdf-body">
                <iframe id="pdfViewerIframe"
                        src=""
                        title="PDF Vorschau"></iframe>
            </div>
        </div>
    </div>
</div>
 


<style>
    :root {
        --docs-bg: #ffffff;
        --docs-soft: #f9fafb;
        --docs-soft-green: #f4fae7;
        --docs-text: #1f2937;
        --docs-muted: #6b7280;
        --docs-border: #e5e7eb;
        --docs-primary: var(--sa-accent);
        --docs-primary-hover: var(--sa-accent-hover);
        --docs-blue: #74b2d4;
        --docs-blue-soft: #eff6ff;
        --docs-danger: #ef4444;
        --docs-radius: 16px;
        --docs-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
        --docs-shadow: 0 12px 30px rgba(15, 23, 42, .12);
    }

    #docsShell,
    #docsShell * {
        box-sizing: border-box;
    }

    #docsShell {
        background: #ffffff;
        color: var(--docs-text);
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        max-height: calc(100vh - 140px);
        overflow: hidden;
        font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    #docsShell .docs-context-card,
    #docsShell .docs-head,
    #docsShell .docs-card,
    #docsShell .docs-list-row {
        background: #ffffff;
        border: 1px solid var(--docs-border);
        border-radius: var(--docs-radius);
        box-shadow: var(--docs-shadow-sm);
    }

    #docsShell .docs-context-card {
        padding: 16px;
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        flex-shrink: 0;
    }

    #docsShell .docs-context-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    #docsShell .docs-context-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: var(--docs-soft-green);
        color: var(--docs-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    #docsShell .docs-context-main {
        min-width: 0;
    }

    #docsShell .docs-context-kicker,
    #docsShell .docs-filter-label,
    #docsShell .docs-field-label {
        font-size: 11px;
        font-weight: 900;
        color: var(--docs-muted);
        text-transform: uppercase;
        letter-spacing: .07em;
    }

    #docsShell .docs-context-title {
        font-size: 18px;
        font-weight: 900;
        color: #111827;
        line-height: 1.2;
        margin-top: 3px;
    }

    #docsShell .docs-context-sub {
        font-size: 13px;
        color: var(--docs-muted);
        margin-top: 5px;
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
    }

    #docsShell .docs-context-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 0 12px;
        border-radius: 999px;
        background: var(--docs-blue-soft);
        color: var(--docs-blue);
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    #docsShell .docs-head {
        padding: 14px;
        flex-shrink: 0;
    }

    #docsShell .docs-header-top {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 12px !important;
        flex-wrap: wrap !important;
        margin-bottom: 14px;
    }

    #docsShell .docs-tabs {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        width: auto !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
        border: 0 !important;
    }

    #docsShell .docs-tab-item {
        display: inline-flex !important;
        width: auto !important;
        flex: 0 0 auto !important;
        margin: 0 !important;
        padding: 0 !important;
        list-style: none !important;
    }

    #docsShell .docs-tab-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        width: auto !important;
        min-height: 36px !important;
        padding: 0 14px !important;
        white-space: nowrap !important;
        border-radius: 999px !important;
        border: 1px solid var(--docs-border) !important;
        background: #ffffff !important;
        color: var(--docs-text) !important;
        text-decoration: none !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        line-height: 1 !important;
        transition: all .18s ease;
    }

    #docsShell .docs-tab-link.active,
    #docsShell .docs-tab-link:hover {
        background: var(--docs-primary) !important;
        border-color: var(--docs-primary) !important;
        color: #ffffff !important;
    }

    #docsShell .docs-view-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--docs-soft);
        border-radius: 999px;
        padding: 4px;
        margin-left: auto;
    }

    #docsShell .docs-view-btn {
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 999px;
        background: transparent;
        color: var(--docs-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .18s ease;
    }

    #docsShell .docs-view-btn:hover,
    #docsShell .docs-view-btn.is-active {
        background: #ffffff;
        color: var(--docs-primary);
        border: 1px solid var(--docs-border);
    }

    #docsShell .docs-header-actions {
        display: grid;
        grid-template-columns: minmax(220px, 1fr) 190px auto;
        gap: 12px;
        align-items: end;
    }

    #docsShell .docs-action-item {
        display: flex;
        flex-direction: column;
        gap: 6px;
        min-width: 0;
    }

    #docsShell .docs-search-input,
    #docsShell .docs-select,
    #docsShell .dz-type-select {
        border: 1px solid var(--docs-border) !important;
        border-radius: 10px !important;
        min-height: 40px;
        color: var(--docs-text) !important;
        background: #ffffff !important;
        box-shadow: none !important;
        font-size: 13px;
    }

    #docsShell .docs-search-input:focus,
    #docsShell .docs-select:focus,
    #docsShell .dz-type-select:focus {
        border-color: var(--docs-primary) !important;
        box-shadow: 0 0 0 3px rgba(147, 194, 28, .16) !important;
    }

    #docsShell .docs-new-upload-action,
    #docsShell .docs-new-upload-action--fixed {
        display: flex !important;
        align-items: flex-end !important;
        justify-content: flex-end !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    #docsShell #btnOpenDocumentUpload,
    #docsShell .docs-new-image-btn {
        border: 0 !important;
        background: var(--docs-primary) !important;
        color: #ffffff !important;
        border-radius: 10px !important;
        min-height: 40px !important;
        padding: 0 14px !important;
        font-size: 12px !important;
        font-weight: 900 !important;
        display: inline-flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 7px !important;
        cursor: pointer !important;
        white-space: nowrap !important;
        visibility: visible !important;
        opacity: 1 !important;
        transition: all .18s ease;
    }

    #docsShell #btnOpenDocumentUpload:hover,
    #docsShell .docs-new-image-btn:hover {
        background: var(--docs-primary-hover) !important;
        transform: translateY(-1px);
    }

    #docsShell .docs-upload-panel {
        margin-top: 14px;
        border-top: 1px solid var(--docs-border);
        padding-top: 14px;
    }

    #docsShell .docs-upload-card {
        border: 1px solid var(--docs-border);
        background: #ffffff;
        border-radius: 16px;
        padding: 12px;
        width: 100%;
        max-width: 760px;
        box-shadow: var(--docs-shadow-sm);
    }

    #docsShell .docs-upload-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 10px;
    }

    #docsShell .docs-upload-title {
        font-size: 13px;
        font-weight: 900;
        color: #111827;
    }

    #docsShell .docs-upload-sub,
    #docsShell .docs-upload-note {
        font-size: 11px;
        color: var(--docs-muted);
        margin-top: 3px;
    }

    #docsShell .docs-upload-close {
        border: 0;
        background: var(--docs-soft);
        color: var(--docs-muted);
        width: 32px;
        height: 32px;
        border-radius: 999px;
        font-size: 20px;
        cursor: pointer;
        line-height: 1;
        transition: all .18s ease;
    }

    #docsShell .docs-upload-close:hover {
        background: #fee2e2;
        color: var(--docs-danger);
    }

    #docsShell .docs-upload-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 220px;
        gap: 10px;
        align-items: stretch;
    }

    #docsShell .docs-upload-drop {
        border: 1px dashed #cbd5e1;
        background: var(--docs-soft);
        border-radius: 14px;
        padding: 13px;
        min-height: 96px;
        color: #374151;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        transition: all .18s ease;
    }

    #docsShell .docs-upload-drop:hover,
    #docsShell .docs-upload-drop.has-file {
        border-color: var(--docs-primary);
        background: var(--docs-soft-green);
    }

    #docsShell .docs-upload-drop span,
    #docsShell .docs-upload-drop strong {
        font-size: 13px;
        font-weight: 900;
        color: #111827;
        word-break: break-word;
    }

    #docsShell .docs-upload-drop small {
        font-size: 11px;
        color: var(--docs-muted);
    }

    #docsShell .docs-upload-fields {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    #docsShell .docs-field-label {
        margin: 0;
    }

    #docsShell .docs-field-label span {
        color: var(--docs-danger);
    }

    #docsShell .docs-upload-btn {
        border: 0;
        background: var(--docs-primary);
        color: #ffffff;
        border-radius: 10px;
        min-height: 38px;
        padding: 0 12px;
        font-size: 12px;
        font-weight: 900;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        transition: all .18s ease;
    }

    #docsShell .docs-upload-btn:hover {
        background: var(--docs-primary-hover);
    }

    #docsShell .docs-upload-btn:disabled {
        opacity: .65;
        cursor: not-allowed;
    }

    #docsShell .docs-upload-drop.has-preview {
        padding: 8px;
    }

    #docsShell .docs-upload-preview,
    #docsShell .docs-upload-file-preview {
        position: relative;
        display: grid;
        grid-template-columns: 96px minmax(0, 1fr) 28px;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    #docsShell .docs-upload-preview-img {
        width: 96px;
        height: 76px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid var(--docs-border);
        background: #ffffff;
    }

    #docsShell .docs-upload-preview-info {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    #docsShell .docs-upload-preview-info strong {
        color: #111827;
        font-size: 13px;
        font-weight: 900;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    #docsShell .docs-upload-preview-info small {
        color: var(--docs-muted);
        font-size: 11px;
    }

    #docsShell .docs-upload-preview-remove {
        width: 26px;
        height: 26px;
        border: 0;
        border-radius: 999px;
        background: #fee2e2;
        color: var(--docs-danger);
        font-size: 18px;
        font-weight: 900;
        line-height: 1;
        cursor: pointer;
    }

    #docsShell .docs-upload-file-icon {
        width: 72px;
        height: 56px;
        border-radius: 12px;
        background: var(--docs-blue-soft);
        color: var(--docs-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 900;
    }

    #docsShell .docs-upload-drop.is-uploading {
        opacity: .7;
        pointer-events: none;
    }

    #docsShell .docs-upload-progress {
        margin-top: 12px;
        padding: 12px;
        border: 1px solid var(--docs-border, #e5e7eb);
        border-radius: 14px;
        background: #f9fafb;
    }

    #docsShell .docs-upload-progress-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
        font-size: 12px;
        font-weight: 900;
        color: #111827;
    }

    #docsShell .docs-upload-progress-track {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        overflow: hidden;
        background: #e5e7eb;
    }

    #docsShell .docs-upload-progress-bar {
        width: 0%;
        height: 100%;
        border-radius: 999px;
        background: var(--docs-primary);
        transition: width .18s ease;
    }

    #docsShell .docs-upload-progress-meta {
        display: block;
        margin-top: 7px;
        color: var(--docs-muted) !important;
        font-size: 11px;
    }

    #docsShell .docs-body {
        min-height: 0;
        overflow-y: auto;
        padding-right: 3px;
    }

    #docsShell .docs-tab-pane {
        display: none;
    }

    #docsShell .docs-tab-pane.show.active {
        display: block;
    }

    #docsShell .docs-view-list {
        display: none;
    }

    #docsShell[data-view="list"] .docs-view-grid {
        display: none;
    }

    #docsShell[data-view="list"] .docs-view-list {
        display: block;
    }

    #docsShell .docs-masonry {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 14px;
    }

    #docsShell .docs-card {
        overflow: hidden;
        transition: border-color .18s ease, transform .18s ease, box-shadow .18s ease;
    }

    #docsShell .docs-card:hover {
        border-color: var(--docs-primary);
        transform: translateY(-1px);
        box-shadow: var(--docs-shadow);
    }

    #docsShell .docs-thumb {
        position: relative;
        background: var(--docs-soft);
        min-height: 180px;
        overflow: hidden;
    }

    #docsShell .docs-media {
        width: 100%;
        height: 190px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--docs-muted);
        text-decoration: none;
        border: 0;
        background: transparent;
    }

    #docsShell .docs-img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        display: block;
    }

    #docsShell .docs-center {
        flex-direction: column;
        gap: 8px;
    }

    #docsShell .docs-file-btn {
        cursor: pointer;
    }

    #docsShell .docs-center small {
        font-size: 12px;
        font-weight: 900;
        color: var(--docs-muted);
    }

    #docsShell .docs-card-actions {
        position: absolute;
        top: 8px;
        right: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        opacity: 0;
        transform: translateY(-4px);
        transition: all .18s ease;
    }

    #docsShell .docs-card:hover .docs-card-actions {
        opacity: 1;
        transform: translateY(0);
    }

    #docsShell .docs-action-btn,
    #docsShell .docs-list-icon-btn {
        width: 34px;
        height: 34px;
        border: 1px solid var(--docs-border);
        border-radius: 10px;
        background: #ffffff;
        color: var(--docs-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        cursor: pointer;
        transition: all .18s ease;
    }

    #docsShell .docs-action-btn:hover,
    #docsShell .docs-list-icon-btn:hover {
        background: var(--docs-soft);
        color: var(--docs-primary);
    }

    #docsShell .docs-card-body {
        padding: 12px;
    }

    #docsShell .docs-title {
        font-size: 14px;
        font-weight: 900;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 9px;
    }

    #docsShell .docs-meta-stack {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 4px;
    }

    #docsShell .docs-meta-stack small,
    #docsShell .docs-list-meta-rich small {
        font-size: 11px;
        color: var(--docs-muted);
    }

    #docsShell .docs-type-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 9px;
        border-radius: 999px;
        background: var(--docs-blue-soft);
        color: var(--docs-blue);
        font-size: 11px;
        font-weight: 900;
    }

    #docsShell .docs-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #docsShell .docs-list-row {
        display: grid;
        grid-template-columns: 64px minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 12px;
    }

    #docsShell .docs-list-thumb {
        width: 58px;
        height: 58px;
        border-radius: 14px;
        background: var(--docs-soft);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    #docsShell .docs-list-img {
        width: 58px;
        height: 58px;
        object-fit: cover;
        display: block;
    }

    #docsShell .docs-list-icon-btn {
        width: 58px;
        height: 58px;
        background: transparent;
        border: 0;
    }

    #docsShell .docs-list-info {
        min-width: 0;
    }

    #docsShell .docs-list-title {
        font-size: 14px;
        font-weight: 900;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 6px;
    }

    #docsShell .docs-list-meta-rich {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        align-items: center;
    }

    #docsShell .docs-list-actions {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    #docsShell .docs-empty {
        grid-column: 1 / -1;
        border: 1px dashed var(--docs-border);
        border-radius: 16px;
        padding: 42px 20px;
        text-align: center;
        background: #ffffff;
    }

    #docsShell .docs-empty-title {
        font-size: 15px;
        font-weight: 900;
        color: #111827;
    }

    #docsShell .docs-empty-sub {
        font-size: 13px;
        color: var(--docs-muted);
        margin-top: 6px;
    }

    #docsShell .docs-pdf-modal {
        position: fixed !important;
        inset: 0 !important;
        display: none;
        z-index: 2147483646 !important;
    }

    #docsShell .docs-pdf-modal.is-open {
        display: block !important;
    }

    #docsShell .docs-pdf-backdrop {
        position: absolute !important;
        inset: 0 !important;
        background: rgba(17, 24, 39, .72) !important;
    }

    #docsShell .docs-pdf-dialog {
        position: relative !important;
        width: 94vw !important;
        height: 92vh !important;
        max-width: 1400px !important;
        margin: 4vh auto !important;
        display: flex !important;
        flex-direction: column !important;
        background: #ffffff !important;
        border-radius: 18px !important;
        overflow: hidden !important;
        z-index: 2147483647 !important;
    }

    #docsShell .docs-pdf-header {
        padding: 14px 18px !important;
        background: #111827 !important;
        color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }

    #docsShell .docs-pdf-header h5 {
        margin: 0 !important;
        color: #ffffff !important;
        font-size: 14px !important;
        font-weight: 900 !important;
    }

    #docsShell .docs-pdf-close {
        border: 0 !important;
        background: transparent !important;
        color: #ffffff !important;
        font-size: 30px !important;
        cursor: pointer !important;
    }

    #docsShell .docs-pdf-body {
        flex: 1 !important;
        min-height: 0 !important;
        background: #525659 !important;
    }

    #docsShell .docs-pdf-body iframe {
        width: 100% !important;
        height: 100% !important;
        border: 0 !important;
        background: #ffffff !important;
    }

    body.docs-pdf-open {
        overflow: hidden !important;
    }

    #docsShell .text-danger {
        color: var(--docs-danger) !important;
    }

    #docsShell .text-primary {
        color: var(--docs-blue) !important;
    }

    #docsShell .text-success {
        color: var(--docs-primary) !important;
    }

    #docsShell .text-muted {
        color: var(--docs-muted) !important;
    }

    @media (max-width: 1180px) {
        #docsShell .docs-header-actions {
            grid-template-columns: 1fr 190px;
        }

        #docsShell .docs-new-upload-action {
            grid-column: 1 / -1;
        }

        #docsShell .docs-new-image-btn {
            width: 100%;
        }
    }

    @media (max-width: 760px) {
        #docsShell {
            max-height: none;
            overflow: visible;
            padding: 12px;
        }

        #docsShell .docs-context-card {
            align-items: flex-start;
            flex-direction: column;
        }

        #docsShell .docs-header-top {
            flex-direction: column !important;
            align-items: stretch !important;
        }

        #docsShell .docs-tabs {
            width: 100% !important;
            overflow-x: auto !important;
            flex-wrap: nowrap !important;
            padding-bottom: 4px !important;
        }

        #docsShell .docs-tab-item {
            flex: 0 0 auto !important;
        }

        #docsShell .docs-view-toggle {
            margin-left: 0 !important;
            align-self: flex-end !important;
        }

        #docsShell .docs-header-actions {
            grid-template-columns: 1fr;
        }

        #docsShell .docs-upload-grid {
            grid-template-columns: 1fr;
        }

        #docsShell .docs-masonry {
            grid-template-columns: 1fr;
        }

        #docsShell .docs-list-row {
            grid-template-columns: 54px minmax(0, 1fr);
        }

        #docsShell .docs-list-actions {
            grid-column: 1 / -1;
            justify-content: flex-start;
        }
    }

    body.docs-image-open {
    overflow: hidden !important;
}

.docs-image-modal {
    position: fixed;
    inset: 0;
    z-index: 2147483000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
}

.docs-image-modal.is-open {
    display: flex;
}

.docs-image-modal-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, .82);
    backdrop-filter: blur(6px);
}

.docs-image-modal-dialog {
    position: relative;
    width: min(1180px, calc(100vw - 36px));
    height: min(92vh, 860px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    border-radius: 22px;
    background: #ffffff;
    border: 1px solid var(--docs-border, #e5e7eb);
    box-shadow: 0 24px 80px rgba(0, 0, 0, .35);
}

.docs-image-modal-header {
    min-height: 58px;
    padding: 12px 16px;
    border-bottom: 1px solid var(--docs-border, #e5e7eb);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    flex-shrink: 0;
}

.docs-image-modal-header strong {
    display: block;
    color: #111827;
    font-size: 14px;
    font-weight: 900;
}

.docs-image-modal-header small {
    display: block;
    margin-top: 2px;
    color: #6b7280;
    font-size: 11px;
}

.docs-image-modal-close {
    width: 38px;
    height: 38px;
    border: 0;
    border-radius: 999px;
    background: #f1f5f9;
    color: #334155;
    font-size: 24px;
    line-height: 1;
    cursor: pointer;
}

.docs-image-modal-close:hover {
    background: #fee2e2;
    color: #dc2626;
}

.docs-image-modal-body {
    flex: 1;
    min-height: 0;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #0f172a;
    padding: 12px;
}

.docs-image-modal-body img {
    display: block;
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
    border-radius: 14px;
}
</style>

