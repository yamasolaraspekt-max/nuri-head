@extends('admin.layouts.app')
@section('title', 'Je Abteilung')

{{--
    Controlling-Cockpit „Je Abteilung" — Welle B2 (2026-07-16). Zwilling zu „Gesamtfirma":
    REINE Präsentationsschicht über die bestehenden Endpunkte department.departments (Wähler)
    und department.overview (Zahlen). Eine Wahrheit, keine duplizierte Rechenlogik. sa-ui-Tokens,
    Marke nur Akzent, Inline-Balken (Hausstil), Lade-/Leer-/Fehler-Zustand.
--}}

@section('content')
<style>
    .ab-wrap { margin: 0 18px 40px; color: #1f2937; }
    .ab-bar { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; margin: 4px 0 18px; }
    .ab-bar select, .ab-bar input[type=date] { border: 1px solid #d1d5db; border-radius: 8px; padding: 6px 9px; font-size: 12.5px; color: #1f2937; background: #fff; }
    .ab-bar button { border: 1px solid var(--sa-accent, #93c21c); background: #f4fae7; color: #4d7c0f; border-radius: 8px; padding: 7px 14px; font-size: 12.5px; font-weight: 700; cursor: pointer; }

    .ab-row { display: flex; gap: 12px; flex-wrap: wrap; margin: 0 0 14px; }
    .ab-tile { flex: 1 1 150px; min-width: 150px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 12px 14px; }
    .ab-tile .k { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
    .ab-tile .v { font-size: 20px; font-weight: 800; margin-top: 4px; font-variant-numeric: tabular-nums; }
    .ab-tile.money { border-color: var(--sa-accent, #93c21c); background: var(--sa-accent-light, #f4fae7); }
    .ab-tile.money.open .v { color: #d97706; }
    .ab-tile .v.success { color: #047857; }
    .ab-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(128px, 1fr)); gap: 10px; margin: 0 0 18px; }
    .ab-mini { background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; padding: 10px 12px; }
    .ab-mini .k { font-size: 11px; color: #9ca3af; font-weight: 600; }
    .ab-mini .v { font-size: 17px; font-weight: 800; margin-top: 2px; font-variant-numeric: tabular-nums; }

    .ab-panels { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 900px) { .ab-panels { grid-template-columns: 1fr; } }
    .ab-panel { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 14px 16px; margin-bottom: 14px; }
    .ab-panel h3 { margin: 0 0 12px; font-size: 13px; font-weight: 700; }

    .ab-chart { display: flex; flex-direction: column; gap: 7px; }
    .ab-chart-row { display: grid; grid-template-columns: 110px 1fr 70px; align-items: center; gap: 8px; font-size: 12px; }
    .ab-chart-row .lbl { color: #6b7280; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .ab-chart-row .track { background: #f3f4f6; border-radius: 5px; height: 12px; overflow: hidden; }
    .ab-chart-row .fill { height: 12px; border-radius: 5px; background: var(--sa-accent, #93c21c); min-width: 2px; }
    .ab-chart-row .val { text-align: right; font-weight: 700; font-variant-numeric: tabular-nums; }

    .ab-team { display: flex; flex-direction: column; gap: 8px; }
    .ab-team-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 12.5px; border-bottom: 1px solid #f3f4f6; padding-bottom: 7px; }
    .ab-team-row:last-child { border-bottom: none; }
    .ab-team-name { font-weight: 600; }
    .ab-team-pos { color: #6b7280; font-size: 11.5px; }
    .ab-pct { font-variant-numeric: tabular-nums; color: #374151; font-weight: 700; }

    .ab-state { background: #fff; border: 1px dashed #d1d5db; border-radius: 10px; padding: 34px; text-align: center; color: #6b7280; font-size: 13.5px; }
    .ab-err { border-color: #ef4444; color: #b91c1c; }
</style>

<div class="ab-wrap">
    <x-page-head title="Je Abteilung" sub="Kennzahlen einer einzelnen Abteilung über den gewählten Zeitraum — aus der zentralen Auswertung." current="Je Abteilung" />

    <div class="ab-bar">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Abteilung</label>
        <select id="ab-dept"><option>lädt …</option></select>
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Von</label>
        <input type="date" id="ab-from">
        <label style="font-size:12px;font-weight:700;color:#6b7280;">Bis</label>
        <input type="date" id="ab-to">
        <button type="button" id="ab-apply">Anwenden</button>
    </div>

    <div id="ab-content"><div class="ab-state">Abteilungen werden geladen …</div></div>
</div>

<script>
(function () {
    const deptListUrl = @json(route('dashboard.department.departments'));
    const overviewUrl = @json(route('dashboard.department.overview'));
    const content = document.getElementById('ab-content');
    const selDept = document.getElementById('ab-dept');
    const eur = (n) => (Number(n) || 0).toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    const num = (n) => (Number(n) || 0).toLocaleString('de-DE');
    const esc = (s) => String(s ?? '').replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

    function balken(labels, values, formatter) {
        if (!labels || !labels.length) return '<div class="ab-state">Keine Daten im Zeitraum.</div>';
        const max = Math.max(1, ...values.map(Number));
        return '<div class="ab-chart">' + labels.map((l, i) => {
            const v = Number(values[i]) || 0;
            const pct = Math.max(2, Math.round(v / max * 100));
            return '<div class="ab-chart-row"><span class="lbl" title="' + esc(l) + '">' + esc(l) + '</span>'
                + '<span class="track"><span class="fill" style="width:' + pct + '%"></span></span>'
                + '<span class="val">' + formatter(v) + '</span></div>';
        }).join('') + '</div>';
    }
    const tile = (k, v, cls) => '<div class="ab-tile ' + (cls || '') + '"><div class="k">' + k + '</div><div class="v ' + (cls && cls.indexOf('success') > -1 ? 'success' : '') + '">' + v + '</div></div>';
    const mini = (k, v) => '<div class="ab-mini"><div class="k">' + k + '</div><div class="v">' + v + '</div></div>';

    function render(d) {
        const k = d.kpis || {};
        let html = '';
        html += '<div class="ab-row">'
            + tile('Rechnungsvolumen', eur(k.invoice_total), 'money')
            + tile('Offene Forderungen', eur(k.invoice_open), 'money open')
            + tile('Rechnungen', num(k.invoices), 'money')
            + '</div>';
        html += '<div class="ab-grid">'
            + mini('Team', num(k.team_members)) + mini('Angebote', num(k.offers))
            + mini('Aufträge', num(k.deals)) + mini('Leads', num(k.leads))
            + mini('Neue Leads', num(k.new_leads)) + mini('Objekte', num(k.objects))
            + mini('Produkte', num(k.products)) + mini('Tickets', num(k.tickets))
            + mini('Termine', num(k.appointments)) + mini('Aufgaben', num(k.tasks))
            + '</div>';

        const c = d.charts || {};
        html += '<div class="ab-panels">'
            + '<div class="ab-panel"><h3>Auslastung je Mitarbeiter</h3>' + balken(c.workload_by_employee?.labels, c.workload_by_employee?.values, num) + '</div>'
            + '<div class="ab-panel"><h3>Aktivität nach Typ</h3>' + balken(c.items_by_type?.labels, c.items_by_type?.values, num) + '</div>'
            + '</div>';
        html += '<div class="ab-panels">'
            + '<div class="ab-panel"><h3>Lead-Pipeline</h3>' + balken(c.lead_pipeline?.labels, c.lead_pipeline?.values, num) + '</div>'
            + '<div class="ab-panel"><h3>Produkte nach Gruppe</h3>' + balken(c.products_by_group?.labels, c.products_by_group?.values, num) + '</div>'
            + '</div>';

        const team = d.team || [];
        html += '<div class="ab-panel"><h3>Team (' + team.length + ')</h3>'
            + (team.length ? '<div class="ab-team">' + team.map((t) => '<div class="ab-team-row"><span>'
                + '<span class="ab-team-name">' + esc(t.name) + '</span> '
                + '<span class="ab-team-pos">' + esc(t.position || '') + '</span></span>'
                + '<span class="ab-pct">' + num(t.percent) + ' %</span></div>').join('') + '</div>'
                : '<div class="ab-state">Kein Team hinterlegt.</div>')
            + '</div>';

        content.innerHTML = html;
    }

    function ladeOverview() {
        content.innerHTML = '<div class="ab-state">Kennzahlen werden geladen …</div>';
        const url = new URL(overviewUrl, window.location.origin);
        if (selDept.value) url.searchParams.set('department_id', selDept.value);
        const from = document.getElementById('ab-from').value;
        const to = document.getElementById('ab-to').value;
        if (from) url.searchParams.set('from', from);
        if (to) url.searchParams.set('to', to);

        fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then((r) => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then((d) => {
                if (!d || d.success === false) throw new Error(d && d.message ? d.message : 'Antwort ungültig');
                if (!document.getElementById('ab-from').value && d.period) {
                    document.getElementById('ab-from').value = d.period.from;
                    document.getElementById('ab-to').value = d.period.to;
                }
                render(d);
            })
            .catch((e) => {
                content.innerHTML = '<div class="ab-state ab-err">Kennzahlen konnten nicht geladen werden (' + esc(e.message) + ').</div>';
            });
    }

    function ladeAbteilungen() {
        fetch(deptListUrl, { credentials: 'same-origin', headers: { Accept: 'application/json' } })
            .then((r) => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then((d) => {
                const list = d.departments || [];
                if (!list.length) { content.innerHTML = '<div class="ab-state">Keine Abteilung zugänglich.</div>'; selDept.innerHTML = ''; return; }
                selDept.innerHTML = list.map((x) => '<option value="' + x.id + '"' + (x.id === d.default_department_id ? ' selected' : '') + '>' + esc(x.name) + '</option>').join('');
                ladeOverview();
            })
            .catch((e) => { content.innerHTML = '<div class="ab-state ab-err">Abteilungen nicht ladbar (' + esc(e.message) + ').</div>'; });
    }

    selDept.addEventListener('change', ladeOverview);
    document.getElementById('ab-apply').addEventListener('click', ladeOverview);
    ladeAbteilungen();
})();
</script>
@endsection
