@php
$offers = collect($offers ?? []);
$analytics = $analytics ?? [
    'total_offers' => 0,
    'total_folders' => 0,
    'total_gross' => 0,
    'total_net' => 0,
];

$employeeImage = function ($emp) {
    if (!empty($emp?->image)) {
        return asset('images/employee/' . $emp->image);
    }

    return asset('images/gender/male.png');
};

$money = function ($value) {
    return number_format((float) $value, 2, ',', '.') . ' €';
};

$offerStatusLabel = function ($status) {
    return match (strtolower((string) $status)) {
        'draft' => 'Entwurf',
        'open' => 'Offen',
        'sent' => 'Gesendet',
        'negotiation' => 'Verhandlung',
        'final' => 'Final',
        'cancel', 'cancelled', 'canceled' => 'Storniert',
        default => $status ? ucfirst((string) $status) : 'Offen',
    };
};

$offerStatusClass = function ($status) {
    return match (strtolower((string) $status)) {
        'sent' => 'status-sent',
        'negotiation' => 'status-warning',
        'final' => 'status-success',
        'cancel', 'cancelled', 'canceled' => 'status-danger',
        default => 'status-draft',
    };
};

$isCancelled = function ($value) {
    return in_array(strtolower((string) $value), ['cancel', 'cancelled', 'canceled', 'storniert'], true);
};

$activeFolderId = null;
$activeOfferId = null;

foreach ($offers as $offer) {
    foreach (($offer->folders ?? collect()) as $folder) {
        $docStatus = strtolower((string) ($folder->document_status ?? $folder->detail?->document_status ?? 'offer'));
        $offerStatus = strtolower((string) ($folder->offer_status ?? $folder->status ?? 'draft'));
        $dealStatus = strtolower((string) ($folder->deal_status ?? ''));

        if (
            !$activeFolderId &&
            !$isCancelled($offerStatus) &&
            !$isCancelled($folder->status ?? null) &&
            !$isCancelled($offer->status ?? null) &&
            !in_array($offerStatus, ['rejected', 'expired'], true) &&
            !in_array($dealStatus, ['lost'], true)
        ) {
            $activeFolderId = $folder->id;
            $activeOfferId = $offer->id;
        }
    }
}

$totalCancelled = $offers->sum(function ($offer) use ($isCancelled) {
    $offerCancelled = $isCancelled($offer->status ?? null) ? 1 : 0;

    $folderCancelled = collect($offer->folders ?? [])->filter(function ($folder) use ($isCancelled) {
        return $isCancelled($folder->status ?? null)
            || $isCancelled($folder->offer_status ?? null);
    })->count();

    return $offerCancelled + $folderCancelled;
});
@endphp

