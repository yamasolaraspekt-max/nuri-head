<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CustomerNote;
use App\Models\CustomerReport;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\MainAppointment;
use App\Models\NewLeads;
use App\Models\OfferFolder;
use App\Models\PersonalTask;
use App\Models\Problem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LeadHistoryController extends Controller
{
    public function show(Request $request, NewLeads $customer)
    {
        $radius = max(1, min(100, (int) $request->integer('radius', 10)));

        $customer->loadMissing([
            'branchRel',
            'contactPerson',
            'contactPeople',
            'objects.latestScreenshot',
            'objects.pvWpDetail',
            'objects.roofs',
            'objects.rooms',
            'objects.customerNotes.creator',
            'objects.reports.reporter',
            'objects.customerProductInfos.media',
            'objects.customerProductInfos.product',
            'objects.productLists.product',
            'objects.productLists.articleGroup',
            'objects.productLists.department',
            'objects.productLists.employee',
            'objects.productLists.fieldEmployee',
            'objects.productLists.companyStage',
            'objects.productLists.productStage',
            'objects.productLists.productTaskPhase',
            'objects.productLists.leadStageSubStage',
            'objects.reviews.employee',
            'objects.offerFolders.offer',
            'objects.offerFolders.detail',
            'objects.offerFolders.product',
            'leadProductLists.product',
            'leadProductLists.articleGroup',
            'leadProductLists.alternative',
            'leadProductLists.department',
            'leadProductLists.employee',
            'leadProductLists.fieldEmployee',
            'leadProductLists.companyStage',
            'leadProductLists.productStage',
            'leadProductLists.productTaskPhase',
            'leadProductLists.leadStageSubStage',
            'customerNotes.creator',
            'customerNotes.replies.creator',
            'reports.reporter',
            'reviews.employee',
            'personalTasks.assignedBy',
            'personalTasks.employees',
            'personalTasks.steps.doneBy',
            'personalTasks.rootComments.author',
            'offerFolders.offer',
            'offerFolders.detail',
            'offerFolders.product',
            'offerFolders.alternative',
            'offerFolders.creator',
            'offerFolders.attachments',
        ]);

        $notes = $customer->customerNotes
            ->whereNull('parent_id')
            ->sortByDesc(fn($note) => $this->dateValue($note, ['created_at', 'due_date']));

        $reports = $customer->reports
            ->sortByDesc(fn($report) => $this->dateValue($report, ['report_date', 'created_at']));

        $appointments = $this->appointmentsForCustomer((int) $customer->id);

        $tickets = Problem::query()
            ->with([
                'responsibleEmployee',
                'startUser',
                'alternative',
                'product',
                'comments.employee',
                'comments.replies.employee',
                'ticketTasks',
                'appointments.reports.author',
            ])
            ->where('customer_id', $customer->id)
            ->latest('date')
            ->latest()
            ->get();

        $tasks = $customer->personalTasks
            ->sortByDesc(fn($task) => $this->dateValue($task, ['due_date', 'start_date', 'created_at']));

        $offerFolders = $customer->offerFolders
            ->sortByDesc(fn($folder) => $this->dateValue($folder, ['updated_at', 'created_at']));

        $invoices = $this->optionalModelRows(
            \App\Models\Invoice::class,
            fn($query) => $query->where('customer_id', $customer->id)->latest('created_at')->get(),
            collect()
        );

        $dailyTimes = $this->optionalModelRows(
            \App\Models\DailyReportTime::class,
            fn($query) => $query->with(['employee', 'customerTimeRows.object', 'customerTimeRows.product', 'customerTimeRows.leadProduct'])
                ->where(function (Builder $q) use ($customer) {
                    $q->where('customer_id', $customer->id)
                        ->orWhereHas('customerTimeRows', fn(Builder $qq) => $qq->where('customer_id', $customer->id));
                })
                ->latest('report_date')
                ->latest('created_at')
                ->get(),
            collect()
        );

        $deliveryNotes = $this->optionalModelRows(
            \App\Models\DeliveryNote::class,
            fn($query) => $query->with(['alternative', 'leadProductList.product', 'deal', 'handoverEmployee'])
                ->where('customer_id', $customer->id)
                ->latest('created_at')
                ->get(),
            collect()
        );

        $goodsReceipts = $this->optionalModelRows(
            \App\Models\GoodsReceipt::class,
            fn($query) => $query->where('customer_id', $customer->id)->latest('created_at')->get(),
            collect()
        );

        $images = $this->optionalModelRows(
            \App\Models\Image::class,
            fn($query) => $query->where('customer_id', $customer->id)->latest('created_at')->limit(120)->get(),
            collect()
        );

        $productInfos = $this->optionalModelRows(
            \App\Models\CustomerProductInfo::class,
            fn($query) => $query->with(['alternative', 'product', 'department', 'media'])
                ->where('customer_id', $customer->id)
                ->latest('created_at')
                ->get(),
            collect()
        );

        $neighbors = $this->neighborsForCustomer($customer, $radius);

        $timeline = $this->buildTimeline(
            $customer,
            $notes,
            $reports,
            $appointments,
            $tickets,
            $tasks,
            $offerFolders,
            $invoices,
            $dailyTimes,
            $deliveryNotes,
            $goodsReceipts,
            $productInfos
        );

        $stats = [
            'objects' => $customer->objects->count(),
            'products' => $customer->leadProductLists->count(),
            'notes' => $notes->count(),
            'reports' => $reports->count(),
            'appointments' => $appointments->count(),
            'tasks' => $tasks->count(),
            'tickets' => $tickets->count(),
            'offers' => $offerFolders->count(),
            'invoices' => $invoices->count(),
            'dailyTimes' => $dailyTimes->count(),
            'images' => $images->count(),
            'reviews' => $customer->reviews->count(),
            'neighbors' => $neighbors->count(),
        ];

        return view('admin.new_leads.history.show', compact(
            'customer',
            'stats',
            'notes',
            'reports',
            'appointments',
            'tickets',
            'tasks',
            'offerFolders',
            'invoices',
            'dailyTimes',
            'deliveryNotes',
            'goodsReceipts',
            'images',
            'productInfos',
            'neighbors',
            'radius',
            'timeline'
        ));
    }

    private function appointmentsForCustomer(int $customerId): Collection
    {
        return MainAppointment::query()
            ->with([
                'createdBy',
                'changedBy',
                'reporter',
                'reports.author',
                'reports.employee',
                'comments.employee',
                'employees',
                'problem',
                'task',
                'dealMeasurement',
            ])
            ->where(function (Builder $q) use ($customerId) {
                $q->where('customer_id', $customerId)
                    ->orWhere('customer_id', (string) $customerId)
                    ->orWhere(function (Builder $jsonQuery) use ($customerId) {
                        $jsonQuery->whereJsonContains('products', ['customer_id' => $customerId])
                            ->orWhereJsonContains('products', ['customer_id' => (string) $customerId])
                            ->orWhere('products', 'like', '%"customer_id":' . $customerId . '%')
                            ->orWhere('products', 'like', '%"customer_id":"' . $customerId . '"%');
                    });
            })
            ->orderByDesc('start_date')
            ->orderByDesc('start_time')
            ->get()
            ->map(function (MainAppointment $appointment) {
                $appointment->history_products = $this->decodeJsonList($appointment->products);
                return $appointment;
            });
    }

    private function neighborsForCustomer(NewLeads $customer, int $radiusKm): Collection
    {
        if (!class_exists(LeadAlternativeAdd::class) || !Schema::hasTable((new LeadAlternativeAdd())->getTable())) {
            return collect();
        }

        $base = $customer->objects->firstWhere('main', true)
            ?: $customer->objects->firstWhere('lat')
            ?: $customer->objects->firstWhere('lon')
            ?: null;

        $baseLat = $base?->lat ?: $customer->latitude;
        $baseLng = $base?->lon ?: $customer->longitude;

        if (!$baseLat || !$baseLng) {
            return collect();
        }

        $lat = (float) $baseLat;
        $lng = (float) $baseLng;

        try {
            return LeadAlternativeAdd::query()
                ->with([
                    'lead',
                    'productLists.product',
                    'productLists.articleGroup',
                    'productLists.companyStage',
                    'productLists.productStage',
                    'customerProductInfos.product',
                    'customerProductInfos.media',
                ])
                ->select('lead_alternative_adds.*')
                ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lon) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) as distance_km', [$lat, $lng, $lat])
                ->whereNotNull('lat')
                ->whereNotNull('lon')
                ->where(function (Builder $q) use ($customer) {
                    $q->where('lead_id', '!=', $customer->id)
                        ->orWhere('id', '!=', $customer->objects->first()?->id);
                })
                ->having('distance_km', '<=', $radiusKm)
                ->orderBy('distance_km')
                ->limit(50)
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    private function buildTimeline(
        NewLeads $customer,
        Collection $notes,
        Collection $reports,
        Collection $appointments,
        Collection $tickets,
        Collection $tasks,
        Collection $offerFolders,
        Collection $invoices,
        Collection $dailyTimes,
        Collection $deliveryNotes,
        Collection $goodsReceipts,
        Collection $productInfos
    ): Collection {
        $items = collect();

        $notes->each(fn($note) => $items->push($this->timelineItem('note', 'Notiz', Str::limit($this->plainText($note->description), 180), $this->dateValue($note, ['created_at', 'due_date']), 'message-square', 'blue', $note)));
        $reports->each(fn($report) => $items->push($this->timelineItem('report', 'Kundenbericht', Str::limit($this->plainText($report->report ?? $report->description ?? $report->note ?? ''), 180), $this->dateValue($report, ['report_date', 'created_at']), 'file-text', 'green', $report)));
        $appointments->each(fn($appointment) => $items->push($this->timelineItem('appointment', $appointment->name ?: ('Termin #' . $appointment->id), Str::limit($this->plainText($appointment->note ?: $appointment->appointment_type ?: 'Termin'), 180), $this->dateValue($appointment, ['start_date', 'created_at']), 'calendar', 'orange', $appointment)));
        $tickets->each(fn($ticket) => $items->push($this->timelineItem('ticket', 'Ticket #' . ($ticket->ticket_no ?: $ticket->id), Str::limit($this->plainText($ticket->problem ?: $ticket->solution ?: ''), 180), $this->dateValue($ticket, ['date', 'created_at']), 'alert-triangle', 'pink', $ticket)));
        $tasks->each(fn($task) => $items->push($this->timelineItem('task', $task->task_title ?: ('Aufgabe #' . $task->id), Str::limit($this->plainText($task->description), 180), $this->dateValue($task, ['due_date', 'start_date', 'created_at']), 'check-square', 'purple', $task)));
        $offerFolders->each(fn($folder) => $items->push($this->timelineItem('offer', $folder->name ?: ('Angebot #' . $folder->id), trim(($folder->workflow_status_label ?? '') . ' ' . ($folder->status ?? '')), $this->dateValue($folder, ['updated_at', 'created_at']), 'briefcase', 'cyan', $folder)));
        $invoices->each(fn($invoice) => $items->push($this->timelineItem('invoice', 'Rechnung #' . ($invoice->invoice_no ?? $invoice->invoice_number ?? $invoice->id), trim(($invoice->status ?? '') . ' ' . ($invoice->total_gross ?? $invoice->total ?? '')), $this->dateValue($invoice, ['invoice_date', 'created_at']), 'credit-card', 'dark', $invoice)));
        $dailyTimes->each(fn($time) => $items->push($this->timelineItem('daily_time', 'Arbeitszeit / Tagesbericht', Str::limit($this->plainText($time->description ?: $time->type ?: ''), 180), $this->dateValue($time, ['report_date', 'created_at']), 'clock', 'teal', $time)));
        $deliveryNotes->each(fn($note) => $items->push($this->timelineItem('delivery_note', 'Lieferschein #' . ($note->delivery_note ?: $note->id), Str::limit($this->plainText($note->description ?: $note->status ?: ''), 180), $this->dateValue($note, ['handover_date', 'order_date', 'created_at']), 'truck', 'amber', $note)));
        $goodsReceipts->each(fn($receipt) => $items->push($this->timelineItem('goods_receipt', 'Wareneingang #' . ($receipt->receipt_no ?? $receipt->id), Str::limit($this->plainText($receipt->description ?? $receipt->status ?? ''), 180), $this->dateValue($receipt, ['accepted_at', 'created_at']), 'package', 'slate', $receipt)));
        $productInfos->each(fn($info) => $items->push($this->timelineItem('installed_product', $info->product_name ?: 'Produktinfo', Str::limit($this->plainText(trim(($info->manufacturer ?? '') . ' ' . ($info->serial_number ?? '') . ' ' . ($info->notes ?? ''))), 180), $this->dateValue($info, ['installation_date', 'purchase_date', 'created_at']), 'cpu', 'blue', $info)));
        $customer->objects->each(fn($object) => $items->push($this->timelineItem('object', $object->display_name, $this->objectAddress($object), $this->dateValue($object, ['project_date', 'request_date', 'created_at']), 'home', 'green', $object)));

        return $items->filter(fn($item) => !empty($item['date']))->sortByDesc('date')->values()->take(300);
    }

    private function timelineItem(string $type, string $title, string $text, $date, string $icon, string $color, $model): array
    {
        return [
            'type' => $type,
            'title' => $title,
            'text' => $text,
            'date' => $date,
            'icon' => $icon,
            'color' => $color,
            'id' => $model->id ?? null,
        ];
    }

    private function optionalModelRows(string $class, callable $callback, $default)
    {
        if (!class_exists($class)) {
            return $default;
        }

        try {
            $model = new $class();
            if (!Schema::hasTable($model->getTable())) {
                return $default;
            }
            return $callback($class::query());
        } catch (\Throwable) {
            return $default;
        }
    }

    private function dateValue($model, array $fields)
    {
        foreach ($fields as $field) {
            if (!empty($model->{$field})) {
                try {
                    return Carbon::parse($model->{$field});
                } catch (\Throwable) {
                    return $model->{$field};
                }
            }
        }
        return $model->created_at ?? null;
    }

    private function decodeJsonList($value): Collection
    {
        if ($value instanceof Collection) {
            return $value->values();
        }
        if (is_array($value)) {
            return collect($value)->values();
        }
        if (!is_string($value) || trim($value) === '') {
            return collect();
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? collect($decoded)->values() : collect();
    }

    public function plainText($value): string
    {
        $text = (string) ($value ?? '');
        $text = preg_replace('/<(br|\/p|\/div|\/li)>/i', "\n", $text);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[ \t]+/', ' ', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        return trim($text);
    }

    public function employeeName($employee): string
    {
        if (!$employee) {
            return 'Unbekannt';
        }
        return trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) ?: ('#' . ($employee->id ?? ''));
    }

    public function objectAddress($object): string
    {
        return trim(($object->object_name ? $object->object_name . ' · ' : '') . ($object->full_address ?: trim(($object->street ?? '') . ', ' . ($object->postcode ?? '') . ' ' . ($object->city ?? '')))) ?: ('Objekt #' . ($object->id ?? ''));
    }
}
