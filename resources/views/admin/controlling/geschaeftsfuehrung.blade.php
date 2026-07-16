@extends('admin.layouts.app')
@section('title', 'Geschäftsführung')

{{--
    Controlling-Cockpit „Geschäftsführung" — Welle B2 (2026-07-16). Profit-Center-Vergleich:
    alle zugänglichen Abteilungen NEBENEINANDER, gerankt nach Rechnungsvolumen. EINE Wahrheit:
    die Zahlen je Abteilung kommen aus DEMSELBEN Endpunkt department.overview wie das
    Je-Abteilung-Cockpit (keine zweite Umsatz-Definition). Reine Präsentationsschicht.

    Bewusste Grenze (V1): pro Abteilung ein Abruf von department.overview (N Abrufe). Bei
    wenigen Abteilungen tragfähig; ein schlanker Sammel-Endpunkt wäre die V2-Optimierung —
    hier ehrlich benannt statt still gedeckelt. sa-ui-Tokens, Inline-Balken, Zustände gestaltet.
--}}

@section('content')
<style>
    .pc-wrap { margin: 0 18px 40px; color: #1f2937; }
    .pc-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 4px 0 16px; }
    .pc-bar input[type=date] { border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 9px; font-size: 12.5px; color: #1f2937; }
    .pc-bar button { border: 1px solid var(--sa-accent, #93c21c); background: #f4fae7; color: #4d7c0f; border-radius: 8px; padding: 7px 14px; font-size: 12.5px; font-weight: 700; cursor: pointer; }
    .pc-note { font-size: 11.5px; color: #9ca3af; }

    .pc-row { display: flex; gap: 12px; flex-wrap: wrap; margin: 0 0 16px; }
    .pc-tile { flex: 1 1 170px; min-width: 170px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .pc-tile.total { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .pc-tile .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .pc-tile .v { font-size: 20px; font-weight: 800; margin-top: 4px; font-variant-numeric: tabular-nums; }
    .pc-tile.open .v { color: #d97706; }

    .pc-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px; }
    .pc-panel h3 { margin: 0 0 12px; font-size: 13px; font-weight: 700; }
    .pc-chart { display: flex; flex-direction: column; gap: 7px; }
    .pc-chart-row { display: grid; grid-template-columns: 150px 1fr 120px; align-items: center; gap: 8px; font-size: 12px; }
    .pc-chart-row .lbl { color: #374151; font-weight: 600; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .pc-chart-row .track { background: #f3f4f6; border-radius: 5px; height: 13px; overflow: hidden; }
    .pc-chart-row .fill { height: 13px; border-radius: 5px; background: var(--sa-accent, #93c21c); min-width: 2px; }
    .pc-chart-row .val { text-align: right; font-weight: 700; font-variant-numeric: tabular-nums; }

    .pc-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    .pc-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 8px 10px; }
    .pc-table th.num, .pc-table td.num { text-align: right; font-variant-numeric: tabular-nums; }
    .pc-table td { border-bottom: 1px solid #f3f4f6; padding: 8px 10px; }
    .pc-table tbody tr:hover { background: #f9fafb; }
    .pc-table tfoot td { font-weight: 800; background: #f9fafb; border-top: 2px solid #e5e7eb; }
    .pc-rank { color: #9ca3af; font-weight: 700; width: 26px; }

    .pc-state { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 34px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .pc-err { border-color: #ef4444; color: #b91c1c; }
    .pc-prog { font-size: 12px; color: #6b7280; }
</style>

<div class="pc-wrap">
    <x-page-head title="Geschäftsführung" sub="Profit-Center-Vergleich aller Abteilungen über den gewählten Zeitraum — gerankt nach Rechnungsvolumen." current="Geschäftsführung" />

    <div class="pc-bar">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Von</label>
        <input type="date" id="pc-from">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Bis</label>
        <input type="date" id="pc-to">
        <button type="button" id="pc-apply">Anwenden</button>
        <span class="pc-note">Zahlen je Abteilung aus derselben Auswertung wie „Je Abteilung".</span>
    </div>

    <div id="pc-content"><div class="pc-state">Abteilungen werden geladen …</div></div>
</div>

<script>
(function () {
    const deptListUrl = @json(route('dashboard.department.departments'));
    const overviewUrl = @json(route('dashboard.department.overview'));
    const content = document.getElementById('pc-content');
    const eur = (n) => (Number(n) || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    const num = (n) => (Number(n) || 0).toLocaleString('de-DE');
    const esc = (s) => String(s ?? '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

    function balken(rows) {
        const max = Math.max(1, ...rows.map((r) => r.volumen));
        return '<div class="pc-chart">' + rows.map((r) => {
            const pct = Math.max(2, Math.round(r.volumen / max * 100));
            return '<div class="pc-chart-row"><span class="lbl" title="' + esc(r.name) + '">' + esc(r.name) + '</span>'
                + '<span class="track"><span class="fill" style="width:' + pct + '%"></span></span>'
                + '<span class="val">' + eur(r.volumen) + '</span></div>';
        }).join('') + '</div>';
    }

    function render(rows) {
        rows.sort((a, b) => b.volumen - a.volumen);
        const sum = (f) => rows.reduce((s, r) => s + (Number(r[f]) || 0), 0);

        let html = '<div class="pc-row">'
            + '<div class="pc-tile total"><div class="k">Rechnungsvolumen gesamt</div><div class="v">' + eur(sum('volumen')) + '</div></div>'
            + '<div class="pc-tile open"><div class="k">Offene Forderungen</div><div class="v">' + eur(sum('offen')) + '</div></div>'
            + '<div class="pc-tile"><div class="k">Aufträge</div><div class="v">' + num(sum('deals')) + '</div></div>'
            + '<div class="pc-tile"><div class="k">Abteilungen</div><div class="v">' + num(rows.length) + '</div></div>'
            + '</div>';

        html += '<div class="pc-panel"><h3>Rechnungsvolumen je Abteilung</h3>' + balken(rows) + '</div>';

        html += '<div class="pc-panel" style="overflow-x:auto;"><h3>Profit-Center-Tabelle</h3><table class="pc-table"><thead><tr>'
            + '<th></th><th>Abteilung</th><th class="num">Team</th><th class="num">Rechnungsvolumen</th><th class="num">Offen</th>'
            + '<th class="num">Aufträge</th><th class="num">Angebote</th><th class="num">Leads</th></tr></thead><tbody>'
            + rows.map((r, i) => '<tr><td class="pc-rank">' + (i + 1) + '</td><td>' + esc(r.name) + '</td>'
                + '<td class="num">' + num(r.team) + '</td><td class="num">' + eur(r.volumen) + '</td>'
                + '<td class="num">' + eur(r.offen) + '</td><td class="num">' + num(r.deals) + '</td>'
                + '<td class="num">' + num(r.offers) + '</td><td class="num">' + num(r.leads) + '</td></tr>').join('')
            + '</tbody><tfoot><tr><td></td><td>Gesamt</td><td class="num">' + num(sum('team')) + '</td>'
            + '<td class="num">' + eur(sum('volumen')) + '</td><td class="num">' + eur(sum('offen')) + '</td>'
            + '<td class="num">' + num(sum('deals')) + '</td><td class="num">' + num(sum('offers')) + '</td>'
            + '<td class="num">' + num(sum('leads')) + '</td></tr></tfoot></table></div>';

        content.innerHTML = html;
    }

    function ladeVergleich() {
        const from = document.getElementById('pc-from').value;
        const to = document.getElementById('pc-to').value;

        fetch(deptListUrl, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then((r) => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then((d) => {
                const list = d.departments || [];
                if (!list.length) { content.innerHTML = '<div class="pc-state">Keine Abteilung zugänglich.</div>'; return; }
                content.innerHTML = '<div class="pc-state"><span class="pc-prog">Lade Kennzahlen von ' + list.length + ' Abteilungen …</span></div>';

                return Promise.all(list.map((dept) => {
                    const url = new URL(overviewUrl, window.location.origin);
                    url.searchParams.set('department_id', dept.id);
                    if (from) url.searchParams.set('from', from);
                    if (to) url.searchParams.set('to', to);
                    return fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
                        .then((r) => r.ok ? r.json() : null)
                        .then((o) => {
                            const k = (o && o.kpis) || {};
                            if (o && o.period && !document.getElementById('pc-from').value) {
                                document.getElementById('pc-from').value = o.period.from;
                                document.getElementById('pc-to').value = o.period.to;
                            }
                            return {
                                name: dept.name,
                                team: k.team_members || 0,
                                volumen: Number(k.invoice_total) || 0,   // Rechnungsvolumen (alle Status, wie Je-Abteilung)
                                offen: Number(k.invoice_open) || 0,
                                deals: k.deals || 0,
                                offers: k.offers || 0,
                                leads: k.leads || 0,
                                fehler: !o,
                            };
                        });
                }));
            })
            .then((rows) => { if (rows) render(rows.filter(Boolean)); })
            .catch((e) => { content.innerHTML = '<div class="pc-state pc-err">Vergleich konnte nicht geladen werden (' + esc(e.message) + ').</div>'; });
    }

    document.getElementById('pc-apply').addEventListener('click', ladeVergleich);
    ladeVergleich();
})();
</script>
@endsection
