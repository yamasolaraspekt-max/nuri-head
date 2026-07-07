@extends('admin.layouts.app')

@section('title', 'Grundriss zeichnen')

{{--
    Interaktiver Grundriss-Editor (Zeichnen → Heizlast), Strang Energie.
    jQuery + inline SVG. KEIN Alpine, kein x-data (CLAUDE.md). Nutzt das global geladene jQuery.
    Daten (Polygon/Wände/Öffnungen) werden im Contract der RaumGeometrie (alle Koordinaten in mm)
    an den GrundrissController (vorschau/speichern) gesendet; gerechnet wird serverseitig.
--}}

@php
    $konstruktionenJs = collect($konstruktionen)->map(fn ($name, $id) => ['id' => (int) $id, 'name' => $name])->values();
@endphp

@section('content')
<section class="energie-grundriss-editor">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="content-header-title mb-0">Grundriss zeichnen</h2>
                <p class="text-muted mb-0">
                    Klicke Ecken in die Zeichenfläche (Snap auf 500&nbsp;mm-Raster). Doppelklick oder
                    „Polygon schließen" verbindet die Wände. Klick auf eine Wand öffnet ihre Eigenschaften.
                </p>
            </div>
            <a href="{{ route('energie.grundriss') }}" class="btn btn-outline-secondary">
                <i class="feather icon-list"></i> Zur Liste
            </a>
        </div>
    </div>

    <div id="grundriss-hinweis" class="alert alert-warning d-none"></div>

    <div class="row">

        {{-- ============ WERKZEUGE / STAMMDATEN (links) ============ --}}
        <div class="col-lg-3 col-md-4">
            <div class="card">
                <div class="card-header py-1"><h5 class="card-title mb-0">Raum</h5></div>
                <div class="card-body">
                    <div class="mb-1">
                        <label class="form-label">Name</label>
                        <input type="text" id="f-name" class="form-control form-control-sm" value="{{ $projektName ?? '' }}" placeholder="z. B. Wohnzimmer">
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Nutzung</label>
                        <select id="f-nutzung" class="form-select form-select-sm">
                            <option value="wohnen">Wohnen</option>
                            <option value="schlafen">Schlafen</option>
                            <option value="bad">Bad</option>
                            <option value="kueche">Küche</option>
                            <option value="flur">Flur</option>
                            <option value="buero">Büro</option>
                        </select>
                    </div>
                    <div class="row g-1 mb-1">
                        <div class="col-6">
                            <label class="form-label">Höhe (mm)</label>
                            <input type="number" id="f-hoehe" class="form-control form-control-sm" value="2500" min="1000" max="20000" step="10">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Geschoss</label>
                            <input type="number" id="f-geschoss" class="form-control form-control-sm" value="0" step="1">
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header py-1"><h5 class="card-title mb-0">Decke &amp; Boden</h5></div>
                <div class="card-body">
                    <div class="mb-1">
                        <label class="form-label">Decke – Grenzfläche</label>
                        <select id="f-decke-grenz" class="form-select form-select-sm">
                            <option value="">— keine Decke —</option>
                            <option value="aussen">aussen</option>
                            <option value="unbeheizt">unbeheizt</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Decke – U-Wert (W/m²K)</label>
                        <input type="text" id="f-decke-u" class="form-control form-control-sm" placeholder="z. B. 0,24" inputmode="decimal">
                    </div>
                    <hr class="my-1">
                    <div class="mb-1">
                        <label class="form-label">Boden – Grenzfläche</label>
                        <select id="f-boden-grenz" class="form-select form-select-sm">
                            <option value="">— kein Boden —</option>
                            <option value="erdreich">erdreich</option>
                            <option value="unbeheizt">unbeheizt</option>
                        </select>
                    </div>
                    <div class="mb-1">
                        <label class="form-label">Boden – U-Wert (W/m²K)</label>
                        <input type="text" id="f-boden-u" class="form-control form-control-sm" placeholder="z. B. 0,35" inputmode="decimal">
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body d-grid gap-1">
                    <button type="button" id="btn-schliessen" class="btn btn-sm btn-outline-primary">
                        <i class="feather icon-check-circle"></i> Polygon schließen
                    </button>
                    <button type="button" id="btn-undo" class="btn btn-sm btn-outline-secondary">
                        <i class="feather icon-corner-up-left"></i> Letzte Ecke zurück
                    </button>
                    <button type="button" id="btn-reset" class="btn btn-sm btn-outline-danger">
                        <i class="feather icon-trash-2"></i> Zeichnung zurücksetzen
                    </button>
                </div>
            </div>
        </div>

        {{-- ============ ZEICHENFLÄCHE (mitte) ============ --}}
        <div class="col-lg-6 col-md-8">
            <div class="card">
                <div class="card-header py-1 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Zeichenfläche</h5>
                    <small class="text-muted">Raster = 500&nbsp;mm · <span id="scale-label"></span></small>
                </div>
                <div class="card-body p-0">
                    <div style="overflow:auto; max-height:640px; background:#fafafa;">
                        <svg id="grundriss-svg" width="900" height="600" style="display:block; cursor:crosshair; touch-action:none;">
                            <g id="layer-grid"></g>
                            <g id="layer-fill"></g>
                            <g id="layer-openings"></g>
                            <g id="layer-walls"></g>
                            <g id="layer-dims"></g>
                            <g id="layer-nodes"></g>
                        </svg>
                    </div>
                </div>
                <div class="card-footer py-1 d-flex justify-content-between align-items-center flex-wrap">
                    <small class="text-muted">
                        Grundfläche: <strong id="lbl-flaeche">0,00</strong> m² ·
                        Ecken: <strong id="lbl-ecken">0</strong> ·
                        Wände: <strong id="lbl-waende">0</strong>
                    </small>
                    <div class="btn-group">
                        <button type="button" id="btn-vorschau" class="btn btn-sm btn-primary">
                            <i class="feather icon-activity"></i> Vorschau (Heizlast)
                        </button>
                        <button type="button" id="btn-speichern" class="btn btn-sm btn-success">
                            <i class="feather icon-save"></i> Speichern
                        </button>
                    </div>
                </div>
            </div>

            {{-- Ergebnis-Box --}}
            <div id="ergebnis-box" class="card d-none">
                <div class="card-header py-1"><h5 class="card-title mb-0">Vorschau-Ergebnis</h5></div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col">
                            <div class="text-muted small">Grundfläche</div>
                            <div class="h4 mb-0"><span id="res-flaeche">—</span> m²</div>
                        </div>
                        <div class="col">
                            <div class="text-muted small">Bauteile</div>
                            <div class="h4 mb-0"><span id="res-bauteile">—</span></div>
                        </div>
                        <div class="col">
                            <div class="text-muted small">Auslegungs-Heizlast</div>
                            <div class="h4 mb-0 text-primary"><span id="res-heizlast">—</span> kW</div>
                        </div>
                    </div>
                    <div class="row text-center mt-1">
                        <div class="col">
                            <div class="text-muted small">H<sub>T</sub> (Transmission)</div>
                            <div class="h6 mb-0"><span id="res-ht">—</span> W/K</div>
                        </div>
                        <div class="col">
                            <div class="text-muted small">H<sub>V</sub> (Lüftung)</div>
                            <div class="h6 mb-0"><span id="res-hv">—</span> W/K</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============ WAND-EIGENSCHAFTEN (rechts) ============ --}}
        <div class="col-lg-3">
            <div class="card">
                <div class="card-header py-1"><h5 class="card-title mb-0">Wand-Eigenschaften</h5></div>
                <div class="card-body">
                    <div id="wall-empty" class="text-muted small">
                        Klicke im Grundriss auf eine Wand, um sie zu bearbeiten.
                    </div>
                    <div id="wall-panel" class="d-none">
                        <div class="mb-1">
                            <span class="badge bg-secondary">Wand <span id="wall-idx">—</span></span>
                            <span class="text-muted small">Länge: <span id="wall-len">—</span> mm</span>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Grenzfläche</label>
                            <select id="wall-grenz" class="form-select form-select-sm">
                                @foreach ($grenzflaechen as $g)
                                    <option value="{{ $g }}">{{ $g }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">U-Wert (W/m²K)</label>
                            <input type="text" id="wall-u" class="form-control form-control-sm" placeholder="z. B. 1,4" inputmode="decimal">
                            <small class="text-muted">leer lassen, wenn Konstruktion gewählt</small>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Konstruktion</label>
                            <select id="wall-konstruktion" class="form-select form-select-sm">
                                <option value="">— keine —</option>
                                @foreach ($konstruktionen as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-1">
                            <label class="form-label">Azimut (Grad, optional)</label>
                            <input type="text" id="wall-azimut" class="form-control form-control-sm" placeholder="0–360" inputmode="decimal">
                        </div>

                        <hr class="my-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label mb-0">Öffnungen</label>
                            <button type="button" id="btn-add-oeffnung" class="btn btn-xs btn-outline-primary">+ Fenster/Tür</button>
                        </div>
                        <div id="oeffnungen-liste"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection

@push('scripts')
<script>
(function ($) {
    'use strict';

    // ---- Konfiguration / Maßstab -------------------------------------------------
    var PPM = 0.05;          // Pixel pro mm  (1000 mm = 50 px)
    var GRID_MM = 500;       // Rasterweite in mm
    var SNAP_MM = 500;       // Snap-Weite in mm
    var CANVAS_W = 900, CANVAS_H = 600;
    var KONSTRUKTIONEN = @json($konstruktionenJs);

    var svg = document.getElementById('grundriss-svg');
    var SVGNS = 'http://www.w3.org/2000/svg';

    // ---- Zustand -----------------------------------------------------------------
    var state = {
        polygon: [],      // [{x,y}] in mm
        segments: [],     // Wandsegmente (Contract)
        closed: false,
        selected: null    // Index der ausgewählten Wand
    };

    // ---- Vorbelegung aus bestehender Geometrie -----------------------------------
    var initial = @json($geometrie);
    var projektId = @json($projektId);
    var raumId = @json($raumId);

    // ---- Helfer ------------------------------------------------------------------
    function mmToPx(v) { return v * PPM; }
    function deNum(v) {
        if (v === null || v === undefined) return null;
        v = String(v).trim().replace(',', '.');
        if (v === '') return null;
        var n = parseFloat(v);
        return isNaN(n) ? null : n;
    }
    function fmt(v, d) {
        d = d === undefined ? 2 : d;
        if (v === null || v === undefined || isNaN(v)) return '—';
        return Number(v).toLocaleString('de-DE', { minimumFractionDigits: d, maximumFractionDigits: d });
    }
    function el(tag, attrs) {
        var e = document.createElementNS(SVGNS, tag);
        for (var k in attrs) { e.setAttribute(k, attrs[k]); }
        return e;
    }
    function snap(v) { return Math.round(v / SNAP_MM) * SNAP_MM; }

    function eventToMm(evt) {
        var rect = svg.getBoundingClientRect();
        var sx = CANVAS_W / rect.width;
        var sy = CANVAS_H / rect.height;
        var pxX = (evt.clientX - rect.left) * sx;
        var pxY = (evt.clientY - rect.top) * sy;
        return { x: snap(pxX / PPM), y: snap(pxY / PPM) };
    }

    function segLenMm(seg) {
        var dx = seg.bis.x - seg.von.x, dy = seg.bis.y - seg.von.y;
        return Math.sqrt(dx * dx + dy * dy);
    }

    function shoelaceM2(poly) {
        var n = poly.length;
        if (n < 3) return 0;
        var s = 0;
        for (var i = 0; i < n; i++) {
            var j = (i + 1) % n;
            s += poly[i].x * poly[j].y - poly[j].x * poly[i].y;
        }
        return Math.abs(s) / 2 / 1000000;
    }

    function hinweis(msg) {
        var $h = $('#grundriss-hinweis');
        if (!msg) { $h.addClass('d-none').text(''); return; }
        $h.removeClass('d-none').text(msg);
    }

    // ---- Segmente aus Polygon ableiten (Eigenschaften erhalten) ------------------
    function rebuildSegments() {
        var n = state.polygon.length;
        var neu = [];
        for (var i = 0; i < n; i++) {
            var von = state.polygon[i];
            var bis = state.polygon[(i + 1) % n];
            var alt = state.segments[i] || {};
            neu.push({
                von: { x: von.x, y: von.y },
                bis: { x: bis.x, y: bis.y },
                grenzflaeche: alt.grenzflaeche || 'aussen',
                bauteil_typ: 'wand',
                azimut_grad: (alt.azimut_grad === undefined ? null : alt.azimut_grad),
                u_wert: (alt.u_wert === undefined ? null : alt.u_wert),
                konstruktion_id: (alt.konstruktion_id === undefined ? null : alt.konstruktion_id),
                oeffnungen: alt.oeffnungen || []
            });
        }
        state.segments = neu;
    }

    // ---- Rendering ---------------------------------------------------------------
    function render() {
        ['layer-grid', 'layer-fill', 'layer-openings', 'layer-walls', 'layer-dims', 'layer-nodes'].forEach(function (id) {
            var g = document.getElementById(id);
            while (g.firstChild) g.removeChild(g.firstChild);
        });

        // Raster
        var grid = document.getElementById('layer-grid');
        var stepPx = mmToPx(GRID_MM);
        for (var x = 0; x <= CANVAS_W; x += stepPx) {
            grid.appendChild(el('line', { x1: x, y1: 0, x2: x, y2: CANVAS_H, stroke: '#e6e6e6', 'stroke-width': 1 }));
        }
        for (var y = 0; y <= CANVAS_H; y += stepPx) {
            grid.appendChild(el('line', { x1: 0, y1: y, x2: CANVAS_W, y2: y, stroke: '#e6e6e6', 'stroke-width': 1 }));
        }

        // Füllung
        if (state.polygon.length >= 3) {
            var pts = state.polygon.map(function (p) { return mmToPx(p.x) + ',' + mmToPx(p.y); }).join(' ');
            document.getElementById('layer-fill').appendChild(el('polygon', {
                points: pts, fill: state.closed ? 'rgba(115,103,240,0.10)' : 'rgba(115,103,240,0.05)',
                stroke: 'none'
            }));
        }

        // Öffnungen + Wände + Bemaßung
        if (state.closed) {
            var openLayer = document.getElementById('layer-openings');
            var wallLayer = document.getElementById('layer-walls');
            var dimLayer = document.getElementById('layer-dims');

            state.segments.forEach(function (seg, i) {
                var x1 = mmToPx(seg.von.x), y1 = mmToPx(seg.von.y);
                var x2 = mmToPx(seg.bis.x), y2 = mmToPx(seg.bis.y);

                // Öffnungen als Marker entlang der Wand
                var len = segLenMm(seg);
                var ux = len ? (seg.bis.x - seg.von.x) / len : 0;
                var uy = len ? (seg.bis.y - seg.von.y) / len : 0;
                var off = len / 2;
                (seg.oeffnungen || []).forEach(function (o, oi) {
                    var w = o.breite_mm || 800;
                    var start = off - (seg.oeffnungen.length - 1) * 300 + oi * 600 - w / 2;
                    var ax = seg.von.x + ux * (start);
                    var ay = seg.von.y + uy * (start);
                    var bx = seg.von.x + ux * (start + w);
                    var by = seg.von.y + uy * (start + w);
                    openLayer.appendChild(el('line', {
                        x1: mmToPx(ax), y1: mmToPx(ay), x2: mmToPx(bx), y2: mmToPx(by),
                        stroke: (o.typ === 'tuer' ? '#ff9f43' : '#00cfe8'), 'stroke-width': 6, 'stroke-linecap': 'butt'
                    }));
                });

                // unsichtbare breite Trefferlinie
                var hit = el('line', { x1: x1, y1: y1, x2: x2, y2: y2, stroke: 'transparent', 'stroke-width': 14, style: 'cursor:pointer' });
                hit.setAttribute('data-wall', i);
                hit.classList.add('wall-hit');
                wallLayer.appendChild(hit);

                // sichtbare Wand
                var col = (i === state.selected) ? '#ea5455' : '#5e5873';
                var line = el('line', { x1: x1, y1: y1, x2: x2, y2: y2, stroke: col, 'stroke-width': (i === state.selected ? 5 : 3), style: 'cursor:pointer' });
                line.setAttribute('data-wall', i);
                line.classList.add('wall-hit');
                wallLayer.appendChild(line);

                // Bemaßung (Länge mm) an der Kanten-Mitte
                var mx = (x1 + x2) / 2, my = (y1 + y2) / 2;
                var t = el('text', { x: mx, y: my - 4, 'text-anchor': 'middle', 'font-size': 11, fill: '#6e6b7b', style: 'pointer-events:none;user-select:none' });
                t.textContent = fmt(len, 0);
                dimLayer.appendChild(t);
            });
        } else {
            // Beim Zeichnen: Kantenlinien der offenen Kette
            var wallLayer2 = document.getElementById('layer-walls');
            for (var k = 0; k + 1 < state.polygon.length; k++) {
                var a = state.polygon[k], b = state.polygon[k + 1];
                wallLayer2.appendChild(el('line', {
                    x1: mmToPx(a.x), y1: mmToPx(a.y), x2: mmToPx(b.x), y2: mmToPx(b.y),
                    stroke: '#7367f0', 'stroke-width': 2, 'stroke-dasharray': '4 3'
                }));
            }
        }

        // Ecken
        var nodeLayer = document.getElementById('layer-nodes');
        state.polygon.forEach(function (p, i) {
            var c = el('circle', { cx: mmToPx(p.x), cy: mmToPx(p.y), r: 4, fill: '#7367f0', stroke: '#fff', 'stroke-width': 1.5 });
            nodeLayer.appendChild(c);
        });

        updateStats();
    }

    function updateStats() {
        $('#lbl-flaeche').text(fmt(shoelaceM2(state.polygon)));
        $('#lbl-ecken').text(state.polygon.length);
        $('#lbl-waende').text(state.closed ? state.segments.length : 0);
        $('#scale-label').text('1 px = ' + Math.round(1 / PPM) + ' mm');
    }

    // ---- Wand-Panel --------------------------------------------------------------
    function selectWall(i) {
        state.selected = i;
        var seg = state.segments[i];
        $('#wall-empty').addClass('d-none');
        $('#wall-panel').removeClass('d-none');
        $('#wall-idx').text(i + 1);
        $('#wall-len').text(fmt(segLenMm(seg), 0));
        $('#wall-grenz').val(seg.grenzflaeche || 'aussen');
        $('#wall-u').val(seg.u_wert === null || seg.u_wert === undefined ? '' : String(seg.u_wert).replace('.', ','));
        $('#wall-konstruktion').val(seg.konstruktion_id ? String(seg.konstruktion_id) : '');
        $('#wall-azimut').val(seg.azimut_grad === null || seg.azimut_grad === undefined ? '' : String(seg.azimut_grad).replace('.', ','));
        renderOeffnungen(i);
        render();
    }

    function renderOeffnungen(i) {
        var seg = state.segments[i];
        var $list = $('#oeffnungen-liste').empty();
        (seg.oeffnungen || []).forEach(function (o, oi) {
            var row = $(
                '<div class="border rounded p-1 mb-1" data-oi="' + oi + '">' +
                '<div class="d-flex justify-content-between align-items-center mb-1">' +
                '<select class="form-select form-select-sm o-typ" style="width:auto">' +
                '<option value="fenster">Fenster</option><option value="tuer">Tür</option></select>' +
                '<button type="button" class="btn btn-xs btn-outline-danger o-del">×</button></div>' +
                '<div class="row g-1">' +
                '<div class="col-6"><input type="text" class="form-control form-control-sm o-breite" placeholder="Breite mm" inputmode="decimal"></div>' +
                '<div class="col-6"><input type="text" class="form-control form-control-sm o-hoehe" placeholder="Höhe mm" inputmode="decimal"></div>' +
                '<div class="col-12 mt-1"><input type="text" class="form-control form-control-sm o-u" placeholder="U-Wert (optional)" inputmode="decimal"></div>' +
                '</div></div>'
            );
            row.find('.o-typ').val(o.typ || 'fenster');
            row.find('.o-breite').val(o.breite_mm || '');
            row.find('.o-hoehe').val(o.hoehe_mm || '');
            row.find('.o-u').val(o.u_wert === null || o.u_wert === undefined ? '' : String(o.u_wert).replace('.', ','));
            $list.append(row);
        });
    }

    function collectOeffnungen(i) {
        var seg = state.segments[i];
        var arr = [];
        $('#oeffnungen-liste > div').each(function () {
            var $r = $(this);
            arr.push({
                typ: $r.find('.o-typ').val(),
                breite_mm: deNum($r.find('.o-breite').val()) || 0,
                hoehe_mm: deNum($r.find('.o-hoehe').val()) || 0,
                u_wert: deNum($r.find('.o-u').val())
            });
        });
        seg.oeffnungen = arr;
    }

    // ---- Contract-Objekt bauen ---------------------------------------------------
    function buildGeometrie() {
        var decke = null, boden = null;
        if ($('#f-decke-grenz').val()) {
            decke = { bauteil_typ: 'decke', grenzflaeche: $('#f-decke-grenz').val(), u_wert: deNum($('#f-decke-u').val()) };
        }
        if ($('#f-boden-grenz').val()) {
            boden = { bauteil_typ: 'boden', grenzflaeche: $('#f-boden-grenz').val(), u_wert: deNum($('#f-boden-u').val()) };
        }
        return {
            name: $('#f-name').val() || '',
            nutzung: $('#f-nutzung').val() || 'wohnen',
            geschoss: parseInt($('#f-geschoss').val(), 10) || 0,
            hoehe_mm: parseInt($('#f-hoehe').val(), 10) || 2500,
            polygon: state.polygon.map(function (p) { return { x: p.x, y: p.y }; }),
            wand_segmente: state.segments.map(function (s) {
                return {
                    von: { x: s.von.x, y: s.von.y }, bis: { x: s.bis.x, y: s.bis.y },
                    grenzflaeche: s.grenzflaeche, bauteil_typ: 'wand',
                    azimut_grad: (s.azimut_grad === undefined ? null : s.azimut_grad),
                    u_wert: (s.u_wert === undefined ? null : s.u_wert),
                    konstruktion_id: (s.konstruktion_id === undefined ? null : s.konstruktion_id),
                    oeffnungen: (s.oeffnungen || []).map(function (o) {
                        return { typ: o.typ, breite_mm: o.breite_mm, hoehe_mm: o.hoehe_mm, u_wert: (o.u_wert === undefined ? null : o.u_wert) };
                    })
                };
            }),
            decke: decke, boden: boden,
            projekt_id: projektId, raum_id: raumId
        };
    }

    function csrf() {
        return $('meta[name="csrf-token"]').attr('content') || '';
    }

    function validGrundriss() {
        if (!state.closed || state.polygon.length < 3) {
            hinweis('Bitte zuerst ein Polygon mit mindestens 3 Ecken zeichnen und schließen.');
            return false;
        }
        hinweis(null);
        return true;
    }

    function postJson(url, payload, onOk) {
        $.ajax({
            url: url, method: 'POST', contentType: 'application/json',
            data: JSON.stringify(payload),
            headers: { 'X-CSRF-TOKEN': csrf(), 'Accept': 'application/json' }
        }).done(onOk).fail(function (xhr) {
            var msg = (xhr.responseJSON && (xhr.responseJSON.message || (xhr.responseJSON.errors && Object.values(xhr.responseJSON.errors)[0][0]))) || 'Anfrage fehlgeschlagen.';
            hinweis(msg);
        });
    }

    // ---- Events ------------------------------------------------------------------
    $(svg).on('click', function (evt) {
        if (evt.target.classList && evt.target.classList.contains('wall-hit')) return; // Wandklick separat
        if (state.closed) return;
        var p = eventToMm(evt);
        state.polygon.push(p);
        render();
    });

    $(svg).on('dblclick', function (evt) {
        evt.preventDefault();
        if (!state.closed && state.polygon.length >= 3) { closePolygon(); }
    });

    $(svg).on('click', '.wall-hit', function (evt) {
        evt.stopPropagation();
        if (!state.closed) return;
        selectWall(parseInt(this.getAttribute('data-wall'), 10));
    });

    function closePolygon() {
        if (state.polygon.length < 3) { hinweis('Ein Polygon braucht mindestens 3 Ecken.'); return; }
        state.closed = true;
        rebuildSegments();
        hinweis(null);
        render();
    }

    $('#btn-schliessen').on('click', closePolygon);

    $('#btn-undo').on('click', function () {
        if (state.closed) {
            state.closed = false;
            state.selected = null;
            $('#wall-panel').addClass('d-none');
            $('#wall-empty').removeClass('d-none');
        } else if (state.polygon.length) {
            state.polygon.pop();
        }
        render();
    });

    $('#btn-reset').on('click', function () {
        state.polygon = []; state.segments = []; state.closed = false; state.selected = null;
        $('#wall-panel').addClass('d-none');
        $('#wall-empty').removeClass('d-none');
        $('#ergebnis-box').addClass('d-none');
        hinweis(null);
        render();
    });

    // Wand-Panel Änderungen live in den Zustand schreiben
    $('#wall-grenz, #wall-u, #wall-konstruktion, #wall-azimut').on('change keyup', function () {
        if (state.selected === null) return;
        var seg = state.segments[state.selected];
        seg.grenzflaeche = $('#wall-grenz').val();
        seg.u_wert = deNum($('#wall-u').val());
        var k = $('#wall-konstruktion').val();
        seg.konstruktion_id = k ? parseInt(k, 10) : null;
        seg.azimut_grad = deNum($('#wall-azimut').val());
    });

    $('#btn-add-oeffnung').on('click', function () {
        if (state.selected === null) return;
        var seg = state.segments[state.selected];
        seg.oeffnungen = seg.oeffnungen || [];
        seg.oeffnungen.push({ typ: 'fenster', breite_mm: 1000, hoehe_mm: 1300, u_wert: null });
        renderOeffnungen(state.selected);
        render();
    });

    $('#oeffnungen-liste').on('click', '.o-del', function () {
        if (state.selected === null) return;
        var oi = parseInt($(this).closest('[data-oi]').attr('data-oi'), 10);
        state.segments[state.selected].oeffnungen.splice(oi, 1);
        renderOeffnungen(state.selected);
        render();
    });

    $('#oeffnungen-liste').on('change keyup', 'input,select', function () {
        if (state.selected === null) return;
        collectOeffnungen(state.selected);
        render();
    });

    // Vorschau
    $('#btn-vorschau').on('click', function () {
        if (!validGrundriss()) return;
        var $b = $(this).prop('disabled', true);
        postJson('{{ route('energie.grundriss.vorschau') }}', buildGeometrie(), function (res) {
            $('#res-flaeche').text(fmt(res.grundflaeche_m2));
            $('#res-bauteile').text(res.bauteil_anzahl);
            $('#res-heizlast').text(fmt(res.auslegungsheizlast_kw));
            $('#res-ht').text(fmt(res.h_t_w_k));
            $('#res-hv').text(fmt(res.h_v_w_k));
            $('#ergebnis-box').removeClass('d-none');
        }).always(function () { $b.prop('disabled', false); });
    });

    // Speichern
    $('#btn-speichern').on('click', function () {
        if (!validGrundriss()) return;
        var $b = $(this).prop('disabled', true);
        postJson('{{ route('energie.grundriss.speichern') }}', buildGeometrie(), function (res) {
            projektId = res.projekt_id; raumId = res.raum_id;
            hinweis(null);
            $('#grundriss-hinweis').removeClass('d-none alert-warning').addClass('alert-success').text(res.message || 'Gespeichert.');
            setTimeout(function () { $('#grundriss-hinweis').addClass('d-none').removeClass('alert-success').addClass('alert-warning'); }, 4000);
        }).always(function () { $b.prop('disabled', false); });
    });

    // ---- Init aus bestehender Geometrie ------------------------------------------
    function loadInitial() {
        if (!initial) { render(); return; }
        $('#f-name').val(initial.name || '');
        $('#f-nutzung').val(initial.nutzung || 'wohnen');
        $('#f-hoehe').val(initial.hoehe_mm || 2500);
        $('#f-geschoss').val(initial.geschoss || 0);
        if (initial.decke) {
            $('#f-decke-grenz').val(initial.decke.grenzflaeche || '');
            if (initial.decke.u_wert != null) $('#f-decke-u').val(String(initial.decke.u_wert).replace('.', ','));
        }
        if (initial.boden) {
            $('#f-boden-grenz').val(initial.boden.grenzflaeche || '');
            if (initial.boden.u_wert != null) $('#f-boden-u').val(String(initial.boden.u_wert).replace('.', ','));
        }
        (initial.polygon || []).forEach(function (p) { state.polygon.push({ x: +p.x, y: +p.y }); });
        (initial.wand_segmente || []).forEach(function (s) {
            state.segments.push({
                von: { x: +s.von.x, y: +s.von.y }, bis: { x: +s.bis.x, y: +s.bis.y },
                grenzflaeche: s.grenzflaeche || 'aussen', bauteil_typ: 'wand',
                azimut_grad: (s.azimut_grad == null ? null : +s.azimut_grad),
                u_wert: (s.u_wert == null ? null : +s.u_wert),
                konstruktion_id: (s.konstruktion_id == null ? null : +s.konstruktion_id),
                oeffnungen: (s.oeffnungen || []).map(function (o) {
                    return { typ: o.typ, breite_mm: +o.breite_mm, hoehe_mm: +o.hoehe_mm, u_wert: (o.u_wert == null ? null : +o.u_wert) };
                })
            });
        });
        if (state.polygon.length >= 3) { state.closed = true; if (!state.segments.length) rebuildSegments(); }
        render();
    }

    loadInitial();

})(jQuery);
</script>
@endpush
