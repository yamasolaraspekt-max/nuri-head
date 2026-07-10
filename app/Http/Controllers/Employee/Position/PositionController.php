<?php
namespace App\Http\Controllers\Employee\Position;
use App\Http\Controllers\Controller;

use App\Models\Position;
use App\Models\PositionQualification;
use Illuminate\Http\Request;
use App\Models\PositionQualificationHierarchy;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    public function __construct() {
        // MASTER-01 P1-IDOR: Personal-Rollen-Gate (permission:Employee)
        $this->middleware('permission:Employee,delete')->only(['destroyAjax', 'qualificationDestroy']);
        $this->middleware('permission:Employee,update')->only(['updateAjax']); $this->middleware('auth'); }

    public function index(Request $request)
    {
        // main page: tabs + containers; data loaded via AJAX
        $quals = PositionQualification::query()
            ->orderBy('sort_order')->orderBy('id')
            ->get(['id','name','default_price','status']);

        return view('admin.employee.position.position', [
            'quals' => $quals
        ]);
    }

    public function hierarchyBoard(Request $request)
{
    $quals = PositionQualification::query()
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['id', 'name', 'default_price', 'sort_order', 'status']);

    $rules = PositionQualificationHierarchy::query()
        ->get()
        ->keyBy(function ($row) {
            return $row->performer_qualification_id . '_' . $row->required_qualification_id;
        });

    $html = view('admin.employee.position.partials._hierarchy_matrix', [
        'quals' => $quals,
        'rules' => $rules,
    ])->render();

    return response()->json([
        'ok' => true,
        'html' => $html,
    ]);
}

public function hierarchySave(Request $request)
{
    $data = $request->validate([
        'rules' => ['required', 'array'],
        'rules.*.performer_qualification_id' => ['required', 'integer', 'exists:position_qualifications,id'],
        'rules.*.required_qualification_id' => ['required', 'integer', 'exists:position_qualifications,id'],
        'rules.*.allowed' => ['nullable', 'boolean'],
        'rules.*.efficiency_factor' => ['nullable', 'numeric', 'min:0.01', 'max:10'],
        'rules.*.cost_factor' => ['nullable', 'numeric', 'min:0.01', 'max:10'],
        'rules.*.notes' => ['nullable', 'string'],
    ]);

    DB::transaction(function () use ($data) {
        foreach ($data['rules'] as $rule) {
            $performerId = (int) $rule['performer_qualification_id'];
            $requiredId = (int) $rule['required_qualification_id'];

            PositionQualificationHierarchy::updateOrCreate(
                [
                    'performer_qualification_id' => $performerId,
                    'required_qualification_id' => $requiredId,
                ],
                [
                    'allowed' => (bool) ($rule['allowed'] ?? false),
                    'efficiency_factor' => $rule['efficiency_factor'] ?? 1,
                    'cost_factor' => $rule['cost_factor'] ?? 1,
                    'notes' => $rule['notes'] ?? null,
                ]
            );
        }
    });

    return response()->json([
        'ok' => true,
    ]);
}

