@extends('admin.layouts.app')

@section('title', 'Weiterleitung zum Lieferanten')

@section('content')
    @php
        $requestConfig = is_array($requestConfig ?? null)
            ? $requestConfig
            : (is_array($connection->request_config ?? null) ? $connection->request_config : []);

        $contentType = strtolower((string) data_get($requestConfig, 'content_type', 'form'));

        $method = strtoupper((string) data_get($requestConfig, 'method', 'POST')) === 'GET'
            ? 'GET'
            : 'POST';

        $enctype = $contentType === 'multipart'
            ? 'multipart/form-data'
            : 'application/x-www-form-urlencoded';

        $supplierKey = strtolower((string) ($connection->supplier_key ?? ''));
        $connectionName = strtolower((string) ($connection->name ?? ''));
        $endpointUrl = strtolower((string) ($connection->endpoint_url ?? ''));

        $isSonepar = str_contains($supplierKey, 'sonepar')
            || str_contains($connectionName, 'sonepar')
            || str_contains($endpointUrl, 'sonepar.de');

        $hiddenInputs = [];

        $pushHiddenInput = function ($key, $value) use (&$hiddenInputs, &$pushHiddenInput) {
            if ($value === null || $value === '') {
                return;
            }

            if (is_array($value)) {
                foreach ($value as $subKey => $subValue) {
                    if ($subValue === null || $subValue === '') {
                        continue;
                    }

                    if (is_array($subValue)) {
                        $pushHiddenInput($key . '[' . $subKey . ']', $subValue);
                    } else {
                        $hiddenInputs[] = [
                            'name' => is_numeric($subKey)
                                ? $key . '[]'
                                : $key . '[' . $subKey . ']',
                            'value' => $subValue,
                        ];
                    }
                }

                return;
            }

            $hiddenInputs[] = [
                'name' => (string) $key,
                'value' => $value,
            ];
        };

        foreach (($params ?? []) as $key => $value) {
            if (
                $isSonepar && in_array((string) $key, [
                    'searchterm',
                    'SEARCHTERM',
                    'query',
                    'search',
                    'rueckurl',
                    'RUECKURL',
                    'link',
                    'LINK',
                ], true)
            ) {
                continue;
            }

            $pushHiddenInput((string) $key, $value);
        }

        $displayValue = function ($value) {
            if (is_array($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            }

            if ($value === null) {
                return '';
            }

            return (string) $value;
        };

        $isSensitiveKey = function ($key) {
            $lowerKey = strtolower((string) $key);

            return $lowerKey === 'pw_kunde'
                || str_contains($lowerKey, 'pass')
                || str_contains($lowerKey, 'password')
                || str_contains($lowerKey, 'pw')
                || str_contains($lowerKey, 'token');
        };
    @endphp

    <div class="ids-forward-page">
        <style>
            .ids-forward-page {
                padding: 30px;
                background: #f8fafc;
                min-height: calc(100vh - 80px);
            }

            .ids-forward-card {
                max-width: 820px;
                margin: 0 auto;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 22px;
                padding: 24px;
                box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
                text-align: center;
            }

            .ids-forward-icon {
                width: 54px;
                height: 54px;
                border-radius: 18px;
                background: rgba(147, 194, 28, .13);
                color: #5f7f12;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 14px;
            }

            .ids-forward-title {
                font-size: 24px;
                font-weight: 900;
                color: #111827;
                margin: 0 0 8px;
                letter-spacing: -0.03em;
            }

            .ids-forward-text {
                color: #6b7280;
                font-size: 14px;
                line-height: 1.6;
                margin-bottom: 20px;
            }

            .ids-forward-actions {
                display: flex;
                gap: 10px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .ids-btn {
                border: none;
                border-radius: 14px;
                padding: 11px 15px;
                font-weight: 900;
                cursor: pointer;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                white-space: nowrap;
                font-size: 14px;
            }

            .ids-btn-green {
                background: #93c21c;
                color: white;
            }

            .ids-btn-green:hover {
                background: #7fa916;
                color: white;
                text-decoration: none;
            }

            .ids-btn-soft {
                background: white;
                color: #374151;
                border: 1px solid #e5e7eb;
            }

            .ids-btn-soft:hover {
                background: #f9fafb;
                color: #111827;
                text-decoration: none;
            }

            .ids-status-box {
                margin: 18px 0;
                padding: 12px 14px;
                border-radius: 16px;
                background: #ecfdf5;
                border: 1px solid #bbf7d0;
                color: #047857;
                font-size: 13px;
                font-weight: 900;
                line-height: 1.5;
                text-align: left;
            }

            .ids-warning {
                margin-top: 14px;
                padding: 10px 12px;
                border-radius: 14px;
                background: #fffbeb;
                border: 1px solid #fde68a;
                color: #92400e;
                font-size: 12px;
                line-height: 1.55;
                text-align: left;
            }

            .ids-param-box {
                margin-top: 20px;
                text-align: left;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                overflow: hidden;
                background: white;
            }

            .ids-param-summary {
                cursor: pointer;
                color: #6b7280;
                font-size: 13px;
                font-weight: 900;
                padding: 12px 14px;
                background: #f9fafb;
            }

            .ids-param-table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            .ids-param-table th {
                text-align: left;
                padding: 8px;
                border-bottom: 1px solid #e5e7eb;
                color: #6b7280;
                background: #f9fafb;
            }

            .ids-param-table td {
                padding: 8px;
                border-bottom: 1px solid #f1f5f9;
                vertical-align: top;
            }

            .ids-param-key {
                font-weight: 900;
                color: #111827;
                white-space: nowrap;
            }

            .ids-param-value {
                max-width: 560px;
                word-break: break-word;
                white-space: pre-wrap;
            }

            .ids-param-empty {
                color: #b91c1c;
                font-weight: 900;
            }

            .ids-param-filled {
                color: #047857;
                font-weight: 900;
            }

            .ids-param-muted {
                color: #64748b;
                font-weight: 800;
            }

            .ids-help {
                margin-top: 16px;
                color: #6b7280;
                font-size: 12px;
                line-height: 1.55;
            }

            @media(max-width: 700px) {
                .ids-forward-page {
                    padding: 18px;
                }

                .ids-btn {
                    width: 100%;
                }

                .ids-param-table {
                    font-size: 11px;
                }
            }
        </style>

        <div class="ids-forward-card">
            <div class="ids-forward-icon">
                <i data-lucide="external-link"></i>
            </div>

            <h1 class="ids-forward-title">
                Weiterleitung zu {{ $connection->name }} …
            </h1>

            <p class="ids-forward-text">
                Bitte warten. Der Lieferanten-Shop wird mit den gespeicherten Zugangsdaten im Browser geöffnet.
            </p>

            <div class="ids-status-box">
                Methode: <strong>{{ $method }}</strong> ·
                Encoding: <strong>{{ $enctype }}</strong> ·
                Ziel: <strong>{{ $shopUrl }}</strong>
            </div>

            <form id="supplierForwardForm" method="{{ $method }}" action="{{ $shopUrl }}" enctype="{{ $enctype }}">

                @foreach($hiddenInputs as $input)
                    <input type="hidden" name="{{ $input['name'] }}"
                        value="{{ is_scalar($input['value']) || is_null($input['value']) ? (string) $input['value'] : json_encode($input['value']) }}">
                @endforeach

                <div class="ids-forward-actions">
                    <button type="submit" class="ids-btn ids-btn-green">
                        Jetzt öffnen
                    </button>

                    @if($isSonepar)
                        <a href="{{ route('admin.supplier-connectors.edit', $connection) }}" class="ids-btn ids-btn-soft">
                            Zurück zur Verbindung
                        </a>
                    @else
                        <a href="{{ route('admin.supplier-connectors.search', $connection) }}" class="ids-btn ids-btn-soft">
                            Zurück zur Suche
                        </a>
                    @endif
                </div>
            </form>

            @if($isSonepar && $enctype !== 'multipart/form-data')
                <div class="ids-warning">
                    Sonepar wurde erkannt, aber das Formular sendet nicht als <strong>multipart/form-data</strong>.
                    Prüfe <strong>request_config.content_type = multipart</strong>.
                </div>
            @endif

            <details class="ids-param-box" open>
                <summary class="ids-param-summary">
                    Gesendete Parameter prüfen
                </summary>

                <table class="ids-param-table">
                    <thead>
                        <tr>
                            <th>Parameter</th>
                            <th>Status / Wert</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($hiddenInputs as $input)
                            @php
                                $key = $input['name'];
                                $value = $input['value'];
                                $sensitive = $isSensitiveKey($key);
                                $shownValue = $displayValue($value);
                            @endphp

                            <tr>
                                <td class="ids-param-key">
                                    {{ $key }}
                                </td>

                                <td class="ids-param-value">
                                    @if($sensitive)
                                        @if(filled($shownValue))
                                            <span class="ids-param-filled">gespeichert</span>
                                        @else
                                            <span class="ids-param-empty">LEER</span>
                                        @endif
                                    @else
                                        @if(filled($shownValue))
                                            {{ $shownValue }}
                                        @else
                                            <span class="ids-param-empty">LEER</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach

                        @if(empty($hiddenInputs))
                            <tr>
                                <td colspan="2">
                                    <span class="ids-param-empty">Keine Parameter vorhanden.</span>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </details>

            <div class="ids-help">
                Für Sonepar muss hier stehen:
                <strong>POST</strong>,
                <strong>multipart/form-data</strong>,
                <strong>OrganizationId</strong>,
                <strong>action</strong>,
                <strong>Version</strong>,
                <strong>target</strong>,
                <strong>kndnr</strong>,
                <strong>name_kunde</strong>,
                <strong>pw_kunde</strong>,
                <strong>hookurl</strong>.
                Es darf kein <strong>searchterm</strong> gesendet werden.
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                document.getElementById('supplierForwardForm')?.submit();
            }, 650);

            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
@endsection