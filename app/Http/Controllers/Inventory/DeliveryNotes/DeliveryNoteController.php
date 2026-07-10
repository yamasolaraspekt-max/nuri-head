<?php

namespace App\Http\Controllers\Inventory\DeliveryNotes;
use App\Http\Controllers\Controller;

use App\Models\Branch;
use App\Models\Deal;
use App\Models\DeliveryNote;
use App\Models\Employee;
use App\Models\LeadProductList;
use App\Models\NewLeads;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Throwable; 
class DeliveryNoteController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update', 'updateProgress', 'toggleStatus']);
        $this->middleware('permission:Product,delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $branches = Branch::select('id', 'branch')->orderBy('branch')->get();
        $employees = Employee::select('id', 'name', 'lastname', 'image')->orderBy('name')->get();

        $deliveryNoteOptions = DeliveryNote::select('id', 'delivery_note')
            ->orderBy('delivery_note')
            ->get();

        return view('admin.product.delivery.index', compact(
            'branches',
            'employees',
            'deliveryNoteOptions'
        ));
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('q', ''));

        $query = LeadProductList::query()
            ->with([
                'customer:id,customer_no,firma,name,lastname,city',
                'alternative:id,lead_id,object_name,street,postcode,city',
                'product:id,article_group',
            ])
            ->whereHas('customer');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', function ($sub) use ($search) {
                    $sub->where('customer_no', 'like', "%{$search}%")
                        ->orWhere('firma', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                })
                ->orWhereHas('alternative', function ($sub) use ($search) {
                    $sub->where('object_name', 'like', "%{$search}%")
                        ->orWhere('street', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                })
                ->orWhereHas('product', function ($sub) use ($search) {
                    $sub->where('article_group', 'like', "%{$search}%");
                });
            });
        }

        $results = $query
            ->orderByDesc('id')
            ->limit(30)
            ->get()
            ->map(function ($row) {
                $customer = $row->customer;
                $alternative = $row->alternative;
                $product = $row->product;

                $customerName = $customer?->display_name
                    ?? (($customer?->firma ?: trim(($customer?->name ?? '') . ' ' . ($customer?->lastname ?? ''))) ?: '#' . $customer?->id);

                $objectName = $alternative
                    ? ($alternative->object_name ?: trim(($alternative->street ?? '') . ' ' . ($alternative->city ?? '')))
                    : 'Ohne Objekt';

                $productName = $product?->article_group ?: ($row->service ?: 'Produkt');

                return [
                    'id' => $row->id,
                    'text' => $customerName . ' — ' . $productName . ' — ' . $objectName,
                    'customer_id' => $row->customer_id,
                    'alternative_id' => $row->alternative_id,
                    'lead_product_list_id' => $row->id,
                    'product_id' => $row->product_id,
                ];
            })
            ->values();

        return response()->json(['results' => $results]);
    }

    public function findDeal(Request $request): JsonResponse
    {
        $customerId = $request->get('customer_id');
        $alternativeId = $request->get('alternative_id');
        $productId = $request->get('product_id');

        $deal = Deal::query()
            ->where('customer_id', $customerId)
            ->when($alternativeId, fn ($q) => $q->where('alternative_id', $alternativeId))
            ->when($productId, fn ($q) => $q->where('product_id', $productId))
            ->orderByDesc('id')
            ->first();

        return response()->json([
            'deal' => $deal ? [
                'id' => $deal->id,
                'text' => $deal->order_number ?: '#' . $deal->id,
            ] : null,
        ]);
    }

    public function analytics(): JsonResponse
    {
        return response()->json([
            'total' => DeliveryNote::count(),
            'available' => DeliveryNote::where('status', 'Verfügbar')->count(),
            'unavailable' => DeliveryNote::where('status', 'Nicht verfügbar')->count(),
            'partial' => DeliveryNote::where('status', 'Teilweise')->count(),
            'completed' => DeliveryNote::where('progress', 100)->count(),
            'with_pdf' => DeliveryNote::whereNotNull('pdf')->where('pdf', '!=', '')->count(),
            'linked' => DeliveryNote::whereNotNull('linked_delivery_note_id')->count(),
        ]);
    }

    public function list(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));
        $branchId = $request->get('branch_id');
        $employeeId = $request->get('handover_by');
        $progress = $request->get('progress');
        $destinationType = trim((string) $request->get('destination_type', ''));
        $customerId = $request->get('customer_id');
        $dealId = $request->get('deal_id');
        $sort = trim((string) $request->get('sort', 'latest'));

        $relations = [
            'branch:id,branch',
            'handoverEmployee:id,name,lastname,image',
            'customer:id,customer_no,firma,name,lastname,city',
            'alternative:id,lead_id,object_name,street,postcode,city',
            'leadProductList:id,customer_id,alternative_id,product_id,service',
            'leadProductList.product:id,article_group',
            'deal:id,customer_id,alternative_id,product_id,order_number,offer_number,deal_status,status',
            'deal.product:id,article_group',
            'images:id,delivery_note_id,name,image',
        ];

        $query = DeliveryNote::query()
            ->with($relations)
            ->with([
                'linkedNotes' => function ($q) use ($relations) {
                    $q->with($relations)->orderBy('id', 'asc');
                },
            ])
            ->whereNull('linked_delivery_note_id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('delivery_note', 'like', "%{$search}%")
                    ->orWhere('delivered_from', 'like', "%{$search}%")
                    ->orWhere('destination_type', 'like', "%{$search}%")
                    ->orWhere('order_by', 'like', "%{$search}%")
                    ->orWhere('order_no', 'like', "%{$search}%")
                    ->orWhere('comission', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('branch', fn ($sub) =>
                        $sub->where('branch', 'like', "%{$search}%")
                    )
                    ->orWhereHas('handoverEmployee', fn ($sub) =>
                        $sub->where('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                    )
                    ->orWhereHas('customer', fn ($sub) =>
                        $sub->where('customer_no', 'like', "%{$search}%")
                            ->orWhere('firma', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                    )
                    ->orWhereHas('alternative', fn ($sub) =>
                        $sub->where('object_name', 'like', "%{$search}%")
                            ->orWhere('street', 'like', "%{$search}%")
                            ->orWhere('postcode', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%")
                    )
                    ->orWhereHas('leadProductList.product', fn ($sub) =>
                        $sub->where('article_group', 'like', "%{$search}%")
                    )
                    ->orWhereHas('deal', fn ($sub) =>
                        $sub->where('order_number', 'like', "%{$search}%")
                            ->orWhere('offer_number', 'like', "%{$search}%")
                            ->orWhere('deal_status', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%")
                    )
                    ->orWhereHas('deal.product', fn ($sub) =>
                        $sub->where('article_group', 'like', "%{$search}%")
                    )
                    ->orWhereHas('linkedNotes', fn ($sub) =>
                        $sub->where('delivery_note', 'like', "%{$search}%")
                            ->orWhere('delivered_from', 'like', "%{$search}%")
                            ->orWhere('order_no', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                    );
            });
        }

        $query
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->when(!empty($branchId), fn ($q) => $q->where('branch_id', $branchId))
            ->when(!empty($employeeId), fn ($q) => $q->where('handover_by', $employeeId))
            ->when($destinationType !== '', fn ($q) => $q->where('destination_type', $destinationType))
            ->when(!empty($customerId), fn ($q) => $q->where('customer_id', $customerId))
            ->when(!empty($dealId), fn ($q) => $q->where('deal_id', $dealId));

        if ($progress !== null && $progress !== '') {
            if ($progress === 'complete') {
                $query->where('progress', 100);
            } elseif ($progress === 'open') {
                $query->where('progress', '<', 100);
            } elseif (is_numeric($progress)) {
                $query->where('progress', (int) $progress);
            }
        }

        match ($sort) {
            'oldest' => $query->orderBy('id', 'asc'),
            'delivery_note_asc' => $query->orderBy('delivery_note', 'asc'),
            'delivery_note_desc' => $query->orderBy('delivery_note', 'desc'),
            'date_asc' => $query->orderBy('handover_date', 'asc'),
            'date_desc' => $query->orderBy('handover_date', 'desc'),
            'progress_asc' => $query->orderBy('progress', 'asc'),
            'progress_desc' => $query->orderBy('progress', 'desc'),
            default => $query->orderBy('id', 'desc'),
        };

        $content = $query->paginate(10)->appends($request->query());

        return response()->json([
            'html' => view('admin.product.delivery.partials.table', compact('content'))->render(),
            'pagination' => view('admin.product.delivery.partials.pagination', compact('content'))->render(),
            'counts' => [
                'total' => $content->total(),
                'from' => $content->firstItem(),
                'to' => $content->lastItem(),
            ],
        ]);
    }

    public function customerRelatedData(NewLeads $customer): JsonResponse
    {
        $customer->load([
            'leadProductLists:id,customer_id,alternative_id,product_id,service,status,work_status',
            'leadProductLists.product:id,article_group',
            'leadProductLists.alternative:id,lead_id,object_name,street,postcode,city',
        ]);

        $products = $customer->leadProductLists->map(function ($row) {
            $productName = $row->product?->article_group ?? 'Produkt';
            $objectName = $row->alternative
                ? ($row->alternative->object_name ?: trim(($row->alternative->street ?? '') . ' ' . ($row->alternative->city ?? '')))
                : 'Ohne Objekt';

            return [
                'id' => $row->id,
                'text' => $productName . ' — ' . $objectName,
                'customer_id' => $row->customer_id,
                'alternative_id' => $row->alternative_id,
                'lead_product_list_id' => $row->id,
                'product_id' => $row->product_id,
            ];
        })->values();

        $deals = Deal::query()
            ->with(['product:id,article_group'])
            ->where('customer_id', $customer->id)
            ->orderByDesc('id')
            ->get()
            ->map(function ($deal) {
                return [
                    'id' => $deal->id,
                    'text' => ($deal->order_number ?: '#' . $deal->id) . ' — ' . ($deal->product?->article_group ?? 'Produkt'),
                    'customer_id' => $deal->customer_id,
                    'alternative_id' => $deal->alternative_id,
                    'product_id' => $deal->product_id,
                ];
            })
            ->values();

        return response()->json([
            'customer' => [
                'id' => $customer->id,
                'text' => $customer->display_name,
            ],
            'products' => $products,
            'deals' => $deals,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = $this->validator($request);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $deliveryNote = new DeliveryNote();
            $this->fillDeliveryNote($deliveryNote, $request);
            $this->handleFiles($deliveryNote, $request);
            $deliveryNote->save();

            return response()->json([
                'message' => 'Datensatz erfolgreich gespeichert.',
                'data' => $deliveryNote->load(['branch', 'handoverEmployee']),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Speichern fehlgeschlagen.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(DeliveryNote $deliveryNote): JsonResponse
    {
        $deliveryNote->load([
            'branch:id,branch',
            'handoverEmployee:id,name,lastname,image',
            'images:id,delivery_note_id,name,image',
            'linkedDeliveryNote:id,delivery_note',
            'linkedNotes.branch:id,branch',
            'linkedNotes.handoverEmployee:id,name,lastname,image',
            'customer:id,customer_no,firma,name,lastname,city',
            'alternative:id,lead_id,object_name,street,postcode,city',
            'leadProductList:id,customer_id,alternative_id,product_id,service',
            'leadProductList.product:id,article_group',
            'deal:id,customer_id,alternative_id,product_id,order_number,offer_number',
            'deal.product:id,article_group',
        ]);

        return response()->json(['data' => $deliveryNote]);
    }

    public function update(Request $request, DeliveryNote $deliveryNote): JsonResponse
    {
        $validator = $this->validator($request, $deliveryNote->id);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validierungsfehler.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->fillDeliveryNote($deliveryNote, $request);
            $this->handleFiles($deliveryNote, $request);
            $deliveryNote->save();

            return response()->json([
                'message' => 'Lieferschein erfolgreich aktualisiert.',
                'data' => $deliveryNote->load(['branch', 'handoverEmployee']),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Aktualisierung fehlgeschlagen.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(DeliveryNote $deliveryNote): JsonResponse
    {
        try {
            $this->deleteFile('images/delivery_note/' . $deliveryNote->image);
            $this->deleteFile('images/delivery_note/pdf/' . $deliveryNote->pdf);

            $deliveryNote->delete();

            return response()->json([
                'message' => 'Der Datensatz wurde erfolgreich gelöscht.',
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Löschen fehlgeschlagen.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProgress(Request $request, DeliveryNote $deliveryNote): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'progress' => 'required|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Ungültiger Fortschritt.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $deliveryNote->progress = (int) $request->progress;
        $deliveryNote->save();

        return response()->json([
            'message' => 'Fortschritt erfolgreich aktualisiert.',
            'data' => [
                'id' => $deliveryNote->id,
                'progress' => $deliveryNote->progress,
            ],
        ]);
    }

    public function uploadPdf(Request $request, DeliveryNote $deliveryNote): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pdf' => 'required|mimes:pdf|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Bitte eine gültige PDF-Datei hochladen.',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $this->deleteFile('images/delivery_note/pdf/' . $deliveryNote->pdf);

            $pdfName = uniqid('delivery_pdf_') . '.' . $request->file('pdf')->getClientOriginalExtension();
            $request->file('pdf')->move(public_path('images/delivery_note/pdf'), $pdfName);

            $deliveryNote->pdf = $pdfName;
            $deliveryNote->save();

            return response()->json([
                'message' => 'PDF-Datei erfolgreich gespeichert.',
                'data' => [
                    'id' => $deliveryNote->id,
                    'pdf' => $deliveryNote->pdf,
                ],
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'PDF konnte nicht gespeichert werden.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStatus(DeliveryNote $deliveryNote): JsonResponse
    {
        $deliveryNote->status = match ($deliveryNote->status) {
            'Verfügbar' => 'Nicht verfügbar',
            'Nicht verfügbar' => 'Teilweise',
            default => 'Verfügbar',
        };

        $deliveryNote->save();

        return response()->json([
            'message' => 'Status erfolgreich aktualisiert.',
            'data' => [
                'id' => $deliveryNote->id,
                'status' => $deliveryNote->status,
            ],
        ]);
    }

    public function linked(DeliveryNote $deliveryNote): JsonResponse
    {
        $deliveryNote->load([
            'branch:id,branch',
            'handoverEmployee:id,name,lastname,image',
            'images:id,delivery_note_id,name,image',
            'linkedDeliveryNote.branch:id,branch',
            'linkedDeliveryNote.handoverEmployee:id,name,lastname,image',
            'linkedDeliveryNote.images:id,delivery_note_id,name,image',
            'linkedNotes.branch:id,branch',
            'linkedNotes.handoverEmployee:id,name,lastname,image',
            'linkedNotes.images:id,delivery_note_id,name,image',
        ]);

        return response()->json([
            'data' => $deliveryNote,
            'parent' => $deliveryNote->linkedDeliveryNote,
            'children' => $deliveryNote->linkedNotes,
        ]);
    }

    protected function validator(Request $request, ?int $ignoreId = null)
    {
        return Validator::make($request->all(), [
            'delivery_note' => 'required|string|max:255|unique:delivery_notes,delivery_note' . ($ignoreId ? ',' . $ignoreId : ''),
            'delivered_from' => 'required|string|max:255',
            'destination_type' => 'required|in:warehouse,customer',
            'branch_id' => 'nullable|exists:branches,id',
            'customer_id' => 'nullable|exists:new_leads,id',
            'alternative_id' => 'nullable|exists:lead_alternative_adds,id',
            'lead_product_list_id' => 'nullable|exists:lead_product_lists,id',
            'deal_id' => 'nullable|exists:deals,id',
            'handover_by' => 'nullable|exists:employees,id',
            'order_by' => 'nullable|string|max:255',
            'order_no' => 'nullable|string|max:255',
            'comission' => 'nullable|string|max:255',
            'order_date' => 'nullable|date',
            'handover_date' => 'nullable|date',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:255',
            'progress' => 'nullable|integer|min:0|max:100',
            'pdf' => 'nullable|mimes:pdf|max:20480',
            'image' => 'nullable|image|max:5120',
            'linked_delivery_note_id' => 'nullable|exists:delivery_notes,id',
        ], [
            'delivery_note.required' => 'Lieferschein-Nr. ist erforderlich.',
            'delivery_note.unique' => 'Diese Lieferschein-Nr. existiert bereits.',
            'delivered_from.required' => 'Geliefert von ist erforderlich.',
            'destination_type.required' => 'Zieltyp ist erforderlich.',
        ]);
    }

    protected function fillDeliveryNote(DeliveryNote $deliveryNote, Request $request): void
    {
        $deliveryNote->delivery_note = $request->delivery_note;
        $deliveryNote->delivered_from = $request->delivered_from;
        $deliveryNote->destination_type = $request->destination_type ?: 'warehouse';
        $deliveryNote->branch_id = $request->branch_id;
        $deliveryNote->customer_id = $request->customer_id;
        $deliveryNote->alternative_id = $request->alternative_id;
        $deliveryNote->lead_product_list_id = $request->lead_product_list_id;
        $deliveryNote->deal_id = $request->deal_id;
        $deliveryNote->handover_by = $request->handover_by;
        $deliveryNote->order_by = $request->order_by;
        $deliveryNote->order_no = $request->order_no;
        $deliveryNote->comission = $request->comission;
        $deliveryNote->order_date = $request->order_date;
        $deliveryNote->handover_date = $request->handover_date;
        $deliveryNote->description = $request->description;
        $deliveryNote->status = $request->status ?: 'Verfügbar';
        $deliveryNote->progress = $request->filled('progress') ? (int) $request->progress : 0;
        $deliveryNote->linked_delivery_note_id = $request->linked_delivery_note_id;
        $deliveryNote->level = $request->linked_delivery_note_id ? 2 : 1;
    }

    protected function handleFiles(DeliveryNote $deliveryNote, Request $request): void
    {
        if ($request->hasFile('pdf')) {
            $this->deleteFile('images/delivery_note/pdf/' . $deliveryNote->pdf);

            $pdfName = uniqid('delivery_pdf_') . '.' . $request->file('pdf')->getClientOriginalExtension();
            $request->file('pdf')->move(public_path('images/delivery_note/pdf'), $pdfName);
            $deliveryNote->pdf = $pdfName;
        }

        if ($request->hasFile('image')) {
            $this->deleteFile('images/delivery_note/' . $deliveryNote->image);

            $imageName = uniqid('delivery_img_') . '.' . $request->file('image')->getClientOriginalExtension();
            $request->file('image')->move(public_path('images/delivery_note'), $imageName);
            $deliveryNote->image = $imageName;
        }
    }

    protected function deleteFile(?string $path): void
    {
        if (!$path) {
            return;
        }

        $fullPath = public_path($path);

        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    public function createFromDeal(Deal $deal)
    {
        $deliveryNote = DeliveryNote::create([
            'delivery_note'         => 'LS-' . now()->format('ymd') . '-' . str_pad((string) ($deal->id), 4, '0', STR_PAD_LEFT),
            'delivered_from'        => 'Auftrag',
            'destination_type'      => 'customer',
            'deal_id'               => $deal->id,
            'customer_id'           => $deal->customer_id,
            'alternative_id'        => $deal->alternative_id,
            'lead_product_list_id'  => null,
            'order_no'              => $deal->order_number,
            'order_date'            => now(),
            'status'                => 'Verfügbar',
            'progress'              => 0,
            'description'           => $deal->info,
            'level'                 => 1,
        ]);

        return redirect()
            ->route('delivery-notes.profile', $deliveryNote->id)
            ->with('success', 'Lieferschein wurde mit dem Auftrag verknüpft.');
    }

    public function byDeal(Deal $deal)
    {
        $deliveryNotes = DeliveryNote::where('deal_id', $deal->id)
            ->latest()
            ->paginate(20);

        return view('admin.product.delivery.by_deal', compact('deal', 'deliveryNotes'));
    }

    public function profile(DeliveryNote $deliveryNote)
    {
        $deliveryNote->load([
            'branch:id,branch',
            'handoverEmployee:id,name,lastname,image',
            'images:id,delivery_note_id,name,image',
            'linkedDeliveryNote:id,delivery_note',
            'linkedNotes.branch:id,branch',
            'linkedNotes.handoverEmployee:id,name,lastname,image',
            'linkedNotes.images:id,delivery_note_id,name,image',
            'customer:id,customer_no,firma,name,lastname,city,email,phone,telephone',
            'alternative:id,lead_id,object_name,street,postcode,city',
            'leadProductList:id,customer_id,alternative_id,product_id,service',
            'leadProductList.product:id,article_group',
            'deal:id,customer_id,alternative_id,product_id,order_number,offer_number,price,status,deal_status,sign_date,confirmed_at',
            'deal.product:id,article_group',
        ]);

        return view('admin.product.delivery.profile', compact('deliveryNote'));
    }
}