<?php

namespace App\Http\Controllers\Customer\Offer;


use App\Http\Controllers\Controller;
use App\Models\ArticleGroup;
use App\Models\Brand;
use App\Models\Department;
use App\Models\Distributor;
use App\Models\Employee;
use App\Models\OfferTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class OfferTemplateController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        // MASTER-01 P1-IDOR Customer: Belegkette-Rollen-Gate (permission:Customer)
        $this->middleware('permission:Customer,update')->only(['update']);
        $this->middleware('permission:Customer,delete')->only(['destroy']);
    }

    public function getOptions(): JsonResponse
    {
        $departments = Department::query()
            ->select('id', 'department_name')
            ->orderBy('department_name')
            ->get();

        $brands = Brand::query()
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $distributors = Distributor::query()
            ->select('id', 'name', 'short_name')
            ->orderBy('name')
            ->get();

        $articleGroups = ArticleGroup::query()
            ->select('id', 'article_group')
            ->orderBy('article_group')
            ->get();

        $employees = Employee::query()
            ->select('id', 'name', 'lastname')
            ->where(function ($q) {
                $q->whereNotNull('name')
                    ->orWhereNotNull('lastname');
            })
            ->orderBy('name')
            ->orderBy('lastname')
            ->get()
            ->map(function (Employee $employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->name ?? '',
                    'lastname' => $employee->lastname ?? '',
                    'full_name' => $this->fullEmployeeName($employee) ?: ('Mitarbeiter #' . $employee->id),
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'departments' => $departments,
            'brands' => $brands,
            'distributors' => $distributors,
            'article_groups' => $articleGroups,
            'employees' => $employees,
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
        $usageFilter = (string) $request->get('usage_filter', '');
        $favoriteOnly = (string) $request->get('favorite_only', '');
        $stampedOnly = (string) $request->get('stamped_only', '');
        $employeeFilter = $request->filled('employee_id') ? (int) $request->get('employee_id') : null;

        // 1. Get the current logged-in employee ID
        $currentEmployeeId = $this->resolveEmployeeId();

        $query = OfferTemplate::query()
            ->with([
                'creator:id,name,lastname',
                'department:id,department_name',
                'articleGroup:id,article_group',
                'brand:id,name',
                'distributor:id,name,short_name',
                'stampedByEmployee:id,name,lastname',
                'lastUsedByEmployee:id,name,lastname',
            ])
            // 2. Check if the current user has favorited this template
            ->withExists([
                'favoritedByEmployees as is_current_user_favorite' => function ($q) use ($currentEmployeeId) {
                    $q->where('employees.id', $currentEmployeeId);
                }
            ])
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%")
                        ->orWhere('company_name', 'like', "%{$q}%")
                        ->orWhere('stamp', 'like', "%{$q}%");
                });
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
            })
            ->when($request->filled('article_group_id'), function ($query) use ($request) {
                $query->where('article_group_id', $request->article_group_id);
            })
            ->when($request->filled('brand_id'), function ($query) use ($request) {
                $query->where('brand_id', $request->brand_id);
            })
            ->when($request->filled('distributor_id'), function ($query) use ($request) {
                $query->where('distributor_id', $request->distributor_id);
            });

        if ($employeeFilter) {
            $this->applyEmployeeFilters(
                $query,
                $employeeFilter,
                $favoriteOnly,
                $stampedOnly,
                $usageFilter
            );
        } else {
            $this->applyGlobalStatusFilters(
                $query,
                $favoriteOnly,
                $stampedOnly,
                $usageFilter
            );
        }

        $this->applySorting($query, $usageFilter);

        $items = $query
            ->limit(100)
            ->get()
            ->map(fn(OfferTemplate $template) => $this->transformTemplateForList($template))
            ->values();

        return response()->json([
            'success' => true,
            'count' => $items->count(),
            'items' => $items,
        ]);
    }


    public function show(OfferTemplate $template): JsonResponse
    {
        $currentEmployeeId = $this->resolveEmployeeId();

        $template->load([
            'creator:id,name,lastname',
            'department:id,department_name',
            'articleGroup:id,article_group',
            'brand:id,name',
            'distributor:id,name,short_name',
            // Removed the old favoriteByEmployee here
            'stampedByEmployee:id,name,lastname',
            'lastUsedByEmployee:id,name,lastname',
        ]);

        $creatorName = $this->fullEmployeeName($template->creator);

        $sections = is_array($template->sections)
            ? $template->sections
            : (json_decode((string) $template->sections, true) ?: []);

        $placedImages = is_array($template->placed_images)
            ? $template->placed_images
            : (json_decode((string) $template->placed_images, true) ?: []);

        $biographyData = is_array($template->biography_data)
            ? $template->biography_data
            : (json_decode((string) $template->biography_data, true) ?: []);

        // Check if the current user has favorited this template
        $isFavorite = $template->favoritedByEmployees()->where('employees.id', $currentEmployeeId)->exists();

        return response()->json([
            'success' => true,
            'template' => [
                'id' => $template->id,
                'name' => $template->name,
                'description' => $template->description,
                'brand_color' => $template->brand_color ?: '#93c21c',
                'brand_mode' => $template->brand_mode ?: 'text',
                'brand_logo_url' => $template->brand_logo_url,
                'company_name' => $template->company_name ?: 'SOLAR ASPEKT',
                'cover_text_html' => $template->cover_text_html,
                'sections' => $sections,
                'placed_images' => $placedImages,
                'biography_data' => $biographyData,
                'creator_id' => $template->created_by,
                'creator_name' => $creatorName ?: 'System',
                'updated_at' => optional($template->updated_at)->format('d.m.Y H:i'),

                'department_id' => $template->department_id,
                'article_group_id' => $template->article_group_id,
                'brand_id' => $template->brand_id,
                'distributor_id' => $template->distributor_id,
                'department_name' => $template->department->department_name ?? 'Keine Abteilung',
                'article_group_name' => $template->articleGroup->article_group ?? 'Keine Gruppe',
                'brand_name' => $template->brand->name ?? 'Keine Marke',
                'distributor_name' => $template->distributor->name ?? 'Kein Lieferant',

                'is_favorite' => $isFavorite,
                'stamp' => $template->stamp,
                'has_stamp' => filled($template->stamp),
                'usage_count' => (int) $template->usage_count,

                // We set these to null because a template can now have multiple favorites
                'favorite_by_employee_id' => null,
                'favorite_by_employee_name' => null,
                'favorite_at' => null,

                'stamped_by_employee_id' => $template->stamped_by_employee_id,
                'stamped_by_employee_name' => $this->fullEmployeeName($template->stampedByEmployee),
                'stamped_at' => optional($template->stamped_at)->format('d.m.Y H:i'),

                'last_used_by_employee_id' => $template->last_used_by_employee_id,
                'last_used_by_employee_name' => $this->fullEmployeeName($template->lastUsedByEmployee),
                'last_used_at' => optional($template->last_used_at)->format('d.m.Y H:i'),
            ],
        ]);
    }

    public function toggleFavorite(OfferTemplate $template): JsonResponse
    {
        try {
            $employeeId = $this->resolveEmployeeId();

            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kein Mitarbeiter konnte ermittelt werden.',
                ], 403);
            }

            // Toggle the pivot table entry
            $toggled = $template->favoritedByEmployees()->toggle($employeeId);

            // If the ID is in the 'attached' array, it means it was just favorited
            $isFavorite = count($toggled['attached']) > 0;

            return response()->json([
                'success' => true,
                'message' => $isFavorite ? 'Vorlage als Favorit markiert.' : 'Favorit entfernt.',
                'is_favorite' => $isFavorite,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Favorit konnte nicht geändert werden: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleStamp(Request $request, OfferTemplate $template): JsonResponse
    {
        try {
            $employeeId = $this->resolveEmployeeId();

            if (!$employeeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kein Mitarbeiter konnte ermittelt werden.',
                ], 403);
            }

            $hasExistingStamp = filled($template->stamp);
            $lockedByAnotherEmployee =
                $hasExistingStamp &&
                !empty($template->stamped_by_employee_id) &&
                (int) $template->stamped_by_employee_id !== (int) $employeeId;

            if ($lockedByAnotherEmployee) {
                $template->load('stampedByEmployee:id,name,lastname');

                return response()->json([
                    'success' => false,
                    'message' => 'Diese Vorlage ist bereits von ' .
                        ($this->fullEmployeeName($template->stampedByEmployee) ?: 'einem anderen Mitarbeiter') .
                        ' gestempelt und kann nicht überschrieben werden.',
                ], 409);
            }

            if ($hasExistingStamp) {
                $template->stamp = null;
                $template->stamped_by_employee_id = null;
                $template->stamped_at = null;
            } else {
                $template->stamp = trim((string) $request->input('stamp', 'Gestempelt')) ?: 'Gestempelt';
                $template->stamped_by_employee_id = $employeeId;
                $template->stamped_at = now();
            }

            $template->save();
            $template->load('stampedByEmployee:id,name,lastname');

            return response()->json([
                'success' => true,
                'message' => filled($template->stamp) ? 'Stempel gesetzt.' : 'Stempel entfernt.',
                'stamp' => $template->stamp,
                'has_stamp' => filled($template->stamp),
                'stamped_by_employee_id' => $template->stamped_by_employee_id,
                'stamped_by_employee_name' => $this->fullEmployeeName($template->stampedByEmployee),
                'stamped_at' => optional($template->stamped_at)->format('d.m.Y H:i'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stempel konnte nicht geändert werden: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function markUsed(OfferTemplate $template): JsonResponse
    {
        try {
            $employeeId = $this->resolveEmployeeId();

            $template->increment('usage_count');

            $template->last_used_by_employee_id = $employeeId;
            $template->last_used_at = now();
            $template->save();

            $template->load('lastUsedByEmployee:id,name,lastname');

            return response()->json([
                'success' => true,
                'usage_count' => (int) $template->fresh()->usage_count,
                'last_used_by_employee_id' => $template->last_used_by_employee_id,
                'last_used_by_employee_name' => $this->fullEmployeeName($template->lastUsedByEmployee),
                'last_used_at' => optional($template->last_used_at)->format('d.m.Y H:i'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verwendungszähler konnte nicht erhöht werden: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function usageHistory(OfferTemplate $template): JsonResponse
    {
        $template->load([
            'creator:id,name,lastname',
            'favoritedByEmployees', // Load the new many-to-many relationship
            'stampedByEmployee:id,name,lastname',
            'lastUsedByEmployee:id,name,lastname',
        ]);

        $items = [];

        // Loop through all employees who favorited this template
        foreach ($template->favoritedByEmployees as $favEmployee) {
            $items[] = [
                'type' => 'favorite',
                'employee_name' => $this->fullEmployeeName($favEmployee) ?: 'Unbekannt',
                'employee_email' => '',
                'used_date' => optional($favEmployee->pivot->created_at)->format('d.m.Y'),
                'used_time' => optional($favEmployee->pivot->created_at)->format('H:i'),
                'note' => 'Vorlage als Favorit markiert',
            ];
        }

        if ($template->stamped_at && $template->stampedByEmployee) {
            $items[] = [
                'type' => 'stamp',
                'employee_name' => $this->fullEmployeeName($template->stampedByEmployee) ?: 'Unbekannt',
                'employee_email' => '',
                'used_date' => optional($template->stamped_at)->format('d.m.Y'),
                'used_time' => optional($template->stamped_at)->format('H:i'),
                'note' => 'Vorlage gestempelt: ' . ($template->stamp ?: 'Gestempelt'),
            ];
        }

        if ($template->last_used_at) {
            $items[] = [
                'type' => 'usage',
                'employee_name' => $this->fullEmployeeName($template->lastUsedByEmployee) ?: 'Unbekannt',
                'employee_email' => '',
                'used_date' => optional($template->last_used_at)->format('d.m.Y'),
                'used_time' => optional($template->last_used_at)->format('H:i'),
                'note' => 'Letzte bekannte Verwendung',
            ];
        }

        usort($items, function ($a, $b) {
            $aKey = ($a['used_date'] ?? '') . ' ' . ($a['used_time'] ?? '');
            $bKey = ($b['used_date'] ?? '') . ' ' . ($b['used_time'] ?? '');
            return strcmp($bKey, $aKey);
        });

        return response()->json([
            'success' => true,
            'total_usage_count' => (int) $template->usage_count,
            'items' => array_values($items),
        ]);
    }

    public function update(Request $request, OfferTemplate $template): JsonResponse
    {
        try {
            $employeeId = $this->resolveEmployeeId();
            $user = Auth::user();
            $employeeName = $this->fullEmployeeName($user->employee ?? null) ?: 'System';

            // 1. Update basic fields
            // Important: when the editor opens in template mode, empty/null fields must NOT wipe existing metadata.
            if ($request->filled('template_name')) {
                $template->name = trim((string) $request->input('template_name'));
            }

            if ($request->exists('template_description')) {
                $template->description = (string) $request->input('template_description', '');
            }

            if ($request->filled('department_id')) {
                $template->department_id = (int) $request->input('department_id');
            }

            if ($request->filled('article_group_id')) {
                $template->article_group_id = (int) $request->input('article_group_id');
            }

            if ($request->filled('brand_id')) {
                $template->brand_id = (int) $request->input('brand_id');
            }

            if ($request->filled('distributor_id')) {
                $template->distributor_id = (int) $request->input('distributor_id');
            }

            // 2. Update editor content
            if ($request->exists('cover_text')) {
                $template->cover_text_html = $request->input('cover_text');
            }

            if ($request->has('sections') && is_array($request->input('sections'))) {
                $template->sections = $request->input('sections');
            }

            if ($request->has('canvas_images') && is_array($request->input('canvas_images'))) {
                $template->placed_images = $request->input('canvas_images');
            }

            if ($request->has('branding')) {
                $branding = $request->input('branding');
                $template->brand_color = $branding['color'] ?? $template->brand_color;
                $template->brand_mode = $branding['mode'] ?? $template->brand_mode;
                $template->brand_logo_url = $branding['logo'] ?? $template->brand_logo_url;
                $template->company_name = $branding['company'] ?? $template->company_name;
            }

            // 3. Update Biography Data (History)
            $bio = is_array($template->biography_data) ? $template->biography_data : [];
            $bio[] = [
                'type' => 'update',
                'employee_id' => $employeeId,
                'employee_name' => $employeeName,
                'date' => now()->format('Y-m-d H:i:s'),
                'note' => 'Vorlage aktualisiert'
            ];
            $template->biography_data = $bio;

            $template->save();

            return response()->json([
                'success' => true,
                'message' => 'Vorlage erfolgreich aktualisiert.',
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name
                ]
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren der Vorlage: ' . $e->getMessage()
            ], 500);
        }
    }


    public function destroy(OfferTemplate $template): JsonResponse
    {
        try {
            $template->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vorlage erfolgreich gelöscht.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen der Vorlage: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function applyEmployeeFilters($query, int $employeeFilter, string $favoriteOnly, string $stampedOnly, string $usageFilter): void
    {
        $hasFavoriteMode = $favoriteOnly === '1';
        $hasStampedMode = $stampedOnly === '1';
        $hasUsageMode = in_array($usageFilter, ['most_used', 'least_used', 'unused'], true);

        if ($hasFavoriteMode) {
            $query->where('is_favorite', true)
                ->where('favorite_by_employee_id', $employeeFilter);
            return;
        }

        if ($hasStampedMode) {
            $query->whereNotNull('stamp')
                ->where('stamp', '!=', '')
                ->where('stamped_by_employee_id', $employeeFilter);
            return;
        }

        if ($hasUsageMode) {
            if ($usageFilter === 'unused') {
                $query->where(function ($sub) use ($employeeFilter) {
                    $sub->where(function ($q) use ($employeeFilter) {
                        $q->where('created_by', $employeeFilter)
                            ->where(function ($inner) {
                                $inner->where('usage_count', 0)
                                    ->orWhereNull('usage_count');
                            });
                    })->orWhere(function ($q) use ($employeeFilter) {
                        $q->where('last_used_by_employee_id', $employeeFilter)
                            ->where(function ($inner) {
                                $inner->where('usage_count', 0)
                                    ->orWhereNull('usage_count');
                            });
                    });
                });
            } else {
                $query->where('last_used_by_employee_id', $employeeFilter);
            }
            return;
        }

        $query->where(function ($sub) use ($employeeFilter) {
            $sub->where('created_by', $employeeFilter)
                ->orWhere('favorite_by_employee_id', $employeeFilter)
                ->orWhere('stamped_by_employee_id', $employeeFilter)
                ->orWhere('last_used_by_employee_id', $employeeFilter);
        });
    }

    protected function applyGlobalStatusFilters($query, string $favoriteOnly, string $stampedOnly, string $usageFilter): void
    {
        if ($favoriteOnly !== '') {
            if ($favoriteOnly === '1') {
                $query->where('is_favorite', true);
            } elseif ($favoriteOnly === '0') {
                $query->where(function ($sub) {
                    $sub->where('is_favorite', false)
                        ->orWhereNull('is_favorite');
                });
            }
        }

        if ($stampedOnly !== '') {
            if ($stampedOnly === '1') {
                $query->whereNotNull('stamp')
                    ->where('stamp', '!=', '');
            } elseif ($stampedOnly === '0') {
                $query->where(function ($sub) {
                    $sub->whereNull('stamp')
                        ->orWhere('stamp', '');
                });
            }
        }

        if ($usageFilter === 'unused') {
            $query->where(function ($sub) {
                $sub->where('usage_count', 0)
                    ->orWhereNull('usage_count');
            });
        }
    }

    protected function applySorting($query, string $usageFilter): void
    {
        if ($usageFilter === 'most_used') {
            $query->orderByDesc('usage_count')
                ->orderByDesc('updated_at');
            return;
        }

        if ($usageFilter === 'least_used') {
            $query->orderBy('usage_count')
                ->orderByDesc('updated_at');
            return;
        }

        // Sort by the current user's favorite status first, then by updated_at
        $query->orderByDesc('is_current_user_favorite')
            ->orderByDesc('updated_at');
    }
    protected function transformTemplateForList(OfferTemplate $template): array
    {
        $sections = is_array($template->sections) ? $template->sections : [];
        $placedImages = is_array($template->placed_images) ? $template->placed_images : [];

        $itemCount = collect($sections)->sum(function ($section) {
            return count($section['items'] ?? []);
        });

        $previewSource = $template->description ?: strip_tags((string) $template->cover_text_html);
        $previewText = Str::limit($previewSource, 140);

        return [
            'id' => $template->id,
            'name' => $template->name,
            'description' => $template->description,
            'company_name' => $template->company_name ?: 'Keine Angabe',

            'brand_color' => $template->brand_color ?: '#93c21c',
            'brand_mode' => $template->brand_mode ?: 'text',
            'brand_logo_url' => $template->brand_logo_url,

            'section_count' => count($sections),
            'item_count' => $itemCount,
            'placed_images_count' => count($placedImages),

            'creator_id' => $template->created_by,
            'creator_name' => $this->fullEmployeeName($template->creator) ?: 'System',
            'updated_at' => optional($template->updated_at)->format('d.m.Y H:i'),
            'preview_text' => $previewText,

            'department_id' => $template->department_id,
            'article_group_id' => $template->article_group_id,
            'brand_id' => $template->brand_id,
            'distributor_id' => $template->distributor_id,

            'department_name' => $template->department->department_name ?? 'Keine Abteilung',
            'article_group_name' => $template->articleGroup->article_group ?? 'Keine Gruppe',
            'brand_name' => $template->brand->name ?? 'Keine Marke',
            'distributor_name' => $template->distributor->name ?? 'Kein Lieferant',

            'is_favorite' => (bool) $template->is_current_user_favorite,
            'stamp' => $template->stamp,
            'has_stamp' => filled($template->stamp),
            'usage_count' => (int) $template->usage_count,

            // Nullified because favorites are now a multi-user pivot array
            'favorite_by_employee_id' => null,
            'favorite_by_employee_name' => null,
            'favorite_at' => null,

            'stamped_by_employee_id' => $template->stamped_by_employee_id,
            'stamped_by_employee_name' => $this->fullEmployeeName($template->stampedByEmployee),
            'stamped_at' => optional($template->stamped_at)->format('d.m.Y H:i'),

            'last_used_by_employee_id' => $template->last_used_by_employee_id,
            'last_used_by_employee_name' => $this->fullEmployeeName($template->lastUsedByEmployee),
            'last_used_at' => optional($template->last_used_at)->format('d.m.Y H:i'),
        ];
    }

    protected function fullEmployeeName($employee): ?string
    {
        if (!$employee) {
            return null;
        }

        $name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));

        return $name !== '' ? $name : null;
    }

    protected function resolveEmployeeId(): ?int
    {
        $user = Auth::user();

        if (!$user) {
            return null;
        }

        if (isset($user->employee) && $user->employee) {
            return (int) $user->employee->id;
        }

        if (isset($user->employee_id) && $user->employee_id) {
            return (int) $user->employee_id;
        }

        return (int) $user->id;
    }

    public function wizardSearch(Request $request)
    {
        $query = trim((string) $request->get('q', ''));
        $articleGroupId = $request->get('article_group_id');

        $templates = OfferTemplate::query()
            ->with(['articleGroup:id,article_group', 'brand:id,name', 'distributor:id,name'])
            ->when($articleGroupId, function ($q) use ($articleGroupId) {
                $q->where('article_group_id', $articleGroupId);
            })
            ->when($query !== '', function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('name', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('company_name', 'like', "%{$query}%")
                        ->orWhere('cover_text_html', 'like', "%{$query}%")
                        ->orWhere('sections', 'like', "%{$query}%");
                });
            })
            ->latest()
            ->limit(30)
            ->get();

        $items = $templates->map(function ($template) use ($query) {
            $sections = $template->sections ?? [];

            if (is_string($sections)) {
                $sections = json_decode($sections, true) ?: [];
            }

            $summary = $this->buildWizardTemplateSummary($sections);

            return [
                'id' => $template->id,
                'name' => $template->name ?? 'Ohne Titel',
                'description' => $template->description ?? '',
                'company_name' => $template->company_name ?? '',

                'article_group_id' => $template->article_group_id ?? null,
                'article_group_name' => optional($template->articleGroup)->article_group ?? 'Keine Gruppe',
                'brand_name' => optional($template->brand)->name ?? 'Keine Marke',
                'distributor_name' => optional($template->distributor)->name ?? 'Kein Lieferant',

                'is_favorite' => (bool) ($template->is_current_user_favorite ?? false),
                'has_stamp' => filled($template->stamp),
                'usage_count' => (int) ($template->usage_count ?? 0),

                'match_score' => $this->calculateWizardTemplateScore($template, $query),
                'match_reasons' => $this->buildWizardMatchReasons($template, $query),
                'summary' => $summary,
            ];
        })
            ->sortByDesc('match_score')
            ->values();

        return response()->json([
            'success' => true,
            'count' => $items->count(),
            'items' => $items,
        ]);
    }
    public function wizardShow($id)
    {
        $template = OfferTemplate::query()
            ->with(['articleGroup', 'brand', 'distributor'])
            ->findOrFail($id);

        $sections = $template->sections ?? [];

        if (is_string($sections)) {
            $sections = json_decode($sections, true) ?: [];
        }

        return response()->json([
            'template' => [
                'id' => $template->id,
                'name' => $template->name ?? 'Ohne Titel',
                'description' => $template->description ?? '',
                'company_name' => $template->company_name ?? '',
                'article_group_id' => $template->article_group_id ?? null,
                'article_group_name' => optional($template->articleGroup)->article_group ?? '',
                'brand_name' => optional($template->brand)->name ?? '',
                'distributor_name' => optional($template->distributor)->name ?? '',
                'sections' => $sections,
                'summary' => $this->buildWizardTemplateSummary($sections),
            ],
        ]);
    }
    private function buildWizardTemplateSummary(array $sections): array
    {
        $sectionCount = count($sections);
        $itemCount = 0;
        $materialNet = 0;
        $laborNet = 0;
        $totalNet = 0;

        foreach ($sections as $section) {
            $items = $section['items'] ?? [];

            foreach ($items as $item) {
                $this->sumWizardItem($item, $itemCount, $materialNet, $laborNet, $totalNet);
            }
        }

        return [
            'section_count' => $sectionCount,
            'item_count' => $itemCount,
            'material_net' => round($materialNet, 2),
            'labor_net' => round($laborNet, 2),
            'total_net' => round($totalNet, 2),
        ];
    }

    private function sumWizardItem(array $item, int &$itemCount, float &$materialNet, float &$laborNet, float &$totalNet): void
    {
        $itemCount++;

        $qty = (float) ($item['qty'] ?? 1);
        $price = (float) ($item['price'] ?? $item['rate'] ?? 0);
        $total = (float) ($item['total'] ?? ($qty * $price));

        $type = strtolower((string) ($item['kind'] ?? $item['item_type'] ?? ''));

        if (str_contains($type, 'labor') || str_contains($type, 'lohn')) {
            $laborNet += $total;
        } else {
            $materialNet += $total;
        }

        $totalNet += $total;

        foreach (($item['subItems'] ?? []) as $child) {
            if (is_array($child)) {
                $this->sumWizardItem($child, $itemCount, $materialNet, $laborNet, $totalNet);
            }
        }
    }

    private function calculateWizardTemplateScore($template, string $query): int
    {
        if ($query === '') {
            return 50;
        }

        $haystack = strtolower(implode(' ', [
            $template->name ?? '',
            $template->description ?? '',
            $template->company_name ?? '',
            $template->sections ?? '',
            $template->raw_snapshot ?? '',
            optional($template->articleGroup)->article_group ?? '',
            optional($template->brand)->name ?? '',
            optional($template->distributor)->name ?? '',
        ]));

        $words = collect(preg_split('/\s+/', strtolower($query)))
            ->filter(fn($word) => mb_strlen($word) >= 3)
            ->unique()
            ->values();

        if ($words->isEmpty()) {
            return 50;
        }

        $hits = 0;

        foreach ($words as $word) {
            if (str_contains($haystack, $word)) {
                $hits++;
            }
        }

        $score = (int) round(($hits / max($words->count(), 1)) * 100);

        return max(35, min(100, $score));
    }

    private function buildWizardMatchReasons($template, string $query): array
    {
        $reasons = [];

        if ($template->article_group_id) {
            $reasons[] = 'Gewerk passt';
        }

        if (!empty($template->sections)) {
            $reasons[] = 'Enthält gespeicherte Sections';
        }

        if (!empty($template->brand_id)) {
            $reasons[] = 'Hersteller vorhanden';
        }

        if ($query !== '') {
            $reasons[] = 'Suchbegriffe gefunden';
        }

        return array_slice($reasons, 0, 4);
    }
}