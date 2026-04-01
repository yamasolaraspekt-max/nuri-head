<div class="angebote-wrapper p-2 fade-in">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0" style="color: #94c11f; font-weight: 700; font-size: 14px;">
            <i class="feather icon-file-text mr-1"></i> Angebote & Ordner
        </h6>
        <button class="btn btn-sm btn-primary shadow-sm py-1 px-2" style="font-size: 11px; font-weight: 600;">
            <i class="feather icon-plus"></i> Neues Angebot
        </button>
    </div>

    <div class="row mb-3 mx-n1">
        <div class="col-4 px-1">
            <div class="kpi-micro-card">
                <div class="kpi-icon text-primary bg-primary-light"><i class="feather icon-file"></i></div>
                <div class="kpi-text">
                    <div class="kpi-label">Angebote</div>
                    <div class="kpi-value text-dark">{{ $analytics['total_offers'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-4 px-1">
            <div class="kpi-micro-card">
                <div class="kpi-icon text-info bg-info-light"><i class="feather icon-folder"></i></div>
                <div class="kpi-text">
                    <div class="kpi-label">Varianten</div>
                    <div class="kpi-value text-dark">{{ $analytics['total_folders'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-4 px-1">
            <div class="kpi-micro-card" style="background: linear-gradient(135deg, #edf4ff, #f4fbf6); border-color: #d1e7dd;">
                <div class="kpi-icon text-success bg-white shadow-sm"><i class="feather icon-pie-chart"></i></div>
                <div class="kpi-text">
                    <div class="kpi-label">Volumen</div>
                    <div class="kpi-value text-success">{{ number_format($analytics['total_gross'], 2, ',', '.') }} €</div>
                </div>
            </div>
        </div>
    </div>

    <div class="offers-list">
        @forelse($offers as $offer)
            <div class="offer-box">
                <div class="offer-header">
                    <div class="d-flex align-items-center">
                        <span class="offer-id">#{{ $offer->id }}</span>
                        <span class="offer-title">DL: {{ $offer->service ?? 'Nicht definiert' }}</span>
                        <span class="offer-meta d-none d-sm-inline">· {{ $offer->created_at->format('d.m.Y H:i') }}</span>
                    </div>

                    <div class="d-flex align-items-center">
                        <span class="badge {{ $offer->status == 'open' ? 'badge-warning' : 'badge-primary' }} offer-status-badge">
                            {{ $offer->status ?? 'Offen' }}
                        </span>

                        @if($offer->creator)
                            <img src="{{ $offer->creator->image ? asset('images/employee/' . $offer->creator->image) : asset('images/gender/male.png') }}" 
                                 alt="{{ $offer->creator->name }}" 
                                 class="offer-avatar"
                                 title="Erstellt von: {{ $offer->creator->name }} {{ $offer->creator->lastname }}"
                                 data-toggle="tooltip">
                        @else
                            <img src="{{ asset('images/gender/male.png') }}" 
                                 class="offer-avatar"
                                 title="System / Unbekannt"
                                 data-toggle="tooltip">
                        @endif
                    </div>
                </div>

                <div class="folder-grid">
                    @forelse($offer->folders as $folder)
                        <a href="{{ url('admin/offers/folders/' . $folder->id . '?new_offer=1') }}" class="folder-item" style="border-left-color: {{ $folder->color ?? '#93c119' }} !important;">
                            <div class="folder-icon" style="background: {{ $folder->color ?? '#93c119' }}1A; color: {{ $folder->color ?? '#93c119' }};">
                                <i class="feather icon-folder"></i>
                            </div>
                            
                            <div class="folder-content">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="folder-name">{{ $folder->name ?? 'Ordner #' . $folder->id }}</div>
                                    @if($folder->creator)
                                        <img src="{{ $folder->creator->image ? asset('images/employee/' . $folder->creator->image) : asset('images/gender/male.png') }}" 
                                             class="folder-avatar" title="{{ $folder->creator->name }}" data-toggle="tooltip">
                                    @endif
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-end">
                                    <span class="badge badge-{{ $folder->status_badge_class }} folder-badge">{{ $folder->status_label }}</span>
                                    <span class="folder-price">
                                        @if($folder->detail)
                                            {{ number_format($folder->detail->total_gross, 2, ',', '.') }} €
                                        @else
                                            0,00 €
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="folder-empty">Keine Ordner vorhanden.</div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="alert alert-secondary d-flex align-items-center py-2 px-3" style="border-radius: 6px; font-size: 12px; margin: 0;">
                <i class="feather icon-info mr-2" style="font-size: 16px;"></i>
                Bisher wurden noch keine Angebote für dieses Produkt erstellt.
            </div>
        @endforelse
    </div>
</div>

<style>
    /* Scope everything to avoid breaking the rest of your app */
    .angebote-wrapper {
        font-family: inherit;
    }

    /* KPI Micro Cards */
    .kpi-micro-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 6px 8px;
        display: flex;
        align-items: center;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        height: 100%;
    }
    .kpi-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .bg-primary-light { background: #e0e7ff; }
    .bg-info-light { background: #e0f2fe; }
    .kpi-text { min-width: 0; }
    .kpi-label { font-size: 9px; text-transform: uppercase; color: #6b7280; font-weight: 700; letter-spacing: 0.5px; line-height: 1; margin-bottom: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .kpi-value { font-size: 14px; font-weight: 800; line-height: 1; }

    /* Offer Box */
    .offer-box {
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        margin-bottom: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .offer-header {
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        padding: 6px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .offer-id { background: #94c11f; color: #fff; border-radius: 4px; padding: 2px 6px; font-size: 10px; font-weight: bold; margin-right: 8px; }
    .offer-title { font-size: 12px; font-weight: 700; color: #111827; }
    .offer-meta { font-size: 11px; color: #6b7280; margin-left: 6px; }
    .offer-status-badge { font-size: 9px; padding: 3px 6px; margin-right: 8px; text-transform: uppercase; letter-spacing: 0.5px;}
    .offer-avatar { width: 22px; height: 22px; border-radius: 50%; object-fit: cover; border: 1px solid #fff; box-shadow: 0 1px 2px rgba(0,0,0,0.1); }

    /* Folders Grid */
    .folder-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 8px;
        padding: 8px 12px;
        background: #ffffff;
    }
    .folder-item {
        display: flex;
        align-items: center;
        padding: 6px 8px;
        border: 1px solid #e5e7eb;
        border-left-width: 4px !important;
        border-left-style: solid;
        border-radius: 6px;
        text-decoration: none !important;
        background: #ffffff;
        transition: all 0.2s ease;
    }
    .folder-item:hover {
        background: #f8fafc;
        border-color: #d1d5db;
        box-shadow: 0 3px 6px rgba(0,0,0,0.04);
        transform: translateY(-1px);
    }
    .folder-icon {
        width: 26px;
        height: 26px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        margin-right: 8px;
        flex-shrink: 0;
    }
    .folder-content {
        flex-grow: 1;
        min-width: 0;
    }
    .folder-name {
        font-size: 11px;
        font-weight: 700;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.2;
    }
    .folder-avatar {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }
    .folder-badge {
        font-size: 8px;
        padding: 2px 4px;
        border-radius: 3px;
    }
    .folder-price {
        font-size: 11px;
        font-weight: 800;
        color: #111827;
    }
    .folder-empty {
        grid-column: 1 / -1;
        font-size: 11px;
        color: #9ca3af;
        font-style: italic;
        padding: 4px;
    }
</style>

<script>
    // Initialize tooltips securely
    if (window.jQuery && $.fn.tooltip) {
        $('[data-toggle="tooltip"]').tooltip();
    }
</script>