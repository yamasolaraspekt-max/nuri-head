@extends('admin.layouts.app')

@section('title', $connection->name . ' Suche')

@section('content')
    <style>
        .ids-search-page {
            padding: 32px 24px 48px;
            background: #f8fafc;
            min-height: calc(100vh - 80px);
        }

        .ids-search-shell {
            max-width: 1120px;
            margin: 0 auto;
        }

        .ids-header {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .ids-title {
            font-size: 26px;
            font-weight: 900;
            color: #111827;
            margin: 0;
            letter-spacing: -0.03em;
        }

        .ids-subtitle {
            margin-top: 6px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.55;
            max-width: 760px;
        }

        .ids-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 20px;
            box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
            padding: 20px;
            margin-bottom: 18px;
        }

        .ids-card-title {
            font-size: 18px;
            font-weight: 900;
            color: #111827;
            margin: 0 0 8px;
        }

        .ids-card-subtitle {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.55;
            margin-bottom: 16px;
        }

        .ids-input-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .ids-input {
            flex: 1 1 280px;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            font-size: 15px;
            outline: none;
            transition: .18s ease;
            background: white;
        }

        .ids-input:focus {
            border-color: #74b2d4;
            box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
        }

        .ids-btn {
            border: none;
            border-radius: 14px;
            padding: 12px 16px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            white-space: nowrap;
            transition: .18s ease;
            font-size: 14px;
        }

        .ids-btn:hover {
            transform: translateY(-1px);
            text-decoration: none;
        }

        .ids-btn-primary {
            background: #74b2d4;
            color: white;
            box-shadow: 0 10px 22px rgba(116, 178, 212, .22);
        }

        .ids-btn-primary:hover {
            background: #5ea3ca;
            color: white;
        }

        .ids-btn-green {
            background: #93c21c;
            color: white;
            box-shadow: 0 10px 22px rgba(147, 194, 28, .22);
        }

        .ids-btn-green:hover {
            background: #7fa91a;
            color: white;
        }

        .ids-btn-soft {
            background: white;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .ids-btn-soft:hover {
            background: #f9fafb;
            color: #111827;
        }

        .ids-note {
            margin-top: 12px;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.55;
        }

        .ids-options {
            margin-top: 12px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            color: #374151;
            font-size: 13px;
            font-weight: 800;
        }

        .ids-info {
            background: rgba(116, 178, 212, .10);
            border: 1px solid rgba(116, 178, 212, .25);
            color: #075985;
            border-radius: 16px;
            padding: 12px 14px;
            font-size: 13px;
            font-weight: 800;
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .ids-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            padding: 6px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            margin-bottom: 16px;
        }

        .ids-tab-btn {
            border: none;
            background: transparent;
            color: #6b7280;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 950;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: .18s ease;
        }

        .ids-tab-btn:hover {
            background: #fff;
            color: #111827;
        }

        .ids-tab-btn.active {
            background: #fff;
            color: #111827;
            box-shadow: 0 8px 18px rgba(15, 23, 42, .07);
        }

        .ids-tab-badge {
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            background: #e5e7eb;
            color: #374151;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 7px;
            font-size: 11px;
            font-weight: 950;
        }

        .ids-tab-btn.active .ids-tab-badge {
            background: rgba(116, 178, 212, .16);
            color: #075985;
        }

        .ids-tab-panel {
            display: none;
        }

        .ids-tab-panel.active {
            display: block;
        }

        .ids-live-header,
        .ids-saved-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 14px;
        }

        .ids-live-status {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            border-radius: 999px;
            padding: 6px 10px;
            background: #f3f4f6;
            color: #374151;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .ids-live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #93c21c;
            display: inline-block;
            animation: idsPulse 1.4s infinite;
        }

        @keyframes idsPulse {
            0% {
                opacity: .35;
                transform: scale(.9);
            }

            50% {
                opacity: 1;
                transform: scale(1.1);
            }

            100% {
                opacity: .35;
                transform: scale(.9);
            }
        }

        .ids-log-empty,
        .ids-saved-empty {
            border: 1px dashed #d1d5db;
            border-radius: 16px;
            padding: 22px;
            text-align: center;
            color: #6b7280;
            background: #fafafa;
            font-size: 14px;
            line-height: 1.55;
        }

        .ids-log-item {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px;
            margin-bottom: 10px;
            background: white;
        }

        .ids-log-top {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .ids-log-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 900;
        }

        .ids-log-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .ids-log-failed {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .ids-log-pending {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb;
        }

        .ids-log-date {
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
        }

        .ids-log-message {
            margin-top: 8px;
            color: #374151;
            font-size: 14px;
            line-height: 1.55;
        }

        .ids-log-meta {
            margin-top: 8px;
            color: #6b7280;
            font-size: 12px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .ids-log-actions {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ids-saved-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-top: 12px;
        }

        .ids-saved-item {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: center;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 14px;
            background: white;
        }

        .ids-saved-title {
            color: #111827;
            font-size: 14px;
            font-weight: 950;
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .ids-saved-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            color: #6b7280;
            font-size: 12px;
            font-weight: 800;
        }

        .ids-saved-pill {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #374151;
            padding: 4px 8px;
            border-radius: 999px;
        }

        .ids-saved-status {
            margin-top: 10px;
            color: #6b7280;
            font-size: 13px;
            font-weight: 800;
        }

        .ids-toast {
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
            animation: idsToastIn .25s ease;
        }

        .ids-toast-success {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #bbf7d0;
        }

        .ids-toast-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        @keyframes idsToastIn {
            from {
                opacity: 0;
                transform: translateY(-8px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media(max-width: 700px) {
            .ids-search-page {
                padding: 20px 14px 36px;
            }

            .ids-header,
            .ids-live-header,
            .ids-saved-header {
                flex-direction: column;
            }

            .ids-btn,
            .ids-input {
                width: 100%;
            }

            .ids-saved-item {
                flex-direction: column;
                align-items: stretch;
            }

            .ids-tab-btn {
                width: 100%;
                justify-content: space-between;
            }

            .ids-toast {
                left: 16px;
                right: 16px;
                top: 16px;
                min-width: unset;
            }
        }
    </style>

    <div class="ids-search-page">
        @if(session('success'))
            <div class="ids-toast ids-toast-success" data-ids-toast>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="ids-toast ids-toast-error" data-ids-toast>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="ids-toast ids-toast-error" data-ids-toast>
                Bitte gib einen Suchbegriff ein.
            </div>
        @endif

        <div class="ids-search-shell">
            <div class="ids-header">
                <div>
                    <h1 class="ids-title">{{ $connection->name }} Artikelsuche</h1>
                    <div class="ids-subtitle">
                        Gib einen Suchbegriff ein. Der Lieferanten-Shop öffnet in einem neuen Tab.
                        Diese Seite bleibt offen und zeigt Rückgabe, gespeicherte Artikel und Importstatus automatisch an.
                    </div>
                </div>

                <a href="{{ route('admin.supplier-connectors.edit', $connection) }}" class="ids-btn ids-btn-soft">
                    Zurück
                </a>
            </div>

            <div class="ids-card">
                <h2 class="ids-card-title">Shop-Suche starten</h2>
                <div class="ids-card-subtitle">
                    Der Suchbegriff wird zusammen mit den gespeicherten Zugangsdaten an den Lieferanten-Shop gesendet.
                </div>

                <div class="ids-info">
                    Für GC Online werden z.B. <strong>action=AS</strong>, <strong>version=2.5</strong>,
                    <strong>target=TOP</strong>, <strong>searchterm</strong>, <strong>hookurl</strong> und
                    <strong>rueckurl</strong> über das Login-Parameter-Mapping gesendet.
                </div>

                <form method="POST" action="{{ route('admin.supplier-connectors.forward', $connection) }}" target="_blank"
                    id="idsSupplierSearchForm">
                    @csrf

                    <div class="ids-input-row">
                        <input type="text" name="query" required autofocus class="ids-input"
                            placeholder="z.B. Waschtisch · Armatur · Artikelnummer">

                        <button type="submit" class="ids-btn ids-btn-primary">
                            In {{ $connection->name }} suchen
                        </button>
                    </div>

                    <div class="ids-options">
                        <label style="display:inline-flex;align-items:center;gap:7px;">
                            <input type="checkbox" name="auto" value="1">
                            Ergebnisse automatisch als Produkte + Lieferantenpreise anlegen
                        </label>
                    </div>

                    <div class="ids-note">
                        Der Lieferanten-Shop öffnet in einem neuen Tab. Nach der Artikelauswahl sendet der Shop den
                        Warenkorb
                        an die Rückgabe-Adresse dieser Verbindung. Diese Seite aktualisiert die Import-Logs automatisch.
                    </div>
                </form>
            </div>

            <div class="ids-card">
                <div class="ids-tabs">
                    <button type="button" class="ids-tab-btn active" data-ids-tab="saved">
                        Gespeicherte Produkte
                        <span class="ids-tab-badge" id="idsSavedCount">0</span>
                    </button>

                    <button type="button" class="ids-tab-btn" data-ids-tab="activity">
                        Aktivität / Rückgabe
                        <span class="ids-tab-badge" id="idsActivityCount">0</span>
                    </button>
                </div>

                <div class="ids-tab-panel active" id="idsTabSaved">
                    <div class="ids-saved-header">
                        <div>
                            <h2 class="ids-card-title">Gespeicherte IDS-Artikel</h2>
                            <div class="ids-card-subtitle">
                                Hier siehst du, welche Artikel über diese IDS-Verbindung gespeichert wurden.
                            </div>
                        </div>

                        <button type="button" class="ids-btn ids-btn-soft" onclick="loadIdsData(true)">
                            Aktualisieren
                        </button>
                    </div>

                    <div id="idsSavedArticlesStatus" class="ids-saved-status">
                        Gespeicherte Artikel werden geladen...
                    </div>

                    <div id="idsSavedArticlesList" class="ids-saved-list">
                        <div class="ids-saved-empty">
                            Noch keine gespeicherten Artikel gefunden.
                        </div>
                    </div>
                </div>

                <div class="ids-tab-panel" id="idsTabActivity">
                    <div class="ids-live-header">
                        <div>
                            <h2 class="ids-card-title">Live-Rückgabe</h2>
                            <div class="ids-card-subtitle">
                                Sobald der Lieferanten-Shop Produkte zurücksendet, erscheint der Importstatus hier
                                automatisch.
                            </div>
                        </div>

                        <div class="ids-live-status">
                            <span class="ids-live-dot"></span>
                            Live aktiv
                        </div>
                    </div>

                    <div id="liveLogsBox">
                        <div class="ids-log-empty">
                            Noch keine Rückgabe empfangen.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let idsLogsUrl = @json(route('admin.supplier-connectors.latest-logs', $connection));

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-ids-toast]').forEach(function (toast) {
                setTimeout(function () {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(-8px) scale(.98)';
                    toast.style.transition = 'all .25s ease';

                    setTimeout(function () {
                        toast.remove();
                    }, 300);
                }, 4500);
            });

            document.querySelectorAll('[data-ids-tab]').forEach(function (button) {
                button.addEventListener('click', function () {
                    activateIdsTab(button.dataset.idsTab);
                });
            });

            const form = document.getElementById('idsSupplierSearchForm');

            if (form) {
                form.addEventListener('submit', function () {
                    setTimeout(function () {
                        loadIdsData(true);
                        activateIdsTab('activity');
                    }, 1200);
                });
            }

            loadIdsData(false);

            setInterval(function () {
                loadIdsData(false);
            }, 3000);

            if (window.lucide) {
                window.lucide.createIcons();
            }
        });

        function activateIdsTab(tabName) {
            document.querySelectorAll('[data-ids-tab]').forEach(function (button) {
                button.classList.toggle('active', button.dataset.idsTab === tabName);
            });

            document.getElementById('idsTabSaved')?.classList.toggle('active', tabName === 'saved');
            document.getElementById('idsTabActivity')?.classList.toggle('active', tabName === 'activity');
        }

        function escapeIdsHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function idsStatusClass(status) {
            if (status === 'success') {
                return 'ids-log-success';
            }

            if (status === 'failed') {
                return 'ids-log-failed';
            }

            return 'ids-log-pending';
        }

        async function loadIdsData(showLoading = false) {
            const savedStatus = document.getElementById('idsSavedArticlesStatus');

            if (showLoading && savedStatus) {
                savedStatus.textContent = 'Gespeicherte Artikel werden aktualisiert...';
            }

            try {
                const response = await fetch(idsLogsUrl, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                const data = await response.json();
                const logs = Array.isArray(data.logs) ? data.logs : [];

                renderIdsLogs(logs);
                renderIdsSavedArticlesFromLogs(logs);
            } catch (error) {
                console.warn('IDS data could not be loaded', error);

                if (savedStatus) {
                    savedStatus.textContent = 'Gespeicherte Artikel konnten nicht geladen werden.';
                }
            }
        }

        function renderIdsLogs(logs) {
            const box = document.getElementById('liveLogsBox');
            const count = document.getElementById('idsActivityCount');

            if (count) {
                count.textContent = logs.length;
            }

            if (!box) {
                return;
            }

            if (!logs.length) {
                box.innerHTML = `
                        <div class="ids-log-empty">
                            Noch keine Rückgabe empfangen.
                        </div>
                    `;
                return;
            }

            box.innerHTML = logs.map(function (log) {
                const previewUrl = log.preview_url || null;

                return `
                        <div class="ids-log-item">
                            <div class="ids-log-top">
                                <span class="ids-log-status ${idsStatusClass(log.status)}">
                                    ${escapeIdsHtml(log.status)}
                                </span>
                                <span class="ids-log-date">
                                    ${escapeIdsHtml(log.created_at)}
                                </span>
                            </div>

                            <div class="ids-log-message">
                                ${escapeIdsHtml(log.message || 'Keine Nachricht vorhanden.')}
                            </div>

                            <div class="ids-log-meta">
                                <span>Quelle: ${escapeIdsHtml(log.source_type || '-')}</span>
                                <span>Gesamt: ${escapeIdsHtml(log.total_items ?? 0)}</span>
                                <span>Erfolgreich: ${escapeIdsHtml(log.success_items ?? 0)}</span>
                                <span>Fehler: ${escapeIdsHtml(log.failed_items ?? 0)}</span>
                            </div>

                            ${previewUrl ? `
                                <div class="ids-log-actions">
                                    <a href="${escapeIdsHtml(previewUrl)}"
                                       target="_blank"
                                       class="ids-btn ids-btn-green">
                                        Daten prüfen / gespeicherte Artikel ansehen
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    `;
            }).join('');
        }

        function renderIdsSavedArticlesFromLogs(logs) {
            const list = document.getElementById('idsSavedArticlesList');
            const status = document.getElementById('idsSavedArticlesStatus');
            const count = document.getElementById('idsSavedCount');

            if (!list || !status) {
                return;
            }

            const products = [];

            logs.forEach(function (log) {
                if (!Array.isArray(log.saved_products)) {
                    return;
                }

                log.saved_products.forEach(function (product) {
                    products.push(product);
                });
            });

            const unique = [];
            const seen = new Set();

            products.forEach(function (product) {
                const key = product.id || product.product_id || product.url || product.article_no;

                if (!key || seen.has(key)) {
                    return;
                }

                seen.add(key);
                unique.push(product);
            });

            if (count) {
                count.textContent = unique.length;
            }

            if (!unique.length) {
                status.textContent = 'Noch keine gespeicherten Artikel gefunden.';

                list.innerHTML = `
                        <div class="ids-saved-empty">
                            Noch keine Produkte gespeichert. Starte eine Suche, übernimm den Warenkorb und speichere die Artikel nach der Prüfung.
                        </div>
                    `;
                return;
            }

            status.textContent = unique.length + ' gespeicherte Artikel gefunden.';

            list.innerHTML = unique.map(function (product) {
                const productId = product.id || product.product_id || '';
                const title = product.title || product.product || product.name || 'Unbenannter Artikel';
                const supplierArticleNo = product.article_no || product.distributor_article_no || '';
                const manufacturerArticleNo = product.manufacturer_article_no || '';
                const price = product.price || '';
                const purchasePrice = product.purchase_price || '';
                const url = product.url || (productId ? ('/product_details/' + productId) : null);

                return `
                        <div class="ids-saved-item">
                            <div>
                                <div class="ids-saved-title">
                                    ${escapeIdsHtml(title)}
                                </div>

                                <div class="ids-saved-meta">
                                    ${productId ? `<span class="ids-saved-pill">Produkt-ID: ${escapeIdsHtml(productId)}</span>` : ''}
                                    ${supplierArticleNo ? `<span class="ids-saved-pill">Lieferanten-Nr.: ${escapeIdsHtml(supplierArticleNo)}</span>` : ''}
                                    ${manufacturerArticleNo ? `<span class="ids-saved-pill">Hersteller-Nr.: ${escapeIdsHtml(manufacturerArticleNo)}</span>` : ''}
                                    ${price ? `<span class="ids-saved-pill">VK: ${escapeIdsHtml(price)}</span>` : ''}
                                    ${purchasePrice ? `<span class="ids-saved-pill">EK: ${escapeIdsHtml(purchasePrice)}</span>` : ''}
                                </div>
                            </div>

                            ${url ? `
                                <a href="${escapeIdsHtml(url)}"
                                   target="_blank"
                                   class="ids-btn ids-btn-primary">
                                    Produkt öffnen
                                </a>
                            ` : ''}
                        </div>
                    `;
            }).join('');
        }
    </script>
@endsection