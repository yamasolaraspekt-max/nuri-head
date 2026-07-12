<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Angebotsreife — Vorgang #{{ $reife['lead_product_list_id'] ?? '?' }}</title>
</head>
<body style="margin:0;padding:28px;background:#f3f4f6;">
    <div style="max-width:760px;margin:0 auto;">
        <h1 style="font-family:system-ui,Arial,sans-serif;font-size:18px;color:#111827;margin:0 0 16px;">
            Angebotsreife (Paket 1 · read-only)
        </h1>
        @include('admin.offer.partials.angebotsreife_panel', ['reife' => $reife])
    </div>
</body>
</html>
