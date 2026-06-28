<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Models\SupplierConnection;
use App\Models\SupplierImportLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class OfferTemplateSupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Optional base dispatcher.
     *
     * Supports:
     * /admin/offer-template-supplier?action=connections
     * /admin/offer-template-supplier?supplier_connection_id=1&query=test
     */
    public function dispatch(Request $request): JsonResponse|RedirectResponse|Response
    {
        if ($request->get('action') === 'connections') {
            return $this->connections($request);
        }

        $supplierConnectionId = (int) $request->get('supplier_connection_id');

        if ($supplierConnectionId > 0) {
            return $this->forward($request, $supplierConnectionId);
        }

        return response()->json([
            'success' => false,
            'message' => 'Ungültige Lieferanten-Anfrage.',
        ], 422);
    }

    /**
     * Template-mode supplier connections.
     *
     * IMPORTANT:
     * Your table has `is_active`, not `active`.
     */
    public function connections(Request $request): JsonResponse
    {
        $connections = SupplierConnection::query()
            ->with('distributor:id,name,short_name')
            ->where('is_active', true)
            ->whereRaw('LOWER(TRIM(connector_type)) IN (?, ?, ?)', [
                'ids',
                'oci',
                'ids/oci',
            ])
            ->orderBy('name')
            ->get()
            ->map(function (SupplierConnection $connection) {
                $connectorType = strtolower(trim((string) $connection->connector_type));

                $distributorName = $connection->distributor
                    ? trim((string) ($connection->distributor->short_name ?: $connection->distributor->name))
                    : '';

                $endpoint = trim((string) ($connection->endpoint_url ?? ''));

                return [
                    'id' => (int) $connection->id,
                    'name' => (string) ($connection->name ?: ('Lieferant #' . $connection->id)),
                    'supplier_key' => (string) ($connection->supplier_key ?? ''),
                    'connector_type' => strtoupper($connectorType ?: 'IDS/OCI'),

                    'distributor_id' => $connection->distributor_id ? (int) $connection->distributor_id : null,
                    'distributor_name' => $distributorName,

                    'ready' => $endpoint !== '',
                    'has_endpoint' => $endpoint !== '',
                    'has_distributor' => !empty($connection->distributor_id),

                    'warning' => $endpoint === ''
                        ? 'Endpoint URL fehlt.'
                        : (!$connection->distributor_id
                            ? 'Kein Distributor verbunden. Du kannst ihn auf der Rückgabe-Seite auswählen.'
                            : null),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'connections' => $connections,
        ]);
    }

    /**
     * Open IDS/OCI supplier shop without requiring offer folder.
     */
    public function forward(Request $request, $supplierConnection): RedirectResponse|Response
    {
        $connection = $this->findConnection((int) $supplierConnection);

        if (!$connection) {
            return $this->popupError('Lieferanten-Verbindung nicht gefunden.');
        }

        if (!$connection->is_active) {
            return $this->popupError('Diese Lieferanten-Verbindung ist deaktiviert.');
        }

        $connectorType = strtolower(trim((string) $connection->connector_type));

        if (!in_array($connectorType, ['ids', 'oci', 'ids/oci'], true)) {
            return $this->popupError('Dieser Lieferant kann nicht als IDS/OCI Shop geöffnet werden.');
        }

        $endpoint = trim((string) ($connection->endpoint_url ?? ''));

        if ($endpoint === '') {
            return $this->popupError('Dieser IDS/OCI Lieferant hat keine Endpoint URL.');
        }

        $session = $this->sessionId($request);
        $query = trim((string) $request->get('query', ''));
        $targetSectionIndex = $request->get('target_section_index');

        $returnUrl = route('admin.offer-template-supplier.return', [
            'supplierConnection' => $connection->id,
        ]);

        $returnUrl .= '?' . http_build_query(array_filter([
            'session' => $session,
            'target_section_index' => $targetSectionIndex,
            'uid' => auth()->id(),
        ], fn($value) => $value !== null && $value !== ''));

        $params = $this->buildForwardParams(
            connection: $connection,
            query: $query,
            returnUrl: $returnUrl,
            request: $request
        );

        $separator = str_contains($endpoint, '?') ? '&' : '?';
        $url = $endpoint . $separator . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        return redirect()->away($url);
    }

    /**
     * Supplier return callback.
     * Returns a small HTML page that posts selected items back to the opener window.
     */
    public function handleReturn(Request $request, $supplierConnection): Response
    {
        $connection = $this->findConnection((int) $supplierConnection);

        if (!$connection) {
            return $this->returnPage([], $request, 'Lieferanten-Verbindung nicht gefunden.');
        }

        if (!$connection->is_active) {
            return $this->returnPage([], $request, 'Diese Lieferanten-Verbindung ist deaktiviert.');
        }

        $items = $this->extractItems($request, $connection);

        return $this->returnPage($items, $request, null, [
            'supplier_connection_id' => (int) $connection->id,
            'supplier_name' => (string) ($connection->name ?? ''),
            'distributor_id' => $connection->distributor_id ? (int) $connection->distributor_id : null,
            'distributor_name' => $this->distributorName($connection),
        ]);
    }

    /**
     * Optional route compatibility.
     * Only needed if your routes include logs/{log}/review.
     */
    public function reviewReturn(
        Request $request,
        SupplierConnection $supplierConnection,
        SupplierImportLog $log
    ): Response {
        if ((int) $log->supplier_connection_id !== (int) $supplierConnection->id) {
            abort(404);
        }

        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];

        return $this->returnPage($items, $request, null, [
            'supplier_connection_id' => (int) $supplierConnection->id,
            'supplier_name' => (string) ($supplierConnection->name ?? ''),
            'distributor_id' => $supplierConnection->distributor_id ? (int) $supplierConnection->distributor_id : null,
            'distributor_name' => $this->distributorName($supplierConnection),
        ]);
    }

    /**
     * Optional route compatibility.
     * Template mode normally imports through postMessage/localStorage,
     * not by writing into an OfferDetail.
     */
    public function importReviewedToTemplate(
        Request $request,
        SupplierConnection $supplierConnection,
        SupplierImportLog $log
    ): Response {
        if ((int) $log->supplier_connection_id !== (int) $supplierConnection->id) {
            abort(404);
        }

        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $items = is_array($payload['items'] ?? null) ? $payload['items'] : [];

        return $this->returnPage($items, $request, null, [
            'supplier_connection_id' => (int) $supplierConnection->id,
            'supplier_name' => (string) ($supplierConnection->name ?? ''),
            'distributor_id' => $supplierConnection->distributor_id ? (int) $supplierConnection->distributor_id : null,
            'distributor_name' => $this->distributorName($supplierConnection),
        ]);
    }

    protected function findConnection(int $id): ?SupplierConnection
    {
        return SupplierConnection::query()
            ->with('distributor:id,name,short_name')
            ->where('id', $id)
            ->first();
    }

    protected function sessionId(Request $request): string
    {
        $session = trim((string) $request->get('session', ''));

        if ($session !== '') {
            $session = preg_replace('/[^A-Za-z0-9_\-]/', '', $session);

            return $session ?: ('tpl_' . Str::uuid()->toString());
        }

        return 'tpl_' . Str::uuid()->toString();
    }

    protected function buildForwardParams(
        SupplierConnection $connection,
        string $query,
        string $returnUrl,
        Request $request
    ): array {
        $requestConfig = $this->arrayValue($connection->request_config ?? null);
        $authParamMap = $this->arrayValue($connection->getAttribute('auth_param_map') ?? null);
        $extraAuthData = $this->arrayValue($connection->extra_auth_data ?? null);

        $params = [];

        if (is_array($requestConfig)) {
            $configuredParams = $requestConfig['params']
                ?? $requestConfig['query']
                ?? $requestConfig['query_params']
                ?? [];

            if (is_array($configuredParams)) {
                foreach ($configuredParams as $key => $value) {
                    if ($key === null || $key === '') {
                        continue;
                    }

                    $params[(string) $key] = $this->replacePlaceholders(
                        (string) $value,
                        $connection,
                        $query,
                        $returnUrl
                    );
                }
            }

            $configuredStatic = $requestConfig['static']
                ?? $requestConfig['static_params']
                ?? [];

            if (is_array($configuredStatic)) {
                foreach ($configuredStatic as $key => $value) {
                    if ($key === null || $key === '') {
                        continue;
                    }

                    $params[(string) $key] = $this->replacePlaceholders(
                        (string) $value,
                        $connection,
                        $query,
                        $returnUrl
                    );
                }
            }
        }

        if (is_array($authParamMap)) {
            foreach ($authParamMap as $paramName => $fieldName) {
                if ($paramName === null || $paramName === '') {
                    continue;
                }

                $params[(string) $paramName] = $this->connectionField(
                    $connection,
                    (string) $fieldName,
                    $extraAuthData
                );
            }
        }

        /**
         * Safe IDS/OCI defaults.
         * Configured params above can override these after merge.
         */
        $defaults = [
            'action' => 'search',
            'version' => '1.0',
            'target' => 'top',

            'searchterm' => $query,
            'query' => $query,
            'q' => $query,

            'uid' => auth()->id(),

            'rueckurl' => $returnUrl,
            'return_url' => $returnUrl,
            'hookurl' => $returnUrl,
            'hook_url' => $returnUrl,
            'callback_url' => $returnUrl,
            'HOOK_URL' => $returnUrl,
        ];

        $params = array_merge($defaults, $params);

        return collect($params)
            ->reject(fn($value) => $value === null || $value === '')
            ->all();
    }

    protected function replacePlaceholders(
        string $value,
        SupplierConnection $connection,
        string $query,
        string $returnUrl
    ): string {
        $map = [
            '{query}' => $query,
            '{search}' => $query,
            '{searchterm}' => $query,

            '{return_url}' => $returnUrl,
            '{returnUrl}' => $returnUrl,
            '{rueckurl}' => $returnUrl,
            '{hookurl}' => $returnUrl,
            '{hook_url}' => $returnUrl,
            '{callback_url}' => $returnUrl,

            '{uid}' => (string) auth()->id(),

            '{username}' => (string) ($connection->username ?? ''),
            '{password}' => (string) ($connection->password ?? ''),
            '{customer_number}' => (string) ($connection->customer_number ?? ''),
            '{token}' => (string) ($connection->token ?? ''),
            '{supplier_key}' => (string) ($connection->supplier_key ?? ''),
        ];

        return strtr($value, $map);
    }

    protected function connectionField(
        SupplierConnection $connection,
        string $field,
        ?array $extraAuthData = null
    ): string {
        $field = trim($field);

        if ($field === '') {
            return '';
        }

        if (str_starts_with($field, 'literal:')) {
            return substr($field, 8);
        }

        if (str_starts_with($field, 'extra_auth_data.')) {
            $key = substr($field, strlen('extra_auth_data.'));

            return (string) Arr::get($extraAuthData ?: [], $key, '');
        }

        return (string) ($connection->{$field} ?? '');
    }

    protected function extractItems(Request $request, SupplierConnection $connection): array
    {
        $payload = $request->all();

        foreach (['items', 'products', 'positions', 'basket'] as $key) {
            $items = $this->arrayValue($request->input($key));

            if (is_array($items) && array_is_list($items)) {
                return array_values(array_filter(array_map(
                    fn($item) => $this->normalizeItem((array) $item, $connection),
                    $items
                )));
            }
        }

        $ociRows = [];

        foreach ($payload as $key => $value) {
            if (preg_match('/^NEW_ITEM-([A-Z0-9_]+)\[(\d+)\]$/i', (string) $key, $m)) {
                $field = strtolower($m[1]);
                $idx = (int) $m[2];
                $ociRows[$idx][$field] = $value;
            }
        }

        if (!empty($ociRows)) {
            ksort($ociRows);

            return array_values(array_filter(array_map(
                fn($item) => $this->normalizeOciItem($item, $connection),
                $ociRows
            )));
        }

        $names = Arr::wrap($request->input('name', $request->input('description', [])));

        if (count($names) > 0 && !blank($names[0])) {
            $items = [];

            foreach ($names as $i => $name) {
                $items[] = $this->normalizeItem([
                    'name' => $name,
                    'qty' => Arr::get(Arr::wrap($request->input('qty', [])), $i, 1),
                    'unit' => Arr::get(Arr::wrap($request->input('unit', [])), $i, 'Stk'),
                    'price' => Arr::get(Arr::wrap($request->input('price', [])), $i, 0),
                    'ek' => Arr::get(Arr::wrap($request->input('ek', [])), $i, 0),
                    'article_no' => Arr::get(Arr::wrap($request->input('article_no', [])), $i, ''),
                    'distributor_article_no' => Arr::get(Arr::wrap($request->input('distributor_article_no', [])), $i, ''),
                    'description' => Arr::get(Arr::wrap($request->input('longtext', [])), $i, ''),
                ], $connection);
            }

            return array_values(array_filter($items));
        }

        return [];
    }

    protected function normalizeOciItem(array $item, SupplierConnection $connection): ?array
    {
        return $this->normalizeItem([
            'name' => $item['description']
                ?? $item['text']
                ?? $item['shorttext']
                ?? $item['longtext']
                ?? null,

            'qty' => $item['quantity'] ?? 1,
            'unit' => $item['unit'] ?? $item['unitofmeasure'] ?? 'Stk',

            'price' => $item['price'] ?? $item['priceamount'] ?? 0,
            'ek' => $item['price'] ?? $item['priceamount'] ?? 0,

            'article_no' => $item['manufactmat']
                ?? $item['matnr']
                ?? $item['manufacturer_article_no']
                ?? '',

            'distributor_article_no' => $item['vendormat']
                ?? $item['suppliermat']
                ?? $item['supplier_article_no']
                ?? '',

            'description' => $item['longtext'] ?? '',
        ], $connection);
    }

    protected function normalizeItem(array $raw, SupplierConnection $connection): ?array
    {
        $name = trim((string) (
            $raw['name']
            ?? $raw['product']
            ?? $raw['title']
            ?? $raw['description']
            ?? ''
        ));

        if ($name === '') {
            return null;
        }

        $price = $this->money($raw['price'] ?? $raw['unit_price'] ?? $raw['vk'] ?? 0);
        $ek = $this->money($raw['ek'] ?? $raw['purchase_price'] ?? $raw['cost'] ?? $price);

        $unit = trim((string) ($raw['unit'] ?? $raw['measure'] ?? 'Stk'));
        $unit = $unit !== '' ? $unit : 'Stk';

        $qty = $this->number($raw['qty'] ?? $raw['quantity'] ?? 1, 1);

        $margin = $ek > 0
            ? round((($price - $ek) / $ek) * 100, 2)
            : 20;

        $distributorName = $this->distributorName($connection);

        return [
            'id' => 'supplier_template_' . Str::uuid()->toString(),

            'item_type' => 'product',
            'kind' => 'article',
            'status' => 'normal',
            'active' => true,

            'product_id' => null,
            'productId' => null,

            'name' => $name,
            'desc_html' => (string) ($raw['desc_html'] ?? $raw['description'] ?? ''),
            'description' => (string) ($raw['description'] ?? $raw['desc_html'] ?? ''),

            'article_no' => (string) ($raw['article_no'] ?? $raw['manufacturer_article_no'] ?? ''),
            'manufacturer_article_no' => (string) ($raw['manufacturer_article_no'] ?? $raw['article_no'] ?? ''),
            'distributor_article_no' => (string) ($raw['distributor_article_no'] ?? $raw['supplier_article_no'] ?? ''),

            'distributor_id' => $connection->distributor_id ? (int) $connection->distributor_id : null,
            'distributor_name' => $distributorName,
            'supplier' => $distributorName ?: (string) ($connection->name ?? ''),

            'qty' => $qty,
            'unit' => $unit,
            'measure' => $unit,

            'price' => $price,
            'unit_price' => $price,
            'ek' => $ek,
            'purchase_price' => $ek,
            'cost' => $ek,
            'buying_price' => $ek,

            'margin' => $margin,
            'marginPercent' => $margin,

            'price_unit_value' => 1,
            'price_unit_label' => $unit,
            'price_unit_text' => '1 ' . $unit,

            'img' => (string) ($raw['image_url'] ?? $raw['img'] ?? ''),
            'image_url' => (string) ($raw['image_url'] ?? $raw['img'] ?? ''),

            'subItems' => [],
            'labor_rows' => [],

            'hideImage' => false,
            'hidePrices' => false,
            'hideNumbering' => false,

            '_source' => 'supplier_template_import',
            '_supplier_connection_id' => (int) $connection->id,
            '_freshFromSupplier' => true,
        ];
    }

    protected function returnPage(
        array $items,
        Request $request,
        ?string $error = null,
        array $meta = []
    ): Response {
        $session = $this->sessionId($request);
        $targetSectionIndex = $request->get('target_section_index');

        $payload = [
            'type' => 'offer_supplier_import_done',
            'template_mode' => true,
            'template_session_id' => $session,
            'context_key' => 'template_' . $session,
            'target_section_index' => is_numeric($targetSectionIndex) ? (int) $targetSectionIndex : null,
            'items' => $items,
            'success' => !$error,
            'message' => $error ?: (count($items) . ' Position(en) übernommen.'),
        ] + $meta;

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $title = $error ? 'Rückgabe fehlgeschlagen' : 'Lieferantenartikel übernehmen';
        $button = $error ? 'Fenster schließen' : 'In Vorlage übernehmen';
        $titleClass = $error ? 'bad' : 'ok';
        $message = e((string) $payload['message']);
        $itemCount = count($items);

        $html = <<<HTML
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title}</title>
    <style>
        body {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 40px;
        }

        .card {
            max-width: 760px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(15, 23, 42, .08);
            padding: 28px;
        }

        .ok { color: #6b8e12; }
        .bad { color: #dc2626; }
        .muted { color: #64748b; }

        .btn {
            border: 0;
            background: #93c21c;
            color: #fff;
            border-radius: 14px;
            padding: 12px 18px;
            font-weight: 900;
            cursor: pointer;
        }

        .btn:hover {
            filter: brightness(.96);
        }

        pre {
            white-space: pre-wrap;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px;
            max-height: 240px;
            overflow: auto;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1 class="{$titleClass}">{$title}</h1>
        <p class="muted">{$message}</p>
        <p><strong>Artikel:</strong> {$itemCount}</p>
        <button class="btn" onclick="sendBack()">{$button}</button>
        <pre id="debug"></pre>
    </div>

    <script>
        const payload = {$json};
        const key = 'offer_supplier_import_' + (payload.context_key || ('template_' + payload.template_session_id));

        document.getElementById('debug').textContent = JSON.stringify(payload.items || [], null, 2);

        function sendBack() {
            try {
                if (window.opener && !window.opener.closed) {
                    window.opener.postMessage(payload, '*');
                }
            } catch (e) {}

            try {
                localStorage.setItem(key, JSON.stringify(payload));
            } catch (e) {}

            setTimeout(() => window.close(), 250);
        }

        if ((payload.items || []).length) {
            setTimeout(sendBack, 450);
        }
    </script>
</body>
</html>
HTML;

        return response($html);
    }

    protected function popupError(string $message): Response
    {
        return response(
            '<!doctype html><meta charset="utf-8"><body style="font-family:sans-serif;padding:30px"><h2>Fehler</h2><p>'
            . e($message)
            . '</p></body>'
        );
    }

    protected function distributorName(SupplierConnection $connection): string
    {
        if ($connection->distributor) {
            return trim((string) ($connection->distributor->short_name ?: $connection->distributor->name));
        }

        if (!empty($connection->distributor_id) && Schema::hasTable('distributors')) {
            return (string) DB::table('distributors')
                ->where('id', $connection->distributor_id)
                ->value('name');
        }

        return (string) ($connection->name ?? '');
    }

    protected function arrayValue($value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded)
            ? $decoded
            : null;
    }

    protected function number($value, float $default = 0.0): float
    {
        if ($value === null || $value === '') {
            return $default;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = str_replace(["\xc2\xa0", '€', 'EUR', ' '], '', (string) $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
        }

        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : $default;
    }

    protected function money($value): float
    {
        return round($this->number($value, 0), 2);
    }
}