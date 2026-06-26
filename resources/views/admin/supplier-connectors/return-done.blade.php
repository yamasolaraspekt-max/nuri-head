<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Rückgabe verarbeitet</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            background: #f8fafc;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            padding: 42px 18px;
            color: #374151;
            margin: 0;
        }

        .ids-return-shell {
            max-width: 980px;
            margin: 0 auto;
        }

        .ids-return-box {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            padding: 24px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
        }

        .ids-return-head {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .ids-return-title {
            color: #111827;
            font-size: 25px;
            font-weight: 900;
            margin: 0 0 7px;
            letter-spacing: -0.03em;
        }

        .ids-return-subtitle {
            color: #6b7280;
            font-size: 14px;
            line-height: 1.55;
        }

        .ids-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .ids-status-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .ids-status-failed {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .ids-status-received,
        .ids-status-pending {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .ids-summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin: 18px 0;
        }

        .ids-summary-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px;
        }

        .ids-summary-label {
            color: #6b7280;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 6px;
        }

        .ids-summary-value {
            color: #111827;
            font-size: 20px;
            font-weight: 900;
        }

        .ids-message {
            border-radius: 16px;
            padding: 13px 15px;
            font-size: 14px;
            font-weight: 800;
            line-height: 1.55;
            margin-bottom: 18px;
        }

        .ids-message-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .ids-message-failed {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .ids-message-received,
        .ids-message-pending {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .ids-section {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
        }

        .ids-section-title {
            margin: 0 0 10px;
            font-size: 17px;
            font-weight: 900;
            color: #111827;
        }

        .ids-section-subtitle {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.55;
            margin-bottom: 14px;
        }

        .ids-product-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .ids-product-item {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 12px;
        }

        .ids-product-title {
            font-size: 14px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 4px;
        }

        .ids-product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: #6b7280;
        }

        .ids-btn {
            border: none;
            border-radius: 13px;
            padding: 9px 12px;
            font-size: 13px;
            font-weight: 900;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            white-space: nowrap;
        }

        .ids-btn-primary {
            background: #74b2d4;
            color: white;
        }

        .ids-btn-green {
            background: #93c21c;
            color: white;
        }

        .ids-btn-soft {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .ids-empty {
            border: 1px dashed #d1d5db;
            border-radius: 16px;
            padding: 18px;
            background: #fafafa;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.55;
        }

        .ids-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }

        details {
            margin-top: 14px;
            text-align: left;
        }

        summary {
            cursor: pointer;
            color: #374151;
            font-size: 13px;
            font-weight: 900;
        }

        pre {
            margin-top: 10px;
            background: #0f172a;
            color: #e5e7eb;
            border-radius: 14px;
            padding: 14px;
            white-space: pre-wrap;
            overflow: auto;
            font-size: 12px;
            line-height: 1.5;
            max-height: 360px;
        }

        .ids-note {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.55;
            margin-top: 16px;
        }

        @media(max-width: 760px) {
            .ids-return-head {
                flex-direction: column;
            }

            .ids-summary-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .ids-product-item {
                flex-direction: column;
                align-items: stretch;
            }

            .ids-btn {
                width: 100%;
            }
        }

        @media(max-width: 480px) {
            .ids-summary-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    @php
        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $savedProducts = $payload['saved_products'] ?? $payload['products'] ?? $payload['imported_products'] ?? [];
        $errors = $payload['errors'] ?? [];

        $statusClass = match ($log->status) {
            'success' => 'ids-status-success',
            'failed' => 'ids-status-failed',
            'received' => 'ids-status-received',
            'pending' => 'ids-status-pending',
            default => 'ids-status-pending',
        };

        $messageClass = match ($log->status) {
            'success' => 'ids-message-success',
            'failed' => 'ids-message-failed',
            'received' => 'ids-message-received',
            'pending' => 'ids-message-pending',
            default => 'ids-message-pending',
        };
    @endphp

    <div class="ids-return-shell">
        <div class="ids-return-box">
            <div class="ids-return-head">
                <div>
                    <h1 class="ids-return-title">Rückgabe verarbeitet</h1>
                    <div class="ids-return-subtitle">
                        Lieferant: <strong>{{ $connection->name }}</strong><br>
                        Log #{{ $log->id }} · {{ optional($log->created_at)->format('d.m.Y H:i:s') }}
                    </div>
                </div>

                <span class="ids-status-badge {{ $statusClass }}">
                    {{ strtoupper($log->status) }}
                </span>
            </div>

            <div class="ids-message {{ $messageClass }}">
                {{ $log->message ?: 'Die Rückgabe wurde empfangen.' }}
            </div>

            <div class="ids-summary-grid">
                <div class="ids-summary-card">
                    <div class="ids-summary-label">Gesamt</div>
                    <div class="ids-summary-value">{{ $log->total_items }}</div>
                </div>

                <div class="ids-summary-card">
                    <div class="ids-summary-label">Erfolgreich</div>
                    <div class="ids-summary-value">{{ $log->success_items }}</div>
                </div>

                <div class="ids-summary-card">
                    <div class="ids-summary-label">Fehler</div>
                    <div class="ids-summary-value">{{ $log->failed_items }}</div>
                </div>

                <div class="ids-summary-card">
                    <div class="ids-summary-label">Quelle</div>
                    <div class="ids-summary-value" style="font-size:14px;">
                        {{ $log->source_type ?: '-' }}
                    </div>
                </div>
            </div>

            <div class="ids-section">
                <h2 class="ids-section-title">Gespeicherte Produkte</h2>
                <div class="ids-section-subtitle">
                    Wenn Produkte erfolgreich importiert wurden, kannst du sie direkt öffnen.
                </div>

                @if(!empty($savedProducts) && is_array($savedProducts))
                    <div class="ids-product-list">
                        @foreach($savedProducts as $savedProduct)
                            @php
                                $productId = $savedProduct['id']
                                    ?? $savedProduct['product_id']
                                    ?? null;

                                $productTitle = $savedProduct['product']
                                    ?? $savedProduct['title']
                                    ?? $savedProduct['name']
                                    ?? ('Produkt #' . $productId);

                                $articleNo = $savedProduct['article_no']
                                    ?? $savedProduct['distributor_article_no']
                                    ?? null;

                                $price = $savedProduct['price']
                                    ?? $savedProduct['purchase_price']
                                    ?? null;
                            @endphp

                            <div class="ids-product-item">
                                <div>
                                    <div class="ids-product-title">
                                        {{ $productTitle }}
                                    </div>

                                    <div class="ids-product-meta">
                                        @if($productId)
                                            <span>Produkt-ID: {{ $productId }}</span>
                                        @endif

                                        @if($articleNo)
                                            <span>Artikelnummer: {{ $articleNo }}</span>
                                        @endif

                                        @if($price)
                                            <span>Preis: {{ $price }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($productId)
                                    <a href="{{ url('product_details/' . $productId) }}" target="_blank"
                                        class="ids-btn ids-btn-primary">
                                        Produkt öffnen
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="ids-empty">
                        Noch keine Produkte gespeichert.
                        Falls der Status <strong>received</strong> ist, öffne die Vorschau und starte den Import manuell.
                    </div>
                @endif
            </div>

            <div class="ids-actions">
                @if(Route::has('admin.supplier-connectors.logs.preview'))
                    <a href="{{ route('admin.supplier-connectors.logs.preview', [$connection, $log]) }}" target="_blank"
                        class="ids-btn ids-btn-green">
                        Daten prüfen / importieren
                    </a>
                @endif

                <a href="{{ route('admin.supplier-connectors.search', $connection) }}" target="_blank"
                    class="ids-btn ids-btn-soft">
                    Suchseite öffnen
                </a>

                <button type="button" onclick="window.close();" class="ids-btn ids-btn-soft">
                    Fenster schließen
                </button>
            </div>

            @if(!empty($errors))
                <div class="ids-section">
                    <h2 class="ids-section-title" style="color:#b91c1c;">Fehlerdetails</h2>

                    <details open>
                        <summary>Fehler anzeigen</summary>
                        <pre>{{ json_encode($errors, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                    </details>
                </div>
            @endif

            <details>
                <summary>Raw Rückgabe anzeigen</summary>
                <pre>{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </details>

            <p class="ids-note">
                Du kannst dieses Fenster schließen. Die Laravel-Suchseite aktualisiert sich automatisch.
            </p>
        </div>
    </div>
</body>

</html>