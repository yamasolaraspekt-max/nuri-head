<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;

use App\Models\Employee;
use App\Models\Machine;
use App\Models\MachineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class MachineServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /** Legacy route: old service detail page now opens the one-page machine UI and auto-opens service drawer. */
    public function index(Request $request, int $machine_id): RedirectResponse
    {
        return redirect()->route('machine.inventory', [
            'machine_id' => $machine_id,
            'open_services' => 1,
        ]);
    }

    /** Legacy route: old create page now opens the one-page machine UI and auto-opens service form. */
    public function create(int $machine_id): RedirectResponse
    {
        return redirect()->route('machine.inventory', [
            'machine_id' => $machine_id,
            'open_services' => 1,
        ]);
    }

    public function data(Request $request, Machine $machine): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 8), 5), 50);

        $services = $machine->services()
            ->with(['serviceEmployee:id,name,lastname', 'faultDetectedByEmployee:id,name,lastname'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->query('search'));
                $query->where(function ($sub) use ($search) {
                    $sub->where('service_type', 'like', "%{$search}%")
                        ->orWhere('service_station', 'like', "%{$search}%")
                        ->orWhere('technician', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%")
                        ->orWhere('fault_description', 'like', "%{$search}%")
                        ->orWhere('repair_description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn($query) => $query->where('status', $request->query('status')))
            ->latest('service_date')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'machine' => [
                'id' => $machine->id,
                'display_name' => $machine->display_name,
            ],
            'items' => $services->getCollection()->map(fn(MachineService $service) => $this->serviceResource($service))->values(),
            'pagination' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
                'from' => $services->firstItem(),
                'to' => $services->lastItem(),
            ],
        ]);
    }

    public function show(MachineService $service): JsonResponse
    {
        $service->load(['serviceEmployee:id,name,lastname', 'faultDetectedByEmployee:id,name,lastname', 'machine:id,name,model']);

        return response()->json(['item' => $this->serviceResource($service)]);
    }

    public function store(Request $request)
    {
        $payload = $this->validatedServicePayload($request);

        DB::beginTransaction();

        try {
            $service = MachineService::create($payload);
            $this->storeReportIfPresent($request, $service);
            $service->save();

            $this->syncMachineAfterService($service);

            DB::commit();

            return $this->serviceSavedResponse($request, $service, 'Service wurde gespeichert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Service konnte nicht gespeichert werden.'], 500);
            }

            return back()->withInput()->with('delete_msg', 'Service konnte nicht gespeichert werden.');
        }
    }

    public function update(Request $request, ?MachineService $service = null)
    {
        $service = $service ?: MachineService::findOrFail($request->input('id'));
        $payload = $this->validatedServicePayload($request, $service->id);

        DB::beginTransaction();

        try {
            $service->fill($payload);
            $this->storeReportIfPresent($request, $service);
            $service->save();

            $this->syncMachineAfterService($service);

            DB::commit();

            return $this->serviceSavedResponse($request, $service, 'Service wurde aktualisiert.');
        } catch (\Throwable $exception) {
            DB::rollBack();
            report($exception);

            if ($request->ajax() || $request->expectsJson()) {
                return response()->json(['message' => 'Service konnte nicht aktualisiert werden.'], 500);
            }

            return back()->withInput()->with('delete_msg', 'Service konnte nicht aktualisiert werden.');
        }
    }

    public function destroy(Request $request, MachineService $service): JsonResponse|RedirectResponse
    {
        $machineId = $service->machine_id;
        $this->deleteReport($service->service_report);
        $service->delete();

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['message' => 'Service wurde gelöscht.']);
        }

        return redirect()->route('machine.inventory', [
            'machine_id' => $machineId,
            'open_services' => 1,
        ])->with('delete_msg', 'Service wurde gelöscht.');
    }

    public function destroyLegacy(int $id): RedirectResponse
    {
        $service = MachineService::findOrFail($id);
        $machineId = $service->machine_id;
        $this->deleteReport($service->service_report);
        $service->delete();

        return redirect()->route('machine.inventory', [
            'machine_id' => $machineId,
            'open_services' => 1,
        ])->with('delete_msg', 'Service wurde gelöscht.');
    }

    private function validatedServicePayload(Request $request, ?int $serviceId = null): array
    {
        $validated = $request->validate([
            'machine_id' => ['required', 'exists:machines,id'],
            'service_type' => ['required', 'string', 'max:120'],
            'service_date' => ['nullable', 'date'],
            'service_by' => ['nullable', 'exists:employees,id'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'service_station' => ['nullable', 'string', 'max:255'],
            'technician' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'service_report' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:8192'],
            'maintenance_interval' => ['nullable', 'integer', 'min:0'],
            'fault_description' => ['nullable', 'string'],
            'repair_description' => ['nullable', 'string'],
            'fault_detected_at' => ['nullable', 'date'],
            'fault_detected_by' => ['nullable', 'exists:employees,id'],
            'fault_detected_location' => ['nullable', 'string', 'max:255'],
            'paid_by' => ['nullable', 'string', 'max:120'],
            'status' => ['required', Rule::in(array_keys(MachineService::statuses()))],
        ]);

        unset($validated['service_report']);

        return $validated;
    }

    private function serviceSavedResponse(Request $request, MachineService $service, string $message)
    {
        if ($request->ajax() || $request->expectsJson()) {
            $service->load(['serviceEmployee:id,name,lastname', 'faultDetectedByEmployee:id,name,lastname']);

            return response()->json([
                'message' => $message,
                'item' => $this->serviceResource($service),
            ]);
        }

        return redirect()->route('machine.inventory', [
            'machine_id' => $service->machine_id,
            'open_services' => 1,
        ])->with('save_msg', $message);
    }

    private function serviceResource(MachineService $service): array
    {
        return [
            'id' => $service->id,
            'machine_id' => $service->machine_id,
            'service_type' => $service->service_type,
            'service_date' => optional($service->service_date)->format('Y-m-d'),
            'service_by' => $service->service_by,
            'service_employee' => trim(($service->serviceEmployee?->name ?? '') . ' ' . ($service->serviceEmployee?->lastname ?? '')) ?: null,
            'price' => $service->price,
            'service_station' => $service->service_station,
            'technician' => $service->technician,
            'location' => $service->location,
            'email' => $service->email,
            'phone' => $service->phone,
            'service_report' => $service->service_report,
            'report_url' => $service->report_url,
            'maintenance_interval' => $service->maintenance_interval,
            'fault_description' => $service->fault_description,
            'repair_description' => $service->repair_description,
            'fault_detected_at' => optional($service->fault_detected_at)->format('Y-m-d'),
            'fault_detected_by' => $service->fault_detected_by,
            'fault_detected_employee' => trim(($service->faultDetectedByEmployee?->name ?? '') . ' ' . ($service->faultDetectedByEmployee?->lastname ?? '')) ?: null,
            'fault_detected_location' => $service->fault_detected_location,
            'paid_by' => $service->paid_by,
            'status' => $service->status,
            'status_label' => MachineService::statuses()[$service->status] ?? $service->status,
            'created_at' => optional($service->created_at)->format('Y-m-d H:i'),
        ];
    }

    private function syncMachineAfterService(MachineService $service): void
    {
        $machine = $service->machine;

        if (!$machine) {
            return;
        }

        if ($service->service_date) {
            $machine->last_service_date = $service->service_date;
        }

        if (strtolower((string) $service->service_type) === 'tuv') {
            $machine->technical_inspection = true;
            $machine->technical_inspection_date = $service->service_date;
        }

        if ($service->status === MachineService::STATUS_IN_PROGRESS) {
            $machine->status = Machine::STATUS_IN_SERVICE;
        }

        if ($service->status === MachineService::STATUS_DONE && in_array($machine->status, [Machine::STATUS_IN_SERVICE, Machine::STATUS_REPAIR], true)) {
            $machine->status = Machine::STATUS_ACTIVE;
        }

        $machine->save();
    }

    private function storeReportIfPresent(Request $request, MachineService $service): void
    {
        if (!$request->hasFile('service_report')) {
            return;
        }

        $this->deleteReport($service->service_report);

        $destination = public_path('documents/machine-services');
        if (!File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $fileName = time() . '_' . $request->file('service_report')->getClientOriginalName();
        $request->file('service_report')->move($destination, $fileName);
        $service->service_report = $fileName;
    }

    private function deleteReport(?string $file): void
    {
        if (!$file) {
            return;
        }

        $path = public_path('documents/machine-services/' . $file);

        if (File::exists($path)) {
            File::delete($path);
        }
    }
}
