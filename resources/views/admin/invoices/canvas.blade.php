@extends('admin.layouts.app')

@section('title', 'Rechnung Canvas')

@section('content')
    {{-- Pilot Bearbeitungs-Sperre (2026-07-16): Rechnung = Geld-Dokument; Banner + sperre:locked-Event. --}}
    @if (!empty($invoice?->id))
        @include('admin.layouts.partials.bearbeitungs-sperre', ['bereich' => 'rechnung', 'sperrId' => $invoice->id])
    @endif
    @php
        $canvasPayload = $payload ?? [];
        $invoiceModel = $invoice ?? null;
        $offerDetailModel = $offerDetail ?? null;

        $saveUrl = $invoiceModel
            ? route('invoices.canvas.save', $invoiceModel)
            : ($offerDetailModel ? route('invoices.canvas.offer-detail.draft', $offerDetailModel) : '#');

        $editMode = $invoiceModel ? 'edit' : 'create';
    @endphp

    <div id="invoice-canvas-app" class="invoice-shell" data-mode="{{ $editMode }}" data-save-url="{{ $saveUrl }}"
        data-index-url="{{ route('admin.invoices.index') }}" data-payload='@json($canvasPayload)'>

        {{-- TOP BAR --}}
        <div class="invoice-topbar no-print">
            <div class="invoice-topbar-left">
                <a href="{{ route('admin.invoices.index') }}" class="invoice-icon-btn" title="Zurück">
                    <i data-lucide="arrow-left"></i>
                </a>

                <div>
                    <div class="invoice-kicker">Rechnung Canvas</div>
                    <div class="invoice-title">
                        <span data-doc-title>Neue Rechnung</span>
                        <span class="invoice-muted" data-doc-number></span>
                    </div>
                </div>
            </div>

            <div class="invoice-topbar-actions">
                <button type="button" class="invoice-btn invoice-btn-light" data-action="toggle-sidebar">
                    <i data-lucide="sliders-horizontal"></i>
                    Einstellungen
                </button>

                <button type="button" class="invoice-btn invoice-btn-light" data-action="print-preview">
                    <i data-lucide="eye"></i>
                    Druckvorschau
                </button>

                <button type="button" class="invoice-btn invoice-btn-primary" data-action="save">
                    <i data-lucide="save"></i>
                    Speichern
                </button>
            </div>
        </div>

        {{-- AUFTRAG SYNC BANNER --}}
        <div class="invoice-sync-banner no-print hidden" data-sync-banner>
            <div class="invoice-sync-icon">
                <i data-lucide="triangle-alert"></i>
            </div>

            <div class="invoice-sync-content">
                <div class="invoice-sync-title">Auftrag wurde geändert</div>
                <div class="invoice-sync-text" data-sync-text>
                    Die ursprünglichen Auftrag-Positionen haben sich geändert.
                </div>
            </div>

            <div class="invoice-sync-actions">
                <button type="button" class="invoice-btn invoice-btn-light" data-action="dismiss-sync">
                    Später
                </button>

                <button type="button" class="invoice-btn invoice-btn-primary" data-action="sync-auftrag">
                    <i data-lucide="refresh-cw"></i>
                    Jetzt synchronisieren
                </button>
            </div>
        </div>

        <div class="invoice-workspace">
            {{-- LEFT SETTINGS --}}
            <aside class="invoice-sidebar no-print" data-sidebar>
                <div class="invoice-sidebar-head">
                    <div>
                        <div class="invoice-kicker">Dokument</div>
                        <h2>Rechnung erstellen</h2>
                    </div>
                    <button type="button" class="invoice-icon-btn lg:hidden" data-action="toggle-sidebar">
                        <i data-lucide="x"></i>
                    </button>
                </div>

                <div class="invoice-sidebar-scroll">
                    <section class="invoice-panel">
                        <h3>Rechnungsart</h3>

                        <label class="invoice-label">Typ</label>
                        <select class="invoice-input" data-field="type">
                            <option value="Rechnung">Rechnung</option>
                            <option value="Teilrechnung">Teilrechnung</option>
                            <option value="Abschlagsrechnung">Abschlagsrechnung</option>
                            <option value="Schlussrechnung">Schlussrechnung</option>
                            <option value="Anzahlung">Anzahlung</option>
                            <option value="Gutschrift">Gutschrift</option>
                        </select>

                        <label class="invoice-label mt-3">Abrechnungsart</label>
                        <select class="invoice-input" data-field="invoice_mode">
                            <option value="full">Alle Auftrag-Positionen</option>
                            <option value="percentage">Prozent vom Auftragsbetrag</option>
                        </select>

                        <div data-percentage-wrap class="mt-3 hidden">
                            <label class="invoice-label">Prozent</label>
                            <div class="invoice-input-group">
                                <input type="number" step="0.01" min="0" max="100" class="invoice-input"
                                    data-field="percentage" value="30">
                                <span>%</span>
                            </div>

                            <button type="button" class="invoice-btn invoice-btn-light w-full mt-3"
                                data-action="apply-percentage">
                                <i data-lucide="percent"></i>
                                Prozent berechnen
                            </button>
                        </div>

                        <button type="button" class="invoice-btn invoice-btn-light w-full mt-3" data-action="reload-full">
                            <i data-lucide="rotate-cw"></i>
                            Auftrag-Positionen laden
                        </button>
                    </section>

                    <section class="invoice-panel">
                        <h3>Termine</h3>

                        <label class="invoice-label">Rechnungsdatum</label>
                        <input type="date" class="invoice-input" data-field="issue_date">

                        <label class="invoice-label mt-3">Fällig bis</label>
                        <input type="date" class="invoice-input" data-field="due_date">

                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div>
                                <label class="invoice-label">Leistung von</label>
                                <input type="date" class="invoice-input" data-field="service_from">
                            </div>
                            <div>
                                <label class="invoice-label">Leistung bis</label>
                                <input type="date" class="invoice-input" data-field="service_to">
                            </div>
                        </div>
                    </section>

                    <section class="invoice-panel">
                        <h3>Zahlung & Text</h3>

                        <label class="invoice-label">Zahlungshinweis</label>
                        <textarea class="invoice-textarea" rows="3" data-field="payment_note"></textarea>

                        <label class="invoice-label mt-3">Notizen</label>
                        <textarea class="invoice-textarea" rows="4" data-field="notes"></textarea>
                    </section>

                    <section class="invoice-panel invoice-position-manager">
                        <h3>Positionen</h3>

                        <div class="invoice-position-tabs">
                            <button type="button" class="invoice-position-tab active" data-position-tab-btn="settings">
                                <i data-lucide="sliders-horizontal"></i> Optionen
                            </button>
                            <button type="button" class="invoice-position-tab" data-position-tab-btn="list">
                                <i data-lucide="list"></i> Liste
                            </button>
                            <button type="button" class="invoice-position-tab" data-position-tab-btn="groups">
                                <i data-lucide="layers"></i> Gruppen
                            </button>
                        </div>

                        <div class="invoice-position-panel active" data-position-tab-panel="settings">
                            <button type="button" class="invoice-btn invoice-btn-light w-full" data-action="add-row">
                                <i data-lucide="plus"></i>
                                Position am Ende hinzufügen
                            </button>

                            <button type="button" class="invoice-btn invoice-btn-light w-full mt-3"
                                data-action="reload-full">
                                <i data-lucide="rotate-cw"></i>
                                Original Auftrag-Positionen laden
                            </button>

                            <label class="invoice-toggle-row mt-3">
                                <input type="checkbox" data-toggle="group-positions">
                                <span>
                                    <strong>Positionen gruppieren</strong>
                                    <small>Gleiche Quellgruppen werden in der Rechnung mit Überschrift angezeigt.</small>
                                </span>
                            </label>

                            <label class="invoice-toggle-row">
                                <input type="checkbox" data-toggle="show-hidden-list" checked>
                                <span>
                                    <strong>Ausgeblendete in Liste anzeigen</strong>
                                    <small>Ausgeblendete Positionen bleiben in der Verwaltung sichtbar.</small>
                                </span>
                            </label>

                            <div class="invoice-mini-hint mt-3">
                                Die Positionen kommen aus <strong>offer_details.sections</strong>. Du kannst sie ziehen,
                                sortieren,
                                ausblenden, löschen oder neue Positionen zwischen zwei Zeilen einfügen.
                            </div>
                        </div>

                        <div class="invoice-position-panel" data-position-tab-panel="list">
                            <div class="invoice-list-head">
                                <span data-position-list-count>0 Positionen</span>
                                <button type="button" class="invoice-mini-btn" data-action="add-row">
                                    <i data-lucide="plus"></i> Neu
                                </button>
                            </div>
                            <div class="invoice-position-list" data-position-list-root></div>
                        </div>

                        <div class="invoice-position-panel" data-position-tab-panel="groups">
                            <div class="invoice-mini-hint">
                                Gruppen werden aus <strong>group_title</strong>, <strong>source_item_type</strong>,
                                <strong>product_group</strong> oder der Artikelgruppe erzeugt.
                            </div>
                            <div class="invoice-group-list mt-3" data-group-list-root></div>
                        </div>
                    </section>
                </div>
            </aside>

            {{-- CANVAS / POSITION WORKSPACE --}}
            <main class="invoice-canvas-area">
                <div class="invoice-mobile-panel-toggle no-print">
                    <button type="button" class="invoice-btn invoice-btn-light" data-action="toggle-sidebar">
                        <i data-lucide="sliders-horizontal"></i>
                        Einstellungen öffnen
                    </button>
                </div>

                <div class="invoice-main-tabs no-print">
                    <button type="button" class="invoice-main-tab active" data-main-view-tab="preview">
                        <i data-lucide="file-text"></i>
                        A4 Vorschau
                    </button>
                    <button type="button" class="invoice-main-tab" data-main-view-tab="positions">
                        <i data-lucide="list-checks"></i>
                        Positionen bearbeiten
                        <span data-main-position-badge>0</span>
                    </button>
                </div>

                <div class="invoice-main-panel active" data-main-view-panel="preview">
                    <div class="a4-page invoice-a4" data-a4-page>
                        {{-- HEADER --}}
                        <header class="invoice-letter-head">
                            <div class="invoice-logo-line">
                                <div class="invoice-logo-box" data-logo-box>
                                    <img data-company-logo src="" alt="Logo" class="hidden">
                                    <div data-company-logo-text class="invoice-logo-text">SOLAR ASPEKT</div>
                                </div>
                            </div>

                            <div class="invoice-sender-line" data-sender-line>
                                Firma, Straße, PLZ Ort
                            </div>
                        </header>

                        {{-- ADDRESS --}}
                        <section class="invoice-address-grid">
                            <div class="invoice-address" data-customer-address>
                                <div>An die</div>
                                <div>Kunde</div>
                                <div>Straße</div>
                                <div>PLZ Ort</div>
                            </div>
                        </section>

                        {{-- TITLE + META --}}
                        <section class="invoice-doc-grid">
                            <div>
                                <h1 data-preview-type>RECHNUNG</h1>
                                <div class="invoice-number" data-preview-invoice-no>Entwurf</div>
                                <div class="invoice-project-title" data-preview-project-title>Projekt</div>
                            </div>

                            <div class="invoice-meta-table">
                                <div>
                                    <strong>Datum</strong>
                                    <span data-preview-issue-date></span>
                                </div>
                                <div>
                                    <strong>Ansprechpartner</strong>
                                    <span data-preview-contact-person></span>
                                </div>
                                <div>
                                    <strong>Kundennummer</strong>
                                    <span data-preview-customer-no></span>
                                </div>
                                <div>
                                    <strong>Leistungszeitraum</strong>
                                    <span data-preview-service-period></span>
                                </div>
                            </div>
                        </section>

                        {{-- INTRO --}}
                        <section class="invoice-intro">
                            <p data-preview-greeting>Sehr geehrte Damen und Herren,</p>
                            <p>hiermit erlauben wir uns, unsere erbrachten Leistungen wie folgt in Rechnung zu stellen:</p>
                        </section>

                        {{-- ITEMS --}}
                        <section class="invoice-items-section">
                            <div class="invoice-table-head">
                                <div>Position</div>
                                <div>Text</div>
                                <div>Menge</div>
                                <div>Einh</div>
                                <div>Einzelpreis</div>
                                <div>Gesamtpreis</div>
                                <div class="no-print"></div>
                            </div>

                            <div data-items-root></div>
                        </section>

                        {{-- TOTALS --}}
                        <section class="invoice-total-box">
                            <div class="invoice-total-row">
                                <span>Nettogesamtpreis</span>
                                <strong data-preview-subtotal>0,00 €</strong>
                            </div>
                            <div class="invoice-total-row">
                                <span>Umsatzsteuer <span data-preview-tax-rate>19,0</span>%</span>
                                <strong data-preview-tax-amount>0,00 €</strong>
                            </div>
                            <div class="invoice-total-row grand">
                                <span>Gesamtsumme</span>
                                <strong data-preview-total>0,00 €</strong>
                            </div>
                        </section>

                        {{-- PAYMENT + LEGAL --}}
                        <section class="invoice-bottom-text">
                            <p data-preview-payment-note>Zahlbar ohne Abzug bis zum angegebenen Fälligkeitsdatum.</p>

                            <p>
                                Sie sind gesetzlich verpflichtet, diese Rechnung mindestens 2 Jahre -
                                als umsatzsteuerlicher Unternehmer mind. 10 Jahre - aufzubewahren.
                                Die Aufbewahrungsfrist beginnt mit Schluss dieses Kalenderjahres.
                            </p>

                            <p>
                                Hinweis für Unternehmer: Eine Freistellungsbescheinigung gemäß § 48b Abs. 1 Satz 1 EStG
                                liegt
                                vor.
                            </p>

                            <p>
                                Vielen Dank für Ihren Auftrag!<br>
                                <span data-preview-company-name>SOLAR ASPEKT</span>
                            </p>
                        </section>

                        {{-- FOOTER --}}
                        <footer class="invoice-footer" data-preview-footer>
                            Firmenfußzeile
                        </footer>
                    </div>
                </div>

                <div class="invoice-main-panel" data-main-view-panel="positions">
                    <section class="invoice-position-workbench no-print">
                        <div class="invoice-workbench-head">
                            <div>
                                <div class="invoice-kicker">Rechnungspositionen</div>
                                <h2>Positionen bearbeiten</h2>
                                <p>Hier kannst du alle Positionen sehen, sortieren, ausblenden, löschen oder neue Zeilen
                                    zwischen bestehende Positionen einfügen.</p>
                            </div>
                            <div class="invoice-workbench-actions">
                                <button type="button" class="invoice-btn invoice-btn-light" data-action="reload-full">
                                    <i data-lucide="rotate-cw"></i> Auftrag neu laden
                                </button>
                                <button type="button" class="invoice-btn invoice-btn-primary" data-action="add-row">
                                    <i data-lucide="plus"></i> Neue Position
                                </button>
                            </div>
                        </div>

                        <div class="invoice-workbench-stats">
                            <div><span>Sichtbar</span><b data-main-visible-count>0</b></div>
                            <div><span>Ausgeblendet</span><b data-main-hidden-count>0</b></div>
                            <div><span>Netto</span><b data-main-net-total>0,00 €</b></div>
                            <div><span>Brutto</span><b data-main-gross-total>0,00 €</b></div>
                        </div>

                        <div class="invoice-workbench-grid">
                            <div class="invoice-workbench-list" data-main-position-root></div>
                            <aside class="invoice-workbench-groups">
                                <div class="invoice-workbench-card">
                                    <h3>Gruppen</h3>
                                    <label class="invoice-toggle-row">
                                        <input type="checkbox" data-toggle="group-positions-main">
                                        <span>
                                            <strong>Gruppiert in A4 anzeigen</strong>
                                            <small>Die A4 Vorschau bekommt Überschriften je Gruppe.</small>
                                        </span>
                                    </label>
                                    <div class="invoice-group-list mt-3" data-main-group-root></div>
                                </div>
                            </aside>
                        </div>
                    </section>
                </div>
            </main>
        </div>
    </div>

    {{-- Print preview modal --}}
    <div class="invoice-print-backdrop no-print" data-print-preview-backdrop></div>
    <div class="invoice-print-modal no-print" data-print-preview-modal>
        <div class="invoice-print-modal-head">
            <div>
                <div class="invoice-kicker">Druckvorschau</div>
                <h3>Rechnung als Druckversion</h3>
            </div>
            <div class="invoice-print-actions">
                <button type="button" class="invoice-btn invoice-btn-light" data-action="close-print-preview">
                    <i data-lucide="x"></i> Schließen
                </button>
                <button type="button" class="invoice-btn invoice-btn-primary" data-action="print-from-preview">
                    <i data-lucide="printer"></i> Jetzt drucken
                </button>
            </div>
        </div>
        <div class="invoice-print-modal-body" data-print-preview-body></div>
    </div>

    <div id="invoice-print-area" data-print-area></div>

    {{-- Toast notifications --}}
    <div class="invoice-toast-stack no-print" data-toast-stack></div>

    {{-- Custom confirm modal --}}
    <div class="invoice-confirm-backdrop no-print" data-confirm-backdrop></div>
    <div class="invoice-confirm-modal no-print" data-confirm-modal role="dialog" aria-modal="true">
        <div class="invoice-confirm-icon" data-confirm-icon>
            <i data-lucide="triangle-alert"></i>
        </div>
        <div class="invoice-confirm-content">
            <div class="invoice-confirm-kicker" data-confirm-kicker>Bestätigung</div>
            <h3 data-confirm-title>Aktion bestätigen</h3>
            <p data-confirm-message>Bitte bestätige diese Aktion.</p>
        </div>
        <div class="invoice-confirm-actions">
            <button type="button" class="invoice-btn invoice-btn-light" data-confirm-cancel>
                <i data-lucide="x"></i>
                Abbrechen
            </button>
            <button type="button" class="invoice-btn invoice-btn-danger" data-confirm-ok>
                <i data-lucide="check"></i>
                Bestätigen
            </button>
        </div>
    </div>

    {{-- Row template --}}
    <template id="invoice-row-template">
        <div class="invoice-item-row" data-row>
            <div class="invoice-pos-no" data-row-no>1</div>

            <div>
                <input type="text" class="invoice-row-input title" data-row-field="title">
                <textarea class="invoice-row-desc" rows="2" data-row-field="description"></textarea>
            </div>

            <input type="number" step="0.01" class="invoice-row-input text-right" data-row-field="qty">
            <input type="text" class="invoice-row-input" data-row-field="unit">
            <input type="number" step="0.01" class="invoice-row-input text-right" data-row-field="unit_price">
            <input type="number" step="0.01" class="invoice-row-input text-right" data-row-field="line_total">

            <div class="invoice-row-actions no-print">
                <div class="invoice-action-menu" data-action-menu>
                    <button type="button" class="invoice-menu-trigger" data-action="toggle-row-menu">
                        <i data-lucide="ellipsis-vertical"></i>
                        <span>Menü</span>
                    </button>
                    <div class="invoice-menu-panel">
                        <button type="button" data-action="add-before-row"><i data-lucide="arrow-up"></i> Position
                            darüber einfügen</button>
                        <button type="button" data-action="add-after-row"><i data-lucide="arrow-down"></i> Position
                            darunter einfügen</button>
                        <button type="button" data-action="toggle-hide-row"><i data-lucide="eye-off"></i> Position
                            ausblenden</button>
                    </div>
                </div>

                <button type="button" class="invoice-direct-delete" data-action="delete-row" title="Position löschen">
                    <i data-lucide="trash-2"></i>
                    <span>Löschen</span>
                </button>
            </div>
        </div>
    </template>
