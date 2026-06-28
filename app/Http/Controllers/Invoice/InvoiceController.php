<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceFile;
use App\Models\InvoiceItem;
use App\Models\NewLeads;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    private const MAX_PER_PAGE = 50;
    private const MIN_PER_PAGE = 6;
    private const DEFAULT_PER_PAGE = 12;

    public function index()
    {
        return view('admin.invoices.index');
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $statuses = defined(Invoice::class . '::STATUS')
            ? Invoice::STATUS
            : ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in($statuses)],
        ]);

        $newStatus = $validated['status'];
        $before = $this->invoiceHistorySnapshot($invoice->fresh(['items']));

        $invoice->status = $newStatus;
        $invoice->updated_by = $this->employeeId();

        $this->applyStatusAccounting($invoice, $newStatus);
        $invoice->save();
        $this->recordInvoiceHistory($invoice, 'status_changed', $before, $this->invoiceHistorySnapshot($invoice), 'Status geändert');

        $invoice->refresh();

        return response()->json([
            'ok' => true,
            'status' => $invoice->status,
            'paid_amount' => (float) $invoice->paid_amount,
            'open_amount' => (float) $invoice->open_amount,
            'label' => match ($invoice->status) {
                'paid' => 'Bezahlt',
                'overdue' => 'Überfällig',
                'sent' => 'Gesendet',
                'cancelled' => 'Storniert',
                default => 'Entwurf',
            },
        ]);
    }

    /* =========================================================================
       LIST + ANALYTICS (AJAX)
       ========================================================================= */

    public function list(Request $request)
    {
        $perPage = (int) $request->input('per_page', self::DEFAULT_PER_PAGE);
        $perPage = max(self::MIN_PER_PAGE, min(self::MAX_PER_PAGE, $perPage));

        $q = Invoice::query()->with([
            'customer:id,name,lastname,firma',
            'object:id,lead_id,object_name,full_address,street,postcode,city',
            'creator:id,name',
            'files:id,invoice_id,stored_name,stored_path,original_name,size',
            'items:id,invoice_id,title,line_total',
        ]);

        // Search
        if ($search = trim((string) $request->input('search'))) {
            $q->where(function ($w) use ($search) {
                $w->where('invoice_no', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($c) use ($search) {
                        $c->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('firma', 'like', "%{$search}%");
                    })
                    ->orWhereHas('object', function ($o) use ($search) {
                        $o->where('object_name', 'like', "%{$search}%")
                            ->orWhere('full_address', 'like', "%{$search}%")
                            ->orWhere('street', 'like', "%{$search}%")
                            ->orWhere('postcode', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    });
            });
        }

        // Filters
        if (($type = $request->input('type')) !== null && $type !== '') {
            $q->where('type', (string) $type);
        }
        if (($status = $request->input('status')) !== null && $status !== '') {
            $q->where('status', (string) $status);
        }
        if (($customerId = $request->input('customer_id')) !== null && $customerId !== '') {
            $q->where('customer_id', (int) $customerId);
        }
        if (($objectId = $request->input('object_id')) !== null && $objectId !== '') {
            $q->where('object_id', (int) $objectId);
        }
        if ($from = $request->input('from')) {
            $q->whereDate('issue_date', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $q->whereDate('issue_date', '<=', $to);
        }

        // Sorting
        $sortBy = (string) $request->input('sort_by', 'issue_date');
        $sortDir = strtolower((string) $request->input('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSort = ['issue_date', 'invoice_no', 'total_amount', 'status', 'type', 'created_at'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'issue_date';
        }

        $p = $q->orderBy($sortBy, $sortDir)->paginate($perPage);

        // Analytics (same filtered query, no pagination)
        $base = (clone $q);

        $financialTypes = [
            'Rechnung',
            'Teilrechnung',
            'Abschlagsrechnung',
            'Schlussrechnung',
            'Anzahlung',
            'Zahlungserinnerung',
            'Mahnung',
            'Gutschrift',
            'Stornorechnung',
        ];

        $financialBase = (clone $base)->whereIn('type', $financialTypes);

        $sumTotal = (float) (clone $financialBase)
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->sum('total_amount');

        $paidSum = (float) (clone $financialBase)
            ->whereNotIn('status', ['cancelled', 'draft'])
            ->sum('paid_amount');

        $openSum = max(0, round($sumTotal - $paidSum, 2));

        $analytics = [
            'count_all' => (int) (clone $base)->count(),
            'sum_total' => $sumTotal,
            'paid_sum' => $paidSum,
            'open_sum' => $openSum,
            'unpaid_count' => (int) (clone $financialBase)
                ->whereNotIn('status', ['paid', 'cancelled', 'draft'])
                ->count(),
            'draft_count' => (int) (clone $base)->where('status', 'draft')->count(),
        ];


        $rows = collect($p->items())->map(function (Invoice $invoice) {
            $invoice->setAttribute('deal_summary', $this->dealSummaryForInvoice($invoice));
            $invoice->setAttribute('deal_balance', $this->dealBalanceForInvoice($invoice));
            return $invoice;
        })->values();

        return response()->json([
            'ok' => true,
            'analytics' => $analytics,
            'data' => $rows,
            'meta' => [
                'current_page' => $p->currentPage(),
                'last_page' => $p->lastPage(),
                'per_page' => $p->perPage(),
                'total' => $p->total(),
            ],
        ]);
    }

    /* =========================================================================
       CRUD (AJAX)  — NO AUTHORIZATION
       ========================================================================= */

    public function store(Request $request)
    {
        $validated = $this->validateInvoice($request);

        return DB::transaction(function () use ($validated) {
            $invoice = new Invoice();
            $invoice->fill($validated);

            $invoice->created_by = $this->employeeId();
            $invoice->updated_by = $this->employeeId();

            // auto number if empty
            if (empty($invoice->invoice_no)) {
                $invoice->invoice_no = method_exists(Invoice::class, 'makeInvoiceNo')
                    ? Invoice::makeInvoiceNo()
                    : ('INV-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4)));
            }

            $invoice->save();

            $this->syncItems($invoice, $validated['items'] ?? []);
            $this->recalcTotals($invoice);

            $invoice->refresh();
            $this->syncDealLimitSnapshot($invoice);
            $this->guardDealLimit($invoice);
            $this->applyStatusAccounting($invoice, $invoice->status);
            $invoice->updated_by = $this->employeeId();
            $invoice->save();
            $this->recordInvoiceHistory($invoice, 'created', null, $this->invoiceHistorySnapshot($invoice), 'Rechnung erstellt');

            return response()->json([
                'ok' => true,
                'invoice_id' => $invoice->id,
                'invoice' => $invoice->fresh(['customer', 'object', 'items', 'files']),
            ]);
        });
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $this->validateInvoice($request);

        return DB::transaction(function () use ($invoice, $validated) {
            // keep existing invoice_no if frontend sends null/empty
            $incomingNo = array_key_exists('invoice_no', $validated) ? trim((string) ($validated['invoice_no'] ?? '')) : '';
            if ($incomingNo === '') {
                unset($validated['invoice_no']);
            }

            $before = $this->invoiceHistorySnapshot($invoice->fresh(['items']));

            $invoice->fill($validated);
            $invoice->updated_by = $this->employeeId();
            $invoice->save();

            $this->syncItems($invoice, $validated['items'] ?? []);
            $this->recalcTotals($invoice);

            $invoice->refresh();
            $this->syncDealLimitSnapshot($invoice);
            $this->guardDealLimit($invoice);
            $this->applyStatusAccounting($invoice, $invoice->status);
            $invoice->updated_by = $this->employeeId();
            $invoice->save();
            $this->recordInvoiceHistory($invoice, 'updated', $before, $this->invoiceHistorySnapshot($invoice), 'Rechnung aktualisiert');

            return response()->json([
                'ok' => true,
                'invoice' => $invoice->fresh(['customer', 'object', 'items', 'files']),
            ]);
        });
    }

    public function destroy(Invoice $invoice)
    {
        return DB::transaction(function () use ($invoice) {
            $invoice->loadMissing('files:id,invoice_id,stored_path');

            foreach ($invoice->files as $f) {
                if ($f->stored_path) {
                    Storage::disk('public')->delete($f->stored_path);
                }
            }

            $invoice->delete();

            return response()->json(['ok' => true]);
        });
    }

    public function show(Invoice $invoice)
    {
        $invoice->load([
            'customer',
            'object',
            'creator',
            'updater',

            'deal',
            'deal.customer',
            'deal.product',
            'deal.alternative',
            'deal.author',
            'deal.checkedBy',
            'deal.reviewer',

            'offerDetail',
            'offerDetail.branch',
            'offerDetail.offer',
            'offerDetail.offer.customer',
            'offerDetail.offer.alternative',

            'items',
            'items.product',
            'files.uploader',
        ]);

        $invoice->setAttribute('deal_summary', $this->dealSummaryForInvoice($invoice));
        $invoice->setAttribute('deal_balance', $this->dealBalanceForInvoice($invoice));
        $invoice->setAttribute('invoice_history', $this->invoiceHistoryRows($invoice));
        $invoice->setAttribute('profile_details', $this->invoiceProfileDetails($invoice));

        return response()->json([
            'ok' => true,
            'invoice' => $invoice,
        ]);
    }

    private function invoiceProfileDetails(Invoice $invoice): array
    {
        $customer = $invoice->customer;
        $object = $invoice->object;
        $deal = $invoice->deal;
        $offerDetail = $invoice->offerDetail;

        $customerName = trim(
            ($customer?->firma ? $customer->firma . ' — ' : '') .
            trim(($customer?->name ?? '') . ' ' . ($customer?->lastname ?? ''))
        );

        $objectAddress = trim((string) (
            $object?->full_address
            ?: trim(($object?->street ?? '') . ' ' . ($object?->postcode ?? '') . ' ' . ($object?->city ?? ''))
        ));

        $dealNumber = null;
        if ($deal) {
            foreach (['order_number', 'deal_no', 'auftrag_no', 'offer_number', 'id'] as $column) {
                if ($column === 'id') {
                    $dealNumber = $deal->id;
                    break;
                }

                if (isset($deal->{$column}) && filled($deal->{$column})) {
                    $dealNumber = $deal->{$column};
                    break;
                }
            }
        }

        return [
            'document' => [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'type' => $invoice->type,
                'status' => $invoice->status,
                'currency' => $invoice->currency ?: 'EUR',
                'issue_date' => optional($invoice->issue_date)->toDateString(),
                'due_date' => optional($invoice->due_date)->toDateString(),
                'service_from' => optional($invoice->service_from)->toDateString(),
                'service_to' => optional($invoice->service_to)->toDateString(),
                'created_at' => optional($invoice->created_at)->toDateTimeString(),
                'updated_at' => optional($invoice->updated_at)->toDateTimeString(),
                'paid_at' => optional($invoice->paid_at)->toDateTimeString(),
                'created_by' => trim(($invoice->creator?->name ?? '') . ' ' . ($invoice->creator?->lastname ?? '')) ?: $invoice->created_by,
                'updated_by' => trim(($invoice->updater?->name ?? '') . ' ' . ($invoice->updater?->lastname ?? '')) ?: $invoice->updated_by,
            ],

            'customer' => [
                'id' => $customer?->id,
                'customer_no' => $customer?->customer_no,
                'name' => $customerName ?: null,
                'firma' => $customer?->firma,
                'firstname' => $customer?->name,
                'lastname' => $customer?->lastname,
                'email' => $customer?->email,
                'phone' => $customer?->phone,
                'telephone' => $customer?->telephone,
                'street' => $customer?->street,
                'postcode' => $customer?->postcode,
                'city' => $customer?->city,
                'full_address' => trim(($customer?->street ?? '') . ' ' . ($customer?->postcode ?? '') . ' ' . ($customer?->city ?? '')) ?: null,
            ],

            'object' => [
                'id' => $object?->id,
                'name' => $object?->object_name,
                'street' => $object?->street,
                'postcode' => $object?->postcode,
                'city' => $object?->city,
                'full_address' => $objectAddress ?: null,
                'lat' => $object?->lat ?? $object?->latitude ?? null,
                'lon' => $object?->lon ?? $object?->longitude ?? null,
            ],

            'deal' => [
                'id' => $deal?->id,
                'number' => $dealNumber,
                'status' => $deal?->status,
                'deal_status' => $deal?->deal_status,
                'project_status' => $deal?->project_status,
                'order_number' => $deal?->order_number,
                'offer_number' => $deal?->offer_number,
                'price' => $deal?->price,
                'sign_date' => $deal?->sign_date,
                'confirmed_at' => $deal?->confirmed_at,
                'delivered_at' => $deal?->delivered_at,
                'product' => $deal?->product?->article_group ?? $deal?->product?->display_name ?? null,
                'employee' => trim(($deal?->author?->name ?? '') . ' ' . ($deal?->author?->lastname ?? '')) ?: $deal?->employee_id,
                'checked_by' => trim(($deal?->checkedBy?->name ?? '') . ' ' . ($deal?->checkedBy?->lastname ?? '')) ?: $deal?->checked_by,
                'reviewer' => trim(($deal?->reviewer?->name ?? '') . ' ' . ($deal?->reviewer?->lastname ?? '')) ?: $deal?->reviewer_id,
            ],

            'offer_detail' => [
                'id' => $offerDetail?->id,
                'offer_id' => $offerDetail?->offer_id,
                'offer_folder_id' => $offerDetail?->offer_folder_id,
                'offer_no' => $offerDetail?->offer_no ?: $offerDetail?->offer?->offer_no,
                'document_status' => $offerDetail?->document_status,
                'company_name' => $offerDetail?->company_name,
                'branch' => $offerDetail?->branch?->branch,
                'total_net' => (float) ($offerDetail?->total_net ?? 0),
                'tax_rate' => (float) ($offerDetail?->tax_rate ?? 0),
                'total_gross' => (float) ($offerDetail?->total_gross ?? 0),
            ],

            'financial' => [
                'subtotal' => (float) ($invoice->subtotal ?? 0),
                'tax_rate' => (float) ($invoice->tax_rate ?? 0),
                'tax_amount' => (float) ($invoice->tax_amount ?? 0),
                'total_amount' => (float) ($invoice->total_amount ?? 0),
                'paid_amount' => (float) ($invoice->paid_amount ?? 0),
                'open_amount' => (float) ($invoice->open_amount ?? 0),
                'deal_limit_amount' => (float) ($invoice->deal_limit_amount ?? 0),
                'deal_remaining_before' => (float) ($invoice->deal_remaining_before ?? 0),
                'deal_remaining_after' => (float) ($invoice->deal_remaining_after ?? 0),
            ],

            'texts' => [
                'payment_note' => $invoice->payment_note,
                'notes' => $invoice->notes,
            ],

            'items' => $invoice->items->map(function (InvoiceItem $item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_group' => $item->product?->article_group ?? $item->product?->display_name ?? null,
                    'article_product_id' => $item->article_product_id ?? null,
                    'component_id' => $item->component_id ?? null,
                    'distributor_id' => $item->distributor_id ?? null,
                    'distributor_price_id' => $item->distributor_price_id ?? null,
                    'distributor_article_no' => $item->distributor_article_no ?? null,
                    'source_item_type' => $item->source_item_type ?? null,
                    'source_item_id' => $item->source_item_id ?? null,
                    'title' => $item->title,
                    'description' => $item->description,
                    'qty' => (float) $item->qty,
                    'unit' => $item->unit,
                    'unit_price' => (float) $item->unit_price,
                    'tax_rate' => (float) $item->tax_rate,
                    'line_total' => (float) $item->line_total,
                    'sort_order' => (int) $item->sort_order,
                ];
            })->values(),
        ];
    }
    
    /* =========================================================================
       FILES (AJAX) — NO AUTHORIZATION
       ========================================================================= */

    public function uploadFiles(Request $request, Invoice $invoice)
    {
        $request->validate([
            'files' => ['required', 'array', 'min:1'],
            'files.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp,gif', 'max:20480'], // 20MB
        ]);

        $invoice->loadMissing(['customer:id,name,lastname,firma', 'items:id,invoice_id,title']);

        $stored = [];

        foreach ($request->file('files', []) as $file) {
            $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');
            $storedName = $this->buildPdfName($invoice, $ext);

            $dir = "customer_invoice/{$invoice->id}";
            $path = $file->storeAs($dir, $storedName, 'public');

            $row = InvoiceFile::create([
                'invoice_id' => $invoice->id,
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $storedName,
                'stored_path' => $path,
                'mime' => $file->getClientMimeType(),
                'size' => (int) $file->getSize(),
                'uploaded_by' => $this->employeeId(),
            ]);

            $stored[] = $row;
            $this->recordInvoiceHistory($invoice, 'file_uploaded', null, [
                'file_id' => $row->id,
                'name' => $row->original_name,
                'mime' => $row->mime,
            ], 'Datei hochgeladen');
        }

        return response()->json(['ok' => true, 'files' => $stored]);
    }

    public function deleteFile(InvoiceFile $file)
    {
        $invoice = $file->invoice;
        if ($invoice) {
            $this->recordInvoiceHistory($invoice, 'file_deleted', [
                'file_id' => $file->id,
                'name' => $file->original_name,
                'mime' => $file->mime,
            ], null, 'Datei gelöscht');
        }

        if ($file->stored_path) {
            Storage::disk('public')->delete($file->stored_path);
        }

        $file->delete();

        return response()->json(['ok' => true]);
    }

    public function downloadFile(InvoiceFile $file)
    {
        if (!$file->stored_path || !Storage::disk('public')->exists($file->stored_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $file->stored_path,
            $file->stored_name ?: ($file->original_name ?: 'invoice.pdf')
        );
    }

    /* =========================================================================
       SELECT2 ENDPOINTS (AJAX)
       ========================================================================= */

    public function selectCustomers(Request $request)
    {
        $term = trim((string) $request->input('term', ''));

        $q = NewLeads::query()
            ->select(['id', 'name', 'lastname', 'firma'])
            // ✅ make sure we don't accidentally exclude records
            ->when($term !== '', function ($x) use ($term) {
                $x->where(function ($w) use ($term) {
                    $w->where('name', 'like', "%{$term}%")
                        ->orWhere('lastname', 'like', "%{$term}%")
                        ->orWhere('firma', 'like', "%{$term}%")
                        // ✅ optional: allow searching by id too
                        ->orWhereRaw('CAST(id as CHAR) like ?', ["%{$term}%"]);
                });
            })
            // ✅ stable ordering even with nulls
            ->orderByRaw("COALESCE(firma,'') asc")
            ->orderByRaw("COALESCE(lastname,'') asc")
            ->orderByRaw("COALESCE(name,'') asc")
            // ✅ Select2 is search-based; raising this helps “missing” perception
            ->limit(50)
            ->get();

        return response()->json([
            'results' => $q->map(function ($c) {
                $firma = trim((string) ($c->firma ?? ''));
                $name = trim((string) ($c->name ?? ''));
                $ln = trim((string) ($c->lastname ?? ''));

                // ✅ Always show First + Last; Firma only if present
                $full = trim($name . ' ' . $ln);
                $text = $firma ? trim($firma . ' — ' . $full) : $full;

                // ✅ Never "Lead #", but also never return empty (Select2 can hide empty labels)
                if ($text === '')
                    $text = 'Unbenannt';

                return ['id' => $c->id, 'text' => $text];
            })->values(),
        ]);
    }

    public function selectObjects(Request $request)
    {
        $term = trim((string) $request->input('term'));
        $customerId = $request->input('customer_id');

        $q = \App\Models\LeadAlternativeAdd::query()
            ->select(['id', 'lead_id', 'object_name', 'full_address', 'street', 'postcode', 'city'])
            ->when($customerId, fn($x) => $x->where('lead_id', (int) $customerId))
            ->when($term !== '', function ($x) use ($term) {
                $x->where(function ($w) use ($term) {
                    $w->where('object_name', 'like', "%{$term}%")
                        ->orWhere('full_address', 'like', "%{$term}%")
                        ->orWhere('street', 'like', "%{$term}%")
                        ->orWhere('postcode', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%");
                });
            })
            ->orderByRaw("COALESCE(object_name,'') asc")
            ->limit(20)
            ->get();

        return response()->json([
            'results' => $q->map(function ($o) {
                $name = trim((string) ($o->object_name ?: ''));
                $addr = trim((string) ($o->full_address ?: (($o->street ?: '') . ' ' . ($o->postcode ?: '') . ' ' . ($o->city ?: ''))));
                $text = trim(($name !== '' ? $name . ' — ' : '') . $addr);
                $text = $text !== '' ? $text : ('Objekt #' . $o->id);
                return ['id' => $o->id, 'text' => $text];
            })->values(),
        ]);
    }

    public function selectProducts(Request $request)
    {
        $term = trim((string) $request->input('term'));
        $customerId = (int) $request->input('customer_id', 0);
        $objectId = (int) $request->input('object_id', 0);

        // Base products query
        $q = \App\Models\ArticleGroup::query()
            ->select(['id', 'article_group', 'min_value', 'max_value']);

        // Filter to products linked to the selected customer + object
        // Uses lead_product_lists (adjust table/columns if yours differ)
        if ($customerId > 0 && $objectId > 0) {
            $q->whereIn('id', function ($sub) use ($customerId, $objectId) {
                $sub->from('lead_product_lists')
                    ->select('product_id')
                    ->where('customer_id', $customerId)
                    ->where('alternative_id', $objectId)
                    ->whereNotNull('product_id');
            });
        } elseif ($customerId > 0) {
            // Optional: if only customer selected, show all products linked to that customer
            $q->whereIn('id', function ($sub) use ($customerId) {
                $sub->from('lead_product_lists')
                    ->select('product_id')
                    ->where('customer_id', $customerId)
                    ->whereNotNull('product_id');
            });
        }

        // Search by text
        if ($term !== '') {
            $q->where('article_group', 'like', "%{$term}%");
        }

        $rows = $q->orderBy('article_group')->limit(25)->get();

        return response()->json([
            'results' => $rows->map(fn($p) => [
                'id' => $p->id,
                'text' => $p->article_group,
                'min_value' => (float) ($p->min_value ?? 0),
                'max_value' => (float) ($p->max_value ?? 0),
            ])->values(),
        ]);
    }




    private function dealNumberExpression(): string
    {
        $parts = [];
        foreach (['order_number', 'deal_no', 'auftrag_no', 'id'] as $column) {
            if ($column === 'id' || Schema::hasColumn('deals', $column)) {
                $parts[] = $column === 'id' ? 'deals.id' : 'deals.' . $column;
            }
        }
        return 'COALESCE(' . implode(', ', $parts) . ')';
    }
    public function selectDeals(Request $request)
    {
        $term = trim((string) $request->input('term', ''));
        $customerId = (int) $request->input('customer_id', 0);
        $objectId = (int) $request->input('object_id', 0);

        if (!Schema::hasTable('deals')) {
            return response()->json(['results' => []]);
        }

        $q = DB::table('deals')
            ->leftJoin('new_leads', 'new_leads.id', '=', 'deals.customer_id')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'deals.alternative_id')
            ->select([
                'deals.*',
                DB::raw($this->dealNumberExpression() . ' as deal_number'),
                DB::raw("TRIM(CONCAT(COALESCE(new_leads.firma,''),' ',COALESCE(new_leads.name,''),' ',COALESCE(new_leads.lastname,''))) as customer_name"),
                DB::raw("COALESCE(alt.object_name, alt.full_address, CONCAT(COALESCE(alt.street,''),' ',COALESCE(alt.postcode,''),' ',COALESCE(alt.city,''))) as object_label"),
            ]);

        if (Schema::hasColumn('deals', 'deleted_at')) {
            $q->whereNull('deals.deleted_at');
        }

        $q->when($customerId > 0, fn($x) => $x->where('deals.customer_id', $customerId))
            ->when($objectId > 0, fn($x) => $x->where('deals.alternative_id', $objectId));

        if ($term !== '') {
            $q->where(function ($w) use ($term) {
                $w->where('deals.id', 'like', "%{$term}%");

                foreach (['order_number', 'deal_no', 'auftrag_no', 'offer_number'] as $column) {
                    if (Schema::hasColumn('deals', $column)) {
                        $w->orWhere('deals.' . $column, 'like', "%{$term}%");
                    }
                }

                $w->orWhere('new_leads.firma', 'like', "%{$term}%")
                    ->orWhere('new_leads.name', 'like', "%{$term}%")
                    ->orWhere('new_leads.lastname', 'like', "%{$term}%")
                    ->orWhere('alt.object_name', 'like', "%{$term}%")
                    ->orWhere('alt.full_address', 'like', "%{$term}%");
            });
        }

        $rows = $q->orderByDesc('deals.id')->limit(30)->get();

        return response()->json([
            'results' => $rows->map(function ($deal) {
                $offerDetail = $this->findOfferDetailForDeal($deal);
                $limit = $this->resolveDealLimitAmount((int) $deal->id);
                $balance = $this->dealInvoiceBalance((int) $deal->id, null, $limit);

                $text = trim(
                    ($deal->deal_number ? ('#' . $deal->deal_number . ' — ') : '')
                    . ($deal->customer_name ?: 'Kunde')
                    . ' — '
                    . ($deal->object_label ?: 'Objekt')
                );

                return [
                    'id' => (int) $deal->id,
                    'text' => $text,
                    'customer_id' => (int) ($deal->customer_id ?? 0),
                    'object_id' => (int) ($deal->alternative_id ?? 0),
                    'product_id' => (int) ($deal->product_id ?? 0),
                    'offer_id' => isset($deal->offer_id) ? (int) $deal->offer_id : null,
                    'offer_folder_id' => isset($deal->offer_folder_id) ? (int) $deal->offer_folder_id : null,
                    'offer_detail_id' => $offerDetail ? (int) $offerDetail->id : null,
                    'deal_limit_amount' => $limit,
                    'invoiced_amount' => $balance['invoiced_amount'],
                    'remaining_amount' => $balance['remaining_amount'],
                ];
            })->values(),
        ]);
    }


    /* =========================================================================
       VALIDATION + HELPERS
       ========================================================================= */
    public function dealItems(Request $request, int $dealId)
    {
        if (!Schema::hasTable('deals')) {
            return response()->json([
                'ok' => false,
                'message' => 'Deals-Tabelle wurde nicht gefunden.',
                'items' => [],
            ], 404);
        }

        $deal = DB::table('deals')->where('id', $dealId)->first();

        if (!$deal) {
            return response()->json([
                'ok' => false,
                'message' => 'Deal nicht gefunden.',
                'items' => [],
            ], 404);
        }

        $offerDetail = $this->findOfferDetailForDeal($deal);

        if (!$offerDetail) {
            return response()->json([
                'ok' => false,
                'message' => 'Kein OfferDetail für diesen Deal gefunden.',
                'items' => [],
            ], 404);
        }

        $sections = $this->decodeOfferSections($offerDetail->sections ?? []);
        $items = $this->invoiceItemsFromOfferSections(
            $sections,
            (int) ($deal->product_id ?? 0),
            true
        );

        return response()->json([
            'ok' => true,
            'deal_id' => (int) $deal->id,
            'offer_detail_id' => (int) $offerDetail->id,
            'items' => $items,
            'total_net' => (float) ($offerDetail->total_net ?? 0),
            'tax_rate' => (float) ($offerDetail->tax_rate ?? 19),
            'total_gross' => (float) ($offerDetail->total_gross ?? 0),
        ]);
    }

    private function findOfferDetailForDeal(object $deal): ?object
    {
        if (!Schema::hasTable('offer_details')) {
            return null;
        }

        $offerId = (int) ($deal->offer_id ?? 0);
        $folderId = (int) ($deal->offer_folder_id ?? 0);
        $customerId = (int) ($deal->customer_id ?? 0);
        $productId = (int) ($deal->product_id ?? 0);
        $alternativeId = (int) ($deal->alternative_id ?? 0);

        if (
            Schema::hasColumn('deals', 'offer_detail_id')
            && !empty($deal->offer_detail_id)
        ) {
            $found = DB::table('offer_details')
                ->where('id', (int) $deal->offer_detail_id)
                ->first();

            if ($found) {
                return $found;
            }
        }

        if ($offerId > 0 && Schema::hasColumn('offer_details', 'offer_id')) {
            $q = DB::table('offer_details')->where('offer_id', $offerId);

            if ($folderId > 0 && Schema::hasColumn('offer_details', 'offer_folder_id')) {
                $q->where('offer_folder_id', $folderId);
            }

            $found = $q->orderByDesc('id')->first();

            if ($found) {
                return $found;
            }
        }

        if ($folderId > 0 && Schema::hasColumn('offer_details', 'offer_folder_id')) {
            $found = DB::table('offer_details')
                ->where('offer_folder_id', $folderId)
                ->orderByDesc('id')
                ->first();

            if ($found) {
                return $found;
            }
        }

        if (Schema::hasTable('offers') && Schema::hasColumn('offer_details', 'offer_id')) {
            $q = DB::table('offer_details')
                ->join('offers', 'offers.id', '=', 'offer_details.offer_id')
                ->select('offer_details.*');

            if ($customerId > 0 && Schema::hasColumn('offers', 'customer_id')) {
                $q->where('offers.customer_id', $customerId);
            }

            if ($productId > 0 && Schema::hasColumn('offers', 'product_id')) {
                $q->where('offers.product_id', $productId);
            }

            if ($alternativeId > 0 && Schema::hasColumn('offers', 'alternative_id')) {
                $q->where('offers.alternative_id', $alternativeId);
            }

            $found = $q->orderByDesc('offer_details.id')->first();

            if ($found) {
                return $found;
            }
        }

        return null;
    }

    private function decodeOfferSections(mixed $sections): array
    {
        if (is_array($sections)) {
            return $sections;
        }

        if (is_string($sections) && trim($sections) !== '') {
            $decoded = json_decode($sections, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function invoiceItemsFromOfferSections(
        array $sections,
        ?int $articleGroupId = null,
        bool $includeHiddenComponents = true
    ): array {
        $rows = [];
        $sort = 0;

        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $items = $section['items'] ?? [];

            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $item) {
                if (!is_array($item)) {
                    continue;
                }

                if (($item['active'] ?? true) === false) {
                    continue;
                }

                if (($item['status'] ?? 'normal') === 'inactive') {
                    continue;
                }

                $subItems = $item['subItems'] ?? [];

                if (is_array($subItems) && count($subItems) > 0) {
                    foreach ($subItems as $subItem) {
                        if (!is_array($subItem)) {
                            continue;
                        }

                        if (($subItem['active'] ?? true) === false) {
                            continue;
                        }

                        if (($subItem['status'] ?? 'normal') === 'inactive') {
                            continue;
                        }

                        if (!$includeHiddenComponents && (($subItem['print_hidden'] ?? false) === true)) {
                            continue;
                        }

                        $rows[] = $this->makeInvoiceItemRowFromOfferItem(
                            $subItem,
                            $articleGroupId,
                            $sort++
                        );
                    }

                    continue;
                }

                if (($item['print_hidden'] ?? false) === true) {
                    continue;
                }

                $rows[] = $this->makeInvoiceItemRowFromOfferItem(
                    $item,
                    $articleGroupId,
                    $sort++
                );
            }
        }

        return $rows;
    }

    private function makeInvoiceItemRowFromOfferItem(array $item, ?int $articleGroupId, int $sort): array
    {
        $qty = (float) ($item['qty'] ?? 1);
        $unitPrice = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
        $lineTotal = round($qty * $unitPrice, 2);

        $descriptionParts = [];
        $desc = $item['desc_html'] ?? $item['desc'] ?? null;

        if ($desc) {
            $clean = trim(strip_tags((string) $desc));
            if ($clean !== '') {
                $descriptionParts[] = $clean;
            }
        }

        if (!empty($item['distributor_name'])) {
            $descriptionParts[] = 'Lieferant: ' . (string) $item['distributor_name'];
        }

        if (!empty($item['distributor_article_no'])) {
            $descriptionParts[] = 'Artikel-Nr.: ' . (string) $item['distributor_article_no'];
        } elseif (!empty($item['article_no'])) {
            $descriptionParts[] = 'Artikel-Nr.: ' . (string) $item['article_no'];
        }

        $sourceItemId = $item['id'] ?? $item['component_id'] ?? $item['productId'] ?? null;

        return [
            // Existing invoice_items.product_id points to article_groups.id.
            'product_id' => $articleGroupId ?: null,

            // Optional source fields. Cast everything to the correct type here,
            // so frontend validation/saving does not fail when IDs are numeric.
            'article_product_id' => isset($item['productId']) && is_numeric($item['productId']) ? (int) $item['productId'] : null,
            'component_id' => isset($item['component_id']) && is_numeric($item['component_id']) ? (int) $item['component_id'] : null,
            'distributor_id' => isset($item['distributor_id']) && is_numeric($item['distributor_id']) ? (int) $item['distributor_id'] : null,
            'distributor_price_id' => isset($item['distributor_price_id']) && is_numeric($item['distributor_price_id']) ? (int) $item['distributor_price_id'] : null,
            'distributor_article_no' => isset($item['distributor_article_no'])
                ? (string) $item['distributor_article_no']
                : (isset($item['article_no']) ? (string) $item['article_no'] : null),
            'source_item_type' => isset($item['item_type'])
                ? (string) $item['item_type']
                : (isset($item['kind']) ? (string) $item['kind'] : null),
            'source_item_id' => $sourceItemId !== null ? (string) $sourceItemId : null,
            'source_payload' => $item,

            'title' => trim((string) ($item['name'] ?? 'Materialposition')),
            'description' => trim(implode("\n", array_filter($descriptionParts))) ?: null,
            'qty' => $qty,
            'unit' => isset($item['measure'])
                ? (string) $item['measure']
                : (isset($item['unit']) ? (string) $item['unit'] : 'Stk.'),
            'unit_price' => $unitPrice,
            'tax_rate' => 0,
            'line_total' => $lineTotal,
            'sort_order' => $sort,
        ];
    }
    private function validateInvoice(Request $request): array
    {
        $types = $this->invoiceTypes();
        $statuses = defined(Invoice::class . '::STATUS')
            ? Invoice::STATUS
            : ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

        return $request->validate([
            'customer_id' => ['required', 'integer'],
            'object_id' => ['nullable', 'integer'],
            'deal_id' => ['nullable', 'integer'],
            'offer_detail_id' => ['nullable', 'integer'],
            'deal_limit_amount' => ['nullable', 'numeric', 'min:0'],

            'invoice_no' => ['nullable', 'string', 'max:50'],

            'type' => ['required', 'string', 'max:60', Rule::in($types)],
            'status' => ['required', 'string', 'max:30', Rule::in($statuses)],

            'issue_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:issue_date'],
            'service_from' => ['nullable', 'date'],
            'service_to' => ['nullable', 'date', 'after_or_equal:service_from'],

            'currency' => ['nullable', 'string', 'size:3'],
            'tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'payment_note' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],

            'items.*.product_id' => ['nullable', 'integer'],
            'items.*.article_product_id' => ['nullable', 'integer'],
            'items.*.component_id' => ['nullable', 'integer'],
            'items.*.distributor_id' => ['nullable', 'integer'],
            'items.*.distributor_price_id' => ['nullable', 'integer'],
            'items.*.distributor_article_no' => ['nullable', 'string', 'max:255'],
            'items.*.source_item_type' => ['nullable', 'string', 'max:100'],
            'items.*.source_item_id' => ['nullable'],
            'items.*.source_payload' => ['nullable'],

            'items.*.title' => ['required', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit' => ['nullable', 'string', 'max:30'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
    private function syncItems(Invoice $invoice, array $items): void
    {
        $invoice->items()->delete();

        $rows = [];

        foreach ($items as $i => $it) {
            $qty = (float) ($it['qty'] ?? 1);
            $unitPrice = (float) ($it['unit_price'] ?? 0);
            $lineTotal = round($qty * $unitPrice, 2);

            $row = [
                'product_id' => $this->nullableInt($it['product_id'] ?? null),
                'title' => trim((string) ($it['title'] ?? 'Position')),
                'description' => $it['description'] ?? null,
                'qty' => $qty,
                'unit' => isset($it['unit']) ? (string) $it['unit'] : null,
                'unit_price' => $unitPrice,
                'tax_rate' => (float) ($it['tax_rate'] ?? 0),
                'line_total' => $lineTotal,
                'sort_order' => (int) ($it['sort_order'] ?? $i),
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
            ] as $column) {
                if (!Schema::hasColumn('invoice_items', $column)) {
                    continue;
                }

                if ($column === 'source_payload') {
                    $row[$column] = $it[$column] ?? null;
                    continue;
                }

                if ($column === 'source_item_id') {
                    $row[$column] = isset($it[$column]) && $it[$column] !== '' ? (string) $it[$column] : null;
                    continue;
                }

                if (in_array($column, ['article_product_id', 'component_id', 'distributor_id', 'distributor_price_id'], true)) {
                    $row[$column] = $this->nullableInt($it[$column] ?? null);
                    continue;
                }

                $row[$column] = isset($it[$column]) && $it[$column] !== '' ? (string) $it[$column] : null;
            }

            $rows[] = new InvoiceItem($row);
        }

        if ($rows) {
            $invoice->items()->saveMany($rows);
        }
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
    }


    /**
     * Keep invoice status and payment fields in sync everywhere.
     *
     * Important: the edit drawer sends `status` through the normal PUT /invoices/{invoice}
     * endpoint. The list quick-select uses PATCH /invoices/{invoice}/status.
     * Both must update paid_amount/paid_at the same way, otherwise a paid invoice
     * still appears as open in analytics and customer summaries.
     */
    private function applyStatusAccounting(Invoice $invoice, ?string $status = null): void
    {
        $status = (string) ($status ?: $invoice->status ?: 'draft');
        $total = round((float) ($invoice->total_amount ?? 0), 2);

        $invoice->status = $status;

        if ($status === 'paid') {
            $invoice->paid_amount = $total;
            $invoice->paid_at = $invoice->paid_at ?: now();
            return;
        }

        if ($status === 'cancelled') {
            $invoice->paid_amount = 0;
            $invoice->paid_at = null;
            return;
        }

        // The current form has no partial-payment input. If the user changes a
        // document away from paid, reset payment fields so open amount is correct.
        if (in_array($status, ['draft', 'sent', 'overdue'], true)) {
            $invoice->paid_amount = 0;
            $invoice->paid_at = null;
        }
    }

    private function recalcTotals(Invoice $invoice): void
    {
        if (method_exists($invoice, 'recalcTotals')) {
            $invoice->refresh()->recalcTotals();
            return;
        }

        $invoice->loadMissing('items:id,invoice_id,line_total');

        $subtotal = (float) $invoice->items->sum('line_total');
        $taxRate = (float) ($invoice->tax_rate ?? 0);
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $total = round($subtotal + $taxAmount, 2);

        $invoice->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
        ])->save();
    }


    private function syncDealLimitSnapshot(Invoice $invoice): void
    {
        if (!Schema::hasColumn('invoices', 'deal_id') || empty($invoice->deal_id)) {
            return;
        }

        $limit = (float) ($invoice->deal_limit_amount ?: 0);
        if ($limit <= 0) {
            $limit = $this->resolveDealLimitAmount((int) $invoice->deal_id);
        }

        $balance = $this->dealInvoiceBalance((int) $invoice->deal_id, (int) $invoice->id, $limit);

        $updates = [];
        if (Schema::hasColumn('invoices', 'deal_limit_amount')) {
            $updates['deal_limit_amount'] = $limit;
        }
        if (Schema::hasColumn('invoices', 'deal_remaining_before')) {
            $updates['deal_remaining_before'] = $balance['remaining_amount'];
        }
        if (Schema::hasColumn('invoices', 'deal_remaining_after')) {
            $updates['deal_remaining_after'] = round($balance['remaining_amount'] - $this->signedInvoiceAmount($invoice), 2);
        }

        if ($updates) {
            $invoice->forceFill($updates)->save();
            $invoice->refresh();
        }
    }

    private function guardDealLimit(Invoice $invoice): void
    {
        if (!Schema::hasColumn('invoices', 'deal_id') || empty($invoice->deal_id)) {
            return;
        }

        if ($invoice->status === 'cancelled') {
            return;
        }

        $limit = (float) ($invoice->deal_limit_amount ?: $this->resolveDealLimitAmount((int) $invoice->deal_id));
        if ($limit <= 0) {
            return;
        }

        $balance = $this->dealInvoiceBalance((int) $invoice->deal_id, (int) $invoice->id, $limit);
        $amount = $this->signedInvoiceAmount($invoice);

        if ($amount > $balance['remaining_amount'] + 0.01) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'deal_id' => [
                    'Dieser Auftrag hat nur noch ' . number_format($balance['remaining_amount'], 2, ',', '.') . ' € offen. Diese Rechnung hat ' . number_format($amount, 2, ',', '.') . ' €.',
                ],
            ]);
        }
    }

    private function dealInvoiceBalance(int $dealId, ?int $excludeInvoiceId = null, ?float $limit = null): array
    {
        $limit = $limit ?? $this->resolveDealLimitAmount($dealId);

        $query = Invoice::query()
            ->where('deal_id', $dealId)
            ->whereNotIn('status', ['cancelled', 'draft']);

        if ($excludeInvoiceId) {
            $query->where('id', '<>', $excludeInvoiceId);
        }

        $rows = $query->get(['id', 'type', 'status', 'total_amount']);
        $invoiced = round($rows->sum(fn(Invoice $invoice) => $this->signedInvoiceAmount($invoice)), 2);

        return [
            'deal_limit_amount' => round((float) $limit, 2),
            'invoiced_amount' => $invoiced,
            'remaining_amount' => round(max(0, (float) $limit - $invoiced), 2),
        ];
    }

    private function signedInvoiceAmount(Invoice $invoice): float
    {
        $type = mb_strtolower((string) $invoice->type);
        $sign = in_array($type, ['gutschrift', 'stornorechnung'], true) ? -1 : 1;
        return round($sign * (float) ($invoice->total_amount ?? 0), 2);
    }
    private function resolveDealLimitAmount(int $dealId): float
    {
        if (!Schema::hasTable('deals')) {
            return 0.0;
        }

        $deal = DB::table('deals')->where('id', $dealId)->first();

        if (!$deal) {
            return 0.0;
        }

        foreach (['total_gross', 'total_amount', 'gross_total', 'final_total', 'auftrag_total', 'amount', 'price'] as $column) {
            if (
                Schema::hasColumn('deals', $column)
                && isset($deal->{$column})
                && (float) $deal->{$column} > 0
            ) {
                return round((float) $deal->{$column}, 2);
            }
        }

        $offerDetail = $this->findOfferDetailForDeal($deal);

        if ($offerDetail) {
            foreach (['total_gross', 'total_amount', 'total_net'] as $column) {
                if (isset($offerDetail->{$column}) && (float) $offerDetail->{$column} > 0) {
                    return round((float) $offerDetail->{$column}, 2);
                }
            }

            $sections = $this->decodeOfferSections($offerDetail->sections ?? []);
            $items = $this->invoiceItemsFromOfferSections(
                $sections,
                (int) ($deal->product_id ?? 0),
                true
            );

            $subtotal = collect($items)->sum(fn($item) => (float) ($item['line_total'] ?? 0));

            if ($subtotal > 0) {
                $taxRate = (float) ($offerDetail->tax_rate ?? 19);

                return round($subtotal + ($subtotal * ($taxRate / 100)), 2);
            }
        }

        return 0.0;
    }


    private function dealSummaryForInvoice(Invoice $invoice): ?array
    {
        if (!Schema::hasColumn('invoices', 'deal_id') || empty($invoice->deal_id) || !Schema::hasTable('deals')) {
            return null;
        }

        $deal = DB::table('deals')
            ->leftJoin('new_leads', 'new_leads.id', '=', 'deals.customer_id')
            ->leftJoin('lead_alternative_adds as alt', 'alt.id', '=', 'deals.alternative_id')
            ->where('deals.id', (int) $invoice->deal_id)
            ->select([
                'deals.id',
                DB::raw($this->dealNumberExpression() . ' as deal_number'),
                DB::raw("TRIM(CONCAT(COALESCE(new_leads.firma,''),' ',COALESCE(new_leads.name,''),' ',COALESCE(new_leads.lastname,''))) as customer_name"),
                DB::raw("COALESCE(alt.object_name, alt.full_address, CONCAT(COALESCE(alt.street,''),' ',COALESCE(alt.postcode,''),' ',COALESCE(alt.city,''))) as object_label"),
            ])
            ->first();

        if (!$deal) {
            return null;
        }

        return [
            'id' => (int) $deal->id,
            'deal_number' => $deal->deal_number,
            'customer_name' => $deal->customer_name,
            'object_label' => $deal->object_label,
        ];
    }

    private function dealBalanceForInvoice(Invoice $invoice): ?array
    {
        if (!Schema::hasColumn('invoices', 'deal_id') || empty($invoice->deal_id)) {
            return null;
        }

        return $this->dealInvoiceBalance((int) $invoice->deal_id, (int) $invoice->id, (float) ($invoice->deal_limit_amount ?: 0));
    }

    private function invoiceHistorySnapshot(?Invoice $invoice): ?array
    {
        if (!$invoice) {
            return null;
        }

        return [
            'status' => $invoice->status,
            'type' => $invoice->type,
            'invoice_no' => $invoice->invoice_no,
            'deal_id' => $invoice->deal_id ?? null,
            'deal_limit_amount' => $invoice->deal_limit_amount ?? null,
            'subtotal' => (float) ($invoice->subtotal ?? 0),
            'tax_amount' => (float) ($invoice->tax_amount ?? 0),
            'total_amount' => (float) ($invoice->total_amount ?? 0),
            'paid_amount' => (float) ($invoice->paid_amount ?? 0),
            'open_amount' => (float) ($invoice->open_amount ?? 0),
            'notes' => $invoice->notes,
        ];
    }


    private function invoiceHistoryRows(Invoice $invoice): array
    {
        if (!Schema::hasTable('invoice_histories')) {
            return [];
        }

        return DB::table('invoice_histories')
            ->leftJoin('employees', 'employees.id', '=', 'invoice_histories.employee_id')
            ->where('invoice_histories.invoice_id', $invoice->id)
            ->orderByDesc('invoice_histories.id')
            ->limit(100)
            ->get([
                'invoice_histories.*',
                DB::raw("TRIM(CONCAT(COALESCE(employees.name,''),' ',COALESCE(employees.lastname,''))) as employee_name"),
            ])
            ->map(function ($row) {
                $row->old_values = $row->old_values ? json_decode($row->old_values, true) : null;
                $row->new_values = $row->new_values ? json_decode($row->new_values, true) : null;
                return $row;
            })
            ->values()
            ->all();
    }

    private function recordInvoiceHistory(Invoice $invoice, string $eventType, ?array $old, ?array $new, ?string $note = null): void
    {
        if (!Schema::hasTable('invoice_histories')) {
            return;
        }

        DB::table('invoice_histories')->insert([
            'invoice_id' => $invoice->id,
            'employee_id' => $this->employeeId(),
            'event_type' => $eventType,
            'old_status' => $old['status'] ?? null,
            'new_status' => $new['status'] ?? null,
            'old_values' => $old ? json_encode($old, JSON_UNESCAPED_UNICODE) : null,
            'new_values' => $new ? json_encode($new, JSON_UNESCAPED_UNICODE) : null,
            'note' => $note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function buildPdfName(Invoice $invoice, string $ext = 'pdf'): string
    {
        $invoice->loadMissing(['customer:id,name,lastname,firma', 'items:id,invoice_id,title']);

        $customer = $invoice->customer;
        $customerName = trim(($customer->firma ?? '') . ' ' . ($customer->lastname ?? '') . ' ' . ($customer->name ?? ''));
        $customerName = $customerName !== '' ? $customerName : 'customer';
        $customerSlug = Str::slug(Str::limit($customerName, 50, ''), '_');

        $date = ($invoice->issue_date instanceof Carbon)
            ? $invoice->issue_date->format('Y-m-d')
            : Carbon::parse($invoice->issue_date)->format('Y-m-d');

        $products = $invoice->items
            ->pluck('title')
            ->filter()
            ->map(fn($t) => Str::slug(Str::limit((string) $t, 22, ''), '_'))
            ->values();

        $prodPart = $products->take(3)->implode('+');
        if ($products->count() > 3)
            $prodPart .= '+more';
        if ($prodPart === '')
            $prodPart = 'items';

        $total = number_format((float) ($invoice->total_amount ?? 0), 2, '.', '');
        $totalPart = 'total-' . $total;

        $rand = Str::lower(Str::random(6));
        $name = "{$customerSlug}_{$date}_{$prodPart}_{$totalPart}_{$rand}.{$ext}";
        $name = preg_replace('/_+/', '_', $name);

        return $name ?: ("invoice_{$invoice->id}_{$rand}.{$ext}");
    }

    private function invoiceTypes(): array
    {
        return [
            'Rechnung',
            'Teilrechnung',
            'Abschlagsrechnung',
            'Schlussrechnung',
            'Anzahlung',
            'Zahlungserinnerung',
            'Mahnung',
            'Gutschrift',
            'Stornorechnung',
            'Proforma',
            'Angebot',
            'Auftrag',
            'Lieferschein',
            'Quittung',
        ];
    }

    private function employeeId(): ?int
    {
        $u = auth()->user();
        if (!$u)
            return null;

        // your system stores employee_id in user->name
        $raw = $u->name;
        $raw = is_string($raw) ? trim($raw) : $raw;

        if (is_int($raw))
            return $raw;
        if (is_string($raw) && ctype_digit($raw))
            return (int) $raw;

        return null;
    }

    public function viewFile(InvoiceFile $file)
    {
        if (!$file->stored_path || !Storage::disk('public')->exists($file->stored_path)) {
            abort(404);
        }

        $downloadName = $file->original_name ?: ($file->stored_name ?: 'invoice-file');
        $mime = $file->mime ?: Storage::disk('public')->mimeType($file->stored_path) ?: 'application/octet-stream';

        return Storage::disk('public')->response(
            $file->stored_path,
            $downloadName,
            [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
            ]
        );
    }


}