<style>
    :root {
        --of-green: #93c21c;
        --of-green-soft: #cfe09b;
        --of-blue: #74b2d4;
        --of-blue-soft: #c0d8ea;
        --of-orange: #f8ac00;
        --of-pink: #e50656;

        --of-bg: #ffffff;
        --of-card: #ffffff;
        --of-text: #374151;
        --of-muted: #6b7280;
        --of-border: #c0d8ea;

        --of-radius: 16px;
        --of-radius-lg: 22px;
        --of-transition: all .18s ease;
    }

    .of-wrap {
        color: var(--of-text);
        background: var(--of-bg);
        padding: 10px;
        max-width: 100%;
        overflow-x: hidden;
    }

    .of-wrap *,
    .of-wrap *::before,
    .of-wrap *::after {
        box-shadow: none !important;
        box-sizing: border-box;
    }

    .of-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .of-title {
        font-size: 24px;
        font-weight: 900;
        color: var(--of-blue);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        text-transform: uppercase;
    }

    .of-sub {
        font-size: 14px;
        color: var(--of-text);
        margin-top: 4px;
        line-height: 1.45;
    }

    .of-btn {
        background: var(--of-blue);
        color: #ffffff;
        border: 0;
        padding: 10px 16px;
        border-radius: 999px;
        font-weight: 900;
        cursor: pointer;
        transition: var(--of-transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        min-height: 42px;
        white-space: nowrap;
    }

    .of-btn:hover {
        background: var(--of-green);
        color: #ffffff;
        text-decoration: none;
    }

    .of-btn-soft {
        background: #ffffff;
        color: var(--of-text);
        border: 1px solid var(--of-border);
        padding: 10px 14px;
        border-radius: 999px;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
    }

    .of-analytics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .of-stat {
        background: var(--of-card);
        border: 1px solid var(--of-border);
        border-radius: var(--of-radius-lg);
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 92px;
        min-width: 0;
    }

    .of-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        color: #ffffff;
    }

    .of-stat-icon.total { background: var(--of-blue); }
    .of-stat-icon.folder { background: var(--of-green); }
    .of-stat-icon.money { background: var(--of-orange); }
    .of-stat-icon.locked { background: var(--of-pink); }

    .of-stat-meta {
        min-width: 0;
    }

    .of-stat-label {
        font-size: 9px;
        font-weight: 900;
        color: var(--of-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .of-stat-value {
        font-size: 12px;
        font-weight: 900;
        color: var(--of-blue);
        line-height: 1.1;
        margin-top: 4px;
        overflow-wrap: anywhere;
    }

    .of-stat-sub {
        font-size: 9px;
        color: var(--of-text);
        margin-top: 4px;
        line-height: 1.35;
    }

    .of-active-note {
        border: 1px solid var(--of-green);
        background: rgba(147, 194, 28, .10);
        border-radius: var(--of-radius-lg);
        padding: 12px 14px;
        margin-bottom: 16px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: var(--of-text);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
    }

    .of-active-note svg {
        width: 18px;
        height: 18px;
        color: var(--of-green);
        flex: 0 0 auto;
        margin-top: 1px;
    }

    .of-card {
        background: #ffffff;
        overflow: hidden;
        max-width: 100%;
    }

    .of-card-head {
        padding: 2px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .of-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 900;
        color: var(--of-blue);
    }

    .of-card-sub {
        font-size: 12px;
        color: var(--of-text);
        margin-top: 4px;
    }

    .of-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 100%;
    }

    .of-item {
        background: #ffffff;
        border: 1px solid var(--of-border);
        border-radius: var(--of-radius-lg);
        transition: var(--of-transition);
        overflow: hidden;
        max-width: 100%;
    }

    .of-item:hover {
        border-color: var(--of-green);
        background: #ffffff;
        transform: translateY(-1px);
    }

    .of-item.is-active {
        border-color: var(--of-green);
    }

    .of-item.is-cancelled {
        border-color: rgba(229, 6, 86, .45);
        background: rgba(229, 6, 86, .025);
    }

    .of-item-main {
        padding: 16px;
        display: grid;
        gap: 12px;
        align-items: start;
        grid-template-columns:
            minmax(86px, 100px)
            minmax(220px, 1.35fr)
            minmax(180px, .95fr)
            minmax(170px, .8fr)
            minmax(120px, .5fr);
        max-width: 100%;
        cursor: pointer;
    }

    .of-cell {
        min-width: 0;
        max-width: 100%;
    }

    .of-cell-title {
        font-size: 9px;
        font-weight: 900;
        color: var(--of-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        display: none;
    }

    .of-offer-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: var(--of-blue-soft);
        border: 1px solid var(--of-border);
        min-height: 82px;
        padding: 8px;
        text-align: center;
    }

    .of-offer-no {
        font-size: 12px;
        line-height: 1.25;
        font-weight: 900;
        color: var(--of-blue);
        overflow-wrap: anywhere;
    }

    .of-offer-label {
        margin-top: 5px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .06em;
        color: var(--of-text);
        text-transform: uppercase;
    }

    .of-main {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .of-ttl {
        font-weight: 900;
        font-size: 13px;
        margin-bottom: 6px;
        color: var(--of-blue);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .of-subt {
        font-size: 13px;
        color: var(--of-text);
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .of-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
        color: var(--of-text);
    }

    .of-badge.status-draft {
        background: var(--of-blue-soft);
        color: var(--of-text);
    }

    .of-badge.status-sent {
        background: var(--of-blue);
        color: #ffffff;
    }

    .of-badge.status-warning {
        background: var(--of-orange);
        color: #ffffff;
    }

    .of-badge.status-success {
        background: var(--of-green);
        color: #ffffff;
    }

    .of-badge.status-danger {
        background: var(--of-pink);
        color: #ffffff;
    }

    .of-badge.active {
        background: var(--of-green);
        color: #ffffff;
    }

    .of-meta-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }

    .of-meta-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--of-text);
        background: #ffffff;
        border: 1px solid var(--of-border);
        border-radius: 999px;
        padding: 8px 10px;
        min-width: 0;
        max-width: 100%;
    }

    .of-meta-pill svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
    }

    .of-meta-pill span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .of-avatar-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .of-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
        background: var(--of-blue-soft);
    }

    .of-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .of-profile-btn {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid var(--of-blue);
        background: var(--of-blue);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
        transition: var(--of-transition);
        text-decoration: none;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .of-profile-btn:hover {
        background: var(--of-green);
        border-color: var(--of-green);
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .of-toggle {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid var(--of-border);
        background: #ffffff;
        color: var(--of-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
        transition: var(--of-transition);
        text-decoration: none;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .of-toggle:hover {
        background: var(--of-blue);
        border-color: var(--of-blue);
        color: #ffffff;
        text-decoration: none;
    }

    .of-collapse {
        display: none;
        border-top: 1px solid var(--of-border);
        padding: 14px 16px 16px;
        background: #ffffff;
    }

    .of-item.is-open .of-collapse {
        display: block;
    }

    .of-item.is-open .of-toggle-icon {
        transform: rotate(180deg);
    }

    .of-folder-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 10px;
        max-width: 100%;
    }

    .of-folder {
        border: 1px solid var(--of-border);
        border-left-width: 5px;
        border-radius: var(--of-radius);
        padding: 12px;
        background: #ffffff;
        text-decoration: none !important;
        transition: var(--of-transition);
        color: var(--of-text);
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .of-folder:hover {
        border-color: var(--of-green);
        color: var(--of-text);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .of-folder.is-active {
        background: rgba(147, 194, 28, .08);
        border-color: var(--of-green);
    }

    .of-folder.is-cancelled {
        background: rgba(229, 6, 86, .04);
        border-color: var(--of-pink);
    }

    .of-folder-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
    }

    .of-folder-left {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        min-width: 0;
    }

    .of-folder-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
    }

    .of-folder-name {
        font-size: 13px;
        font-weight: 900;
        color: var(--of-blue);
        line-height: 1.3;
        overflow-wrap: anywhere;
    }

    .of-folder-sub {
        font-size: 12px;
        color: var(--of-muted);
        margin-top: 3px;
        line-height: 1.35;
    }

    .of-folder-bottom {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .of-price {
        font-size: 15px;
        font-weight: 900;
        color: var(--of-text);
    }

    .of-net {
        font-size: 11px;
        font-weight: 700;
        color: var(--of-muted);
        margin-top: 2px;
    }

    .of-empty {
        text-align: center;
        padding: 44px 16px;
        color: var(--of-muted);
        background: #ffffff;
        border: 1px dashed var(--of-border);
        border-radius: 16px;
    }

    @media(max-width: 1400px) {
        .of-item-main {
            grid-template-columns:
                minmax(86px, 100px)
                minmax(220px, 1.3fr)
                minmax(180px, 1fr);
        }

        .of-cell:nth-child(4),
        .of-cell:nth-child(5) {
            grid-column: span 1;
        }
    }

    @media(max-width: 1200px) {
        .of-analytics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .of-item-main {
            grid-template-columns:
                minmax(86px, 100px)
                minmax(220px, 1fr);
        }

        .of-cell:nth-child(3),
        .of-cell:nth-child(4),
        .of-cell:nth-child(5) {
            grid-column: 1 / -1;
        }

        .of-cell-title {
            display: block;
        }

        .of-actions {
            justify-content: flex-start;
        }
    }

    @media(max-width: 768px) {
        .of-wrap {
            padding: 8px;
        }

        .of-title {
            font-size: 22px;
            align-items: flex-start;
        }

        .of-titlebar,
        .of-card-head {
            align-items: stretch;
        }

        .of-btn,
        .of-btn-soft,
        .of-profile-btn,
        .of-toggle {
            width: 100%;
            justify-content: center;
        }

        .of-list {
            padding: 0;
        }

        .of-item-main {
            grid-template-columns: 1fr;
            padding: 14px;
        }

        .of-offer-badge {
            min-height: 72px;
            align-items: flex-start;
            text-align: left;
            padding: 12px;
        }

        .of-actions {
            justify-content: stretch;
        }

        .of-folder-grid {
            grid-template-columns: 1fr;
        }

        .of-meta-pill {
            border-radius: 16px;
            align-items: flex-start;
        }

        .of-meta-pill span {
            white-space: normal;
            overflow-wrap: anywhere;
        }
    }

    @media(max-width: 700px) {
        .of-analytics {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="of-wrap contentOffers">
    <div id="offerFilterContext"
         data-customer="{{ $customer->id ?? $customer_id ?? '' }}"
         data-alternative="{{ $alternative->id ?? $alternative_id ?? '' }}"
         data-product="{{ $productData->product_id ?? $product_id ?? '' }}">
    </div>

    <div class="of-titlebar">
        <div>
            <h2 class="of-title">
                <i data-feather="file-text"></i>
                <span>Angebote & Ordner</span>
            </h2>

            <div class="of-sub">
                Alle Angebotsdokumente, Varianten und aktiven Entwürfe für diesen Kunden- und Produktkontext.
            </div>
        </div>

        <button type="button" class="of-btn" id="quickOpenOfferBtn" data-customer-id="{{ $customer->id ?? $customer_id ?? '' }}"
            data-alternative-id="{{ $alternative->id ?? $alternative_id ?? '' }}"
            data-product-id="{{ $productData->product_id ?? $product_id ?? '' }}">
            <i data-feather="plus"></i>
            Neues Angebot
        </button>
    </div>

    <div class="of-analytics">
        <div class="of-stat">
            <div class="of-stat-icon total">
                <i data-feather="file"></i>
            </div>

            <div class="of-stat-meta">
                <div class="of-stat-label">Angebote</div>
                <div class="of-stat-value">{{ $analytics['total_offers'] }}</div>
                <div class="of-stat-sub">Angebote insgesamt</div>
            </div>
        </div>

        <div class="of-stat">
            <div class="of-stat-icon folder">
                <i data-feather="folder"></i>
            </div>

            <div class="of-stat-meta">
                <div class="of-stat-label">Varianten</div>
                <div class="of-stat-value">{{ $analytics['total_folders'] }}</div>
                <div class="of-stat-sub">Ordner / Versionen</div>
            </div>
        </div>

        <div class="of-stat">
            <div class="of-stat-icon money">
                <i data-feather="pie-chart"></i>
            </div>

            <div class="of-stat-meta">
                <div class="of-stat-label">Volumen Brutto</div>
                <div class="of-stat-value">{{ $money($analytics['total_gross']) }}</div>
                <div class="of-stat-sub">Gesamtsumme aller Varianten</div>
            </div>
        </div>

        <div class="of-stat">
            <div class="of-stat-icon locked">
                <i data-feather="lock"></i>
            </div>

            <div class="of-stat-meta">
                <div class="of-stat-label">Gesperrt / Storniert</div>
                <div class="of-stat-value">{{ $totalCancelled }}</div>
                <div class="of-stat-sub">Nicht aktive Einträge</div>
            </div>
        </div>
    </div>

    @if($activeFolderId)
        <div class="of-active-note">
            <i data-feather="check-circle"></i>
            <div>
                <strong>Aktiver Angebotsordner:</strong>
                Der grün markierte Ordner ist aktuell aktiv. Stornierte oder abgelehnte Varianten sind mit einem Schloss gekennzeichnet.
            </div>
        </div>
    @endif

    <div class="of-card">
        <div class="of-card-head">
            <div>
                <h3 class="of-card-title">Angebotsliste</h3>
                <div class="of-card-sub">{{ $offers->count() }} Angebote gefunden</div>
            </div>
        </div>

        @if($offers->isEmpty())
            <div class="of-empty">
                <i data-feather="alert-triangle" style="width:32px;height:32px;margin-bottom:10px;"></i>
                <div>Bisher wurden noch keine Angebote für dieses Produkt erstellt.</div>
            </div>
        @else
            <div class="of-list">
                @foreach($offers as $offer)
                    @php
        $folders = collect($offer->folders ?? []);
        $offerGross = $folders->sum(fn($folder) => (float) ($folder->detail?->total_gross ?? 0));
        $offerNet = $folders->sum(fn($folder) => (float) ($folder->detail?->total_net ?? 0));
        $offerCancelled = $isCancelled($offer->status ?? null);
        $offerActive = (int) $activeOfferId === (int) $offer->id;

        $createdDate = $offer->created_at ? $offer->created_at->format('d') : '--';
        $createdMonth = $offer->created_at ? $offer->created_at->translatedFormat('M') : '---';

        $offerNo = $offer->offer_no ?: ('#' . $offer->id);
        $firstFolderUrl = $folders->first()
            ? url('admin/offers/folders/' . $folders->first()->id . '?new_offer=1')
            : '#';
                    @endphp

                    <div class="of-item {{ $offerActive ? 'is-active is-open' : '' }} {{ $offerCancelled ? 'is-cancelled' : '' }}"
                         data-offer-card
                         data-offer-id="{{ $offer->id }}">

                        <div class="of-item-main" data-offer-toggle>
                            <div class="of-cell">
                                <div class="of-cell-title">Angebot</div>

                                <div class="of-offer-badge">
                                    <div class="of-offer-no">{{ $offerNo }}</div>
                                    <div class="of-offer-label">{{ $createdDate }} {{ $createdMonth }}</div>
                                </div>
                            </div>

                            <div class="of-cell">
                                <div class="of-cell-title">Übersicht</div>

                                <div class="of-main">
                                    <div class="of-ttl">
                                        <span>Angebot {{ $offerNo }}</span>

                                        <span class="of-badge {{ $offerStatusClass($offer->status) }}">
                                            @if($offerCancelled)
                                                <i data-feather="lock"></i>
                                            @endif
                                            {{ $offerStatusLabel($offer->status) }}
                                        </span>

                                        @if($offerActive)
                                            <span class="of-badge active">
                                                <i data-feather="activity"></i>
                                                Aktiv
                                            </span>
                                        @endif
                                    </div>

                                    <div class="of-subt">
                                        <strong>Dienstleistung:</strong>
                                        {{ $offer->service ?? 'Nicht definiert' }}
                                        <br>

                                        <strong>Produkt:</strong>
                                        {{ $productData->articleGroup->article_group ?? $offer->product->article_group ?? '-' }}
                                        <br>

                                        <strong>Erstellt:</strong>
                                        {{ $offer->created_at ? $offer->created_at->format('d.m.Y H:i') : '–' }}
                                    </div>
                                </div>
                            </div>

                            <div class="of-cell">
                                <div class="of-cell-title">Summen</div>

                                <div class="of-meta-stack">
                                    <div class="of-meta-pill">
                                        <i data-feather="folder"></i>
                                        <span>{{ $folders->count() }} Ordner / Varianten</span>
                                    </div>

                                    <div class="of-meta-pill">
                                        <i data-feather="trending-up"></i>
                                        <span>Brutto: {{ $money($offerGross) }}</span>
                                    </div>

                                    <div class="of-meta-pill">
                                        <i data-feather="bar-chart-2"></i>
                                        <span>Netto: {{ $money($offerNet) }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="of-cell">
                                <div class="of-cell-title">Mitarbeiter</div>

                                @if($offer->creator)
                                    <div class="of-avatar-row">
                                        <img src="{{ $employeeImage($offer->creator) }}"
                                             class="of-avatar"
                                             title="{{ $offer->creator->name }} {{ $offer->creator->lastname }}"
                                             alt="{{ $offer->creator->name }} {{ $offer->creator->lastname }}"
                                             onerror="this.src='{{ asset('images/gender/male.png') }}'">

                                        <div class="of-subt">
                                            <strong>{{ $offer->creator->name }}</strong>
                                            {{ $offer->creator->lastname }}
                                        </div>
                                    </div>
                                @else
                                    <div class="of-meta-pill">
                                        <i data-feather="user-x"></i>
                                        <span>System / Unbekannt</span>
                                    </div>
                                @endif
                            </div>

                            <div class="of-cell">
                                <div class="of-cell-title">Aktionen</div>

                                <div class="of-actions">
                                    @if($folders->isNotEmpty())
                                        <a href="{{ $firstFolderUrl }}"
                                           class="of-profile-btn"
                                           onclick="event.stopPropagation();">
                                            <i data-feather="external-link"></i>
                                            Öffnen
                                        </a>
                                    @endif

                                    <button type="button"
                                            class="of-toggle"
                                            onclick="event.stopPropagation(); toggleOfferCollapse(this);">
                                        <i class="of-toggle-icon" data-feather="chevron-down"></i>
                                        Details
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="of-collapse">
                            @if($folders->isEmpty())
                                <div class="of-empty" style="padding:24px;">
                                    Keine Ordner vorhanden.
                                </div>
                            @else
                                <div class="of-folder-grid">
                                    @foreach($folders as $folder)
                                        @php
                $folderColor = $folder->color ?: '#93c21c';
                $folderGross = (float) ($folder->detail?->total_gross ?? 0);
                $folderNet = (float) ($folder->detail?->total_net ?? 0);

                $folderCancelled = $isCancelled($folder->status ?? null)
                    || $isCancelled($folder->offer_status ?? null);

                $folderActive = (int) $activeFolderId === (int) $folder->id;

                $workflowLabel = $folder->workflow_status_label
                    ?? $folder->status_label
                    ?? $offerStatusLabel($folder->status ?? null);

                $workflowClass = $folderCancelled
                    ? 'status-danger'
                    : (
                        $folderActive
                        ? 'status-success'
                        : 'status-draft'
                    );

                $docStatusLabel = $folder->detail?->documentStatusLabel()
                    ?? match (strtolower((string) ($folder->document_status ?? 'offer'))) {
                        'deal' => 'Deal',
                        'auftrag' => 'Auftrag',
                        default => 'Angebot',
                    };
                                        @endphp

                                        <a href="{{ url('admin/offers/folders/' . $folder->id . '?new_offer=1') }}"
                                           class="of-folder {{ $folderActive ? 'is-active' : '' }} {{ $folderCancelled ? 'is-cancelled' : '' }}"
                                           style="border-left-color: {{ $folderColor }} !important;">

                                            <div class="of-folder-top">
                                                <div class="of-folder-left">
                                                    <div class="of-folder-icon"
                                                         style="background: {{ $folderColor }}1A; color: {{ $folderColor }};">
                                                        <i data-feather="{{ $folderCancelled ? 'lock' : 'folder' }}"></i>
                                                    </div>

                                                    <div style="min-width:0;">
                                                        <div class="of-folder-name">
                                                            {{ $folder->name ?? 'Ordner #' . $folder->id }}
                                                        </div>

                                                        <div class="of-folder-sub">
                                                            {{ $docStatusLabel }}
                                                            ·
                                                            {{ $folder->created_at ? $folder->created_at->format('d.m.Y H:i') : '–' }}
                                                        </div>
                                                    </div>
                                                </div>

                                                @if($folder->creator)
                                                    <img src="{{ $employeeImage($folder->creator) }}"
                                                         class="of-avatar"
                                                         style="width:28px;height:28px;"
                                                         title="{{ $folder->creator->name }} {{ $folder->creator->lastname }}"
                                                         alt="{{ $folder->creator->name }}"
                                                         onerror="this.src='{{ asset('images/gender/male.png') }}'">
                                                @endif
                                            </div>

                                            <div class="of-folder-bottom">
                                                <div>
                                                    <span class="of-badge {{ $workflowClass }}">
                                                        @if($folderCancelled)
                                                            <i data-feather="lock"></i>
                                                        @elseif($folderActive)
                                                            <i data-feather="activity"></i>
                                                        @endif

                                                        {{ $folderActive ? 'Aktiv' : $workflowLabel }}
                                                    </span>
                                                </div>

                                                <div style="text-align:right;">
                                                    <div class="of-price">
                                                        {{ $money($folderGross) }}
                                                    </div>
                                                    <div class="of-net">
                                                        Netto {{ $money($folderNet) }}
                                                    </div>
                                                </div>
                                            </div>

                                            @if($folderActive)
                                                <div class="of-active-note" style="margin:0;padding:9px 10px;border-radius:14px;font-size:12px;">
                                                    <i data-feather="check-circle"></i>
                                                    <div>Dies ist aktuell die aktive Angebotsvariante.</div>
                                                </div>
                                            @endif

                                            @if($folderCancelled)
                                                <div class="of-active-note" style="margin:0;padding:9px 10px;border-radius:14px;font-size:12px;background:rgba(229,6,86,.08);border-color:rgba(229,6,86,.35);">
                                                    <i data-feather="lock"></i>
                                                    <div>Diese Variante ist storniert/gesperrt und sollte nicht als aktive Version verwendet werden.</div>
                                                </div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
