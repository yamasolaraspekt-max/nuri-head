<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AppointmentReport;
use App\Models\CustomerNote;
use App\Models\CustomerReport;
use App\Models\Deal;
use App\Models\DealNote;
use App\Models\MainAppointment;
use App\Models\PersonalTask;
use App\Models\PersonalTaskComment;
use App\Models\Problem;
use App\Models\ProblemComment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CustomerContextFeedController extends Controller
{
    public function index(Request $request, string $type)
    {
        $customerId = $request->integer('customer_id');
        $alternativeId = $request->integer('alternative_id');
        $productId = $request->integer('product_id');
        $leadProductListId = $request->integer('lead_product_list_id');

        abort_if(!$customerId, 422, 'customer_id fehlt.');

        $base = [
            'customerId' => $customerId,
            'alternativeId' => $alternativeId ?: null,
            'productId' => $productId ?: null,
            'leadProductListId' => $leadProductListId ?: null,
            'type' => $type,
        ];

        return match ($type) {
            'notes' => $this->notes($base),
            'tickets' => $this->tickets($base),
            'appointments' => $this->appointments($base),
            'tasks' => $this->tasks($base),
            'deals' => $this->deals($base),
            'customer_reports' => $this->customerReports($base),
            default => response()->view('admin.new_leads.layouts.context-feed.empty', [
                ...$base,
                'message' => 'Unbekannter Bereich.',
            ]),
        };
    }

    private function notes(array $ctx)
    {
        $notes = CustomerNote::query()
            ->with(['creator', 'replies.creator'])
            ->whereNull('parent_id')
            ->where('customer_id', $ctx['customerId'])
            ->when($ctx['alternativeId'], fn(Builder $q) => $q->where('alternative_id', $ctx['alternativeId']))
            ->when($ctx['productId'], fn(Builder $q) => $q->where('product_id', $ctx['productId']))
            ->when($ctx['leadProductListId'], fn(Builder $q) => $q->where('lead_product_list_id', $ctx['leadProductListId']))
            ->latest()
            ->get();

        return view('admin.new_leads.layouts.notes.note-list-body', [
            'notes' => $notes,
            'customer_id' => $ctx['customerId'],
            'alternative_id' => $ctx['alternativeId'],
            'product_id' => $ctx['productId'],
        ]);
    }

    private function tickets(array $ctx)
    {
        $tickets = Problem::query()
            ->with([
                'responsibleEmployee',
                'startUser',
                'comments.employee',
                'comments.replies.employee',
                'ticketTasks',
                'appointments.reports.author',
            ])
            ->where('customer_id', $ctx['customerId'])
            ->when($ctx['alternativeId'], fn(Builder $q) => $q->where('alternative_id', $ctx['alternativeId']))
            ->when($ctx['productId'], fn(Builder $q) => $q->where('product_id', $ctx['productId']))
            ->latest('date')
            ->latest()
            ->get();

        return view('admin.new_leads.layouts.context-feed.tickets', compact('tickets', 'ctx'));
    }

    /**
     * Appointment feed matching rules:
     * 1. If main_appointments.customer_id matches the current customer, show the appointment immediately.
     *    Do NOT hide it again with alternative_id/product_id filters.
     * 2. If customer_id is empty/different, use the products JSON as fallback.
     * 3. In the JSON fallback, match products[].customer_id and optionally products[].alternative_id/product_id.
     *
     * This fixes the case where the appointment belongs to the customer in main_appointments.customer_id,
     * but products/other_id do not exactly match the currently selected object/product.
     */
    private function appointments(array $ctx)
    {
        $customerId = (int) $ctx['customerId'];

        $appointments = MainAppointment::query()
            ->with([
                'createdBy',
                'changedBy',
                'reporter',
                'reports.author',
                'reports.employee',
                'comments.employee',
                'employees',
            ])
            ->where(function (Builder $query) use ($ctx, $customerId) {
                /*
                 * Main rule:
                 * Any appointment with this customer_id must be visible in the customer note/context sidebar.
                 * Do not add alternative_id/product_id as global AND filters here.
                 */
                $query->where(function (Builder $direct) use ($customerId) {
                    $direct->where('customer_id', $customerId)
                        ->orWhere('customer_id', (string) $customerId);
                });

                /*
                 * Fallback:
                 * Some appointments may only have the customer inside products JSON.
                 */
                $query->orWhere(function (Builder $jsonQuery) use ($ctx, $customerId) {
                    $jsonQuery->where(function (Builder $customerJson) use ($customerId) {
                        $this->whereAppointmentProductsCustomer($customerJson, $customerId);
                    });

                    if (!empty($ctx['alternativeId'])) {
                        $jsonQuery->where(function (Builder $alternativeJson) use ($ctx) {
                            $this->whereAppointmentProductsAlternative($alternativeJson, (int) $ctx['alternativeId']);
                        });
                    }

                    if (!empty($ctx['productId'])) {
                        $jsonQuery->where(function (Builder $productJson) use ($ctx) {
                            $this->whereAppointmentProductsProduct($productJson, (int) $ctx['productId']);
                        });
                    }
                });
            })
            ->orderByDesc('start_date')
            ->orderByDesc('start_time')
            ->orderByDesc('id')
            ->get()
            ->map(function (MainAppointment $appointment) use ($ctx) {
                $appointment->context_products = $this->appointmentProductsForContext($appointment, $ctx);
                $appointment->context_title = $this->appointmentTitle($appointment);
                $appointment->context_date_label = $this->appointmentDateLabel($appointment);
                $appointment->context_time_label = $this->appointmentTimeLabel($appointment);

                return $appointment;
            })
            ->groupBy(fn(MainAppointment $appointment) => optional($appointment->start_date)->format('Y-m-d') ?: 'ohne-datum');

        return view('admin.new_leads.layouts.context-feed.appointments', compact('appointments', 'ctx'));
    }

    private function whereAppointmentProductsCustomer(Builder $query, int $customerId): void
    {
        $query->whereJsonContains('products', ['customer_id' => (string) $customerId])
            ->orWhereJsonContains('products', ['customer_id' => $customerId])
            ->orWhere('products', 'like', '%"customer_id":"' . $customerId . '"%')
            ->orWhere('products', 'like', '%"customer_id": "' . $customerId . '"%')
            ->orWhere('products', 'like', '%"customer_id":' . $customerId . '%')
            ->orWhere('products', 'like', '%"customer_id": ' . $customerId . '%');
    }

    private function whereAppointmentProductsAlternative(Builder $query, int $alternativeId): void
    {
        $query->whereJsonContains('products', ['alternative_id' => $alternativeId])
            ->orWhereJsonContains('products', ['alternative_id' => (string) $alternativeId])
            ->orWhere('products', 'like', '%"alternative_id":' . $alternativeId . '%')
            ->orWhere('products', 'like', '%"alternative_id": ' . $alternativeId . '%')
            ->orWhere('products', 'like', '%"alternative_id":"' . $alternativeId . '"%')
            ->orWhere('products', 'like', '%"alternative_id": "' . $alternativeId . '"%')
            ->orWhere('products', 'like', '%_' . $alternativeId . '"%');
    }

    private function whereAppointmentProductsProduct(Builder $query, int $productId): void
    {
        $query->whereJsonContains('products', ['product_id' => $productId])
            ->orWhereJsonContains('products', ['product_id' => (string) $productId])
            ->orWhere('products', 'like', '%"product_id":' . $productId . '%')
            ->orWhere('products', 'like', '%"product_id": ' . $productId . '%')
            ->orWhere('products', 'like', '%"product_id":"' . $productId . '"%')
            ->orWhere('products', 'like', '%"product_id": "' . $productId . '"%');
    }

    private function appointmentProductsForContext(MainAppointment $appointment, array $ctx): Collection
    {
        return $this->decodeAppointmentProducts($appointment->products ?? null)
            ->filter(function (array $product) use ($ctx) {
                $productCustomerId = (string) Arr::get($product, 'customer_id', '');
                $productAlternativeId = (string) Arr::get($product, 'alternative_id', '');
                $productProductId = (string) Arr::get($product, 'product_id', '');

                /*
                 * If products JSON has product rows, show the matching rows.
                 * Empty/missing product keys should not hide the appointment itself.
                 */
                if ($productCustomerId !== '' && $productCustomerId !== (string) $ctx['customerId']) {
                    return false;
                }

                if ($ctx['alternativeId'] && $productAlternativeId !== '' && $productAlternativeId !== (string) $ctx['alternativeId']) {
                    return false;
                }

                if ($ctx['productId'] && $productProductId !== '' && $productProductId !== (string) $ctx['productId']) {
                    return false;
                }

                return true;
            })
            ->values();
    }

    private function decodeAppointmentProducts($products): Collection
    {
        if ($products instanceof Collection) {
            return $products->map(fn($item) => (array) $item)->values();
        }

        if (is_array($products)) {
            return collect($products)->map(fn($item) => (array) $item)->values();
        }

        if (!$products || !is_string($products)) {
            return collect();
        }

        $decoded = json_decode($products, true);

        if (!is_array($decoded)) {
            return collect();
        }

        return collect($decoded)->map(fn($item) => (array) $item)->values();
    }

    private function appointmentTitle(MainAppointment $appointment): string
    {
        foreach (['title', 'name', 'subject', 'appointment_title', 'termin_title'] as $field) {
            if (!empty($appointment->{$field})) {
                return (string) $appointment->{$field};
            }
        }

        return 'Termin #' . $appointment->id;
    }

    private function appointmentDateLabel(MainAppointment $appointment): string
    {
        if (!$appointment->start_date) {
            return 'Ohne Datum';
        }

        try {
            return Carbon::parse($appointment->start_date)->format('d.m.Y');
        } catch (\Throwable) {
            return (string) $appointment->start_date;
        }
    }

    private function appointmentTimeLabel(MainAppointment $appointment): string
    {
        $start = $appointment->start_time ? Str::of((string) $appointment->start_time)->substr(0, 5) : null;
        $end = $appointment->end_time ? Str::of((string) $appointment->end_time)->substr(0, 5) : null;

        if ($start && $end) {
            return $start . ' - ' . $end;
        }

        if ($start) {
            return (string) $start;
        }

        return 'Ohne Uhrzeit';
    }

    private function tasks(array $ctx)
    {
        $tasks = PersonalTask::query()
            ->with([
                'assignedBy',
                'employees',
                'steps.doneBy',
                'rootComments.author',
                'rootComments.replies.author',
            ])
            ->where('customer_id', $ctx['customerId'])
            ->when($ctx['alternativeId'], fn(Builder $q) => $q->where('alternative_id', $ctx['alternativeId']))
            ->when($ctx['productId'], fn(Builder $q) => $q->where('product_id', $ctx['productId']))
            ->latest()
            ->get();

        return view('admin.new_leads.layouts.context-feed.tasks', compact('tasks', 'ctx'));
    }

    private function deals(array $ctx)
    {
        $deals = Deal::query()
            ->with([
                'author',
                'checkedBy',
                'reviewer',
                'product',
                'folder',
                'measurements',
                'latestMeasurement',
                'deliveryNotes',
                'latestDeliveryNote',
            ])
            ->where('customer_id', $ctx['customerId'])
            ->when($ctx['alternativeId'], fn(Builder $q) => $q->where('alternative_id', $ctx['alternativeId']))
            ->when($ctx['productId'], fn(Builder $q) => $q->where('product_id', $ctx['productId']))
            ->latest()
            ->get();

        $dealNotes = DealNote::query()
            ->with(['children'])
            ->whereNull('parent_id')
            ->where('customer_id', $ctx['customerId'])
            ->when($ctx['alternativeId'], fn(Builder $q) => $q->where('alternative_id', $ctx['alternativeId']))
            ->when($ctx['productId'], fn(Builder $q) => $q->where('product_id', $ctx['productId']))
            ->latest()
            ->get()
            ->groupBy('deal_id');

        return view('admin.new_leads.layouts.context-feed.deals', compact('deals', 'dealNotes', 'ctx'));
    }

    private function customerReports(array $ctx)
    {
        $reports = CustomerReport::query()
            ->with(['reporter', 'comments'])
            ->where('customer_id', $ctx['customerId'])
            ->when($ctx['alternativeId'], fn(Builder $q) => $q->where('alternative_id', $ctx['alternativeId']))
            ->when($ctx['productId'], fn(Builder $q) => $q->where('product_id', $ctx['productId']))
            ->latest()
            ->get();

        return view('admin.new_leads.layouts.context-feed.customer-reports', compact('reports', 'ctx'));
    }

    public function storeTicketComment(Request $request, Problem $problem)
    {
        $request->validate([
            'comment' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer'],
            'ticket_task_id' => ['nullable', 'integer'],
        ]);

        $comment = ProblemComment::create([
            'ticket_id' => $problem->id,
            'employee_id' => auth()->user()->name,
            'parent_id' => $request->parent_id,
            'comment' => $request->comment,
            'ticket_task_id' => $request->ticket_task_id,
        ]);

        return response()->json(['success' => true, 'id' => $comment->id]);
    }

    public function storeAppointmentReport(Request $request, MainAppointment $appointment)
    {
        $request->validate([
            'report' => ['required', 'string'],
            'next_step' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
            'type' => ['nullable', 'string'],
        ]);

        $report = AppointmentReport::create([
            'appointment_id' => $appointment->id,
            'employee_id' => auth()->user()->name,
            'report_by' => auth()->user()->name,
            'type' => $request->type ?: 'customer_context',
            'report' => $request->report,
            'report_date' => now(),
            'next_step' => $request->next_step,
            'due_date' => $request->due_date,
        ]);

        return response()->json(['success' => true, 'id' => $report->id]);
    }

    public function storeTaskComment(Request $request, PersonalTask $task)
    {
        $request->validate([
            'comment' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $comment = PersonalTaskComment::create([
            'task_id' => $task->id,
            'comment_by' => auth()->user()->name,
            'comment' => $request->comment,
            'status' => 'Published',
            'parent_id' => $request->parent_id,
        ]);

        return response()->json(['success' => true, 'id' => $comment->id]);
    }

    public function storeDealNote(Request $request, Deal $deal)
    {
        $request->validate([
            'description' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $note = DealNote::create([
            'deal_id' => $deal->id,
            'customer_id' => $deal->customer_id,
            'alternative_id' => $deal->alternative_id,
            'product_id' => $deal->product_id,
            'parent_id' => $request->parent_id,
            'description' => $request->description,
            'created_by' => auth()->user()->name,
        ]);

        return response()->json(['success' => true, 'id' => $note->id]);
    }
}
