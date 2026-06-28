<?php

namespace App\Http\Controllers\Planner;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PlannerMobileCustomerImageController extends Controller
{
    /**
     * POST /api/planner/customer-images/upload
     *
     * Called by Nuriva. Saves all task photos into the normal nuri-head
     * customer image gallery: storage/app/uploads/customers + images table.
     */
    public function upload(Request $request): JsonResponse
    {
        if (!Schema::hasTable('images')) {
            return response()->json([
                'ok' => false,
                'message' => 'Die Tabelle images wurde nicht gefunden.',
            ], 500);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => ['required', 'integer', 'exists:new_leads,id'],
            'alternative_id' => ['nullable', 'integer', 'exists:lead_alternative_adds,id'],
            'product_id' => ['nullable', 'integer', 'exists:article_groups,id'],
            'lead_product_list_id' => ['nullable', 'integer'],

            'planner_item_id' => ['nullable', 'integer'],
            'local_task_id' => ['nullable', 'integer'],
            'task_id' => ['nullable', 'integer'],
            'sub_task_id' => ['nullable', 'integer'],
            'phase_id' => ['nullable', 'integer'],

            'source' => ['nullable', 'string', 'max:100'],
            'source_type' => ['nullable', 'string', 'max:100'],
            'source_id' => ['nullable', 'integer'],
            'task_title' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:5000'],
            'stage' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', 'string', 'max:100'],
            'offline_uuid' => ['nullable', 'string', 'max:100'],
            'device_created_at' => ['nullable', 'date'],

            'photos' => ['nullable', 'array'],
            'photos.*' => ['file', 'mimes:jpg,jpeg,png,webp,gif', 'max:25600'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:25600'],
            'file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,gif', 'max:25600'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $files = $this->uploadedImageFiles($request);

        if (empty($files)) {
            return response()->json([
                'ok' => false,
                'message' => 'Bitte mindestens ein Foto auswählen.',
            ], 422);
        }

        $employeeId = $this->authEmployeeId();
        $saved = [];

        DB::beginTransaction();

        try {
            foreach ($files as $file) {
                if (!$file || !$file->isValid()) {
                    continue;
                }

                $originalName = $this->cleanFilename($file->getClientOriginalName());
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'jpg');
                $filename = 'mobile_task_' . now()->format('Ymd_His') . '_' . Str::random(8) . '_' . $originalName;
                $path = $file->storeAs('uploads/customers', $filename, 'local');

                if (!$path || !Storage::disk('local')->exists($path)) {
                    throw new \RuntimeException('Foto konnte nicht gespeichert werden: ' . $originalName);
                }

                $imageId = DB::table('images')->insertGetId(
                    $this->filterForTable('images', [
                        'customer_id' => (int) $request->input('customer_id'),
                        'alternative_id' => $request->filled('alternative_id') ? (int) $request->input('alternative_id') : null,
                        'article_group' => $request->filled('product_id') ? (int) $request->input('product_id') : null,
                        'lead_product_list_id' => $request->filled('lead_product_list_id') ? (int) $request->input('lead_product_list_id') : null,
                        'planner_item_id' => $request->filled('planner_item_id') ? (int) $request->input('planner_item_id') : null,
                        'phase_id' => $request->filled('phase_id') ? (int) $request->input('phase_id') : null,
                        'task_id' => $this->imageTaskId($request),
                        'sub_task_id' => $request->filled('sub_task_id') ? (int) $request->input('sub_task_id') : null,
                        'created_by' => $employeeId,
                        'update_by' => null,
                        'stage' => $request->input('stage', 'mobile_task_photo'),
                        'image_name' => pathinfo($originalName, PATHINFO_FILENAME),
                        'image' => $filename,
                        'file_type' => $extension,
                        'status' => $request->input('status', 'mobile_task_photo'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ])
                );

                $saved[] = [
                    'id' => (int) $imageId,
                    'image_id' => (int) $imageId,
                    'customer_id' => (int) $request->input('customer_id'),
                    'alternative_id' => $request->filled('alternative_id') ? (int) $request->input('alternative_id') : null,
                    'product_id' => $request->filled('product_id') ? (int) $request->input('product_id') : null,
                    'task_id' => $this->imageTaskId($request),
                    'planner_item_id' => $request->filled('planner_item_id') ? (int) $request->input('planner_item_id') : null,
                    'image_name' => pathinfo($originalName, PATHINFO_FILENAME),
                    'image' => $filename,
                    'file_type' => $extension,
                    'url' => $this->secureImageUrl((int) $imageId),
                    'created_at' => now()->format('d.m.Y H:i'),
                ];
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Planner mobile customer image upload failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Fotos konnten nicht in nuri-head gespeichert werden.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }

        return response()->json([
            'ok' => true,
            'success' => true,
            'message' => count($saved) . ' Foto(s) wurden in der Kundenakte gespeichert.',
            'images' => $saved,
        ]);
    }

    /**
     * GET /api/planner/customer-images
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id' => ['required', 'integer'],
            'alternative_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'task_id' => ['nullable', 'integer'],
            'planner_item_id' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $q = DB::table('images')
            ->where('customer_id', (int) $request->input('customer_id'))
            ->when($this->safeColumn('images', 'deleted_at'), fn($query) => $query->whereNull('deleted_at'));

        if ($request->filled('alternative_id')) {
            $q->where('alternative_id', (int) $request->input('alternative_id'));
        }

        if ($request->filled('product_id')) {
            $q->where('article_group', (int) $request->input('product_id'));
        }

        if ($request->filled('task_id')) {
            $q->where('task_id', (int) $request->input('task_id'));
        }

        if ($request->filled('planner_item_id') && $this->safeColumn('images', 'planner_item_id')) {
            $q->where('planner_item_id', (int) $request->input('planner_item_id'));
        }

        $rows = $q->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit((int) $request->input('limit', 100))
            ->get()
            ->map(function ($row) {
                return [
                    'id' => (int) $row->id,
                    'image_id' => (int) $row->id,
                    'image_name' => $row->image_name ?? null,
                    'image' => $row->image ?? null,
                    'file_type' => $row->file_type ?? null,
                    'stage' => $row->stage ?? null,
                    'status' => $row->status ?? null,
                    'customer_id' => isset($row->customer_id) ? (int) $row->customer_id : null,
                    'alternative_id' => isset($row->alternative_id) ? (int) $row->alternative_id : null,
                    'product_id' => isset($row->article_group) ? (int) $row->article_group : null,
                    'task_id' => isset($row->task_id) ? (int) $row->task_id : null,
                    'url' => $this->secureImageUrl((int) $row->id),
                    'created_at' => $row->created_at ?? null,
                ];
            })
            ->values();

        return response()->json([
            'ok' => true,
            'images' => $rows,
        ]);
    }

    private function uploadedImageFiles(Request $request): array
    {
        $files = [];

        foreach (['photos', 'photo', 'file'] as $key) {
            if (!$request->hasFile($key)) {
                continue;
            }

            $value = $request->file($key);
            $files = array_merge($files, is_array($value) ? $value : [$value]);
        }

        return array_values(array_filter($files, fn($file) => $file instanceof UploadedFile));
    }

    private function imageTaskId(Request $request): ?int
    {
        if ($request->filled('task_id')) {
            return (int) $request->input('task_id');
        }

        if ($request->filled('planner_item_id')) {
            return (int) $request->input('planner_item_id');
        }

        if ($request->filled('local_task_id')) {
            return (int) $request->input('local_task_id');
        }

        return null;
    }

    private function authEmployeeId(): ?int
    {
        $user = auth()->user();
        $id = $user?->employee_id ?? $user?->employee?->id ?? $user?->name ?? null;
        return is_numeric($id) ? (int) $id : (auth()->id() ?: null);
    }

    private function cleanFilename(string $name): string
    {
        $name = basename($name ?: 'foto.jpg');
        $name = preg_replace('/[^a-zA-Z0-9\-_\.]/', '_', $name);
        return $name ?: ('foto_' . Str::random(8) . '.jpg');
    }

    private function filterForTable(string $table, array $data): array
    {
        return collect($data)
            ->filter(fn($value, $column) => Schema::hasColumn($table, $column))
            ->all();
    }

    private function safeColumn(string $table, string $column): bool
    {
        return Schema::hasTable($table) && Schema::hasColumn($table, $column);
    }

    private function secureImageUrl(int $imageId): ?string
    {
        if (Route::has('secure.image')) {
            return route('secure.image', $imageId);
        }

        if (Route::has('image.secure')) {
            return route('image.secure', $imageId);
        }

        return null;
    }
}
