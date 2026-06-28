<?php

namespace App\Http\Controllers\Customer\Deal;
use App\Http\Controllers\Controller;

use App\Models\DealMeasurement;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DealMeasurementImageController extends Controller
{
    private const STAGE = 'Feisnaufmass';

    /**
     * Load Feinaufmaß images.
     *
     * Important:
     * - image column stores only filename.
     * - file is stored in storage/app/uploads/customers/{filename}.
     * - article_group stores product_id because images table has no product_id.
     */
    public function index(DealMeasurement $measurement)
    {
        $measurement->load(['customer', 'alternative', 'product']);

        $customerId = $measurement->customer_id
            ?: optional($measurement->customer)->id
            ?: optional($measurement->alternative)->lead_id;

        $alternativeId = $measurement->alternative_id
            ?: optional($measurement->alternative)->id;

        $productId = $measurement->product_id
            ?: optional($measurement->product)->id;

        $images = Image::query()
            ->where('stage', self::STAGE)
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->when($alternativeId, function ($query) use ($alternativeId) {
                $query->where('alternative_id', $alternativeId);
            })
            ->when($productId, function ($query) use ($productId) {
                $query->where('article_group', $productId);
            })
            ->whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn(Image $image) => $this->formatImage($image))
            ->values();

        return response()->json([
            'success' => true,
            'images' => $images,
        ]);
    }

    /**
     * Upload image from DealMeasurement.
     *
     * Saves file into:
     * storage/app/uploads/customers/{filename}
     *
     * Saves DB image column as:
     * {filename}
     */
    public function upload(Request $request, DealMeasurement $measurement)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'image_name' => ['nullable', 'string', 'max:255'],
        ]);
        if ($measurement->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Dieses Aufmaß ist abgeschlossen und gesperrt. Bitte zuerst entsperren.',
            ], 423);
        }

        $measurement->load(['customer', 'alternative', 'product']);

        $customerId = $measurement->customer_id
            ?: optional($measurement->customer)->id
            ?: optional($measurement->alternative)->lead_id;

        $alternativeId = $measurement->alternative_id
            ?: optional($measurement->alternative)->id;

        $productId = $measurement->product_id
            ?: optional($measurement->product)->id;

        if (!$customerId) {
            return response()->json([
                'success' => false,
                'message' => 'Customer-ID wurde nicht gefunden.',
            ], 422);
        }

        $file = $request->file('image');

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            $extension = 'jpg';
        }

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeOriginalName = Str::slug($originalName) ?: 'screenshot';

        /*
         * Example:
         * screenshot_1769677727_697b239fe05f7.png
         */
        $filename = $safeOriginalName . '_' . time() . '_' . Str::lower(Str::random(12)) . '.' . $extension;

        /*
         * Same as your normal ImageController:
         * storage/app/uploads/customers/{filename}
         */
        $path = $file->storeAs('uploads/customers', $filename);

        if (!$path || !Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Bild konnte nicht gespeichert werden.',
            ], 500);
        }

        $image = Image::create([
            'customer_id' => $customerId,
            'alternative_id' => $alternativeId,

            /*
             * Your table has no product_id.
             * Store product_id inside article_group.
             */
            'article_group' => $productId,

            'phase_id' => null,
            'task_id' => null,
            'sub_task_id' => null,

            'created_by' => auth()->user()->name ?? auth()->id() ?? 'system',
            'update_by' => auth()->user()->name ?? auth()->id() ?? 'system',

            'stage' => self::STAGE,

            /*
             * Keep image_name readable.
             * image column must be only filename.
             */
            'image_name' => $request->input('image_name')
                ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),

            'image' => $filename,
            'file_type' => $extension,
            'status' => 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bild wurde gespeichert.',
            'image' => $this->formatImage($image),
        ]);
    }

    /**
     * Delete image file and DB row.
     */
    public function destroy(Image $image)
    {
        $filename = ltrim((string) $image->image, '/');
        $path = 'uploads/customers/' . $filename;

        if ($filename && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }

        $image->update_by = auth()->user()->name ?? auth()->id();
        $image->save();

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bild wurde gelöscht.',
        ]);
    }

    /**
     * Format image for Feinaufmaß JS.
     *
     * The actual file is not public directly.
     * It should be loaded through your secure image route.
     */
    private function formatImage(Image $image): array
    {
        $filename = ltrim((string) $image->image, '/');

        $url = $this->secureImageUrl($image, $filename);

        return [
            'id' => $image->id,
            'url' => $url,
            'path' => $filename,
            'name' => $image->image_name,
            'uploadedBy' => $image->created_by,
            'uploadedAt' => optional($image->created_at)->toISOString(),
            'fileType' => $image->file_type,
            'status' => $image->status,
        ];
    }

    /**
     * Build URL for showing the private storage image.
     *
     * Use your existing secure image route if available.
     */
    private function secureImageUrl(Image $image, string $filename): string
    {
        if (Route::has('secure.image.byFilename')) {
            return route('secure.image.byFilename', ['filename' => $filename]);
        }

        if (Route::has('images.secure.filename')) {
            return route('images.secure.filename', ['filename' => $filename]);
        }

        if (Route::has('images.secure')) {
            return route('images.secure', ['id' => $image->id]);
        }

        if (Route::has('secure.image')) {
            return route('secure.image', ['id' => $image->id]);
        }

        return url('/secure-image/file/' . rawurlencode($filename));
    }
}