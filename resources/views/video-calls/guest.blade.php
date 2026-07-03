<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video-Call — Solar Aspekt</title>
    <style>
        html,body{margin:0;height:100%;font-family:Arial,Helvetica,sans-serif;background:#111827;color:#f9fafb;}
        .topbar{display:flex;align-items:center;gap:10px;padding:12px 18px;background:#0b0f19;}
        .logo{color:#93c21c;font-weight:bold;font-size:16px;}
        #jitsi-container{width:100%;height:calc(100vh - 46px);}
        .ended{display:none;position:fixed;inset:0;align-items:center;justify-content:center;text-align:center;padding:24px;}
        .ended.show{display:flex;}
        .ended .box{max-width:420px;}
    </style>
</head>
<body>
    <div class="topbar"><span class="logo">Solar Aspekt</span><span>Video-Call</span></div>
    <div id="jitsi-container"></div>

    <div class="ended" id="ended">
        <div class="box">
            <h2>Das Gespräch wurde beendet</h2>
            <p>Vielen Dank. Sie können dieses Fenster jetzt schließen.</p>
        </div>
    </div>

    <script src="https://{{ config('jitsi.domain') }}/external_api.js"></script>
    <script>
        (function () {
            var domain = @json(config('jitsi.domain'));
            var options = {
                roomName: @json($videoCall->room_name),
                parentNode: document.getElementById('jitsi-container'),
                lang: 'de',
                userInfo: { displayName: @json($displayName) }
                @if($jwt), jwt: @json($jwt) @endif
            };

            if (typeof JitsiMeetExternalAPI === 'undefined') {
                document.getElementById('jitsi-container').innerHTML =
                    '<div style="padding:24px;text-align:center;">Der Video-Dienst ist derzeit nicht erreichbar.</div>';
                return;
            }

            var api = new JitsiMeetExternalAPI(domain, options);
            function showEnded() {
                document.getElementById('ended').classList.add('show');
                try { api.dispose(); } catch (e) {}
            }
            api.addEventListener('readyToClose', showEnded);
        })();
    </script>
</body>
</html>
