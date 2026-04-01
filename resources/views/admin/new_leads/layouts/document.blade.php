@php
    // Icons
    $icoUpload   = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5"/><path d="M12 3v12"/><path d="M7 8l5-5 5 5"/></svg>';
    $icoDownload = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16.5V19a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2.5"/><path d="M12 3v12"/><path d="M7 11l5 5 5-5"/></svg>';
    $icoTrash    = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/></svg>';
    $icoEdit     = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>';

    $icoPdf  = '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h6"/></svg>';
    $icoWord = '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 12l1.5 6L11 14l1.5 4L14 12"/></svg>';
    $icoXls  = '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8"/><path d="M8 17h8"/><path d="M8 9h4"/></svg>';
    $icoFile = '<svg viewBox="0 0 24 24" width="34" height="34" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>';
    
    $icoGrid = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="8" height="8"/><rect x="13" y="3" width="8" height="8"/><rect x="3" y="13" width="8" height="8"/><rect x="13" y="13" width="8" height="8"/></svg>';
    $icoList = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13"/><path d="M8 12h13"/><path d="M8 18h13"/><path d="M3 6h.01"/><path d="M3 12h.01"/><path d="M3 18h.01"/></svg>';

    $ph = asset('images/icons/placeholder.svg');
@endphp

@php
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
        'Other' => 'Sonstiges'
    ];
@endphp

