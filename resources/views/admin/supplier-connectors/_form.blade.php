@php
    $requestConfig = is_array($connection->request_config ?? null)
        ? $connection->request_config
        : [];

    $importConfig = is_array($connection->import_config ?? null)
        ? $connection->import_config
        : [];

    $extraAuthData = is_array($connection->extra_auth_data ?? null)
        ? $connection->extra_auth_data
        : [];

    $supplierKey = strtolower((string) ($connection->supplier_key ?? ''));
    $connectionName = strtolower((string) ($connection->name ?? ''));
    $endpointUrl = strtolower((string) ($connection->endpoint_url ?? ''));

    $isSonepar = str_contains($supplierKey, 'sonepar')
        || str_contains($connectionName, 'sonepar')
        || str_contains($endpointUrl, 'sonepar.de');

    $isGcOnline = str_contains($supplierKey, 'gc')
        || str_contains($supplierKey, 'gc_online')
        || str_contains($connectionName, 'gc online')
        || str_contains($connectionName, 'gc-online');

    $isFega = str_contains($supplierKey, 'fega')
        || str_contains($supplierKey, 'schmitt')
        || str_contains($connectionName, 'fega')
        || str_contains($connectionName, 'schmitt')
        || str_contains($endpointUrl, 'fega');

    $defaultMethod = $isSonepar || $isGcOnline || $isFega
        ? 'POST'
        : ($requestConfig['method'] ?? 'GET');

    $defaultOpenMode = $isSonepar
        ? 'manual_post'
        : ($requestConfig['open_mode'] ?? ($isGcOnline ? 'search_then_auto_post' : 'auto_post_form'));

    $defaultContentType = $isSonepar
        ? 'multipart'
        : ($requestConfig['content_type'] ?? ($isGcOnline || $isFega ? 'form' : ''));

    $defaultBasketParam = $requestConfig['basket_param'] ?? 'warenkorb';

    $defaultSearchParam = $isSonepar
        ? ''
        : ($requestConfig['search_param'] ?? ($isGcOnline ? 'searchterm' : ''));

    $defaultAccept = $isSonepar
        ? '*/*'
        : ($requestConfig['accept'] ?? '');

    $defaultAuthMap = $requestConfig['auth_param_map'] ?? [];

    if (empty($defaultAuthMap) && $isSonepar) {
        $defaultAuthMap = [
            'kndnr' => '{customer_number}',
            'name_kunde' => '{username}',
            'pw_kunde' => '{password}',
            'hookurl' => '{callback_url}',
        ];
    }

    $defaultExtraParams = $requestConfig['extra_params'] ?? [];

    if (empty($defaultExtraParams) && $isSonepar) {
        $defaultExtraParams = [
            'OrganizationId' => '1',
            'action' => 'WKE',
            'Version' => '2.0',
            'target' => 'TOP',
        ];
    }
@endphp

