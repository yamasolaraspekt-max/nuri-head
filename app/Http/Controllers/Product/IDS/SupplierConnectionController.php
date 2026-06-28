<?php

namespace App\Http\Controllers\Product\IDS;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\Brand;
use App\Models\Distributor;
use App\Models\SubArticleGroup;
use App\Models\SupplierConnection;
use App\Models\SupplierConnectionMapping;
use App\Models\SupplierImportLog;
use App\Services\Suppliers\SupplierConnectionTestService;
use App\Services\Suppliers\SupplierConnectorService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SupplierConnectionController extends Controller
{
    public function index(Request $request)
    {
        $query = SupplierConnection::query()
            ->with('distributor')
            ->withCount(['mappings', 'logs'])
            ->latest();

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('supplier_key', 'like', '%' . $search . '%')
                    ->orWhere('connector_type', 'like', '%' . $search . '%')
                    ->orWhereHas('distributor', function ($distributorQuery) use ($search) {
                        $distributorQuery->where('name', 'like', '%' . $search . '%')
                            ->orWhere('short_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%')
                            ->orWhere('city', 'like', '%' . $search . '%')
                            ->orWhere('account_number', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('type')) {
            $query->where('connector_type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            }

            if ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('test_status')) {
            $query->where('last_test_status', $request->test_status);
        }

        $connections = $query->paginate(20)->withQueryString();

        return view('admin.supplier-connectors.index', compact('connections'));
    }

    public function create()
    {
        $connection = new SupplierConnection([
            'connector_type' => 'ids',
            'auth_type' => 'basic',
            'is_active' => true,
            'request_config' => [
                'method' => 'GET',
                'open_mode' => 'search_then_auto_post',
                'content_type' => null,
                'timeout' => 20,
                'basket_param' => 'warenkorb',
                'search_param' => 'searchterm',
                'extra_params' => [],
                'auth_param_map' => $this->authPreset('standard_ids'),
            ],
            'import_config' => [
                'match_by' => 'ean',
                'update_existing' => true,
                'create_missing' => true,
                'price_mode' => 'purchase_price',
            ],
        ]);

        $distributors = Distributor::orderBy('name')->get();

        return view('admin.supplier-connectors.create', compact('connection', 'distributors'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatedConnectionData($request);
        $data = $this->prepareConnectionPayload($request, $validated);

        $connection = SupplierConnection::create($data);

        $this->createDefaultMappingsIfMissing($connection);

        return redirect()
            ->route('admin.supplier-connectors.edit', $connection)
            ->with('success', 'Lieferanten-Schnittstelle wurde erstellt.');
    }

    public function edit(SupplierConnection $supplierConnector)
    {
        $connection = $supplierConnector->load([
            'distributor',
            'mappings' => fn($query) => $query->orderBy('sort_order')->orderBy('id'),
            'logs' => fn($query) => $query->latest()->limit(20),
        ]);

        $distributors = Distributor::orderBy('name')->get();

        return view('admin.supplier-connectors.edit', compact('connection', 'distributors'));
    }

    public function update(Request $request, SupplierConnection $supplierConnector)
    {
        $validated = $this->validatedConnectionData($request, $supplierConnector->id);
        $data = $this->prepareConnectionPayload($request, $validated, $supplierConnector);

        $supplierConnector->update($data);

        return redirect()
            ->route('admin.supplier-connectors.edit', $supplierConnector)
            ->with('success', 'Lieferanten-Schnittstelle wurde aktualisiert.');
    }

    public function destroy(SupplierConnection $supplierConnector)
    {
        $supplierConnector->delete();

        return redirect()
            ->route('admin.supplier-connectors.index')
            ->with('success', 'Lieferanten-Schnittstelle wurde gelöscht.');
    }

    public function test(
        SupplierConnection $supplierConnector,
        SupplierConnectionTestService $testService
    ) {
        $result = $testService->test($supplierConnector);

        $supplierConnector->update([
            'last_test_status' => $result['success'] ? 'success' : 'failed',
            'last_test_message' => $result['message'],
            'last_tested_at' => now(),
        ]);

        return back()->with(
            $result['success'] ? 'success' : 'error',
            $result['message']
        );
    }

    public function open(
        SupplierConnection $supplierConnector,
        SupplierConnectorService $service
    ) {
        abort_unless($supplierConnector->is_active, 404);

        if (!in_array($supplierConnector->connector_type, ['ids', 'oci'], true)) {
            return back()->with('error', 'Dieser Verbindungstyp kann nicht direkt geöffnet werden.');
        }

        if (!$supplierConnector->endpoint_url) {
            return back()->with('error', 'Endpoint URL fehlt.');
        }

        $supplierKey = strtolower((string) $supplierConnector->supplier_key);
        $name = strtolower((string) $supplierConnector->name);
        $endpoint = strtolower((string) $supplierConnector->endpoint_url);

        $isSonepar = str_contains($supplierKey, 'sonepar')
            || str_contains($name, 'sonepar')
            || str_contains($endpoint, 'sonepar.de');

        /*
        |--------------------------------------------------------------------------
        | Sonepar IDS
        |--------------------------------------------------------------------------
        | Sonepar does not need a search page.
        | It must open the shop directly via browser POST.
        |--------------------------------------------------------------------------
        */
        if ($isSonepar) {
            $params = $service->buildOpenParams($supplierConnector, [
                'auto' => false,
                'uid' => auth()->id(),
            ]);

            unset(
                $params['searchterm'],
                $params['SEARCHTERM'],
                $params['query'],
                $params['search'],
                $params['rueckurl'],
                $params['RUECKURL'],
                $params['link'],
                $params['LINK']
            );

            return response()->view('admin.supplier-connectors.forward-to-shop', [
                'connection' => $supplierConnector,
                'shopUrl' => $supplierConnector->endpoint_url,
                'params' => $params,
                'requestConfig' => $supplierConnector->request_config ?? [],
            ]);
        }

        return redirect()->route('admin.supplier-connectors.search', $supplierConnector);
    }
    public function search(SupplierConnection $supplierConnector)
    {
        abort_unless($supplierConnector->is_active, 404);

        if (!in_array($supplierConnector->connector_type, ['ids', 'oci'], true)) {
            return redirect()
                ->route('admin.supplier-connectors.edit', $supplierConnector)
                ->with('error', 'Für diese Verbindung ist keine Shop-Suche verfügbar.');
        }

        if (!$supplierConnector->endpoint_url) {
            return redirect()
                ->route('admin.supplier-connectors.edit', $supplierConnector)
                ->with('error', 'Endpoint URL fehlt.');
        }

        return view('admin.supplier-connectors.search', [
            'connection' => $supplierConnector,
            'requestConfig' => $supplierConnector->request_config ?? [],
        ]);
    }

    public function forward(
        Request $request,
        SupplierConnection $supplierConnector,
        SupplierConnectorService $service
    ) {
        abort_unless($supplierConnector->is_active, 404);

        $validated = $request->validate([
            'query' => ['required', 'string', 'max:255'],
            'auto' => ['nullable', 'boolean'],
        ]);

        if (!$supplierConnector->endpoint_url) {
            return back()->with('error', 'Endpoint URL fehlt.');
        }

        $params = $service->buildOpenParams($supplierConnector, [
            'searchterm' => $validated['query'],
            'query' => $validated['query'],
            'auto' => $request->boolean('auto'),
            'uid' => auth()->id(),
        ]);

        return response()->view('admin.supplier-connectors.forward-to-shop', [
            'connection' => $supplierConnector,
            'shopUrl' => $supplierConnector->endpoint_url,
            'params' => $params,
        ]);
    }

    public function latestLogs(SupplierConnection $supplierConnector)
    {
        $logs = $supplierConnector->logs()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($log) use ($supplierConnector) {
                $payload = is_array($log->payload ?? null) ? $log->payload : [];
                $savedProducts = $payload['saved_products'] ?? [];

                return [
                    'id' => $log->id,
                    'status' => $log->status,
                    'source_type' => $log->source_type,
                    'total_items' => $log->total_items,
                    'success_items' => $log->success_items,
                    'failed_items' => $log->failed_items,
                    'message' => $log->message,
                    'created_at' => optional($log->created_at)->format('d.m.Y H:i:s'),
                    'preview_url' => route('admin.supplier-connectors.logs.preview', [$supplierConnector, $log]),

                    'saved_products' => collect($savedProducts)->map(function ($product) {
                        $productId = $product['id'] ?? $product['product_id'] ?? null;

                        return [
                            'id' => $productId,
                            'product_id' => $productId,

                            'title' => $product['product']
                                ?? $product['title']
                                ?? $product['name']
                                ?? null,

                            // Lieferanten-Artikelnummer from distributor_prices.article_no
                            'article_no' => $product['article_no']
                                ?? $product['distributor_article_no']
                                ?? null,

                            // Hersteller-Artikelnummer from products.article_no
                            'manufacturer_article_no' => $product['manufacturer_article_no'] ?? null,

                            'price' => $product['price'] ?? null,
                            'purchase_price' => $product['purchase_price'] ?? null,

                            // New flags for UI badges
                            'price_only' => (bool) ($product['price_only'] ?? false),
                            'was_existing_product' => (bool) ($product['was_existing_product'] ?? false),

                            'url' => $productId ? url('product_details/' . $productId) : null,
                        ];
                    })->values(),
                ];
            });

        return response()->json([
            'logs' => $logs,
        ]);
    }
    public function handleReturn(
        Request $request,
        SupplierConnection $supplierConnector,
        SupplierConnectorService $service
    ) {
        if (!$supplierConnector->is_active) {
            return response('Diese Lieferanten-Schnittstelle ist deaktiviert.', 403);
        }

        $autoImport = $request->boolean('auto');

        $log = $service->handleReturn(
            $request,
            $supplierConnector,
            $autoImport
        );

        return response()->view('admin.supplier-connectors.return-done', [
            'connection' => $supplierConnector,
            'log' => $log,
        ]);
    }

    public function previewReturn(
        SupplierConnection $supplierConnector,
        SupplierImportLog $log
    ) {
        abort_unless((int) $log->supplier_connection_id === (int) $supplierConnector->id, 404);

        $payload = is_array($log->payload ?? null) ? $log->payload : [];
        $items = $payload['items'] ?? [];

        if (!is_array($items)) {
            $items = [];
        }

        $selected = $payload['selected'] ?? [];

        $selectedDistributor = null;

        if ($supplierConnector->distributor_id) {
            $selectedDistributor = Distributor::find($supplierConnector->distributor_id);
        }

        if (!$selectedDistributor && !empty($selected['distributor_id'])) {
            $selectedDistributor = Distributor::find($selected['distributor_id']);
        }

        $selectedBrand = !empty($selected['brand_id'])
            ? Brand::find($selected['brand_id'])
            : null;

        $selectedArticleGroup = !empty($selected['article_group_id'])
            ? ArticleGroup::find($selected['article_group_id'])
            : null;

        $selectedSubArticleGroup = !empty($selected['sub_article_group_id'])
            ? SubArticleGroup::find($selected['sub_article_group_id'])
            : null;

        $updatePriceOnlyIfExists = array_key_exists('update_price_only_if_exists', $selected)
            ? (bool) $selected['update_price_only_if_exists']
            : true;

        return view('admin.supplier-connectors.preview-return', [
            'connection' => $supplierConnector,
            'log' => $log,
            'items' => $items,
            'selectedDistributor' => $selectedDistributor,
            'selectedBrand' => $selectedBrand,
            'selectedArticleGroup' => $selectedArticleGroup,
            'selectedSubArticleGroup' => $selectedSubArticleGroup,
            'updatePriceOnlyIfExists' => $updatePriceOnlyIfExists,
        ]);
    }
    public function importReturn(
        Request $request,
        SupplierConnection $supplierConnector,
        SupplierImportLog $log,
        SupplierConnectorService $service
    ) {
        abort_unless((int) $log->supplier_connection_id === (int) $supplierConnector->id, 404);

        $validated = $request->validate([
            'distributor_id' => ['required', 'exists:distributors,id'],
            'default_brand_id' => ['nullable', 'exists:brands,id'],
            'default_article_group_id' => ['nullable', 'exists:article_groups,id'],
            'default_sub_article_group_id' => ['nullable', 'exists:sub_article_groups,id'],

            /**
             * When enabled:
             * If product exists, only distributor_prices is updated.
             * products table remains unchanged.
             */
            'update_price_only_if_exists' => ['nullable', 'boolean'],

            'items' => ['required', 'array'],

            'items.*.import' => ['nullable', 'boolean'],
            'items.*.product_title' => ['nullable', 'string', 'max:255'],
            'items.*.ean' => ['nullable', 'string', 'max:255'],
            'items.*.brand_id' => ['nullable', 'exists:brands,id'],
            'items.*.article_group_id' => ['nullable', 'exists:article_groups,id'],
            'items.*.sub_article_group_id' => ['nullable', 'exists:sub_article_groups,id'],

            // products.article_no = Herstellernummer
            'items.*.manufacturer_article_no' => ['nullable', 'string', 'max:255'],

            // distributor_prices.article_no = Lieferanten-Artikelnummer
            'items.*.distributor_article_no' => ['nullable', 'string', 'max:255'],

            'items.*.price' => ['nullable'],
            'items.*.purchase_price' => ['nullable'],
            'items.*.discount_price' => ['nullable'],
            'items.*.discount_percent' => ['nullable'],
            'items.*.availability' => ['nullable', 'string', 'max:255'],
            'items.*.measure_unit' => ['nullable', 'string', 'max:255'],
            'items.*.short_description' => ['nullable', 'string'],
            'items.*.image_url' => ['nullable', 'string', 'max:2000'],
            'items.*.vat_percent' => ['nullable'],
        ]);

        $result = $service->importReviewedItems(
            $supplierConnector,
            $log,
            (int) $validated['distributor_id'],
            !empty($validated['default_brand_id']) ? (int) $validated['default_brand_id'] : null,
            $validated['items'] ?? [],
            !empty($validated['default_article_group_id']) ? (int) $validated['default_article_group_id'] : null,
            !empty($validated['default_sub_article_group_id']) ? (int) $validated['default_sub_article_group_id'] : null,
            $request->boolean('update_price_only_if_exists')
        );

        return redirect()
            ->route('admin.supplier-connectors.logs.preview', [$supplierConnector, $log])
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function select2Brands(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 20;

        $query = Brand::query()
            ->select(['id', 'name', 'status', 'type'])
            ->orderBy('name');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        $total = (clone $query)->count();

        $brands = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'results' => $brands->map(function ($brand) {
                $label = trim((string) $brand->name);

                if ($brand->type) {
                    $label .= ' · ' . $brand->type;
                }

                return [
                    'id' => $brand->id,
                    'text' => $label,
                    'name' => $brand->name,
                    'type' => $brand->type,
                ];
            })->values(),
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    public function select2Distributors(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 20;

        $query = Distributor::query()
            ->select([
                'id',
                'short_name',
                'name',
                'city',
                'email',
                'account_number',
                'status',
                'is_hidden',
            ])
            ->orderByRaw('COALESCE(NULLIF(name, ""), short_name) ASC');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('short_name', 'like', '%' . $search . '%')
                    ->orWhere('city', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('account_number', 'like', '%' . $search . '%');
            });
        }

        $total = (clone $query)->count();

        $distributors = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'results' => $distributors->map(function ($distributor) {
                $label = $distributor->name ?: $distributor->short_name;

                $extra = collect([
                    $distributor->short_name && $distributor->short_name !== $label ? $distributor->short_name : null,
                    $distributor->city,
                    $distributor->account_number ? 'Konto: ' . $distributor->account_number : null,
                ])->filter()->implode(' · ');

                return [
                    'id' => $distributor->id,
                    'text' => $extra ? $label . ' · ' . $extra : $label,
                    'name' => $distributor->name,
                    'short_name' => $distributor->short_name,
                    'city' => $distributor->city,
                    'account_number' => $distributor->account_number,
                ];
            })->values(),
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    public function select2ArticleGroups(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 20;

        $query = ArticleGroup::query()
            ->select(['id', 'article_group', 'initial'])
            ->orderBy('article_group');

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('article_group', 'like', '%' . $search . '%')
                    ->orWhere('initial', 'like', '%' . $search . '%');
            });
        }

        $total = (clone $query)->count();

        $groups = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'results' => $groups->map(function ($group) {
                $label = trim((string) $group->article_group);

                if ($group->initial) {
                    $label .= ' · ' . $group->initial;
                }

                return [
                    'id' => $group->id,
                    'text' => $label,
                    'article_group' => $group->article_group,
                    'initial' => $group->initial,
                ];
            })->values(),
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    public function select2SubArticleGroups(Request $request)
    {
        $search = trim((string) $request->input('q', ''));
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 20;
        $articleGroupId = $request->input('article_group_id');

        $query = SubArticleGroup::query()
            ->with('articleGroup:id,article_group')
            ->select(['id', 'article_group_id', 'sub_article', 'initial', 'value', 'status'])
            ->orderBy('sub_article');

        if ($articleGroupId) {
            $query->where('article_group_id', $articleGroupId);
        }

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('sub_article', 'like', '%' . $search . '%')
                    ->orWhere('initial', 'like', '%' . $search . '%')
                    ->orWhere('value', 'like', '%' . $search . '%');
            });
        }

        $total = (clone $query)->count();

        $subGroups = $query
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        return response()->json([
            'results' => $subGroups->map(function ($subGroup) {
                $label = trim((string) $subGroup->sub_article);

                if ($subGroup->initial) {
                    $label .= ' · ' . $subGroup->initial;
                }

                if ($subGroup->articleGroup?->article_group) {
                    $label .= ' · ' . $subGroup->articleGroup->article_group;
                }

                return [
                    'id' => $subGroup->id,
                    'text' => $label,
                    'sub_article' => $subGroup->sub_article,
                    'article_group_id' => $subGroup->article_group_id,
                ];
            })->values(),
            'pagination' => [
                'more' => ($page * $perPage) < $total,
            ],
        ]);
    }

    public function storeMapping(Request $request, SupplierConnection $supplierConnector)
    {
        $data = $this->validatedMappingData($request);

        $data['supplier_connection_id'] = $supplierConnector->id;
        $data['is_required'] = $request->boolean('is_required');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['sort_order'] = $data['sort_order'] ?? 0;

        SupplierConnectionMapping::create($data);

        return back()->with('success', 'Mapping wurde hinzugefügt.');
    }

    public function updateMapping(Request $request, SupplierConnectionMapping $mapping)
    {
        $data = $this->validatedMappingData($request);

        $data['is_required'] = $request->boolean('is_required');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $mapping->update($data);

        return back()->with('success', 'Mapping wurde aktualisiert.');
    }

    public function destroyMapping(SupplierConnectionMapping $mapping)
    {
        $mapping->delete();

        return back()->with('success', 'Mapping wurde gelöscht.');
    }

    public function duplicate(SupplierConnection $supplierConnector)
    {
        $newConnection = $supplierConnector->replicate([
            'last_test_status',
            'last_test_message',
            'last_tested_at',
        ]);

        $newConnection->name = $supplierConnector->name . ' Kopie';
        $newConnection->supplier_key = $this->makeUniqueSupplierKey($supplierConnector->supplier_key . '_copy');
        $newConnection->last_test_status = null;
        $newConnection->last_test_message = null;
        $newConnection->last_tested_at = null;
        $newConnection->save();

        foreach ($supplierConnector->mappings as $mapping) {
            $newMapping = $mapping->replicate();
            $newMapping->supplier_connection_id = $newConnection->id;
            $newMapping->save();
        }

        return redirect()
            ->route('admin.supplier-connectors.edit', $newConnection)
            ->with('success', 'Lieferanten-Schnittstelle wurde kopiert.');
    }

    public function applyPreset(Request $request, SupplierConnection $supplierConnector)
    {
        $request->validate([
            'preset' => [
                'required',
                'string',
                Rule::in([
                    'gc_online',
                    'standard_ids',
                    'standard_oci',
                    'empty_custom',
                ]),
            ],
        ]);

        $requestConfig = $supplierConnector->request_config ?? [];

        $requestConfig['auth_param_map'] = $this->authPreset($request->preset);

        if ($request->preset === 'gc_online') {
            $requestConfig['method'] = 'POST';
            $requestConfig['open_mode'] = 'search_then_auto_post';
            $requestConfig['basket_param'] = 'warenkorb';
            $requestConfig['search_param'] = 'searchterm';
        }

        if ($request->preset === 'standard_ids') {
            $requestConfig['method'] = 'GET';
            $requestConfig['open_mode'] = 'search_then_auto_post';
            $requestConfig['basket_param'] = $requestConfig['basket_param'] ?? 'warenkorb';
            $requestConfig['search_param'] = $requestConfig['search_param'] ?? 'searchterm';
        }

        if ($request->preset === 'standard_oci') {
            $requestConfig['method'] = 'POST';
            $requestConfig['open_mode'] = 'search_then_auto_post';
            $requestConfig['basket_param'] = $requestConfig['basket_param'] ?? 'OCI_DATA';
            $requestConfig['search_param'] = $requestConfig['search_param'] ?? 'searchterm';
        }

        $requestConfig['timeout'] = $requestConfig['timeout'] ?? 20;
        $requestConfig['content_type'] = $requestConfig['content_type'] ?? null;
        $requestConfig['extra_params'] = $requestConfig['extra_params'] ?? [];

        $supplierConnector->update([
            'request_config' => $requestConfig,
        ]);

        return back()->with('success', 'Login-Parameter Vorlage wurde übernommen.');
    }

    private function validatedConnectionData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'distributor_id' => ['nullable', 'exists:distributors,id'],
            'name' => ['required', 'string', 'max:255'],
            'supplier_key' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('supplier_connections', 'supplier_key')->ignore($ignoreId),
            ],
            'connector_type' => [
                'required',
                'string',
                Rule::in(['ids', 'oci', 'api', 'csv', 'xml', 'bmecat', 'datanorm']),
            ],
            'auth_type' => [
                'required',
                'string',
                Rule::in(['none', 'basic', 'token', 'bearer', 'custom']),
            ],
            'endpoint_url' => ['nullable', 'string', 'max:1000'],
            'test_url' => ['nullable', 'string', 'max:1000'],
            'return_url' => ['nullable', 'string', 'max:1000'],
            'username' => ['nullable', 'string', 'max:2000'],
            'password' => ['nullable', 'string', 'max:2000'],
            'customer_number' => ['nullable', 'string', 'max:2000'],
            'token' => ['nullable', 'string', 'max:4000'],
            'extra_auth_data' => ['nullable', 'string'],
            'extra_params' => ['nullable', 'string'],
            'auth_param_map' => ['nullable', 'string'],
            'request_method' => ['nullable', 'string', Rule::in(['GET', 'POST', 'PUT', 'PATCH'])],
            'open_mode' => ['nullable', 'string', Rule::in(['redirect_get', 'auto_post_form', 'search_then_auto_post'])],
            'request_content_type' => ['nullable', 'string', 'max:255'],
            'timeout' => ['nullable', 'integer', 'min:3', 'max:120'],
            'basket_param' => ['nullable', 'string', 'max:100'],
            'search_param' => ['nullable', 'string', 'max:100'],
            'match_by' => ['nullable', 'string', Rule::in(['ean', 'sku', 'article_no', 'supplier_article_number'])],
            'price_mode' => ['nullable', 'string', Rule::in(['purchase_price', 'sale_price', 'both'])],
            'update_existing' => ['nullable', 'boolean'],
            'create_missing' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function validatedMappingData(Request $request): array
    {
        return $request->validate([
            'source_field' => ['required', 'string', 'max:255'],
            'target_table' => [
                'required',
                'string',
                Rule::in([
                    'products',
                    'brands',
                    'distributors',
                    'distributor_prices',
                ]),
            ],
            'target_field' => ['required', 'string', 'max:255'],
            'transformer' => [
                'nullable',
                'string',
                Rule::in([
                    'text',
                    'decimal',
                    'integer',
                    'html_strip',
                    'uppercase',
                    'lowercase',
                    'boolean',
                ]),
            ],
            'default_value' => ['nullable', 'string'],
            'is_required' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer'],
        ]);
    }

    private function prepareConnectionPayload(
        Request $request,
        array $validated,
        ?SupplierConnection $existingConnection = null
    ): array {
        $supplierKey = $validated['supplier_key'] ?: $validated['name'];
        $requestConfig = $existingConnection?->request_config ?? [];

        return [
            'distributor_id' => $validated['distributor_id'] ?? null,
            'name' => $validated['name'],
            'supplier_key' => Str::slug($supplierKey, '_'),
            'connector_type' => $validated['connector_type'],
            'auth_type' => $validated['auth_type'],
            'endpoint_url' => $validated['endpoint_url'] ?? null,
            'test_url' => $validated['test_url'] ?? null,
            'return_url' => $validated['return_url'] ?? null,
            'username' => $request->input('username'),
            'password' => $request->filled('password')
                ? $request->input('password')
                : $existingConnection?->password,
            'customer_number' => $request->input('customer_number'),
            'token' => $request->filled('token')
                ? $request->input('token')
                : $existingConnection?->token,
            'extra_auth_data' => $this->jsonToArray(
                $request->input('extra_auth_data'),
                $existingConnection?->extra_auth_data ?? []
            ),
            'request_config' => [
                'method' => strtoupper($request->input(
                    'request_method',
                    $requestConfig['method'] ?? 'GET'
                )),
                'open_mode' => $request->input(
                    'open_mode',
                    $requestConfig['open_mode'] ?? 'search_then_auto_post'
                ),
                'content_type' => $request->input(
                    'request_content_type',
                    $requestConfig['content_type'] ?? null
                ),
                'timeout' => (int) $request->input(
                    'timeout',
                    $requestConfig['timeout'] ?? 20
                ),
                'basket_param' => $request->input(
                    'basket_param',
                    $requestConfig['basket_param'] ?? 'warenkorb'
                ),
                'search_param' => $request->input(
                    'search_param',
                    $requestConfig['search_param'] ?? 'searchterm'
                ),
                'extra_params' => $this->jsonToArray(
                    $request->input('extra_params'),
                    $requestConfig['extra_params'] ?? []
                ),
                'auth_param_map' => $this->resolveAuthParamMap($request, $requestConfig),
            ],
            'import_config' => [
                'match_by' => $request->input('match_by', 'ean'),
                'update_existing' => $request->boolean('update_existing', true),
                'create_missing' => $request->boolean('create_missing', true),
                'price_mode' => $request->input('price_mode', 'purchase_price'),
            ],
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function resolveAuthParamMap(Request $request, array $existingRequestConfig = []): array
    {
        $rawMap = $request->input('auth_param_map');

        $supplierName = strtolower((string) $request->input('name'));
        $supplierKey = strtolower((string) $request->input('supplier_key'));
        $endpointUrl = strtolower((string) $request->input('endpoint_url'));

        $isGcOnline =
            str_contains($supplierName, 'gc online')
            || str_contains($supplierName, 'gconline')
            || str_contains($supplierName, 'gc-online')
            || str_contains($supplierKey, 'gc')
            || str_contains($supplierKey, 'gc_online')
            || str_contains($supplierKey, 'gconline')
            || str_contains($endpointUrl, 'gconline');

        $isSonepar =
            str_contains($supplierName, 'sonepar')
            || str_contains($supplierKey, 'sonepar')
            || str_contains($endpointUrl, 'sonepar.de');

        /*
        |--------------------------------------------------------------------------
        | GC Online
        |--------------------------------------------------------------------------
        | GC Online must keep its own special IDS mapping.
        | Do not allow old/default mappings to override it.
        |--------------------------------------------------------------------------
        */
        if ($isGcOnline) {
            return $this->authPreset('gc_online');
        }

        /*
        |--------------------------------------------------------------------------
        | Sonepar IDS
        |--------------------------------------------------------------------------
        | Sonepar WKE must not receive USERNAME/PASSWORD/KUNDENNR/SEARCHTERM.
        | It requires only the IDS login fields below plus hookurl.
        |--------------------------------------------------------------------------
        */
        if ($isSonepar) {
            return [
                'kndnr' => '{customer_number}',
                'name_kunde' => '{username}',
                'pw_kunde' => '{password}',
                'hookurl' => '{callback_url}',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Custom user mapping
        |--------------------------------------------------------------------------
        | For all other suppliers, use the JSON from the form if it is valid.
        |--------------------------------------------------------------------------
        */
        if ($rawMap && trim((string) $rawMap) !== '') {
            $decoded = $this->jsonToArray($rawMap, []);

            if (!empty($decoded)) {
                return $decoded;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Existing mapping fallback
        |--------------------------------------------------------------------------
        | Keep saved mappings for normal suppliers.
        |--------------------------------------------------------------------------
        */
        if (
            !empty($existingRequestConfig['auth_param_map'])
            && is_array($existingRequestConfig['auth_param_map'])
        ) {
            return $existingRequestConfig['auth_param_map'];
        }

        return $this->authPreset('standard_ids');
    }
    private function jsonToArray(?string $json, array $fallback = []): array
    {
        if (!$json || trim($json) === '') {
            return $fallback;
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : $fallback;
    }

    private function authPreset(string $preset): array
    {
        return match ($preset) {
            'gc_online' => [
                'action' => 'AS',
                'version' => '2.5',
                'target' => 'TOP',
                'kndnr' => '{customer_number}',
                'name_kunde' => '{username}',
                'pw_kunde' => '{password}',
                'searchterm' => '{searchterm}',
                'hookurl' => '{callback_url}',
                'rueckurl' => '{return_url}',
            ],
            'standard_oci' => [
                'USERNAME' => '{username}',
                'PASSWORD' => '{password}',
                'HOOK_URL' => '{callback_url}',
                'RETURN_URL' => '{return_url}',
                'searchterm' => '{searchterm}',
            ],
            'empty_custom' => [],
            default => [
                'USERNAME' => '{username}',
                'PASSWORD' => '{password}',
                'KUNDENNR' => '{customer_number}',
                'RETURNURL' => '{callback_url}',
                'RUECKURL' => '{return_url}',
                'SEARCHTERM' => '{searchterm}',
            ],
        };
    }

    private function createDefaultMappingsIfMissing(SupplierConnection $connection): void
    {
        if ($connection->mappings()->exists()) {
            return;
        }

        $mappings = [
            ['NEW_ITEM-DESCRIPTION', 'products', 'product', 'text', true],
            ['NEW_ITEM-LONGTEXT', 'products', 'short_description', 'html_strip', false],
            ['NEW_ITEM-EAN', 'products', 'ean', 'text', false],
            ['NEW_ITEM-UNIT', 'products', 'measure_unit', 'text', false],
            ['NEW_ITEM-MANUFACTMAT', 'products', 'model', 'text', false],
            ['NEW_ITEM-MANUFACTCODE', 'brands', 'name', 'text', false],
            ['NEW_ITEM-VENDORMAT', 'distributor_prices', 'article_no', 'text', true],
            ['NEW_ITEM-PRICE', 'distributor_prices', 'purchase_price', 'decimal', false],
            ['NEW_ITEM-PRICE', 'distributor_prices', 'price', 'decimal', false],
        ];

        foreach ($mappings as $index => [$source, $table, $field, $transformer, $required]) {
            SupplierConnectionMapping::create([
                'supplier_connection_id' => $connection->id,
                'source_field' => $source,
                'target_table' => $table,
                'target_field' => $field,
                'transformer' => $transformer,
                'is_required' => $required,
                'is_active' => true,
                'sort_order' => $index + 1,
            ]);
        }
    }

    private function makeUniqueSupplierKey(string $baseKey): string
    {
        $key = Str::slug($baseKey, '_');
        $originalKey = $key;
        $counter = 1;

        while (SupplierConnection::where('supplier_key', $key)->exists()) {
            $key = $originalKey . '_' . $counter;
            $counter++;
        }

        return $key;
    }
}