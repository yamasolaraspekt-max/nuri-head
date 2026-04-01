<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>Zurück zu SA-DESK…</title>
    <style>
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f9fafb;
            color: #4b5563;
            padding: 24px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>

<p>Die Artikelauswahl ist abgeschlossen. Du kannst dieses Fenster schließen.</p>

<script>
    // Try to notify the parent window that the GC session is done.
    try {
        if (window.top && window.top !== window) {
            window.top.postMessage(
                { type: "GC_IDS_DONE" },
                window.location.origin // only same-origin parent
            );
        }
    } catch (e) {
        console.warn("postMessage to parent failed:", e);
    }
</script>

</body>
</html>
