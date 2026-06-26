@extends('admin.layouts.app')
@section('title', 'Heizkörperkonfiguration')

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

<style>
:root{
    --bg:#f3f4f6;
    --card:#fff;
    --text:#111827;
    --muted:#6b7280;
    --border:#e5e7eb;
    --primary:#93c21c;
    --primary-hover:#7baa18;
    --primary-soft:#f4fae7;
    --danger:#ef4444;
    --danger-soft:#fef2f2;
    --shadow:0 8px 24px -18px rgba(0,0,0,.35);
}

.rc-wrap { 
    color:var(--text);
    font-family:Inter, system-ui, -apple-system, sans-serif;
}

.rc-head{
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    gap:16px;
    flex-wrap:wrap;
    margin-bottom:20px;
}

.rc-title{
    font-size:26px;
    font-weight:900;
    letter-spacing:-.03em;
}

.rc-sub{
    color:var(--muted);
    font-size:14px;
    margin-top:4px;
}

.rc-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:18px;
    overflow:hidden;
    box-shadow:var(--shadow);
}

.rc-panel{
    background:#fff;
    border:1px solid var(--border);
    border-radius:18px;
    padding:16px;
    box-shadow:var(--shadow);
    margin-bottom:18px;
}

.rc-toolbar{
    display:grid;
    grid-template-columns:1.5fr 1.5fr auto;
    gap:12px;
    align-items:end;
}

.rc-btn{
    border:0;
    background:var(--primary);
    color:#fff;
    border-radius:10px;
    padding:10px 15px;
    font-weight:900;
    cursor:pointer;
    text-decoration:none;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:42px;
}

.rc-btn:hover{
    background:var(--primary-hover);
    color:#fff;
    text-decoration:none;
}

.rc-btn:disabled{
    opacity:.55;
    cursor:not-allowed;
}

.rc-btn-soft{
    border:1px solid var(--border);
    background:#fff;
    color:var(--text);
    border-radius:10px;
    padding:10px 14px;
    font-weight:800;
    cursor:pointer;
    text-decoration:none;
}

.rc-icon-btn{
    width:36px;
    height:36px;
    border-radius:9px;
    border:1px solid var(--border);
    background:#fff;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    color:var(--muted);
}

.rc-icon-btn.edit{
    background:var(--primary-soft);
    color:var(--primary);
    border-color:#d8edaa;
}

.rc-icon-btn.danger{
    background:var(--danger-soft);
    color:var(--danger);
    border-color:#fecaca;
}

.rc-stats{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
    margin-bottom:18px;
}

.rc-stat{
    background:#fff;
    border:1px solid var(--border);
    border-radius:16px;
    padding:16px;
    box-shadow:var(--shadow);
}

.rc-stat-label{
    font-size:11px;
    color:var(--muted);
    text-transform:uppercase;
    letter-spacing:.07em;
    font-weight:900;
}

.rc-stat-value{
    font-size:24px;
    font-weight:900;
    margin-top:5px;
}

.rc-label{
    font-size:11px;
    color:var(--muted);
    font-weight:900;
    text-transform:uppercase;
    letter-spacing:.06em;
    display:block;
    margin-bottom:6px;
}

.rc-input,
.rc-select{
    width:100%;
    border:1px solid var(--border);
    background:#f9fafb;
    border-radius:10px;
    padding:10px 12px;
    outline:none;
    font-size:14px;
}

.rc-input:focus,
.rc-select:focus{
    border-color:var(--primary);
    background:#fff;
    box-shadow:0 0 0 3px var(--primary-soft);
}

.rc-summary{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:14px;
    margin-top:14px;
}

.rc-summary-item{
    background:#f9fafb;
    border:1px solid var(--border);
    border-radius:14px;
    padding:12px;
}

.rc-summary-value{
    font-size:14px;
    font-weight:900;
    margin-top:4px;
}

.rc-table-wrap{
    overflow:auto;
}

.rc-table{
    width:100%;
    min-width:1120px;
    border-collapse:collapse;
}

.rc-table th,
.rc-table td{
    padding:13px 14px;
    border-bottom:1px solid var(--border);
    vertical-align:middle;
}

.rc-table th{
    background:#fafafa;
    color:var(--muted);
    font-size:11px;
    text-transform:uppercase;
    letter-spacing:.06em;
    font-weight:900;
}