@endsection

@push('style')
    <style>
        :root {
            --invoice-brand: #7b2d73;
            --invoice-text: #111827;
            --invoice-muted: #6b7280;
            --invoice-line: #7b2d73;
            --invoice-bg: #f1f5f9;
        }

        .invoice-shell {
            height: calc(100vh - 0px);
            background: var(--invoice-bg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            color: var(--invoice-text);
        }

        .invoice-topbar {
            height: 64px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 0 18px;
            flex-shrink: 0;
            z-index: 20;
        }

        .invoice-topbar-left,
        .invoice-topbar-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .invoice-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: #94a3b8;
        }

        .invoice-title {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
        }

        .invoice-muted {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            margin-left: 6px;
        }

        .invoice-icon-btn {
            width: 38px;
            height: 38px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: .18s ease;
        }

        .invoice-icon-btn:hover {
            border-color: var(--invoice-brand);
            color: var(--invoice-brand);
            background: #f8fafc;
        }

        .invoice-btn {
            min-height: 38px;
            border-radius: 12px;
            padding: 0 14px;
            font-size: 13px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1px solid transparent;
            transition: .18s ease;
        }

        .invoice-btn-primary {
            background: var(--invoice-brand);
            color: #fff;
            box-shadow: 0 10px 18px rgba(123, 45, 115, .18);
        }

        .invoice-btn-primary:hover {
            filter: brightness(1.04);
        }

        .invoice-btn-light {
            background: #fff;
            border-color: #e2e8f0;
            color: #0f172a;
        }

        .invoice-btn-light:hover {
            border-color: var(--invoice-brand);
            color: var(--invoice-brand);
        }

        .invoice-workspace {
            flex: 1;
            min-height: 0;
            display: flex;
            overflow: hidden;
        }

        .invoice-sidebar {
            width: 360px;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 15;
        }

        .invoice-sidebar-head {
            padding: 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .invoice-sidebar-head h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .invoice-sidebar-scroll {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .invoice-panel {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 14px;
            background: linear-gradient(180deg, #fff, #f8fafc);
            margin-bottom: 14px;
        }

        .invoice-panel h3 {
            font-size: 13px;
            font-weight: 900;
            margin: 0 0 12px 0;
            color: #0f172a;
        }

        .invoice-label {
            display: block;
            font-size: 11px;
            font-weight: 800;
            color: #64748b;
            margin-bottom: 6px;
        }

        .invoice-input,
        .invoice-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #fff;
            padding: 9px 11px;
            font-size: 13px;
            color: #0f172a;
            outline: none;
            transition: .18s ease;
        }

        .invoice-input:focus,
        .invoice-textarea:focus {
            border-color: var(--invoice-brand);
            box-shadow: 0 0 0 4px rgba(123, 45, 115, .10);
        }

        .invoice-input-group {
            display: flex;
            align-items: center;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
        }

        .invoice-input-group .invoice-input {
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }

        .invoice-input-group span {
            padding: 0 12px;
            font-size: 12px;
            font-weight: 900;
            color: #64748b;
            background: #f8fafc;
            align-self: stretch;
            display: flex;
            align-items: center;
            border-left: 1px solid #e2e8f0;
        }

        .invoice-mini-hint {
            font-size: 12px;
            color: #64748b;
            line-height: 1.45;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 12px;
            padding: 10px;
        }

        .invoice-canvas-area {
            flex: 1;
            min-width: 0;
            overflow: auto;
            padding: 32px;
        }

        .invoice-mobile-panel-toggle {
            display: none;
            margin-bottom: 16px;
        }

        .invoice-a4 {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 40px auto;
            background: #fff;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .18);
            padding: 20mm 16mm 14mm 16mm;
            position: relative;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            color: #000;
        }

        .invoice-letter-head {
            flex-shrink: 0;
        }

        .invoice-logo-line {
            min-height: 52px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .invoice-logo-box {
            text-align: center;
        }

        .invoice-logo-box img {
            max-height: 56px;
            max-width: 230px;
            object-fit: contain;
        }

        .invoice-logo-text {
            font-size: 30px;
            font-weight: 900;
            color: var(--invoice-brand);
            letter-spacing: -.04em;
        }

        .invoice-sender-line {
            margin-top: 34px;
            font-size: 8px;
            color: #9ca3af;
        }

        .invoice-address-grid {
            margin-top: 12px;
            display: grid;
            grid-template-columns: 1fr 230px;
            gap: 20px;
        }

        .invoice-address {
            font-size: 13px;
            line-height: 1.35;
            min-height: 86px;
        }

        .invoice-doc-grid {
            margin-top: 42px;
            display: grid;
            grid-template-columns: 1fr 260px;
            gap: 28px;
            align-items: start;
        }

        .invoice-doc-grid h1 {
            margin: 0;
            font-size: 22px;
            line-height: 1;
            color: var(--invoice-brand);
            font-weight: 900;
            text-transform: uppercase;
        }

        .invoice-number {
            margin-top: 6px;
            font-size: 22px;
            color: rgba(123, 45, 115, .45);
            font-weight: 400;
            line-height: 1;
        }

        .invoice-project-title {
            margin-top: 18px;
            font-size: 16px;
            line-height: 1.2;
            font-weight: 900;
            color: #4b5563;
            text-transform: uppercase;
        }

        .invoice-meta-table {
            font-size: 11px;
            line-height: 1.5;
        }

        .invoice-meta-table>div {
            display: grid;
            grid-template-columns: 110px 1fr;
            gap: 8px;
        }

        .invoice-meta-table strong {
            color: #4b5563;
            font-weight: 900;
        }

        .invoice-meta-table span {
            text-align: right;
        }

        .invoice-intro {
            margin-top: 34px;
            font-size: 13px;
            line-height: 1.5;
        }

        .invoice-items-section {
            margin-top: 20px;
        }

        .invoice-table-head,
        .invoice-item-row {
            display: grid;
            grid-template-columns: 70px minmax(0, 1fr) 58px 52px 90px 100px 96px;
            gap: 8px;
            align-items: start;
        }

        .invoice-table-head {
            color: #111827;
            font-size: 12px;
            border-top: 2px solid var(--invoice-line);
            border-bottom: 2px solid var(--invoice-line);
            padding: 8px 0;
        }

        .invoice-item-row {
            padding: 10px 0;
            font-size: 12px;
            border-bottom: 1px solid #e5e7eb;
        }

        .invoice-pos-no {
            font-size: 12px;
            padding-top: 7px;
        }

        .invoice-row-input,
        .invoice-row-desc {
            width: 100%;
            border: 1px solid transparent;
            background: transparent;
            border-radius: 6px;
            padding: 5px 6px;
            font-size: 12px;
            outline: none;
            color: #000;
        }

        .invoice-row-input:hover,
        .invoice-row-desc:hover,
        .invoice-row-input:focus,
        .invoice-row-desc:focus {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        .invoice-row-input.title {
            font-weight: 800;
        }

        .invoice-row-desc {
            resize: vertical;
            line-height: 1.35;
            min-height: 34px;
        }

        .invoice-row-delete {
            width: 30px;
            height: 30px;
            border-radius: 9px;
            border: 1px solid #fee2e2;
            color: #dc2626;
            background: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .invoice-total-box {
            margin-left: 70px;
            margin-top: 10px;
            border-top: 2px solid #000;
            border-bottom: 4px solid #000;
            padding: 6px 0;
            font-size: 13px;
        }

        .invoice-total-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            line-height: 1.45;
        }

        .invoice-total-row.grand {
            font-weight: 900;
        }

        .invoice-bottom-text {
            margin-top: 12px;
            font-size: 11px;
            line-height: 1.45;
        }

        .invoice-bottom-text p {
            margin: 0 0 13px 0;
        }

        .invoice-footer {
            margin-top: auto;
            padding-top: 20px;
            font-size: 9px;
            line-height: 1.45;
            color: #6b7280;
            font-weight: 600;
        }

        .hidden {
            display: none !important;
        }

        .mt-3 {
            margin-top: .75rem;
        }

        .w-full {
            width: 100%;
        }

        .grid {
            display: grid;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .gap-3 {
            gap: .75rem;
        }

        .text-right {
            text-align: right;
        }

        @media (max-width: 1180px) {
            .invoice-sidebar {
                position: fixed;
                top: 64px;
                bottom: 0;
                left: 0;
                transform: translateX(-105%);
                transition: .25s ease;
                box-shadow: 20px 0 40px rgba(15, 23, 42, .15);
            }

            .invoice-shell.sidebar-open .invoice-sidebar {
                transform: translateX(0);
            }

            .invoice-mobile-panel-toggle {
                display: block;
            }

            .invoice-canvas-area {
                padding: 18px;
            }

            .invoice-a4 {
                transform-origin: top left;
            }
        }

        @media (max-width: 840px) {
            .invoice-topbar {
                height: auto;
                padding: 12px;
                align-items: flex-start;
                flex-direction: column;
            }

            .invoice-topbar-actions {
                width: 100%;
                overflow-x: auto;
            }

            .invoice-canvas-area {
                padding: 12px;
            }

            .invoice-a4 {
                width: 210mm;
                transform: scale(.55);
                transform-origin: top left;
                margin: 0;
            }
        }



        /* Enhanced invoice canvas controls */
        .invoice-position-tabs {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 5px;
            margin-bottom: 12px;
        }

        .invoice-position-tab {
            min-height: 34px;
            border: 1px solid transparent;
            border-radius: 10px;
            background: transparent;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: .18s ease;
        }

        .invoice-position-tab.active {
            background: #fff;
            border-color: rgba(123, 45, 115, .22);
            color: var(--invoice-brand);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .07);
        }

        .invoice-position-panel {
            display: none;
        }

        .invoice-position-panel.active {
            display: block;
        }

        .invoice-toggle-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            border: 1px solid #e2e8f0;
            background: #fff;
            border-radius: 14px;
            padding: 10px;
            cursor: pointer;
        }

        .invoice-toggle-row+.invoice-toggle-row {
            margin-top: 8px;
        }

        .invoice-toggle-row input {
            margin-top: 3px;
            accent-color: var(--invoice-brand);
        }

        .invoice-toggle-row strong {
            display: block;
            font-size: 12px;
            color: #0f172a;
        }

        .invoice-toggle-row small {
            display: block;
            font-size: 11px;
            color: #64748b;
            line-height: 1.35;
        }

        .invoice-list-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 900;
            color: #64748b;
        }

        .invoice-mini-btn {
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #0f172a;
            border-radius: 10px;
            min-height: 30px;
            padding: 0 9px;
            display: inline-flex;
            gap: 6px;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
        }

        .invoice-mini-btn:hover {
            color: var(--invoice-brand);
            border-color: color-mix(in srgb, var(--invoice-brand) 50%, #e2e8f0);
        }

        .invoice-position-list,
        .invoice-group-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 420px;
            overflow: auto;
            padding-right: 3px;
        }

        .invoice-position-card {
            border: 1px solid #e2e8f0;
            background: #fff;
            border-radius: 14px;
            padding: 10px;
            box-shadow: 0 8px 16px rgba(15, 23, 42, .04);
        }

        .invoice-position-card.is-hidden {
            opacity: .58;
            background: repeating-linear-gradient(45deg, #fff, #fff 10px, #f8fafc 10px, #f8fafc 20px);
        }

        .invoice-position-card.dragging,
        .invoice-item-row.dragging {
            opacity: .35;
        }

        .invoice-position-card.drag-over,
        .invoice-item-row.drag-over {
            outline: 2px dashed var(--invoice-brand);
            outline-offset: 3px;
        }

        .invoice-position-card-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            align-items: flex-start;
        }

        .invoice-position-drag {
            width: 28px;
            height: 28px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            cursor: grab;
            background: #f8fafc;
        }

        .invoice-position-title {
            font-size: 12px;
            font-weight: 900;
            color: #0f172a;
            line-height: 1.35;
        }

        .invoice-position-meta {
            font-size: 11px;
            color: #64748b;
            font-weight: 700;
            margin-top: 2px;
            line-height: 1.35;
        }

        .invoice-position-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 9px;
        }

        .invoice-position-action,
        .invoice-row-mini {
            width: 28px;
            height: 28px;
            border-radius: 9px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .invoice-position-action:hover,
        .invoice-row-mini:hover {
            color: var(--invoice-brand);
            border-color: var(--invoice-brand);
            background: #f8fafc;
        }

        .invoice-position-action.danger:hover,
        .invoice-row-mini.danger:hover {
            color: #dc2626;
            border-color: #fecaca;
            background: #fef2f2;
        }

        .invoice-row-actions {
            display: grid;
            grid-template-columns: repeat(2, 28px);
            gap: 4px;
        }

        .invoice-item-row {
            cursor: grab;
            position: relative;
        }

        .invoice-item-row:active {
            cursor: grabbing;
        }

        .invoice-item-row.is-hidden {
            display: none;
        }

        .invoice-group-title-row {
            display: grid;
            grid-template-columns: 70px minmax(0, 1fr) 58px 52px 90px 100px 96px;
            gap: 8px;
            align-items: center;
            padding: 9px 0 6px;
            color: var(--invoice-brand);
            font-size: 12px;
            font-weight: 900;
            border-bottom: 1px solid color-mix(in srgb, var(--invoice-brand) 40%, #e5e7eb);
        }

        .invoice-group-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            background: #fff;
            padding: 10px;
        }

        .invoice-group-card b {
            color: #0f172a;
            font-size: 12px;
        }

        .invoice-group-card span {
            display: block;
            margin-top: 3px;
            color: #64748b;
            font-size: 11px;
            font-weight: 700;
        }

        .invoice-print-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .66);
            backdrop-filter: blur(5px);
            z-index: 10000;
            opacity: 0;
            visibility: hidden;
            transition: .18s ease;
        }

        .invoice-print-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .invoice-print-modal {
            position: fixed;
            inset: 3vh 3vw;
            background: #f1f5f9;
            border-radius: 22px;
            box-shadow: 0 30px 90px rgba(2, 6, 23, .35);
            z-index: 10001;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            opacity: 0;
            visibility: hidden;
            transform: scale(.98);
            transition: .18s ease;
        }

        .invoice-print-modal.active {
            opacity: 1;
            visibility: visible;
            transform: scale(1);
        }

        .invoice-print-modal-head {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .invoice-print-modal-head h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .invoice-print-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .invoice-print-modal-body {
            flex: 1;
            overflow: auto;
            padding: 26px;
        }

        .invoice-print-modal-body .invoice-a4 {
            box-shadow: 0 20px 50px rgba(2, 6, 23, .16);
            margin-bottom: 26px;
        }

        .invoice-main-tabs {
            display: inline-flex;
            gap: 8px;
            background: rgba(255, 255, 255, .86);
            border: 1px solid #e2e8f0;
            padding: 6px;
            border-radius: 16px;
            margin: 0 auto 18px auto;
            position: sticky;
            top: 0;
            z-index: 12;
            backdrop-filter: blur(8px);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .08);
        }

        .invoice-main-tab {
            border: 1px solid transparent;
            background: transparent;
            color: #64748b;
            border-radius: 12px;
            min-height: 40px;
            padding: 0 14px;
            font-size: 13px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: .18s ease;
        }

        .invoice-main-tab span {
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
        }

        .invoice-main-tab.active {
            background: #fff;
            border-color: color-mix(in srgb, var(--invoice-brand) 35%, #e2e8f0);
            color: var(--invoice-brand);
            box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
        }

        .invoice-main-panel {
            display: none;
        }

        .invoice-main-panel.active {
            display: block;
        }

        .invoice-position-workbench {
            max-width: 1320px;
            margin: 0 auto 40px auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .12);
            overflow: hidden;
        }

        .invoice-workbench-head {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: flex-start;
            background: linear-gradient(135deg, rgba(123, 45, 115, .08), rgba(116, 178, 212, .08));
            border-bottom: 1px solid #e2e8f0;
        }

        .invoice-workbench-head h2 {
            margin: 2px 0 4px;
            font-size: 22px;
            font-weight: 900;
            color: #0f172a;
        }

        .invoice-workbench-head p {
            margin: 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.45;
        }

        .invoice-workbench-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        .invoice-workbench-stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
        }

        .invoice-workbench-stats div {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px;
        }

        .invoice-workbench-stats span {
            display: block;
            font-size: 11px;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .invoice-workbench-stats b {
            display: block;
            margin-top: 4px;
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
        }

        .invoice-workbench-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 320px;
            gap: 18px;
            padding: 20px;
            background: #fff;
        }

        .invoice-workbench-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 0;
        }

        .invoice-workbench-row {
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .05);
            padding: 12px;
            display: grid;
            grid-template-columns: 34px minmax(220px, 1fr) 88px 78px 110px 118px 120px;
            gap: 10px;
            align-items: start;
        }

        .invoice-workbench-row.is-hidden {
            opacity: .62;
            background: repeating-linear-gradient(45deg, #fff, #fff 10px, #f8fafc 10px, #f8fafc 20px);
        }

        .invoice-workbench-row.dragging {
            opacity: .35;
        }

        .invoice-workbench-row.drag-over {
            outline: 2px dashed var(--invoice-brand);
            outline-offset: 3px;
        }

        .invoice-drag-handle {
            width: 34px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: grab;
        }

        .invoice-workbench-field label {
            display: block;
            margin-bottom: 5px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #94a3b8;
        }

        .invoice-workbench-input,
        .invoice-workbench-textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            background: #fff;
            padding: 8px 10px;
            font-size: 13px;
            color: #0f172a;
            outline: none;
        }

        .invoice-workbench-input:focus,
        .invoice-workbench-textarea:focus {
            border-color: var(--invoice-brand);
            box-shadow: 0 0 0 4px rgba(123, 45, 115, .10);
        }

        .invoice-workbench-textarea {
            min-height: 66px;
            resize: vertical;
            line-height: 1.35;
        }

        .invoice-action-menu {
            position: relative;
            display: inline-flex;
            justify-content: flex-end;
        }

        .invoice-menu-trigger {
            min-height: 34px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #0f172a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
        }

        .invoice-menu-trigger:hover {
            color: var(--invoice-brand);
            border-color: var(--invoice-brand);
            background: #f8fafc;
        }

        .invoice-menu-panel {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            min-width: 240px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 22px 50px rgba(15, 23, 42, .18);
            padding: 7px;
            z-index: 80;
            display: none;
        }

        .invoice-action-menu.open .invoice-menu-panel {
            display: block;
        }

        .invoice-menu-panel button {
            width: 100%;
            border: 0;
            background: transparent;
            color: #0f172a;
            padding: 9px 10px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 12px;
            font-weight: 800;
            text-align: left;
            cursor: pointer;
        }

        .invoice-menu-panel button:hover {
            background: #f8fafc;
            color: var(--invoice-brand);
        }

        .invoice-menu-panel button.danger {
            color: #dc2626;
        }

        .invoice-menu-panel button.danger:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        #invoice-print-area {
            display: none;
        }

        .invoice-shell [data-lucide],
        .invoice-print-modal [data-lucide],
        .invoice-confirm-modal [data-lucide],
        .invoice-toast-stack [data-lucide] {
            width: 16px;
            height: 16px;
            stroke-width: 2.35;
            flex-shrink: 0;
        }

        .invoice-btn-danger {
            background: #dc2626;
            color: #fff;
            border-color: #dc2626;
            box-shadow: 0 10px 18px rgba(220, 38, 38, .16);
        }

        .invoice-btn-danger:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }

        .invoice-row-actions {
            display: flex !important;
            align-items: center;
            justify-content: flex-end;
            gap: 6px;
        }

        .invoice-direct-delete {
            min-height: 34px;
            border-radius: 12px;
            border: 1px solid #fecaca;
            background: #fff;
            color: #dc2626;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 0 10px;
            font-size: 12px;
            font-weight: 900;
            cursor: pointer;
            transition: .18s ease;
        }

        .invoice-direct-delete:hover {
            background: #fef2f2;
            border-color: #dc2626;
            color: #b91c1c;
        }

        .invoice-menu-delete-row {
            margin-top: 8px;
            display: flex;
            justify-content: flex-end;
        }

        .invoice-toast-stack {
            position: fixed;
            right: 20px;
            bottom: 20px;
            z-index: 12050;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .invoice-toast {
            width: min(420px, calc(100vw - 32px));
            border: 1px solid #e2e8f0;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(12px);
            box-shadow: 0 24px 60px rgba(15, 23, 42, .18);
            border-radius: 18px;
            padding: 12px 14px;
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr) 30px;
            gap: 10px;
            align-items: start;
            pointer-events: auto;
            transform: translateY(12px);
            opacity: 0;
            transition: .22s ease;
            overflow: hidden;
        }

        .invoice-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .invoice-toast::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--invoice-brand);
        }

        .invoice-toast-icon {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(123, 45, 115, .10);
            color: var(--invoice-brand);
        }

        .invoice-toast-title {
            font-size: 13px;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .invoice-toast-message {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            line-height: 1.45;
            white-space: pre-wrap;
        }

        .invoice-toast-close {
            width: 30px;
            height: 30px;
            border: 0;
            border-radius: 10px;
            background: #f8fafc;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .invoice-toast-close:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .invoice-toast.success::before {
            background: #16a34a;
        }

        .invoice-toast.success .invoice-toast-icon {
            background: rgba(22, 163, 74, .10);
            color: #16a34a;
        }

        .invoice-toast.error::before {
            background: #dc2626;
        }

        .invoice-toast.error .invoice-toast-icon {
            background: rgba(220, 38, 38, .10);
            color: #dc2626;
        }

        .invoice-toast.warning::before {
            background: #d97706;
        }

        .invoice-toast.warning .invoice-toast-icon {
            background: rgba(217, 119, 6, .12);
            color: #d97706;
        }

        .invoice-toast.info::before {
            background: #0284c7;
        }

        .invoice-toast.info .invoice-toast-icon {
            background: rgba(2, 132, 199, .10);
            color: #0284c7;
        }

        .invoice-confirm-backdrop {
            position: fixed;
            inset: 0;
            z-index: 12000;
            background: rgba(15, 23, 42, .62);
            backdrop-filter: blur(5px);
            opacity: 0;
            visibility: hidden;
            transition: .18s ease;
        }

        .invoice-confirm-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .invoice-confirm-modal {
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -48%) scale(.98);
            z-index: 12001;
            width: min(480px, calc(100vw - 30px));
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 36px 90px rgba(2, 6, 23, .35);
            padding: 22px;
            opacity: 0;
            visibility: hidden;
            transition: .18s ease;
        }

        .invoice-confirm-modal.active {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1);
        }

        .invoice-confirm-icon {
            width: 54px;
            height: 54px;
            border-radius: 18px;
            background: rgba(220, 38, 38, .10);
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }

        .invoice-confirm-icon [data-lucide] {
            width: 24px;
            height: 24px;
        }

        .invoice-confirm-kicker {
            font-size: 10px;
            font-weight: 900;
            letter-spacing: .14em;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .invoice-confirm-content h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 950;
            color: #0f172a;
        }

        .invoice-confirm-content p {
            margin: 8px 0 0;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.55;
            color: #64748b;
            white-space: pre-wrap;
        }

        .invoice-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        @keyframes invoiceSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .invoice-spin {
            animation: invoiceSpin .9s linear infinite;
        }

        .invoice-static-text {
            white-space: pre-wrap;
            min-height: 1em;
        }



        /* =========================
               PRINT PAGINATION — A4 pages like Angebot
            ========================= */
        .invoice-print-document {
            width: 210mm;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 18px;
            align-items: center;
        }

        .invoice-print-document .invoice-print-page {
            width: 210mm !important;
            min-width: 210mm !important;
            max-width: 210mm !important;
            height: 297mm !important;
            min-height: 297mm !important;
            max-height: 297mm !important;
            margin: 0 auto 18px auto !important;
            padding: 20mm 16mm 12mm 16mm !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
            background: #fff !important;
            color: #000 !important;
            display: flex !important;
            flex-direction: column !important;
            box-shadow: 0 22px 52px rgba(15, 23, 42, .14);
            page-break-after: always;
            break-after: page;
        }

        .invoice-print-document .invoice-print-page:last-child {
            page-break-after: auto;
            break-after: auto;
            margin-bottom: 0 !important;
        }

        .invoice-print-continuation-head {
            margin-top: 18px;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--invoice-line);
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
        }

        .invoice-print-continuation-title {
            color: var(--invoice-brand);
            font-size: 15px;
            line-height: 1.2;
            font-weight: 900;
            text-transform: uppercase;
        }

        .invoice-print-continuation-meta {
            text-align: right;
            font-size: 10px;
            line-height: 1.35;
            color: #4b5563;
            font-weight: 800;
        }

        .invoice-print-page-no {
            margin-top: 5px;
            font-size: 9px;
            color: #64748b;
            text-align: right;
            font-weight: 800;
        }

        .invoice-print-final-blocks {
            margin-top: 8px;
        }

        .invoice-print-measure {
            position: fixed !important;
            left: -12000px !important;
            top: 0 !important;
            width: 210mm !important;
            visibility: hidden !important;
            pointer-events: none !important;
            z-index: -999 !important;
            gap: 0 !important;
        }

        @media(max-width: 1180px) {
            .invoice-workbench-grid {
                grid-template-columns: 1fr;
            }

            .invoice-workbench-stats {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .invoice-workbench-row {
                grid-template-columns: 34px minmax(0, 1fr);
            }

            .invoice-workbench-row .invoice-workbench-field {
                grid-column: 2 / -1;
            }
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            html,
            body {
                width: 210mm !important;
                min-height: 297mm !important;
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
                overflow: visible !important;
            }

            body * {
                visibility: hidden !important;
            }

            #invoice-print-area,
            #invoice-print-area * {
                visibility: visible !important;
            }

            #invoice-print-area {
                display: block !important;
                position: static !important;
                width: 210mm !important;
                min-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                overflow: visible !important;
            }

            #invoice-print-area .invoice-a4,
            #invoice-print-area .invoice-print-ready {
                display: flex !important;
                flex-direction: column !important;
                width: 210mm !important;
                min-width: 210mm !important;
                max-width: 210mm !important;
                min-height: 297mm !important;
                height: auto !important;
                max-height: none !important;
                margin: 0 !important;
                padding: 20mm 16mm 14mm 16mm !important;
                box-shadow: none !important;
                transform: none !important;
                overflow: visible !important;
                background: #fff !important;
                color: #000 !important;
                page-break-after: auto !important;
                break-after: auto !important;
            }

            .no-print,
            .invoice-topbar,
            .invoice-sidebar,
            .invoice-main-tabs,
            .invoice-print-backdrop,
            .invoice-print-modal,
            .invoice-confirm-backdrop,
            .invoice-confirm-modal,
            .invoice-toast-stack,
            .invoice-menu-panel,
            .invoice-row-actions,
            .invoice-direct-delete,
            .invoice-action-menu {
                display: none !important;
                visibility: hidden !important;
            }

            .invoice-shell,
            .invoice-workspace,
            .invoice-canvas-area,
            .invoice-main-panel,
            .invoice-main-panel.active {
                display: block !important;
                height: auto !important;
                min-height: 0 !important;
                overflow: visible !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #fff !important;
            }

            .invoice-table-head,
            .invoice-item-row {
                grid-template-columns: 70px minmax(0, 1fr) 58px 52px 90px 100px !important;
            }

            .invoice-item-row {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .invoice-total-box,
            .invoice-bottom-text,
            .invoice-footer {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            .invoice-footer {
                margin-top: 12mm !important;
            }

            .invoice-row-input,
            .invoice-row-desc,
            .invoice-static-text {
                border: 0 !important;
                background: transparent !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
                resize: none !important;
                box-shadow: none !important;
                outline: none !important;
            }

            .invoice-row-desc,
            .invoice-static-text {
                white-space: pre-wrap !important;
                overflow: visible !important;
            }
        }

        .invoice-sync-banner {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border-bottom: 1px solid rgba(245, 158, 11, .35);
            background:
                radial-gradient(circle at top left, rgba(245, 158, 11, .18), transparent 32%),
                linear-gradient(135deg, #fff7ed, #ffffff);
            color: #7c2d12;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .08);
            z-index: 19;
        }

        .invoice-sync-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: rgba(245, 158, 11, .16);
            color: #d97706;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .invoice-sync-content {
            flex: 1;
            min-width: 0;
        }

        .invoice-sync-title {
            font-size: 14px;
            font-weight: 900;
            color: #7c2d12;
        }

        .invoice-sync-text {
            margin-top: 2px;
            font-size: 12px;
            font-weight: 700;
            color: #9a3412;
        }

        .invoice-sync-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }

        @media(max-width: 760px) {
            .invoice-sync-banner {
                align-items: flex-start;
                flex-direction: column;
            }

            .invoice-sync-actions {
                width: 100%;
            }

            .invoice-sync-actions .invoice-btn {
                flex: 1;
            }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script>
        (function () {
            'use strict';

            const app = document.getElementById('invoice-canvas-app');
            if (!app) return;

            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

            const $ = (sel, root = document) => root.querySelector(sel);
            const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

            function cloneDeep(value) {
                try {
                    return structuredClone(value);
                } catch (e) {
                    return JSON.parse(JSON.stringify(value ?? null));
                }
            }

            function parsePayload() {
                try {
                    return JSON.parse(app.dataset.payload || '{}') || {};
                } catch (e) {
                    console.error('Invalid invoice canvas payload', e);
                    return {};
                }
            }

            const payload = parsePayload();
            const originalItems = Array.isArray(payload.items) ? cloneDeep(payload.items) : [];

            const state = {
                payload,
                items: Array.isArray(payload.items) ? cloneDeep(payload.items) : [],
                groupPositions: false,
                showHiddenInList: true,
                draggingIndex: null,
            };

            let syncDismissed = false;

            const els = {
                sidebar: $('[data-sidebar]'),
                a4Page: $('[data-a4-page]'),
                itemsRoot: $('[data-items-root]'),
                rowTemplate: $('#invoice-row-template'),

                docTitle: $('[data-doc-title]'),
                docNumber: $('[data-doc-number]'),

                companyLogo: $('[data-company-logo]'),
                companyLogoText: $('[data-company-logo-text]'),
                senderLine: $('[data-sender-line]'),

                customerAddress: $('[data-customer-address]'),
                previewType: $('[data-preview-type]'),
                previewInvoiceNo: $('[data-preview-invoice-no]'),
                previewProjectTitle: $('[data-preview-project-title]'),
                previewIssueDate: $('[data-preview-issue-date]'),
                previewContactPerson: $('[data-preview-contact-person]'),
                previewCustomerNo: $('[data-preview-customer-no]'),
                previewServicePeriod: $('[data-preview-service-period]'),
                previewGreeting: $('[data-preview-greeting]'),
                previewSubtotal: $('[data-preview-subtotal]'),
                previewTaxRate: $('[data-preview-tax-rate]'),
                previewTaxAmount: $('[data-preview-tax-amount]'),
                previewTotal: $('[data-preview-total]'),
                previewPaymentNote: $('[data-preview-payment-note]'),
                previewCompanyName: $('[data-preview-company-name]'),
                previewFooter: $('[data-preview-footer]'),

                percentageWrap: $('[data-percentage-wrap]'),
                positionListRoot: $('[data-position-list-root]'),
                positionListCount: $('[data-position-list-count]'),
                groupListRoot: $('[data-group-list-root]'),
                mainPositionRoot: $('[data-main-position-root]'),
                mainGroupRoot: $('[data-main-group-root]'),
                mainPositionBadge: $('[data-main-position-badge]'),
                mainVisibleCount: $('[data-main-visible-count]'),
                mainHiddenCount: $('[data-main-hidden-count]'),
                mainNetTotal: $('[data-main-net-total]'),
                mainGrossTotal: $('[data-main-gross-total]'),
                printBackdrop: $('[data-print-preview-backdrop]'),
                printModal: $('[data-print-preview-modal]'),
                printBody: $('[data-print-preview-body]'),
                printArea: $('[data-print-area]'),

                syncBanner: $('[data-sync-banner]'),
                syncText: $('[data-sync-text]'),
                toastStack: $('[data-toast-stack]'),
                confirmBackdrop: $('[data-confirm-backdrop]'),
                confirmModal: $('[data-confirm-modal]'),
                confirmIcon: $('[data-confirm-icon]'),
                confirmKicker: $('[data-confirm-kicker]'),
                confirmTitle: $('[data-confirm-title]'),
                confirmMessage: $('[data-confirm-message]'),
                confirmCancel: $('[data-confirm-cancel]'),
                confirmOk: $('[data-confirm-ok]'),
            };

            const fields = {};
            $$('[data-field]').forEach(el => fields[el.dataset.field] = el);

            function escapeHtml(value) {
                return String(value ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function refreshLucideIcons(root = document) {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons({ attrs: { 'stroke-width': 2.35 }, root });
                }
            }

            function toastIcon(type) {
                return ({
                    success: 'check-circle-2',
                    error: 'circle-alert',
                    warning: 'triangle-alert',
                    info: 'info',
                })[type] || 'info';
            }

            function showToast(type = 'info', title = 'Hinweis', message = '', timeout = 4200) {
                if (!els.toastStack) return;

                const toast = document.createElement('div');
                toast.className = `invoice-toast ${type}`;
                toast.innerHTML = `
                            <div class="invoice-toast-icon"><i data-lucide="${toastIcon(type)}"></i></div>
                            <div>
                                <div class="invoice-toast-title">${escapeHtml(title)}</div>
                                <div class="invoice-toast-message">${escapeHtml(message)}</div>
                            </div>
                            <button type="button" class="invoice-toast-close" aria-label="Schließen"><i data-lucide="x"></i></button>
                        `;

                const close = () => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 220);
                };

                toast.querySelector('.invoice-toast-close')?.addEventListener('click', close);
                els.toastStack.appendChild(toast);
                refreshLucideIcons(toast);
                requestAnimationFrame(() => toast.classList.add('show'));

                if (timeout) setTimeout(close, timeout);
            }

            function customConfirm({
                title = 'Aktion bestätigen',
                message = 'Bitte bestätige diese Aktion.',
                confirmText = 'Bestätigen',
                cancelText = 'Abbrechen',
                variant = 'danger',
                icon = 'triangle-alert',
            } = {}) {
                return new Promise(resolve => {
                    if (!els.confirmModal || !els.confirmBackdrop || !els.confirmOk || !els.confirmCancel) {
                        showToast('error', 'Bestätigung nicht verfügbar', 'Das Bestätigungsmodal wurde nicht gefunden.');
                        resolve(false);
                        return;
                    }

                    els.confirmTitle.textContent = title;
                    els.confirmMessage.textContent = message;
                    els.confirmKicker.textContent = variant === 'danger' ? 'Sicher löschen?' : 'Bestätigung';
                    els.confirmOk.innerHTML = `<i data-lucide="${variant === 'danger' ? 'trash-2' : 'check'}"></i> ${escapeHtml(confirmText)}`;
                    els.confirmCancel.innerHTML = `<i data-lucide="x"></i> ${escapeHtml(cancelText)}`;
                    els.confirmOk.classList.toggle('invoice-btn-danger', variant === 'danger');
                    els.confirmOk.classList.toggle('invoice-btn-primary', variant !== 'danger');
                    if (els.confirmIcon) els.confirmIcon.innerHTML = `<i data-lucide="${icon}"></i>`;

                    const cleanup = (result) => {
                        els.confirmModal.classList.remove('active');
                        els.confirmBackdrop.classList.remove('active');
                        els.confirmOk.removeEventListener('click', onOk);
                        els.confirmCancel.removeEventListener('click', onCancel);
                        els.confirmBackdrop.removeEventListener('click', onCancel);
                        document.removeEventListener('keydown', onKey);
                        resolve(result);
                    };

                    const onOk = () => cleanup(true);
                    const onCancel = () => cleanup(false);
                    const onKey = (event) => {
                        if (event.key === 'Escape') cleanup(false);
                        if (event.key === 'Enter') cleanup(true);
                    };

                    els.confirmOk.addEventListener('click', onOk);
                    els.confirmCancel.addEventListener('click', onCancel);
                    els.confirmBackdrop.addEventListener('click', onCancel);
                    document.addEventListener('keydown', onKey);

                    els.confirmBackdrop.classList.add('active');
                    els.confirmModal.classList.add('active');
                    refreshLucideIcons(els.confirmModal);
                    setTimeout(() => els.confirmCancel.focus(), 40);
                });
            }

            function cleanText(value) {
                const div = document.createElement('div');
                div.innerHTML = String(value || '');
                return (div.textContent || div.innerText || '').trim();
            }

            function money(value) {
                return Number(value || 0).toLocaleString('de-DE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                }) + ' €';
            }

            function dateOnly(value) {
                if (!value) return '';
                return String(value).slice(0, 10);
            }

            function formatDate(value) {
                const d = dateOnly(value);
                if (!d) return '';
                const parts = d.split('-');
                if (parts.length !== 3) return d;
                return `${parts[2]}.${parts[1]}.${parts[0]}`;
            }

            function isGoodColor(value) {
                return /^#[0-9a-f]{3,8}$/i.test(String(value || '').trim());
            }

            function sourcePayloadObject(item) {
                const raw = item?.source_payload;
                if (!raw) return {};
                if (typeof raw === 'object') return raw;
                if (typeof raw === 'string') {
                    try { return JSON.parse(raw) || {}; } catch (e) { return {}; }
                }
                return {};
            }

            function documentData() { return payload.document || {}; }
            function companyData() { return payload.company || {}; }
            function customerData() { return payload.customer || {}; }
            function objectData() { return payload.object || {}; }
            function auftragData() { return payload.auftrag || {}; }

            function setField(name, value) {
                if (!fields[name]) return;
                fields[name].value = value ?? '';
            }

            function getField(name) {
                return fields[name]?.value ?? '';
            }

            function normalizeItem(item, index = 0) {
                const sourcePayload = sourcePayloadObject(item);
                const invoiceCanvas = sourcePayload.invoice_canvas || {};
                const qty = Number(item.qty ?? 1);
                const unitPrice = Number(item.unit_price ?? item.price ?? 0);
                const sourceItemId = item.source_item_id ?? item.id ?? item.component_id ?? item.article_product_id ?? item.productId ?? null;

                return {
                    product_id: item.product_id ?? item.productId ?? null,
                    article_product_id: item.article_product_id ?? item.productId ?? null,
                    component_id: item.component_id ?? null,
                    distributor_id: item.distributor_id ?? null,
                    distributor_price_id: item.distributor_price_id ?? null,
                    distributor_article_no: item.distributor_article_no ?? item.article_no ?? null,
                    source_item_type: item.source_item_type ?? item.item_type ?? item.kind ?? null,
                    source_item_id: sourceItemId !== null ? String(sourceItemId) : null,
                    source_payload: sourcePayload,

                    title: item.title ?? item.name ?? 'Position',
                    description: item.description ?? cleanText(item.desc_html ?? item.desc ?? ''),
                    qty,
                    unit: item.unit ?? item.measure ?? 'Stk.',
                    unit_price: unitPrice,
                    line_total: Number(item.line_total ?? (qty * unitPrice)),
                    sort_order: Number(item.sort_order ?? index),

                    print_hidden: Boolean(item.print_hidden ?? item.hidden ?? invoiceCanvas.print_hidden ?? false),
                    group_title: item.group_title ?? invoiceCanvas.group_title ?? item.section_title ?? null,
                    product_group: item.product_group ?? invoiceCanvas.product_group ?? null,
                };
            }

            function normalizeItems(items) {
                return (Array.isArray(items) ? items : []).map((item, index) => normalizeItem(item, index));
            }

            state.items = normalizeItems(state.items);

            function initFields() {
                const doc = documentData();

                setField('type', doc.type || 'Rechnung');
                setField('invoice_mode', 'full');
                setField('percentage', 30);
                setField('issue_date', dateOnly(doc.issue_date) || new Date().toISOString().slice(0, 10));
                setField('due_date', dateOnly(doc.due_date));
                setField('service_from', dateOnly(doc.service_from));
                setField('service_to', dateOnly(doc.service_to));
                setField('payment_note', doc.payment_note || 'Zahlbar ohne Abzug bis zum angegebenen Fälligkeitsdatum.');
                setField('notes', doc.notes || '');
            }

            function applyCompanyColor() {
                const company = companyData();
                const brand = company.brand_color || company.color || company.primary_color || '#7b2d73';
                const second = company.second_color || company.secondColor || company.secondary_color || brand;
                const safeBrand = isGoodColor(brand) ? brand : '#7b2d73';
                const safeSecond = isGoodColor(second) ? second : safeBrand;

                app.style.setProperty('--invoice-brand', safeBrand);
                app.style.setProperty('--invoice-line', safeSecond);
                document.documentElement.style.setProperty('--invoice-brand', safeBrand);
                document.documentElement.style.setProperty('--invoice-line', safeSecond);
            }

            function renderCompany() {
                applyCompanyColor();

                const company = companyData();
                const name = company.name || company.company_name || 'SOLAR ASPEKT';
                const logoUrl = company.logo_url || company.logoUrl || company.brand_logo_url || '';

                if (els.companyLogo && logoUrl) {
                    els.companyLogo.src = logoUrl;
                    els.companyLogo.classList.remove('hidden');
                    els.companyLogoText?.classList.add('hidden');
                } else {
                    if (els.companyLogoText) {
                        els.companyLogoText.textContent = name;
                        els.companyLogoText.classList.remove('hidden');
                    }
                    els.companyLogo?.classList.add('hidden');
                }

                if (els.senderLine) {
                    const addressParts = [company.street, company.postcode, company.city].filter(Boolean);
                    els.senderLine.textContent = addressParts.length ? `${name}, ${addressParts.join(' ')}` : name;
                }

                if (els.previewCompanyName) els.previewCompanyName.textContent = name;

                if (els.previewFooter) {
                    const footer = company.footer;
                    if (Array.isArray(footer)) {
                        els.previewFooter.innerHTML = footer.filter(Boolean).map(x => `<div>${escapeHtml(x)}</div>`).join('');
                    } else if (footer && typeof footer === 'object') {
                        els.previewFooter.innerHTML = Object.values(footer).filter(Boolean).map(x => `<div>${escapeHtml(x)}</div>`).join('');
                    } else {
                        els.previewFooter.textContent = String(footer || name);
                    }
                }
            }

            function renderHeader() {
                const doc = documentData();
                const customer = customerData();
                const object = objectData();
                const auftrag = auftragData();
                const type = getField('type') || doc.type || 'Rechnung';

                if (els.docTitle) els.docTitle.textContent = type;
                if (els.docNumber) els.docNumber.textContent = doc.invoice_no ? `#${doc.invoice_no}` : '';
                if (els.previewType) els.previewType.textContent = type.toUpperCase();
                if (els.previewInvoiceNo) els.previewInvoiceNo.textContent = doc.invoice_no || 'Nummer wird beim Senden vergeben';
                if (els.previewProjectTitle) els.previewProjectTitle.textContent = object.name || object.full_address || auftrag.offer_no || 'Projekt';
                if (els.previewIssueDate) els.previewIssueDate.textContent = formatDate(getField('issue_date'));
                if (els.previewContactPerson) els.previewContactPerson.textContent = companyData().contact_person || companyData().contactPerson || '';
                if (els.previewCustomerNo) els.previewCustomerNo.textContent = customer.customer_no || customer.id || '';

                const from = formatDate(getField('service_from'));
                const to = formatDate(getField('service_to'));
                if (els.previewServicePeriod) els.previewServicePeriod.textContent = from && to ? `${from} - ${to}` : (from || to || '');

                if (els.previewGreeting) {
                    const name = customer.firma || customer.name || '';
                    els.previewGreeting.textContent = name ? `Sehr geehrte/r ${name},` : 'Sehr geehrte Damen und Herren,';
                }

                if (els.customerAddress) {
                    const customerName = customer.firma || customer.name || 'Kunde';
                    const street = object.street || customer.street || 'Straße';
                    const postcode = object.postcode || customer.postcode || 'PLZ';
                    const city = object.city || customer.city || 'Ort';

                    els.customerAddress.innerHTML = `
                                    <div>An die</div>
                                    <div>${escapeHtml(customerName)}</div>
                                    <div>${escapeHtml(street)}</div>
                                    <div>${escapeHtml(postcode)} ${escapeHtml(city)}</div>
                                `;
                }

                if (els.previewPaymentNote) {
                    els.previewPaymentNote.textContent = getField('payment_note') || 'Zahlbar ohne Abzug bis zum angegebenen Fälligkeitsdatum.';
                }
            }

            function itemLineTotal(item) {
                return Math.round((Number(item.qty || 0) * Number(item.unit_price || 0)) * 100) / 100;
            }

            function visibleItems() {
                return state.items.filter(item => !item.print_hidden);
            }

            function totals() {
                const subtotal = Math.round(visibleItems().reduce((sum, item) => sum + itemLineTotal(item), 0) * 100) / 100;
                const taxRate = Number(documentData().tax_rate ?? 19);
                const taxAmount = Math.round((subtotal * taxRate / 100) * 100) / 100;
                const total = Math.round((subtotal + taxAmount) * 100) / 100;

                return { subtotal, taxRate, taxAmount, total };
            }

            function renderTotals() {
                const t = totals();

                if (els.previewSubtotal) els.previewSubtotal.textContent = money(t.subtotal);
                if (els.previewTaxRate) els.previewTaxRate.textContent = Number(t.taxRate).toLocaleString('de-DE', { minimumFractionDigits: 1, maximumFractionDigits: 3 });
                if (els.previewTaxAmount) els.previewTaxAmount.textContent = money(t.taxAmount);
                if (els.previewTotal) els.previewTotal.textContent = money(t.total);
            }

            function groupKey(item) {
                return item.group_title || item.product_group || item.source_item_type || item.distributor_article_no || 'Positionen';
            }

            function moveItem(fromIndex, toIndex) {
                fromIndex = Number(fromIndex);
                toIndex = Number(toIndex);

                if (!Number.isInteger(fromIndex) || !Number.isInteger(toIndex) || fromIndex === toIndex) return;
                if (fromIndex < 0 || fromIndex >= state.items.length || toIndex < 0 || toIndex >= state.items.length) return;

                const [moved] = state.items.splice(fromIndex, 1);
                if (fromIndex < toIndex) toIndex -= 1;
                state.items.splice(toIndex, 0, moved);
                state.items = normalizeItems(state.items);
                render();
            }

            function bindDrag(element, index) {
                element.draggable = true;

                element.addEventListener('dragstart', event => {
                    state.draggingIndex = index;
                    element.classList.add('dragging');
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', String(index));
                });

                element.addEventListener('dragend', () => {
                    state.draggingIndex = null;
                    $$('.dragging, .drag-over').forEach(el => el.classList.remove('dragging', 'drag-over'));
                });

                element.addEventListener('dragover', event => {
                    event.preventDefault();
                    element.classList.add('drag-over');
                });

                element.addEventListener('dragleave', () => element.classList.remove('drag-over'));

                element.addEventListener('drop', event => {
                    event.preventDefault();
                    element.classList.remove('drag-over');
                    const from = Number(event.dataTransfer.getData('text/plain') || state.draggingIndex);
                    moveItem(from, index);
                });
            }

            function createGroupHeader(title) {
                const div = document.createElement('div');
                div.className = 'invoice-group-title-row';
                div.innerHTML = `
                                <div></div>
                                <div>${escapeHtml(title)}</div>
                                <div></div><div></div><div></div><div></div><div></div>
                            `;
                return div;
            }

            function createInvoiceRow(item, index, shownNo) {
                const row = els.rowTemplate.content.firstElementChild.cloneNode(true);
                row.dataset.index = String(index);
                row.classList.toggle('is-hidden', Boolean(item.print_hidden));
                bindDrag(row, index);

                $('[data-row-no]', row).textContent = String(shownNo);

                $$('[data-row-field]', row).forEach(input => {
                    const key = input.dataset.rowField;

                    if (key === 'line_total') {
                        input.value = itemLineTotal(item).toFixed(2);
                        input.readOnly = true;
                    } else {
                        input.value = item[key] ?? '';
                    }

                    input.addEventListener('input', () => {
                        if (key === 'line_total') return;

                        if (['qty', 'unit_price'].includes(key)) {
                            item[key] = Number(input.value || 0);
                        } else {
                            item[key] = input.value;
                        }

                        const lineInput = $('[data-row-field="line_total"]', row);
                        if (lineInput) lineInput.value = itemLineTotal(item).toFixed(2);

                        renderTotals();
                        renderHeader();
                        renderPositionPanels();
                    });
                });

                $$('[data-action]', row).forEach(btn => {
                    btn.addEventListener('click', async event => {
                        event.stopPropagation();
                        const action = btn.dataset.action;
                        if (action === 'toggle-row-menu') {
                            const menu = btn.closest('[data-action-menu]');
                            const willOpen = !menu.classList.contains('open');
                            closeAllMenus(menu);
                            menu.classList.toggle('open', willOpen);
                            return;
                        }
                        await runPositionAction(action, index);
                        closeAllMenus();
                    });
                });

                return row;
            }

            function renderItems() {
                if (!els.itemsRoot || !els.rowTemplate) return;
                els.itemsRoot.innerHTML = '';

                const indexedVisible = state.items
                    .map((item, index) => ({ item, index }))
                    .filter(x => !x.item.print_hidden);

                let no = 1;
                let lastGroup = null;

                indexedVisible.forEach(({ item, index }) => {
                    const currentGroup = groupKey(item);
                    if (state.groupPositions && currentGroup !== lastGroup) {
                        els.itemsRoot.appendChild(createGroupHeader(currentGroup));
                        lastGroup = currentGroup;
                    }

                    els.itemsRoot.appendChild(createInvoiceRow(item, index, no++));
                });

                renderTotals();
                renderPositionPanels();
            }

            function actionMenuHtml(index, hidden, source) {
                const prefix = source === 'row' ? 'row' : 'position';
                const eyeIcon = hidden ? 'eye' : 'eye-off';
                const eyeText = hidden ? 'Position einblenden' : 'Position ausblenden';
                return `
                                <div class="invoice-action-menu" data-action-menu>
                                    <button type="button" class="invoice-menu-trigger" data-action="toggle-row-menu">
                                        <i data-lucide="ellipsis-vertical"></i>
                                        <span>Aktionen</span>
                                    </button>
                                    <div class="invoice-menu-panel">
                                        <button type="button" data-action="add-before-${prefix}" data-index="${index}"><i data-lucide="arrow-up"></i> Position darüber einfügen</button>
                                        <button type="button" data-action="add-after-${prefix}" data-index="${index}"><i data-lucide="arrow-down"></i> Position darunter einfügen</button>
                                        <button type="button" data-action="toggle-hide-${prefix}" data-index="${index}"><i data-lucide="${eyeIcon}"></i> ${eyeText}</button>
                                    </div>
                                </div>
                                <button type="button" class="invoice-direct-delete" data-action="delete-${prefix}" data-index="${index}" title="Position löschen">
                                    <i data-lucide="trash-2"></i>
                                    <span>Löschen</span>
                                </button>`;
            }

            function closeAllMenus(except = null) {
                $$('[data-action-menu].open').forEach(menu => {
                    if (menu !== except) menu.classList.remove('open');
                });
            }

            async function runPositionAction(action, index) {
                index = Number(index);
                if (!Number.isInteger(index) || index < 0 || index >= state.items.length) return;

                if (action === 'add-before-position' || action === 'add-before-row') {
                    addRow(index);
                    showToast('success', 'Position eingefügt', 'Die neue Position wurde darüber eingefügt.');
                }

                if (action === 'add-after-position' || action === 'add-after-row') {
                    addRow(index + 1);
                    showToast('success', 'Position eingefügt', 'Die neue Position wurde darunter eingefügt.');
                }

                if (action === 'toggle-hide-position' || action === 'toggle-hide-row') {
                    state.items[index].print_hidden = !state.items[index].print_hidden;
                    const hidden = state.items[index].print_hidden;
                    render();
                    showToast('info', hidden ? 'Position ausgeblendet' : 'Position eingeblendet', hidden ? 'Die Position erscheint nicht in der A4-Vorschau und im Druck.' : 'Die Position erscheint wieder in der A4-Vorschau und im Druck.');
                }

                if (action === 'delete-position' || action === 'delete-row') {
                    const title = state.items[index]?.title || 'Position';
                    const ok = await customConfirm({
                        title: 'Position löschen?',
                        message: `Diese Position wird aus der Rechnung entfernt:

        ${title}`,
                        confirmText: 'Position löschen',
                        cancelText: 'Abbrechen',
                        variant: 'danger',
                        icon: 'trash-2',
                    });

                    if (!ok) return;

                    state.items.splice(index, 1);
                    render();
                    showToast('success', 'Position gelöscht', 'Die Position wurde aus der Rechnung entfernt.');
                }
            }

            function bindWorkbenchInputs(row, item, index) {
                $$('[data-main-field]', row).forEach(input => {
                    const key = input.dataset.mainField;
                    input.value = key === 'line_total' ? itemLineTotal(item).toFixed(2) : (item[key] ?? '');
                    input.addEventListener('input', () => {
                        if (key === 'line_total') return;
                        if (['qty', 'unit_price'].includes(key)) item[key] = Number(input.value || 0);
                        else item[key] = input.value;
                        const totalInput = $('[data-main-field="line_total"]', row);
                        if (totalInput) totalInput.value = itemLineTotal(item).toFixed(2);
                        renderItems();
                    });
                });
            }

            function renderMainPositionWorkbench() {
                const t = totals();
                const visibleCount = state.items.filter(x => !x.print_hidden).length;
                const hiddenCount = state.items.length - visibleCount;

                if (els.mainPositionBadge) els.mainPositionBadge.textContent = String(state.items.length);
                if (els.mainVisibleCount) els.mainVisibleCount.textContent = String(visibleCount);
                if (els.mainHiddenCount) els.mainHiddenCount.textContent = String(hiddenCount);
                if (els.mainNetTotal) els.mainNetTotal.textContent = money(t.subtotal);
                if (els.mainGrossTotal) els.mainGrossTotal.textContent = money(t.total);

                if (els.mainPositionRoot) {
                    els.mainPositionRoot.innerHTML = state.items.map((item, index) => `
                                    <div class="invoice-workbench-row ${item.print_hidden ? 'is-hidden' : ''}" data-main-position-row data-index="${index}" draggable="true">
                                        <div class="invoice-drag-handle" title="Ziehen zum Sortieren"><i data-lucide="grip-vertical"></i></div>
                                        <div class="invoice-workbench-field">
                                            <label>Position / Beschreibung</label>
                                            <input class="invoice-workbench-input" data-main-field="title" value="${escapeHtml(item.title || '')}">
                                            <textarea class="invoice-workbench-textarea" data-main-field="description">${escapeHtml(item.description || '')}</textarea>
                                            <div class="invoice-position-meta">
                                                ${item.distributor_article_no ? 'Art.-Nr.: ' + escapeHtml(item.distributor_article_no) + ' · ' : ''}
                                                ${item.source_item_type ? 'Quelle: ' + escapeHtml(item.source_item_type) + ' · ' : ''}
                                                ${item.source_item_id ? 'ID: ' + escapeHtml(item.source_item_id) : ''}
                                            </div>
                                        </div>
                                        <div class="invoice-workbench-field"><label>Menge</label><input type="number" step="0.01" class="invoice-workbench-input text-right" data-main-field="qty" value="${escapeHtml(item.qty || 0)}"></div>
                                        <div class="invoice-workbench-field"><label>Einheit</label><input class="invoice-workbench-input" data-main-field="unit" value="${escapeHtml(item.unit || '')}"></div>
                                        <div class="invoice-workbench-field"><label>Einzelpreis</label><input type="number" step="0.01" class="invoice-workbench-input text-right" data-main-field="unit_price" value="${escapeHtml(item.unit_price || 0)}"></div>
                                        <div class="invoice-workbench-field"><label>Gesamt</label><input readonly class="invoice-workbench-input text-right" data-main-field="line_total" value="${escapeHtml(itemLineTotal(item).toFixed(2))}"></div>
                                        <div class="invoice-workbench-field"><label>Menü</label>${actionMenuHtml(index, item.print_hidden, 'position')}</div>
                                    </div>
                                `).join('') || '<div class="invoice-mini-hint">Keine Positionen vorhanden.</div>';

                    $$('[data-main-position-row]', els.mainPositionRoot).forEach(row => {
                        const index = Number(row.dataset.index);
                        bindDrag(row, index);
                        bindWorkbenchInputs(row, state.items[index], index);
                    });
                }

                if (els.mainGroupRoot) {
                    els.mainGroupRoot.innerHTML = buildGroupCardsHtml();
                }
            }

            function buildGroupCardsHtml() {
                const groups = new Map();
                state.items.forEach(item => {
                    const key = groupKey(item);
                    if (!groups.has(key)) groups.set(key, { total: 0, visible: 0, amount: 0 });
                    const g = groups.get(key);
                    g.total++;
                    if (!item.print_hidden) {
                        g.visible++;
                        g.amount += itemLineTotal(item);
                    }
                });

                return Array.from(groups.entries()).map(([key, g]) => `
                                <div class="invoice-group-card">
                                    <b>${escapeHtml(key)}</b>
                                    <span>${g.visible}/${g.total} sichtbar · ${escapeHtml(money(g.amount))}</span>
                                </div>
                            `).join('') || '<div class="invoice-mini-hint">Keine Gruppen vorhanden.</div>';
            }

            function renderPositionPanels() {
                if (els.positionListCount) {
                    const visibleCount = state.items.filter(x => !x.print_hidden).length;
                    const hiddenCount = state.items.length - visibleCount;
                    els.positionListCount.textContent = `${visibleCount} sichtbar / ${hiddenCount} ausgeblendet`;
                }

                if (els.positionListRoot) {
                    const itemsToShow = state.showHiddenInList ? state.items : state.items.filter(x => !x.print_hidden);
                    els.positionListRoot.innerHTML = itemsToShow.map(item => {
                        const index = state.items.indexOf(item);
                        return `
                                        <div class="invoice-position-card ${item.print_hidden ? 'is-hidden' : ''}" data-position-card data-index="${index}" draggable="true">
                                            <div class="invoice-position-card-head">
                                                <div class="invoice-position-drag" title="Ziehen zum Sortieren"><i data-lucide="grip-vertical"></i></div>
                                                <div style="min-width:0;flex:1;">
                                                    <div class="invoice-position-title">${escapeHtml(index + 1)}. ${escapeHtml(item.title || 'Position')}</div>
                                                    <div class="invoice-position-meta">
                                                        ${escapeHtml(item.qty || 0)} ${escapeHtml(item.unit || '')} × ${escapeHtml(money(item.unit_price || 0))}<br>
                                                        ${item.distributor_article_no ? 'Art.-Nr.: ' + escapeHtml(item.distributor_article_no) + ' · ' : ''}
                                                        ${item.source_item_type ? 'Quelle: ' + escapeHtml(item.source_item_type) : ''}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="invoice-position-actions">
                                                ${actionMenuHtml(index, item.print_hidden, 'position')}
                                            </div>
                                        </div>`;
                    }).join('') || '<div class="invoice-mini-hint">Keine Positionen vorhanden.</div>';

                    $$('[data-position-card]', els.positionListRoot).forEach(card => {
                        bindDrag(card, Number(card.dataset.index));
                    });
                }

                if (els.groupListRoot) {
                    const groups = new Map();
                    state.items.forEach(item => {
                        const key = groupKey(item);
                        if (!groups.has(key)) groups.set(key, { total: 0, visible: 0, amount: 0 });
                        const g = groups.get(key);
                        g.total++;
                        if (!item.print_hidden) {
                            g.visible++;
                            g.amount += itemLineTotal(item);
                        }
                    });

                    els.groupListRoot.innerHTML = buildGroupCardsHtml();
                }

                renderMainPositionWorkbench();
            }

            function renderSyncBanner() {
                const sync = state.payload?.auftrag_sync || {};
                if (!els.syncBanner) return;

                const shouldShow = Boolean(sync.available && sync.changed && !syncDismissed);
                els.syncBanner.classList.toggle('hidden', !shouldShow);

                if (!shouldShow || !els.syncText) return;

                const invoiceCount = Number(sync.invoice_item_count || state.items.length || 0);
                const latestCount = Number(sync.latest_item_count || 0);
                const lastSync = sync.source_offer_synced_at
                    ? `Letzte Synchronisierung: ${escapeHtml(sync.source_offer_synced_at)}`
                    : 'Diese Rechnung wurde noch nicht mit dem aktuellen Auftrag synchronisiert.';

                els.syncText.innerHTML = `
                                Die Auftrag-Positionen wurden geändert.
                                Rechnung: <strong>${invoiceCount}</strong> Positionen ·
                                Auftrag aktuell: <strong>${latestCount}</strong> Positionen.<br>
                                ${lastSync}
                            `;
            }

            async function syncAuftragPositions(btn) {
                const sync = state.payload?.auftrag_sync || {};
                const url = sync.sync_url;

                if (!url) {
                    showToast('error', 'Sync-URL fehlt', 'Bitte prüfe, ob die Route invoices.canvas.sync-auftrag existiert.');
                    return;
                }

                const ok = await customConfirm({
                    title: 'Auftrag synchronisieren?',
                    message: 'Die aktuellen Rechnungspositionen werden durch die neuesten Auftrag-Positionen ersetzt. Diese Aktion kann die manuell bearbeiteten Positionen überschreiben.',
                    confirmText: 'Jetzt synchronisieren',
                    cancelText: 'Abbrechen',
                    variant: 'primary',
                    icon: 'refresh-cw',
                });
                if (!ok) return;

                const oldHtml = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<i data-lucide="loader-circle" class="invoice-spin"></i> Synchronisiere...';
                refreshLucideIcons(btn);

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ sync: true }),
                    });

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok || !data.ok) {
                        throw new Error(data.message || 'Synchronisierung fehlgeschlagen.');
                    }

                    state.items = normalizeItems(data.items || []);
                    state.payload.items = cloneDeep(state.items);
                    state.payload.auftrag_sync = data.auftrag_sync || {
                        available: true,
                        changed: false,
                    };

                    syncDismissed = false;
                    render();
                    showToast('success', 'Synchronisiert', data.message || 'Rechnung wurde mit dem aktuellen Auftrag synchronisiert.');
                } catch (error) {
                    showToast('error', 'Synchronisierung fehlgeschlagen', error.message || 'Synchronisierung fehlgeschlagen.');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = oldHtml;
                }
            }

            function applyPercentage() {
                const percentage = Number(getField('percentage') || 0);
                const auftrag = auftragData();
                const baseNet = Number(auftrag.total_net || totals().subtotal || 0);
                const amount = Math.round(baseNet * percentage) / 100;

                state.items = [normalizeItem({
                    product_id: null,
                    title: `${percentage}% Anzahlung${auftrag.offer_no ? ' für Auftrag ' + auftrag.offer_no : ''}`,
                    description: null,
                    qty: 1,
                    unit: 'psch',
                    unit_price: amount,
                    group_title: 'Abschlag',
                })];

                render();
            }

            function reloadFullPositions() {
                state.items = normalizeItems(originalItems);
                render();
            }

            function addRow(insertAt = null) {
                const item = normalizeItem({
                    title: 'Neue Position',
                    description: '',
                    qty: 1,
                    unit: 'Stk.',
                    unit_price: 0,
                    source_item_type: 'manual',
                    group_title: 'Manuelle Positionen',
                }, state.items.length);

                if (insertAt === null || insertAt === undefined || insertAt < 0 || insertAt > state.items.length) {
                    state.items.push(item);
                } else {
                    state.items.splice(Number(insertAt), 0, item);
                }

                render();
            }

            function enrichSourcePayload(item) {
                const payload = sourcePayloadObject(item);
                return {
                    ...payload,
                    invoice_canvas: {
                        ...(payload.invoice_canvas || {}),
                        print_hidden: Boolean(item.print_hidden),
                        group_title: item.group_title || null,
                        product_group: item.product_group || null,
                    }
                };
            }

            function buildSavePayload() {
                return {
                    type: getField('type') || 'Rechnung',
                    status: documentData().status || 'draft',
                    invoice_mode: getField('invoice_mode') || 'full',
                    percentage: Number(getField('percentage') || 0),
                    issue_date: getField('issue_date') || null,
                    due_date: getField('due_date') || null,
                    service_from: getField('service_from') || null,
                    service_to: getField('service_to') || null,
                    payment_note: getField('payment_note') || null,
                    notes: getField('notes') || null,
                    items: state.items.map((item, index) => ({
                        product_id: item.product_id ?? null,
                        article_product_id: item.article_product_id ?? null,
                        component_id: item.component_id ?? null,
                        distributor_id: item.distributor_id ?? null,
                        distributor_price_id: item.distributor_price_id ?? null,
                        distributor_article_no: item.distributor_article_no ?? null,
                        source_item_type: item.source_item_type ?? null,
                        source_item_id: item.source_item_id !== null && item.source_item_id !== undefined ? String(item.source_item_id) : null,
                        source_payload: enrichSourcePayload(item),
                        print_hidden: Boolean(item.print_hidden),
                        group_title: item.group_title || null,

                        title: item.title || 'Position',
                        description: item.description || null,
                        qty: Number(item.qty || 1),
                        unit: item.unit || null,
                        unit_price: Number(item.unit_price || 0),
                        line_total: itemLineTotal(item),
                        sort_order: index,
                    })),
                };
            }

            async function save() {
                const saveUrl = app.dataset.saveUrl;
                if (!saveUrl || saveUrl === '#') {
                    showToast('error', 'Speicher-URL fehlt', 'Bitte prüfe die data-save-url im Canvas.');
                    return;
                }

                const res = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(buildSavePayload()),
                });

                const data = await res.json().catch(() => ({}));

                if (!res.ok || !data.ok) {
                    const message = data.message || Object.values(data.errors || {}).flat().join('\n') || 'Speichern fehlgeschlagen.';
                    throw new Error(message);
                }

                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                    return;
                }

                showToast('success', 'Gespeichert', data.message || 'Rechnung wurde gespeichert.');
            }

            function staticizePreviewForm(clone) {
                $$('input, textarea, select', clone).forEach(el => {
                    const div = document.createElement('div');
                    div.className = 'invoice-static-text';
                    div.textContent = el.value || '';
                    el.replaceWith(div);
                });
                $$('.no-print, .invoice-row-actions, .invoice-action-menu, .invoice-menu-panel', clone).forEach(el => el.remove());
            }

            function printableClone() {
                render();
                const clone = els.a4Page.cloneNode(true);
                staticizePreviewForm(clone);
                clone.classList.add('invoice-print-ready');
                return clone;
            }

            function clonePrintNode(source, selector) {
                const node = selector ? $(selector, source) : source;
                return node ? node.cloneNode(true) : null;
            }

            function preparePrintPage(page) {
                page.classList.add('invoice-print-page', 'invoice-print-ready');
                page.style.width = '210mm';
                page.style.minWidth = '210mm';
                page.style.maxWidth = '210mm';
                page.style.height = '297mm';
                page.style.minHeight = '297mm';
                page.style.maxHeight = '297mm';
                page.style.margin = '0 auto 18px auto';
                page.style.padding = '20mm 16mm 12mm 16mm';
                page.style.boxShadow = 'none';
                page.style.transform = 'none';
                page.style.overflow = 'hidden';
                page.style.pageBreakAfter = 'always';
                page.style.breakAfter = 'page';
                return page;
            }

            function continuationHeaderHtml(pageNo) {
                const doc = documentData();
                const type = escapeHtml((getField('type') || doc.type || 'Rechnung').toUpperCase());
                const invoiceNo = escapeHtml(doc.invoice_no || 'Nummer wird beim Senden vergeben');
                const project = escapeHtml(objectData().name || objectData().full_address || auftragData().offer_no || 'Projekt');

                return `
                            <section class="invoice-print-continuation-head">
                                <div>
                                    <div class="invoice-print-continuation-title">${type}</div>
                                    <div class="invoice-number" style="font-size:15px;margin-top:4px;">${invoiceNo}</div>
                                </div>
                                <div class="invoice-print-continuation-meta">
                                    <div>Fortsetzung · Seite ${pageNo}</div>
                                    <div>${project}</div>
                                </div>
                            </section>
                        `;
            }

            function buildPrintPageFactory(source) {
                const sourceHeader = clonePrintNode(source, '.invoice-letter-head');
                const sourceAddress = clonePrintNode(source, '.invoice-address-grid');
                const sourceDocGrid = clonePrintNode(source, '.invoice-doc-grid');
                const sourceIntro = clonePrintNode(source, '.invoice-intro');
                const sourceTableHead = clonePrintNode(source, '.invoice-table-head');
                const sourceFooter = clonePrintNode(source, '.invoice-footer');

                return function createPage(pageNo, firstPage = false) {
                    const page = preparePrintPage(document.createElement('div'));
                    page.className = `a4-page invoice-a4 invoice-print-ready invoice-print-page${firstPage ? ' is-first-page' : ' is-continuation-page'}`;
                    page.dataset.printPage = String(pageNo);

                    if (sourceHeader) page.appendChild(sourceHeader.cloneNode(true));

                    if (firstPage) {
                        if (sourceAddress) page.appendChild(sourceAddress.cloneNode(true));
                        if (sourceDocGrid) page.appendChild(sourceDocGrid.cloneNode(true));
                        if (sourceIntro) page.appendChild(sourceIntro.cloneNode(true));
                    } else {
                        const continuation = document.createElement('div');
                        continuation.innerHTML = continuationHeaderHtml(pageNo);
                        page.appendChild(continuation.firstElementChild);
                    }

                    const itemsSection = document.createElement('section');
                    itemsSection.className = 'invoice-items-section';

                    if (sourceTableHead) {
                        itemsSection.appendChild(sourceTableHead.cloneNode(true));
                    }

                    const itemsRoot = document.createElement('div');
                    itemsRoot.dataset.printItemsRoot = '1';
                    itemsSection.appendChild(itemsRoot);
                    page.appendChild(itemsSection);

                    const footer = sourceFooter
                        ? sourceFooter.cloneNode(true)
                        : document.createElement('footer');

                    footer.classList.add('invoice-footer');
                    footer.dataset.printFooter = '1';
                    page.appendChild(footer);

                    return { page, itemsRoot, footer };
                };
            }

            function pageIsOverflowing(page) {
                // 2px tolerance prevents Chrome sub-pixel rounding from creating extra pages.
                return page.scrollHeight > page.clientHeight + 2;
            }

            function addPageNumberLabels(pages) {
                const total = pages.length;
                pages.forEach((entry, idx) => {
                    const pageNo = idx + 1;
                    entry.page.dataset.printPage = String(pageNo);
                    entry.page.style.pageBreakAfter = pageNo === total ? 'auto' : 'always';
                    entry.page.style.breakAfter = pageNo === total ? 'auto' : 'page';

                    let label = entry.footer.querySelector('.invoice-print-page-no');
                    if (!label) {
                        label = document.createElement('div');
                        label.className = 'invoice-print-page-no';
                        entry.footer.appendChild(label);
                    }

                    label.textContent = `Seite ${pageNo} / ${total}`;
                });
            }

            function buildPaginatedPrintDocument() {
                render();

                const source = printableClone();
                const printDoc = document.createElement('div');
                printDoc.className = 'invoice-print-document';

                const rowSourceRoot = $('[data-items-root]', source);
                const printableRows = rowSourceRoot
                    ? Array.from(rowSourceRoot.children).map(row => row.cloneNode(true))
                    : [];

                const finalBlocks = document.createElement('div');
                finalBlocks.className = 'invoice-print-final-blocks';

                const totals = clonePrintNode(source, '.invoice-total-box');
                const bottom = clonePrintNode(source, '.invoice-bottom-text');

                if (totals) finalBlocks.appendChild(totals);
                if (bottom) finalBlocks.appendChild(bottom);

                const createPage = buildPrintPageFactory(source);

                const measure = document.createElement('div');
                measure.className = 'invoice-print-document invoice-print-measure';
                document.body.appendChild(measure);

                const pages = [];
                let current = createPage(1, true);
                pages.push(current);
                measure.appendChild(current.page);

                function startNewPage() {
                    current = createPage(pages.length + 1, false);
                    pages.push(current);
                    measure.appendChild(current.page);
                }

                printableRows.forEach(row => {
                    const candidate = row.cloneNode(true);
                    current.itemsRoot.appendChild(candidate);

                    if (pageIsOverflowing(current.page) && current.itemsRoot.children.length > 1) {
                        current.itemsRoot.removeChild(candidate);
                        startNewPage();
                        current.itemsRoot.appendChild(candidate);
                    }
                });

                if (finalBlocks.children.length) {
                    current.page.insertBefore(finalBlocks, current.footer);

                    if (pageIsOverflowing(current.page) && current.itemsRoot.children.length > 0) {
                        finalBlocks.remove();
                        startNewPage();
                        current.page.insertBefore(finalBlocks, current.footer);
                    }
                }

                addPageNumberLabels(pages);

                pages.forEach(entry => {
                    printDoc.appendChild(entry.page.cloneNode(true));
                });

                measure.remove();

                return printDoc;
            }

            function openPrintPreview() {
                if (!els.printModal || !els.printBody || !els.a4Page) return;

                els.printBody.innerHTML = '';
                els.printBody.appendChild(buildPaginatedPrintDocument());
                refreshLucideIcons(els.printBody);
                els.printBackdrop?.classList.add('active');
                els.printModal.classList.add('active');
            }

            function closePrintPreview() {
                els.printBackdrop?.classList.remove('active');
                els.printModal?.classList.remove('active');
            }

            function collectPrintableStyles() {
                const styles = [];

                document.querySelectorAll('style').forEach(style => {
                    if (style.textContent && style.textContent.trim()) {
                        styles.push(`<style>${style.textContent}</style>`);
                    }
                });

                document.querySelectorAll('link[rel="stylesheet"]').forEach(link => {
                    const href = link.getAttribute('href');
                    if (href) styles.push(`<link rel="stylesheet" href="${escapeHtml(href)}">`);
                });

                styles.push(`
                            <style>
                                @page { size: A4 portrait; margin: 0; }

                                * {
                                    -webkit-print-color-adjust: exact !important;
                                    print-color-adjust: exact !important;
                                    box-sizing: border-box !important;
                                }

                                html, body {
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    background: #ffffff !important;
                                    width: 210mm !important;
                                    min-height: 297mm !important;
                                    overflow: visible !important;
                                }

                                html, body, body * {
                                    visibility: visible !important;
                                }

                                body {
                                    display: block !important;
                                }

                                .no-print,
                                .invoice-row-actions,
                                .invoice-direct-delete,
                                .invoice-action-menu,
                                .invoice-menu-panel,
                                .invoice-print-backdrop,
                                .invoice-print-modal,
                                .invoice-confirm-backdrop,
                                .invoice-confirm-modal,
                                .invoice-toast-stack,
                                [data-lucide] {
                                    display: none !important;
                                }

                                .invoice-print-document {
                                    display: block !important;
                                    width: 210mm !important;
                                    margin: 0 !important;
                                    padding: 0 !important;
                                    background: #fff !important;
                                }

                                .invoice-print-document .invoice-print-page,
                                .invoice-print-document .invoice-a4 {
                                    width: 210mm !important;
                                    min-width: 210mm !important;
                                    max-width: 210mm !important;
                                    height: 297mm !important;
                                    min-height: 297mm !important;
                                    max-height: 297mm !important;
                                    margin: 0 !important;
                                    padding: 20mm 16mm 12mm 16mm !important;
                                    box-shadow: none !important;
                                    transform: none !important;
                                    overflow: hidden !important;
                                    background: #ffffff !important;
                                    color: #000000 !important;
                                    display: flex !important;
                                    flex-direction: column !important;
                                    page-break-after: always !important;
                                    break-after: page !important;
                                }

                                .invoice-print-document .invoice-print-page:last-child,
                                .invoice-print-document .invoice-a4:last-child {
                                    page-break-after: auto !important;
                                    break-after: auto !important;
                                }

                                .invoice-table-head,
                                .invoice-item-row,
                                .invoice-group-title-row {
                                    grid-template-columns: 70px minmax(0, 1fr) 58px 52px 90px 100px !important;
                                }

                                .invoice-row-input,
                                .invoice-row-desc,
                                .invoice-static-text {
                                    border: 0 !important;
                                    background: transparent !important;
                                    padding-left: 0 !important;
                                    padding-right: 0 !important;
                                    resize: none !important;
                                    box-shadow: none !important;
                                    outline: none !important;
                                }

                                .invoice-row-desc,
                                .invoice-static-text {
                                    white-space: pre-wrap !important;
                                    overflow: visible !important;
                                }

                                .invoice-letter-head,
                                .invoice-items-section,
                                .invoice-table-head,
                                .invoice-item-row,
                                .invoice-group-title-row,
                                .invoice-total-box,
                                .invoice-bottom-text,
                                .invoice-footer,
                                .invoice-print-continuation-head {
                                    break-inside: avoid !important;
                                    page-break-inside: avoid !important;
                                }

                                .invoice-footer {
                                    margin-top: auto !important;
                                }

                                @media print {
                                    html, body, body * {
                                        visibility: visible !important;
                                    }

                                    .invoice-print-document .invoice-print-page,
                                    .invoice-print-document .invoice-a4 {
                                        page-break-after: always !important;
                                        break-after: page !important;
                                    }

                                    .invoice-print-document .invoice-print-page:last-child,
                                    .invoice-print-document .invoice-a4:last-child {
                                        page-break-after: auto !important;
                                        break-after: auto !important;
                                    }
                                }
                            </style>
                        `);

                return styles.join('\n');
            }

            function buildIsolatedPrintHtml() {
                const paginatedDocument = buildPaginatedPrintDocument();

                return `<!doctype html>
        <html lang="de">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Druckvorschau Rechnung</title>
            ${collectPrintableStyles()}
        </head>
        <body>
            ${paginatedDocument.outerHTML}
        </body>
        </html>`;
            }

            function waitForPrintDocumentAssets(doc) {
                const images = Array.from(doc.images || []);
                if (!images.length) {
                    return Promise.resolve();
                }

                return Promise.all(images.map(img => {
                    if (img.complete) return Promise.resolve();
                    return new Promise(resolve => {
                        img.onload = resolve;
                        img.onerror = resolve;
                    });
                })).then(() => undefined);
            }

            async function printWithPrintWindow(html) {
                const printWindow = window.open('', 'invoice_print_window', 'width=980,height=1200,menubar=no,toolbar=no,location=no,status=no,scrollbars=yes,resizable=yes');

                if (!printWindow) {
                    throw new Error('Der Druck-Tab wurde vom Browser blockiert. Bitte Popups für diese Seite erlauben.');
                }

                printWindow.document.open();
                printWindow.document.write(html);
                printWindow.document.close();

                await new Promise(resolve => {
                    if (printWindow.document.readyState === 'complete') {
                        resolve();
                        return;
                    }
                    printWindow.onload = resolve;
                    setTimeout(resolve, 700);
                });

                await waitForPrintDocumentAssets(printWindow.document);
                await new Promise(resolve => setTimeout(resolve, 450));

                printWindow.focus();
                printWindow.print();

                // Do not close immediately. Chrome/Safari sometimes cancel printing if the window closes too fast.
                printWindow.onafterprint = () => {
                    setTimeout(() => {
                        try { printWindow.close(); } catch (e) { }
                    }, 500);
                };
            }

            async function printFromPreview() {
                if (!els.a4Page) {
                    showToast('error', 'Drucken nicht möglich', 'Die A4-Vorschau wurde nicht gefunden.');
                    return;
                }

                const html = buildIsolatedPrintHtml();

                try {
                    await printWithPrintWindow(html);
                } catch (e) {
                    console.error(e);
                    showToast('error', 'Druck fehlgeschlagen', e.message || 'Die Druckansicht konnte nicht erstellt werden.');
                }
            }

            function bindActions() {
                document.addEventListener('click', async (event) => {
                    const menuTrigger = event.target.closest('[data-action="toggle-row-menu"]');
                    if (menuTrigger) {
                        event.preventDefault();
                        const menu = menuTrigger.closest('[data-action-menu]');
                        const willOpen = !menu.classList.contains('open');
                        closeAllMenus(menu);
                        menu.classList.toggle('open', willOpen);
                        return;
                    }

                    const positionAction = event.target.closest('[data-action="add-before-position"],[data-action="add-after-position"],[data-action="toggle-hide-position"],[data-action="delete-position"],[data-action="add-before-row"],[data-action="add-after-row"],[data-action="toggle-hide-row"],[data-action="delete-row"]');
                    if (positionAction) {
                        const rowIndex = positionAction.dataset.index || positionAction.closest('[data-index]')?.dataset.index || positionAction.closest('[data-row]')?.dataset.index;
                        await runPositionAction(positionAction.dataset.action, rowIndex);
                        closeAllMenus();
                        return;
                    }

                    const mainViewTab = event.target.closest('[data-main-view-tab]');
                    if (mainViewTab) {
                        const view = mainViewTab.dataset.mainViewTab;
                        $$('[data-main-view-tab]').forEach(btn => btn.classList.toggle('active', btn === mainViewTab));
                        $$('[data-main-view-panel]').forEach(panel => panel.classList.toggle('active', panel.dataset.mainViewPanel === view));
                        renderPositionPanels();
                        return;
                    }

                    const tabBtn = event.target.closest('[data-position-tab-btn]');
                    if (tabBtn) {
                        const tab = tabBtn.dataset.positionTabBtn;
                        $$('[data-position-tab-btn]').forEach(btn => btn.classList.toggle('active', btn === tabBtn));
                        $$('[data-position-tab-panel]').forEach(panel => panel.classList.toggle('active', panel.dataset.positionTabPanel === tab));
                        renderPositionPanels();
                        return;
                    }

                    const btn = event.target.closest('[data-action]');
                    if (!btn) return;

                    const action = btn.dataset.action;

                    if (action === 'toggle-sidebar') {
                        app.classList.toggle('sidebar-open');
                        els.sidebar?.classList.toggle('active');
                    }

                    if (action === 'dismiss-sync') {
                        syncDismissed = true;
                        renderSyncBanner();
                    }

                    if (action === 'sync-auftrag') {
                        await syncAuftragPositions(btn);
                    }

                    if (action === 'print-preview') openPrintPreview();
                    if (action === 'close-print-preview') closePrintPreview();
                    if (action === 'print-from-preview') printFromPreview();

                    if (action === 'save') {
                        btn.disabled = true;
                        try {
                            await save();
                        } catch (e) {
                            showToast('error', 'Speichern fehlgeschlagen', e.message || 'Speichern fehlgeschlagen.');
                        } finally {
                            btn.disabled = false;
                        }
                    }

                    if (action === 'add-row') { addRow(); showToast('success', 'Position hinzugefügt', 'Eine neue Position wurde am Ende eingefügt.'); }
                    if (action === 'apply-percentage') { applyPercentage(); showToast('success', 'Prozent berechnet', 'Die Prozentposition wurde erstellt.'); }
                    if (action === 'reload-full') { reloadFullPositions(); showToast('info', 'Positionen geladen', 'Die ursprünglichen Auftrag-Positionen wurden geladen.'); }
                });

                document.addEventListener('click', event => {
                    if (!event.target.closest('[data-action-menu]')) closeAllMenus();
                });

                window.addEventListener('afterprint', () => {
                    if (els.printArea) els.printArea.innerHTML = '';
                });

                document.addEventListener('change', event => {
                    const toggle = event.target.closest('[data-toggle]');
                    if (!toggle) return;

                    if (toggle.dataset.toggle === 'group-positions' || toggle.dataset.toggle === 'group-positions-main') {
                        state.groupPositions = toggle.checked;
                        $$('[data-toggle="group-positions"], [data-toggle="group-positions-main"]').forEach(el => { el.checked = toggle.checked; });
                        render();
                    }

                    if (toggle.dataset.toggle === 'show-hidden-list') {
                        state.showHiddenInList = toggle.checked;
                        renderPositionPanels();
                    }
                });

                Object.values(fields).forEach(el => {
                    const update = () => {
                        renderHeader();
                        if (el.dataset.field === 'invoice_mode') {
                            els.percentageWrap?.classList.toggle('hidden', el.value !== 'percentage');
                        }
                    };
                    el.addEventListener('input', update);
                    el.addEventListener('change', update);
                });

                els.printBackdrop?.addEventListener('click', closePrintPreview);
                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape') closePrintPreview();
                });
            }

            function render() {
                renderCompany();
                renderHeader();
                renderItems();
                renderTotals();
                renderPositionPanels();
                renderSyncBanner();

                if (fields.invoice_mode && els.percentageWrap) {
                    els.percentageWrap.classList.toggle('hidden', fields.invoice_mode.value !== 'percentage');
                }

                refreshLucideIcons(app);
            }

            initFields();
            bindActions();
            render();
            refreshLucideIcons(document);
        })();
    </script>
@endpush