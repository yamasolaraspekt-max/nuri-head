@extends('admin.layouts.app')
@section('title', 'Gesamtfirma')

{{--
    Controlling-Cockpit „Gesamtfirma" — Welle B2 (2026-07-16). REINE Präsentationsschicht:
    alle Zahlen kommen aus dem bestehenden Endpunkt dashboard.company.overview (eine Wahrheit,
    keine duplizierte Rechenlogik). Diese Seite fetcht das JSON und rendert KPIs + Charts +
    Abteilungstabelle. sa-ui-Tokens, Markenfarbe nur als Akzent, kein Schwarz/Fremdblau,
    Lade-/Leer-/Fehler-Zustand gestaltet. Charts als Inline-Balken (Hausstil wie Umsätze),
    keine Chart-Bibliothek.
--}}

@section('content')
<style>
    .gf-wrap { margin: 0 18px 40px; color: #1f2937; }
    .gf-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 4px 0 18px; }
    .gf-bar input[type=date] { border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 9px; font-size: 12.5px; color: #1f2937; }
    .gf-bar button { border: 1px solid var(--sa-accent, #93c21c); background: #f4fae7; color: #4d7c0f; border-radius: 8px; padding: 7px 14px; font-size: 12.5px; font-weight: 700; cursor: pointer; }
    .gf-dept { font-size: 12.5px; color: #6b7280; }

    .gf-row { display: flex; gap: 12px; flex-wrap: wrap; margin: 0 0 14px; }
    .gf-tile { flex: 1 1 150px; min-width: 150px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .gf-tile .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .gf-tile .v { font-size: 20px; font-weight: 800; margin-top: 4px; font-variant-numeric: tabular-nums; }
    .gf-tile.money { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .gf-tile.money.open .v { color: #d97706; }
    .gf-tile .v.success { color: #047857; }
    .gf-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; margin: 0 0 18px; }
    .gf-mini { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; }
    .gf-mini .k { font-size: 11px; color: #9ca3af; font-weight: 600; }
    .gf-mini .v { font-size: 17px; font-weight: 800; margin-top: 2px; font-variant-numeric: tabular-nums; }

    .gf-panels { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 900px) { .gf-panels { grid-template-columns: 1fr; } }
    .gf-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; }
    .gf-panel h3 { margin: 0 0 12px; font-size: 13px; font-weight: 700; }

    .gf-chart { display: flex; flex-direction: column; gap: 7px; }
    .gf-chart-row { display: grid; grid-template-columns: 92px 1fr 84px; align-items: center; gap: 8px; font-size: 12px; }
    .gf-chart-row .lbl { color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .gf-chart-row .track { background: #f3f4f6; border-radius: 5px; height: 12px; overflow: hidden; }
    .gf-chart-row .fill { height: 12px; border-radius: 5px; background: var(--sa-accent, #93c21c); min-width: 2px; }
    .gf-chart-row .val { text-align: right; font-weight: 700; font-variant-numeric: tabular-nums; }

    .gf-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    .gf-table th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; border-bottom: 1px solid #e5e7eb; padding: 7px 9px; }
    .gf-table td { border-bottom: 1px solid #f3f4f6; padding: 7px 9px; }
    .gf-table td.num, .gf-table th.num { text-align: right; font-variant-numeric: tabular-nums; }

    .gf-state { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 34px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .gf-err { border-color: #ef4444; color: #b91c1c; }
    .gf-panels.mt { margin-top: 14px; }
</style>

<div class="gf-wrap">
    <x-page-head title="Gesamtfirma" sub="Firmenweite Kennzahlen über den gewählten Zeitraum — aus der zentralen Auswertung." current="Gesamtfirma" />

    <div class="gf-bar">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Von</label>
        <input type="date" id="gf-from">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Bis</label>
        <input type="date" id="gf-to">
        <button type="button" id="gf-apply">Anwenden</button>
        <span class="gf-dept" id="gf-dept"></span>
    </div>

    <div id="gf-content"><div class="gf-state" id="gf-loading">Kennzahlen werden geladen …</div></div>
</div>

<script>
(function () {
    const overviewUrl = @json(route('dashboard.company.overview'));
    const content = document.getElementById('gf-content');
    const eur = (n) => (Number(n) || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    const num = (n) => (Number(n) || 0).toLocaleString('de-DE');
    const esc = (s) => String(s ?? '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

    function balken(labels, values, formatter) {
        const max = Math.max(1, ...values.map(Number));
        return '<div class="gf-chart">' + labels.map((l, i) => {
            const v = Number(values[i]) || 0;
            const pct = Math.max(2, Math.round(v / max * 100));
            return '<div class="gf-chart-row"><span class="lbl" title="' + esc(l) + '">' + esc(l) + '</span>'
                + '<span class="track"><span class="fill" style="width:' + pct + '%"></span></span>'
                + '<span class="val">' + formatter(v) + '</span></div>';
        }).join('') + '</div>';
    }

    function tile(k, v, cls) { return '<div class="gf-tile ' + (cls || '') + '"><div class="k">' + k + '</div><div class="v ' + (cls && cls.indexOf('success') > -1 ? 'success' : '') + '">' + v + '</div></div>'; }
    function mini(k, v) { return '<div class="gf-mini"><div class="k">' + k + '</div><div class="v">' + v + '</div></div>'; }

    function render(d) {
        const k = d.kpis || {};
        const dept = document.getElementById('gf-dept');
        if (d.main_department) dept.textContent = 'Hauptabteilung: ' + (d.main_department.name || '—');

        let html = '';
        // Geld-Zeile (führend)
        html += '<div class="gf-row">'
            + tile('Umsatz (bezahlt)', eur(k.invoice_total), 'money success')
            + tile('Offene Forderungen', eur(k.invoice_open), 'money open')
            + tile('Bezahlte Rechnungen', num(k.paid_invoices), 'money')
            + '</div>';

        // Volumen-Raster
        html += '<div class="gf-grid">'
            + mini('Angebote', num(k.offers)) + mini('Aufträge', num(k.deals))
            + mini('Rechnungen', num(k.invoices)) + mini('Leads gesamt', num(k.leads))
            + mini('Neue Leads', num(k.new_leads)) + mini('Tickets', num(k.tickets))
            + mini('Termine', num(k.appointments)) + mini('Aufgaben', num(k.tasks))
            + mini('Mitarbeiter', num(k.employees)) + mini('Abteilungen', num(k.departments))
            + '</div>';

        // Charts
        const mr = d.charts?.monthly_revenue || { labels: [], values: [] };
        const it = d.charts?.items_by_type || { labels: [], values: [] };
        html += '<div class="gf-panels">'
            + '<div class="gf-panel"><h3>Monatsumsatz (bezahlt) · Σ ' + eur(mr.total) + '</h3>'
            + (mr.labels.length ? balken(mr.labels, mr.values, eur) : '<div class="gf-state">Keine Umsätze im Zeitraum.</div>') + '</div>'
            + '<div class="gf-panel"><h3>Aktivität nach Typ</h3>'
            + (it.labels.length ? balken(it.labels, it.values, num) : '<div class="gf-state">Keine Aktivität.</div>') + '</div>'
            + '</div>';

        // Abteilungstabelle
        const rows = (d.recent?.departments || []);
        html += '<div class="gf-panels mt"><div class="gf-panel" style="grid-column:1/-1;overflow-x:auto;">'
            + '<h3>Abteilungen im Zeitraum</h3><table class="gf-table"><thead><tr>'
            + '<th>Abteilung</th><th class="num">Team</th><th class="num">Leads gesamt</th><th class="num">Neue Leads</th><th class="num">Angebote</th><th class="num">Aufträge</th>'
            + '</tr></thead><tbody>'
            + (rows.length ? rows.map((r) => '<tr><td>' + esc(r.name) + '</td><td class="num">' + num(r.team_count)
                + '</td><td class="num">' + num(r.leads) + '</td><td class="num">' + num(r.new_leads)
                + '</td><td class="num">' + num(r.offers) + '</td><td class="num">' + num(r.deals) + '</td></tr>').join('')
                : '<tr><td colspan="6" style="color:#9ca3af;padding:16px;text-align:center;">Keine Abteilungsdaten.</td></tr>')
            + '</tbody></table></div></div>';

        content.innerHTML = html;
    }

    function laden() {
        content.innerHTML = '<div class="gf-state">Kennzahlen werden geladen …</div>';
        const from = document.getElementById('gf-from').value;
        const to = document.getElementById('gf-to').value;
        const url = new URL(overviewUrl, window.location.origin);
        if (from) url.searchParams.set('from', from);
        if (to) url.searchParams.set('to', to);

        fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then((r) => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then((d) => {
                if (!d || d.success === false) throw new Error('Antwort ungültig');
                if (!document.getElementById('gf-from').value && d.period) {
                    document.getElementById('gf-from').value = d.period.from;
                    document.getElementById('gf-to').value = d.period.to;
                }
                render(d);
            })
            .catch((e) => {
                content.innerHTML = '<div class="gf-state gf-err">Kennzahlen konnten nicht geladen werden ('
                    + esc(e.message) + '). Bitte erneut versuchen.</div>';
            });
    }

    document.getElementById('gf-apply').addEventListener('click', laden);
    laden();
})();
</script>
@endsection
