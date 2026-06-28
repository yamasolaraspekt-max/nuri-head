<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Models\OfferRoofLayoutConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class OfferRoofLayoutConfigurationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Request $request): JsonResponse
    {
        $configuration = $this->queryForContext($request)->latest('id')->first();

        return response()->json([
            'success' => true,
            'configuration' => $configuration ? $this->resource($configuration) : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'offer_id' => ['nullable', 'integer', 'exists:offers,id'],
            'offer_folder_id' => ['nullable', 'integer', 'exists:offer_folders,id'],
            'offer_detail_id' => ['nullable', 'integer', 'exists:offer_details,id'],
            'offer_template_id' => ['nullable', 'integer', 'exists:offer_templates,id'],

            'enabled' => ['nullable', 'boolean'],
            'title' => ['nullable', 'string', 'max:255'],
            'offer_number' => ['nullable', 'string', 'max:255'],
            'system_power_kwp' => ['nullable', 'string', 'max:50'],
            'module_count' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'module_power_wp' => ['nullable', 'integer', 'min:0', 'max:999999'],

            'selected_roofs' => ['nullable', 'array'],
            'selected_roofs.*' => [
                'string',
                Rule::in([
                    'nord',
                    'nord_west',
                    'west',
                    'nord_ost',
                    'ost',
                    'flachdach',
                    'sued',
                    'sued_ost',
                    'sued_west',
                ]),
            ],
            'show_all_icons' => ['nullable', 'boolean'],
            'compass_image_path' => ['nullable', 'string', 'max:2000'],
            'compass_image_url' => ['nullable', 'string', 'max:10000000'],
            'canvas_layout' => ['nullable'],
            'canvas_design_width' => ['nullable', 'integer', 'min:100', 'max:5000'],
            'canvas_design_height' => ['nullable', 'integer', 'min:100', 'max:5000'],
            'note' => ['nullable', 'string'],
            'footer_company' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ]);

        if (
            empty($validated['offer_id'])
            && empty($validated['offer_folder_id'])
            && empty($validated['offer_detail_id'])
            && empty($validated['offer_template_id'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Bitte zuerst Angebot oder Vorlage speichern, damit die Dachbelegung zugeordnet werden kann.',
            ], 422);
        }

        $employeeId = $this->employeeId();
        $configuration = $this->queryForContext($request)->latest('id')->first();

        if (!$configuration) {
            $configuration = new OfferRoofLayoutConfiguration();
            $configuration->created_by_employee_id = $employeeId;
        }

        $canvasLayout = $this->normalizeCanvasLayout($request->input('canvas_layout'));

        $configuration->fill([
            'offer_id' => $validated['offer_id'] ?? $configuration->offer_id,
            'offer_folder_id' => $validated['offer_folder_id'] ?? $configuration->offer_folder_id,
            'offer_detail_id' => $validated['offer_detail_id'] ?? $configuration->offer_detail_id,
            'offer_template_id' => $validated['offer_template_id'] ?? $configuration->offer_template_id,
            'enabled' => (bool) ($validated['enabled'] ?? false),
            'title' => $validated['title'] ?? 'BELEGUNG DER DACHFLÄCHE',
            'offer_number' => $validated['offer_number'] ?? null,
            'system_power_kwp' => $validated['system_power_kwp'] ?? null,
            'module_count' => $validated['module_count'] ?? null,
            'module_power_wp' => $validated['module_power_wp'] ?? null,
            'selected_roofs' => array_values($validated['selected_roofs'] ?? []),
            'show_all_icons' => (bool) ($validated['show_all_icons'] ?? true),
            'compass_image_path' => $validated['compass_image_path'] ?? null,
            'canvas_layout' => $canvasLayout,
            'canvas_design_width' => (int) ($validated['canvas_design_width'] ?? 1000),
            'canvas_design_height' => (int) ($validated['canvas_design_height'] ?? 700),
            'note' => $validated['note'] ?? null,
            'footer_company' => $validated['footer_company'] ?? null,
            'meta' => $validated['meta'] ?? [],
            'updated_by_employee_id' => $employeeId,
        ]);

        $configuration->save();

        return response()->json([
            'success' => true,
            'message' => 'Dachbelegung gespeichert.',
            'configuration' => $this->resource($configuration->fresh()),
        ]);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'image', 'max:8192'],
        ]);

        $path = $request->file('image')->store('offer-roof-layouts/' . date('Y/m'), 'public');

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }

    protected function queryForContext(Request $request)
    {
        $query = OfferRoofLayoutConfiguration::query();

        if ($request->filled('offer_detail_id')) {
            return $query->where('offer_detail_id', (int) $request->integer('offer_detail_id'));
        }

        if ($request->filled('offer_template_id')) {
            return $query->where('offer_template_id', (int) $request->integer('offer_template_id'));
        }

        if ($request->filled('offer_folder_id')) {
            return $query->where('offer_folder_id', (int) $request->integer('offer_folder_id'));
        }

        if ($request->filled('offer_id')) {
            return $query->where('offer_id', (int) $request->integer('offer_id'));
        }

        return $query->whereRaw('1 = 0');
    }

    protected function resource(?OfferRoofLayoutConfiguration $configuration): ?array
    {
        if (!$configuration) {
            return null;
        }

        return [
            'id' => (int) $configuration->id,
            'offer_id' => $configuration->offer_id ? (int) $configuration->offer_id : null,
            'offer_folder_id' => $configuration->offer_folder_id ? (int) $configuration->offer_folder_id : null,
            'offer_detail_id' => $configuration->offer_detail_id ? (int) $configuration->offer_detail_id : null,
            'offer_template_id' => $configuration->offer_template_id ? (int) $configuration->offer_template_id : null,
            'enabled' => (bool) $configuration->enabled,
            'title' => (string) ($configuration->title ?? 'BELEGUNG DER DACHFLÄCHE'),
            'offer_number' => $configuration->offer_number,
            'system_power_kwp' => $configuration->system_power_kwp,
            'module_count' => $configuration->module_count,
            'module_power_wp' => $configuration->module_power_wp,
            'selected_roofs' => $configuration->selected_roofs ?: [],
            'show_all_icons' => (bool) $configuration->show_all_icons,
            'compass_image_path' => $configuration->compass_image_path,
            'compass_image_url' => $configuration->compass_image_url,
            'canvas_layout' => $configuration->canvas_layout ?: [],
            'canvas_design_width' => (int) ($configuration->canvas_design_width ?: 1000),
            'canvas_design_height' => (int) ($configuration->canvas_design_height ?: 700),
            'note' => $configuration->note,
            'footer_company' => $configuration->footer_company,
            'meta' => $configuration->meta ?: [],
        ];
    }

    protected function normalizeCanvasLayout($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = json_last_error() === JSON_ERROR_NONE ? $decoded : [];
        }

        if (!is_array($value)) {
            return [];
        }

        $items = [];

        foreach ($value as $item) {
            if (!is_array($item)) {
                continue;
            }

            $src = trim((string) ($item['src'] ?? $item['url'] ?? $item['image_url'] ?? ''));
            if ($src === '') {
                continue;
            }

            $items[] = [
                'id' => (string) ($item['id'] ?? ('roof_' . uniqid())),
                'kind' => (string) ($item['kind'] ?? 'image'),
                'roof_type' => $item['roof_type'] ?? null,
                'label' => (string) ($item['label'] ?? ''),
                'src' => $src,
                'path' => (string) ($item['path'] ?? ''),
                'x' => (float) ($item['x'] ?? 60),
                'y' => (float) ($item['y'] ?? 60),
                'width' => max(20, (float) ($item['width'] ?? 120)),
                'height' => max(20, (float) ($item['height'] ?? 120)),
                'rotation' => (float) ($item['rotation'] ?? 0),
                'zIndex' => (int) ($item['zIndex'] ?? $item['z_index'] ?? 1),
                'opacity' => max(0.05, min(1, (float) ($item['opacity'] ?? 1))),
                'objectFit' => (string) ($item['objectFit'] ?? $item['object_fit'] ?? 'contain'),
            ];
        }

        return $items;
    }

    protected function employeeId(): ?int
    {
        $user = auth()->user();

        if (!$user) {
            return null;
        }

        if (is_numeric($user->name ?? null)) {
            return (int) $user->name;
        }

        if (!empty($user->employee_id) && is_numeric($user->employee_id)) {
            return (int) $user->employee_id;
        }

        return is_numeric($user->id ?? null) ? (int) $user->id : null;
    }
}