<div class="docs-shell" id="docsShell" data-view="grid">
    <div class="docs-head">
        <div class="docs-header-top">
            <ul class="docs-tabs" id="galleryTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#allTab" data-tab="#allTab">Alle Dateien</a></li>
                <li class="nav-item"><a class="nav-link" href="#imagesTab" data-tab="#imagesTab">Bilder</a></li>
                <li class="nav-item"><a class="nav-link" href="#pdfTab" data-tab="#pdfTab">PDFs</a></li>
                <li class="nav-item"><a class="nav-link" href="#othersTab" data-tab="#othersTab">Sonstiges</a></li>
            </ul>
            
            <div class="docs-view-toggle">
                <button type="button" class="view-btn is-active" data-view="grid" title="Gitter">{!! $icoGrid !!}</button>
                <button type="button" class="view-btn" data-view="list" title="Liste">{!! $icoList !!}</button>
            </div>
        </div>

        <div class="docs-header-actions">
            <div class="action-item search-wrap">
                <input type="text" id="searchImage" class="form-control" placeholder="🔍 Name, Typ oder Datum suchen...">
            </div>
            
            <div class="action-item filter-wrap">
                <select id="stageFilter" class="form-control">
                    <option value="">Alle Stufen</option>
                    @foreach($docTypes as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
            </div>

            <form action="{{ route('document.upload') }}" method="POST" class="dz-compact dropzone" id="documentDropzone">
                @csrf
                <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
                <input type="hidden" name="alternative_id" value="{{ request('alternative_id') }}">
                <input type="hidden" name="product_id" id="image_product">
                
                <div class="dz-clickable">
                    {!! $icoUpload !!} <span>Upload</span>
                </div>

                <select name="stage" class="form-control form-control-sm dz-type-select" id="dzStageSelect">
                    @foreach($docTypes as $key => $val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="docs-body">
        <div class="tab-content" id="galleryTabsContent">
            @php
                $tabConfigs = [
                    'allTab' => ['active' => true, 'filter' => null],
                    'imagesTab' => ['active' => false, 'filter' => 'image'],
                    'pdfTab' => ['active' => false, 'filter' => 'pdf'],
                    'othersTab' => ['active' => false, 'filter' => 'other']
                ];
            @endphp

            @foreach($tabConfigs as $tabId => $config)
            <div class="tab-pane {{ $config['active'] ? 'show active' : '' }}" id="{{ $tabId }}" role="tabpanel">
                <div class="docs-view-grid">
                    <div class="masonry">
                        @foreach($images as $img)
                            @php
                                $ext = strtolower(pathinfo((string)$img->image, PATHINFO_EXTENSION));
                                $isImage = in_array($ext, ['jpg','jpeg','png','webp','gif']);
                                $isPdf = ($ext === 'pdf');
                                $isWord = in_array($ext, ['doc','docx']);
                                $isXls = in_array($ext, ['xls','xlsx']);
                                
                                if($config['filter'] === 'image' && !$isImage) continue;
                                if($config['filter'] === 'pdf' && !$isPdf) continue;
                                if($config['filter'] === 'other' && ($isImage || $isPdf)) continue;

                                // Used for inline display (images and PDFs in iframe)
                                $fileUrl = route('secure.image', $img->id).'?v='.($img->updated_at ? $img->updated_at->timestamp : $img->id);
                            @endphp

                            <div class="masonry-item gallery-item" data-name="{{ $img->image_name }}" data-stage="{{ $img->stage }}" data-ext="{{ $ext }}">
                                <div class="doc-card">
                                    <div class="doc-thumb">
                                        @if($isImage)
                                            <a href="{{ $fileUrl }}" class="glightbox doc-media">
                                                <img src="{{ $fileUrl }}" class="doc-img" onerror="this.src='{{ $ph }}'">
                                            </a>
                                        @else
                                            @if($isPdf)
                                                <a href="javascript:void(0)" onclick="openPdfViewer('{{ $fileUrl }}', '{{ addslashes($img->image_name) }}')" class="doc-media doc-center text-decoration-none">
                                                    <span class="file-ico text-danger" title="Vorschau ansehen">{!! $icoPdf !!}</span>
                                                </a>
                                            @else
                                                <a href="{{ route('document.download', $img->id) }}" target="_blank" class="doc-media doc-center text-decoration-none" title="Herunterladen">
                                                    @if($isWord) <span class="file-ico text-primary">{!! $icoWord !!}</span>
                                                    @elseif($isXls) <span class="file-ico text-success">{!! $icoXls !!}</span>
                                                    @else <span class="file-ico text-muted">{!! $icoFile !!}</span>
                                                    @endif
                                                </a>
                                            @endif
                                        @endif

                                        <div class="doc-actions">
                                            <button type="button" class="doc-action-btn" onclick="editDocumentDetails({{ $img->id }}, '{{ addslashes($img->image_name) }}', '{{ $img->stage }}')" title="Bearbeiten">
                                                {!! $icoEdit !!}
                                            </button>
                                            <a href="{{ route('document.download', $img->id) }}" class="doc-action-btn" target="_blank" title="Herunterladen">
                                                {!! $icoDownload !!}
                                            </a>
                                            <button type="button" class="doc-action-btn text-danger" onclick="deleteDocument({{ $img->id }}, this)" title="Löschen">
                                                {!! $icoTrash !!}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="doc-body">
                                        <div class="doc-title" title="{{ $img->image_name }}">{{ $img->image_name }}</div>
                                        <div class="doc-meta">
                                            <span class="badge badge-light text-muted">{{ $docTypes[$img->stage] ?? $img->stage }}</span>
                                            <small>{{ \Carbon\Carbon::parse($img->created_at)->format('d.m.Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="docs-view-list">
                    <div class="docs-list">
                        @foreach($images as $img)
                             @php
                                $ext = strtolower(pathinfo((string)$img->image, PATHINFO_EXTENSION));
                                if($config['filter'] === 'image' && !in_array($ext, ['jpg','jpeg','png','webp','gif'])) continue;
                                if($config['filter'] === 'pdf' && $ext !== 'pdf') continue;
                                if($config['filter'] === 'other' && in_array($ext, ['jpg','jpeg','png','webp','gif','pdf'])) continue;

                                $fileUrl = route('secure.image', $img->id).'?v='.($img->updated_at ? $img->updated_at->timestamp : $img->id);
                            @endphp
                            <div class="list-row gallery-item" data-name="{{ $img->image_name }}" data-stage="{{ $img->stage }}">
                                <div class="list-info">
                                    <div class="list-title">{{ $img->image_name }}</div>
                                    <div class="list-meta">
                                        <span class="badge badge-pill badge-light">{{ $docTypes[$img->stage] ?? $img->stage }}</span>
                                        <small>{{ strtoupper($img->file_type) }} • {{ \Carbon\Carbon::parse($img->created_at)->format('d.m.Y') }}</small>
                                    </div>
                                </div>
                                <div class="list-actions">
                                    @if($ext === 'pdf')
                                        <button class="btn btn-sm btn-icon text-primary" onclick="openPdfViewer('{{ $fileUrl }}', '{{ addslashes($img->image_name) }}')" title="Vorschau ansehen">
                                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                        </button>
                                    @endif

                                    <button class="btn btn-sm btn-icon" onclick="editDocumentDetails({{ $img->id }}, '{{ addslashes($img->image_name) }}', '{{ $img->stage }}')" title="Bearbeiten">
                                        {!! $icoEdit !!}
                                    </button>
                                    <a href="{{ route('document.download', $img->id) }}" class="btn btn-sm btn-icon" target="_blank" title="Herunterladen">
                                        {!! $icoDownload !!}
                                    </a>
                                    <button class="btn btn-sm btn-icon text-danger" onclick="deleteDocument({{ $img->id }}, this)" title="Löschen">
                                        {!! $icoTrash !!}
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="cmodal" id="pdfViewerModal" aria-hidden="true" style="z-index: 99999;">
        <div class="cmodal__backdrop" data-modal-close onclick="closePdfViewer()"></div>
        <div class="cmodal__dialog" role="dialog" style="width: 90vw; height: 90vh; max-width: 1400px; display: flex; flex-direction: column; margin: 2vh auto;">
            
            <div class="cmodal__header bg-dark text-white" style="border-radius: 12px 12px 0 0;">
                <h5 class="cmodal__title" id="pdfViewerTitle" style="color: white; margin: 0;">Dokument wird geladen...</h5>
                <button type="button" class="cmodal__close text-white" data-modal-close aria-label="Close" onclick="closePdfViewer()" style="background: none; border: none; font-size: 24px; cursor: pointer;">×</button>
            </div>
            
            <div class="cmodal__body" style="flex: 1; padding: 0; max-height: none; overflow: hidden; background: #525659;">
                <iframe id="pdfViewerIframe" src="" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>

        </div>
    </div>

    <style>
        .docs-shell { background:#fff; padding:15px; display:flex; flex-direction:column; max-height:calc(100vh - 140px); overflow:hidden; }
        .docs-header-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; padding-bottom:10px; border-bottom:1px solid #f1f5f9; }
        .docs-header-actions { display:flex; align-items:center; gap:12px; margin-bottom:15px; flex-wrap:nowrap; }

        .search-wrap { flex:1; min-width: 200px; }
        .filter-wrap { width: 180px; }

        #docsShell[data-view="grid"] .docs-view-list { display: none !important; }
        #docsShell[data-view="list"] .docs-view-grid { display: none !important; }

        .dz-compact { 
            display:flex; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; 
            padding:4px 10px; border-radius:8px; gap:10px; cursor:pointer; min-width: 280px;
        }
        .dz-clickable { display:flex; align-items:center; gap:8px; color:#64748b; font-size:13px; font-weight:600; flex:1; }
        .dz-type-select { width: 130px !important; height: 32px !important; font-size: 12px; }
        .dz-compact .dz-message, .dz-compact .dz-preview { display:none !important; }

        .masonry { column-gap:15px; columns: 4 250px; }
        .masonry-item { break-inside:avoid; margin-bottom:15px; }
        .doc-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; transition: 0.2s; position: relative;}
        .doc-card:hover { box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); }
        .doc-thumb { position:relative; background:#f1f5f9; min-height:140px; }
        .doc-actions { position:absolute; top:8px; right:8px; display:flex; flex-direction:column; gap:5px; opacity:0; transition:0.2s; z-index: 10;}
        .doc-thumb:hover .doc-actions { opacity:1; }
        .doc-action-btn { width:30px; height:30px; border-radius:6px; background:#fff; border:1px solid #e2e8f0; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#1e293b; text-decoration:none;}
        .doc-action-btn:hover { background: #f8fafc; text-decoration:none;}
        .doc-center { display: flex; align-items: center; justify-content: center; height: 140px; transition: 0.2s; cursor: pointer; }
        .doc-center:hover { transform: scale(1.05); }

        .doc-body { padding:10px; border-top:1px solid #f1f5f9; }
        .doc-title { font-weight:600; font-size:13px; color:#1e293b; text-overflow:ellipsis; overflow:hidden; white-space:nowrap; }
        .doc-meta { display:flex; justify-content:space-between; align-items:center; margin-top:5px; }

        .docs-tabs { display:flex; gap:20px; list-style:none; padding:0; margin:0; }
        .docs-tabs .nav-link { color:#64748b; font-weight:600; padding:5px 0; border-bottom:2px solid transparent; text-decoration:none !important;}
        .docs-tabs .nav-link.active { color:#93c21c; border-bottom-color:#93c21c; }

        .view-btn { background: #fff; border: 1px solid #e2e8f0; color: #64748b; padding: 5px 8px; border-radius: 4px; }
        .view-btn.is-active { color: #93c21c; border-color: #93c21c; background: #f9fdf4; }

        .list-row { display: flex; align-items: center; justify-content: space-between; padding: 10px 15px; border-bottom: 1px solid #f1f5f9; transition: 0.2s; }
        .list-row:hover { background: #f8fafc; }
        .list-title { font-weight: 600; font-size: 14px; color: #1e293b; }
        .list-meta { display: flex; align-items: center; gap: 10px; margin-top: 4px; color: #64748b; }
        .list-actions { display: flex; gap: 8px; }

        /* Generic modal base classes needed for the PDF Viewer */
        .cmodal { position: fixed; inset: 0; display: none; z-index: 9999; }
        .cmodal.is-open { display: block; }
        .cmodal__backdrop { position: absolute; inset: 0; background: rgba(0,0,0,.55); }
        .cmodal-open { overflow: hidden; }

        @media (max-width: 992px) {
            .docs-header-actions { flex-wrap: wrap; }
            .search-wrap { width: 100%; order: 1; }
            .filter-wrap { flex: 1; order: 2; }
            .dz-compact { flex: 1; order: 3; }
        }
    </style>
    
   
</div>