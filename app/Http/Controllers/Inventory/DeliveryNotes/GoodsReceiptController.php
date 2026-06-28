<?php

namespace App\Http\Controllers\Inventory\DeliveryNotes;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptAttachment;
use App\Models\LeadProductList;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GoodsReceiptController extends Controller
{
    public function relationOptions(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        $rows = LeadProductList::query()
            ->with([
                'customer:id,firma,name,lastname,customer_no',
                'alternative:id,lead_id,object_name,street,address_no,city',
                'articleGroup:id,article_group',
                'department:id,department_name',
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('customer', function ($customer) use ($q) {
                        $customer->where('firma', 'like', "%{$q}%")
                            ->orWhere('name', 'like', "%{$q}%")
                            ->orWhere('lastname', 'like', "%{$q}%")
                            ->orWhere('customer_no', 'like', "%{$q}%");
                    })
                    ->orWhereHas('alternative', function ($alt) use ($q) {
                        $alt->where('object_name', 'like', "%{$q}%")
                            ->orWhere('street', 'like', "%{$q}%")
                            ->orWhere('city', 'like', "%{$q}%")
                            ->orWhere('address_no', 'like', "%{$q}%");
                    })
                    ->orWhereHas('articleGroup', function ($product) use ($q) {
                        $product->where('article_group', 'like', "%{$q}%");
                    });
                });
            })
            ->latest('id')
            ->limit(30)
            ->get();

        $results = $rows->map(function (LeadProductList $row) {
            $customerName = $row->customer?->firma
                ?: trim(($row->customer?->name ?? '') . ' ' . ($row->customer?->lastname ?? ''));

            $objectName = $row->alternative?->object_name
                ?: trim(collect([
                    $row->alternative?->street,
                    $row->alternative?->address_no,
                    $row->alternative?->city,
                ])->filter()->implode(' '));

            $productName = $row->articleGroup?->article_group;

            return [
                'id' => $row->id,
                'text' => collect([$customerName, $objectName, $productName])->filter()->implode(' • '),

                'lead_product_list_id' => $row->id,
                'customer_id' => $row->customer_id,
                'object_id' => $row->alternative_id,
                'product_id' => $row->product_id,
                'article_group_id' => $row->product_id,
                'department_id' => $row->department_id,

                'customer_name' => $customerName,
                'object_name' => $objectName,
                'product_name' => $productName,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'results' => $results,
        ]);
    }

    public function index()
    {
        return view('admin.good_receipts.index');
    }

    public function data(Request $request): JsonResponse
    {
        $allowedSorts = [
            'id'                => 'id',
            'code'              => 'code',
            'received_at'       => 'received_at',
            'description'       => 'description',
            'status'            => 'status',
            'inspection_status' => 'inspection_status',
            'created_at'        => 'created_at',
        ];

        $sortBy = $allowedSorts[$request->get('sort_by', 'received_at')] ?? 'received_at';
        $sortDir = strtolower((string) $request->get('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage = max(1, min((int) $request->get('per_page', 10), 100));

        $query = GoodsReceipt::query()
            ->with($this->defaultRelations())
            ->search($request->get('search'))
            ->filter($request->only([
                'status',
                'inspection_status',
                'department_id',
                'customer_id',
                'object_id',
                'article_group_id',
                'destination',
                'date_from',
                'date_to',
            ]))
            ->orderBy($sortBy, $sortDir)
            ->orderByDesc('id');

        $paginated = $query->paginate($perPage);

        $stats = [
            'total'      => GoodsReceipt::count(),
            'pending'    => GoodsReceipt::where('status', 'pending')->count(),
            'processing' => GoodsReceipt::where('status', 'processing')->count(),
            'completed'  => GoodsReceipt::where('status', 'completed')->count(),
            'issued'     => GoodsReceipt::where('status', 'issued')->count(),
            'issues'     => GoodsReceipt::where('inspection_status', 'issue')->count(),
        ];

        $rows = $paginated->getCollection()
            ->map(fn (GoodsReceipt $item) => $this->transformGoodsReceiptForList($item))
            ->values();

        return response()->json([
            'success' => true,
            'data' => $rows,
            'meta' => [
                'current_page' => $paginated->currentPage(),
                'last_page'    => $paginated->lastPage(),
                'per_page'     => $paginated->perPage(),
                'total'        => $paginated->total(),
                'from'         => $paginated->firstItem(),
                'to'           => $paginated->lastItem(),
            ],
            'stats' => $stats,
        ]);
    }

    public function show(GoodsReceipt $goodsReceipt): JsonResponse
    {
        $goodsReceipt->load($this->defaultRelations());

        $customerName = $goodsReceipt->customer?->firma
            ?: trim(($goodsReceipt->customer?->name ?? '') . ' ' . ($goodsReceipt->customer?->lastname ?? ''));

        $objectName = $goodsReceipt->object?->object_name
            ?: trim(collect([
                $goodsReceipt->object?->street,
                $goodsReceipt->object?->address_no,
                $goodsReceipt->object?->city,
            ])->filter()->implode(' '));

        $productName = $goodsReceipt->articleGroup?->article_group
            ?: $goodsReceipt->leadProductList?->articleGroup?->article_group;

        $payload = $this->transformGoodsReceiptForList($goodsReceipt);

        $payload['relation_picker_text'] = collect([
            $customerName ?: null,
            $objectName ?: null,
            $productName ?: null,
        ])->filter()->implode(' • ');

        $payload['customer_display'] = $customerName ?: null;
        $payload['object_display'] = $objectName ?: null;
        $payload['product_display'] = $productName ?: null;

        if (empty($payload['article_group_id']) && $goodsReceipt->leadProductList?->product_id) {
            $payload['article_group_id'] = $goodsReceipt->leadProductList->product_id;
        }

        if (empty($payload['product_id']) && $goodsReceipt->leadProductList?->product_id) {
            $payload['product_id'] = $goodsReceipt->leadProductList->product_id;
        }

        return response()->json([
            'success' => true,
            'data' => $payload,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate($this->rules(false));
        $employeeId = $this->authEmployeeId();

        $goodsReceipt = DB::transaction(function () use ($request, $validated, $employeeId) {
            $payload = $validated;
            unset($payload['inbound_files'], $payload['outbound_files']);

            $payload['code'] = GoodsReceipt::nextCode();
            $payload['received_at'] = $payload['received_at'] ?? now();
            $payload['created_by_employee_id'] = $employeeId;
            $payload['updated_by_employee_id'] = $employeeId;

            $goodsReceipt = GoodsReceipt::create($payload);

            $this->storeUploadedFiles(
                goodsReceipt: $goodsReceipt,
                files: $request->file('inbound_files', []),
                scope: 'inbound',
                employeeId: $employeeId,
                defaultLabel: 'Lieferschein / Eingang'
            );

            $goodsReceipt->load($this->defaultRelations());

            $this->notifyAdminUsers(
                goodsReceipt: $goodsReceipt,
                action: 'create',
                title: 'Wareneingang erstellt',
                message: 'Ein neuer Wareneingang wurde angelegt.',
                oldValues: null,
                newValues: $goodsReceipt->toArray()
            );

            return $goodsReceipt;
        });

        $goodsReceipt->load($this->defaultRelations());

        return response()->json([
            'success' => true,
            'message' => 'Wareneingang wurde erstellt.',
            'data' => $this->transformGoodsReceiptForList($goodsReceipt),
        ], 201);
    }

    public function update(Request $request, GoodsReceipt $goodsReceipt): JsonResponse
    {
        $validated = $request->validate($this->rules(true));

        if (
            isset($validated['status']) &&
            $validated['status'] === 'issued' &&
            $goodsReceipt->status !== 'issued'
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Status "issued" darf nur über Warenausgang gesetzt werden.',
            ], 422);
        }

        $employeeId = $this->authEmployeeId();
        $oldValues = $goodsReceipt->toArray();

        DB::transaction(function () use ($request, $goodsReceipt, $validated, $employeeId, $oldValues) {
            $payload = $validated;
            unset($payload['inbound_files'], $payload['outbound_files']);

            $payload['updated_by_employee_id'] = $employeeId;

            $goodsReceipt->update($payload);

            $this->storeUploadedFiles(
                goodsReceipt: $goodsReceipt,
                files: $request->file('inbound_files', []),
                scope: 'inbound',
                employeeId: $employeeId,
                defaultLabel: 'Zusätzlicher Eingang'
            );

            $goodsReceipt->refresh();
            $goodsReceipt->load($this->defaultRelations());

            $this->notifyAdminUsers(
                goodsReceipt: $goodsReceipt,
                action: 'update',
                title: 'Wareneingang aktualisiert',
                message: 'Ein Wareneingang wurde bearbeitet.',
                oldValues: $oldValues,
                newValues: $goodsReceipt->toArray()
            );
        });

        $goodsReceipt->load($this->defaultRelations());

        return response()->json([
            'success' => true,
            'message' => 'Wareneingang wurde aktualisiert.',
            'data' => $this->transformGoodsReceiptForList($goodsReceipt),
        ]);
    }

    public function destroy(GoodsReceipt $goodsReceipt): JsonResponse
    {
        $oldValues = $goodsReceipt->toArray();

        DB::transaction(function () use ($goodsReceipt, $oldValues) {
            $this->notifyAdminUsers(
                goodsReceipt: $goodsReceipt,
                action: 'delete',
                title: 'Wareneingang gelöscht',
                message: 'Ein Wareneingang wurde gelöscht.',
                oldValues: $oldValues,
                newValues: null
            );

            foreach ($goodsReceipt->attachments as $attachment) {
                if ($attachment->file_path && Storage::disk($attachment->disk ?: 'public')->exists($attachment->file_path)) {
                    Storage::disk($attachment->disk ?: 'public')->delete($attachment->file_path);
                }
            }

            $goodsReceipt->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Eintrag wurde gelöscht.',
        ]);
    }

    public function issue(Request $request, GoodsReceipt $goodsReceipt): JsonResponse
    {
        if (!$goodsReceipt->can_be_issued) {
            return response()->json([
                'success' => false,
                'message' => 'Nur abgeschlossene / lagernde Ware kann ausgebucht werden.',
            ], 422);
        }

        $validated = $request->validate([
            'outbound_recipient'   => ['required', 'string', 'max:255'],
            'outbound_project'     => ['nullable', 'string', 'max:255'],
            'outbound_customer_id' => ['nullable', 'exists:new_leads,id'],
            'outbound_object_id'   => ['nullable', 'exists:lead_alternative_adds,id'],
            'outbound_files'       => ['nullable', 'array'],
            'outbound_files.*'     => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $employeeId = $this->authEmployeeId();
        $oldValues = $goodsReceipt->toArray();

        DB::transaction(function () use ($request, $goodsReceipt, $validated, $employeeId, $oldValues) {
            $goodsReceipt->update([
                'status'                 => 'issued',
                'outbound_at'            => now(),
                'outbound_recipient'     => $validated['outbound_recipient'],
                'outbound_project'       => $validated['outbound_project'] ?? null,
                'outbound_customer_id'   => $validated['outbound_customer_id'] ?? null,
                'outbound_object_id'     => $validated['outbound_object_id'] ?? null,
                'issued_by_employee_id'  => $employeeId,
                'updated_by_employee_id' => $employeeId,
            ]);

            $this->storeUploadedFiles(
                goodsReceipt: $goodsReceipt,
                files: $request->file('outbound_files', []),
                scope: 'outbound',
                employeeId: $employeeId,
                defaultLabel: 'Warenausgang / Beleg'
            );

            $goodsReceipt->refresh();
            $goodsReceipt->load($this->defaultRelations());

            $this->notifyAdminUsers(
                goodsReceipt: $goodsReceipt,
                action: 'issue',
                title: 'Warenausgang gebucht',
                message: 'Die Ware wurde ausgebucht.',
                oldValues: $oldValues,
                newValues: $goodsReceipt->toArray()
            );
        });

        $goodsReceipt->load($this->defaultRelations());

        return response()->json([
            'success' => true,
            'message' => 'Ware wurde erfolgreich ausgebucht.',
            'data' => $this->transformGoodsReceiptForList($goodsReceipt),
        ]);
    }

    public function quickStatus(Request $request, GoodsReceipt $goodsReceipt): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'processing', 'completed'])],
        ]);

        $employeeId = $this->authEmployeeId();
        $oldValues = $goodsReceipt->toArray();

        DB::transaction(function () use ($goodsReceipt, $validated, $employeeId, $oldValues) {
            $goodsReceipt->update([
                'status'                 => $validated['status'],
                'updated_by_employee_id' => $employeeId,
            ]);

            $goodsReceipt->refresh();
            $goodsReceipt->load($this->defaultRelations());

            $this->notifyAdminUsers(
                goodsReceipt: $goodsReceipt,
                action: 'quick_status',
                title: 'Status geändert',
                message: 'Der Status wurde per Schnellaktion geändert.',
                oldValues: ['status' => $oldValues['status'] ?? null],
                newValues: ['status' => $goodsReceipt->status]
            );
        });

        return response()->json([
            'success' => true,
            'message' => 'Status aktualisiert.',
        ]);
    }

    protected function rules(bool $isUpdate = false): array
    {
        $statusValues = $isUpdate
            ? ['pending', 'processing', 'completed', 'issued']
            : ['pending', 'processing', 'completed'];

        return [
            'customer_id'              => ['nullable', 'exists:new_leads,id'],
            'object_id'                => ['nullable', 'exists:lead_alternative_adds,id'],
            'lead_product_list_id'     => ['nullable', 'exists:lead_product_lists,id'],
            'article_group_id'         => ['nullable', 'exists:article_groups,id'],
            'department_id'            => ['nullable', 'exists:departments,id'],
            'accepted_by_employee_id'  => ['nullable', 'exists:employees,id'],
            'orderer_employee_id'      => ['nullable', 'exists:employees,id'],

            'received_at'              => ['nullable', 'date'],
            'description'              => ['required', 'string', 'max:255'],
            'note'                     => ['nullable', 'string'],

            'status'                   => ['required', Rule::in($statusValues)],
            'inspection_status'        => ['required', Rule::in(['pending', 'ok', 'issue'])],
            'issue_description'        => ['nullable', 'string'],

            'destination'              => ['nullable', Rule::in(['lager', 'kommission'])],
            'commission_details'       => ['nullable', 'string', 'max:255'],

            'qty'                      => ['nullable', 'numeric'],
            'unit'                     => ['nullable', 'string', 'max:50'],
            'purchase_price'           => ['nullable', 'numeric'],

            'meta'                     => ['nullable', 'array'],

            'inbound_files'            => ['nullable', 'array'],
            'inbound_files.*'          => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],

            'outbound_files'           => ['nullable', 'array'],
            'outbound_files.*'         => ['file', 'max:10240', 'mimes:jpg,jpeg,png,webp,pdf'],
        ];
    }

    protected function authEmployeeId(): ?int
    {
        $raw = Auth::user()?->name;

        if (!$raw || !is_numeric($raw)) {
            return null;
        }

        $employeeId = (int) $raw;

        return Employee::where('id', $employeeId)->exists() ? $employeeId : null;
    }

    protected function authEmployee(): ?Employee
    {
        $employeeId = $this->authEmployeeId();

        if (!$employeeId) {
            return null;
        }

        return Employee::find($employeeId);
    }

    protected function authEmployeeName(): ?string
    {
        $employee = $this->authEmployee();

        return $employee ? $this->employeeFullName($employee) : null;
    }

    protected function employeeFullName(Employee $employee): string
    {
        return trim(implode(' ', array_filter([
            $employee->title,
            $employee->name,
            $employee->midname,
            $employee->lastname,
        ])));
    }

    protected function notifyAdminUsers(
        GoodsReceipt $goodsReceipt,
        string $action,
        ?string $title = null,
        ?string $message = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $meta = null
    ): void {
        $users = User::query()->get();

        foreach ($users as $user) {
            $this->storeDatabaseNotification(
                notifiable: $user,
                goodsReceipt: $goodsReceipt,
                action: $action,
                title: $title,
                message: $message,
                oldValues: $oldValues,
                newValues: $newValues,
                meta: $meta
            );
        }
    }

    protected function storeDatabaseNotification(
        User $notifiable,
        GoodsReceipt $goodsReceipt,
        string $action,
        ?string $title = null,
        ?string $message = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $meta = null
    ): void {
        DB::table('notifications')->insert([
            'id' => (string) Str::uuid(),
            'type' => 'goods_receipt_activity',
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->getKey(),
            'data' => json_encode([
                'title' => $title ?: 'Warenbewegung',
                'message' => $message,
                'action' => $action,
                'goods_receipt_id' => $goodsReceipt->id,
                'goods_receipt_code' => $goodsReceipt->code,
                'actor_employee_id' => $this->authEmployeeId(),
                'actor_employee_name' => $this->authEmployeeName(),
                'status' => $goodsReceipt->status,
                'inspection_status' => $goodsReceipt->inspection_status,
                'happened_at' => now()->toDateTimeString(),
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'meta' => $meta,
            ], JSON_UNESCAPED_UNICODE),
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function defaultRelations(): array
    {
        return [
            'customer:id,customer_no,name,lastname,firma',
            'object:id,lead_id,object_name,city,street,address_no',
            'leadProductList:id,customer_id,alternative_id,product_id,department_id,employee_id,field_employee,service,status,work_status,interest,realization_time,stage,price,price_latest,project_minutes',
            'leadProductList.articleGroup:id,article_group',
            'articleGroup:id,article_group',
            'department:id,department_name',
            'acceptedByEmployee:id,title,name,midname,lastname,image',
            'ordererEmployee:id,title,name,midname,lastname,image',
            'createdByEmployee:id,title,name,midname,lastname,image',
            'updatedByEmployee:id,title,name,midname,lastname,image',
            'issuedByEmployee:id,title,name,midname,lastname,image',
            'attachments:id,goods_receipt_id,scope,kind,label,original_name,file_name,file_path,disk,mime_type,file_size,uploaded_by_employee_id',
        ];
    }

    protected function transformGoodsReceiptForList(GoodsReceipt $item): array
    {
        $attachments = $item->attachments ?? collect();

        return [
            'id'                 => $item->id,
            'code'               => $item->code,
            'received_at'        => optional($item->received_at)->toDateTimeString(),
            'received_at_human'  => optional($item->received_at)?->format('d.m.Y H:i'),
            'description'        => $item->description,
            'note'               => $item->note,
            'status'             => $item->status,
            'inspection_status'  => $item->inspection_status,
            'issue_description'  => $item->issue_description,
            'destination'        => $item->destination,
            'commission_details' => $item->commission_details,
            'qty'                => $item->qty,
            'unit'               => $item->unit,
            'purchase_price'     => $item->purchase_price,
            'outbound_at'        => optional($item->outbound_at)->toDateTimeString(),
            'outbound_at_human'  => optional($item->outbound_at)?->format('d.m.Y H:i'),
            'outbound_recipient' => $item->outbound_recipient,
            'outbound_project'   => $item->outbound_project,
            'can_be_issued'      => $item->can_be_issued,

            'customer_id' => $item->customer_id,
            'object_id' => $item->object_id,
            'lead_product_list_id' => $item->lead_product_list_id,
            'product_id' => $item->leadProductList?->product_id ?: $item->article_group_id,
            'article_group_id' => $item->article_group_id ?: $item->leadProductList?->product_id,
            'department_id' => $item->department_id,
            'accepted_by_employee_id' => $item->accepted_by_employee_id,
            'orderer_employee_id' => $item->orderer_employee_id,

            'customer' => $item->customer ? [
                'id'          => $item->customer->id,
                'customer_no' => $item->customer->customer_no,
                'name'        => $item->customer->name,
                'lastname'    => $item->customer->lastname,
                'firma'       => $item->customer->firma,
            ] : null,

            'object' => $item->object ? [
                'id'          => $item->object->id,
                'lead_id'     => $item->object->lead_id,
                'object_name' => $item->object->object_name,
                'city'        => $item->object->city,
                'street'      => $item->object->street,
                'address_no'  => $item->object->address_no,
            ] : null,

            'lead_product_list' => $item->leadProductList ? [
                'id'               => $item->leadProductList->id,
                'customer_id'      => $item->leadProductList->customer_id,
                'alternative_id'   => $item->leadProductList->alternative_id,
                'product_id'       => $item->leadProductList->product_id,
                'department_id'    => $item->leadProductList->department_id,
                'employee_id'      => $item->leadProductList->employee_id,
                'field_employee'   => $item->leadProductList->field_employee,
                'service'          => $item->leadProductList->service,
                'status'           => $item->leadProductList->status,
                'work_status'      => $item->leadProductList->work_status,
                'interest'         => $item->leadProductList->interest,
                'realization_time' => $item->leadProductList->realization_time,
                'stage'            => $item->leadProductList->stage,
                'price'            => $item->leadProductList->price,
                'price_latest'     => $item->leadProductList->price_latest,
                'project_minutes'  => $item->leadProductList->project_minutes,
            ] : null,

            'article_group' => $item->articleGroup ? [
                'id'            => $item->articleGroup->id,
                'article_group' => $item->articleGroup->article_group,
            ] : null,

            'department' => $item->department ? [
                'id'              => $item->department->id,
                'department_name' => $item->department->department_name,
            ] : null,

            'accepted_by_employee' => $item->acceptedByEmployee ? [
                'id' => $item->acceptedByEmployee->id,
                'name' => $this->employeeFullName($item->acceptedByEmployee),
                'image' => $item->acceptedByEmployee->image
                    ? asset('images/employee/' . $item->acceptedByEmployee->image)
                    : null,
            ] : null,

            'orderer_employee' => $item->ordererEmployee ? [
                'id' => $item->ordererEmployee->id,
                'name' => $this->employeeFullName($item->ordererEmployee),
                'image' => $item->ordererEmployee->image
                    ? asset('images/employee/' . $item->ordererEmployee->image)
                    : null,
            ] : null,

            'created_by_employee' => $item->createdByEmployee ? [
                'id' => $item->createdByEmployee->id,
                'name' => $this->employeeFullName($item->createdByEmployee),
                'image' => $item->createdByEmployee->image
                    ? asset('images/employee/' . $item->createdByEmployee->image)
                    : null,
            ] : null,

            'updated_by_employee' => $item->updatedByEmployee ? [
                'id' => $item->updatedByEmployee->id,
                'name' => $this->employeeFullName($item->updatedByEmployee),
                'image' => $item->updatedByEmployee->image
                    ? asset('images/employee/' . $item->updatedByEmployee->image)
                    : null,
            ] : null,

            'issued_by_employee' => $item->issuedByEmployee ? [
                'id' => $item->issuedByEmployee->id,
                'name' => $this->employeeFullName($item->issuedByEmployee),
                'image' => $item->issuedByEmployee->image
                    ? asset('images/employee/' . $item->issuedByEmployee->image)
                    : null,
            ] : null,

            'attachments' => $attachments
                ->map(fn (GoodsReceiptAttachment $file) => $this->transformAttachment($file))
                ->values(),

            'inbound_attachments' => $attachments
                ->where('scope', 'inbound')
                ->map(fn (GoodsReceiptAttachment $file) => $this->transformAttachment($file))
                ->values(),

            'outbound_attachments' => $attachments
                ->where('scope', 'outbound')
                ->map(fn (GoodsReceiptAttachment $file) => $this->transformAttachment($file))
                ->values(),
        ];
    }

    protected function transformAttachment(GoodsReceiptAttachment $file): array
    {
        $disk = $file->disk ?: 'public';
        $url = $file->file_path ? Storage::disk($disk)->url($file->file_path) : null;
        $mime = $file->mime_type ?: '';

        return [
            'id' => $file->id,
            'scope' => $file->scope,
            'kind' => $file->kind,
            'label' => $file->label,
            'original_name' => $file->original_name,
            'file_name' => $file->file_name,
            'file_path' => $file->file_path,
            'disk' => $disk,
            'file_url' => $url,
            'mime_type' => $mime,
            'file_size' => $file->file_size,
            'is_image' => str_starts_with($mime, 'image/'),
        ];
    }

    protected function storeUploadedFiles(
        GoodsReceipt $goodsReceipt,
        array $files,
        string $scope,
        ?int $employeeId = null,
        ?string $defaultLabel = null
    ): void {
        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $storedPath = $file->store('goods-receipts/' . $goodsReceipt->id . '/' . $scope, 'public');
            $mime = $file->getMimeType() ?: $file->getClientMimeType();
            $kind = str_starts_with((string) $mime, 'image/') ? 'image' : 'document';

            GoodsReceiptAttachment::create([
                'goods_receipt_id' => $goodsReceipt->id,
                'scope' => $scope,
                'kind' => $kind,
                'label' => $defaultLabel,
                'original_name' => $file->getClientOriginalName(),
                'file_name' => basename($storedPath),
                'file_path' => $storedPath,
                'disk' => 'public',
                'mime_type' => $mime,
                'file_size' => $file->getSize(),
                'uploaded_by_employee_id' => $employeeId,
            ]);
        }
    }
}