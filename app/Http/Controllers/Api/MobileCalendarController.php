<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\MainAppointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MobileCalendarController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | GET /api/mobile/calendar
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
    {
        $employeeId = $this->resolveEmployeeId($request);

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Employee not found.',
                'debug' => [
                    'request_employee_id' => $request->input('employee_id'),
                    'auth_model' => $request->user() ? get_class($request->user()) : null,
                    'auth_id' => $request->user()?->id,
                    'auth_name' => $request->user()?->name ?? null,
                    'auth_email' => $request->user()?->email ?? null,
                ],
            ], 403);
        }

        $month = (int) $request->input('month', now()->month);
        $year = (int) $request->input('year', now()->year);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        if ($year < 2000 || $year > 2100) {
            $year = now()->year;
        }

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        try {
            $appointments = MainAppointment::query()
                ->with($this->appointmentRelations())
                ->whereBetween('start_date', [$startDate, $endDate])
                ->where(function (Builder $query) use ($employeeId) {
                    $query
                        ->where('created_by', $employeeId)
                        ->orWhereHas('employees', function (Builder $employeeQuery) use ($employeeId) {
                            $employeeQuery->where('employees.id', $employeeId);
                        });
                });

            $this->applyNotDeletedAppointmentFilter($appointments);

            $appointments = $appointments
                ->orderBy('start_date')
                ->orderBy('start_time')
                ->orderBy('id')
                ->limit(1000)
                ->get();

            $lookup = $this->buildProductLookups($appointments);

            $data = $appointments
                ->map(fn(MainAppointment $appointment) => $this->mapAppointment($appointment, $employeeId, $lookup))
                ->values();

            return response()->json([
                'ok' => true,
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year,
                'data' => $data,
            ]);
        } catch (\Throwable $e) {
            Log::error('Mobile calendar index failed', [
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Calendar load failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GET /api/mobile/calendar/{id}
    |--------------------------------------------------------------------------
    */
    public function show(Request $request, int $id)
    {
        $employeeId = $this->resolveEmployeeId($request);

        if (!$employeeId) {
            return response()->json([
                'ok' => false,
                'message' => 'Employee not found.',
            ], 403);
        }

        $query = MainAppointment::query()
            ->with($this->appointmentRelations())
            ->where('id', $id)
            ->where(function (Builder $query) use ($employeeId) {
                $query
                    ->where('created_by', $employeeId)
                    ->orWhereHas('employees', function (Builder $employeeQuery) use ($employeeId) {
                        $employeeQuery->where('employees.id', $employeeId);
                    });
            });

        $this->applyNotDeletedAppointmentFilter($query);

        $appointment = $query->first();

        if (!$appointment) {
            return response()->json([
                'ok' => false,
                'message' => 'Appointment not found.',
            ], 404);
        }

        $lookup = $this->buildProductLookups(collect([$appointment]));

        return response()->json([
            'ok' => true,
            'employee_id' => $employeeId,
            'data' => $this->mapAppointment($appointment, $employeeId, $lookup),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST /api/mobile/calendar
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $createdBy = $this->resolveEmployeeId($request);

        if (!$createdBy) {
            return response()->json([
                'ok' => false,
                'message' => 'Employee not found.',
                'debug' => [
                    'request_employee_id' => $request->input('employee_id'),
                    'auth_model' => $request->user() ? get_class($request->user()) : null,
                    'auth_id' => $request->user()?->id,
                    'auth_name' => $request->user()?->name ?? null,
                ],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],

            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i'],

            'color' => ['nullable', 'string', 'max:30'],
            'appointment_type' => ['nullable', 'string', 'max:255'],
            'execution_type' => ['nullable', 'string', 'max:255'],
            'contact_mode' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'priority' => ['nullable', 'string', 'max:255'],

            'note' => ['nullable', 'string', 'max:5000'],
            'link' => ['nullable', 'string', 'max:1000'],

            'full_address' => ['nullable', 'string', 'max:500'],
            'street' => ['nullable', 'string', 'max:255'],
            'postcode' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'lon' => ['nullable', 'numeric'],

            'email' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],

            'customer_id' => ['nullable', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'branch_address_id' => ['nullable', 'integer'],
            'contact_id' => ['nullable', 'integer'],
            'contact_type' => ['nullable', 'string', 'max:255'],

            'lead_product_list_id' => ['nullable', 'integer'],
            'lead_stage_id' => ['nullable', 'integer'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer'],

            'employee_ids' => ['nullable', 'array'],
            'employee_ids.*' => ['integer'],

            'product_items' => ['nullable', 'array'],
            'product_items.*.label' => ['nullable', 'string', 'max:255'],
            'product_items.*.customer_id' => ['nullable', 'integer'],
            'product_items.*.product_id' => ['nullable', 'integer'],
            'product_items.*.alternative_id' => ['nullable', 'integer'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $employeeIds = collect($data['employee_ids'] ?? [])
            ->map(fn($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($employeeIds->isEmpty()) {
            $employeeIds = collect([(int) $createdBy]);
        }

        try {
            $appointment = DB::transaction(function () use ($data, $createdBy, $employeeIds) {
                $appointment = MainAppointment::create([
                    'created_by' => (int) $createdBy,

                    'name' => $data['name'],
                    'note' => $data['note'] ?? null,
                    'color' => $data['color'] ?? '#93c21c',

                    'start_date' => $data['start_date'],
                    'end_date' => $data['end_date'] ?? $data['start_date'],
                    'start_time' => $this->timeForDb($data['start_time'] ?? null),
                    'end_time' => $this->timeForDb($data['end_time'] ?? null),

                    'appointment_type' => $data['appointment_type'] ?? null,
                    'execution_type' => $data['execution_type'] ?? null,
                    'contact_mode' => $data['contact_mode'] ?? 'new',
                    'type' => $data['type'] ?? 'appointment',
                    'source' => $data['source'] ?? 'mobile',
                    'priority' => $data['priority'] ?? null,
                    'link' => $data['link'] ?? null,

                    'street' => $data['street'] ?? null,
                    'postcode' => $data['postcode'] ?? null,
                    'city' => $data['city'] ?? null,
                    'full_address' => $data['full_address'] ?? $this->buildAddressFromParts(
                        $data['street'] ?? null,
                        $data['postcode'] ?? null,
                        $data['city'] ?? null
                    ),

                    'latitude' => $data['latitude'] ?? $data['lat'] ?? null,
                    'longitude' => $data['longitude'] ?? $data['lng'] ?? $data['lon'] ?? null,

                    'email' => $data['email'] ?? null,
                    'phone' => $data['phone'] ?? null,

                    'customer_id' => $data['customer_id'] ?? null,
                    'branch_id' => $data['branch_id'] ?? null,
                    'branch_address_id' => $data['branch_address_id'] ?? null,
                    'contact_id' => $data['contact_id'] ?? null,
                    'contact_type' => $data['contact_type'] ?? null,

                    'lead_product_list_id' => $data['lead_product_list_id'] ?? null,
                    'lead_stage_id' => $data['lead_stage_id'] ?? null,
                    'lead_stage_sub_stage_id' => $data['lead_stage_sub_stage_id'] ?? null,

                    'products' => $this->buildLegacyProductsPayload($data),
                    'status' => 'Published',
                ]);

                $syncPayload = [];

                foreach ($employeeIds as $employeeId) {
                    $syncPayload[(int) $employeeId] = [
                        'status' => 'send',
                        'reason' => null,
                    ];
                }

                $appointment->employees()->sync($syncPayload);

                return $appointment->fresh($this->appointmentRelations());
            });

            $lookup = $this->buildProductLookups(collect([$appointment]));

            return response()->json([
                'ok' => true,
                'message' => 'Termin gespeichert.',
                'data' => $this->mapAppointment($appointment, $createdBy, $lookup),
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Mobile calendar store failed', [
                'employee_id' => $createdBy,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Save failed.',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null,
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Query helpers
    |--------------------------------------------------------------------------
    */
    private function appointmentRelations(): array
    {
        return [
            'employees',
            'creator',
            'createdBy',
            'changedBy',
            'customer',
            'branch',
            'branchAddress',
            'leadProductList',
            'leadStage',
            'leadStageSubStage',
        ];
    }

    private function applyNotDeletedAppointmentFilter(Builder $query): void
    {
        /*
        |--------------------------------------------------------------------------
        | SoftDeletes:
        |--------------------------------------------------------------------------
        | Eloquent usually excludes soft-deleted rows automatically, but we keep
        | the explicit condition because this mobile API must never leak deleted
        | appointments even if a global scope is bypassed later.
        |--------------------------------------------------------------------------
        */
        if (Schema::hasColumn('main_appointments', 'deleted_at')) {
            $query->whereNull('main_appointments.deleted_at');
        }

        /*
        |--------------------------------------------------------------------------
        | Legacy delete/status protection:
        |--------------------------------------------------------------------------
        | Some old records may not have deleted_at set but are marked deleted,
        | archived, trash, etc. Those must also be hidden from mobile.
        |--------------------------------------------------------------------------
        */
        if (Schema::hasColumn('main_appointments', 'status')) {
            $query->where(function (Builder $statusQuery) {
                $statusQuery
                    ->whereNull('status')
                    ->orWhereNotIn(DB::raw("LOWER(TRIM(COALESCE(status, '')))"), [
                        'deleted',
                        'delete',
                        'trash',
                        'trashed',
                        'archived',
                        'archive',
                        'gelöscht',
                        'papierkorb',
                    ]);
            });
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Mapping
    |--------------------------------------------------------------------------
    */
    private function mapAppointment(MainAppointment $appointment, int $currentEmployeeId, array $lookup): array
    {
        $normalizedProducts = $this->normalizeProducts($this->decodeProducts($appointment->products));
        $products = $this->mapProducts($normalizedProducts, $lookup);

        $customer = $this->customerPayload($appointment->customer);
        $creator = $this->employeePayload($appointment->creator ?: $appointment->createdBy);
        $changedBy = $this->employeePayload($appointment->changedBy);

        $employees = $appointment->employees
            ->map(fn(Employee $employee) => $this->employeePayload($employee, [
                'pivot_status' => $employee->pivot->status ?? null,
                'pivot_reason' => $employee->pivot->reason ?? null,
            ]))
            ->filter()
            ->values()
            ->all();

        $appointmentAddress = $this->buildAddressFromParts(
            $appointment->street,
            $appointment->postcode,
            $appointment->city
        );

        $bestAddress = $appointment->full_address
            ?: ($appointmentAddress !== '' ? $appointmentAddress : null)
            ?: ($products[0]['object_address'] ?? null)
            ?: ($customer['full_address'] ?? null);

        $bestLat = $appointment->latitude
            ?: ($products[0]['lat'] ?? null)
            ?: ($customer['lat'] ?? null);

        $bestLon = $appointment->longitude
            ?: ($products[0]['lon'] ?? null)
            ?: ($products[0]['lng'] ?? null)
            ?: ($customer['lon'] ?? null)
            ?: ($customer['lng'] ?? null);

        $startDate = $appointment->start_date
            ? Carbon::parse($appointment->start_date)->toDateString()
            : null;

        $endDate = $appointment->end_date
            ? Carbon::parse($appointment->end_date)->toDateString()
            : null;

        return [
            'id' => (int) $appointment->id,

            'name' => $appointment->name ?: 'Termin',
            'title' => $appointment->name ?: 'Termin',
            'note' => $appointment->note,
            'link' => $appointment->link,

            'date' => $startDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $this->formatTime($appointment->start_time),
            'start' => $this->formatTime($appointment->start_time),
            'end_time' => $this->formatTime($appointment->end_time),
            'end' => $this->formatTime($appointment->end_time),

            'color' => $appointment->color ?: '#164191',
            'status' => $appointment->status,
            'priority' => $appointment->priority,
            'type' => $appointment->appointment_type ?: $appointment->type,
            'appointment_type' => $appointment->appointment_type,
            'execution_type' => $appointment->execution_type,
            'contact_mode' => $appointment->contact_mode,
            'source' => $appointment->source,

            'address' => $bestAddress,
            'full_address' => $bestAddress,
            'appointment_address' => $appointmentAddress !== '' ? $appointmentAddress : null,
            'street' => $appointment->street,
            'postcode' => $appointment->postcode,
            'city' => $appointment->city,
            'lat' => $bestLat,
            'latitude' => $bestLat,
            'lon' => $bestLon,
            'lng' => $bestLon,
            'longitude' => $bestLon,

            'email' => $appointment->email,
            'phone' => $appointment->phone,

            'customer_id' => $appointment->customer_id,
            'customer_label' => $customer['label'] ?? null,
            'customer' => $customer,

            'branch_id' => $appointment->branch_id,
            'branch' => $this->simpleModelPayload($appointment->branch),
            'branch_address_id' => $appointment->branch_address_id,
            'branch_address' => $this->simpleModelPayload($appointment->branchAddress),

            'products' => $products,

            'employees' => $employees,
            'team' => $employees,
            'has_employees' => !empty($employees),

            'created_by' => $appointment->created_by,
            'creator' => $creator,
            'created_by_employee' => $creator,

            'changed_by' => $appointment->changed_by,
            'changed_by_employee' => $changedBy,

            'is_creator' => (int) $appointment->created_by === (int) $currentEmployeeId,
            'is_assigned' => collect($employees)->contains(
                fn($employee) => (int) ($employee['id'] ?? 0) === (int) $currentEmployeeId
            ),

            'lead_product_list_id' => $appointment->lead_product_list_id,
            'lead_stage_id' => $appointment->lead_stage_id,
            'lead_stage_sub_stage_id' => $appointment->lead_stage_sub_stage_id,
            'lead_product_list' => $this->simpleModelPayload($appointment->leadProductList),
            'lead_stage' => $this->simpleModelPayload($appointment->leadStage),
            'lead_stage_sub_stage' => $this->simpleModelPayload($appointment->leadStageSubStage),
        ];
    }

    private function employeePayload($employee, array $extra = []): ?array
    {
        if (!$employee) {
            return null;
        }

        $fullName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return array_merge([
            'id' => (int) $employee->id,
            'employee_id' => (int) $employee->id,
            'title' => $employee->title ?? null,
            'name' => $fullName !== '' ? $fullName : ('#' . $employee->id),
            'first_name' => $employee->name ?? null,
            'lastname' => $employee->lastname ?? null,
            'email' => $employee->email ?? null,
            'phone' => $employee->phone ?? null,
            'status' => $employee->status ?? null,
            'branch' => $employee->branch ?? null,
            'color' => $employee->color ?? null,
            'img' => $this->resolvePhotoUrl($employee->image ?? null),
            'image' => $this->resolvePhotoUrl($employee->image ?? null),
            'photo_url' => $this->resolvePhotoUrl($employee->image ?? null),
        ], $extra);
    }

    private function customerPayload($customer): ?array
    {
        if (!$customer) {
            return null;
        }

        $label = trim(
            ($customer->firma ?? '') . ' ' .
            ($customer->name ?? '') . ' ' .
            ($customer->lastname ?? '')
        );

        $address = $customer->full_address
            ?? $this->buildAddressFromParts(
                $customer->street ?? null,
                $customer->postcode ?? null,
                $customer->city ?? null
            );

        return [
            'id' => (int) $customer->id,
            'label' => $label !== '' ? $label : ('#' . $customer->id),
            'firma' => $customer->firma ?? null,
            'name' => $customer->name ?? null,
            'lastname' => $customer->lastname ?? null,
            'full_address' => $address ?: null,
            'address' => $address ?: null,
            'street' => $customer->street ?? null,
            'postcode' => $customer->postcode ?? null,
            'city' => $customer->city ?? null,
            'phone' => $customer->phone ?? null,
            'email' => $customer->email ?? null,
            'lat' => $customer->latitude ?? $customer->lat ?? null,
            'lon' => $customer->longitude ?? $customer->lon ?? $customer->lng ?? null,
            'lng' => $customer->longitude ?? $customer->lon ?? $customer->lng ?? null,
        ];
    }

    private function simpleModelPayload($model): ?array
    {
        if (!$model) {
            return null;
        }

        return [
            'id' => $model->id ?? null,
            'name' => $model->name
                ?? $model->title
                ?? $model->article_group
                ?? $model->stage_name
                ?? $model->label
                ?? null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Product / object helpers
    |--------------------------------------------------------------------------
    */
    private function buildProductLookups(Collection $appointments): array
    {
        $allAlternativeIds = [];
        $allProductIds = [];

        foreach ($appointments as $appointment) {
            foreach ($this->normalizeProducts($this->decodeProducts($appointment->products)) as $row) {
                if (!empty($row['alternative_id'])) {
                    $allAlternativeIds[] = (int) $row['alternative_id'];
                }

                if (!empty($row['product_id'])) {
                    $allProductIds[] = (int) $row['product_id'];
                }
            }
        }

        $allAlternativeIds = array_values(array_unique($allAlternativeIds));
        $allProductIds = array_values(array_unique($allProductIds));

        $objectsById = [];
        $productsById = [];

        if (!empty($allAlternativeIds) && Schema::hasTable('lead_alternative_adds')) {
            $objectsById = DB::table('lead_alternative_adds')
                ->whereIn('id', $allAlternativeIds)
                ->get()
                ->keyBy('id')
                ->all();
        }

        if (!empty($allProductIds) && Schema::hasTable('article_groups')) {
            $productsById = DB::table('article_groups')
                ->whereIn('id', $allProductIds)
                ->get()
                ->keyBy('id')
                ->all();
        }

        return [
            'objects_by_id' => $objectsById,
            'products_by_id' => $productsById,
        ];
    }

    private function mapProducts(array $normalizedProducts, array $lookup): array
    {
        $objectsById = $lookup['objects_by_id'] ?? [];
        $productsById = $lookup['products_by_id'] ?? [];

        return collect($normalizedProducts)
            ->map(function (array $row) use ($objectsById, $productsById) {
                $object = !empty($row['alternative_id']) && isset($objectsById[$row['alternative_id']])
                    ? $objectsById[$row['alternative_id']]
                    : null;

                $product = !empty($row['product_id']) && isset($productsById[$row['product_id']])
                    ? $productsById[$row['product_id']]
                    : null;

                $objectAddress = $object->full_address
                    ?? $this->buildAddressFromParts(
                        $object->street ?? null,
                        $object->postcode ?? null,
                        $object->city ?? null
                    );

                return [
                    'label' => $row['label'],
                    'customer_id' => $row['customer_id'],
                    'product_id' => $row['product_id'],
                    'product_name' => $product->article_group ?? $product->name ?? null,
                    'product_image' => $this->resolveProductImageUrl($product->image ?? null),
                    'alternative_id' => $row['alternative_id'],

                    'object_id' => $object->id ?? null,
                    'object_name' => $object->object_name ?? $object->name ?? null,
                    'object_address' => $objectAddress ?: null,
                    'lat' => $object->lat ?? $object->latitude ?? null,
                    'lon' => $object->lon ?? $object->lng ?? $object->longitude ?? null,
                    'lng' => $object->lon ?? $object->lng ?? $object->longitude ?? null,
                    'main' => $object->main ?? null,
                ];
            })
            ->values()
            ->all();
    }

    private function decodeProducts($value): array
    {
        if (!$value) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return is_array($decoded) ? $decoded : [];
    }

    private function normalizeProducts(array $products): array
    {
        $rows = [];

        foreach ($products as $label => $value) {
            if (!is_array($value)) {
                continue;
            }

            if ($this->isListArray($value)) {
                $rows[] = [
                    'label' => (string) $label,
                    'customer_id' => isset($value[0]) ? (int) $value[0] : null,
                    'product_id' => isset($value[1]) ? (int) $value[1] : null,
                    'alternative_id' => isset($value[2]) && $value[2] !== '' ? (int) $value[2] : null,
                ];

                continue;
            }

            $rows[] = [
                'label' => (string) ($value['label'] ?? $label),
                'customer_id' => isset($value['customer_id']) ? (int) $value['customer_id'] : null,
                'product_id' => isset($value['product_id']) ? (int) $value['product_id'] : null,
                'alternative_id' => isset($value['alternative_id']) ? (int) $value['alternative_id'] : null,
            ];
        }

        return $rows;
    }

    private function buildLegacyProductsPayload(array $data): ?array
    {
        if (empty($data['product_items']) || !is_array($data['product_items'])) {
            return null;
        }

        $legacy = [];

        foreach ($data['product_items'] as $item) {
            $label = $item['label'] ?? null;

            if (!$label) {
                continue;
            }

            $legacy[$label] = [
                (int) ($item['customer_id'] ?? ($data['customer_id'] ?? 0)),
                (int) ($item['product_id'] ?? 0),
                (string) ($item['alternative_id'] ?? ''),
            ];
        }

        return !empty($legacy) ? $legacy : null;
    }

    /*
    |--------------------------------------------------------------------------
    | Employee resolver
    |--------------------------------------------------------------------------
    */
    private function resolveEmployeeId(Request $request): ?int
    {
        $user = $request->user();

        if ($user instanceof Employee) {
            return (int) $user->id;
        }

        if ($user instanceof User) {
            if (!empty($user->name) && is_numeric($user->name)) {
                return (int) $user->name;
            }

            if ($user->employee?->id) {
                return (int) $user->employee->id;
            }

            if (!empty($user->employee_id) && is_numeric($user->employee_id)) {
                return (int) $user->employee_id;
            }
        }

        if ($user) {
            if (!empty($user->name) && is_numeric($user->name)) {
                return (int) $user->name;
            }

            if (!empty($user->employee_id) && is_numeric($user->employee_id)) {
                return (int) $user->employee_id;
            }

            if (!empty($user->email) && Schema::hasTable('employees') && Schema::hasColumn('employees', 'email')) {
                $employeeId = DB::table('employees')
                    ->where('email', $user->email)
                    ->value('id');

                if ($employeeId) {
                    return (int) $employeeId;
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Last fallback only:
        | Nuriva local can pass employee_id, but token-auth user should win.
        |--------------------------------------------------------------------------
        */
        $requestEmployeeId = $request->input('employee_id');

        if ($requestEmployeeId && is_numeric($requestEmployeeId)) {
            return (int) $requestEmployeeId;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Small helpers
    |--------------------------------------------------------------------------
    */
    private function buildAddressFromParts($street = null, $postcode = null, $city = null): string
    {
        $line1 = trim((string) $street);

        $line2 = trim(implode(' ', array_filter([
            trim((string) $postcode),
            trim((string) $city),
        ])));

        return trim(implode(', ', array_filter([$line1, $line2])));
    }

    private function timeForDb(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i', $value)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function formatTime($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('H:i');
        } catch (\Throwable $e) {
            return substr((string) $value, 0, 5);
        }
    }

    private function resolvePhotoUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        $image = trim($image);

        if ($image === '') {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        if (Str::startsWith($image, ['/'])) {
            return url($image);
        }

        if (Str::contains($image, 'images/employee')) {
            return url($image);
        }

        return url('images/employee/' . ltrim($image, '/'));
    }

    private function resolveProductImageUrl(?string $image): ?string
    {
        if (!$image) {
            return null;
        }

        $image = trim($image);

        if ($image === '') {
            return null;
        }

        if (Str::startsWith($image, ['http://', 'https://'])) {
            return $image;
        }

        if (Str::startsWith($image, ['/'])) {
            return url($image);
        }

        return url(ltrim($image, '/'));
    }

    private function isListArray(array $array): bool
    {
        if (function_exists('array_is_list')) {
            return array_is_list($array);
        }

        return array_keys($array) === range(0, count($array) - 1);
    }
}

