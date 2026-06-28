<?php

namespace App\Http\Controllers\Customer\Maintenance;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Distributor;
use App\Models\MaintenanceChecklist;
use App\Models\MaintenanceChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MaintenanceChecklistController extends Controller
{
    /**
     * Liste mit Tabs: aktiv / archiviert / Papierkorb.
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $sort   = $request->query('sort', 'title_asc');
        $scope  = $request->query('scope', 'active'); // active|archived|deleted

        // Basis-Query inkl. Beziehungen
        $baseQuery = MaintenanceChecklist::with([
            'items',
            'brands',
            'distributors',
            'creatorEmployee',
        ])->withTrashed(); // damit wir für "Papierkorb" auch gelöschte sehen

        if ($scope === 'deleted') {
            // nur Soft-Deleted
            $query = (clone $baseQuery)->onlyTrashed();
        } else {
            // nicht gelöschte
            $query = (clone $baseQuery)->whereNull('deleted_at');

            if ($scope === 'archived') {
                $query->where('status', 'archived');
            } else {
                // active: alles außer "archived"
                $query->where('status', '!=', 'archived');
            }
        }

        // Suche
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%')
                    ->orWhereHas('brands', function ($qb) use ($search) {
                        $qb->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('distributors', function ($qb) use ($search) {
                        $qb->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        // Sortierung
        switch ($sort) {
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'latest':
                $query->latest();
                break;
            case 'title_asc':
            default:
                $query->orderBy('title', 'asc');
                break;
        }

        $maintenanceChecklists = $query->paginate(24)->withQueryString();

        $brands       = Brand::orderBy('name')->get();
        $distributors = Distributor::orderBy('name')->get();

        return view('admin.maintenance_checklists.index', compact(
            'maintenanceChecklists',
            'brands',
            'distributors',
            'search',
            'sort',
            'scope'
        ));
    }

    /**
     * Neue Wartungs-Checkliste speichern.
     */
    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        DB::transaction(function () use ($request, $data) {
            $slug = $this->generateUniqueSlug($data['title']);

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('maintenance_checklists/logos', 'public');
            }

            // created_by / updated_by = employee_id, das du in user()->name speicherst
            $employeeId = optional($request->user())->name;

            $checklist = MaintenanceChecklist::create([
                'title'       => $data['title'],
                'slug'        => $slug,
                'description' => $data['description'] ?? null,
                'logo_path'   => $logoPath,
                'type'        => $data['type'] ?: 'maintenance',
                'status'      => $data['status'] ?? 'draft',
                'is_global'   => true,
                'created_by'  => $employeeId,
                'updated_by'  => $employeeId,
            ]);

            $checklist->brands()->sync($data['brand_ids'] ?? []);
            $checklist->distributors()->sync($data['distributor_ids'] ?? []);

            $items = $this->decodeItemsJson($data['items_json'] ?? '[]');
            $this->syncItems($checklist, $items);
        });

        return redirect()
            ->route('admin.maintenance_checklists.index')
            ->with('success', 'Wartungs-Checkliste wurde erstellt.');
    }

    /**
     * Bestehende Checkliste aktualisieren.
     */
    public function update(Request $request, MaintenanceChecklist $maintenance_checklist)
    {
        $data = $this->validateRequest($request, $maintenance_checklist->id);

        DB::transaction(function () use ($request, $data, $maintenance_checklist) {
            $checklist = $maintenance_checklist;

            if ($data['title'] !== $checklist->title) {
                $checklist->slug = $this->generateUniqueSlug($data['title'], $checklist->id);
            }

            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('maintenance_checklists/logos', 'public');
                $checklist->logo_path = $logoPath;
            }

            $employeeId = optional($request->user())->name;

            $checklist->title       = $data['title'];
            $checklist->description = $data['description'] ?? null;
            $checklist->type        = $data['type'] ?: 'maintenance';
            $checklist->status      = $data['status'] ?? $checklist->status;
            $checklist->updated_by  = $employeeId;
            $checklist->save();

            $checklist->brands()->sync($data['brand_ids'] ?? []);
            $checklist->distributors()->sync($data['distributor_ids'] ?? []);

            $items = $this->decodeItemsJson($data['items_json'] ?? '[]');
            $this->syncItems($checklist, $items);
        });

        return redirect()
            ->route('admin.maintenance_checklists.index')
            ->with('success', 'Wartungs-Checkliste wurde aktualisiert.');
    }

    /**
     * Archivieren (Status = archived).
     */
    public function archive(MaintenanceChecklist $maintenance_checklist)
    {
        $maintenance_checklist->status = 'archived';
        $maintenance_checklist->save();

        return redirect()
            ->route('admin.maintenance_checklists.index', ['scope' => 'archived'])
            ->with('success', 'Wartungs-Checkliste wurde archiviert.');
    }

    /**
     * Archivierung aufheben (zurück auf draft).
     */
    public function unarchive(MaintenanceChecklist $maintenance_checklist)
    {
        $maintenance_checklist->status = 'draft';
        $maintenance_checklist->save();

        return redirect()
            ->route('admin.maintenance_checklists.index', ['scope' => 'active'])
            ->with('success', 'Archivierung wurde aufgehoben.');
    }

    /**
     * In den Papierkorb verschieben (Soft Delete).
     */
    public function destroy(MaintenanceChecklist $maintenance_checklist)
    {
        $maintenance_checklist->delete();

        return redirect()
            ->route('admin.maintenance_checklists.index', ['scope' => 'deleted'])
            ->with('success', 'Wartungs-Checkliste wurde in den Papierkorb verschoben.');
    }

    /**
     * Aus dem Papierkorb wiederherstellen.
     */
    public function restore($id)
    {
        $checklist = MaintenanceChecklist::onlyTrashed()->findOrFail($id);
        $checklist->restore();

        return redirect()
            ->route('admin.maintenance_checklists.index', ['scope' => 'active'])
            ->with('success', 'Wartungs-Checkliste wurde wiederhergestellt.');
    }

    /**
     * JSON für Edit-/Duplicate-Modal.
     */
    public function editJson(MaintenanceChecklist $maintenance_checklist)
    {
        $checklist = $maintenance_checklist->load(['items', 'brands', 'distributors']);

        return response()->json([
            'id'              => $checklist->id,
            'title'           => $checklist->title,
            'description'     => $checklist->description,
            'type'            => $checklist->type,
            'status'          => $checklist->status,
            'logo_path'       => $checklist->logo_path,
            'brand_ids'       => $checklist->brands->pluck('id')->values(),
            'distributor_ids' => $checklist->distributors->pluck('id')->values(),
            'items'           => $checklist->items->map(function (MaintenanceChecklistItem $item) {
                return [
                    'id'          => $item->id,
                    'label'       => $item->label,
                    'field_name'  => $item->field_name,
                    'field_type'  => $item->field_type,
                    'options'     => $item->options ?? [],
                    'is_required' => (bool) $item->is_required,
                    'help_text'   => $item->help_text,
                    'placeholder' => $item->placeholder,
                    'file_accept' => $item->file_accept,
                    'sort_order'  => $item->sort_order,
                ];
            })->values(),
        ]);
    }

    /**
     * Validierung für create / update.
     */
    protected function validateRequest(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'type'        => ['nullable', 'string', 'max:100'],
            'status'      => ['required', Rule::in(['draft', 'active', 'archived'])],
            'description' => ['nullable', 'string'],

            'logo'        => ['nullable', 'image', 'max:2048'],

            'brand_ids'        => ['nullable', 'array'],
            'brand_ids.*'      => ['integer', 'exists:brands,id'],
            'distributor_ids'  => ['nullable', 'array'],
            'distributor_ids.*'=> ['integer', 'exists:distributors,id'],

            'items_json'  => ['nullable', 'string'],
        ]);
    }

    protected function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug     = $baseSlug;
        $counter  = 1;

        while (
            MaintenanceChecklist::where('slug', $slug)
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function decodeItemsJson(string $json): array
    {
        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            return [];
        }

        return array_map(function ($item) {
            $options = $item['options'] ?? [];
            if (is_string($options)) {
                $options = array_filter(array_map('trim', explode(',', $options)));
            }

            return [
                'id'          => $item['id'] ?? null,
                'label'       => $item['label'] ?? '',
                'field_name'  => $item['field_name'] ?? '',
                'field_type'  => $item['field_type'] ?? 'text',
                'options'     => $options,
                'is_required' => !empty($item['is_required']),
                'help_text'   => $item['help_text'] ?? null,
                'placeholder' => $item['placeholder'] ?? null,
                'file_accept' => $item['file_accept'] ?? null,
                'sort_order'  => $item['sort_order'] ?? 0,
            ];
        }, $decoded);
    }

    protected function syncItems(MaintenanceChecklist $checklist, array $items): void
    {
        $existingIds = $checklist->items()->pluck('id')->all();
        $incomingIds = collect($items)->pluck('id')->filter()->all();

        $toDelete = array_diff($existingIds, $incomingIds);
        if (!empty($toDelete)) {
            MaintenanceChecklistItem::whereIn('id', $toDelete)->delete();
        }

        foreach ($items as $index => $itemData) {
            $payload = [
                'label'       => $itemData['label'],
                'field_name'  => $itemData['field_name'] ?: 'field_' . ($index + 1),
                'field_type'  => $itemData['field_type'],
                'options'     => $itemData['options'],
                'is_required' => $itemData['is_required'],
                'help_text'   => $itemData['help_text'],
                'placeholder' => $itemData['placeholder'],
                'file_accept' => $itemData['file_accept'],
                'sort_order'  => $index,
            ];

            if (!empty($itemData['id'])) {
                $item = MaintenanceChecklistItem::where('maintenance_checklist_id', $checklist->id)
                    ->where('id', $itemData['id'])
                    ->first();

                if ($item) {
                    $item->update($payload);
                    continue;
                }
            }

            $checklist->items()->create($payload);
        }
    }

    public function bulk(Request $request)
{
    $ids    = (array) $request->input('ids', []);
    $action = $request->input('action');

    $query = MaintenanceChecklist::whereIn('id', $ids);

    switch ($action) {
        case 'archive':
            $query->update(['status' => 'archived']);
            break;
        case 'unarchive':
            $query->update(['status' => 'draft']);
            break;
        case 'trash':
            $query->delete(); // or soft-delete / move to "deleted" scope, depending on your model
            break;
        case 'restore':
            MaintenanceChecklist::onlyTrashed()
                ->whereIn('id', $ids)
                ->restore();
            break;
        case 'status_active':
            $query->update(['status' => 'active']);
            break;
        case 'status_draft':
            $query->update(['status' => 'draft']);
            break;
        case 'status_archived':
            $query->update(['status' => 'archived']);
            break;
    }

    return redirect()->back();
}

}
