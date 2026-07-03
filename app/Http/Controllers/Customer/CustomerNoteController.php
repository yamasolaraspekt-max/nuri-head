<?php

namespace App\Http\Controllers\Customer;

use App\Events\CustomerNoteChanged;
use App\Http\Controllers\Controller;
use App\Models\CustomerNote;
use App\Models\Employee;
use App\Models\UserRoll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CustomerNoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /* =========================================================================
     |  HTML / VIEW LOADERS
     | ========================================================================= */

    /**
     * Old route support:
     *
     * GET /customer-notes/{customer}/{alternative}/{product}
     *
     * - product = general / 0 / empty => allgemeine Kundennotizen
     * - product = numeric            => product_id, NOT lead_product_list_id
     */
    public function getNotesHtml($customer, $alternative, $product = null)
    {
        $customerId = (int) $customer;
        $alternativeId = (int) $alternative;
        $context = $this->normalizeContextValue($product);

        Log::info('Customer notes: loading old route context', [
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product' => $product,
            'context' => $context,
        ]);

        if ($context['is_general']) {
            return $this->renderNotesHtml(
                customerId: $customerId,
                alternativeId: $alternativeId,
                productId: null,
                leadProductListId: null,
                type: 'general'
            );
        }

        return $this->renderNotesHtml(
            customerId: $customerId,
            alternativeId: $alternativeId,
            productId: $context['id'],
            leadProductListId: null,
            type: 'product'
        );
    }

    /**
     * New correct route:
     *
     * GET /customer-notes/context/{customer}/{alternative}/{product}/{leadProductList?}
     *
     * product         = article_groups.id / product_id
     * leadProductList = lead_product_lists.id
     */
    public function getNotesHtmlByContext($customer, $alternative, $product = null, $leadProductList = null)
    {
        $customerId = (int) $customer;
        $alternativeId = (int) $alternative;
        $productId = $this->nullableInt($product);
        $leadProductListId = $this->nullableInt($leadProductList);

        Log::info('Customer notes: loading full context route', [
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'lead_product_list_id' => $leadProductListId,
        ]);

        if (!$productId && !$leadProductListId) {
            return $this->renderNotesHtml(
                customerId: $customerId,
                alternativeId: $alternativeId,
                productId: null,
                leadProductListId: null,
                type: 'general'
            );
        }

        if ($leadProductListId && !$productId) {
            $productId = $this->productIdFromLeadProductList($leadProductListId);
        }

        return $this->renderNotesHtml(
            customerId: $customerId,
            alternativeId: $alternativeId,
            productId: $productId,
            leadProductListId: $leadProductListId,
            type: 'product'
        );
    }

    protected function renderNotesHtml(
        int $customerId,
        int $alternativeId,
        ?int $productId = null,
        ?int $leadProductListId = null,
        string $type = 'general'
    ) {
        $notes = $this->notesQueryForContext(
            customerId: $customerId,
            alternativeId: $alternativeId,
            productId: $productId,
            leadProductListId: $leadProductListId,
            type: $type
        )
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->get();

        Log::info('Customer notes: rendered notes', [
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'lead_product_list_id' => $leadProductListId,
            'type' => $type,
            'count' => $notes->count(),
        ]);

        return view('admin.new_leads.layouts.notes.note-wrapper', [
            'notes' => $notes,
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $productId,
            'lead_product_list_id' => $leadProductListId,
            'note_type' => $type,
        ]);
    }

    protected function notesQueryForContext(
        int $customerId,
        int $alternativeId,
        ?int $productId = null,
        ?int $leadProductListId = null,
        string $type = 'general'
    ) {
        $query = CustomerNote::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->whereNull('parent_id')
            ->with([
                'creator',
                'replies' => function ($q) {
                    $q->with('creator')->orderBy('created_at', 'asc');
                },
            ]);

        if ($type === 'general') {
            return $query
                ->whereNull('product_id')
                ->whereNull('lead_product_list_id')
                ->where(function ($q) {
                    $q->whereNull('type')
                        ->orWhereIn('type', $this->generalNoteTypes());
                });
        }

        $query->where(function ($q) {
            $q->whereIn('type', $this->productNoteTypes())
                ->orWhereNotNull('product_id')
                ->orWhereNotNull('lead_product_list_id');
        });

        if ($leadProductListId) {
            return $query->where('lead_product_list_id', $leadProductListId);
        }

        if ($productId) {
            return $query->where('product_id', $productId);
        }

        return $query->whereRaw('1 = 0');
    }

    public function edit($id)
    {
        $note = CustomerNote::with(['creator', 'parent'])->findOrFail($id);

        return response()->json(['note' => $note]);
    }

    public function getDeletedNotes($parentId)
    {
        $notes = CustomerNote::onlyTrashed()
            ->with('creator')
            ->where('parent_id', $parentId)
            ->orderByDesc('created_at')
            ->get();

        $html = view('admin.new_leads.layouts.notes.partials.deleted-list', compact('notes'))->render();

        return response()->json(['html' => $html]);
    }

    public function getAllDeletedNotes(Request $request)
    {
        $query = CustomerNote::onlyTrashed()
            ->with([
                'creator',
                'parent' => function ($q) {
                    $q->withTrashed()->select('id', 'description');
                },
            ]);

        if ($request->filled('customer_id')) {
            $query->where('customer_id', (int) $request->customer_id);
        }

        if ($request->filled('alternative_id')) {
            $query->where('alternative_id', (int) $request->alternative_id);
        }

        if ($request->filled('lead_product_list_id')) {
            $query->where('lead_product_list_id', (int) $request->lead_product_list_id);
        } elseif ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->product_id);
        }

        if ($request->filled('type')) {
            $type = $this->normalizeNoteType($request->type, null);
            if ($type) {
                $query->where('type', $type);
            }
        }

        $deletedNotes = $query
            ->orderByDesc('deleted_at')
            ->get();

        $html = view('admin.new_leads.layouts.notes.partials.deleted-all', compact('deletedNotes'))->render();

        return response()->json(['html' => $html]);
    }

    /* =========================================================================
     |  CREATE
     | ========================================================================= */

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable', 'integer'],
            'description' => ['required', 'string'],
            'type' => ['nullable', Rule::in($this->allowedNoteTypes())],
            'color' => ['nullable', 'string', 'max:50'],
            'due_date' => ['nullable', 'date'],
            'stage' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer', 'exists:customer_notes,id'],

            // Kanban stage/sub-stage context. These are optional so the same controller
            // still works for the customer profile and older note forms.
            'lead_stage_id' => ['nullable', 'integer'],
            'lead_stage_key' => ['nullable', 'string', 'max:100'],
            'lead_stage_name' => ['nullable', 'string', 'max:255'],
            'lead_stage_color' => ['nullable', 'string', 'max:50'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer'],
            'lead_stage_sub_stage_name' => ['nullable', 'string', 'max:255'],
            'lead_stage_sub_stage_color' => ['nullable', 'string', 'max:50'],

            // Follow-up (F2): optionaler Abschluss-Dialog. Der Client schickt NUR outcome;
            // follow_up_art wird server-seitig abgeleitet, source_type ist hart 'customer_note'.
            'follow_up_outcome' => ['nullable', Rule::in(['keine', 'nachfass', 'weitere_aufgaben'])],
            'follow_up_next_step' => ['nullable', 'required_if:follow_up_outcome,nachfass,weitere_aufgaben', 'string', 'max:5000'],
            'follow_up_due_date' => ['nullable', 'required_if:follow_up_outcome,nachfass,weitere_aufgaben', 'date'],
            'follow_up_responsible' => ['nullable', 'integer', 'exists:employees,id'],
        ]);

        $employeeId = $this->currentEmployeeId();
        $employeeName = $this->currentEmployeeName();

        $note = DB::transaction(function () use ($data, $employeeId, $employeeName) {
            if (!empty($data['parent_id'])) {
                return $this->createReplyNoteFromStore($data, $employeeId, $employeeName);
            }

            $context = $this->resolveNoteContext($data);
            $stageContext = $this->resolveStageContext($data, $context);
            $type = $this->resolveRequestedType($data, $context, $stageContext);

            $note = new CustomerNote();
            $note->customer_id = (int) $data['customer_id'];
            $note->alternative_id = $context['alternative_id'];
            $note->product_id = $context['product_id'];
            $note->lead_product_list_id = $context['lead_product_list_id'];
            $note->parent_id = null;
            $note->description = $this->sanitizeHtml($data['description']);
            $note->due_date = $data['due_date'] ?? null;
            $note->color = $data['color'] ?? '#cfe09b';
            $note->stage = $stageContext['lead_stage_key'] ?? ($data['stage'] ?? null);
            $note->type = $type;
            $note->order_no = $this->nextOrderNoForScope(
                customerId: (int) $data['customer_id'],
                alternativeId: (int) $context['alternative_id'],
                leadProductListId: $context['lead_product_list_id'],
                productId: $context['product_id'],
                type: $note->type
            );
            $note->created_by = $employeeId;

            $this->applyStageContextToNote($note, $stageContext);

            $this->pushHistory($note, 'created', $employeeId, $employeeName, [
                'info' => $this->createdInfoText($note->type),
                'content_snippet' => Str::limit(strip_tags($note->description), 150),
                'product_id' => $note->product_id,
                'lead_product_list_id' => $note->lead_product_list_id,
                'stage_context' => $stageContext,
            ]);

            $note->save();

            // F2: optionalen Follow-up INNERHALB derselben Transaktion erzeugen
            // -> Notiz + Follow-up entstehen atomar oder gar nicht.
            if (in_array($data['follow_up_outcome'] ?? 'keine', ['nachfass', 'weitere_aufgaben'], true)) {
                $this->createFollowUpFromNote($note, $data, $employeeId);
            }

            return $note->load(['creator', 'replies.creator']);
        });

        $this->logNoteActivity('created', $note->id, $note->customer_id, $note->alternative_id, $note->product_id, [
            'info' => $note->parent_id ? 'Antwort auf Notiz verfasst' : $this->createdInfoText($note->type),
            'parent_note_id' => $note->parent_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'content_snippet' => Str::limit(strip_tags($note->description), 50),
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        $this->broadcastNote($note->fresh(['creator', 'parent']), 'created', [
            'parent_id' => $note->parent_id,
            'product_id' => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        if ($note->parent_id) {
            return response()->json([
                'success' => true,
                'reply' => view('admin.new_leads.layouts.notes.partials.reply', [
                    'reply' => $note,
                ])->render(),
                'note' => $note,
            ]);
        }

        return response()->json([
            'success' => true,
            'html' => view('admin.new_leads.layouts.notes.single-note', [
                'note' => $note,
            ])->render(),
            'note' => $note,
        ]);
    }

    /**
     * F2 Follow-up: erzeugt aus einer Notiz einen Follow-up-personal_task (Termin-Muster
     * verallgemeinert). WIRD NUR INNERHALB der store()-Transaktion aufgerufen (atomar).
     *
     * - naechster Schritt = task_title (auf /home garantiert sichtbar: Focus-Today rendert
     *   den Titel immer, description nur wenn vorhanden) -> die Handlung ist die Ueberschrift.
     * - source_type hart 'customer_note'; follow_up_art server-seitig aus outcome abgeleitet.
     * - next_reminder_at = NULL -> ProcessPersonalTaskScheduler ueberspringt es -> dashboard-only
     *   (kein Mail/Push, Stufe-1-Konvention).
     * - controller_id = [$responsible] als PHP-Array; PersonalTask castet 'controller_id' => 'array'
     *   -> genau einmal JSON-encodiert (kein manuelles json_encode).
     */
    protected function createFollowUpFromNote(CustomerNote $note, array $data, ?int $employeeId): void
    {
        $art = (($data['follow_up_outcome'] ?? null) === 'weitere_aufgaben') ? 'wiederaufnahme' : 'nachfass';

        $responsible = (int) ($data['follow_up_responsible'] ?? 0);
        if ($responsible <= 0) {
            $responsible = (int) $employeeId; // Default = Ersteller
        }

        $task = \App\Models\PersonalTask::create([
            'task_title'           => Str::limit(trim(strip_tags((string) $data['follow_up_next_step'])), 120, '') ?: 'Follow-up',
            'description'          => 'Aus Notiz: ' . Str::limit(strip_tags((string) $note->description), 200),
            'task_status'          => 'open',
            'type'                 => 'follow_up',
            'follow_up_art'        => $art,
            'source_type'          => 'customer_note',
            'source_id'            => $note->id,
            'due_date'             => $data['follow_up_due_date'],
            'reminder_date'        => $data['follow_up_due_date'],
            'next_reminder_at'     => null,
            'assigned_by'          => (string) $employeeId,
            'customer_id'          => $note->customer_id,
            'alternative_id'       => $note->alternative_id,
            'product_id'           => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'controller_id'        => [$responsible],
        ]);

        \App\Models\EmployeesPersonalTask::create([
            'employee_id' => $responsible,
            'task_id'     => $task->id,
            'status'      => 'send',
        ]);
    }

    protected function createReplyNoteFromStore(array $data, ?int $employeeId, string $employeeName): CustomerNote
    {
        $parent = CustomerNote::withTrashed()->findOrFail($data['parent_id']);

        if ($parent->trashed()) {
            abort(422, 'Sie können nicht auf eine gelöschte Notiz antworten.');
        }

        $reply = new CustomerNote();
        $reply->customer_id = $parent->customer_id;
        $reply->alternative_id = $parent->alternative_id;
        $reply->product_id = $parent->product_id;
        $reply->lead_product_list_id = $parent->lead_product_list_id;
        $reply->parent_id = $parent->id;
        $reply->description = $this->sanitizeHtml($data['description']);
        $reply->due_date = $data['due_date'] ?? null;
        $reply->color = $data['color'] ?? '#e9ecef';
        $reply->stage = $parent->stage ?: ($data['stage'] ?? null);
        $reply->type = $parent->type ?: ($parent->lead_product_list_id || $parent->product_id ? 'product' : 'general');
        $reply->order_no = $this->nextOrderNoForScope(
            customerId: (int) $parent->customer_id,
            alternativeId: (int) $parent->alternative_id,
            leadProductListId: $parent->lead_product_list_id,
            productId: $parent->product_id,
            type: $reply->type
        );
        $reply->created_by = $employeeId;

        // Replies inherit their parent stage/sub-stage context.
        $this->applyStageContextToNote($reply, $this->stageContextFromNote($parent));

        $this->pushHistory($reply, 'created', $employeeId, $employeeName, [
            'info' => 'Antwort auf Notiz erstellt',
            'parent_note_id' => $parent->id,
            'product_id' => $reply->product_id,
            'lead_product_list_id' => $reply->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($parent),
            'content' => Str::limit(strip_tags($reply->description), 150),
        ]);

        $reply->save();
        $parent->touch();

        return $reply->load(['creator', 'parent']);
    }

    public function reply(Request $request, $noteId)
    {
        $parent = CustomerNote::whereNull('deleted_at')->findOrFail($noteId);

        $data = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
        ]);

        $employeeId = $this->currentEmployeeId();
        $employeeName = $this->currentEmployeeName();

        $reply = DB::transaction(function () use ($parent, $data, $employeeId, $employeeName) {
            $reply = new CustomerNote();

            $reply->customer_id = $parent->customer_id;
            $reply->alternative_id = $parent->alternative_id;
            $reply->product_id = $parent->product_id;
            $reply->lead_product_list_id = $parent->lead_product_list_id;
            $reply->parent_id = $parent->id;
            $reply->description = $this->sanitizeHtml($data['description']);
            $reply->due_date = null;
            $reply->color = '#e9ecef';
            $reply->stage = $parent->stage;
            $reply->type = $parent->type ?: ($parent->lead_product_list_id || $parent->product_id ? 'product' : 'general');
            $reply->order_no = $this->nextOrderNoForScope(
                customerId: (int) $parent->customer_id,
                alternativeId: (int) $parent->alternative_id,
                leadProductListId: $parent->lead_product_list_id,
                productId: $parent->product_id,
                type: $reply->type
            );
            $reply->created_by = $employeeId;

            $this->applyStageContextToNote($reply, $this->stageContextFromNote($parent));

            $this->pushHistory($reply, 'created', $employeeId, $employeeName, [
                'info' => 'Antwort auf Notiz erstellt',
                'parent_note_id' => $parent->id,
                'product_id' => $reply->product_id,
                'lead_product_list_id' => $reply->lead_product_list_id,
                'stage_context' => $this->stageContextFromNote($parent),
                'content' => Str::limit(strip_tags($reply->description), 150),
            ]);

            $reply->save();
            $parent->touch();

            return $reply->load(['creator', 'parent']);
        });

        $this->logNoteActivity('created', $reply->id, $reply->customer_id, $reply->alternative_id, $reply->product_id, [
            'info' => 'Antwort auf Notiz verfasst',
            'parent_note_id' => $reply->parent_id,
            'lead_product_list_id' => $reply->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($reply),
            'content_snippet' => Str::limit(strip_tags($reply->description), 50),
        ]);

        $this->broadcastNote($reply->fresh(['creator', 'parent']), 'created', [
            'parent_id' => $reply->parent_id,
            'product_id' => $reply->product_id,
            'lead_product_list_id' => $reply->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($reply),
        ]);

        return response()->json([
            'success' => true,
            'reply' => view('admin.new_leads.layouts.notes.partials.reply', [
                'reply' => $reply,
            ])->render(),
            'note' => $reply,
        ]);
    }

    public function processStore(Request $request)
    {
        return $this->store($request);
    }

    public function saveAjax(Request $request)
    {
        return $this->store($request);
    }

    public function noteStore(Request $request)
    {
        return $this->store($request);
    }

    /* =========================================================================
     |  UPDATE
     | ========================================================================= */

    public function update(Request $request, $id)
    {
        $note = CustomerNote::findOrFail($id);

        return $this->performUpdate($request, $note, false);
    }

    public function updateReply(Request $request, $id)
    {
        $note = CustomerNote::findOrFail($id);

        return $this->performUpdate($request, $note, true);
    }

    public function inlineUpdate(Request $request, $id)
    {
        $note = CustomerNote::findOrFail($id);
        $this->authorizeUpdateOrFail($note);

        $data = $request->validate([
            'field' => ['required', Rule::in(['description', 'color', 'stage', 'due_date'])],
            'value' => ['nullable'],
        ]);

        $field = $data['field'];
        $value = $data['value'];

        $oldValue = $note->{$field};

        if ($field === 'description') {
            $value = $this->sanitizeHtml((string) $value);
        }

        if ($field === 'due_date' && $value === '') {
            $value = null;
        }

        $note->{$field} = $value;

        $employeeId = $this->currentEmployeeId();
        $employeeName = $this->currentEmployeeName();

        $this->pushHistory($note, 'updated', $employeeId, $employeeName, [
            'info' => "Notiz-Feld '{$field}' aktualisiert",
            $field => [
                'from' => $oldValue,
                'to' => $value,
            ],
        ]);

        $note->save();
        $note->load(['creator', 'parent']);

        $this->logNoteActivity('updated', $note->id, $note->customer_id, $note->alternative_id, $note->product_id, [
            'info' => "Notiz-Feld '{$field}' aktualisiert",
            $field => [
                'from' => $oldValue,
                'to' => $value,
            ],
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        $this->broadcastNote($note, 'updated', [
            'field' => $field,
            'product_id' => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        return response()->json([
            'success' => true,
            'note' => $note,
        ]);
    }

    protected function performUpdate(Request $request, CustomerNote $note, bool $isReply = false)
    {
        $this->authorizeUpdateOrFail($note);

        $data = $request->validate([
            'description' => ['nullable', 'string', $isReply ? 'max:1000' : 'max:50000'],
            'due_date' => ['nullable', 'date'],
            'color' => ['nullable', 'regex:/^#([A-Fa-f0-9]{3}|[A-Fa-f0-9]{6})$/'],
            'order_no' => ['nullable', 'integer', 'min:0'],
            'stage' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', Rule::in($this->allowedNoteTypes())],

            // Optional stage/sub-stage context update.
            'lead_stage_id' => ['nullable', 'integer'],
            'lead_stage_key' => ['nullable', 'string', 'max:100'],
            'lead_stage_name' => ['nullable', 'string', 'max:255'],
            'lead_stage_color' => ['nullable', 'string', 'max:50'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer'],
            'lead_stage_sub_stage_name' => ['nullable', 'string', 'max:255'],
            'lead_stage_sub_stage_color' => ['nullable', 'string', 'max:50'],
        ]);

        $employeeId = $this->currentEmployeeId();
        $employeeName = $this->currentEmployeeName();

        $changes = [];
        $oldContent = $note->description;

        if ($request->has('description')) {
            $newDescription = $this->sanitizeHtml($data['description'] ?? '');

            if ((string) $note->description !== (string) $newDescription) {
                $changes['description'] = [
                    'from' => Str::limit(strip_tags((string) $note->description), 100),
                    'to' => Str::limit(strip_tags((string) $newDescription), 100),
                ];
                $note->description = $newDescription;
            }
        }

        if ($request->has('due_date')) {
            $old = $note->due_date ? $note->due_date->format('Y-m-d') : null;
            $new = $data['due_date'] ?? null;

            if ($old !== $new) {
                $changes['due_date'] = ['from' => $old, 'to' => $new];
                $note->due_date = $new;
            }
        }

        if ($request->has('color')) {
            $old = $note->color;
            $new = $data['color'] ?? '#cfe09b';

            if ((string) $old !== (string) $new) {
                $changes['color'] = ['from' => $old, 'to' => $new];
                $note->color = $new;
            }
        }

        if ($request->has('order_no')) {
            $old = (int) $note->order_no;
            $new = (int) ($data['order_no'] ?? 0);

            if ($old !== $new) {
                $changes['order_no'] = ['from' => $old, 'to' => $new];
                $note->order_no = $new;
            }
        }

        if ($request->has('stage')) {
            $old = $note->stage;
            $new = $data['stage'] ?? null;

            if ((string) $old !== (string) $new) {
                $changes['stage'] = ['from' => $old, 'to' => $new];
                $note->stage = $new;
            }
        }

        if ($request->has('type')) {
            $old = $note->type;
            $fallback = $note->lead_product_list_id || $note->product_id ? 'product' : 'general';
            $new = $this->normalizeNoteType($data['type'] ?? null, $fallback);

            if ((string) $old !== (string) $new) {
                $changes['type'] = ['from' => $old, 'to' => $new];
                $note->type = $new;
            }
        }

        $stagePayloadExists = collect([
            'lead_stage_id',
            'lead_stage_key',
            'lead_stage_name',
            'lead_stage_color',
            'lead_stage_sub_stage_id',
            'lead_stage_sub_stage_name',
            'lead_stage_sub_stage_color',
        ])->contains(fn($key) => $request->has($key));

        if ($stagePayloadExists) {
            $oldStage = $this->stageContextFromNote($note);
            $newStage = $this->resolveStageContext($data, [
                'lead_product_list_id' => $note->lead_product_list_id,
                'product_id' => $note->product_id,
                'alternative_id' => $note->alternative_id,
            ]);

            $this->applyStageContextToNote($note, $newStage);

            if ($oldStage !== $this->stageContextFromNote($note)) {
                $changes['stage_context'] = [
                    'from' => $oldStage,
                    'to' => $this->stageContextFromNote($note),
                ];
            }
        }

        if (!empty($changes)) {
            $this->pushHistory($note, 'updated', $employeeId, $employeeName, [
                'info' => $isReply ? 'Antwort bearbeitet' : 'Notiz aktualisiert',
                'changes' => $changes,
            ]);

            $note->save();
            $note->load(['creator', 'parent']);

            $this->logNoteActivity('updated', $note->id, $note->customer_id, $note->alternative_id, $note->product_id, [
                'description' => isset($changes['description'])
                    ? [
                        'from' => Str::limit(strip_tags((string) $oldContent), 30),
                        'to' => Str::limit(strip_tags((string) $note->description), 30),
                    ]
                    : null,
                'info' => $isReply ? 'Antwort bearbeitet' : 'Notiz inhaltlich bearbeitet',
                'changes' => $changes,
                'lead_product_list_id' => $note->lead_product_list_id,
                'stage_context' => $this->stageContextFromNote($note),
            ]);

            $this->broadcastNote($note, 'updated', [
                'is_reply' => $isReply,
                'product_id' => $note->product_id,
                'lead_product_list_id' => $note->lead_product_list_id,
                'stage_context' => $this->stageContextFromNote($note),
            ]);
        }

        return response()->json([
            'success' => true,
            'updated_description' => $note->description,
            'note' => $note,
        ]);
    }

    /* =========================================================================
     |  DELETE / RESTORE / FORCE DELETE
     | ========================================================================= */

    public function destroy(CustomerNote $note)
    {
        return $this->performDelete($note, 'Notiz in Papierkorb verschoben');
    }

    public function delete($id)
    {
        $note = CustomerNote::find($id);

        if (!$note) {
            return response()->json(['status' => 'deleted']);
        }

        return $this->performDelete($note, 'Notiz in Papierkorb verschoben');
    }

    public function deleteReply($id)
    {
        $reply = CustomerNote::findOrFail($id);

        return $this->performDelete($reply, 'Antwort gelöscht');
    }

    protected function performDelete(CustomerNote $note, string $infoText)
    {
        $this->authorizeUpdateOrFail($note);

        $employeeId = $this->currentEmployeeId();
        $employeeName = $this->currentEmployeeName();

        $this->pushHistory($note, 'deleted', $employeeId, $employeeName, [
            'info' => $infoText,
            'product_id' => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        $note->save();
        $note->delete();

        $this->logNoteActivity('deleted', $note->id, $note->customer_id, $note->alternative_id, $note->product_id, [
            'info' => $infoText,
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        $this->broadcastNote($note->fresh(['creator', 'parent']), 'deleted', [
            'deleted_id' => $note->id,
            'product_id' => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        return response()->json([
            'success' => true,
            'status' => 'deleted',
        ]);
    }

    public function restore($id)
    {
        $note = CustomerNote::withTrashed()->findOrFail($id);

        $note->restore();

        $this->pushHistory($note, 'restored', $this->currentEmployeeId(), $this->currentEmployeeName(), [
            'info' => 'Notiz wiederhergestellt',
            'product_id' => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        $note->save();
        $note->load(['creator', 'parent']);

        $this->logNoteActivity('restored', $note->id, $note->customer_id, $note->alternative_id, $note->product_id, [
            'info' => 'Notiz wiederhergestellt',
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        $this->broadcastNote($note, 'restored', [
            'product_id' => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        return response()->json(['success' => true]);
    }

    public function forceDelete(Request $request, $id)
    {
        $username = $request->user;
        $password = $request->pass;

        $employee = Employee::where('name', $username)->first();

        if (!$employee || !Hash::check($password, $employee->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Ungültige Zugangsdaten.',
            ]);
        }

        $adminRole = UserRoll::where('user_id', $employee->id)
            ->where('item_id', 'Administrator')
            ->where('is_delete', '1')
            ->first();

        if (!$adminRole) {
            return response()->json([
                'success' => false,
                'message' => 'Keine Administratorrechte.',
            ]);
        }

        $note = CustomerNote::withTrashed()->findOrFail($id);

        $this->logNoteActivity('force_deleted', $note->id, $note->customer_id, $note->alternative_id, $note->product_id, [
            'info' => 'Notiz endgültig gelöscht durch Admin',
            'lead_product_list_id' => $note->lead_product_list_id,
            'stage_context' => $this->stageContextFromNote($note),
        ]);

        $payload = [
            'id' => $note->id,
            'customer_id' => $note->customer_id,
            'alternative_id' => $note->alternative_id,
            'product_id' => $note->product_id,
            'lead_product_list_id' => $note->lead_product_list_id,
            'parent_id' => $note->parent_id,
            'type' => $note->type,
            ...$this->stageContextFromNote($note),
        ];

        $note->forceDelete();

        $this->broadcastRawPayload($payload, 'force_deleted');

        return response()->json([
            'success' => true,
            'message' => 'Notiz dauerhaft gelöscht.',
        ]);
    }

    /* =========================================================================
     |  REORDER / READ / INDEX
     | ========================================================================= */

    public function reorder(Request $request)
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*.id' => ['required', 'integer', 'exists:customer_notes,id'],
            'order.*.order_no' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            foreach ($data['order'] as $item) {
                $note = CustomerNote::find($item['id']);

                if (!$note) {
                    continue;
                }

                $this->authorizeUpdateOrFail($note);

                $oldOrder = (int) $note->order_no;
                $newOrder = (int) $item['order_no'];

                if ($oldOrder !== $newOrder) {
                    $note->order_no = $newOrder;

                    $this->pushHistory($note, 'reordered', $this->currentEmployeeId(), $this->currentEmployeeName(), [
                        'info' => 'Reihenfolge geändert',
                        'order_no' => ['from' => $oldOrder, 'to' => $newOrder],
                        'stage_context' => $this->stageContextFromNote($note),
                    ]);

                    $note->save();

                    $this->broadcastNote($note->fresh(['creator', 'parent']), 'reordered', [
                        'order_no' => $newOrder,
                        'product_id' => $note->product_id,
                        'lead_product_list_id' => $note->lead_product_list_id,
                        'stage_context' => $this->stageContextFromNote($note),
                    ]);
                }
            }
        });

        return response()->json(['status' => 'ok']);
    }

    public function noteIndex(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer'],
            'lead_product_list_id' => ['nullable'],
            'type' => ['nullable', Rule::in($this->allowedNoteTypes())],
            'lead_stage_id' => ['nullable', 'integer'],
            'lead_stage_key' => ['nullable', 'string', 'max:100'],
            'lead_stage_sub_stage_id' => ['nullable', 'integer'],
            'q' => ['nullable', 'string', 'max:2000'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($data['per_page'] ?? 20);

        $customerId = (int) $data['customer_id'];
        $alternativeId = $this->nullableInt($data['alternative_id'] ?? null);
        $productId = $this->nullableInt($data['product_id'] ?? null);
        $leadProductListRaw = $data['lead_product_list_id'] ?? null;
        $type = $this->normalizeNoteType($data['type'] ?? null, null);

        $query = CustomerNote::query()
            ->with(['author:id,name,lastname,image', 'creator:id,name,lastname,image', 'parent:id,description'])
            ->where('customer_id', $customerId);

        if ($alternativeId) {
            $query->where('alternative_id', $alternativeId);
        }

        if ($leadProductListRaw === 'general' || $type === 'general') {
            $query
                ->whereNull('product_id')
                ->whereNull('lead_product_list_id')
                ->where(function ($q) {
                    $q->whereNull('type')
                        ->orWhereIn('type', $this->generalNoteTypes());
                });
        } elseif ($this->nullableInt($leadProductListRaw)) {
            $query->where('lead_product_list_id', $this->nullableInt($leadProductListRaw));
        } elseif ($productId) {
            $query->where('product_id', $productId);
        }

        if ($type && $type !== 'general') {
            $query->where('type', $type);
        }

        if ($this->hasNoteColumn('lead_stage_id') && !empty($data['lead_stage_id'])) {
            $query->where('lead_stage_id', (int) $data['lead_stage_id']);
        }

        if ($this->hasNoteColumn('lead_stage_key') && !empty($data['lead_stage_key'])) {
            $query->where('lead_stage_key', (string) $data['lead_stage_key']);
        }

        if ($this->hasNoteColumn('lead_stage_sub_stage_id') && !empty($data['lead_stage_sub_stage_id'])) {
            $query->where('lead_stage_sub_stage_id', (int) $data['lead_stage_sub_stage_id']);
        }

        if ($request->filled('q')) {
            $q = trim($data['q']);

            $query->where(function ($qq) use ($q) {
                $qq->where('description', 'like', '%' . $q . '%')
                    ->orWhere('stage', 'like', '%' . $q . '%')
                    ->orWhere('type', 'like', '%' . $q . '%');

                foreach (['lead_stage_name', 'lead_stage_key', 'lead_stage_sub_stage_name'] as $column) {
                    if ($this->hasNoteColumn($column)) {
                        $qq->orWhere($column, 'like', '%' . $q . '%');
                    }
                }
            });
        }

        $query->orderByDesc('created_at');

        $paginator = $query->paginate($perPage);
        $latest = (clone $query)->first();

        return response()->json([
            'notes' => $paginator->items(),
            'latest' => $latest,
            'total' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }

    public function markAsRead($id)
    {
        $note = CustomerNote::findOrFail($id);

        $employeeId = $this->currentEmployeeId();
        $employeeName = $this->currentEmployeeName();

        $readBy = collect($note->read_by ?? []);

        $alreadyRead = $readBy->contains(function ($item) use ($employeeId) {
            return (int) ($item['user_id'] ?? 0) === (int) $employeeId;
        });

        if (!$alreadyRead) {
            $readBy->push([
                'user_id' => $employeeId,
                'user_name' => $employeeName,
                'read_at' => now()->toDateTimeString(),
            ]);

            $note->read_by = $readBy->values()->all();
            $note->last_read_at = now();

            $this->pushHistory($note, 'read', $employeeId, $employeeName, [
                'info' => 'Notiz gelesen',
                'stage_context' => $this->stageContextFromNote($note),
            ]);

            $note->save();
            $note->load(['creator', 'parent']);

            $this->broadcastNote($note, 'read', [
                'product_id' => $note->product_id,
                'lead_product_list_id' => $note->lead_product_list_id,
                'stage_context' => $this->stageContextFromNote($note),
            ]);
        }

        return response()->json([
            'success' => true,
            'read_by' => $note->read_by,
        ]);
    }

    /* =========================================================================
     |  CONTEXT HELPERS
     | ========================================================================= */

    protected function resolveNoteContext(array $data): array
    {
        $customerId = (int) $data['customer_id'];
        $alternativeId = $this->nullableInt($data['alternative_id'] ?? null);
        $productId = $this->nullableInt($data['product_id'] ?? null);
        $leadProductListId = $this->nullableInt($data['lead_product_list_id'] ?? null);

        if ($leadProductListId) {
            $leadProduct = DB::table('lead_product_lists')
                ->where('id', $leadProductListId)
                ->first();

            if ($leadProduct) {
                $productId = $productId ?: $this->nullableInt($leadProduct->product_id ?? null);
                $alternativeId = $alternativeId ?: $this->nullableInt($leadProduct->alternative_id ?? null);
            }
        }

        if (!$leadProductListId && $productId && $alternativeId) {
            $leadProduct = DB::table('lead_product_lists')
                ->where('customer_id', $customerId)
                ->where('alternative_id', $alternativeId)
                ->where('product_id', $productId)
                ->orderByDesc('id')
                ->first();

            if ($leadProduct) {
                $leadProductListId = (int) $leadProduct->id;
            }
        }

        $hasProductContext = (bool) ($productId || $leadProductListId);

        return [
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,
            'product_id' => $hasProductContext ? $productId : null,
            'lead_product_list_id' => $hasProductContext ? $leadProductListId : null,
            'type' => $hasProductContext ? 'product' : 'general',
        ];
    }

    protected function resolveStageContext(array $data, array $noteContext = []): array
    {
        $leadProductListId = $this->nullableInt($noteContext['lead_product_list_id'] ?? ($data['lead_product_list_id'] ?? null));

        $stage = [
            'lead_stage_id' => $this->nullableInt($data['lead_stage_id'] ?? null),
            'lead_stage_key' => $this->nullableString($data['lead_stage_key'] ?? ($data['stage'] ?? null)),
            'lead_stage_name' => $this->nullableString($data['lead_stage_name'] ?? null),
            'lead_stage_color' => $this->nullableString($data['lead_stage_color'] ?? null),
            'lead_stage_sub_stage_id' => $this->nullableInt($data['lead_stage_sub_stage_id'] ?? null),
            'lead_stage_sub_stage_name' => $this->nullableString($data['lead_stage_sub_stage_name'] ?? null),
            'lead_stage_sub_stage_color' => $this->nullableString($data['lead_stage_sub_stage_color'] ?? null),
        ];

        if ($leadProductListId && (!$stage['lead_stage_key'] || !$stage['lead_stage_sub_stage_id'])) {
            $leadProduct = DB::table('lead_product_lists')
                ->where('id', $leadProductListId)
                ->first();

            if ($leadProduct) {
                $stage['lead_stage_key'] = $stage['lead_stage_key'] ?: $this->normalizeStageKey($leadProduct->status ?? null);

                if (!$stage['lead_stage_sub_stage_id'] && Schema::hasColumn('lead_product_lists', 'lead_stage_sub_stage_id')) {
                    $stage['lead_stage_sub_stage_id'] = $this->nullableInt($leadProduct->lead_stage_sub_stage_id ?? null);
                }
            }
        }

        if (($stage['lead_stage_key'] || $stage['lead_stage_id']) && Schema::hasTable('lead_stages')) {
            $stageRow = DB::table('lead_stages')
                ->when($stage['lead_stage_id'], fn($q) => $q->where('id', $stage['lead_stage_id']))
                ->when(!$stage['lead_stage_id'] && $stage['lead_stage_key'], fn($q) => $q->whereRaw('LOWER(`key`) = ?', [strtolower($stage['lead_stage_key'])]))
                ->first();

            if ($stageRow) {
                $stage['lead_stage_id'] = $stage['lead_stage_id'] ?: (int) $stageRow->id;
                $stage['lead_stage_key'] = $stage['lead_stage_key'] ?: (string) ($stageRow->key ?? '');
                $stage['lead_stage_name'] = $stage['lead_stage_name'] ?: (string) ($stageRow->name ?? '');
                $stage['lead_stage_color'] = $stage['lead_stage_color'] ?: (string) ($stageRow->color ?? '');
            }
        }

        if ($stage['lead_stage_sub_stage_id'] && Schema::hasTable('lead_stage_sub_stages')) {
            $subRow = DB::table('lead_stage_sub_stages')
                ->where('id', $stage['lead_stage_sub_stage_id'])
                ->first();

            if ($subRow) {
                $stage['lead_stage_sub_stage_name'] = $stage['lead_stage_sub_stage_name'] ?: (string) ($subRow->name ?? '');
                $stage['lead_stage_sub_stage_color'] = $stage['lead_stage_sub_stage_color'] ?: (string) ($subRow->color ?? '');

                if (!$stage['lead_stage_id'] && !empty($subRow->lead_stage_id)) {
                    $stage['lead_stage_id'] = (int) $subRow->lead_stage_id;
                }
            }
        }

        return $stage;
    }

    protected function applyStageContextToNote(CustomerNote $note, array $stageContext): void
    {
        foreach ($this->stageContextColumns() as $column) {
            if ($this->hasNoteColumn($column)) {
                $note->{$column} = $stageContext[$column] ?? null;
            }
        }

        if (!empty($stageContext['lead_stage_key'])) {
            $note->stage = $stageContext['lead_stage_key'];
        }
    }

    protected function stageContextFromNote(?CustomerNote $note): array
    {
        if (!$note) {
            return $this->emptyStageContext();
        }

        $context = [];
        foreach ($this->stageContextColumns() as $column) {
            $context[$column] = $this->hasNoteColumn($column) ? ($note->{$column} ?? null) : null;
        }

        $context['lead_stage_key'] = $context['lead_stage_key'] ?: ($note->stage ?? null);

        return $context;
    }

    protected function emptyStageContext(): array
    {
        return array_fill_keys($this->stageContextColumns(), null);
    }

    protected function stageContextColumns(): array
    {
        return [
            'lead_stage_id',
            'lead_stage_key',
            'lead_stage_name',
            'lead_stage_color',
            'lead_stage_sub_stage_id',
            'lead_stage_sub_stage_name',
            'lead_stage_sub_stage_color',
        ];
    }

    protected function resolveRequestedType(array $data, array $context, array $stageContext): string
    {
        $requested = $this->normalizeNoteType($data['type'] ?? null, null);

        if ($requested) {
            return $requested;
        }

        if (!empty($stageContext['lead_stage_sub_stage_id'])) {
            return 'sub_stage_note';
        }

        if (!empty($stageContext['lead_stage_id']) || !empty($stageContext['lead_stage_key'])) {
            return 'stage_note';
        }

        return $context['type'] ?? 'general';
    }

    protected function normalizeContextValue($value): array
    {
        $raw = is_string($value) ? trim($value) : $value;

        if ($raw === null || $raw === '' || $raw === '0' || $raw === 0 || $raw === 'general' || $raw === 'dashboard') {
            return [
                'is_general' => true,
                'id' => null,
            ];
        }

        return [
            'is_general' => false,
            'id' => (int) $raw,
        ];
    }

    protected function nullableInt($value): ?int
    {
        if ($value === null || $value === '' || $value === 'null' || $value === 'undefined' || $value === 'general' || $value === '0' || $value === 0) {
            return null;
        }

        return (int) $value;
    }

    protected function nullableString($value): ?string
    {
        if ($value === null || $value === '' || $value === 'null' || $value === 'undefined') {
            return null;
        }

        return trim((string) $value) ?: null;
    }

    protected function productIdFromLeadProductList(int $leadProductListId): ?int
    {
        return $this->nullableInt(
            DB::table('lead_product_lists')
                ->where('id', $leadProductListId)
                ->value('product_id')
        );
    }

    protected function normalizeStageKey($stage): ?string
    {
        $stage = strtolower(trim((string) $stage));

        if ($stage === '' || $stage === 'open' || $stage === 'new' || $stage === 'neue') {
            return 'lead';
        }

        return $stage;
    }

    /* =========================================================================
     |  NOTE TYPE HELPERS
     | ========================================================================= */

    protected function allowedNoteTypes(): array
    {
        return [
            'product',
            'general',
            'customer',
            'internal',
            'stage_note',
            'sub_stage_note',
        ];
    }

    protected function productNoteTypes(): array
    {
        return [
            'product',
            'stage_note',
            'sub_stage_note',
        ];
    }

    protected function generalNoteTypes(): array
    {
        return [
            'general',
            'customer',
            'internal',
        ];
    }

    protected function normalizeNoteType($type, ?string $fallback = 'general'): ?string
    {
        $type = is_string($type) ? trim($type) : $type;

        if ($type === null || $type === '') {
            return $fallback;
        }

        if (in_array($type, $this->allowedNoteTypes(), true)) {
            return $type;
        }

        return $fallback;
    }

    protected function createdInfoText(?string $type): string
    {
        return match ($type) {
            'stage_note' => 'Neue Hauptphasen-Notiz erstellt',
            'sub_stage_note' => 'Neue Unterphasen-Notiz erstellt',
            'product' => 'Neue Produkt-Notiz erstellt',
            'internal' => 'Neue interne Notiz erstellt',
            default => 'Neue allgemeine Notiz erstellt',
        };
    }

    /* =========================================================================
     |  GENERAL HELPERS
     | ========================================================================= */

    protected function currentEmployeeId(): ?int
    {
        return Auth::check() ? (int) Auth::user()->name : null;
    }

    protected function currentEmployeeName(): string
    {
        $employeeId = $this->currentEmployeeId();

        if (!$employeeId) {
            return 'System';
        }

        $employee = Employee::find($employeeId);

        if (!$employee) {
            return 'Unbekannt';
        }

        return trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
    }

    protected function sanitizeHtml(?string $html): string
    {
        $html = (string) $html;

        $html = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('/\son\w+="[^"]*"/i', '', $html);
        $html = preg_replace("/\son\w+='[^']*'/i", '', $html);
        $html = preg_replace('/\s(href|src)\s*=\s*"(javascript:[^"]*)"/i', ' $1="#"', $html);
        $html = preg_replace("/\s(href|src)\s*=\s*'(javascript:[^']*)'/i", " $1='#'", $html);

        return trim($html);
    }

    protected function authorizeUpdateOrFail(CustomerNote $note): void
    {
        $employeeId = $this->currentEmployeeId();

        $isCreator = $employeeId !== null && (int) $note->created_by === (int) $employeeId;

        if (!$isCreator) {
            abort(403, 'Sie dürfen diese Notiz nicht bearbeiten/löschen.');
        }
    }

    protected function nextOrderNoForScope(
        int $customerId,
        int $alternativeId,
        $leadProductListId = null,
        $productId = null,
        ?string $type = 'general'
    ): int {
        $query = CustomerNote::query()
            ->where('customer_id', $customerId)
            ->where('alternative_id', $alternativeId)
            ->whereNull('parent_id');

        if (!empty($leadProductListId)) {
            $query->where('lead_product_list_id', $leadProductListId);
        } elseif (!empty($productId)) {
            $query->where('product_id', $productId);
        } else {
            $query
                ->whereNull('product_id')
                ->whereNull('lead_product_list_id')
                ->where(function ($q) use ($type) {
                    $q->whereNull('type')
                        ->orWhere('type', $type ?: 'general');
                });
        }

        return (int) $query->max('order_no') + 1;
    }

    protected function pushHistory(CustomerNote $note, string $action, ?int $userId, string $userName, array $meta = []): void
    {
        $history = $note->history;

        if (is_string($history)) {
            $decoded = json_decode($history, true);
            $history = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($history)) {
            $history = [];
        }

        $history[] = [
            'action' => $action,
            'at' => now()->toDateTimeString(),
            'user_id' => $userId,
            'user_name' => $userName,
            'meta' => $meta,
        ];

        $note->history = $history;
    }

    protected function broadcastNote(?CustomerNote $note, string $action, array $extra = []): void
    {
        if (!$note) {
            return;
        }

        $extra = array_merge([
            'stage_context' => $this->stageContextFromNote($note),
        ], $extra);

        try {
            broadcast(new CustomerNoteChanged($note, $action, $extra))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('CustomerNoteChanged broadcast failed', [
                'action' => $action,
                'note_id' => $note->id ?? null,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function broadcastRawPayload(array $payload, string $action, array $extra = []): void
    {
        try {
            $fakeNote = new CustomerNote($payload);
            $fakeNote->exists = true;

            $extra = array_merge([
                'stage_context' => array_intersect_key($payload, array_flip($this->stageContextColumns())),
            ], $extra);

            broadcast(new CustomerNoteChanged($fakeNote, $action, $extra))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('CustomerNoteChanged raw broadcast failed', [
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function logNoteActivity($event, $noteId, $leadId, $altId, $prodId, $changes = [])
    {
        $userName = 'System';

        if (Auth::check()) {
            $employeeId = (int) Auth::user()->name;
            $employee = DB::table('employees')->where('id', $employeeId)->first();

            if ($employee) {
                $userName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
            }
        }

        DB::table('lead_activity_logs')->insert([
            'new_leads_id' => $leadId,
            'alternative_id' => $altId,
            'product_id' => $prodId,
            'user_id' => Auth::id(),
            'user_name' => $userName,
            'event_type' => $event,
            'model_type' => CustomerNote::class,
            'model_id' => $noteId,
            'changes' => json_encode($changes, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function hasNoteColumn(string $column): bool
    {
        static $cache = [];

        if (!array_key_exists($column, $cache)) {
            $cache[$column] = Schema::hasColumn('customer_notes', $column);
        }

        return $cache[$column];
    }
}