.rc-avatar{
    width:76px;
    height:58px;
    border-radius:12px;
    border:1px solid var(--border);
    background:var(--primary-soft);
    color:var(--primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:900;
    overflow:hidden;
}

.rc-avatar img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.rc-name{
    font-weight:900;
}

.rc-muted{
    color:var(--muted);
    font-size:12px;
    margin-top:3px;
}

.rc-badge{
    display:inline-flex;
    padding:5px 9px;
    border-radius:999px;
    font-size:11px;
    font-weight:900;
    background:var(--primary-soft);
    color:var(--primary);
}

.rc-actions{
    display:flex;
    justify-content:flex-end;
    gap:7px;
}

.rc-empty{
    padding:50px;
    text-align:center;
    color:var(--muted);
}

.rc-modal-backdrop{
    position:fixed;
    inset:0;
    background:rgba(17,24,39,.58);
    backdrop-filter:blur(3px);
    z-index:2000;
    display:none;
    align-items:center;
    justify-content:center;
    padding:18px;
}

.rc-modal-backdrop.open{
    display:flex;
}

.rc-modal{
    width:100%;
    max-width:1080px;
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 20px 50px rgba(0,0,0,.25);
}

.rc-modal-head,
.rc-modal-foot{
    padding:16px 18px;
    background:#fafafa;
    border-bottom:1px solid var(--border);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
}

.rc-modal-foot{
    border-top:1px solid var(--border);
    border-bottom:0;
    justify-content:flex-end;
}

.rc-modal-title{
    font-size:16px;
    font-weight:900;
    margin:0;
}

.rc-modal-body{
    padding:18px;
    max-height:76vh;
    overflow:auto;
}

.rc-grid{
    display:grid;
    grid-template-columns:repeat(4,minmax(0,1fr));
    gap:15px;
}

.rc-grid-2{
    display:grid;
    grid-template-columns:280px 1fr;
    gap:18px;
    align-items:start;
}

.rc-field{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.rc-section-title{
    grid-column:1/-1;
    font-size:14px;
    font-weight:900;
    color:var(--primary);
    margin-top:8px;
    padding-top:10px;
    border-top:1px solid var(--border);
}

.rc-radio-row{
    display:flex;
    gap:14px;
    align-items:center;
    flex-wrap:wrap;
    min-height:42px;
}

.rc-radio-row label{
    display:flex;
    align-items:center;
    gap:7px;
    margin:0;
    font-weight:800;
}

.rc-preview{
    border:1px solid var(--border);
    border-radius:16px;
    background:#fafafa;
    padding:12px;
}

.rc-preview-img{
    width:100%;
    height:220px;
    border-radius:14px;
    object-fit:contain;
    background:#fff;
    border:1px dashed var(--border);
    display:none;
}

.rc-preview-img.show{
    display:block;
}

.rc-toast-wrap{
    position:fixed;
    right:20px;
    bottom:20px;
    z-index:3000;
    display:flex;
    flex-direction:column;
    gap:10px;
}

.rc-toast{
    background:#fff;
    border:1px solid var(--border);
    border-radius:14px;
    padding:12px 14px;
    box-shadow:0 12px 30px rgba(0,0,0,.15);
    min-width:280px;
}

.select2-container .select2-selection--single{
    height:42px !important;
    border:1px solid var(--border) !important;
    border-radius:10px !important;
    background:#f9fafb !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered{
    line-height:40px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow{
    height:40px !important;
}

.rc-option-img{
    float:right;
    width:74px;
    max-height:55px;
    object-fit:contain;
}

@media(max-width:1000px){
    .rc-toolbar,
    .rc-stats,
    .rc-summary{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

    .rc-grid,
    .rc-grid-2{
        grid-template-columns:1fr;
    }
}

@media(max-width:700px){
    .rc-wrap{
        margin-top:90px;
        padding:0 16px;
    }

    .rc-toolbar,
    .rc-stats,
    .rc-summary{
        grid-template-columns:1fr;
    }
}
</style>
@endsection

@php
$typeOptions = [
    ['value' => 'Vertikal profilierte Flachheizkörper', 'label' => 'Vertikal profilierte Flachheizkörper', 'image' => asset('images/radiators/Radiator/csm_vertically_profiled_flat_radiator_isometric_b4099d875e.png')],
    ['value' => 'Glattwandig profilierte Flachheizkörper', 'label' => 'Glattwandig profilierte Flachheizkörper', 'image' => asset('images/radiators/Radiator/csm_smoothly_profiled_flat_radiator_isometric_65fac7a2fd.png')],
    ['value' => 'Kompaktheizkörper', 'label' => 'Kompaktheizkörper', 'image' => asset('images/radiators/Radiator/Kompakt.png')],
    ['value' => 'Ventilheizkörper', 'label' => 'Ventilheizkörper', 'image' => asset('images/radiators/Radiator/Ventil.png')],
    ['value' => 'DIN Stahlrohr Radiator', 'label' => 'DIN Stahlrohr Radiator', 'image' => asset('images/radiators/Radiator/csm_steel_radiator_isometric_03e7ee2d16.png')],
    ['value' => 'DIN Stahl Radiator', 'label' => 'DIN Stahl Radiator', 'image' => asset('images/radiators/Radiator/csm_steel_pipe_radiator_isometric_ee41a0d27b.png')],
    ['value' => 'Gußradiator', 'label' => 'Gußradiator', 'image' => asset('images/radiators/Radiator/csm_cast_iron_radiator_isometric_791cc17f49.png')],
    ['value' => 'Handtuchheizkörper', 'label' => 'Handtuchheizkörper', 'image' => null],
];

$radiatorTypeOptions = [
    ['value' => '10', 'label' => '10', 'image' => asset('images/radiators/Radiator/10.png')],
    ['value' => '11', 'label' => '11', 'image' => asset('images/radiators/Radiator/11.png')],
    ['value' => '21', 'label' => '21', 'image' => asset('images/radiators/Radiator/21.png')],
    ['value' => '22', 'label' => '22', 'image' => asset('images/radiators/Radiator/22.png')],
    ['value' => '33', 'label' => '33', 'image' => asset('images/radiators/Radiator/33.png')],
];

$floors = ['Keller', 'Erdgeschoss', 'Obergeschoss', 'Dachgeschoss', 'Anbau', 'sonstiges'];
$rooms = ['Keller', 'Abstellraum', 'Bad', 'Kinderzimmer', 'Wohnzimmer', 'Esszimmer', 'Gästezimmer', 'Flur', 'Dach', 'sonstiges'];
$supplyValves = ['oben links', 'oben rechts', 'unten links', 'unten rechts'];
$returnValves = ['unten links Wand', 'unten links Rückwand', 'unten links Boden', 'unten rechts Wand', 'unten rechts Rückwand', 'unten rechts Boden', 'Boden unten links', 'Boden unten rechts'];
$designs = ['Eck', 'Durchgang', 'Winkeleck'];
@endphp

@section('content')
<div class="rc-wrap">

    <div class="rc-head">
        <div>
            <div class="rc-title">HEIZKÖRPERKONFIGURATION</div>
            <div class="rc-sub">Kunde suchen, Objekt wählen und Heizkörper direkt auf derselben Seite verwalten.</div>
        </div>

        <button type="button" class="rc-btn" id="openCreateModal" disabled>
            + Neuer Heizkörper
        </button>
    </div>

    <div class="rc-panel">
        <div class="rc-toolbar">
            <div>
                <label class="rc-label">Kunde suchen</label>
                <select id="customerSelect" class="rc-select" style="width:100%;"></select>
            </div>

            <div>
                <label class="rc-label">Objekt / Adresse</label>
                <select id="objectSelect" class="rc-select" style="width:100%;" disabled>
                    <option value="">Bitte zuerst Kunde wählen</option>
                </select>
            </div>

            <button type="button" class="rc-btn-soft" id="resetPageBtn">
                Zurücksetzen
            </button>
        </div>

        <div class="rc-summary" id="selectionSummary" style="display:none;">
            <div class="rc-summary-item">
                <div class="rc-label">Kunde</div>
                <div class="rc-summary-value" id="summaryCustomer">—</div>
            </div>

            <div class="rc-summary-item">
                <div class="rc-label">Telefon</div>
                <div class="rc-summary-value" id="summaryPhone">—</div>
            </div>

            <div class="rc-summary-item">
                <div class="rc-label">E-Mail</div>
                <div class="rc-summary-value" id="summaryEmail">—</div>
            </div>

            <div class="rc-summary-item">
                <div class="rc-label">Objekt</div>
                <div class="rc-summary-value" id="summaryObject">—</div>
            </div>
        </div>
    </div>

    <div class="rc-stats">
        <div class="rc-stat">
            <div class="rc-stat-label">Gesamt</div>
            <div class="rc-stat-value" id="statTotal">0</div>
        </div>

        <div class="rc-stat">
            <div class="rc-stat-label">Mit Bild</div>
            <div class="rc-stat-value" id="statImage">0</div>
        </div>

        <div class="rc-stat">
            <div class="rc-stat-label">Mit Steckdose</div>
            <div class="rc-stat-value" id="statSocket">0</div>
        </div>

        <div class="rc-stat">
            <div class="rc-stat-label">Thermostat erneuern</div>
            <div class="rc-stat-value" id="statThermostat">0</div>
        </div>
    </div>

    <div class="rc-card">
        <div class="rc-table-wrap">
            <table class="rc-table">
                <thead>
                    <tr>
                        <th style="width:95px;">Bild</th>
                        <th style="width:90px;">Nr.</th>
                        <th>Standort</th>
                        <th>Art / Typ</th>
                        <th>Größe</th>
                        <th>Anschlüsse</th>
                        <th>Status</th>
                        <th style="width:130px;text-align:right;">Aktion</th>
                    </tr>
                </thead>

                <tbody id="radiatorTableBody">
                    <tr>
                        <td colspan="8">
                            <div class="rc-empty">Bitte zuerst Kunde und Objekt auswählen.</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="rc-modal-backdrop" id="radiatorModal">
    <div class="rc-modal">
        <div class="rc-modal-head">
            <h3 class="rc-modal-title" id="modalTitle">Neuer Heizkörper</h3>
            <button type="button" class="rc-icon-btn" onclick="closeRadiatorModal()">×</button>
        </div>

        <form id="radiatorForm" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="id" id="field_id">
            <input type="hidden" name="customer_id" id="field_customer_id">
            <input type="hidden" name="alternative_id" id="field_alternative_id">
            <input type="hidden" name="postcode" id="field_postcode">
            <input type="hidden" name="address_no" id="field_address_no">

            <div class="rc-modal-body">
                <div class="rc-grid-2">
                    <div class="rc-preview">
                        <img src="" alt="Vorschau" class="rc-preview-img" id="imagePreview">

                        <div style="margin-top:12px;">
                            <label class="rc-label">Heizkörper Bild</label>
                            <input type="file" name="image" id="field_image" class="rc-input" accept="image/*">
                        </div>
                    </div>

                    <div class="rc-grid">
                        <div class="rc-section-title">Grunddaten</div>

                        <div class="rc-field">
                            <label class="rc-label">Art</label>
                            <select name="type" id="field_type" class="rc-select js-select-image">
                                <option value="">Wählen...</option>
                                @foreach($typeOptions as $option)
                                    <option value="{{ $option['value'] }}" data-image="{{ $option['image'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rc-field" id="radiatorTypeWrap">
                            <label class="rc-label">Typ</label>
                            <select name="radiator_type" id="field_radiator_type" class="rc-select js-select-image">
                                <option value="">Wählen...</option>
                                @foreach($radiatorTypeOptions as $option)
                                    <option value="{{ $option['value'] }}" data-image="{{ $option['image'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Nr.</label>
                            <input type="text" name="number" id="field_number" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Glieder</label>
                            <input type="text" name="limbs" id="field_limbs" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Etage</label>
                            <select name="floor" id="field_floor" class="rc-select">
                                <option value="">Wählen...</option>
                                @foreach($floors as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Raum</label>
                            <select name="room" id="field_room" class="rc-select">
                                <option value="">Wählen...</option>
                                @foreach($rooms as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Fläche qm</label>
                            <input type="number" step="0.01" name="room_size" id="field_room_size" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Fensterbank</label>
                            <div class="rc-radio-row">
                                <label><input type="radio" name="has_window_sill" value="1"> Ja</label>
                                <label><input type="radio" name="has_window_sill" value="0" checked> Nein</label>
                            </div>
                        </div>

                        <div class="rc-section-title">Größe</div>

                        <div class="rc-field">
                            <label class="rc-label">Breite mm</label>
                            <input type="number" name="width" id="field_width" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Höhe mm</label>
                            <input type="number" name="height" id="field_height" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Tiefe mm</label>
                            <input type="number" name="depth" id="field_depth" class="rc-input">
                        </div>

                        <div></div>

                        <div class="rc-section-title">Nische</div>

                        <div class="rc-field">
                            <label class="rc-label">Oben</label>
                            <input type="number" name="niche_top" id="field_niche_top" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Unten</label>
                            <input type="number" name="niche_bottom" id="field_niche_bottom" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Links</label>
                            <input type="number" name="niche_left" id="field_niche_left" class="rc-input">
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Rechts</label>
                            <input type="number" name="niche_right" id="field_niche_right" class="rc-input">
                        </div>

                        <div class="rc-section-title">Anschlüsse</div>

                        <div class="rc-field">
                            <label class="rc-label">Vorlaufventil</label>
                            <select name="supply_valve" id="field_supply_valve" class="rc-select">
                                <option value="">Wählen...</option>
                                @foreach($supplyValves as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Voreinstellbar</label>
                            <div class="rc-radio-row">
                                <label><input type="checkbox" name="supply_valve_presettable" value="1" id="field_supply_valve_presettable"> Ja</label>
                            </div>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Rücklaufventil</label>
                            <select name="return_valve" id="field_return_valve" class="rc-select">
                                <option value="">Wählen...</option>
                                @foreach($returnValves as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Rücklauf vorhanden</label>
                            <div class="rc-radio-row">
                                <label><input type="checkbox" name="return_valve_present" value="1" id="field_return_valve_present"> Ja</label>
                            </div>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Bauform</label>
                            <select name="design" id="field_design" class="rc-select">
                                <option value="">Wählen...</option>
                                @foreach($designs as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Thermostatkopf erneuern</label>
                            <div class="rc-radio-row">
                                <label><input type="radio" name="renew_thermostat_head" value="1"> Ja</label>
                                <label><input type="radio" name="renew_thermostat_head" value="0" checked> Nein</label>
                            </div>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Steckdose vorhanden</label>
                            <div class="rc-radio-row">
                                <label><input type="radio" name="has_socket" value="1"> Ja</label>
                                <label><input type="radio" name="has_socket" value="0" checked> Nein</label>
                            </div>
                        </div>

                        <div class="rc-field">
                            <label class="rc-label">Entfernung Steckdose m</label>
                            <input type="number" step="0.01" name="socket_distance" id="field_socket_distance" class="rc-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="rc-modal-foot">
                <button type="button" class="rc-btn-soft" onclick="closeRadiatorModal()">Abbrechen</button>
                <button type="submit" class="rc-btn" id="saveBtn">Speichern</button>
            </div>
        </form>
    </div>
</div>

<div class="rc-toast-wrap" id="toast-wrap"></div>
@endsection

@section('script')
<script src="{{ asset('js/select2.min.js') }}"></script>

<script>
const ROUTES = {
    customers: @json(route('radiator.config.customers')),
    objectsBase: @json(url('/radiator_config_objects')),
    list: @json(route('radiator.config.list')),
    store: @json(route('radiator.config.store')),
    update: @json(route('radiator.config.update')),
};

const csrfToken = @json(csrf_token());

let selectedCustomer = null;
let selectedObject = null;
const initialParams = new URLSearchParams(window.location.search);
const initialCustomerId = initialParams.get('customer_id');
const initialAlternativeId = initialParams.get('alternative_id');
let initialAutoSelectDone = false;
const form = document.getElementById('radiatorForm');
const modal = document.getElementById('radiatorModal');
const modalTitle = document.getElementById('modalTitle');
const imagePreview = document.getElementById('imagePreview');
const openCreateModalBtn = document.getElementById('openCreateModal');
const tableBody = document.getElementById('radiatorTableBody');

function e(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function toast(type, message) {
    const wrap = document.getElementById('toast-wrap');
    if (!wrap) return;

    const el = document.createElement('div');
    el.className = 'rc-toast';
    el.innerHTML = `
        <strong>${type === 'ok' ? 'Erfolgreich' : 'Hinweis'}</strong>
        <div style="font-size:13px;color:#374151;margin-top:4px;white-space:pre-line;">${e(message)}</div>
    `;

    wrap.appendChild(el);

    setTimeout(() => {
        try { el.remove(); } catch(err) {}
    }, 3500);
}

function openRadiatorModal() {
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeRadiatorModal() {
    modal.classList.remove('open');
    document.body.style.overflow = '';
    $('.js-select-image').select2('close');
}

function formatOption(state) {
    if (!state.id) return state.text;

    const image = $(state.element).data('image');

    if (!image) {
        return $('<span>' + state.text + '</span>');
    }

    return $('<span>' + state.text + '</span><img src="' + image + '" class="rc-option-img">');
}

function resetStats() {
    document.getElementById('statTotal').innerText = '0';
    document.getElementById('statImage').innerText = '0';
    document.getElementById('statSocket').innerText = '0';
    document.getElementById('statThermostat').innerText = '0';
}

function setStats(stats) {
    document.getElementById('statTotal').innerText = stats?.total ?? 0;
    document.getElementById('statImage').innerText = stats?.with_image ?? 0;
    document.getElementById('statSocket').innerText = stats?.with_socket ?? 0;
    document.getElementById('statThermostat').innerText = stats?.needs_thermostat ?? 0;
}

function emptyTable(message) {
    tableBody.innerHTML = `
        <tr>
            <td colspan="8">
                <div class="rc-empty">${e(message)}</div>
            </td>
        </tr>
    `;
}

function updateSummary() {
    const box = document.getElementById('selectionSummary');

    if (!selectedCustomer) {
        box.style.display = 'none';
        return;
    }

    box.style.display = '';

    document.getElementById('summaryCustomer').innerText = selectedCustomer.name || '—';
    document.getElementById('summaryPhone').innerText = selectedCustomer.phone || '—';
    document.getElementById('summaryEmail').innerText = selectedCustomer.email || '—';
    document.getElementById('summaryObject').innerText = selectedObject?.text || '—';
}

function canCreate() {
    return !!selectedCustomer && selectedObject !== null;
}

function updateCreateButton() {
    openCreateModalBtn.disabled = !canCreate();
}

function resetPage() {
    selectedCustomer = null;
    selectedObject = null;

    $('#customerSelect').val(null).trigger('change');
    $('#objectSelect').empty()
        .append(new Option('Bitte zuerst Kunde wählen', '', true, true))
        .prop('disabled', true)
        .trigger('change');

    openCreateModalBtn.disabled = true;

    resetStats();
    updateSummary();
    emptyTable('Bitte zuerst Kunde und Objekt auswählen.');
}
function loadObjects(customerId) {
    selectedObject = null;
    updateCreateButton();
    updateSummary();
    resetStats();
    emptyTable('Objekte werden geladen...');

    $('#objectSelect')
        .empty()
        .append(new Option('Objekte werden geladen...', '', true, true))
        .prop('disabled', true)
        .trigger('change');

    fetch(`${ROUTES.objectsBase}/${customerId}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        selectedCustomer = data.customer || selectedCustomer;

        const objects = data.objects || [];

        $('#objectSelect').empty();

        if (!objects.length) {
            $('#objectSelect')
                .append(new Option('Kein Objekt gefunden', '', true, true))
                .prop('disabled', true)
                .trigger('change');

            emptyTable('Kein Objekt für diesen Kunden gefunden.');
            updateSummary();
            return;
        }

        if (objects.length > 1) {
            $('#objectSelect').append(new Option('Bitte Objekt wählen', '', true, true));
        }

        objects.forEach(obj => {
            const optionValue = obj.id ?? '__main__';
            const option = new Option(obj.text, optionValue, false, false);
            option.dataset.object = JSON.stringify(obj);
            $('#objectSelect').append(option);
        });

        $('#objectSelect').prop('disabled', false);

        let valueToSelect = null;

        if (initialAlternativeId && !initialAutoSelectDone) {
            const foundObject = objects.find(obj => String(obj.id) === String(initialAlternativeId));

            if (foundObject) {
                valueToSelect = foundObject.id ?? '__main__';
            }
        }

        if (!valueToSelect && data.auto_select && objects.length === 1) {
            valueToSelect = objects[0].id ?? '__main__';
        }

        if (valueToSelect) {
            $('#objectSelect').val(valueToSelect).trigger('change');
        } else {
            $('#objectSelect').trigger('change');
            emptyTable('Bitte Objekt auswählen.');
        }

        initialAutoSelectDone = true;
        updateSummary();
    })
    .catch(() => {
        toast('bad', 'Objekte konnten nicht geladen werden.');
        emptyTable('Objekte konnten nicht geladen werden.');
    });
}

function loadRadiators() {
    if (!selectedCustomer || selectedObject === null) {
        updateCreateButton();
        return;
    }

    updateCreateButton();
    updateSummary();

    emptyTable('Heizkörper werden geladen...');

    const params = new URLSearchParams();
    params.set('customer_id', selectedCustomer.id);

    if (selectedObject.id) {
        params.set('alternative_id', selectedObject.id);
    }

    fetch(`${ROUTES.list}?${params.toString()}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        setStats(data.stats || {});
        renderRadiators(data.data || []);
    })
    .catch(() => {
        toast('bad', 'Heizkörper konnten nicht geladen werden.');
        emptyTable('Heizkörper konnten nicht geladen werden.');
    });
}

function renderRadiators(rows) {
    if (!rows.length) {
        emptyTable('Noch keine Heizkörper vorhanden. Klicke oben auf “Neuer Heizkörper”.');
        return;
    }

    tableBody.innerHTML = rows.map(red => {
        const img = red.image_url
            ? `<img src="${e(red.image_url)}" alt="Heizkörper">`
            : 'HK';

        const badges = [
            red.has_window_sill ? '<span class="rc-badge">Fensterbank</span>' : '',
            red.has_socket ? `<span class="rc-badge">Steckdose ${red.socket_distance ? e(red.socket_distance) + ' m' : ''}</span>` : '',
            red.renew_thermostat_head ? '<span class="rc-badge">Thermostat erneuern</span>' : '',
        ].join('');

        return `
            <tr id="radiator-row-${e(red.id)}">
                <td>
                    <div class="rc-avatar">${img}</div>
                </td>

                <td>
                    <div class="rc-name">${e(red.number || '—')}</div>
                    <div class="rc-muted">ID: ${e(red.id)}</div>
                </td>

                <td>
                    <div class="rc-name">${e(red.floor || '—')} · ${e(red.room || '—')}</div>
                    <div class="rc-muted">${red.room_size ? e(red.room_size) + ' qm' : 'Keine Fläche'}</div>
                </td>

                <td>
                    <div class="rc-name">${e(red.type || '—')}</div>
                    <div class="rc-muted">Typ: ${e(red.radiator_type || '—')} ${red.limbs ? '· Glieder: ' + e(red.limbs) : ''}</div>
                </td>

                <td>
                    <div class="rc-name">B ${e(red.width || '—')} × H ${e(red.height || '—')} × T ${e(red.depth || '—')} mm</div>
                    <div class="rc-muted">
                        Nische: O ${e(red.niche_top || '—')},
                        U ${e(red.niche_bottom || '—')},
                        L ${e(red.niche_left || '—')},
                        R ${e(red.niche_right || '—')}
                    </div>
                </td>

                <td>
                    <div class="rc-name">VL: ${e(red.supply_valve || '—')}</div>
                    <div class="rc-muted">RL: ${e(red.return_valve || '—')} · ${e(red.design || '—')}</div>
                </td>

                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">${badges || '<span class="rc-muted">—</span>'}</div>
                </td>

                <td>
                    <div class="rc-actions">
                        <button type="button" class="rc-icon-btn edit js-edit-radiator" data-radiator='${e(JSON.stringify(red))}'>✎</button>
                        <button type="button" class="rc-icon-btn danger js-delete-radiator" data-id="${e(red.id)}">×</button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

function setRadio(name, value) {
    document.querySelectorAll(`input[name="${name}"]`).forEach(input => {
        input.checked = String(input.value) === String(value ? 1 : 0);
    });
}

function resetForm() {
    form.reset();

    document.getElementById('field_id').value = '';
    document.getElementById('field_customer_id').value = selectedCustomer?.id || '';
    document.getElementById('field_alternative_id').value = selectedObject?.id || '';
    document.getElementById('field_postcode').value = selectedObject?.postcode || selectedCustomer?.postcode || '';
    document.getElementById('field_address_no').value = selectedObject?.address_no || '';

    imagePreview.src = '';
    imagePreview.classList.remove('show');

    $('#field_type').val('').trigger('change');
    $('#field_radiator_type').val('').trigger('change');

    setRadio('has_window_sill', 0);
    setRadio('renew_thermostat_head', 0);
    setRadio('has_socket', 0);

    document.getElementById('field_supply_valve_presettable').checked = false;
    document.getElementById('field_return_valve_present').checked = false;

    toggleRadiatorType();
}

function fillForm(data) {
    resetForm();

    document.getElementById('field_id').value = data.id || '';
    document.getElementById('field_customer_id').value = data.customer_id || selectedCustomer?.id || '';
    document.getElementById('field_alternative_id').value = data.alternative_id || selectedObject?.id || '';
    document.getElementById('field_postcode').value = data.postcode || selectedObject?.postcode || '';
    document.getElementById('field_address_no').value = data.address_no || selectedObject?.address_no || '';

    const fields = [
        'number',
        'floor',
        'room',
        'room_size',
        'width',
        'height',
        'depth',
        'niche_top',
        'niche_bottom',
        'niche_left',
        'niche_right',
        'supply_valve',
        'return_valve',
        'design',
        'socket_distance',
        'limbs'
    ];

    fields.forEach(field => {
        const el = document.getElementById('field_' + field);
        if (el) el.value = data[field] ?? '';
    });

    $('#field_type').val(data.type || '').trigger('change');
    $('#field_radiator_type').val(data.radiator_type || '').trigger('change');

    document.getElementById('field_supply_valve_presettable').checked = !!data.supply_valve_presettable;
    document.getElementById('field_return_valve_present').checked = !!data.return_valve_present;

    setRadio('has_window_sill', data.has_window_sill ? 1 : 0);
    setRadio('renew_thermostat_head', data.renew_thermostat_head ? 1 : 0);
    setRadio('has_socket', data.has_socket ? 1 : 0);

    if (data.image_url) {
        imagePreview.src = data.image_url;
        imagePreview.classList.add('show');
    }

    toggleRadiatorType();
}

function toggleRadiatorType() {
    const type = document.getElementById('field_type').value;
    const wrap = document.getElementById('radiatorTypeWrap');
    const showLimbsTypes = ['Gußradiator', 'DIN Stahl Radiator', 'DIN Stahlrohr Radiator'];

    if (showLimbsTypes.includes(type)) {
        wrap.style.display = 'none';
        $('#field_radiator_type').val('').trigger('change');
    } else {
        wrap.style.display = '';
    }
}

function deleteRadiator(id) {
    if (!confirm('Möchten Sie diesen Heizkörper wirklich löschen?')) {
        return;
    }

    fetch(`{{ url('/radiator_config_delete') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok || data.ok === false) {
            throw new Error(data.message || 'Löschen fehlgeschlagen.');
        }

        toast('ok', data.message || 'Gelöscht.');
        loadRadiators();
    })
    .catch(error => {
        toast('bad', error.message || 'Löschen nicht möglich.');
    });
}

function bootFromUrlParams() {
    if (!initialCustomerId) {
        return;
    }

    emptyTable('Kunde wird geladen...');

    fetch(`${ROUTES.customers}?id=${encodeURIComponent(initialCustomerId)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const customerOption = data.results?.[0];

        if (!customerOption) {
            toast('bad', 'Kunde aus der URL wurde nicht gefunden.');
            emptyTable('Kunde wurde nicht gefunden.');
            return;
        }

        selectedCustomer = customerOption.customer || {
            id: customerOption.id,
            name: customerOption.text,
        };

        const option = new Option(customerOption.text, customerOption.id, true, true);
        option.dataset.customer = JSON.stringify(selectedCustomer);

        $('#customerSelect')
            .append(option)
            .trigger('change');

        loadObjects(customerOption.id);
    })
    .catch(() => {
        toast('bad', 'Kunde aus der URL konnte nicht geladen werden.');
        emptyTable('Kunde konnte nicht geladen werden.');
    });
}

document.addEventListener('DOMContentLoaded', function () {
    $('#customerSelect').select2({
        placeholder: 'Kunde suchen nach Name, Firma, PLZ, Stadt, Telefon, E-Mail...',
        width: '100%',
        allowClear: true,
        ajax: {
            url: ROUTES.customers,
            dataType: 'json',
            delay: 250,
            data: params => ({
                q: params.term || ''
            }),
            processResults: data => data
        }
    });

    $('#objectSelect').select2({
        placeholder: 'Objekt wählen',
        width: '100%',
        allowClear: true
    });

    $('.js-select-image').select2({
        templateResult: formatOption,
        templateSelection: formatOption,
        width: '100%',
        dropdownParent: $('#radiatorModal'),
        minimumResultsForSearch: Infinity
    });

    $('#customerSelect').on('select2:select', function (event) {
        selectedCustomer = event.params.data.customer || {
            id: event.params.data.id,
            name: event.params.data.text
        };

        loadObjects(selectedCustomer.id);
    });

    $('#customerSelect').on('select2:clear', function () {
        resetPage();
    });

    $('#objectSelect').on('change', function () {
        const option = this.options[this.selectedIndex];

        if (!option || !option.dataset.object) {
            selectedObject = null;
            updateCreateButton();
            updateSummary();
            emptyTable('Bitte Objekt auswählen.');
            return;
        }

        selectedObject = JSON.parse(option.dataset.object);

        updateCreateButton();
        updateSummary();
        loadRadiators();
    });

    document.getElementById('resetPageBtn').addEventListener('click', resetPage);

    openCreateModalBtn.addEventListener('click', function () {
        if (!canCreate()) {
            toast('bad', 'Bitte zuerst Kunde und Objekt auswählen.');
            return;
        }

        resetForm();
        modalTitle.innerText = 'Neuer Heizkörper';
        form.action = ROUTES.store;
        openRadiatorModal();
    });

    document.getElementById('field_type').addEventListener('change', toggleRadiatorType);

    document.getElementById('field_image').addEventListener('change', function () {
        const file = this.files && this.files[0];

        if (!file) return;

        const reader = new FileReader();

        reader.onload = function (event) {
            imagePreview.src = event.target.result;
            imagePreview.classList.add('show');
        };

        reader.readAsDataURL(file);
    });

    resetPage();
    bootFromUrlParams();
});

document.addEventListener('click', function (event) {
    if (event.target.classList.contains('rc-modal-backdrop')) {
        closeRadiatorModal();
        return;
    }

    const editBtn = event.target.closest('.js-edit-radiator');

    if (editBtn) {
        const data = JSON.parse(editBtn.dataset.radiator || '{}');

        fillForm(data);

        modalTitle.innerText = 'Heizkörper bearbeiten';
        form.action = ROUTES.update;
        openRadiatorModal();

        return;
    }

    const deleteBtn = event.target.closest('.js-delete-radiator');

    if (deleteBtn) {
        deleteRadiator(deleteBtn.dataset.id);
        return;
    }
});

form.addEventListener('submit', function (event) {
    event.preventDefault();

    const submitBtn = document.getElementById('saveBtn');
    const formData = new FormData(form);

    submitBtn.disabled = true;
    submitBtn.innerText = 'Speichern...';

    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));

        if (!response.ok || data.ok === false) {
            let message = data.message || 'Speichern fehlgeschlagen.';

            if (data.errors) {
                message = Object.values(data.errors).flat().join('\n');
            }

            throw new Error(message);
        }

        toast('ok', data.message || 'Gespeichert.');
        closeRadiatorModal();
        loadRadiators();
    })
    .catch(error => {
        toast('bad', error.message || 'Fehler beim Speichern.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerText = 'Speichern';
    });
});

document.addEventListener('keydown', function(event) {
    if (event.key !== 'Escape') return;

    closeRadiatorModal();
});
</script>
@endsection


@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'HEIZKÖRPERKONFIGURATION',
                url: "{{ url()->current() }}",
                clickable: false

            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush