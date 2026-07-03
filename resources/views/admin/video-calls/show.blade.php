<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Video-Call — Solar Aspekt</title>
    <style>
        html,body{margin:0;height:100%;font-family:Arial,Helvetica,sans-serif;background:#111827;color:#f9fafb;}
        .layout{display:flex;height:100vh;}
        .main{flex:1;display:flex;flex-direction:column;min-width:0;}
        .topbar{display:flex;align-items:center;gap:12px;padding:10px 16px;background:#0b0f19;}
        .logo{color:#93c21c;font-weight:bold;font-size:16px;}
        .topbar .spacer{flex:1;}
        .btn{cursor:pointer;border:0;border-radius:6px;padding:8px 14px;font-size:14px;text-decoration:none;display:inline-block;}
        .btn-back{background:#374151;color:#fff;}
        .btn-primary{background:#93c21c;color:#fff;}
        .btn-copy{background:#2563eb;color:#fff;}
        #jitsi-container{flex:1;min-height:0;}
        .side{width:320px;background:#0b0f19;border-left:1px solid #1f2937;padding:16px;overflow:auto;}
        .side h3{font-size:14px;margin:0 0 8px;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;}
        .field{width:100%;box-sizing:border-box;padding:8px;border-radius:6px;border:1px solid #374151;background:#111827;color:#f9fafb;margin-bottom:8px;font-size:13px;}
        .muted{color:#9ca3af;font-size:12px;}
        .row{display:flex;gap:8px;margin-bottom:8px;}
        .row .field{margin-bottom:0;}
        .result{font-size:12px;margin-top:8px;}
        .result .ok{color:#84cc16;}
        .result .err{color:#f87171;}
        hr{border:0;border-top:1px solid #1f2937;margin:16px 0;}
    </style>
</head>
<body>
<div class="layout">
    <div class="main">
        <div class="topbar">
            <span class="logo">Solar Aspekt</span>
            <span>Video-Call
                @if($videoCall->customer) · {{ trim(($videoCall->customer->name ?? '').' '.($videoCall->customer->lastname ?? '')) ?: ($videoCall->customer->firma ?? 'Kunde') }}
                @elseif($videoCall->peer) · {{ optional($videoCall->peer)->name }}
                @elseif($videoCall->chatGroup) · {{ optional($videoCall->chatGroup)->name }}
                @endif
            </span>
            <span class="spacer"></span>
            <a href="javascript:history.back()" class="btn btn-back">Zurück</a>
        </div>
        <div id="jitsi-container"></div>
    </div>

    <div class="side">
        @if($guestUrl)
            <h3>Gast-Link für den Kunden</h3>
            <p class="muted">Diesen Link an den Kunden senden (per E-Mail unten oder kopieren). Kein Login nötig.</p>
            <textarea class="field" id="guestLink" rows="3" readonly>{{ $guestUrl }}</textarea>
            <button class="btn btn-copy" type="button" onclick="copyGuestLink()">Link kopieren</button>
            <div class="result" id="copyResult"></div>

            <hr>

            <h3>Per E-Mail einladen</h3>
            <div id="inviteRows">
                @if($videoCall->customer && $videoCall->customer->email)
                    <div class="row invite-row">
                        <input class="field invite-name" type="text" placeholder="Name" value="{{ trim(($videoCall->customer->name ?? '').' '.($videoCall->customer->lastname ?? '')) }}">
                        <input class="field invite-email" type="email" placeholder="E-Mail" value="{{ $videoCall->customer->email }}">
                    </div>
                @endif
                <div class="row invite-row">
                    <input class="field invite-name" type="text" placeholder="Name (optional)">
                    <input class="field invite-email" type="email" placeholder="E-Mail">
                </div>
            </div>
            <button class="btn btn-back" type="button" onclick="addInviteRow()">+ Empfänger</button>
            <button class="btn btn-primary" type="button" onclick="sendInvites()">Einladungen senden</button>
            <div class="result" id="inviteResult"></div>
        @else
            <h3>Interner Video-Call</h3>
            <p class="muted">Interner Call zwischen Mitarbeitern. Kein Gast-Zugang, keine externen Einladungen.</p>
        @endif
    </div>
</div>

<script src="https://{{ config('jitsi.domain') }}/external_api.js"></script>
<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const ENDE_URL = @json(route('video-calls.ende', $videoCall));
    const INVITE_URL = @json($guestUrl ? route('video-calls.einladungen', $videoCall) : null);

    (function () {
        const domain = @json(config('jitsi.domain'));
        const options = {
            roomName: @json($videoCall->room_name),
            parentNode: document.getElementById('jitsi-container'),
            lang: 'de',
            userInfo: { displayName: @json($displayName) }
            @if($jwt), jwt: @json($jwt) @endif
        };

        if (typeof JitsiMeetExternalAPI === 'undefined') {
            document.getElementById('jitsi-container').innerHTML =
                '<div style="padding:24px;">Der Video-Dienst ist derzeit nicht erreichbar.</div>';
            return;
        }

        const api = new JitsiMeetExternalAPI(domain, options);
        let ended = false;
        function endCall() {
            if (ended) return;
            ended = true;
            fetch(ENDE_URL, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
            }).catch(function(){}).finally(function () {
                try { api.dispose(); } catch (e) {}
                history.back();
            });
        }
        api.addEventListener('videoConferenceLeft', endCall);
        api.addEventListener('readyToClose', endCall);
    })();

    function copyGuestLink() {
        const el = document.getElementById('guestLink');
        el.select();
        try {
            navigator.clipboard.writeText(el.value);
            document.getElementById('copyResult').innerHTML = '<span class="ok">Link kopiert.</span>';
        } catch (e) {
            document.execCommand('copy');
            document.getElementById('copyResult').innerHTML = '<span class="ok">Link kopiert.</span>';
        }
    }

    function addInviteRow() {
        const wrap = document.getElementById('inviteRows');
        const row = document.createElement('div');
        row.className = 'row invite-row';
        row.innerHTML = '<input class="field invite-name" type="text" placeholder="Name (optional)">' +
                        '<input class="field invite-email" type="email" placeholder="E-Mail">';
        wrap.appendChild(row);
    }

    function sendInvites() {
        if (!INVITE_URL) return;
        const rows = document.querySelectorAll('#inviteRows .invite-row');
        const recipients = [];
        rows.forEach(function (r) {
            const email = r.querySelector('.invite-email').value.trim();
            const name = r.querySelector('.invite-name').value.trim();
            if (email) recipients.push({ email: email, name: name || null });
        });
        const box = document.getElementById('inviteResult');
        if (recipients.length === 0) { box.innerHTML = '<span class="err">Bitte mindestens eine E-Mail angeben.</span>'; return; }
        box.textContent = 'Sende …';
        fetch(INVITE_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ recipients: recipients })
        }).then(function (r) { return r.json(); }).then(function (data) {
            const lines = (data.results || []).map(function (x) {
                return x.sent ? '<div class="ok">✓ ' + x.email + '</div>'
                              : '<div class="err">✗ ' + x.email + (x.error ? ' — ' + x.error : '') + '</div>';
            });
            box.innerHTML = '<div>' + (data.message || '') + '</div>' + lines.join('');
        }).catch(function () {
            box.innerHTML = '<span class="err">Senden fehlgeschlagen.</span>';
        });
    }
</script>
</body>
</html>
