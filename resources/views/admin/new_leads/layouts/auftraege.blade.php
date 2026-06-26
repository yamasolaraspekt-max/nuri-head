@php
    $deals = collect($deals ?? []);
    $notes = collect($notes ?? []);

    $analytics = $analytics ?? [
        'total_deals' => 0,
        'open_deals' => 0,
        'completed_deals' => 0,
        'cancelled_deals' => 0,
        'total_price' => 0,
        'measurements' => 0,
        'delivery_notes' => 0,
    ];

    $money = function ($value) {
        return number_format((float) $value, 2, ',', '.') . ' €';
    };

    $employeeImage = function ($emp) {
        if (!empty($emp?->image)) {
            return asset('images/employee/' . $emp->image);
        }

        return asset('images/gender/male.png');
    };

    $isCancelled = function ($value) {
        return in_array(strtolower((string) $value), [
            'cancel',
            'cancelled',
            'canceled',
            'storniert',
        ], true);
    };

    $isDone = function ($value) {
        return in_array(strtolower((string) $value), [
            'done',
            'complete',
            'completed',
            'geschlossen',
            'closed',
            'end',
        ], true);
    };

    $statusLabel = function ($status) {
        return match (strtolower((string) $status)) {
            'done', 'complete', 'completed', 'geschlossen', 'closed', 'end' => 'Abgeschlossen',
            'cancel', 'cancelled', 'canceled', 'storniert' => 'Storniert',
            'confirmed' => 'Bestätigt',
            'delivered' => 'Geliefert',
            'checked' => 'Geprüft',
            'draft' => 'Entwurf',
            'open' => 'Offen',
            default => $status ? ucfirst((string) $status) : 'Offen',
        };
    };

    $statusClass = function ($status) use ($isCancelled, $isDone) {
        if ($isCancelled($status)) {
            return 'status-danger';
        }

        if ($isDone($status)) {
            return 'status-success';
        }

        return match (strtolower((string) $status)) {
            'confirmed', 'checked' => 'status-success',
            'delivered' => 'status-blue',
            'draft' => 'status-draft',
            default => 'status-open',
        };
    };

    $activeDealId = optional($deals->first(function ($deal) use ($isCancelled, $isDone) {
        return !$isCancelled($deal->status) && !$isDone($deal->status);
    }))->id;

    if (!$activeDealId && $deals->isNotEmpty()) {
        $activeDealId = $deals->first()->id;
    }
@endphp

