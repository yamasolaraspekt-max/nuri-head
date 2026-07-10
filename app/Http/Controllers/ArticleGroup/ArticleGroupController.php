<?php

namespace App\Http\Controllers\ArticleGroup;
use App\Http\Controllers\Controller;

use App\Models\ArticleGroup;
use App\Models\SubArticleGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
class ArticleGroupController extends Controller
{
    public function __construct()
    {
        // MASTER-01 P1-IDOR Product: Katalog/Lager-Rollen-Gate (permission:Product)
        $this->middleware('permission:Product,update')->only(['save', 'update', 'updateSubArticleGroup']);
        $this->middleware('permission:Product,delete')->only(['destroy', 'destroySubArticleGroup']);
        $this->middleware('auth');
    }

    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------
public function index(Request $request)
{
    $search = trim((string) $request->query('search', ''));
    $minValue = $request->query('min_value');
    $maxValue = $request->query('max_value');

    $allowedSorts = ['id', 'initial', 'article_group', 'min_value', 'max_value'];
    $sort = $request->query('sort', 'article_group');
    $direction = strtolower((string) $request->query('direction', 'asc')) === 'desc' ? 'desc' : 'asc';

    if (!in_array($sort, $allowedSorts, true)) {
        $sort = 'article_group';
    }

    $query = ArticleGroup::query()
        ->with([
            'subArticleGroups' => function ($q) {
                $q->orderBy('sub_article');
            }
        ]);

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('article_group', 'LIKE', "%{$search}%")
                ->orWhere('initial', 'LIKE', "%{$search}%")
                ->orWhereHas('subArticleGroups', function ($sub) use ($search) {
                    $sub->where('sub_article', 'LIKE', "%{$search}%")
                        ->orWhere('initial', 'LIKE', "%{$search}%")
                        ->orWhere('value', 'LIKE', "%{$search}%");
                });
        });
    }

    if ($request->filled('min_value')) {
        $query->where('min_value', '>=', (float) $minValue);
    }

    if ($request->filled('max_value')) {
        $query->where('max_value', '<=', (float) $maxValue);
    }

    $query->orderBy($sort, $direction);

    $statsQuery = clone $query;

    $data = $query->paginate(30)->appends($request->query());

    $data->getCollection()->transform(function ($group) {
        $group->usage_rows = $this->getArticleGroupUsage($group);
        $group->usage_count = collect($group->usage_rows)->sum('count');
        $group->can_delete = empty($group->usage_rows);
        $group->delete_warning = $group->can_delete
            ? null
            : $this->articleGroupUsageMessage($group);

        return $group;
    });

    $stats = $this->getStats($statsQuery);

    if ($request->ajax()) {
        return response()->json([
            'success'    => true,
            'html'       => view('admin.product.article_group.main.partials.table', compact('data', 'sort', 'direction'))->render(),
            'pagination' => view('admin.product.article_group.main.partials.pagination', compact('data'))->render(),
            'stats'      => $stats,
        ]);
    }

    return view('admin.product.article_group.main.article_group', compact('data', 'stats', 'sort', 'direction'));
}

protected function getArticleGroupUsage(ArticleGroup $group): array
{
    $checks = [
        // table => [columns...]
        'lead_product_lists'      => ['article_group_id', 'product_id'],
        'customer_product_lists'  => ['article_group_id', 'product_id'],
        'phase_sections'          => ['product_id'],
        'sub_article_groups'      => ['article_group_id'],
        'products'                => ['article_group_id'],
        'offer_items'             => ['article_group_id', 'product_id'],
        'customer_alternative_adds' => ['article_group_id', 'product_id'],
        'lead_alternative_adds'     => ['article_group_id', 'product_id'],
    ];

    $usedIn = [];

    foreach ($checks as $table => $columns) {
        if (!Schema::hasTable($table)) {
            continue;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                continue;
            }

            $count = DB::table($table)->where($column, $group->id)->count();

            if ($count > 0) {
                $usedIn[] = [
                    'table' => $table,
                    'column' => $column,
                    'count' => $count,
                ];
            }
        }
    }

    return $usedIn;
}

protected function canDeleteArticleGroup(ArticleGroup $group): bool
{
    return count($this->getArticleGroupUsage($group)) === 0;
}

