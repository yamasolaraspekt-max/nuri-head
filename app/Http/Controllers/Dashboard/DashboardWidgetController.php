<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\DashboardWidget;
use App\Models\UserDashboardSetting;
use App\Models\UserDashboardWidget;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DashboardWidgetController extends Controller
{
    private array $allowedViews = [
        'personal',
        'department',
        'company',
    ];

    private array $allowedThemes = [
        'default',
        'soft',
        'contrast',
    ];

    private array $allowedDensities = [
        'compact',
        'normal',
        'comfortable',
    ];

    private function employeeId(): ?int
    {
        $value = auth()->user()?->name;

        return is_numeric($value) ? (int) $value : null;
    }

    public function load(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nicht angemeldet.',
            ], 401);
        }

        $registry = DashboardWidget::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $setting = UserDashboardSetting::query()
            ->where('user_id', $user->id)
            ->first();

        $savedWidgets = $this->savedWidgetsForUser($user->id);

        if ($savedWidgets->isEmpty()) {
            $this->createDefaultLayoutForUser($user->id, $this->employeeId());
            $savedWidgets = $this->savedWidgetsForUser($user->id);
        }

        return response()->json([
            'success' => true,

            'settings' => [
                'activeView' => $this->safeView($setting?->active_view ?? 'personal'),
                'theme' => $this->safeTheme($setting?->theme ?? 'default'),
                'density' => $this->safeDensity($setting?->density ?? 'normal'),
                'settings' => is_array($setting?->settings) ? $setting->settings : [],
            ],

            'registry' => $registry
                ->map(fn(DashboardWidget $widget) => $this->registryPayload($widget))
                ->values(),

            'layout' => $savedWidgets
                ->filter(function (UserDashboardWidget $item) {
                    return $item->widget || !empty($item->widget_key);
                })
                ->map(fn(UserDashboardWidget $item) => $this->layoutPayload($item))
                ->values(),
        ]);
    }

    public function save(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'activeView' => [
                'nullable',
                'string',
                Rule::in($this->allowedViews),
            ],

            'settings' => ['nullable', 'array'],
            'settings.theme' => [
                'nullable',
                'string',
                Rule::in($this->allowedThemes),
            ],
            'settings.density' => [
                'nullable',
                'string',
                Rule::in($this->allowedDensities),
            ],

            'widgets' => ['nullable', 'array'],

            'widgets.*.instanceKey' => [
                'required',
                'string',
                'max:120',
            ],
            'widgets.*.widgetKey' => [
                'required',
                'string',
                'max:120',
            ],
            'widgets.*.view' => [
                'required',
                'string',
                Rule::in($this->allowedViews),
            ],
            'widgets.*.isVisible' => [
                'required',
                'boolean',
            ],
            'widgets.*.sortOrder' => [
                'required',
                'integer',
                'min:0',
            ],
            'widgets.*.colSpan' => [
                'required',
                'integer',
                'min:1',
                'max:12',
            ],
            'widgets.*.rowSpan' => [
                'required',
                'integer',
                'min:1',
                'max:24',
            ],
            'widgets.*.config' => [
                'nullable',
                'array',
            ],
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nicht angemeldet.',
            ], 401);
        }

        $employeeId = $this->employeeId();

        $incomingWidgets = collect($validated['widgets'] ?? [])
            ->filter(fn($item) => !empty($item['instanceKey']) && !empty($item['widgetKey']))
            ->map(fn($item) => $this->normalizeIncomingWidget($item))
            ->values();

        $incomingWidgetKeys = $incomingWidgets
            ->pluck('widgetKey')
            ->filter()
            ->unique()
            ->values();

        $widgetsByKey = DashboardWidget::query()
            ->whereIn('key', $incomingWidgetKeys)
            ->where('is_active', true)
            ->get()
            ->keyBy('key');

        /*
        |--------------------------------------------------------------------------
        | Save only active registered widgets
        |--------------------------------------------------------------------------
        | If old/static Blade widgets are in the DOM but not in dashboard_widgets,
        | they are skipped instead of breaking the whole save request.
        |--------------------------------------------------------------------------
        */
        $validWidgets = $incomingWidgets
            ->filter(fn($item) => $widgetsByKey->has($item['widgetKey']))
            ->values();

        $skippedWidgets = $incomingWidgets
            ->reject(fn($item) => $widgetsByKey->has($item['widgetKey']))
            ->pluck('widgetKey')
            ->filter()
            ->unique()
            ->values();

        Log::info('DASHBOARD SAVE DEBUG', [
            'user_id' => $user->id,
            'employee_id' => $employeeId,
            'active_view' => $validated['activeView'] ?? 'personal',
            'incoming_count' => $incomingWidgets->count(),
            'valid_count' => $validWidgets->count(),
            'skipped_count' => $skippedWidgets->count(),
            'skipped_widgets' => $skippedWidgets->all(),
        ]);

        DB::transaction(function () use ($validated, $user, $employeeId, $widgetsByKey, $validWidgets) {
            UserDashboardSetting::updateOrCreate(
                [
                    'user_id' => $user->id,
                ],
                [
                    'employee_id' => $employeeId,
                    'active_view' => $this->safeView($validated['activeView'] ?? 'personal'),
                    'theme' => $this->safeTheme(data_get($validated, 'settings.theme', 'default')),
                    'density' => $this->safeDensity(data_get($validated, 'settings.density', 'normal')),
                    'settings' => $validated['settings'] ?? [],
                ]
            );

            $incomingInstanceKeys = $validWidgets
                ->pluck('instanceKey')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $incomingViews = $validWidgets
                ->pluck('view')
                ->filter()
                ->unique()
                ->values()
                ->all();

            /*
            |--------------------------------------------------------------------------
            | Important fix
            |--------------------------------------------------------------------------
            | Delete only widgets from the views currently sent by frontend.
            | The old code deleted every widget not in the current request, including
            | widgets from other tabs/views like department/company.
            |--------------------------------------------------------------------------
            */
            if (!empty($incomingInstanceKeys) && !empty($incomingViews)) {
                UserDashboardWidget::query()
                    ->where('user_id', $user->id)
                    ->whereIn('view', $incomingViews)
                    ->whereNotIn('instance_key', $incomingInstanceKeys)
                    ->delete();
            }

            foreach ($validWidgets as $item) {
                $widget = $widgetsByKey->get($item['widgetKey']);

                if (!$widget) {
                    continue;
                }

                UserDashboardWidget::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'instance_key' => $item['instanceKey'],
                    ],
                    [
                        'employee_id' => $employeeId,
                        'dashboard_widget_id' => $widget->id,
                        'widget_key' => $widget->key,
                        'view' => $item['view'],
                        'is_visible' => (bool) $item['isVisible'],
                        'sort_order' => (int) $item['sortOrder'],
                        'col_span' => (int) $item['colSpan'],
                        'row_span' => (int) $item['rowSpan'],
                        'config' => $item['config'] ?? [],
                    ]
                );
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Dashboard gespeichert.',
            'saved_count' => $validWidgets->count(),
            'skipped_count' => $skippedWidgets->count(),
            'skipped_widgets' => $skippedWidgets,
        ]);
    }

    public function reset(): JsonResponse
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Nicht angemeldet.',
            ], 401);
        }

        DB::transaction(function () use ($user) {
            UserDashboardSetting::query()
                ->where('user_id', $user->id)
                ->delete();

            UserDashboardWidget::query()
                ->where('user_id', $user->id)
                ->delete();

            $this->createDefaultLayoutForUser($user->id, $this->employeeId());
        });

        return response()->json([
            'success' => true,
            'message' => 'Dashboard zurückgesetzt.',
        ]);
    }

    public function registry(): JsonResponse
    {
        $widgets = DashboardWidget::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return response()->json([
            'success' => true,
            'widgets' => $widgets
                ->map(fn(DashboardWidget $widget) => $this->registryPayload($widget))
                ->values(),
        ]);
    }

    private function savedWidgetsForUser(int $userId): Collection
    {
        return UserDashboardWidget::query()
            ->with('widget')
            ->where('user_id', $userId)
            ->orderBy('view')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    private function createDefaultLayoutForUser(int $userId, ?int $employeeId): void
    {
        $widgets = DashboardWidget::query()
            ->where('is_active', true)
            ->where('default_view', '!=', 'global')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($widgets as $index => $widget) {
            UserDashboardWidget::updateOrCreate(
                [
                    'user_id' => $userId,
                    'instance_key' => $widget->key,
                ],
                [
                    'employee_id' => $employeeId,
                    'dashboard_widget_id' => $widget->id,
                    'widget_key' => $widget->key,
                    'view' => $this->safeView($widget->default_view ?? 'personal'),
                    'is_visible' => true,
                    'sort_order' => $index + 1,
                    'col_span' => (int) ($widget->default_col_span ?: 4),
                    'row_span' => (int) ($widget->default_row_span ?: 4),
                    'config' => [],
                ]
            );
        }

        UserDashboardSetting::updateOrCreate(
            [
                'user_id' => $userId,
            ],
            [
                'employee_id' => $employeeId,
                'active_view' => 'personal',
                'theme' => 'default',
                'density' => 'normal',
                'settings' => [],
            ]
        );
    }

    private function registryPayload(DashboardWidget $widget): array
    {
        return [
            'id' => $widget->id,
            'key' => $widget->key,
            'title' => $widget->title,
            'subtitle' => $widget->subtitle,
            'icon' => $widget->icon,
            'color' => $widget->color,
            'defaultView' => $this->safeView($widget->default_view ?? 'personal'),
            'tags' => is_array($widget->tags) ? $widget->tags : [],
            'defaultColSpan' => (int) ($widget->default_col_span ?: 4),
            'defaultRowSpan' => (int) ($widget->default_row_span ?: 4),
            'allowMultiple' => (bool) $widget->allow_multiple,
            'component' => $widget->component,
            'meta' => is_array($widget->meta) ? $widget->meta : [],
        ];
    }

    private function layoutPayload(UserDashboardWidget $item): array
    {
        return [
            'id' => $item->id,
            'instanceKey' => $item->instance_key,
            'widgetKey' => $item->widget?->key ?: $item->widget_key,
            'view' => $this->safeView($item->view ?? 'personal'),
            'isVisible' => (bool) $item->is_visible,
            'sortOrder' => (int) $item->sort_order,
            'colSpan' => (int) $item->col_span,
            'rowSpan' => (int) $item->row_span,
            'config' => is_array($item->config) ? $item->config : [],
        ];
    }

    private function normalizeIncomingWidget(array $item): array
    {
        $instanceKey = trim((string) ($item['instanceKey'] ?? ''));
        $widgetKey = trim((string) ($item['widgetKey'] ?? ''));

        return [
            'instanceKey' => $instanceKey,
            'widgetKey' => $widgetKey,
            'view' => $this->safeView($item['view'] ?? 'personal'),
            'isVisible' => (bool) ($item['isVisible'] ?? true),
            'sortOrder' => max(0, (int) ($item['sortOrder'] ?? 0)),
            'colSpan' => min(12, max(1, (int) ($item['colSpan'] ?? 4))),
            'rowSpan' => min(24, max(1, (int) ($item['rowSpan'] ?? 4))),
            'config' => is_array($item['config'] ?? null) ? $item['config'] : [],
        ];
    }

    private function safeView(?string $view): string
    {
        return in_array($view, $this->allowedViews, true)
            ? $view
            : 'personal';
    }

    private function safeTheme(?string $theme): string
    {
        return in_array($theme, $this->allowedThemes, true)
            ? $theme
            : 'default';
    }

    private function safeDensity(?string $density): string
    {
        return in_array($density, $this->allowedDensities, true)
            ? $density
            : 'normal';
    }
}