public function hierarchyAutoGenerate(Request $request)
{
    /*
     * Auto-generate based on sort_order:
     *
     * Lower sort_order = stronger/higher qualification.
     *
     * Example:
     * 1 Meister
     * 2 Geselle
     * 3 Helfer
     * 4 Azubi 3
     * 5 Azubi 2
     * 6 Azubi 1
     *
     * Meister can perform everyone below.
     * Geselle can perform Geselle and everyone below.
     */
    $quals = PositionQualification::query()
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    DB::transaction(function () use ($quals) {
        foreach ($quals as $performer) {
            foreach ($quals as $required) {
                $allowed = (int) $performer->sort_order <= (int) $required->sort_order;

                PositionQualificationHierarchy::updateOrCreate(
                    [
                        'performer_qualification_id' => $performer->id,
                        'required_qualification_id' => $required->id,
                    ],
                    [
                        'allowed' => $allowed,
                        'efficiency_factor' => 1,
                        'cost_factor' => 1,
                    ]
                );
            }
        }
    });

    return response()->json([
        'ok' => true,
    ]);
}

    public function hierarchyCheck(Request $request)
    {
        $data = $request->validate([
            'performer_qualification_id' => ['required', 'integer', 'exists:position_qualifications,id'],
            'required_qualification_id' => ['required', 'integer', 'exists:position_qualifications,id'],
            'hours' => ['nullable', 'numeric', 'min:0'],
        ]);

        $performer = PositionQualification::findOrFail($data['performer_qualification_id']);
        $required = PositionQualification::findOrFail($data['required_qualification_id']);

        $rule = PositionQualificationHierarchy::ruleFor($performer->id, $required->id);

        $allowed = $rule && (bool) $rule->allowed;

        $hours = (float) ($data['hours'] ?? 1);
        $efficiency = $allowed ? (float) ($rule->efficiency_factor ?? 1) : 0;
        $costFactor = $allowed ? (float) ($rule->cost_factor ?? 1) : 0;

        $effectiveHours = $allowed ? $hours * $efficiency : 0;
        $hourlyCost = (float) $performer->default_price * $costFactor;
        $totalCost = $allowed ? $effectiveHours * $hourlyCost : 0;

        return response()->json([
            'ok' => true,
            'allowed' => $allowed,
            'performer' => [
                'id' => $performer->id,
                'name' => $performer->name,
                'hourly_cost' => $performer->default_price,
            ],
            'required' => [
                'id' => $required->id,
                'name' => $required->name,
            ],
            'hours' => $hours,
            'efficiency_factor' => $efficiency,
            'cost_factor' => $costFactor,
            'effective_hours' => round($effectiveHours, 2),
            'hourly_cost' => round($hourlyCost, 2),
            'total_cost' => round($totalCost, 2),
        ]);
    }

    // ---------------------------
    // TAB 1: Positions list (AJAX)
    // ---------------------------
    public function list(Request $request)
    {
        $q = trim((string)$request->get('q',''));
        $status = $request->get('status');              // Published/Unpublished/null
        $qualId = $request->get('qualification_id');    // id/null
        $sort = $request->get('sort','id');             // id/position/price/status
        $dir  = strtolower($request->get('dir','desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSort = ['id','position','price','status','qualification'];
        if (!in_array($sort, $allowedSort, true)) $sort = 'id';

        $rows = Position::query()
            ->with(['qualificationRef:id,name,default_price'])
            ->when($q !== '', function($qq) use ($q) {
                $qq->where(function($w) use ($q) {
                    $w->where('position','like',"%{$q}%")
                      ->orWhere('description','like',"%{$q}%");
                });
            })
            ->when($status, fn($qq) => $qq->where('status', $status))
            ->when($qualId, fn($qq) => $qq->where('qualification_id', $qualId))
            ->when($sort === 'qualification', function($qq) use ($dir) {
                $qq->leftJoin('position_qualifications as pq','pq.id','=','positions.qualification_id')
                   ->orderBy('pq.name', $dir)
                   ->select('positions.*');
            }, function($qq) use ($sort,$dir) {
                $qq->orderBy($sort, $dir);
            })
            ->paginate(20)
            ->withQueryString();

        $html = view('admin.employee.position.partials._table', [
            'data' => $rows
        ])->render();

        return response()->json([
            'ok' => true,
            'html' => $html,
        ]);
    }

    public function storeAjax(Request $request)
    {
        $data = $request->validate([
            'position' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'qualification_id' => ['nullable','exists:position_qualifications,id'],
        ]);

        $qual = null;
        if (!empty($data['qualification_id'])) {
            $qual = PositionQualification::find($data['qualification_id']);
        }

        $pos = Position::create([
            'position' => $data['position'],
            'description' => $data['description'] ?? null,
            'status' => 'Published',
            'qualification_id' => $qual?->id,
            // keep old columns consistent:
            'qualification' => $qual?->name,
            'price' => $qual?->default_price,
        ]);

        return response()->json(['ok' => true, 'id' => $pos->id]);
    }

    public function updateAjax(Request $request, Position $position)
    {
        $data = $request->validate([
            'position' => ['required','string','max:255'],
            'status' => ['nullable','in:Published,Unpublished'],
            'qualification_id' => ['nullable','exists:position_qualifications,id'],
            'price' => ['nullable','numeric'],
        ]);

        $qual = null;
        if (array_key_exists('qualification_id', $data) && $data['qualification_id']) {
            $qual = PositionQualification::find($data['qualification_id']);
        }

        $position->position = $data['position'];
        if (isset($data['status'])) $position->status = $data['status'];

        if (array_key_exists('qualification_id', $data)) {
            $position->qualification_id = $qual?->id;
            $position->qualification = $qual?->name;
            // if user didn’t explicitly override price, take default
            if (!array_key_exists('price', $data)) {
                $position->price = $qual?->default_price;
            }
        }

        if (array_key_exists('price', $data)) {
            $position->price = $data['price'];
        }

        $position->save();

        return response()->json(['ok' => true]);
    }

    public function destroyAjax(Position $position)
    {
        $position->delete();
        return response()->json(['ok' => true]);
    }

    public function toggle(Position $position)
    {
        $position->status = $position->status === 'Published' ? 'Unpublished' : 'Published';
        $position->save();
        return response()->json(['ok' => true, 'status' => $position->status]);
    }

    public function updateDescriptionAjax(Request $request, Position $position)
    {
        $data = $request->validate([
            'description' => ['required','string']
        ]);

        $position->description = $data['description'];
        $position->save();

        return response()->json(['ok' => true]);
    }

    // ---------------------------
    // TAB 2: Qualifications board (AJAX)
    // ---------------------------
   public function qualificationsBoard(Request $request)
    {
        $q = trim((string)$request->get('q',''));

        // Sidebar positions (searchable)
        $positions = Position::query()
            ->select('id','position','status','qualification_id','price','qualification')
            ->when($q !== '', function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('position','like',"%{$q}%")
                    ->orWhere('description','like',"%{$q}%");
                });
            })
            ->orderBy('position')
            ->limit(400) // protect UI; adjust if needed
            ->get();

        // Qualification cards with assigned positions
        $quals = PositionQualification::query()
            ->orderBy('sort_order')->orderBy('id')
            ->with(['positions' => function($qq){
                $qq->select('id','position','status','qualification_id','price','qualification')
                ->orderBy('position');
            }])
            ->get(['id','name','default_price','sort_order','status']);

        $html = view('admin.employee.position.partials._board_sidebar', [
            'quals' => $quals,
            'positions' => $positions,
            'q' => $q,
        ])->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }


    public function qualificationStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255','unique:position_qualifications,name'],
            'default_price' => ['required','numeric','min:0'],
        ]);

        $max = (int) PositionQualification::max('sort_order');

        $q = PositionQualification::create([
            'name' => $data['name'],
            'default_price' => $data['default_price'],
            'sort_order' => $max + 1,
            'status' => 'Published',
        ]);

        return response()->json(['ok' => true, 'id' => $q->id]);
    }

    public function qualificationUpdate(Request $request, $qid)
    {
        $qual = PositionQualification::findOrFail($qid);

        $data = $request->validate([
            'name' => ['required','string','max:255','unique:position_qualifications,name,'.$qual->id],
            'default_price' => ['required','numeric','min:0'],
            'status' => ['nullable','in:Published,Unpublished'],
        ]);

        $qual->fill($data)->save();

        // Optional: update positions that use this qualification with new default price
        // (only if they match old default or if you want force update)
        Position::where('qualification_id', $qual->id)->update([
            'qualification' => $qual->name,
        ]);

        return response()->json(['ok' => true]);
    }

    public function qualificationDestroy($qid)
    {
        $qual = PositionQualification::findOrFail($qid);

        // Unassign positions
        Position::where('qualification_id', $qual->id)->update([
            'qualification_id' => null,
            'qualification' => null,
            'price' => null,
        ]);

        $qual->delete();

        return response()->json(['ok' => true]);
    }

    public function qualificationReorder(Request $request)
    {
        $data = $request->validate([
            'order' => ['required','array'],
            'order.*' => ['integer','exists:position_qualifications,id'],
        ]);

        foreach ($data['order'] as $i => $id) {
            PositionQualification::where('id', $id)->update(['sort_order' => $i + 1]);
        }

        return response()->json(['ok' => true]);
    }

    // Drag-drop: assign position to qualification -> auto set price
    public function assignQualification(Request $request)
    {
        $data = $request->validate([
            'position_id' => ['required','exists:positions,id'],
            'qualification_id' => ['nullable','exists:position_qualifications,id'],
        ]);

        $pos = Position::findOrFail($data['position_id']);
        $qual = $data['qualification_id'] ? PositionQualification::find($data['qualification_id']) : null;

        $pos->qualification_id = $qual?->id;
        $pos->qualification = $qual?->name;
        $pos->price = $qual?->default_price;
        $pos->save();

        return response()->json(['ok' => true]);
    }

    public function storeJson(Request $request)
    {
        $data = $request->validate([
            'position' => 'required|string|max:255',
        ]);

        // Prevent duplicates (case-insensitive)
        $existing = Position::whereRaw('LOWER(position) = ?', [mb_strtolower($data['position'])])->first();
        if ($existing) {
            return response()->json([
                'id' => $existing->id,
                'text' => $existing->position,
            ]);
        }

        $pos = Position::create([
            'position' => $data['position'],
            'status'   => 'Published',
        ]);

        return response()->json([
            'id' => $pos->id,
            'text' => $pos->position,
        ]);
    }
}
