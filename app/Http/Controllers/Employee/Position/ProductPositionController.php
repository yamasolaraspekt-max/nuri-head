<?php

namespace App\Http\Controllers\Employee\Position;

use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\Department;
use App\Models\DepartmentPosition;
use App\Models\Employee;
use App\Models\PhaseSection;
use App\Models\Position;
use App\Models\ProductPosition;
use Illuminate\Http\Request;

class ProductPositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('admin.product.position.product_position');
    }

    public function getArticleGroups()
    {
        $groups = ArticleGroup::select('id', 'article_group')->get();
        return response()->json($groups);
    }

    public function getDepartments()
    {
        $departments = Department::select('id', 'department_name')
            ->where('status', 'Published')
            ->get();

        return response()->json($departments);
    }

    public function getPositions($department_id)
    {
        $positions = DepartmentPosition::where('department_id', $department_id)
            ->whereHas('position')
            ->with('position:id,position')
            ->get()
            ->map(fn($dp) => $dp->position)
            ->filter()
            ->unique('id')
            ->values();

        \Log::info('Positions Cleaned', [$positions]);

        return response()->json($positions);
    }

    public function save(Request $request)
    {
        $rows = $request->input('rows', []);

        foreach ($rows as $row) {
            $incoming = $row['positions'] ?? [];
            $normalized = [
                'internal' => array_values(array_filter($incoming['internal'] ?? [])),
                'external' => array_values(array_filter($incoming['external'] ?? [])),
            ];

            ProductPosition::create([
                'stage' => $row['stage'],
                'article_group_id' => $row['produkt'],
                'service_id' => $row['service'],
                'department_id' => $row['department'],
                'position_ids' => $normalized,
            ]);
        }

        return response()->json(['message' => 'Saved successfully!']);
    }

    /**
     * Saved records are enriched with:
     * - readable position names
     * - employees attached to the selected internal/external positions
     *
     * This avoids loading employees with one AJAX request per row in the Blade.
     */
    public function getSavedRecords()
    {
        $records = ProductPosition::with(['articleGroup', 'department', 'service'])
            ->latest('id')
            ->get();

        $allPositionIds = $records
            ->flatMap(function ($record) {
                $positionIds = $this->normalizePositionIds($record->position_ids);

                return array_merge(
                    $positionIds['internal'] ?? [],
                    $positionIds['external'] ?? []
                );
            })
            ->filter()
            ->unique()
            ->values();

        $positionNamesById = $allPositionIds->isNotEmpty()
            ? Position::whereIn('id', $allPositionIds)->pluck('position', 'id')
            : collect();

        $employeesByPositionId = $this->buildEmployeesByPositionId($allPositionIds->all());

        foreach ($records as $record) {
            $positionIds = $this->normalizePositionIds($record->position_ids);
            $idsInternal = $positionIds['internal'] ?? [];
            $idsExternal = $positionIds['external'] ?? [];

            $record->position_ids = [
                'internal' => array_values($idsInternal),
                'external' => array_values($idsExternal),
            ];

            $record->position_internal = collect($idsInternal)
                ->map(fn($id) => $positionNamesById[$id] ?? null)
                ->filter()
                ->values()
                ->all();

            $record->position_external = collect($idsExternal)
                ->map(fn($id) => $positionNamesById[$id] ?? null)
                ->filter()
                ->values()
                ->all();

            $record->employees_internal = $this->employeesForPositionIds($idsInternal, $employeesByPositionId);
            $record->employees_external = $this->employeesForPositionIds($idsExternal, $employeesByPositionId);
        }

        return response()->json($records);
    }

    public function delete($id)
    {
        ProductPosition::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted successfully!']);
    }

    public function getEmployeesByPositions(Request $request)
    {
        $payload = $request->input('position_ids', []);
        $ids = array_unique(array_merge(
            $payload['internal'] ?? [],
            $payload['external'] ?? []
        ));

        $employees = Employee::whereHas('departmentPositions', fn($q) => $q->whereIn('position_id', $ids))
            ->with(['departmentPositions.position' => fn($q) => $q->whereIn('id', $ids)])
            ->select('id', 'name', 'lastname', 'image')
            ->get()
            ->map(function ($employee) {
                $positions = $employee->departmentPositions
                    ->map(fn($dp) => $dp->position->position ?? null)
                    ->filter()
                    ->unique()
                    ->values();

                $employee->positions = $positions;

                return $employee;
            });

        return response()->json($employees);
    }

    public function update(Request $request, $id)
    {
        $position = ProductPosition::findOrFail($id);

        $incoming = $request->input('positions', []);
        $normalized = [
            'internal' => array_values(array_filter($incoming['internal'] ?? [])),
            'external' => array_values(array_filter($incoming['external'] ?? [])),
        ];

        $position->update([
            'stage' => $request->input('stage'),
            'article_group_id' => $request->input('produkt'),
            'service_id' => $request->input('service'),
            'department_id' => $request->input('department'),
            'position_ids' => $normalized,
        ]);

        return response()->json(['message' => 'Updated successfully!']);
    }

    public function getPhaseSections($product_id)
    {
        $sections = PhaseSection::where('product_id', $product_id)
            ->get(['id', 'phase_section']);

        $translated = $sections->map(function ($section) {
            $label = match (strtolower($section->phase_section)) {
                'complete' => 'Komplett',
                'montage' => 'Montage',
                'product' => 'Produkt',
                'plan' => 'Planung',
                'maintenance' => 'Wartung',
                'repair' => 'Reparatur',
                'others' => 'Others',
                default => ucfirst($section->phase_section),
            };

            return [
                'id' => $section->id,
                'label' => $label,
            ];
        });

        return response()->json($translated);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Keine IDs übergeben.'], 422);
        }

        ProductPosition::whereIn('id', $ids)->delete();

        return response()->json(['message' => 'Ausgewählte Vorschläge gelöscht.']);
    }

    public function bulkDuplicate(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['message' => 'Keine IDs übergeben.'], 422);
        }

        $positions = ProductPosition::whereIn('id', $ids)->get();
        $created = [];

        foreach ($positions as $position) {
            $clone = $position->replicate();
            $clone->created_at = now();
            $clone->updated_at = now();
            $clone->save();

            $created[] = $clone->id;
        }

        return response()->json([
            'message' => 'Ausgewählte Vorschläge dupliziert.',
            'created_ids' => $created,
        ]);
    }

    private function normalizePositionIds($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($value)) {
            $value = [];
        }

        return [
            'internal' => array_values(array_filter($value['internal'] ?? [])),
            'external' => array_values(array_filter($value['external'] ?? [])),
        ];
    }

    private function buildEmployeesByPositionId(array $positionIds): array
    {
        if (empty($positionIds)) {
            return [];
        }

        $employees = Employee::whereHas('departmentPositions', fn($q) => $q->whereIn('position_id', $positionIds))
            ->with(['departmentPositions.position' => fn($q) => $q->whereIn('id', $positionIds)])
            ->select('id', 'name', 'lastname', 'image')
            ->get();

        $map = [];

        foreach ($employees as $employee) {
            foreach ($employee->departmentPositions as $departmentPosition) {
                $position = $departmentPosition->position;

                if (!$position) {
                    continue;
                }

                $positionId = (int) $position->id;

                if (!isset($map[$positionId])) {
                    $map[$positionId] = [];
                }

                $map[$positionId][$employee->id] = [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'lastname' => $employee->lastname,
                    'image' => $employee->image,
                    'positions' => $employee->departmentPositions
                        ->map(fn($dp) => $dp->position->position ?? null)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all(),
                ];
            }
        }

        return $map;
    }

    private function employeesForPositionIds(array $positionIds, array $employeesByPositionId): array
    {
        $employees = [];

        foreach ($positionIds as $positionId) {
            $positionId = (int) $positionId;

            foreach (($employeesByPositionId[$positionId] ?? []) as $employeeId => $employee) {
                $employees[$employeeId] = $employee;
            }
        }

        return array_values($employees);
    }
}
