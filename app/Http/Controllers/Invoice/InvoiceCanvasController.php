<?php

namespace App\Http\Controllers\Invoice;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\OfferDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InvoiceCanvasController extends Controller
{
    public function createFromOfferDetail(OfferDetail $offerDetail)
    {
        $offerDetail->load([
            'offer.customer',
            'offer.alternative',
            'branch',
        ]);

        $payload = $this->buildCanvasPayloadFromOfferDetail($offerDetail);

        return view('admin.invoices.canvas', [
            'mode' => 'create',
            'invoice' => null,
            'offerDetail' => $offerDetail,
            'payload' => $payload,
        ]);
    }

    public function storeDraftFromOfferDetail(Request $request, OfferDetail $offerDetail)
    {
        $offerDetail->load([
            'offer.customer',
            'offer.alternative',
            'branch',
        ]);

        $data = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'invoice_mode' => ['required', 'string', 'max:100'], // full, percentage, selected, final
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'issue_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'service_from' => ['nullable', 'date'],
            'service_to' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'payment_note' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($data, $offerDetail) {
            $offer = $offerDetail->offer;

            $invoice = Invoice::create([
                'account_id' => auth()->user()->current_tenant_id ?? null,

                'customer_id' => $offer?->customer_id,
                'object_id' => $offer?->alternative_id,
                'deal_id' => null,
                'offer_detail_id' => $offerDetail->id,

                'invoice_no' => $this->makeInvoiceNo(),
                'type' => $data['type'],
                'status' => 'draft',

                'issue_date' => $data['issue_date'] ?? now()->toDateString(),
                'due_date' => $data['due_date'] ?? now()->addDays(8)->toDateString(),
                'service_from' => $data['service_from'] ?? null,
                'service_to' => $data['service_to'] ?? null,

                'currency' => 'EUR',

                'subtotal' => 0,
                'tax_rate' => (float) ($offerDetail->tax_rate ?? 19),
                'tax_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,

                'payment_note' => $data['payment_note'] ?? null,
                'notes' => $data['notes'] ?? null,

                'created_by' => $this->employeeId(),
                'updated_by' => $this->employeeId(),
            ]);

            $items = $this->buildInvoiceItemsFromOfferDetail($offerDetail, $data);

            foreach ($items as $index => $item) {
                $qty = (float) ($item['qty'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineTotal = array_key_exists('line_total', $item)
                    ? (float) $item['line_total']
                    : round($qty * $unitPrice, 2);

                InvoiceItem::create($this->invoiceItemCreateData(
                    $invoice,
                    $item,
                    $index + 1,
                    $qty,
                    $unitPrice,
                    $lineTotal
                ));
            }

            $this->recalculateInvoiceTotals($invoice);
            $this->markAuftragSourceSynced($invoice, $offerDetail, $items);

            return response()->json([
                'ok' => true,
                'message' => 'Rechnungsentwurf wurde erstellt.',
                'invoice_id' => $invoice->id,
                'redirect_url' => route('invoices.canvas.edit', $invoice),
            ]);
        });
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load([
            'customer',
            'object',
            'offerDetail.offer.customer',
            'offerDetail.offer.alternative',
            'offerDetail.branch',
            'items',
            'files',
        ]);

        return view('admin.invoices.canvas', [
            'mode' => 'edit',
            'invoice' => $invoice,
            'offerDetail' => $invoice->offerDetail,
            'payload' => $this->buildCanvasPayloadFromInvoice($invoice),
        ]);
    }

    public function save(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'type' => ['required', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:50'],
            'issue_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'service_from' => ['nullable', 'date'],
            'service_to' => ['nullable', 'date'],
            'payment_note' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array'],
            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.article_product_id' => ['nullable', 'integer'],
            'items.*.component_id' => ['nullable', 'integer'],
            'items.*.distributor_id' => ['nullable', 'integer'],
            'items.*.distributor_price_id' => ['nullable', 'integer'],
            'items.*.distributor_article_no' => ['nullable', 'string', 'max:255'],
            'items.*.source_item_type' => ['nullable', 'string', 'max:100'],
            'items.*.source_item_id' => ['nullable'],
            'items.*.source_payload' => ['nullable'],
            'items.*.print_hidden' => ['nullable', 'boolean'],
            'items.*.group_title' => ['nullable', 'string', 'max:255'],
            'items.*.title' => ['required', 'string'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['nullable', 'numeric'],
            'items.*.unit' => ['nullable', 'string'],
            'items.*.unit_price' => ['nullable', 'numeric'],
            'items.*.line_total' => ['nullable', 'numeric'],
        ]);

        return DB::transaction(function () use ($invoice, $data) {
            $invoice->fill([
                'type' => $data['type'],
                'status' => $data['status'] ?? $invoice->status,
                'issue_date' => $data['issue_date'] ?? $invoice->issue_date,
                'due_date' => $data['due_date'] ?? $invoice->due_date,
                'service_from' => $data['service_from'] ?? null,
                'service_to' => $data['service_to'] ?? null,
                'payment_note' => $data['payment_note'] ?? null,
                'notes' => $data['notes'] ?? null,
                'updated_by' => $this->employeeId(),
            ]);

            $invoice->save();

            $invoice->items()->delete();

            foreach ($data['items'] as $index => $row) {
                $qty = (float) ($row['qty'] ?? 1);
                $unitPrice = (float) ($row['unit_price'] ?? 0);
                $lineTotal = array_key_exists('line_total', $row)
                    ? (float) $row['line_total']
                    : round($qty * $unitPrice, 2);

                InvoiceItem::create($this->invoiceItemCreateData(
                    $invoice,
                    $row,
                    $index + 1,
                    $qty,
                    $unitPrice,
                    $lineTotal
                ));
            }

            $this->recalculateInvoiceTotals($invoice);

            return response()->json([
                'ok' => true,
                'message' => 'Rechnung wurde gespeichert.',
                'invoice' => $invoice->fresh(['items']),
            ]);
        });
    }

    private function buildCanvasPayloadFromOfferDetail(OfferDetail $offerDetail): array
    {
        $offer = $offerDetail->offer;
        $customer = $offer?->customer;
        $object = $offer?->alternative;
        $branch = $offerDetail->branch;

        return [
            'document' => [
                'type' => 'Rechnung',
                'invoice_no' => null,
                'offer_no' => $offerDetail->offer_no ?: $offer?->offer_no,
                'issue_date' => now()->toDateString(),
                'due_date' => now()->addDays(8)->toDateString(),
                'service_from' => null,
                'service_to' => null,
                'tax_rate' => (float) ($offerDetail->tax_rate ?? 19),
            ],

            'company' => [
                'name' => $offerDetail->company_name ?: ($branch?->branch ?? 'SOLAR ASPEKT'),
                'brand_color' => $offerDetail->brand_color ?: ($branch?->color ?? '#93c21c'),
                'logo_url' => $offerDetail->brand_logo_url ?: ($branch?->logo_url ?? null),
                'footer' => $offerDetail->company_footer ?? [],
            ],

            'customer' => [
                'id' => $customer?->id,
                'customer_no' => $customer?->customer_no ?? null,
                'firma' => $customer?->firma ?? null,
                'name' => trim(($customer?->name ?? '') . ' ' . ($customer?->lastname ?? '')),
                'street' => $customer?->street ?? null,
                'postcode' => $customer?->postcode ?? null,
                'city' => $customer?->city ?? null,
            ],

            'object' => [
                'id' => $object?->id,
                'name' => $object?->object_name ?? null,
                'street' => $object?->street ?? null,
                'postcode' => $object?->postcode ?? null,
                'city' => $object?->city ?? null,
                'full_address' => $object?->full_address ?? null,
            ],

            'auftrag' => [
                'offer_detail_id' => $offerDetail->id,
                'offer_id' => $offer?->id,
                'offer_no' => $offerDetail->offer_no ?: $offer?->offer_no,
                'total_net' => (float) ($offerDetail->total_net ?? 0),
                'tax_rate' => (float) ($offerDetail->tax_rate ?? 19),
                'total_gross' => (float) ($offerDetail->total_gross ?? 0),
                'sections' => $offerDetail->sections ?? [],
            ],

            'items' => $this->flattenOfferDetailSections($offerDetail),
        ];
    }

    private function buildCanvasPayloadFromInvoice(Invoice $invoice): array
    {
        $offerDetail = $invoice->offerDetail;

        $payload = $offerDetail
            ? $this->buildCanvasPayloadFromOfferDetail($offerDetail)
            : [];

        $invoiceItems = $invoice->items
            ->sortBy('sort_order')
            ->map(fn($item) => [
                'product_id' => $item->product_id,
                'article_product_id' => $item->article_product_id ?? null,
                'component_id' => $item->component_id ?? null,
                'distributor_id' => $item->distributor_id ?? null,
                'distributor_price_id' => $item->distributor_price_id ?? null,
                'distributor_article_no' => $item->distributor_article_no ?? null,
                'source_item_type' => $item->source_item_type ?? null,
                'source_item_id' => $item->source_item_id ?? null,
                'source_payload' => $item->source_payload ?? null,
                'print_hidden' => (bool) ($item->print_hidden ?? false),
                'group_title' => $item->group_title ?? null,

                'title' => $item->title,
                'description' => $item->description,
                'qty' => (float) $item->qty,
                'unit' => $item->unit,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
                'sort_order' => (int) $item->sort_order,
            ])
            ->values()
            ->all();

        $latestAuftragItems = $offerDetail
            ? $this->flattenOfferDetailSections($offerDetail)
            : [];

        $latestHash = $offerDetail
            ? $this->makeInvoiceItemsHash($latestAuftragItems)
            : null;

        $invoiceItemsHash = $this->makeInvoiceItemsHash($invoiceItems);

        $savedSourceHash = $invoice->source_offer_items_hash ?: $invoiceItemsHash;

        $hasAuftragChanged = $offerDetail
            && $latestHash
            && $savedSourceHash
            && !hash_equals($savedSourceHash, $latestHash);

        $payload['document'] = array_merge($payload['document'] ?? [], [
            'id' => $invoice->id,
            'type' => $invoice->type,
            'invoice_no' => $invoice->invoice_no,
            'issue_date' => optional($invoice->issue_date)->toDateString(),
            'due_date' => optional($invoice->due_date)->toDateString(),
            'service_from' => optional($invoice->service_from)->toDateString(),
            'service_to' => optional($invoice->service_to)->toDateString(),
            'tax_rate' => (float) $invoice->tax_rate,
            'subtotal' => (float) $invoice->subtotal,
            'tax_amount' => (float) $invoice->tax_amount,
            'total_amount' => (float) $invoice->total_amount,
            'payment_note' => $invoice->payment_note,
            'notes' => $invoice->notes,
        ]);

        $payload['items'] = $invoiceItems;

        $payload['auftrag_sync'] = [
            'available' => (bool) $offerDetail,
            'changed' => (bool) $hasAuftragChanged,
            'invoice_item_count' => count($invoiceItems),
            'latest_item_count' => count($latestAuftragItems),
            'saved_hash' => $savedSourceHash,
            'latest_hash' => $latestHash,
            'source_offer_detail_id' => $invoice->source_offer_detail_id ?: $invoice->offer_detail_id,
            'source_offer_synced_at' => optional($invoice->source_offer_synced_at)->toDateTimeString(),
            'source_offer_updated_at' => optional($invoice->source_offer_updated_at)->toDateTimeString(),
            'latest_offer_updated_at' => optional($offerDetail?->updated_at)->toDateTimeString(),
            'sync_url' => route('invoices.canvas.sync-auftrag', $invoice),
        ];

        return $payload;
    }

    public function syncFromAuftrag(Request $request, Invoice $invoice)
    {
        $invoice->load([
            'offerDetail.offer.customer',
            'offerDetail.offer.alternative',
            'offerDetail.branch',
            'items',
        ]);

        $offerDetail = $invoice->offerDetail;

        if (!$offerDetail) {
            return response()->json([
                'ok' => false,
                'message' => 'Diese Rechnung ist mit keinem Auftrag/OfferDetail verbunden.',
            ], 422);
        }

        $items = $this->flattenOfferDetailSections($offerDetail);

        return DB::transaction(function () use ($invoice, $offerDetail, $items) {
            $invoice->items()->delete();

            foreach ($items as $index => $item) {
                $qty = (float) ($item['qty'] ?? 1);
                $unitPrice = (float) ($item['unit_price'] ?? 0);
                $lineTotal = array_key_exists('line_total', $item)
                    ? (float) $item['line_total']
                    : round($qty * $unitPrice, 2);

                $row = [
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'title' => $item['title'] ?? 'Position',
                    'description' => $item['description'] ?? null,
                    'qty' => $qty,
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $unitPrice,
                    'tax_rate' => (float) ($invoice->tax_rate ?? 19),
                    'line_total' => $lineTotal,
                    'sort_order' => $index + 1,
                ];

                foreach ([
                    'article_product_id',
                    'component_id',
                    'distributor_id',
                    'distributor_price_id',
                    'distributor_article_no',
                    'source_item_type',
                    'source_item_id',
                    'source_payload',
                    'print_hidden',
                    'group_title',
                ] as $column) {
                    if (Schema::hasColumn('invoice_items', $column)) {
                        $row[$column] = $item[$column] ?? null;
                    }
                }

                InvoiceItem::create($row);
            }

            $this->recalculateInvoiceTotals($invoice);
            $this->markAuftragSourceSynced($invoice->refresh(), $offerDetail, $items);

            $invoice->load('items');

            return response()->json([
                'ok' => true,
                'message' => 'Rechnung wurde mit dem aktuellen Auftrag synchronisiert.',
                'items' => $invoice->items
                    ->sortBy('sort_order')
                    ->map(fn($item) => [
                        'product_id' => $item->product_id,
                        'article_product_id' => $item->article_product_id ?? null,
                        'component_id' => $item->component_id ?? null,
                        'distributor_id' => $item->distributor_id ?? null,
                        'distributor_price_id' => $item->distributor_price_id ?? null,
                        'distributor_article_no' => $item->distributor_article_no ?? null,
                        'source_item_type' => $item->source_item_type ?? null,
                        'source_item_id' => $item->source_item_id ?? null,
                        'source_payload' => $item->source_payload ?? null,
                        'print_hidden' => (bool) ($item->print_hidden ?? false),
                        'group_title' => $item->group_title ?? null,

                        'title' => $item->title,
                        'description' => $item->description,
                        'qty' => (float) $item->qty,
                        'unit' => $item->unit,
                        'unit_price' => (float) $item->unit_price,
                        'line_total' => (float) $item->line_total,
                        'sort_order' => (int) $item->sort_order,
                    ])
                    ->values(),
                'auftrag_sync' => [
                    'available' => true,
                    'changed' => false,
                    'invoice_item_count' => $invoice->items->count(),
                    'latest_item_count' => count($items),
                    'saved_hash' => $this->makeInvoiceItemsHash($items),
                    'latest_hash' => $this->makeInvoiceItemsHash($items),
                    'source_offer_synced_at' => now()->toDateTimeString(),
                    'latest_offer_updated_at' => optional($offerDetail->updated_at)->toDateTimeString(),
                    'sync_url' => route('invoices.canvas.sync-auftrag', $invoice),
                ],
            ]);
        });
    }

    private function markAuftragSourceSynced(Invoice $invoice, OfferDetail $offerDetail, array $items): void
    {
        $updates = [];

        if (Schema::hasColumn('invoices', 'source_offer_detail_id')) {
            $updates['source_offer_detail_id'] = $offerDetail->id;
        }

        if (Schema::hasColumn('invoices', 'source_offer_items_hash')) {
            $updates['source_offer_items_hash'] = $this->makeInvoiceItemsHash($items);
        }

        if (Schema::hasColumn('invoices', 'source_offer_synced_at')) {
            $updates['source_offer_synced_at'] = now();
        }

        if (Schema::hasColumn('invoices', 'source_offer_updated_at')) {
            $updates['source_offer_updated_at'] = $offerDetail->updated_at ?: now();
        }

        if ($updates) {
            $invoice->forceFill($updates)->save();
        }
    }

    private function makeInvoiceItemsHash(array $items): string
    {
        $normalized = collect($items)
            ->map(function ($item) {
                return [
                    'source_item_id' => (string) ($item['source_item_id'] ?? $item['component_id'] ?? $item['article_product_id'] ?? $item['product_id'] ?? ''),
                    'product_id' => (int) ($item['product_id'] ?? 0),
                    'article_product_id' => (int) ($item['article_product_id'] ?? 0),
                    'component_id' => (int) ($item['component_id'] ?? 0),
                    'title' => trim((string) ($item['title'] ?? '')),
                    'description' => trim(strip_tags((string) ($item['description'] ?? ''))),
                    'qty' => round((float) ($item['qty'] ?? 0), 4),
                    'unit' => trim((string) ($item['unit'] ?? '')),
                    'unit_price' => round((float) ($item['unit_price'] ?? 0), 4),
                    'line_total' => round((float) ($item['line_total'] ?? 0), 4),
                ];
            })
            ->values()
            ->all();

        return hash('sha256', json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function buildInvoiceItemsFromOfferDetail(OfferDetail $offerDetail, array $options): array
    {
        $mode = $options['invoice_mode'] ?? 'full';

        if ($mode === 'percentage') {
            $percentage = (float) ($options['percentage'] ?? 0);
            $auftragNet = (float) ($offerDetail->total_net ?? 0);
            $lineTotal = round($auftragNet * ($percentage / 100), 2);

            $offerNo = $offerDetail->offer_no ?: $offerDetail->offer?->offer_no;
            $projectTitle = $this->projectTitle($offerDetail);

            return [
                [
                    'product_id' => $offerDetail->offer?->product_id,
                    'title' => "{$percentage}% Anzahlung für Auftrag {$offerNo}" . ($projectTitle ? " - {$projectTitle}" : ''),
                    'description' => null,
                    'qty' => 1,
                    'unit' => 'psch',
                    'unit_price' => $lineTotal,
                    'line_total' => $lineTotal,
                ]
            ];
        }

        return $this->flattenOfferDetailSections($offerDetail);
    }

    private function flattenOfferDetailSections(OfferDetail $offerDetail): array
    {
        $sections = $offerDetail->sections ?? [];
        $rows = [];

        foreach ($sections as $section) {
            foreach (($section['items'] ?? []) as $item) {
                $this->pushInvoiceItemFromSectionItem($rows, $item);
            }
        }

        return $rows;
    }

    private function pushInvoiceItemFromSectionItem(array &$rows, array $item): void
    {
        if (($item['active'] ?? true) === false) {
            return;
        }

        if (($item['print_hidden'] ?? false) === true) {
            return;
        }

        if (($item['status'] ?? 'normal') === 'inactive') {
            return;
        }

        $subItems = $item['subItems'] ?? [];

        // If the parent is a master set/package, invoice the inserted material positions inside it,
        // not both the package and its components. This prevents duplicated invoice positions.
        if (is_array($subItems) && count($subItems) > 0) {
            foreach ($subItems as $subItem) {
                if (is_array($subItem)) {
                    $this->pushInvoiceItemFromSectionItem($rows, $subItem);
                }
            }
            return;
        }

        $qty = (float) ($item['qty'] ?? 1);
        $unitPrice = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
        $lineTotal = round($qty * $unitPrice, 2);
        $sourceItemId = $item['id'] ?? $item['component_id'] ?? $item['productId'] ?? null;

        $sourcePayload = $item;
        $sourcePayload['invoice_canvas'] = array_merge($sourcePayload['invoice_canvas'] ?? [], [
            'print_hidden' => false,
            'group_title' => $item['group_title'] ?? $item['section_title'] ?? null,
        ]);

        $rows[] = [
            'product_id' => $item['product_id'] ?? $item['productId'] ?? null,
            'article_product_id' => $item['article_product_id'] ?? $item['productId'] ?? null,
            'component_id' => $item['component_id'] ?? null,
            'distributor_id' => $item['distributor_id'] ?? null,
            'distributor_price_id' => $item['distributor_price_id'] ?? null,
            'distributor_article_no' => $item['distributor_article_no'] ?? $item['article_no'] ?? null,
            'source_item_type' => $item['source_item_type'] ?? $item['item_type'] ?? $item['kind'] ?? null,
            'source_item_id' => $sourceItemId !== null ? (string) $sourceItemId : null,
            'source_payload' => $sourcePayload,
            'print_hidden' => false,
            'group_title' => $item['group_title'] ?? $item['section_title'] ?? null,

            'title' => $item['name'] ?? $item['title'] ?? 'Position',
            'description' => $item['desc_html'] ?? $item['desc'] ?? $item['description'] ?? null,
            'qty' => $qty,
            'unit' => $item['unit'] ?? $item['measure'] ?? null,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal,
        ];
    }

    private function invoiceItemCreateData(
        Invoice $invoice,
        array $row,
        int $sortOrder,
        float $qty,
        float $unitPrice,
        float $lineTotal
    ): array {
        $data = [
            'invoice_id' => $invoice->id,
            'product_id' => $row['product_id'] ?? null,
            'title' => $row['title'] ?? 'Position',
            'description' => $row['description'] ?? null,
            'qty' => $qty,
            'unit' => $row['unit'] ?? null,
            'unit_price' => $unitPrice,
            'tax_rate' => $invoice->tax_rate,
            'line_total' => $lineTotal,
            'sort_order' => $sortOrder,
        ];

        $optionalColumns = [
            'article_product_id',
            'component_id',
            'distributor_id',
            'distributor_price_id',
            'distributor_article_no',
            'source_item_type',
            'source_item_id',
            'source_payload',
            'print_hidden',
            'group_title',
        ];

        foreach ($optionalColumns as $column) {
            if (!Schema::hasColumn('invoice_items', $column)) {
                continue;
            }

            if (in_array($column, ['article_product_id', 'component_id', 'distributor_id', 'distributor_price_id'], true)) {
                $data[$column] = isset($row[$column]) && is_numeric($row[$column]) ? (int) $row[$column] : null;
                continue;
            }

            if ($column === 'source_item_id') {
                $data[$column] = isset($row[$column]) ? (string) $row[$column] : null;
                continue;
            }

            if ($column === 'source_payload') {
                $payload = $row['source_payload'] ?? [];
                if (is_string($payload)) {
                    $decoded = json_decode($payload, true);
                    $payload = is_array($decoded) ? $decoded : [];
                }
                if (!is_array($payload)) {
                    $payload = [];
                }
                $payload['invoice_canvas'] = array_merge($payload['invoice_canvas'] ?? [], [
                    'print_hidden' => (bool) ($row['print_hidden'] ?? false),
                    'group_title' => $row['group_title'] ?? null,
                ]);
                $data[$column] = $payload;
                continue;
            }

            if ($column === 'print_hidden') {
                $data[$column] = (bool) ($row[$column] ?? false);
                continue;
            }

            $data[$column] = isset($row[$column]) ? (string) $row[$column] : null;
        }

        return $data;
    }

    private function recalculateInvoiceTotals(Invoice $invoice): void
    {
        $subtotal = (float) $invoice->items()->sum('line_total');
        $taxRate = (float) ($invoice->tax_rate ?? 19);
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $totalAmount = round($subtotal + $taxAmount, 2);

        $invoice->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'updated_by' => $this->employeeId(),
        ])->save();
    }

    private function makeInvoiceNo(): string
    {
        $prefix = 'RE-' . now()->format('y');

        $lastNo = Invoice::query()
            ->where('invoice_no', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('invoice_no');

        $next = 1;

        if ($lastNo && preg_match('/(\d+)$/', $lastNo, $m)) {
            $next = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    private function projectTitle(OfferDetail $offerDetail): string
    {
        $object = $offerDetail->offer?->alternative;

        return trim((string) (
            $object?->object_name
            ?: $object?->street
            ?: $offerDetail->offer?->product?->article_group
            ?: ''
        ));
    }

    private function employeeId(): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if (is_numeric($user->name ?? null)) {
            return (int) $user->name;
        }

        if (!empty($user->employee_id) && is_numeric($user->employee_id)) {
            return (int) $user->employee_id;
        }

        return is_numeric($user->id ?? null) ? (int) $user->id : null;
    }
}