<style>
    .sc-page {
        padding: 24px;
        background: #f8fafc;
        min-height: calc(100vh - 80px);
    }

    .sc-section {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
        margin-bottom: 18px;
    }

    .sc-section-head {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .sc-section-icon {
        width: 38px;
        height: 38px;
        border-radius: 14px;
        background: rgba(116, 178, 212, .13);
        color: #16729f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .sc-section-title {
        font-size: 17px;
        font-weight: 900;
        color: #111827;
        margin: 0;
    }

    .sc-section-desc {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
        margin-top: 4px;
    }

    .sc-info-box {
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

    .sc-warning-box {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .sc-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .sc-full {
        grid-column: 1 / -1;
    }

    .sc-label {
        display: block;
        font-size: 13px;
        font-weight: 900;
        color: #374151;
        margin-bottom: 6px;
    }

    .sc-input,
    .sc-select,
    .sc-textarea {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 14px;
        padding: 10px 12px;
        font-size: 14px;
        background: #ffffff;
        outline: none;
        transition: .18s ease;
    }

    .sc-input:focus,
    .sc-select:focus,
    .sc-textarea:focus {
        border-color: #74b2d4;
        box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
    }

    .sc-textarea {
        min-height: 130px;
        resize: vertical;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 13px;
        line-height: 1.55;
    }

    .sc-help {
        color: #6b7280;
        font-size: 12px;
        margin-top: 6px;
        line-height: 1.45;
    }

    .sc-help strong {
        color: #374151;
    }

    .sc-check {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 900;
        color: #374151;
    }

    .sc-error {
        color: #b91c1c;
        font-size: 12px;
        margin-top: 5px;
        font-weight: 900;
    }

    .sc-actions-bottom {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 30px;
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
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(116, 178, 212, .22);
    }

    .sc-btn-primary:hover {
        background: #5ea3ca;
        color: #ffffff;
    }

    .sc-btn-soft {
        background: #ffffff;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .sc-btn-soft:hover {
        background: #f9fafb;
        color: #111827;
    }

    @media (max-width: 900px) {
        .sc-form-grid {
            grid-template-columns: 1fr;
        }

        .sc-actions-bottom {
            flex-direction: column-reverse;
        }

        .sc-btn {
            width: 100%;
        }
    }
</style>

<div class="sc-section">
    <div class="sc-section-head">
        <div class="sc-section-icon"><i data-lucide="plug-zap"></i></div>
        <div>
            <h2 class="sc-section-title">Allgemeine Verbindung</h2>
            <div class="sc-section-desc">
                Lege fest, welcher Lieferanten-Shop verbunden wird und welche Schnittstelle verwendet wird.
            </div>
        </div>
    </div>

    @if($isSonepar)
        <div class="sc-info-box">
            Sonepar IDS wurde erkannt. Nutze <strong>POST</strong>, <strong>Manueller Browser-POST</strong>,
            <strong>multipart</strong> und lasse den <strong>Suchparameter leer</strong>.
            Der Shop wird über den Browser geöffnet, damit Sonepar die Session-Cookies setzen kann.
        </div>
    @elseif($isGcOnline)
        <div class="sc-info-box">
            GC Online Plus wurde erkannt. Nutze <strong>IDS Shop</strong>, <strong>POST</strong>,
            <strong>Suchseite → Auto-POST</strong>, Basket Param <strong>warenkorb</strong>
            und Suchparameter <strong>searchterm</strong>.
        </div>
    @else
        <div class="sc-info-box">
            Standard IDS/OCI: Zugangsdaten und Parameter können je Lieferant unterschiedlich sein.
        </div>
    @endif

    <div class="sc-form-grid">
        <div>
            <label class="sc-label">Anzeigename</label>
            <input type="text"
                   name="name"
                   value="{{ old('name', $connection->name) }}"
                   class="sc-input"
                   placeholder="z.B. Sonepar IDS">
            <div class="sc-help">Dieser Name wird später in der Liste angezeigt.</div>
            @error('name') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Interner Schlüssel</label>
            <input type="text"
                   name="supplier_key"
                   value="{{ old('supplier_key', $connection->supplier_key) }}"
                   class="sc-input"
                   placeholder="z.B. sonepar">
            <div class="sc-help">Kann leer bleiben. Das System erzeugt automatisch einen eindeutigen Schlüssel.</div>
            @error('supplier_key') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Lieferant im System</label>
            <select name="distributor_id" class="sc-select">
                <option value="">Automatisch erstellen</option>
                @foreach($distributors ?? [] as $distributor)
                    <option value="{{ $distributor->id }}"
                        @selected(old('distributor_id', $connection->distributor_id) == $distributor->id)>
                        {{ $distributor->name ?? $distributor->short_name }}
                    </option>
                @endforeach
            </select>
            <div class="sc-help">Falls der Lieferant schon existiert, hier auswählen.</div>
            @error('distributor_id') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Schnittstellen-Typ</label>
            <select name="connector_type" class="sc-select">
                @foreach([
                    'ids' => 'IDS Shop',
                    'oci' => 'OCI Shop',
                    'api' => 'API',
                    'csv' => 'CSV Datei',
                    'xml' => 'XML Datei',
                    'bmecat' => 'BMEcat Katalog',
                    'datanorm' => 'DATANORM',
                ] as $key => $label)
                    <option value="{{ $key }}"
                        @selected(old('connector_type', $connection->connector_type ?: 'ids') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('connector_type') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Shop-Adresse / IDS-Adresse</label>
            <input type="text"
                   name="endpoint_url"
                   value="{{ old('endpoint_url', $connection->endpoint_url) }}"
                   class="sc-input"
                   placeholder="https://www.sonepar.de/punchout/v1/ids/setup">
            @error('endpoint_url') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Test-Adresse</label>
            <input type="text"
                   name="test_url"
                   value="{{ old('test_url', $connection->test_url) }}"
                   class="sc-input"
                   placeholder="Optional, meistens gleich wie Shop-Adresse">
            @error('test_url') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Rückgabe-Adresse manuell</label>
            <input type="text"
                   name="return_url"
                   value="{{ old('return_url', $connection->return_url) }}"
                   class="sc-input"
                   placeholder="Kann leer bleiben">
            <div class="sc-help">
                Normalerweise leer lassen. Laravel erzeugt automatisch die Rückgabe-Adresse.
                Für Produktion muss <strong>APP_URL=https://nuri-head.de</strong> gesetzt sein.
            </div>
            @error('return_url') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Status</label>
            <label class="sc-check">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $connection->is_active ?? true))>
                Verbindung aktiv
            </label>
            @error('is_active') <div class="sc-error">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="sc-section">
    <div class="sc-section-head">
        <div class="sc-section-icon"><i data-lucide="key-round"></i></div>
        <div>
            <h2 class="sc-section-title">Zugangsdaten vom Lieferanten</h2>
            <div class="sc-section-desc">
                Diese Werte werden dynamisch in das Login-Parameter-Mapping eingesetzt.
            </div>
        </div>
    </div>

    @if($isSonepar)
        <div class="sc-info-box">
            Für Sonepar:
            <strong>Kundennummer = 023051</strong>,
            <strong>Benutzername = ids</strong>,
            <strong>Passwort = dein Sonepar IDS Passwort</strong>.
        </div>
    @else
        <div class="sc-info-box">
            Bei IDS/OCI gilt meistens:
            <strong>kndnr</strong> = Kundennummer,
            <strong>name_kunde</strong> = Benutzername,
            <strong>pw_kunde</strong> = Passwort.
        </div>
    @endif

    <div class="sc-form-grid">
        <div>
            <label class="sc-label">Anmeldeart</label>
            <select name="auth_type" class="sc-select">
                @foreach([
                    'none' => 'Keine Anmeldung',
                    'basic' => 'Benutzername + Passwort',
                    'token' => 'Token',
                    'bearer' => 'Bearer Token',
                    'custom' => 'Spezial-Anmeldung',
                ] as $key => $label)
                    <option value="{{ $key }}"
                        @selected(old('auth_type', $connection->auth_type ?? 'basic') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('auth_type') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Kundennummer</label>
            <input type="text"
                   name="customer_number"
                   value="{{ old('customer_number', $connection->customer_number) }}"
                   class="sc-input"
                   placeholder="{{ $isSonepar ? '023051' : 'z.B. 017896' }}">
            @error('customer_number') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Benutzername</label>
            <input type="text"
                   name="username"
                   value="{{ old('username', $connection->username) }}"
                   class="sc-input"
                   placeholder="{{ $isSonepar ? 'ids' : 'z.B. 160160017896' }}">
            @error('username') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Passwort</label>
            <input type="password"
                   name="password"
                   value=""
                   class="sc-input"
                   placeholder="{{ $connection->exists && $connection->password ? 'Gespeichert – leer lassen zum Behalten' : 'Passwort eingeben' }}">
            <div class="sc-help">Beim Bearbeiten leer lassen, wenn es nicht geändert werden soll.</div>
            @error('password') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Token / API-Schlüssel</label>
            <input type="password"
                   name="token"
                   value=""
                   class="sc-input"
                   placeholder="{{ $connection->exists && $connection->token ? 'Gespeichert – leer lassen zum Behalten' : 'Optional' }}">
            @error('token') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Timeout Sekunden</label>
            <input type="number"
                   name="timeout"
                   value="{{ old('timeout', $requestConfig['timeout'] ?? 20) }}"
                   class="sc-input"
                   min="3"
                   max="120">
            @error('timeout') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div class="sc-full">
            <label class="sc-label">Erweiterte Zugangsdaten JSON</label>
            <textarea name="extra_auth_data"
                      class="sc-textarea"
                      placeholder='{}'>{{ old('extra_auth_data', json_encode($extraAuthData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
            <div class="sc-help">Für Lieferanten mit zusätzlichen Zugangsfeldern. Normalerweise <strong>{}</strong>.</div>
            @error('extra_auth_data') <div class="sc-error">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="sc-section">
    <div class="sc-section-head">
        <div class="sc-section-icon"><i data-lucide="settings-2"></i></div>
        <div>
            <h2 class="sc-section-title">Request, Suche & Rückgabe</h2>
            <div class="sc-section-desc">
                Hier steuerst du, wie Laravel den Shop öffnet, welcher Suchparameter gesendet wird und welches Warenkorb-Feld zurückkommt.
            </div>
        </div>
    </div>

    @if($isSonepar)
        <div class="sc-warning-box">
            Wichtig für Sonepar: <strong>Verbindung testen</strong> ist nur ein Backend-Test.
            Der echte IDS-Login funktioniert über <strong>Shop im Browser öffnen</strong>, weil Sonepar Cookies und eine 303-Weiterleitung setzt.
        </div>
    @endif

    <div class="sc-form-grid">
        <div>
            <label class="sc-label">Request Methode</label>
            <select name="request_method" class="sc-select">
                @foreach(['GET', 'POST', 'PUT', 'PATCH'] as $method)
                    <option value="{{ $method }}"
                        @selected(old('request_method', $defaultMethod) === $method)>
                        {{ $method }}
                    </option>
                @endforeach
            </select>
            <div class="sc-help">
                Für Sonepar, FEGA und GC Online: <strong>POST</strong>.
            </div>
            @error('request_method') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Öffnungsmodus</label>
            <select name="open_mode" class="sc-select">
                @foreach([
                    'search_then_auto_post' => 'Suchseite → Auto-POST',
                    'auto_post_form' => 'Direkter Auto-POST',
                    'manual_post' => 'Manueller Browser-POST',
                    'redirect_get' => 'Direkter GET Redirect',
                ] as $key => $label)
                    <option value="{{ $key }}"
                        @selected(old('open_mode', $defaultOpenMode) === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <div class="sc-help">
                Sonepar: <strong>Manueller Browser-POST</strong>.
                GC Online: <strong>Suchseite → Auto-POST</strong>.
            </div>
            @error('open_mode') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Content Type</label>
            <select name="request_content_type" class="sc-select">
                @foreach([
                    '' => 'Standard / leer',
                    'form' => 'Form URL Encoded',
                    'multipart' => 'Multipart Form Data',
                    'json' => 'JSON',
                    'xml' => 'XML',
                ] as $key => $label)
                    <option value="{{ $key }}"
                        @selected(old('request_content_type', $defaultContentType) === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <div class="sc-help">
                Sonepar: <strong>Multipart Form Data</strong>.
                GC Online/FEGA meistens: <strong>Form URL Encoded</strong>.
            </div>
            @error('request_content_type') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Accept Header</label>
            <input type="text"
                   name="accept"
                   value="{{ old('accept', $defaultAccept) }}"
                   class="sc-input"
                   placeholder="{{ $isSonepar ? '*/*' : 'Optional, z.B. text/html' }}">
            <div class="sc-help">Für Sonepar: <strong>*/*</strong>. Leer lassen bei normalen Lieferanten.</div>
            @error('accept') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Warenkorb-Rückgabeparameter</label>
            <input type="text"
                   name="basket_param"
                   value="{{ old('basket_param', $defaultBasketParam) }}"
                   class="sc-input"
                   placeholder="z.B. warenkorb">
            <div class="sc-help">Für IDS meistens: <strong>warenkorb</strong>.</div>
            @error('basket_param') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Suchparameter</label>
            <input type="text"
                   name="search_param"
                   value="{{ old('search_param', $defaultSearchParam) }}"
                   class="sc-input"
                   placeholder="{{ $isSonepar ? 'Für Sonepar leer lassen' : 'z.B. searchterm' }}">
            <div class="sc-help">
                Sonepar WKE: <strong>leer lassen</strong>.
                GC Online: <strong>searchterm</strong>.
            </div>
            @error('search_param') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Produkt-Abgleich über</label>
            <select name="match_by" class="sc-select">
                @foreach([
                    'ean' => 'EAN',
                    'sku' => 'SKU',
                    'article_no' => 'Artikelnummer',
                    'supplier_article_number' => 'Lieferanten-Artikelnummer',
                ] as $key => $label)
                    <option value="{{ $key }}"
                        @selected(old('match_by', $importConfig['match_by'] ?? 'ean') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('match_by') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-label">Preis-Modus</label>
            <select name="price_mode" class="sc-select">
                @foreach([
                    'purchase_price' => 'EK Preis',
                    'sale_price' => 'VK Preis',
                    'both' => 'EK und VK',
                ] as $key => $label)
                    <option value="{{ $key }}"
                        @selected(old('price_mode', $importConfig['price_mode'] ?? 'purchase_price') === $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('price_mode') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div>
            <label class="sc-check">
                <input type="checkbox"
                       name="update_existing"
                       value="1"
                       @checked(old('update_existing', $importConfig['update_existing'] ?? true))>
                Bestehende Produkte aktualisieren
            </label>
        </div>

        <div>
            <label class="sc-check">
                <input type="checkbox"
                       name="create_missing"
                       value="1"
                       @checked(old('create_missing', $importConfig['create_missing'] ?? true))>
                Fehlende Produkte erstellen
            </label>
        </div>

        <div class="sc-full">
            <label class="sc-label">Login-Parameter Mapping JSON</label>
            <textarea name="auth_param_map"
                      class="sc-textarea"
                      placeholder='{"kndnr":"{customer_number}", "name_kunde":"{username}", "pw_kunde":"{password}", "hookurl":"{callback_url}"}'>{{ old('auth_param_map', json_encode($defaultAuthMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
            <div class="sc-help">
                Links steht der Parametername vom Lieferanten. Rechts steht dein Laravel-Platzhalter.
                Verfügbar:
                <strong>{username}</strong>,
                <strong>{password}</strong>,
                <strong>{customer_number}</strong>,
                <strong>{token}</strong>,
                <strong>{searchterm}</strong>,
                <strong>{callback_url}</strong>,
                <strong>{return_url}</strong>.
            </div>
            @error('auth_param_map') <div class="sc-error">{{ $message }}</div> @enderror
        </div>

        <div class="sc-full">
            <label class="sc-label">Zusätzliche feste Parameter JSON</label>
            <textarea name="extra_params"
                      class="sc-textarea"
                      placeholder='{"OrganizationId":"1", "action":"WKE", "Version":"2.0", "target":"TOP"}'>{{ old('extra_params', json_encode($defaultExtraParams, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) }}</textarea>
            <div class="sc-help">
                Sonepar braucht:
                <strong>OrganizationId=1</strong>,
                <strong>action=WKE</strong>,
                <strong>Version=2.0</strong>,
                <strong>target=TOP</strong>.
            </div>
            @error('extra_params') <div class="sc-error">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="sc-actions-bottom">
    <a href="{{ route('admin.supplier-connectors.index') }}" class="sc-btn sc-btn-soft">
        Zurück
    </a>

    <button type="submit" class="sc-btn sc-btn-primary">
        Speichern
    </button>
</div>