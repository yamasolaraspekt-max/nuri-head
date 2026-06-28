<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\DailyReportAttachment;
use App\Models\DailyReportNote;
use App\Models\DailyReportTime;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class DailyReportAttachmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexByContext(Request $req)
    {
        $data = $req->validate([
            'date' => 'required|date',
            'entry_id' => 'nullable|exists:daily_report_times,id',
            'q' => 'nullable|string|max:255',
            'source' => 'nullable|in:all,report,customer',
            'customer_id' => 'nullable|exists:new_leads,id',
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer|exists:new_leads,id',
        ]);

        $employeeId = $this->resolveEmployeeId();
        $entryId = $data['entry_id'] ?? null;
        $search = trim((string) ($data['q'] ?? ''));
        $source = $data['source'] ?? 'all';

        $customerIds = $this->resolveCustomerIds($req, $entryId);

        if (!empty($data['customer_id'])) {
            $customerIds = [(int) $data['customer_id']];
        }

        $items = collect();

        if ($source === 'all' || $source === 'report') {
            $note = $this->getOrCreateContainerNote($employeeId, $data['date'], $entryId);

            $reportItems = $note->attachments()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('original_name', 'like', "%{$search}%")
                            ->orWhere('mime', 'like', "%{$search}%")
                            ->orWhere('path', 'like', "%{$search}%");
                    });
                })
                ->latest('id')
                ->get()
                ->map(fn($a) => [
                    'id' => $a->id,
                    'source' => 'report',
                    'source_label' => 'Bericht-Anhang',
                    'url' => $a->url,
                    'name' => $a->original_name,
                    'mime' => $a->mime,
                    'size' => $a->size,
                    'size_label' => $a->size_label ?? $this->formatBytes((int) $a->size),
                    'ext' => $a->ext,
                    'is_image' => $a->is_image,
                    'customer_id' => null,
                    'image_id' => null,
                ]);

            $items = $items->merge($reportItems);
        }

        if (($source === 'all' || $source === 'customer') && !empty($customerIds)) {
            $customerItems = Image::query()
                ->whereIn('customer_id', $customerIds)
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('image_name', 'like', "%{$search}%")
                            ->orWhere('image', 'like', "%{$search}%")
                            ->orWhere('file_type', 'like', "%{$search}%")
                            ->orWhere('stage', 'like', "%{$search}%")
                            ->orWhere('status', 'like', "%{$search}%");
                    });
                })
                ->latest('id')
                ->get()
                ->map(function ($img) {
                    $fileName = $img->image ?: $img->image_name;
                    $ext = strtolower((string) ($img->file_type ?: pathinfo((string) $fileName, PATHINFO_EXTENSION)));

                    return [
                        'id' => 'image_' . $img->id,
                        'source' => 'customer',
                        'source_label' => 'Kunden-Datei',
                        'url' => $this->customerImageUrl($fileName),
                        'name' => $img->image_name ?: $fileName,
                        'mime' => $this->mimeFromExt($ext),
                        'size' => null,
                        'size_label' => '',
                        'ext' => $ext,
                        'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true),
                        'customer_id' => $img->customer_id,
                        'image_id' => $img->id,
                        'stage' => $img->stage,
                        'status' => $img->status,
                    ];
                });

            $items = $items->merge($customerItems);
        }

        return response()->json([
            'attachments' => $items->values(),
            'customer_ids' => $customerIds,
        ]);
    }

    public function storeByContext(Request $req)
    {
        $req->validate([
            'date' => 'required|date',
            'entry_id' => 'nullable|exists:daily_report_times,id',
            'files' => 'required',
            'files.*' => [
                'required',
                File::types(['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt'])
                    ->max(10 * 1024),
            ],
            'customer_ids' => 'nullable|array',
            'customer_ids.*' => 'integer|exists:new_leads,id',
            'stage_id' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'alternative_id' => 'nullable|integer',
            'product_id' => 'nullable|integer',
        ]);

        $employeeId = $this->resolveEmployeeId();
        $entryId = $req->input('entry_id');

        $note = $this->getOrCreateContainerNote($employeeId, $req->input('date'), $entryId);

        $customerIds = $this->resolveCustomerIds($req, $entryId);

        $disk = 'public';
        $dir = "daily_attachments/{$note->id}/" . date('Y/m');

        $savedAttachments = [];
        $savedImages = [];

        foreach ((array) $req->file('files', []) as $file) {
            /*
             * 1. Always save as Tagesbericht attachment.
             */
            $path = $file->store($dir, $disk);

            $attachment = DailyReportAttachment::create([
                'note_id' => $note->id,
                'employee_id' => $employeeId,
                'disk' => $disk,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);

            $savedAttachments[] = $attachment;

            /*
             * 2. If the row has customer IDs, also save into images table.
             */
            if (!empty($customerIds)) {
                foreach ($customerIds as $customerId) {
                    $savedImages[] = $this->storeCustomerImageFromDailyAttachment(
                        $file,
                        (int) $customerId,
                        $employeeId,
                        $req
                    );
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => !empty($customerIds)
                ? 'Dateien wurden als Bericht-Anhang und Kunden-Datei gespeichert.'
                : 'Dateien wurden als Bericht-Anhang gespeichert.',
            'attachments' => collect($savedAttachments)->map(fn($a) => [
                'id' => $a->id,
                'source' => 'report',
                'source_label' => 'Bericht-Anhang',
                'url' => $a->url,
                'name' => $a->original_name,
                'mime' => $a->mime,
                'size' => $a->size,
                'size_label' => $a->size_label ?? $this->formatBytes((int) $a->size),
                'ext' => $a->ext,
                'is_image' => $a->is_image,
            ])->values(),
            'images' => collect($savedImages)->filter()->values(),
            'customer_ids' => $customerIds,
        ], 201);
    }

    public function destroy(DailyReportAttachment $attachment)
    {
        if ($attachment->path && Storage::disk($attachment->disk ?: 'public')->exists($attachment->path)) {
            Storage::disk($attachment->disk ?: 'public')->delete($attachment->path);
        }

        $attachment->delete();

        return response()->json(['success' => true]);
    }

    private function getOrCreateContainerNote(int $employeeId, string $date, ?string $entryId = null): DailyReportNote
    {
        $report = DailyReport::firstOrCreate(
            [
                'employee_id' => $employeeId,
                'start_date' => $date,
            ],
            [
                'status' => 'started',
            ]
        );

        return DailyReportNote::firstOrCreate(
            [
                'report_id' => $report->id,
                'employee_id' => $employeeId,
                'daily_report_time_id' => $entryId ?: null,
                'report_date' => $date,
                'message' => '',
            ],
            []
        );
    }

    private function resolveEmployeeId(): int
    {
        return (int) auth()->user()->name;
    }

    private function resolveCustomerIds(Request $req, ?string $entryId = null): array
    {
        $ids = collect($req->input('customer_ids', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->filter(fn($id) => $id > 0)
            ->values();

        if ($ids->isNotEmpty()) {
            return $ids->unique()->values()->all();
        }

        if (!$entryId) {
            return [];
        }

        try {
            $entry = DailyReportTime::with('customers')->find($entryId);

            if (!$entry || !$entry->customers) {
                return [];
            }

            return $entry->customers
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->filter(fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Could not resolve customers for daily attachment.', [
                'entry_id' => $entryId,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    private function storeCustomerImageFromDailyAttachment($file, int $customerId, int $employeeId, Request $req): ?array
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $fileName = time() . '_' . uniqid('', true) . '.' . $extension;

            /*
             * Keep this compatible with your existing customer upload logic:
             * storage/app/uploads/customers/{fileName}
             */
            $folder = 'uploads/customers';
            $file->storeAs($folder, $fileName);

            $image = Image::create([
                'customer_id' => $customerId,
                'alternative_id' => $req->input('alternative_id'),
                'article_group' => $req->input('product_id'),
                'stage' => $req->input('stage_id', 'daily_report'),
                'image_name' => $file->getClientOriginalName(),
                'image' => $fileName,
                'status' => $req->input('status', 'daily_report'),
                'created_by' => $employeeId,
                'file_type' => $extension,
            ]);

            return [
                'id' => $image->id,
                'customer_id' => $customerId,
                'name' => $image->image_name,
                'image' => $image->image,
                'file_type' => $image->file_type,
                'stage' => $image->stage,
                'status' => $image->status,
            ];
        } catch (\Throwable $e) {
            Log::error('Could not save daily report attachment into images table.', [
                'customer_id' => $customerId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function customerImageUrl(?string $fileName): string
    {
        if (!$fileName) {
            return '';
        }

        $path = 'uploads/customers/' . ltrim($fileName, '/');

        if (Storage::exists($path)) {
            return Storage::url($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        return url('storage/' . $path);
    }

    private function mimeFromExt(?string $ext): string
    {
        $ext = strtolower((string) $ext);

        return match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'csv' => 'text/csv',
            'txt' => 'text/plain',
            default => 'application/octet-stream',
        };
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1024 * 1024) {
            return number_format($bytes / (1024 * 1024), 2, ',', '.') . ' MB';
        }

        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }
}