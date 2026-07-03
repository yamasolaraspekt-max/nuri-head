<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video-Call nicht verfügbar</title>
    <style>
        html,body{margin:0;height:100%;font-family:Arial,Helvetica,sans-serif;background:#f4f5f7;color:#1f2937;}
        .wrap{display:flex;min-height:100%;align-items:center;justify-content:center;padding:24px;}
        .card{background:#fff;border-radius:10px;max-width:440px;width:100%;padding:32px;text-align:center;box-shadow:0 6px 24px rgba(0,0,0,.06);}
        .logo{color:#93c21c;font-weight:bold;font-size:18px;margin-bottom:16px;}
        h1{font-size:20px;margin:0 0 12px;}
        p{font-size:15px;line-height:1.5;color:#4b5563;margin:0;}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <div class="logo">Solar Aspekt</div>
            @if(($reason ?? '') === 'ended')
                <h1>Dieser Video-Call ist beendet</h1>
                <p>Das Gespräch wurde bereits beendet. Bitte fordern Sie bei Bedarf einen neuen Einladungslink an.</p>
            @else
                <h1>Link nicht mehr gültig</h1>
                <p>Dieser Einladungslink ist abgelaufen oder ungültig. Bitte fordern Sie einen neuen Link an.</p>
            @endif
        </div>
    </div>
</body>
</html>
