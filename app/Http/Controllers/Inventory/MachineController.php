<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\AssetInstallment;
use App\Models\InstallmentPayment;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Machine;
use App\Models\MachineService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class MachineController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['update']);
        $this->middleware('permission:Product,delete')->only(['destroy', 'destroyLegacy']);
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('admin.product.assets.car.machines', [
            'branches' => Branch::where('status', 'Published')->orderBy('branch')->get(),
            'departments' => Department::with('branch')->orderBy('department_name')->get(),
            'employees' => Employee::where('status', 'Active')->orderBy('name')->get(),
            'article_groups' => ArticleGroup::orderBy('article_group')->get(),
            'machineStatuses' => Machine::statuses(),
            'serviceStatuses' => MachineService::statuses(),
            'installmentStatuses' => method_exists(AssetInstallment::class, 'statuses') ? AssetInstallment::statuses() : [
                'open' => 'Offen',
                'paid' => 'Bezahlt',
                'overdue' => 'Überfällig',
                'cancelled' => 'Storniert',
            ],
            'selectedMachineId' => $request->query('machine_id'),
            'openServices' => $request->boolean('open_services'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 12), 6), 100);
        $sortBy = $request->query('sort_by', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc') === 'asc' ? 'asc' : 'desc';

        $allowedSorts = [
            'id',
            'name',
            'model',
            'year',
            'mileage',
            'purchase_price',
            'purchase_date',
            'last_service_date',
            'technical_inspection_date',
            'status',
            'created_at',
        ];

        if (!in_array($sortBy, $allowedSorts, true)) {
            $sortBy = 'created_at';
        }

        $machines = $this->machineQuery($request)
            ->with(['branch:id,branch', 'department:id,department_name', 'articleGroup:id,article_group', 'latestService'])
            ->withCount(['services', 'installments'])
            ->withSum('services as services_total_cost', 'price')
            ->withSum('installments as installments_total', 'total')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'items' => $machines->getCollection()->map(fn(Machine $machine) => $this->machineResource($machine))->values(),
            'pagination' => [
                'current_page' => $machines->currentPage(),
                'last_page' => $machines->lastPage(),
                'per_page' => $machines->perPage(),
                'total' => $machines->total(),
                'from' => $machines->firstItem(),
                'to' => $machines->lastItem(),
            ],
        ]);
    }

    public function analytics(Request $request): JsonResponse
    {
        $query = $this->machineQuery($request);
        $machineIds = (clone $query)->pluck('id');

        $total = (clone $query)->count();
        $serviceCostYear = MachineService::whereIn('machine_id', $machineIds)
            ->whereYear('service_date', now()->year)
            ->sum('price');

        $installmentIds = AssetInstallment::where('type', 'machine')->whereIn('asset_id', $machineIds)->pluck('id');
        $installmentTotal = (float) AssetInstallment::whereIn('id', $installmentIds)->sum('total');
        $installmentPaid = (float) InstallmentPayment::whereIn('installment_id', $installmentIds)->sum('payment_amount');

        return response()->json([
            'total' => $total,
            'active' => (clone $query)->where('status', Machine::STATUS_ACTIVE)->count(),
            'in_service' => (clone $query)->where('status', Machine::STATUS_IN_SERVICE)->count(),
            'repair' => (clone $query)->where('status', Machine::STATUS_REPAIR)->count(),
            'inspection_due' => (clone $query)
                ->whereNotNull('technical_inspection_date')
                ->whereDate('technical_inspection_date', '<=', now()->addDays(30)->toDateString())
                ->count(),
            'purchase_value' => (float) (clone $query)->sum('purchase_price'),
            'service_cost_year' => (float) $serviceCostYear,
            'installment_total' => $installmentTotal,
            'installment_paid' => $installmentPaid,
            'installment_remaining' => max($installmentTotal - $installmentPaid, 0),
        ]);
    }

    public function show(Machine $machine): JsonResponse
    {
        $machine->load(['branch:id,branch', 'department:id,department_name', 'articleGroup:id,article_group', 'latestService'])
            ->loadCount(['services', 'installments'])
            ->loadSum('services as services_total_cost', 'price')
            ->loadSum('installments as installments_total', 'total');

        return response()->json([
            'item' => $this->machineResource($machine),
        ]);
    }

    public function store(Request $request)
    {
        $payload = $this->validatedMachinePayload($request);

        DB::beginTransaction();

        try {
            $machine = Machine::create($payload);
            $this->storeImageIfPresent($request, $machine);
            $machine->save();

            DB::commit();

            return $this->machineSavedResponse($request, $machine, 'Maschine wurde erfolgreich gespeichert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Maschine konnte nicht gespeichert werden.'], 500);
            }

            return back()->withInput()->with('delete_msg', 'Maschine konnte nicht gespeichert werden.');
        }
    }

    public function update(Request $request, ?Machine $machine = null)
    {
        $machine = $machine ?: Machine::findOrFail($request->input('id'));
        $payload = $this->validatedMachinePayload($request, $machine->id);

        DB::beginTransaction();

        try {
            $machine->fill($payload);
            $this->storeImageIfPresent($request, $machine);
            $machine->save();

            DB::commit();

            return $this->machineSavedResponse($request, $machine, 'Maschine wurde erfolgreich aktualisiert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Maschine konnte nicht aktualisiert werden.'], 500);
            }

            return back()->withInput()->with('delete_msg', 'Maschine konnte nicht aktualisiert werden.');
        }
    }

    public function destroy(Request $request, Machine $machine): JsonResponse|RedirectResponse
    {
        $this->deletePhoto($machine->image);
        $machine->delete();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'Maschine wurde gelöscht.']);
        }

        return redirect()->route('machine.inventory')->with('delete_msg', 'Maschine wurde gelöscht.');
    }

    public function destroyLegacy(int $id): RedirectResponse
    {
        $machine = Machine::findOrFail($id);
        $this->deletePhoto($machine->image);
        $machine->delete();

        return redirect()->route('machine.inventory')->with('delete_msg', 'Maschine wurde gelöscht.');
    }

    private function machineQuery(Request $request)
    {
        return Machine::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->query('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('serial_no', 'like', "%{$search}%")
                        ->orWhere('owner_name', 'like', "%{$search}%")
                        ->orWhere('owner_contact', 'like', "%{$search}%")
                        ->orWhere('year', 'like', "%{$search}%")
                        ->orWhereHas('branch', fn($branch) => $branch->where('branch', 'like', "%{$search}%"))
                        ->orWhereHas('department', fn($department) => $department->where('department_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('branch_id'), fn($query) => $query->where('branch_id', $request->query('branch_id')))
            ->when($request->filled('department_id'), fn($query) => $query->where('department_id', $request->query('department_id')))
            ->when($request->filled('article_group'), fn($query) => $query->where('article_group', $request->query('article_group')))
            ->when($request->filled('purchase_type'), fn($query) => $query->where('purchase_type', $request->query('purchase_type')))
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->query('status')))
            ->when($request->boolean('inspection_due'), function ($query) {
                $query->whereNotNull('technical_inspection_date')
                    ->whereDate('technical_inspection_date', '<=', now()->addDays(30)->toDateString());
            });
    }

    private function validatedMachinePayload(Request $request, ?int $machineId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:' . (now()->year + 1)],
            'color' => ['nullable', 'string', 'max:100'],
            'engine_type' => ['nullable', 'string', 'max:120'],
            'mileage' => ['nullable', 'integer', 'min:0'],
            'serial_no' => ['nullable', 'string', 'max:255'],
            'purchase_type' => ['required', Rule::in(['Barzahlung', 'Ratenzahlung', 'Leasing'])],
            'purchase_price' => ['nullable', 'numeric', 'min:0'],
            'purchase_date' => ['nullable', 'date'],
            'leasing_from' => ['nullable', 'string', 'max:255'],
            'leasing_start_date' => ['nullable', 'date'],
            'leasing_end_date' => ['nullable', 'date', 'after_or_equal:leasing_start_date'],
            'leasing_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'last_service_date' => ['nullable', 'date'],
            'technical_inspection' => ['nullable'],
            'technical_inspection_date' => ['nullable', 'date'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'article_group' => ['nullable', 'exists:article_groups,id'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'owner_contact' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(array_keys(Machine::statuses()))],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        unset($validated['image']);

        $validated['technical_inspection'] = $request->boolean('technical_inspection');

        if (($validated['purchase_type'] ?? null) !== 'Leasing') {
            $validated['leasing_from'] = null;
            $validated['leasing_start_date'] = null;
            $validated['leasing_end_date'] = null;
            $validated['leasing_price'] = null;
        }

        return $validated;
    }

    private function machineSavedResponse(Request $request, Machine $machine, string $message)
    {
        $installmentUrl = null;

        if ($machine->purchase_type === 'Ratenzahlung') {
            $installmentUrl = route('machine.installment.legacy.create', ['machine' => $machine->id]);
        }

        if ($request->ajax() || $request->expectsJson()) {
            $machine->load(['branch:id,branch', 'department:id,department_name', 'articleGroup:id,article_group'])
                ->loadCount('services')
                ->loadSum('services as services_total_cost', 'price');

            return response()->json([
                'message' => $message,
                'item' => $this->machineResource($machine),
                'installment_url' => $installmentUrl,
            ]);
        }

        if ($installmentUrl) {
            return redirect()->to($installmentUrl)->with('save_msg', 'Bitte Ratenzahlung einrichten.');
        }

        return redirect()->route('machine.inventory')->with('save_msg', $message);
    }

    private function machineResource(Machine $machine): array
    {
        $installmentIds = AssetInstallment::where('type', 'machine')->where('asset_id', $machine->id)->pluck('id');
        $installmentTotal = (float) AssetInstallment::whereIn('id', $installmentIds)->sum('total');
        $installmentPaid = (float) InstallmentPayment::whereIn('installment_id', $installmentIds)->sum('payment_amount');

        return [
            'id' => $machine->id,
            'name' => $machine->name,
            'model' => $machine->model,
            'display_name' => $machine->display_name,
            'year' => $machine->year,
            'color' => $machine->color,
            'engine_type' => $machine->engine_type,
            'mileage' => $machine->mileage,
            'serial_no' => $machine->serial_no,
            'purchase_type' => $machine->purchase_type,
            'purchase_price' => $machine->purchase_price,
            'purchase_date' => optional($machine->purchase_date)->format('Y-m-d'),
            'leasing_from' => $machine->leasing_from,
            'leasing_start_date' => optional($machine->leasing_start_date)->format('Y-m-d'),
            'leasing_end_date' => optional($machine->leasing_end_date)->format('Y-m-d'),
            'leasing_price' => $machine->leasing_price,
            'description' => $machine->description,
            'last_service_date' => optional($machine->last_service_date)->format('Y-m-d'),
            'technical_inspection' => (bool) $machine->technical_inspection,
            'technical_inspection_date' => optional($machine->technical_inspection_date)->format('Y-m-d'),
            'branch_id' => $machine->branch_id,
            'department_id' => $machine->department_id,
            'article_group' => $machine->article_group,
            'owner_name' => $machine->owner_name,
            'owner_contact' => $machine->owner_contact,
            'status' => $machine->status,
            'status_label' => Machine::statuses()[$machine->status] ?? $machine->status,
            'image' => $machine->image,
            'image_url' => $machine->image_url,
            'branch' => $machine->branch?->branch,
            'department' => $machine->department?->department_name,
            'article_group_name' => $machine->articleGroup?->article_group,
            'services_count' => (int) ($machine->services_count ?? 0),
            'services_total_cost' => (float) ($machine->services_total_cost ?? 0),
            'latest_service_date' => optional($machine->latestService?->service_date)->format('Y-m-d'),
            'created_at' => optional($machine->created_at)->format('Y-m-d H:i'),
            'installments_count' => (int) ($machine->installments_count ?? $installmentIds->count()),
            'installments_total' => $installmentTotal,
            'installments_paid' => $installmentPaid,
            'installments_remaining' => max($installmentTotal - $installmentPaid, 0),
            'installment_url' => route('machine.installment.legacy.create', ['machine' => $machine->id]),
        ];
    }

    private function storeImageIfPresent(Request $request, Machine $machine): void
    {
        if (!$request->hasFile('image')) {
            return;
        }

        $this->deletePhoto($machine->image);

        $destination = public_path('images/asset/car');
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
        $request->file('image')->move($destination, $imageName);
        $machine->image = $imageName;
    }

    private function deletePhoto(?string $photo): void
    {
        if (!$photo) {
            return;
        }

        $photoPath = public_path('images/asset/car/' . $photo);

        if (File::exists($photoPath)) {
            File::delete($photoPath);
        }
    }
}
