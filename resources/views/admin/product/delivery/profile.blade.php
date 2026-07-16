@extends('admin.layouts.app')

@section('title', 'Lieferschein Profil')

@section('style')
<style>
:root{
    --dn-primary:var(--sa-accent);
    --dn-primary-dark:var(--sa-accent-hover);
    --dn-bg:#f3f4f6;
    --dn-card:#ffffff;
    --dn-text:#111827;
    --dn-muted:#6b7280;
    --dn-border:#e5e7eb;
    --dn-soft:#f9fafb;
    --dn-green:#10b981;
    --dn-orange:#f59e0b;
    --dn-red:#ef4444;
    --dn-blue:#3b82f6;
}

.dn-wrap {
    max-width: 1450px;
    margin: 0px auto;
    padding: 100px 28px;
    color: var(--dn-text);
    font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.dn-header{
    display:grid;
    grid-template-columns:1.5fr .8fr;
    gap:18px;
    margin-bottom:18px;
}

.dn-hero,
.dn-card{
    background:var(--dn-card);
    border:1px solid var(--dn-border);
    border-radius:22px;
    box-shadow:0 10px 30px rgba(15,23,42,.07);
}

.dn-hero{
    padding:24px;
    position:relative;
    overflow:hidden;
}

.dn-hero:before{
    content:"";
    position:absolute;
    inset:0;
    background:linear-gradient(135deg, rgba(147,194,28,.14), transparent 45%);
    pointer-events:none;
}

.dn-hero-content{
    position:relative;
    z-index:1;
}

.dn-back{
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-bottom:16px;
    color:var(--dn-muted);
    text-decoration:none;
    font-weight:800;
}

.dn-back:hover{
    color:var(--dn-text);
    text-decoration:none;
}

.dn-title-row{
    display:flex;
    align-items:flex-start;
    justify-content:space-between;
    gap:14px;
    flex-wrap:wrap;
}

.dn-title{
    margin:0;
    font-size:30px;
    font-weight:950;
    letter-spacing:-.04em;
}

.dn-subtitle{
    margin-top:8px;
    color:var(--dn-muted);
    font-size:14px;
}

.dn-badge{
    display:inline-flex;
    align-items:center;
    gap:7px;
    padding:8px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:900;
    white-space:nowrap;
}

.dn-badge.green{background:#ecfdf5;color:#047857;}
.dn-badge.orange{background:#fffbeb;color:#b45309;}
.dn-badge.red{background:#fef2f2;color:#b91c1c;}
.dn-badge.blue{background:#eff6ff;color:#1d4ed8;}
.dn-badge.gray{background:#f3f4f6;color:#4b5563;}

.dn-actions{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-top:22px;
}

.dn-btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:11px 15px;
    border-radius:14px;
    border:1px solid var(--dn-border);
    background:#fff;
    color:var(--dn-text);
    font-weight:900;
    text-decoration:none;
}

.dn-btn:hover{
    background:var(--dn-soft);
    color:var(--dn-text);
    text-decoration:none;
}

.dn-btn.primary{
    background:var(--dn-primary);
    border-color:var(--dn-primary);
    color:#fff !important;
}

.dn-btn.primary:hover{
    background:var(--dn-primary-dark);
    color:#fff !important;
}

.dn-progress-card{
    padding:22px;
}

.dn-progress-circle{
    width:150px;
    height:150px;
    margin:0 auto 14px;
    border-radius:999px;
    background:
        conic-gradient(var(--dn-primary) calc(var(--progress) * 1%), #eef2f7 0);
    display:flex;
    align-items:center;
    justify-content:center;
}

.dn-progress-inner{
    width:112px;
    height:112px;
    border-radius:999px;
    background:#fff;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    box-shadow:inset 0 0 0 1px var(--dn-border);
}

.dn-progress-value{
    font-size:28px;
    font-weight:950;
}

.dn-progress-label{
    font-size:12px;
    color:var(--dn-muted);
    font-weight:800;
}

.dn-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px;
}

.dn-card{
    padding:20px;
    margin-bottom:18px;
}

.dn-card-title{
    display:flex;
    align-items:center;
    gap:10px;
    margin:0 0 16px;
    font-size:16px;
    font-weight:950;
}

.dn-info-grid{
    display:grid;
    grid-template-columns:repeat(2,minmax(0,1fr));
    gap:12px;
}

.dn-info{
    background:var(--dn-soft);
    border:1px solid var(--dn-border);
    border-radius:16px;
    padding:13px 14px;
}

.dn-label{
    font-size:11px;
    font-weight:900;
    color:var(--dn-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
    margin-bottom:5px;
}

.dn-value{
    font-size:14px;
    font-weight:800;
    color:var(--dn-text);
    word-break:break-word;
}

.dn-muted{
    color:var(--dn-muted);
}

.dn-description{
    min-height:110px;
    padding:16px;
    border-radius:16px;
    background:var(--dn-soft);
    border:1px solid var(--dn-border);
    line-height:1.65;
    color:#374151;
}

.dn-timeline{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.dn-time-item{
    display:grid;
    grid-template-columns:34px 1fr;
    gap:12px;
}

.dn-time-dot{
    width:34px;
    height:34px;
    border-radius:999px;
    background:#ecfdf5;
    color:#047857;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
}

.dn-time-box{
    background:var(--dn-soft);
    border:1px solid var(--dn-border);
    border-radius:16px;
    padding:12px 14px;
}

.dn-image-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
    gap:12px;
}

.dn-image{
    border:1px solid var(--dn-border);
    border-radius:16px;
    overflow:hidden;
    background:#fff;
}

.dn-image img{
    width:100%;
    height:130px;
    object-fit:cover;
    display:block;
}

.dn-image-name{
    padding:9px 10px;
    font-size:12px;
    font-weight:800;
    color:var(--dn-muted);
}

.dn-linked-list{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.dn-linked-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:12px;
    padding:14px;
    background:var(--dn-soft);
    border:1px solid var(--dn-border);
    border-radius:16px;
}

.dn-linked-title{
    font-weight:950;
}

.dn-empty{
    padding:28px;
    text-align:center;
    color:var(--dn-muted);
    border:1px dashed var(--dn-border);
    border-radius:18px;
    background:var(--dn-soft);
}

@media(max-width:1100px){
    .dn-header,
    .dn-grid{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){
    .dn-wrap{
        padding:0 14px;
        margin-top:90px;
    }

    .dn-info-grid{
        grid-template-columns:1fr;
    }

    .dn-title{
        font-size:24px;
    }
}
</style>
@endsection

@section('content')
@php
    $note = $deliveryNote;

    $statusClass = match($note->status) {
        'Verfügbar' => 'green',
        'Teilweise' => 'orange',
        'Nicht verfügbar' => 'red',
        default => 'blue',
    };

    $destinationLabel = ($note->destination_type ?? 'customer') === 'warehouse' ? 'Lager' : 'Kunde';

    $customerName = $note->customer
        ? ($note->customer->display_name ?? (($note->customer->firma ?: trim(($note->customer->name ?? '').' '.($note->customer->lastname ?? ''))) ?: '#'.$note->customer->id))
        : '—';

    $objectName = $note->alternative
        ? (($note->alternative->object_name ?: '#'.$note->alternative->id).' · '.trim(($note->alternative->street ?? '').' '.($note->alternative->postcode ?? '').' '.($note->alternative->city ?? '')))
        : '—';

    $employeeName = $note->handoverEmployee
        ? trim(($note->handoverEmployee->name ?? '').' '.($note->handoverEmployee->lastname ?? ''))
        : '—';

    $productName = $note->leadProductList?->product?->article_group
        ?? $note->deal?->product?->article_group
        ?? '—';

    $progress = max(0, min(100, (int)($note->progress ?? 0)));

    $pdfUrl = $note->pdf ? asset('images/delivery_note/pdf/'.$note->pdf) : null;
    $mainImageUrl = $note->image ? asset('images/delivery_note/'.$note->image) : null;
@endphp

<div class="dn-wrap">
    <div class="dn-header">
        <div class="dn-hero">
            <div class="dn-hero-content">
                <a href="{{ route('delivery-notes.index') }}" class="dn-back">
                    <i class="feather icon-arrow-left"></i>
                    Zurück zur Übersicht
                </a>

                <div class="dn-title-row">
                    <div>
                        <h1 class="dn-title">
                            {{ $note->delivery_note ?: 'Lieferschein #'.$note->id }}
                        </h1>

                        <div class="dn-subtitle">
                            Auftrag:
                            <strong>{{ $note->deal?->order_number ?: '—' }}</strong>
                            · Kunde:
                            <strong>{{ $customerName }}</strong>
                        </div>
                    </div>

                    <span class="dn-badge {{ $statusClass }}">
                        <i class="feather icon-activity"></i>
                        {{ $note->status ?: '—' }}
                    </span>
                </div>

                <div class="dn-actions">
                    <a href="{{ route('delivery-notes.index') }}" class="dn-btn">
                        <i class="feather icon-list"></i>
                        Übersicht
                    </a>

                    @if($pdfUrl)
                        <a href="{{ $pdfUrl }}" target="_blank" class="dn-btn primary">
                            <i class="feather icon-file-text"></i>
                            PDF öffnen
                        </a>
                    @endif

                    <a href="{{ route('delivery-notes.images.index', $note->id) }}" class="dn-btn">
                        <i class="feather icon-image"></i>
                        Bilder
                    </a>

                    @if($note->deal)
                        <a href="{{ url('/deal_all_list#deal-'.$note->deal->id) }}" class="dn-btn">
                            <i class="feather icon-briefcase"></i>
                            Auftrag öffnen
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="dn-card dn-progress-card" style="--progress: {{ $progress }};">
            <div class="dn-progress-circle">
                <div class="dn-progress-inner">
                    <div class="dn-progress-value">{{ $progress }}%</div>
                    <div class="dn-progress-label">Fortschritt</div>
                </div>
            </div>

            <div class="dn-info">
                <div class="dn-label">Zieltyp</div>
                <div class="dn-value">{{ $destinationLabel }}</div>
            </div>
        </div>
    </div>

    <div class="dn-grid">
        <div>
            <div class="dn-card">
                <h3 class="dn-card-title">
                    <i class="feather icon-file-text"></i>
                    Lieferschein-Daten
                </h3>

                <div class="dn-info-grid">
                    <div class="dn-info">
                        <div class="dn-label">Lieferschein-Nr.</div>
                        <div class="dn-value">{{ $note->delivery_note ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Geliefert von</div>
                        <div class="dn-value">{{ $note->delivered_from ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Bestellnummer</div>
                        <div class="dn-value">{{ $note->order_no ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Kommission</div>
                        <div class="dn-value">{{ $note->comission ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Bestellt von</div>
                        <div class="dn-value">{{ $note->order_by ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Übergabe durch</div>
                        <div class="dn-value">{{ $employeeName }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Bestelldatum</div>
                        <div class="dn-value">{{ optional($note->order_date)->format('d.m.Y') ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Übergabedatum</div>
                        <div class="dn-value">{{ optional($note->handover_date)->format('d.m.Y') ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="dn-card">
                <h3 class="dn-card-title">
                    <i class="feather icon-user"></i>
                    Kunde & Objekt
                </h3>

                <div class="dn-info-grid">
                    <div class="dn-info">
                        <div class="dn-label">Kunde</div>
                        <div class="dn-value">{{ $customerName }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Kundennummer</div>
                        <div class="dn-value">{{ $note->customer?->customer_no ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Telefon</div>
                        <div class="dn-value">{{ $note->customer?->phone ?: ($note->customer?->telephone ?: '—') }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">E-Mail</div>
                        <div class="dn-value">{{ $note->customer?->email ?: '—' }}</div>
                    </div>

                    <div class="dn-info" style="grid-column:1 / -1;">
                        <div class="dn-label">Objekt</div>
                        <div class="dn-value">{{ $objectName }}</div>
                    </div>
                </div>
            </div>

            <div class="dn-card">
                <h3 class="dn-card-title">
                    <i class="feather icon-align-left"></i>
                    Beschreibung
                </h3>

                <div class="dn-description">
                    {!! nl2br(e($note->description ?: 'Keine Beschreibung vorhanden.')) !!}
                </div>
            </div>
        </div>

        <div>
            <div class="dn-card">
                <h3 class="dn-card-title">
                    <i class="feather icon-briefcase"></i>
                    Auftrag & Produkt
                </h3>

                <div class="dn-info-grid">
                    <div class="dn-info">
                        <div class="dn-label">Auftragsnummer</div>
                        <div class="dn-value">{{ $note->deal?->order_number ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Angebotsnummer</div>
                        <div class="dn-value">{{ $note->deal?->offer_number ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Produkt</div>
                        <div class="dn-value">{{ $productName }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Auftragssumme</div>
                        <div class="dn-value">
                            {{ $note->deal?->price ? number_format((float)$note->deal->price, 2, ',', '.') : '—' }}
                        </div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Auftragsstatus</div>
                        <div class="dn-value">{{ $note->deal?->status ?: '—' }}</div>
                    </div>

                    <div class="dn-info">
                        <div class="dn-label">Niederlassung</div>
                        <div class="dn-value">{{ $note->branch?->branch ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="dn-card">
                <h3 class="dn-card-title">
                    <i class="feather icon-clock"></i>
                    Verlauf
                </h3>

                <div class="dn-timeline">
                    <div class="dn-time-item">
                        <div class="dn-time-dot">1</div>
                        <div class="dn-time-box">
                            <div class="dn-label">Erstellt</div>
                            <div class="dn-value">{{ optional($note->created_at)->format('d.m.Y H:i') ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="dn-time-item">
                        <div class="dn-time-dot">2</div>
                        <div class="dn-time-box">
                            <div class="dn-label">Aktualisiert</div>
                            <div class="dn-value">{{ optional($note->updated_at)->format('d.m.Y H:i') ?: '—' }}</div>
                        </div>
                    </div>

                    <div class="dn-time-item">
                        <div class="dn-time-dot">3</div>
                        <div class="dn-time-box">
                            <div class="dn-label">Übergabe</div>
                            <div class="dn-value">{{ optional($note->handover_date)->format('d.m.Y') ?: 'Noch nicht gesetzt' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dn-card">
                <h3 class="dn-card-title">
                    <i class="feather icon-link"></i>
                    Verlinkte Lieferscheine
                </h3>

                @if($note->linkedNotes->count())
                    <div class="dn-linked-list">
                        @foreach($note->linkedNotes as $child)
                            <div class="dn-linked-item">
                                <div>
                                    <div class="dn-linked-title">{{ $child->delivery_note ?: '#'.$child->id }}</div>
                                    <div class="dn-muted">
                                        {{ $child->status ?: '—' }} · {{ (int)($child->progress ?? 0) }}%
                                    </div>
                                </div>

                                <a href="{{ route('delivery-notes.profile', $child->id) }}" class="dn-btn">
                                    Öffnen
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="dn-empty">Keine verlinkten Lieferscheine vorhanden.</div>
                @endif
            </div>

            <div class="dn-card">
                <h3 class="dn-card-title">
                    <i class="feather icon-image"></i>
                    Bilder
                </h3>

                @if($mainImageUrl || $note->images->count())
                    <div class="dn-image-grid">
                        @if($mainImageUrl)
                            <a href="{{ $mainImageUrl }}" target="_blank" class="dn-image">
                                <img src="{{ $mainImageUrl }}" alt="Lieferschein Bild">
                                <div class="dn-image-name">Hauptbild</div>
                            </a>
                        @endif

                        @foreach($note->images as $image)
                            @php
                                $imageUrl = asset('images/delivery_note/'.$image->image);
                            @endphp

                            <a href="{{ $imageUrl }}" target="_blank" class="dn-image">
                                <img src="{{ $imageUrl }}" alt="{{ $image->name ?: 'Bild' }}">
                                <div class="dn-image-name">{{ $image->name ?: 'Bild #'.$image->id }}</div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="dn-empty">Keine Bilder vorhanden.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection