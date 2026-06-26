@extends('admin.layouts.app')

@section('title', 'Lieferantenartikel übernommen')

@section('content')
    <div style="min-height:calc(100vh - 80px);background:#f8fafc;padding:28px;">
        <div style="max-width:760px;margin:0 auto;background:white;border:1px solid #e5e7eb;border-radius:22px;padding:24px;box-shadow:0 14px 35px rgba(15,23,42,.08);">
            <div style="width:56px;height:56px;border-radius:18px;background:#ecfdf5;color:#047857;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <i data-lucide="check-circle"></i>
            </div>

            <h1 style="font-size:24px;font-weight:900;color:#111827;margin:0 0 8px;">
                Artikel gespeichert und ins Angebot eingefügt
            </h1>

            <p style="color:#6b7280;font-size:14px;line-height:1.6;margin:0 0 16px;">
                Verbindung: <strong>{{ $connection->name }}</strong><br>
                Log #{{ $log->id }} · Status: <strong>{{ $log->status }}</strong><br>
                {{ $log->message }}
            </p>

            @if(count($items))
                <div style="background:#ecfdf5;border:1px solid #bbf7d0;color:#047857;border-radius:16px;padding:12px 14px;font-weight:800;margin-bottom:16px;">
                    {{ count($items) }} Artikel wurden per Reverb an das offene Angebot gesendet.
                </div>
            @else
                <div style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;border-radius:16px;padding:12px 14px;font-weight:800;margin-bottom:16px;">
                    Keine Artikel wurden eingefügt.
                </div>
            @endif

            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a href="{{ route('admin.offers.folders.show', $folder) }}"
                   style="background:#93c21c;color:white;border-radius:14px;padding:11px 15px;font-weight:900;text-decoration:none;">
                    Zum Angebot
                </a>

                <button type="button"
                        onclick="window.close()"
                        style="background:white;color:#374151;border:1px solid #e5e7eb;border-radius:14px;padding:11px 15px;font-weight:900;cursor:pointer;">
                    Tab schließen
                </button>
            </div>
        </div>
    </div>

    <script>
    (function () {
        const payload = {
            type: 'offer_supplier_import_done',
            folder_id: {{ (int) $folder->id }},
            log_id: {{ (int) $log->id }},
            target_section_index: @json($targetSectionIndex),
            items: @json($items),
            message: @json(count($items) . ' Artikel wurden übernommen.')
        };

        try {
            localStorage.setItem('offer_supplier_import_{{ (int) $folder->id }}', JSON.stringify(payload));
        } catch (e) {}

        try {
            if (window.opener) {
                window.opener.postMessage(payload, window.location.origin);
            }
        } catch (e) {}

        if (window.lucide) {
            window.lucide.createIcons();
        }
    })();
    </script>
@endsection
