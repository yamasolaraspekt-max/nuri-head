@extends('admin.layouts.app')

@section('title', 'Lieferanten-Schnittstellen')

@section('content')
<div class="sc-page">
    <style>
        .sc-page {
            padding: 24px;
            background: #f8fafc;
            min-height: calc(100vh - 80px);
        }

        .sc-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .sc-title {
            font-size: 26px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            letter-spacing: -0.03em;
        }

        .sc-subtitle {
            color: #6b7280;
            margin-top: 6px;
            font-size: 14px;
            line-height: 1.55;
            max-width: 920px;
        }

        .sc-toolbar {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .sc-guide {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .sc-guide-item {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 14px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .045);
        }

        .sc-guide-icon {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: rgba(116, 178, 212, .13);
            color: #16729f;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }

        .sc-guide-title {
            font-size: 13px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 4px;
        }

        .sc-guide-text {
            font-size: 12px;
            line-height: 1.45;
            color: #6b7280;
        }

        .sc-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .sc-filter {
            display: grid;
            grid-template-columns: 1fr 220px 180px 180px auto;
            gap: 12px;
            padding: 16px;
            border-bottom: 1px solid #e5e7eb;
            align-items: center;
            background: white;
        }

        .sc-input,
        .sc-select {
            border: 1px solid #d1d5db;
            border-radius: 14px;
            padding: 10px 12px;
            font-size: 14px;
            width: 100%;
            background: white;
            outline: none;
            transition: .18s ease;
        }

        .sc-input:focus,
        .sc-select:focus {
            border-color: #74b2d4;
            box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
        }

        .sc-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: none;
            border-radius: 14px;
            padding: 10px 14px;
            font-weight: 900;
            text-decoration: none;
            cursor: pointer;
            transition: .18s ease;
            font-size: 14px;
            white-space: nowrap;
        }

        .sc-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .sc-btn-primary {
            background: #74b2d4;
            color: white;
            box-shadow: 0 10px 22px rgba(116, 178, 212, .22);
        }

        .sc-btn-primary:hover {
            background: #5ea3ca;
            color: white;
        }

        .sc-btn-green {
            background: #93c21c;
            color: white;
            box-shadow: 0 10px 22px rgba(147, 194, 28, .20);
        }

        .sc-btn-green:hover {
            background: #7fa916;
            color: white;
        }

        .sc-btn-soft {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .sc-btn-soft:hover {
            background: #f9fafb;
            color: #111827;
        }

        .sc-btn-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .sc-btn-danger:hover {
            background: #fee2e2;
            color: #991b1b;
        }

        .sc-table-wrap {
            overflow-x: auto;
        }

        .sc-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
            min-width: 1180px;
        }

        .sc-table th {
            background: #f9fafb;
            color: #6b7280;
            text-align: left;
            padding: 12px 14px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .06em;
            white-space: nowrap;
        }

        .sc-table td {
            padding: 14px;
            border-top: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .sc-main-name {
            font-weight: 900;
            color: #111827;
            display: block;
            margin-bottom: 4px;
        }

        .sc-muted {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.45;
            word-break: break-all;
        }

        .sc-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .sc-badge-green {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .sc-badge-gray {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .sc-badge-blue {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .sc-badge-red {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .sc-actions {
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .sc-empty {
            padding: 48px 24px;
            text-align: center;
            color: #6b7280;
        }

        .sc-empty-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 14px;
            border-radius: 18px;
            background: rgba(116, 178, 212, .12);
            color: #16729f;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sc-empty-title {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            margin-bottom: 6px;
        }

        .sc-empty-text {
            max-width: 580px;
            margin: 0 auto 18px;
            font-size: 14px;
            line-height: 1.6;
        }

        .sc-footer {
            padding: 16px;
            border-top: 1px solid #e5e7eb;
            background: white;
        }

        .sc-toast {
            position: fixed;
            right: 24px;
            top: 24px;
            z-index: 99999;
            min-width: 320px;
            max-width: 460px;
            padding: 14px 16px;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
            font-weight: 900;
            line-height: 1.45;
            animation: scToastIn .25s ease;
        }

        .sc-toast-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .sc-toast-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        @keyframes scToastIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(.98);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 1100px) {
            .sc-guide {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .sc-filter {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 700px) {
            .sc-page {
                padding: 16px;
            }

            .sc-header {
                flex-direction: column;
                align-items: stretch;
            }

            .sc-toolbar {
                justify-content: stretch;
            }

            .sc-toolbar .sc-btn {
                width: 100%;
            }

            .sc-guide,
            .sc-filter {
                grid-template-columns: 1fr;
            }

            .sc-toast {
                left: 16px;
                right: 16px;
                top: 16px;
                min-width: unset;
            }
        }
    </style>

    @if(session('success'))
        <div class="sc-toast sc-toast-success" data-sc-toast>{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="sc-toast sc-toast-error" data-sc-toast>{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="sc-toast sc-toast-error" data-sc-toast>
            Bitte prüfe die Eingaben. Einige Felder sind nicht korrekt ausgefüllt.
        </div>
    @endif

    <div class="sc-header">
        <div>
            <h1 class="sc-title">Lieferanten-Schnittstellen</h1>
            <div class="sc-subtitle">
                Verwalte IDS, OCI, API, CSV, XML, BMEcat und DATANORM-Verbindungen zentral.
                Zugangsdaten, Parameter-Mapping, Suchfluss und Rückgabe-URL werden direkt in der Datenbank gespeichert.
            </div>
        </div>

        <div class="sc-toolbar">
            <a href="{{ route('admin.supplier-connectors.create') }}" class="sc-btn sc-btn-primary">
                <i data-lucide="plus-circle"></i>
                Neue Verbindung
            </a>
        </div>
    </div>

    <div class="sc-guide">
        <div class="sc-guide-item">
            <div class="sc-guide-icon"><i data-lucide="plug-zap"></i></div>
            <div class="sc-guide-title">1. Verbindung anlegen</div>
            <div class="sc-guide-text">Lieferant auswählen oder automatisch erstellen lassen.</div>
        </div>

        <div class="sc-guide-item">
            <div class="sc-guide-icon"><i data-lucide="key-round"></i></div>
            <div class="sc-guide-title">2. Zugangsdaten speichern</div>
            <div class="sc-guide-text">Benutzername, Passwort, Kundennummer, Token und Parameter-Mapping.</div>
        </div>

        <div class="sc-guide-item">
            <div class="sc-guide-icon"><i data-lucide="search"></i></div>
            <div class="sc-guide-title">3. Suchbegriff eingeben</div>
            <div class="sc-guide-text">Der Shop wird nicht leer geöffnet, sondern über eine Suchseite gestartet.</div>
        </div>

        <div class="sc-guide-item">
            <div class="sc-guide-icon"><i data-lucide="shopping-cart"></i></div>
            <div class="sc-guide-title">4. Warenkorb übernehmen</div>
            <div class="sc-guide-text">Der Lieferanten-Shop sendet den Warenkorb an die Rückgabe-URL.</div>
        </div>
    </div>

    <div class="sc-card">
        <form method="GET" class="sc-filter">
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="sc-input"
                   placeholder="Suche nach Name, Lieferant, Key oder Typ...">

            <select name="type" class="sc-select">
                <option value="">Alle Typen</option>
                @foreach([
                    'ids' => 'IDS Shop',
                    'oci' => 'OCI Shop',
                    'api' => 'API',
                    'csv' => 'CSV Datei',
                    'xml' => 'XML Datei',
                    'bmecat' => 'BMEcat',
                    'datanorm' => 'DATANORM',
                ] as $key => $label)
                    <option value="{{ $key }}" @selected(request('type') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="sc-select">
                <option value="">Alle Status</option>
                <option value="active" @selected(request('status') === 'active')>Aktiv</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inaktiv</option>
            </select>

            <select name="test_status" class="sc-select">
                <option value="">Alle Tests</option>
                <option value="success" @selected(request('test_status') === 'success')>Test erfolgreich</option>
                <option value="failed" @selected(request('test_status') === 'failed')>Test fehlgeschlagen</option>
            </select>

            <button class="sc-btn sc-btn-soft" type="submit">
                <i data-lucide="filter"></i>
                Filtern
            </button>
        </form>

        @if($connections->count())
            <div class="sc-table-wrap">
                <table class="sc-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Lieferant</th>
                            <th>Key</th>
                            <th>Typ</th>
                            <th>Status</th>
                            <th>Test</th>
                            <th>Mappings</th>
                            <th>Logs</th>
                            <th style="text-align:right;">Aktionen</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($connections as $connection)
                            <tr>
                                <td>
                                    <span class="sc-main-name">{{ $connection->name }}</span>
                                    <div class="sc-muted">
                                        {{ $connection->endpoint_url ?: 'Keine Shop-/Endpoint-Adresse eingetragen' }}
                                    </div>
                                </td>

                                <td>
                                    <strong>{{ $connection->distributor?->name ?? $connection->distributor?->short_name ?? '-' }}</strong>
                                    <div class="sc-muted">
                                        {{ $connection->customer_number ? 'Kundennr. gespeichert' : 'Keine Kundennr.' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="sc-badge sc-badge-gray">{{ $connection->supplier_key }}</span>
                                </td>

                                <td>
                                    <span class="sc-badge sc-badge-blue">{{ $connection->type_label }}</span>
                                </td>

                                <td>
                                    @if($connection->is_active)
                                        <span class="sc-badge sc-badge-green">Aktiv</span>
                                    @else
                                        <span class="sc-badge sc-badge-gray">Inaktiv</span>
                                    @endif
                                </td>

                                <td>
                                    @if($connection->last_test_status === 'success')
                                        <span class="sc-badge sc-badge-green" title="{{ $connection->last_test_message }}">Test ok</span>
                                    @elseif($connection->last_test_status === 'failed')
                                        <span class="sc-badge sc-badge-red" title="{{ $connection->last_test_message }}">Fehler</span>
                                    @else
                                        <span class="sc-badge sc-badge-gray">Nicht getestet</span>
                                    @endif

                                    @if($connection->last_tested_at)
                                        <div class="sc-muted">{{ $connection->last_tested_at->format('d.m.Y H:i') }}</div>
                                    @endif
                                </td>

                                <td><span class="sc-badge sc-badge-gray">{{ $connection->mappings_count }}</span></td>
                                <td><span class="sc-badge sc-badge-gray">{{ $connection->logs_count }}</span></td>

                                <td>
                                    <div class="sc-actions">
                                        <form method="POST" action="{{ route('admin.supplier-connectors.test', $connection) }}">
                                            @csrf
                                            <button class="sc-btn sc-btn-soft" type="submit">
                                                <i data-lucide="activity"></i>
                                                Test
                                            </button>
                                        </form>

                                        @if(in_array($connection->connector_type, ['ids', 'oci']))
                                            <a href="{{ route('admin.supplier-connectors.open', $connection) }}"
                                               class="sc-btn sc-btn-green">
                                                <i data-lucide="search"></i>
                                                Suche öffnen
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.supplier-connectors.edit', $connection) }}"
                                           class="sc-btn sc-btn-soft">
                                            <i data-lucide="pencil"></i>
                                            Bearbeiten
                                        </a>

                                        <form method="POST" action="{{ route('admin.supplier-connectors.duplicate', $connection) }}">
                                            @csrf
                                            <button class="sc-btn sc-btn-soft" type="submit">
                                                <i data-lucide="copy"></i>
                                                Kopieren
                                            </button>
                                        </form>

                                        <form method="POST"
                                              action="{{ route('admin.supplier-connectors.destroy', $connection) }}"
                                              onsubmit="return confirm('Diese Verbindung wirklich löschen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="sc-btn sc-btn-danger" type="submit">
                                                <i data-lucide="trash-2"></i>
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="sc-footer">
                {{ $connections->links() }}
            </div>
        @else
            <div class="sc-empty">
                <div class="sc-empty-icon"><i data-lucide="plug-zap"></i></div>
                <div class="sc-empty-title">Noch keine Lieferanten-Schnittstelle erstellt</div>
                <div class="sc-empty-text">
                    Erstelle deine erste Verbindung, z.B. für GC Online Plus. Danach kannst du Zugangsdaten speichern,
                    die Verbindung testen und eine Artikelsuche im Lieferanten-Shop starten.
                </div>

                <a href="{{ route('admin.supplier-connectors.create') }}" class="sc-btn sc-btn-primary">
                    <i data-lucide="plus-circle"></i>
                    Erste Verbindung erstellen
                </a>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-sc-toast]').forEach(function (toast) {
            setTimeout(function () {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px) scale(.98)';
                toast.style.transition = 'all .25s ease';

                setTimeout(function () {
                    toast.remove();
                }, 300);
            }, 4500);
        });

        if (window.lucide) {
            window.lucide.createIcons();
        }
    });
</script>
@endsection