<style>
    :root {
        --od-green: #93c21c;
        --od-green-soft: #cfe09b;
        --od-blue: #74b2d4;
        --od-blue-soft: #c0d8ea;
        --od-orange: #f8ac00;
        --od-pink: #e50656;

        --od-bg: #ffffff;
        --od-card: #ffffff;
        --od-text: #374151;
        --od-muted: #6b7280;
        --od-border: #c0d8ea;

        --od-radius: 16px;
        --od-radius-lg: 22px;
        --od-transition: all .18s ease;
    }

    .od-wrap {
        color: var(--od-text);
        background: var(--od-bg);
        padding: 10px;
        max-width: 100%;
        overflow-x: hidden;
    }

    .od-wrap *,
    .od-wrap *::before,
    .od-wrap *::after {
        box-shadow: none !important;
        box-sizing: border-box;
    }

    .od-titlebar {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .od-title {
        font-size: 24px;
        font-weight: 900;
        color: var(--od-blue);
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        text-transform: uppercase;
    }

    .od-sub {
        font-size: 14px;
        color: var(--od-text);
        margin-top: 4px;
        line-height: 1.45;
    }

    .od-btn {
        background: var(--od-green);
        color: #ffffff;
        border: 0;
        padding: 10px 16px;
        border-radius: 999px;
        font-weight: 900;
        cursor: pointer;
        transition: var(--od-transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        min-height: 42px;
        white-space: nowrap;
    }

    .od-btn:hover {
        background: var(--od-blue);
        color: #ffffff;
        text-decoration: none;
    }

    .od-analytics {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 18px;
    }

    .od-stat {
        background: var(--od-card);
        border: 1px solid var(--od-border);
        border-radius: var(--od-radius-lg);
        padding: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-height: 92px;
        min-width: 0;
    }

    .od-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        color: #ffffff;
    }

    .od-stat-icon.total { background: var(--od-blue); }
    .od-stat-icon.open { background: var(--od-orange); }
    .od-stat-icon.done { background: var(--od-green); }
    .od-stat-icon.locked { background: var(--od-pink); }

    .od-stat-label {
        font-size: 9px;
        font-weight: 900;
        color: var(--od-muted);
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .od-stat-value {
        font-size: 22px;
        font-weight: 900;
        color: var(--od-blue);
        line-height: 1.1;
        margin-top: 4px;
        overflow-wrap: anywhere;
    }

    .od-stat-sub {
        font-size: 9px;
        color: var(--od-text);
        margin-top: 4px;
        line-height: 1.35;
    }

    .od-active-note {
        border: 1px solid var(--od-green);
        background: rgba(147, 194, 28, .10);
        border-radius: var(--od-radius-lg);
        padding: 12px 14px;
        margin-bottom: 16px;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        color: var(--od-text);
        font-size: 13px;
        font-weight: 800;
        line-height: 1.45;
    }

    .od-active-note svg {
        width: 18px;
        height: 18px;
        color: var(--od-green);
        flex: 0 0 auto;
        margin-top: 1px;
    }

    .od-card-head {
        padding: 2px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .od-card-title {
        margin: 0;
        font-size: 16px;
        font-weight: 900;
        color: var(--od-blue);
    }

    .od-card-sub {
        font-size: 12px;
        color: var(--od-text);
        margin-top: 4px;
    }

    .od-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
        max-width: 100%;
    }

    .od-item {
        background: #ffffff;
        border: 1px solid var(--od-border);
        border-radius: var(--od-radius-lg);
        transition: var(--od-transition);
        overflow: hidden;
        max-width: 100%;
    }

    .od-item:hover {
        border-color: var(--od-green);
        transform: translateY(-1px);
    }

    .od-item.is-active {
        border-color: var(--od-green);
    }

    .od-item.is-cancelled {
        border-color: rgba(229, 6, 86, .45);
        background: rgba(229, 6, 86, .025);
    }

    .od-item-main {
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

    .od-cell {
        min-width: 0;
        max-width: 100%;
    }

    .od-cell-title {
        font-size: 9px;
        font-weight: 900;
        color: var(--od-muted);
        text-transform: uppercase;
        margin-bottom: 6px;
        display: none;
    }

    .od-order-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        background: var(--od-green-soft);
        border: 1px solid var(--od-border);
        min-height: 82px;
        padding: 8px;
        text-align: center;
    }

    .od-order-no {
        font-size: 12px;
        line-height: 1.25;
        font-weight: 900;
        color: var(--od-blue);
        overflow-wrap: anywhere;
    }

    .od-order-label {
        margin-top: 5px;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .06em;
        color: var(--od-text);
        text-transform: uppercase;
    }

    .od-ttl {
        font-weight: 900;
        font-size: 13px;
        margin-bottom: 6px;
        color: var(--od-blue);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .od-subt {
        font-size: 13px;
        color: var(--od-text);
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .od-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
        color: var(--od-text);
    }

    .od-badge.status-draft {
        background: var(--od-blue-soft);
        color: var(--od-text);
    }

    .od-badge.status-open {
        background: var(--od-orange);
        color: #ffffff;
    }

    .od-badge.status-blue {
        background: var(--od-blue);
        color: #ffffff;
    }

    .od-badge.status-success {
        background: var(--od-green);
        color: #ffffff;
    }

    .od-badge.status-danger {
        background: var(--od-pink);
        color: #ffffff;
    }

    .od-badge.active {
        background: var(--od-green);
        color: #ffffff;
    }

    .od-meta-stack {
        display: flex;
        flex-direction: column;
        gap: 8px;
        min-width: 0;
    }

    .od-meta-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--od-text);
        background: #ffffff;
        border: 1px solid var(--od-border);
        border-radius: 999px;
        padding: 8px 10px;
        min-width: 0;
        max-width: 100%;
    }

    .od-meta-pill svg {
        width: 14px;
        height: 14px;
        flex: 0 0 auto;
    }

    .od-meta-pill span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        min-width: 0;
    }

    .od-avatar-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
    }

    .od-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ffffff;
        background: var(--od-blue-soft);
    }

    .od-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .od-profile-btn,
    .od-toggle {
        min-height: 38px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid var(--od-blue);
        background: var(--od-blue);
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        cursor: pointer;
        transition: var(--od-transition);
        text-decoration: none;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .od-toggle {
        background: #ffffff;
        color: var(--od-blue);
        border-color: var(--od-border);
    }

    .od-profile-btn:hover,
    .od-toggle:hover {
        background: var(--od-green);
        border-color: var(--od-green);
        color: #ffffff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .od-collapse {
        display: none;
        border-top: 1px solid var(--od-border);
        padding: 14px 16px 16px;
        background: #ffffff;
    }

    .od-item.is-open .od-collapse {
        display: block;
    }

    .od-item.is-open .od-toggle-icon {
        transform: rotate(180deg);
    }

    .od-detail-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 10px;
    }

    .od-detail-box {
        border: 1px solid var(--od-border);
        border-radius: var(--od-radius);
        padding: 12px;
        background: #ffffff;
        min-width: 0;
    }

    .od-detail-title {
        font-size: 12px;
        font-weight: 900;
        color: var(--od-blue);
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
    }

    .od-detail-text {
        font-size: 13px;
        color: var(--od-text);
        line-height: 1.5;
        overflow-wrap: anywhere;
    }

    .od-empty {
        text-align: center;
        padding: 44px 16px;
        color: var(--od-muted);
        background: #ffffff;
        border: 1px dashed var(--od-border);
        border-radius: 16px;
    }

    @media(max-width: 1400px) {
        .od-item-main {
            grid-template-columns:
                minmax(86px, 100px)
                minmax(220px, 1.3fr)
                minmax(180px, 1fr);
        }
    }

    @media(max-width: 1200px) {
        .od-analytics {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .od-item-main {
            grid-template-columns:
                minmax(86px, 100px)
                minmax(220px, 1fr);
        }

        .od-cell:nth-child(3),
        .od-cell:nth-child(4),
        .od-cell:nth-child(5) {
            grid-column: 1 / -1;
        }

        .od-cell-title {
            display: block;
        }

        .od-actions {
            justify-content: flex-start;
        }

        .od-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    @media(max-width: 768px) {
        .od-wrap {
            padding: 8px;
        }

        .od-title {
            font-size: 22px;
        }

        .od-btn,
        .od-profile-btn,
        .od-toggle {
            width: 100%;
            justify-content: center;
        }

        .od-item-main {
            grid-template-columns: 1fr;
            padding: 14px;
        }

        .od-order-badge {
            min-height: 72px;
            align-items: flex-start;
            text-align: left;
            padding: 12px;
        }

        .od-actions {
            justify-content: stretch;
        }

        .od-meta-pill {
            border-radius: 16px;
            align-items: flex-start;
        }

        .od-meta-pill span {
            white-space: normal;
            overflow-wrap: anywhere;
        }
    }

    @media(max-width: 700px) {
        .od-analytics {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="od-wrap contentDeals">
    <div id="dealFilterContext"
         data-customer="{{ $customer->id ?? '' }}"
         data-alternative="{{ $alternative->id ?? '' }}"
         data-product="{{ $productData->product_id ?? '' }}">
    </div>

    <div class="od-titlebar">
        <div>
            <h2 class="od-title">
                <i data-feather="briefcase"></i>
                <span>Aufträge</span>
            </h2>

            <div class="od-sub">
                Auftragsdaten, Freigaben, Feinaufmaß und Lieferscheine für diesen Kunden- und Produktkontext.
            </div>
        </div>
 
    </div>

    <div class="od-analytics">
        <div class="od-stat">
            <div class="od-stat-icon total">
                <i data-feather="briefcase"></i>
            </div>
            <div>
                <div class="od-stat-label">Aufträge</div>
                <div class="od-stat-value">{{ $analytics['total_deals'] }}</div>
                <div class="od-stat-sub">Aufträge insgesamt</div>
            </div>
        </div>

        <div class="od-stat">
            <div class="od-stat-icon open">
                <i data-feather="loader"></i>
            </div>
            <div>
                <div class="od-stat-label">Offen</div>
                <div class="od-stat-value">{{ $analytics['open_deals'] }}</div>
                <div class="od-stat-sub">Aktive Aufträge</div>
            </div>
        </div>

        <div class="od-stat">
            <div class="od-stat-icon done">
                <i data-feather="check-circle"></i>
            </div>
            <div>
                <div class="od-stat-label">Abgeschlossen</div>
                <div class="od-stat-value">{{ $analytics['completed_deals'] }}</div>
                <div class="od-stat-sub">Fertige Aufträge</div>
            </div>
        </div>

        <div class="od-stat">
            <div class="od-stat-icon locked">
                <i data-feather="lock"></i>
            </div>
            <div>
                <div class="od-stat-label">Storniert</div>
                <div class="od-stat-value">{{ $analytics['cancelled_deals'] }}</div>
                <div class="od-stat-sub">{{ $money($analytics['total_price']) }} Gesamtwert</div>
            </div>
        </div>
    </div>

    @if($activeDealId)
        <div class="od-active-note">
            <i data-feather="activity"></i>
            <div>
                <strong>Aktiver Auftrag:</strong>
                Der grün markierte Auftrag ist aktuell der relevante Auftrag. Stornierte Aufträge sind mit einem Schloss gekennzeichnet.
            </div>
        </div>
    @endif

    <div class="od-card-head">
        <div>
            <h3 class="od-card-title">Auftragsliste</h3>
            <div class="od-card-sub">{{ $deals->count() }} Aufträge gefunden</div>
        </div>
    </div>

    @if($deals->isEmpty())
        <div class="od-empty">
            <i data-feather="briefcase" style="width:32px;height:32px;margin-bottom:10px;"></i>
            <div>Bisher wurde noch kein Auftrag für dieses Produkt erstellt.</div>
        </div>
    @else
        <div class="od-list">
            @foreach($deals as $deal)
                @php
                    $dealCancelled = $isCancelled($deal->status);
                    $dealDone = $isDone($deal->status);
                    $dealActive = (int) $activeDealId === (int) $deal->id;

                    $createdDate = $deal->created_at ? $deal->created_at->format('d') : '--';
                    $createdMonth = $deal->created_at ? $deal->created_at->translatedFormat('M') : '---';

                    $dealNo = $deal->order_number ?: ('#' . $deal->id);
                    $dealNotes = collect($notes->get($deal->id, []));
                    $latestMeasurement = $deal->latestMeasurement;
                    $latestDeliveryNote = $deal->latestDeliveryNote;
                @endphp

                <div class="od-item {{ $dealActive ? 'is-active is-open' : '' }} {{ $dealCancelled ? 'is-cancelled' : '' }}"
                     data-deal-card
                     data-deal-id="{{ $deal->id }}">

                    <div class="od-item-main" data-deal-toggle>
                        <div class="od-cell">
                            <div class="od-cell-title">Auftrag</div>

                            <div class="od-order-badge">
                                <div class="od-order-no">{{ $dealNo }}</div>
                                <div class="od-order-label">{{ $createdDate }} {{ $createdMonth }}</div>
                            </div>
                        </div>

                        <div class="od-cell">
                            <div class="od-cell-title">Übersicht</div>

                            <div>
                                <div class="od-ttl">
                                    <span>Auftrag {{ $dealNo }}</span>

                                    <span class="od-badge {{ $statusClass($deal->status) }}">
                                        @if($dealCancelled)
                                            <i data-feather="lock"></i>
                                        @endif
                                        {{ $statusLabel($deal->status) }}
                                    </span>

                                    @if($dealActive)
                                        <span class="od-badge active">
                                            <i data-feather="activity"></i>
                                            Aktiv
                                        </span>
                                    @endif
                                </div>

                                <div class="od-subt">
                                    <strong>Dienstleistung:</strong>
                                    {{ $deal->service ?? 'Nicht definiert' }}
                                    <br>

                                    <strong>Produkt:</strong>
                                    {{ $deal->product->article_group ?? $productData->articleGroup->article_group ?? '-' }}
                                    <br>

                                    <strong>Angebot:</strong>
                                    {{ $deal->offer_number ?? $deal->offer?->offer_no ?? '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="od-cell">
                            <div class="od-cell-title">Status</div>

                            <div class="od-meta-stack">
                                <div class="od-meta-pill">
                                    <i data-feather="euro"></i>
                                    <span>Preis: {{ $money($deal->price ?? 0) }}</span>
                                </div>

                                <div class="od-meta-pill">
                                    <i data-feather="ruler"></i>
                                    <span>Feinaufmaß: {{ $deal->measurements->count() }}</span>
                                </div>

                                <div class="od-meta-pill">
                                    <i data-feather="truck"></i>
                                    <span>Lieferscheine: {{ $deal->deliveryNotes->count() }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="od-cell">
                            <div class="od-cell-title">Mitarbeiter</div>

                            @if($deal->author)
                                <div class="od-avatar-row">
                                    <img src="{{ $employeeImage($deal->author) }}"
                                         class="od-avatar"
                                         title="{{ $deal->author->name }} {{ $deal->author->lastname }}"
                                         alt="{{ $deal->author->name }}"
                                         onerror="this.src='{{ asset('images/gender/male.png') }}'">

                                    <div class="od-subt">
                                        <strong>{{ $deal->author->name }}</strong>
                                        {{ $deal->author->lastname }}
                                    </div>
                                </div>
                            @else
                                <div class="od-meta-pill">
                                    <i data-feather="user-x"></i>
                                    <span>System / Unbekannt</span>
                                </div>
                            @endif
                        </div>

                        <div class="od-cell">
                            <div class="od-cell-title">Aktionen</div>

                            <div class="od-actions">
                                @if($deal->folder)
                                    <a href="{{ url('admin/offers/folders/' . $deal->folder->id . '?new_offer=1') }}"
                                       class="od-profile-btn"
                                       onclick="event.stopPropagation();">
                                        <i data-feather="external-link"></i>
                                        Dokument
                                    </a>
                                @endif

                                <button type="button"
                                        class="od-toggle"
                                        onclick="event.stopPropagation(); toggleDealCollapse(this);">
                                    <i class="od-toggle-icon" data-feather="chevron-down"></i>
                                    Details
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="od-collapse">
                        <div class="od-detail-grid">
                            <div class="od-detail-box">
                                <div class="od-detail-title">
                                    <i data-feather="clipboard"></i>
                                    Auftragsdaten
                                </div>

                                <div class="od-detail-text">
                                    <strong>Status:</strong> {{ $statusLabel($deal->status) }}<br>
                                    <strong>Projektstatus:</strong> {{ $deal->project_status ?: '-' }}<br>
                                    <strong>Messstatus:</strong> {{ $deal->measurement_status ?: '-' }}<br>
                                    <strong>Unterschrieben am:</strong> {{ $deal->sign_date ? \Carbon\Carbon::parse($deal->sign_date)->format('d.m.Y') : '-' }}<br>
                                    <strong>Bestätigt am:</strong> {{ $deal->confirmed_at ? \Carbon\Carbon::parse($deal->confirmed_at)->format('d.m.Y H:i') : '-' }}<br>
                                    <strong>Geliefert am:</strong> {{ $deal->delivered_at ? \Carbon\Carbon::parse($deal->delivered_at)->format('d.m.Y H:i') : '-' }}
                                </div>
                            </div>

                            <div class="od-detail-box">
                                <div class="od-detail-title">
                                    <i data-feather="ruler"></i>
                                    Feinaufmaß
                                </div>

                                @if($latestMeasurement)
                                    <div class="od-detail-text">
                                        <strong>Nr.:</strong> {{ $latestMeasurement->measurement_no }}<br>
                                        <strong>Status:</strong> {{ $latestMeasurement->status ?: '-' }}<br>
                                        <strong>Typ:</strong> {{ $latestMeasurement->measurement_kind }}<br>
                                        <strong>Material:</strong>
                                        {{ (int) ($latestMeasurement->materials_approved_count ?? 0) }}
                                        /
                                        {{ (int) ($latestMeasurement->materials_total_count ?? 0) }}
                                        freigegeben<br>
                                        <strong>Erstellt:</strong> {{ $latestMeasurement->created_at ? $latestMeasurement->created_at->format('d.m.Y H:i') : '-' }}
                                    </div>
                                @else
                                    <div class="od-detail-text text-muted">
                                        Kein Feinaufmaß vorhanden.
                                    </div>
                                @endif
                            </div>

                            <div class="od-detail-box">
                                <div class="od-detail-title">
                                    <i data-feather="truck"></i>
                                    Lieferschein
                                </div>

                                @if($latestDeliveryNote)
                                    <div class="od-detail-text">
                                        <strong>Nr.:</strong> {{ $latestDeliveryNote->delivery_note ?: '#' . $latestDeliveryNote->id }}<br>
                                        <strong>Status:</strong> {{ $latestDeliveryNote->status ?: '-' }}<br>
                                        <strong>Fortschritt:</strong> {{ (int) ($latestDeliveryNote->progress ?? 0) }}%<br>
                                        <strong>Bestelldatum:</strong> {{ $latestDeliveryNote->order_date ? $latestDeliveryNote->order_date->format('d.m.Y') : '-' }}<br>
                                        <strong>Übergabe:</strong> {{ $latestDeliveryNote->handover_date ? $latestDeliveryNote->handover_date->format('d.m.Y') : '-' }}
                                    </div>
                                @else
                                    <div class="od-detail-text text-muted">
                                        Kein Lieferschein vorhanden.
                                    </div>
                                @endif
                            </div>

                            <div class="od-detail-box">
                                <div class="od-detail-title">
                                    <i data-feather="message-circle"></i>
                                    Notizen
                                </div>

                                @if($dealNotes->isNotEmpty())
                                    <div class="od-detail-text">
                                        @foreach($dealNotes->take(4) as $note)
                                            <div style="margin-bottom:8px;">
                                                {{ \Illuminate\Support\Str::limit(strip_tags($note->description), 160) }}
                                            </div>
                                        @endforeach

                                        @if($dealNotes->count() > 4)
                                            <strong>+ {{ $dealNotes->count() - 4 }} weitere Notizen</strong>
                                        @endif
                                    </div>
                                @else
                                    <div class="od-detail-text text-muted">
                                        Keine Notizen vorhanden.
                                    </div>
                                @endif
                            </div>

                            <div class="od-detail-box">
                                <div class="od-detail-title">
                                    <i data-feather="info"></i>
                                    Info
                                </div>

                                <div class="od-detail-text">
                                    {{ $deal->info ?: 'Keine zusätzliche Information vorhanden.' }}
                                </div>
                            </div>

                            <div class="od-detail-box">
                                <div class="od-detail-title">
                                    <i data-feather="map-pin"></i>
                                    Standort
                                </div>

                                <div class="od-detail-text">
                                    {{ $deal->location ?: ($deal->alternative->full_address ?? $deal->alternative->street ?? '-') }}
                                </div>
                            </div>
                        </div>

                        @if($dealActive)
                            <div class="od-active-note" style="margin-top:12px;margin-bottom:0;">
                                <i data-feather="check-circle"></i>
                                <div>Dies ist aktuell der aktive Auftrag für diesen Kontext.</div>
                            </div>
                        @endif

                        @if($dealCancelled)
                            <div class="od-active-note" style="margin-top:12px;margin-bottom:0;background:rgba(229,6,86,.08);border-color:rgba(229,6,86,.35);">
                                <i data-feather="lock"></i>
                                <div>Dieser Auftrag ist storniert/gesperrt und sollte nicht weiter bearbeitet werden.</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    (function () {
        "use strict";

        window.toggleDealCollapse = function (button) {
            const card = button.closest('[data-deal-card]');
            if (!card) return;

            card.classList.toggle('is-open');

            if (window.feather) {
                window.feather.replace();
            }
        };

        function replaceIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        function bindDealCards() {
            document.querySelectorAll('[data-deal-toggle]').forEach(function (row) {
                if (row.dataset.bound === '1') return;

                row.dataset.bound = '1';

                row.addEventListener('click', function (event) {
                    const ignored = event.target.closest('a, button, input, select, textarea, label, form');

                    if (ignored) return;

                    const card = row.closest('[data-deal-card]');
                    if (!card) return;

                    card.classList.toggle('is-open');

                    replaceIcons();
                });
            });
        }

        replaceIcons();
        bindDealCards();

        if (window.jQuery && $.fn.tooltip) {
            $('[title]').tooltip();
        }
    })();
</script>