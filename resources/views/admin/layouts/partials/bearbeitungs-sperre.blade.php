{{--
    Bearbeitungs-Sperre (Drop-in, 2026-07-16) — EINE Mechanik für alle Dokument-Editoren.
    Nutzung in einer Editor-View:
        @include('admin.layouts.partials.bearbeitungs-sperre', ['bereich' => 'rechnung', 'sperrId' => $invoice->id])
    Verhalten: Heartbeat alle 30 s; hält ein Kollege die Sperre, erscheint ein fixes Banner
    und das Event `sperre:locked` (bzw. `sperre:frei`) auf window — Seiten können damit
    zusätzlich ihre Speichern-Buttons deaktivieren. Beim Verlassen wird die Sperre
    per sendBeacon freigegeben; nach 2 Minuten ohne Heartbeat verfällt sie ohnehin.
    Sperre gilt JE DOKUMENT (Yama-Entscheid 2026-07-16). Additiv — bricht nichts, wenn JS aus.
--}}
@php
    $sperrBereich = $bereich ?? null;
    $sperrIdWert = $sperrId ?? null;
@endphp
@if ($sperrBereich && $sperrIdWert !== null)
<div id="sa-sperre-banner" style="display:none; position:fixed; top:0; left:0; right:0; z-index:99999;
    background:#fff7ed; border-bottom:2px solid #f59e0b; color:#d97706;
    font:600 13px Inter,system-ui,sans-serif; padding:9px 16px; text-align:center;">
    🔒 <span id="sa-sperre-text">Dieses Dokument wird gerade bearbeitet.</span>
    <span style="font-weight:500; color:#92400e;">Bitte nicht gleichzeitig ändern — deine Änderungen könnten verloren gehen.</span>
</div>
<script>
(function () {
    var bereich = @json($sperrBereich);
    var id = String(@json($sperrIdWert));
    var pingUrl = @json(route('system.sperre.ping'));
    var leaveUrl = @json(route('system.sperre.leave'));
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content
        || @json(csrf_token());
    var banner = document.getElementById('sa-sperre-banner');
    var text = document.getElementById('sa-sperre-text');
    var lockedByOther = false;

    function ping() {
        fetch(pingUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            body: JSON.stringify({ bereich: bereich, id: id }),
            credentials: 'same-origin'
        }).then(function (r) { return r.ok ? r.json() : null; }).then(function (data) {
            if (!data || !data.success) { return; }
            var nowLocked = !!data.locked_by_other;
            if (nowLocked && data.lock_user && data.lock_user.name) {
                text.textContent = 'Dieses Dokument wird gerade von ' + data.lock_user.name + ' bearbeitet.';
            }
            banner.style.display = nowLocked ? 'block' : 'none';
            if (nowLocked !== lockedByOther) {
                lockedByOther = nowLocked;
                window.dispatchEvent(new CustomEvent(nowLocked ? 'sperre:locked' : 'sperre:frei', { detail: data }));
            }
        }).catch(function () { /* Netzfehler: nichts blockieren (weiche Sperre) */ });
    }

    ping();
    var timer = setInterval(ping, 30000);

    window.addEventListener('beforeunload', function () {
        clearInterval(timer);
        try {
            navigator.sendBeacon(leaveUrl, new Blob(
                [JSON.stringify({ bereich: bereich, id: id, _token: csrf })],
                { type: 'application/json' }
            ));
        } catch (e) { /* Verfall nach 2 Min greift ohnehin */ }
    });
})();
</script>
@endif
