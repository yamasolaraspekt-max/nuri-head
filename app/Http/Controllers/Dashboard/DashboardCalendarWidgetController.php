<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\Employee;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\MainAppointment;
use App\Models\NewLeads;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardCalendarWidgetController extends Controller
{
    private function employeeId(): int
    {
        return (int) auth()->user()->name;
    }

    public function employees(): JsonResponse
    {
        $currentEmployeeId = $this->employeeId();

        $employees = Employee::query()
            ->select('id', 'name', 'lastname', 'image')
            ->orderBy('name')
            ->orderBy('lastname')
            ->get()
            ->map(fn($employee) => $this->employeeMini($employee))
            ->values();

        return response()->json([
            'success' => true,
            'current_employee_id' => $currentEmployeeId,
            'employees' => $employees,
        ]);
    }

    public function month(Request $request): JsonResponse
    {
        $employeeId = (int) $request->input('employee_id', $this->employeeId());

        $month = $request->input('month', now()->format('Y-m'));

        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $appointments = $this->appointmentBaseQuery($employeeId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhereBetween('end_date', [$start->toDateString(), $end->toDateString()])
                    ->orWhere(function ($sq) use ($start, $end) {
                        $sq->whereDate('start_date', '<=', $start->toDateString())
                            ->whereDate('end_date', '>=', $end->toDateString());
                    });
            })
            ->with([
                'employees:id,name,lastname,image',
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,full_address,phone,telephone,email,latitude,longitude',
                'problem:id,ticket_no,problem,status,priority,customer_id,alternative_id,product_id,responsible',
                'problem.responsibleEmployee:id,name,lastname,image',
                'reports:id,appointment_id,employee_id,report_by,type,report,report_date,next_step,due_date,likes,dislikes',
            ])
            ->orderBy('start_date')
            ->orderBy('start_time')
            ->get();

        $days = [];

        foreach ($appointments as $appointment) {
            $dateKey = optional($appointment->start_date)->format('Y-m-d');

            if (!$dateKey) {
                continue;
            }

            if (!isset($days[$dateKey])) {
                $days[$dateKey] = [
                    'date' => $dateKey,
                    'count' => 0,
                    'has_report' => 0,
                    'missing_report' => 0,
                    'ticket_count' => 0,
                    'appointments' => [],
                ];
            }

            $hasReport = $appointment->reports->isNotEmpty() || (bool) $appointment->is_report;

            $days[$dateKey]['count']++;
            $days[$dateKey]['has_report'] += $hasReport ? 1 : 0;
            $days[$dateKey]['missing_report'] += $hasReport ? 0 : 1;
            $days[$dateKey]['ticket_count'] += $appointment->problem_id ? 1 : 0;

            $days[$dateKey]['appointments'][] = $this->appointmentCard($appointment);
        }

        return response()->json([
            'success' => true,
            'employee_id' => $employeeId,
            'month' => $start->format('Y-m'),
            'month_label' => $start->translatedFormat('F Y'),
            'days' => array_values($days),
            'summary' => [
                'total' => $appointments->count(),
                'with_report' => $appointments->filter(fn($a) => $a->reports->isNotEmpty() || (bool) $a->is_report)->count(),
                'without_report' => $appointments->filter(fn($a) => !$a->reports->isNotEmpty() && !(bool) $a->is_report)->count(),
                'tickets' => $appointments->whereNotNull('problem_id')->count(),
            ],
        ]);
    }

    public function day(Request $request): JsonResponse
    {
        $employeeId = (int) $request->input('employee_id', $this->employeeId());
        $date = Carbon::parse($request->input('date', now()->toDateString()))->toDateString();

        $appointments = $this->appointmentBaseQuery($employeeId)
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->with([
                'employees:id,name,lastname,image',
                'createdBy:id,name,lastname,image',
                'changedBy:id,name,lastname,image',
                'customer:id,customer_no,firma,name,lastname,street,postcode,city,full_address,phone,telephone,email,latitude,longitude',
                'problem:id,ticket_no,problem,status,priority,customer_id,alternative_id,product_id,responsible',
                'problem.responsibleEmployee:id,name,lastname,image',
                'reports:id,appointment_id,employee_id,report_by,type,report,report_date,next_step,due_date,likes,dislikes',
            ])
            ->orderBy('start_time')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'date' => $date,
            'date_label' => Carbon::parse($date)->translatedFormat('l, d.m.Y'),
            'appointments' => $appointments->map(fn($appointment) => $this->appointmentCard($appointment))->values(),
        ]);
    }

    public function show(MainAppointment $appointment): JsonResponse
    {
        $appointment->load([
            'employees:id,name,lastname,image',
            'createdBy:id,name,lastname,image',
            'changedBy:id,name,lastname,image',
            'customer:id,customer_no,firma,name,lastname,street,postcode,city,full_address,phone,telephone,email,latitude,longitude,info,status',
            'branch:id,branch,slug,initial,color,second_color,logo_url',
            'branchAddress',
            'problem:id,ticket_no,problem,solution,status,priority,customer_id,alternative_id,product_id,responsible,first_contact,start_user,progress_user,end_user',
            'problem.customer:id,customer_no,firma,name,lastname,street,postcode,city,full_address,phone,telephone,email,latitude,longitude',
            'problem.alternative:id,lead_id,object_name,street,postcode,city,full_address,lat,lon,status,stage',
            'problem.product',
            'problem.responsibleEmployee:id,name,lastname,image',
            'task:id,task_title,description,task_status,priority,due_date,due_time,customer_id',
            'reports.employee:id,name,lastname,image',
            'reports.author:id,name,lastname,image',
            'comments',
        ]);

        $productContext = $this->resolveProductContext($appointment);

        return response()->json([
            'success' => true,
            'appointment' => [
                'id' => $appointment->id,
                'title' => $appointment->name ?: ('Termin #' . $appointment->id),
                'note' => $appointment->note,
                'status' => $appointment->status,
                'priority' => $appointment->priority,
                'appointment_type' => $appointment->appointment_type,
                'execution_type' => $appointment->execution_type,
                'contact_mode' => $appointment->contact_mode,
                'source' => $appointment->source,
                'type' => $appointment->type,
                'date_type' => $appointment->date_type,
                'start_date' => optional($appointment->start_date)->format('Y-m-d'),
                'end_date' => optional($appointment->end_date)->format('Y-m-d'),
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'total_time' => $appointment->total_time,
                'repeat' => $appointment->repeat,
                'reminder_date' => optional($appointment->reminder_date)->format('Y-m-d'),
                'reminder_time' => $appointment->reminder_time,
                'full_address' => $this->appointmentAddress($appointment, $productContext),
                'street' => $appointment->street,
                'postcode' => $appointment->postcode,
                'city' => $appointment->city,
                'latitude' => $this->appointmentLatitude($appointment, $productContext),
                'longitude' => $this->appointmentLongitude($appointment, $productContext),
                'map_url' => $this->mapUrl(
                    $this->appointmentLatitude($appointment, $productContext),
                    $this->appointmentLongitude($appointment, $productContext),
                    $this->appointmentAddress($appointment, $productContext)
                ),
                'has_report' => $appointment->reports->isNotEmpty() || (bool) $appointment->is_report,
                'is_ticket' => (bool) $appointment->problem_id,
                'is_task' => (bool) $appointment->task_id,
                'is_customer' => (bool) $appointment->customer_id,
            ],
            'employees' => $appointment->employees->map(fn($employee) => $this->employeeMini($employee))->values(),
            'creator' => $this->employeeMini($appointment->createdBy),
            'changed_by' => $this->employeeMini($appointment->changedBy),
            'customer' => $this->customerPayload($appointment->customer ?: $appointment->problem?->customer),
            'object' => $this->objectPayload($productContext['object'] ?? $appointment->problem?->alternative),
            'product' => $this->productPayload($productContext['product'] ?? $appointment->problem?->product),
            'lead_product' => $this->leadProductPayload($productContext['lead_product'] ?? null),
            'ticket' => $this->ticketPayload($appointment),
            'reports' => $appointment->reports->map(fn($report) => [
                'id' => $report->id,
                'type' => $report->type,
                'report' => $report->report,
                'report_date' => optional($report->report_date)->format('d.m.Y'),
                'next_step' => $report->next_step,
                'due_date' => optional($report->due_date)->format('d.m.Y'),
                'employee' => $this->employeeMini($report->employee),
                'author' => $this->employeeMini($report->author),
                'likes' => $report->likes_count ?? (int) $report->likes,
                'dislikes' => $report->dislikes_count ?? (int) $report->dislikes,
                'comments' => $report->comment_items ?? [],
            ])->values(),
            'comments' => $appointment->comments->map(fn($comment) => [
                'id' => $comment->id,
                'comment' => $comment->comment ?? $comment->text ?? $comment->description ?? null,
                'created_at' => optional($comment->created_at)->format('d.m.Y H:i'),
            ])->values(),
        ]);
    }

    private function appointmentBaseQuery(int $employeeId)
    {
        return MainAppointment::query()
            ->where(function ($q) use ($employeeId) {
                $q->where('created_by', $employeeId)
                    ->orWhere('report_by', $employeeId)
                    ->orWhereJsonContains('report_responsible', $employeeId)
                    ->orWhereHas('employees', function ($sq) use ($employeeId) {
                        $sq->where('employees.id', $employeeId);
                    })
                    ->orWhereHas('problem.employees', function ($sq) use ($employeeId) {
                        $sq->where('employees.id', $employeeId);
                    })
                    ->orWhereHas('problem', function ($sq) use ($employeeId) {
                        $sq->where('responsible', $employeeId)
                            ->orWhere('first_contact', $employeeId)
                            ->orWhere('start_user', $employeeId)
                            ->orWhere('progress_user', $employeeId)
                            ->orWhere('end_user', $employeeId);
                    });
            });
    }

    private function appointmentCard(MainAppointment $appointment): array
    {
        $productContext = $this->resolveProductContext($appointment);
        $hasReport = $appointment->reports->isNotEmpty() || (bool) $appointment->is_report;

        return [
            'id' => $appointment->id,
            'title' => $appointment->name ?: ('Termin #' . $appointment->id),
            'date' => optional($appointment->start_date)->format('Y-m-d'),
            'date_label' => optional($appointment->start_date)->format('d.m.Y'),
            'time' => trim(($appointment->start_time ?? '') . ' - ' . ($appointment->end_time ?? '')),
            'start_time' => $appointment->start_time,
            'end_time' => $appointment->end_time,
            'status' => $appointment->status,
            'priority' => $appointment->priority,
            'appointment_type' => $appointment->appointment_type,
            'execution_type' => $appointment->execution_type,
            'color' => $appointment->color,
            'has_report' => $hasReport,
            'report_count' => $appointment->reports->count(),
            'is_ticket' => (bool) $appointment->problem_id,
            'ticket_no' => $appointment->problem?->ticket_no,
            'customer' => $this->customerName($appointment->customer ?: $appointment->problem?->customer),
            'address' => $this->appointmentAddress($appointment, $productContext),
            'employees' => $appointment->employees->map(fn($employee) => $this->employeeMini($employee))->values(),
        ];
    }

    private function resolveProductContext(MainAppointment $appointment): array
    {
        $products = $appointment->products;

        if (is_string($products)) {
            $decoded = json_decode($products, true);
            $products = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($products) || empty($products)) {
            return [
                'customer' => $appointment->customer,
                'object' => null,
                'product' => null,
                'lead_product' => null,
            ];
        }

        $first = collect($products)->first();

        if (!is_array($first)) {
            return [];
        }

        $customerId = isset($first[0]) ? (int) $first[0] : null;
        $productId = isset($first[1]) ? (int) $first[1] : null;
        $alternativeId = isset($first[2]) ? (int) $first[2] : null;

        $customer = $customerId ? NewLeads::query()->find($customerId) : $appointment->customer;

        $object = $alternativeId
            ? LeadAlternativeAdd::query()->find($alternativeId)
            : null;

        $product = $productId
            ? ArticleGroup::query()->find($productId)
            : null;

        $leadProduct = LeadProductList::query()
            ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
            ->when($productId, fn($q) => $q->where('product_id', $productId))
            ->when($alternativeId, fn($q) => $q->where('alternative_id', $alternativeId))
            ->with(['department:id,department_name', 'employee:id,name,lastname,image', 'fieldEmployee:id,name,lastname,image'])
            ->first();

        return [
            'customer' => $customer,
            'object' => $object,
            'product' => $product,
            'lead_product' => $leadProduct,
        ];
    }

    private function appointmentAddress(MainAppointment $appointment, array $context = []): ?string
    {
        return $appointment->full_address
            ?: trim(($appointment->street ?? '') . ' ' . ($appointment->postcode ?? '') . ' ' . ($appointment->city ?? ''))
            ?: ($context['object']?->full_address ?? null)
            ?: trim(($context['object']?->street ?? '') . ' ' . ($context['object']?->postcode ?? '') . ' ' . ($context['object']?->city ?? ''))
            ?: ($appointment->customer?->full_address ?? null);
    }

    private function appointmentLatitude(MainAppointment $appointment, array $context = []): ?float
    {
        return $appointment->latitude
            ? (float) $appointment->latitude
            : ($context['object']?->lat ? (float) $context['object']->lat : ($appointment->customer?->latitude ? (float) $appointment->customer->latitude : null));
    }

    private function appointmentLongitude(MainAppointment $appointment, array $context = []): ?float
    {
        return $appointment->longitude
            ? (float) $appointment->longitude
            : ($context['object']?->lon ? (float) $context['object']->lon : ($appointment->customer?->longitude ? (float) $appointment->customer->longitude : null));
    }

    private function mapUrl(?float $lat, ?float $lng, ?string $address): ?string
    {
        if ($lat && $lng) {
            return 'https://www.google.com/maps?q=' . urlencode($lat . ',' . $lng);
        }

        if ($address) {
            return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);
        }

        return null;
    }

    private function employeeMini(?Employee $employee): ?array
    {
        if (!$employee) {
            return null;
        }

        return [
            'id' => $employee->id,
            'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
            'image_url' => $employee->image
                ? asset('images/employee/' . $employee->image)
                : asset('images/gender/male.png'),
        ];
    }

    private function customerPayload(?NewLeads $customer): ?array
    {
        if (!$customer) {
            return null;
        }

        return [
            'id' => $customer->id,
            'customer_no' => $customer->customer_no,
            'name' => $this->customerName($customer),
            'status' => $customer->status,
            'phone' => $customer->phone ?: $customer->telephone,
            'email' => $customer->email,
            'address' => $customer->full_address ?: trim(($customer->street ?? '') . ' ' . ($customer->postcode ?? '') . ' ' . ($customer->city ?? '')),
            'latitude' => $customer->latitude,
            'longitude' => $customer->longitude,
            'info' => $customer->info,
        ];
    }

    private function customerName(?NewLeads $customer): ?string
    {
        if (!$customer) {
            return null;
        }

        return $customer->display_name
            ?? $customer->firma
            ?? trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''))
            ?: ('Kunde #' . $customer->id);
    }

    private function objectPayload(?LeadAlternativeAdd $object): ?array
    {
        if (!$object) {
            return null;
        }

        return [
            'id' => $object->id,
            'name' => $object->display_name,
            'status' => $object->status,
            'stage' => $object->stage,
            'address' => $object->full_address ?: trim(($object->street ?? '') . ' ' . ($object->postcode ?? '') . ' ' . ($object->city ?? '')),
            'latitude' => $object->lat,
            'longitude' => $object->lon,
            'note' => $object->note,
        ];
    }

    private function productPayload($product): ?array
    {
        if (!$product) {
            return null;
        }

        return [
            'id' => $product->id,
            'name' => $product->article_group_name ?? $product->name ?? $product->title ?? ('Produkt #' . $product->id),
        ];
    }

    private function leadProductPayload(?LeadProductList $leadProduct): ?array
    {
        if (!$leadProduct) {
            return null;
        }

        return [
            'id' => $leadProduct->id,
            'status' => $leadProduct->status,
            'work_status' => $leadProduct->work_status,
            'stage' => $leadProduct->stage,
            'price' => $leadProduct->price,
            'net_amount' => $leadProduct->net_amount,
            'gross_amount' => $leadProduct->gross_amount,
            'department' => $leadProduct->department?->department_name,
            'employee' => $this->employeeMini($leadProduct->employee),
            'field_employee' => $this->employeeMini($leadProduct->fieldEmployee),
        ];
    }

    private function ticketPayload(MainAppointment $appointment): ?array
    {
        $problem = $appointment->problem;

        if (!$problem) {
            return null;
        }

        return [
            'id' => $problem->id,
            'ticket_no' => $problem->ticket_no,
            'problem' => $problem->problem,
            'solution' => $problem->solution,
            'status' => $problem->status,
            'priority' => $problem->priority,
            'responsible' => $this->employeeMini($problem->responsibleEmployee),
        ];
    }
}