protected function articleGroupUsageMessage(ArticleGroup $group): string
{
    $usedIn = $this->getArticleGroupUsage($group);

    if (empty($usedIn)) {
        return '';
    }

    $parts = collect($usedIn)->map(function ($item) {
        return $item['table'] . ' (' . $item['count'] . ')';
    })->implode(', ');

    return 'Diese Artikel-Gruppe wird bereits verwendet in: ' . $parts . '. Löschen ist nicht erlaubt.';
}

    // -------------------------------------------------------------------------
    // STORE – Artikel-Gruppe
    // -------------------------------------------------------------------------
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_group' => 'required|string|max:255',
            'initial'       => 'nullable|string|max:255',
            'min_value'     => 'nullable|numeric',
            'max_value'     => 'nullable|numeric',
            'image'         => 'nullable|image|max:4096',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($request, $validator);
        }

        try {
            $group = new ArticleGroup();
            $group->article_group = $request->article_group;
            $group->initial = $request->initial;
            $group->min_value = $request->min_value;
            $group->max_value = $request->max_value;

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images/articles'), $imageName);
                $group->image = $imageName;
            }

            $group->save();

            $defaultSections = [
                'complete',
                'montage',
                'product',
                'plan',
                'maintenance',
                'repair',
                'reclaim',
                'others',
            ];

            foreach ($defaultSections as $section) {
                DB::table('phase_sections')->insert([
                    'product_id'    => $group->id,
                    'phase_section' => $section,
                    'status'        => 'pending',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            if ($request->ajax()) {
                return $this->ajaxSuccessResponse($request, 'Die Artikel-Gruppe wurde erfolgreich gespeichert.');
            }

            return redirect()
                ->route('article_group.index')
                ->with('save_msg', 'Die Artikel-Gruppe wurde erfolgreich gespeichert.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($request, $e, 'Die Artikel-Gruppe konnte nicht gespeichert werden.');
        }
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_group' => 'required|string|max:255|unique:article_groups,article_group',
            'initial'       => 'nullable|string|max:255',
            'min_value'     => 'nullable|numeric',
            'max_value'     => 'nullable|numeric',
            'image'         => 'nullable|image|max:4096',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($request, $validator);
        }

        try {
            $group = new ArticleGroup();
            $group->article_group = $request->article_group;
            $group->initial = $request->initial;
            $group->min_value = $request->min_value;
            $group->max_value = $request->max_value;

            if ($request->hasFile('image')) {
                $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images/articles'), $imageName);
                $group->image = $imageName;
            }

            $group->save();

            $defaultSections = [
                'complete',
                'montage',
                'product',
                'plan',
                'maintenance',
                'repair',
                'reclaim',
                'others',
            ];

            foreach ($defaultSections as $section) {
                DB::table('phase_sections')->insert([
                    'product_id'    => $group->id,
                    'phase_section' => $section,
                    'status'        => 'pending',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Die Artikel-Gruppe wurde erfolgreich gespeichert.',
                    'redirect' => route('task_phase.index', ['product' => $group->id]),
                ]);
            }

            return redirect()
                ->route('task_phase.index', ['product' => $group->id])
                ->with('save_msg', 'Die Artikel-Gruppe wurde erfolgreich gespeichert.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($request, $e, 'Die Artikel-Gruppe konnte nicht gespeichert werden.');
        }
    }

    // -------------------------------------------------------------------------
    // UPDATE – Artikel-Gruppe
    // -------------------------------------------------------------------------
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'            => 'required|exists:article_groups,id',
            'article_group' => 'required|string|max:255',
            'initial'       => 'nullable|string|max:255',
            'min_value'     => 'nullable|numeric',
            'max_value'     => 'nullable|numeric',
            'image'         => 'nullable|image|max:4096',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($request, $validator);
        }

        try {
            $group = ArticleGroup::findOrFail($request->id);
            $group->article_group = $request->article_group;
            $group->initial = $request->initial;
            $group->min_value = $request->min_value;
            $group->max_value = $request->max_value;

            if ($request->hasFile('image')) {
                $this->deletePhoto($group->image);
                $imageName = time() . '_' . uniqid() . '.' . $request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images/articles'), $imageName);
                $group->image = $imageName;
            }

            $group->save();

            if ($request->ajax()) {
                return $this->ajaxSuccessResponse($request, 'Die Artikel-Gruppe wurde erfolgreich aktualisiert.');
            }

            return redirect()
                ->route('article_group.index')
                ->with('update_msg', 'Die Artikel-Gruppe wurde erfolgreich aktualisiert.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($request, $e, 'Die Artikel-Gruppe konnte nicht aktualisiert werden.');
        }
    }

    // -------------------------------------------------------------------------
    // DESTROY – Artikel-Gruppe
    // -------------------------------------------------------------------------
    public function destroy(Request $request, $id)
    {
        try {
            $group = ArticleGroup::findOrFail($id);

            $usedIn = $this->getArticleGroupUsage($group);

            if (!empty($usedIn)) {
                $message = $this->articleGroupUsageMessage($group);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'used_in' => $usedIn,
                    ], 422);
                }

                return redirect()
                    ->route('article_group.index')
                    ->with('delete_msg', $message);
            }

            $this->deletePhoto($group->image);
            $group->delete();

            if ($request->ajax()) {
                return $this->ajaxSuccessResponse($request, 'Die Artikel-Gruppe wurde erfolgreich gelöscht.');
            }

            return redirect()
                ->route('article_group.index')
                ->with('delete_msg', 'Die Artikel-Gruppe wurde erfolgreich gelöscht.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($request, $e, 'Die Artikel-Gruppe konnte nicht gelöscht werden.');
        }
    }
    // -------------------------------------------------------------------------
    // SUB-ARTIKELGRUPPEN – STORE
    // -------------------------------------------------------------------------
    public function storeSubArticleGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_group_id' => 'required|exists:article_groups,id',
            'sub_article'      => 'required|string|max:255',
            'initial'          => 'nullable|string|max:255',
            'value'            => 'nullable|string|max:255',
            'status'           => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($request, $validator);
        }

        try {
            SubArticleGroup::create([
                'article_group_id' => $request->article_group_id,
                'sub_article'      => $request->sub_article,
                'initial'          => $request->initial,
                'value'            => $request->value,
                'status'           => $request->status,
            ]);

            if ($request->ajax()) {
                return $this->ajaxSuccessResponse($request, 'Die Sub-Artikelgruppe wurde erfolgreich gespeichert.');
            }

            return redirect()
                ->route('article_group.index')
                ->with('save_msg', 'Die Sub-Artikelgruppe wurde erfolgreich gespeichert.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($request, $e, 'Die Sub-Artikelgruppe konnte nicht gespeichert werden.');
        }
    }

    // -------------------------------------------------------------------------
    // SUB-ARTIKELGRUPPEN – UPDATE
    // -------------------------------------------------------------------------
    public function updateSubArticleGroup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'          => 'required|exists:sub_article_groups,id',
            'sub_article' => 'required|string|max:255',
            'initial'     => 'nullable|string|max:255',
            'value'       => 'nullable|string|max:255',
            'status'      => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($request, $validator);
        }

        try {
            $sub = SubArticleGroup::findOrFail($request->id);
            $sub->sub_article = $request->sub_article;
            $sub->initial = $request->initial;
            $sub->value = $request->value;
            $sub->status = $request->status;
            $sub->save();

            if ($request->ajax()) {
                return $this->ajaxSuccessResponse($request, 'Die Sub-Artikelgruppe wurde erfolgreich aktualisiert.');
            }

            return redirect()
                ->route('article_group.index')
                ->with('update_msg', 'Die Sub-Artikelgruppe wurde erfolgreich aktualisiert.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($request, $e, 'Die Sub-Artikelgruppe konnte nicht aktualisiert werden.');
        }
    }

    // -------------------------------------------------------------------------
    // SUB-ARTIKELGRUPPEN – DESTROY
    // -------------------------------------------------------------------------
    public function destroySubArticleGroup(Request $request, $id)
    {
        try {
            $sub = SubArticleGroup::findOrFail($id);
            $sub->delete();

            if ($request->ajax()) {
                return $this->ajaxSuccessResponse($request, 'Die Sub-Artikelgruppe wurde erfolgreich gelöscht.');
            }

            return redirect()
                ->route('article_group.index')
                ->with('delete_msg', 'Die Sub-Artikelgruppe wurde erfolgreich gelöscht.');
        } catch (\Throwable $e) {
            return $this->exceptionResponse($request, $e, 'Die Sub-Artikelgruppe konnte nicht gelöscht werden.');
        }
    }

    // -------------------------------------------------------------------------
    // AJAX HELPERS
    // -------------------------------------------------------------------------
    protected function ajaxSuccessResponse(Request $request, string $message)
    {
        $refresh = $this->buildRefreshPayload($request);

        return response()->json([
            'success'    => true,
            'message'    => $message,
            'html'       => $refresh['html'],
            'pagination' => $refresh['pagination'],
            'stats'      => $refresh['stats'],
        ]);
    }

  
    protected function buildRefreshPayload(Request $request): array
    {
        $params = [
            'search'    => $request->input('search', $request->query('search')),
            'min_value' => $request->input('min_value', $request->query('min_value')),
            'max_value' => $request->input('max_value', $request->query('max_value')),
            'sort'      => $request->input('sort', $request->query('sort', 'article_group')),
            'direction' => $request->input('direction', $request->query('direction', 'asc')),
            'page'      => max(1, (int) $request->input('page', $request->query('page', 1))),
        ];

        $allowedSorts = ['id', 'initial', 'article_group', 'min_value', 'max_value'];
        $sort = in_array($params['sort'], $allowedSorts, true) ? $params['sort'] : 'article_group';
        $direction = strtolower((string) $params['direction']) === 'desc' ? 'desc' : 'asc';

        $query = ArticleGroup::query()
            ->with([
                'subArticleGroups' => function ($q) {
                    $q->orderBy('sub_article');
                }
            ]);

        if (!empty($params['search'])) {
            $search = trim((string) $params['search']);

            $query->where(function ($q) use ($search) {
                $q->where('article_group', 'LIKE', "%{$search}%")
                    ->orWhere('initial', 'LIKE', "%{$search}%")
                    ->orWhereHas('subArticleGroups', function ($sub) use ($search) {
                        $sub->where('sub_article', 'LIKE', "%{$search}%")
                            ->orWhere('initial', 'LIKE', "%{$search}%")
                            ->orWhere('value', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($params['min_value'] !== null && $params['min_value'] !== '') {
            $query->where('min_value', '>=', (float) $params['min_value']);
        }

        if ($params['max_value'] !== null && $params['max_value'] !== '') {
            $query->where('max_value', '<=', (float) $params['max_value']);
        }

        $statsQuery = clone $query;

        $data = $query
            ->orderBy($sort, $direction)
            ->paginate(30, ['*'], 'page', $params['page'])
            ->appends($params);

        if ($data->currentPage() > $data->lastPage() && $data->lastPage() > 0) {
            $params['page'] = $data->lastPage();

            $data = $query
                ->orderBy($sort, $direction)
                ->paginate(30, ['*'], 'page', $params['page'])
                ->appends($params);
        }

        $data->getCollection()->transform(function ($group) {
            $group->usage_rows = $this->getArticleGroupUsage($group);
            $group->usage_count = collect($group->usage_rows)->sum('count');
            $group->can_delete = empty($group->usage_rows);
            $group->delete_warning = $group->can_delete
                ? null
                : $this->articleGroupUsageMessage($group);

            return $group;
        });

        return [
            'html'       => view('admin.product.article_group.main.partials.table', [
                'data' => $data,
                'sort' => $sort,
                'direction' => $direction,
            ])->render(),
            'pagination' => view('admin.product.article_group.main.partials.pagination', compact('data'))->render(),
            'stats'      => $this->getStats($statsQuery),
        ];
    }

    protected function validationErrorResponse(Request $request, $validator)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte prüfen Sie die Eingaben.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        return redirect()->back()->withErrors($validator)->withInput();
    }

    protected function exceptionResponse(Request $request, \Throwable $e, string $message)
    {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return redirect()->back()->with('delete_msg', $message);
    }

    protected function getStats($query): array
    {
        $groups = (clone $query)
            ->with(['subArticleGroups'])
            ->get();

        return [
            'total'      => $groups->count(),
            'with_image' => $groups->filter(fn ($item) => !empty($item->image))->count(),
            'with_range' => $groups->filter(fn ($item) => !is_null($item->min_value) || !is_null($item->max_value))->count(),
            'sub_total'  => $groups->sum(fn ($item) => $item->subArticleGroups->count()),
        ];
    }

    // -------------------------------------------------------------------------
    // Helper – delete image file
    // -------------------------------------------------------------------------
    protected function deletePhoto($photo): void
    {
        if (!empty($photo)) {
            $photoPath = public_path('images/articles/' . $photo);
            if (file_exists($photoPath)) {
                @unlink($photoPath);
            }
        }
    }
}