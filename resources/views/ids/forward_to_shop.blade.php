<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Weiterleitung zum Großhandel…</title>

    {{-- Auto-submit --}}
    <script>
        window.onload = function () {
            document.getElementById('gcForm').submit();
        };
    </script>

    <style>
        body {
            background: #f7fafc;
            font-family: system-ui, Arial, sans-serif;
            padding: 60px 20px;
            text-align: center;
            color: #4a5568;
        }
        .ids-box {
            display: inline-block;
            padding: 32px 40px;
            background: white;
            border-radius: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }
        .ids-box h2 {
            margin: 0 0 8px;
            font-size: 22px;
            color: #1a202c;
        }
        .ids-box p {
            margin-top: 6px;
            font-size: 14px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

<div class="ids-box">
    <h2>Weiterleitung zum Großhandel…</h2>
    <p>
        Bitte warten. Du wirst automatisch weitergeleitet.<br>
        Schließe im Großhandel die Artikelauswahl ab und du wirst wieder hierher zurückgeführt.
    </p>
</div>

{{-- FORM → Auto-post to GC Online --}}
<form id="gcForm" action="{{ $shopUrl }}" method="POST">

    {{-- ACTION REQUIRED BY GC --}}
    <input type="hidden" name="action" value="AS">
    <input type="hidden" name="version" value="2.5">
    <input type="hidden" name="target" value="TOP">

    {{-- LOGIN --}}
    <input type="hidden" name="kndnr" value="{{ $credentials['kndnr'] }}">
    <input type="hidden" name="name_kunde" value="{{ $credentials['name_kunde'] }}">
    <input type="hidden" name="pw_kunde" value="{{ $credentials['pw_kunde'] }}">

    {{-- SEARCH --}}
    <input type="hidden" name="searchterm" value="{{ $searchterm }}">

    {{-- SERVER CALLBACK (XML) --}}
    <input type="hidden" name="hookurl" value="{{ route('ids.callback') }}">

    {{-- AFTER FINISH USER RETURNS HERE --}}
    <input type="hidden" name="rueckurl" value="{{ route('ids.search.form') }}">
</form>


</body>
</html>
