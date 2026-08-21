<?php

namespace App\Http\Controllers\Customer;
use App\Http\Controllers\Controller;

use App\Models\Image;
use App\Models\LeadAlternativeAdd;
use Illuminate\Http\Request;
use App\Models\ArticleGroup;
use Illuminate\Support\Facades\Log;   
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\OfferFolderAttachment;
use DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;
class ImageController extends Controller

{

    
public function uploads(Request $request)
{
    Log::info('Request received:', $request->all());

    try {
        // ✅ Validation
        $request->validate([
            'file' => 'required',
            'file.*' => 'file|mimes:jpg,jpeg,png,pdf,doc,docx',
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'nullable|integer',
            'stage_id' => 'required|string',
        ]);

        Log::info('Validation passed.');

        // ✅ Normalize to array
        $files = is_array($request->file('file')) ? $request->file('file') : [$request->file('file')];

        Log::info('Uploading files:', ['count' => count($files)]);

        foreach ($files as $file) {
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $folder = 'uploads/customers';
            $storagePath = $file->storeAs($folder, $fileName); // saved to storage/app/uploads/customers

            Log::info('File stored:', [
                'original' => $file->getClientOriginalName(),
                'stored_as' => $fileName,
                'path' => $storagePath
            ]);

            // ✅ Save to database
            $image = new Image();
            $image->customer_id     = $request->customer_id;
            $image->alternative_id  = $request->alternative_id;
            $image->article_group   = $request->product_id ?? null;
            $image->stage           = $request->stage_id;
            $image->image_name      = $fileName;
            $image->image           = $fileName;
            $image->status          = $request->status ?? 'offer';
            $image->created_by      = auth()->user()->name;
            $image->file_type       = $file->getClientOriginalExtension();

            if ($image->save()) {
                Log::info('Saved image record:', $image->toArray());
            } else {
                Log::error('Failed to save image record.', ['file' => $fileName]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Alle Dateien wurden erfolgreich hochgeladen!',
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error('Validation error:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validierung fehlgeschlagen.',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Exception $e) {
        Log::error('Upload failed:', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Etwas ist schief gelaufen.',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    
public function destroy($id)
{
    $image = Image::find($id);

    if (!$image) {
        return response()->json(['success' => false, 'message' => 'Image not found'], 404);
    }

    // Delete the physical file (use correct folder if not in public/)
    $this->delete_photo($image->image);

    // Optionally log who deleted it (before delete)
    $image->update_by = auth()->user()->name;
    $image->save();

    // Now delete from DB
    $image->delete();

    return response()->json(['success' => true, 'message' => 'Bild wurde erfolgreich gelöscht.']);
}

protected function delete_photo($fileName)
{
    if (!$fileName) return;

    $path = 'uploads/customers/' . $fileName;

    if (Storage::exists($path)) {
        Storage::delete($path);
        \Log::info('Deleted file: ' . $path);
    } else {
        \Log::warning('File not found for deletion: ' . $path);
    }
}

  


    public function update(Request $request){
       $validate= $request->validate([
            'image_name'    =>  'required',
            'id'    =>  'required|exists:images,id'
        ]);

        $data = Image::find($validate['id']);
        $data->image_name = $request->image_name;
        $data->update_by = auth()->user()->name;
        $data->save();
        return response()->json(['success' => 'Image updated successfully']);
    }


public function getImage($customer, $alternative, $product)
{
    $images = Image::where('customer_id', $customer)
                   ->where('alternative_id', $alternative)
                   ->where('article_group', $product)
                   ->get();

    $imageData = $images->map(function ($image) {
        return [
            'id'         => $image->id,
            'image_name' => $image->image_name,
            'image'      => $image->image,
            'file_path'  => $this->generatePublicUrl($image->image),
        ];
    });

    Log::info('Loaded Images', ['count' => $imageData->count()]);

    return response()->json(['data' => $imageData]);
}

public function getDocument($customer, $alternative, $product, $status)
{
    $images = Image::where('customer_id', $customer)
                   ->where('alternative_id', $alternative)
                   ->where('article_group', $product)
                   ->where('status', $status)
                   ->get();

    $imageData = $images->map(function ($image) {
        return [
            'id'         => $image->id,
            'image_name' => $image->image_name,
            'file_type'  => $image->file_type,
            'stage'      => $image->stage,
            'file_path'  => $this->generatePublicUrl($image->image),
        ];
    });

    Log::info('Documents Found', ['count' => $imageData->count(), 'status' => $status]);

    return response()->json(['data' => $imageData]);
}


    public function updateDetails(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:images,id',
            'image_name' => 'required|string|max:255',
            'stage' => 'required|string|max:100',
            'article_group' => 'required|integer',
        ]);

        $image = Image::findOrFail($request->id);

        $image->update([
            'image_name' => $request->image_name,
            'stage' => $request->stage,
            'article_group' => $request->article_group,
            'update_by' => auth()->user()->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Details aktualisiert.',
        ]);
    }

protected function generatePublicUrl($filename)
{
    $path = 'uploads/customers/' . $filename;

    return Storage::disk('public')->exists($path)
        ? Storage::disk('public')->url($path)
        : asset('images/placeholder.png'); // fallback placeholder
}


   public function load(Request $request)
    {
        $pid = $request->product_id;

        $customer = DB::table('new_leads')
            ->where('id', $request->customer_id)
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Product = ArticleGroup
        |--------------------------------------------------------------------------
        | In your system product_id points to article_groups.id.
        */
        $product = null;

        if (!empty($pid)) {
            $product = ArticleGroup::query()
                ->whereKey($pid)
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Images / Documents
        |--------------------------------------------------------------------------
        | auth()->user()->name is the employee ID in your app.
        | images.created_by stores that employee ID.
        */
        $images = Image::query()
            ->leftJoin('employees as creator', 'creator.id', '=', 'images.created_by')
            ->where('images.customer_id', $request->customer_id)
            ->where('images.alternative_id', $request->alternative_id)
            ->when(!empty($pid), function ($q) use ($pid) {
                $q->where(function ($qq) use ($pid) {
                    $qq->where('images.article_group', $pid)
                        ->orWhereNull('images.article_group')
                        ->orWhere('images.article_group', 0);
                });
            })
            ->whereNull('images.deleted_at')
            ->select([
                'images.*',
                DB::raw("
                    COALESCE(
                        NULLIF(TRIM(CONCAT_WS(' ', creator.name, creator.lastname)), ''),
                        creator.name,
                        images.created_by
                    ) as uploader_name
                "),
                DB::raw("images.created_by as uploader_employee_id"),
            ])
            ->get()
            ->map(function ($img) {
                $img->doc_model_type = 'image';
                return $img;
            });

        /*
        |--------------------------------------------------------------------------
        | Offer Attachments
        |--------------------------------------------------------------------------
        */
        $offerQuery = \App\Models\Offer::query()
            ->where('customer_id', $request->customer_id)
            ->where('alternative_id', $request->alternative_id);

        if (!empty($pid)) {
            $offerQuery->where('product_id', $pid);
        }

        $offerIds = $offerQuery->pluck('id');

        $offerAttachments = \App\Models\OfferFolderAttachment::query()
            ->whereIn('offer_id', $offerIds)
            ->get()
            ->map(function ($att) {
                $cleanPath = ltrim($att->file_path, '/');

                $publicUrl = str_starts_with($cleanPath, 'storage/')
                    ? asset($cleanPath)
                    : asset('storage/' . $cleanPath);

                return (object) [
                    'id' => $att->id,
                    'doc_model_type' => 'offer_attachment',
                    'image_name' => $att->original_name ?? $att->title ?? 'Angebot_Dokument',
                    'image' => $att->file_path,
                    'stage' => 'Angebot',
                    'file_type' => $att->extension ?? pathinfo($att->file_path, PATHINFO_EXTENSION) ?? 'doc',
                    'created_at' => $att->created_at,
                    'updated_at' => $att->updated_at,
                    'custom_file_url' => $publicUrl,
                    'uploader_name' => 'System / Angebot',
                    'uploader_employee_id' => null,
                    'article_group' => null,
                ];
            });

        $allDocuments = $images
            ->concat($offerAttachments)
            ->sortByDesc('created_at')
            ->values();

        return view('admin.new_leads.layouts.document', [
            'images' => $allDocuments,
            'documentCustomer' => $customer,
            'documentProduct' => $product,
            'documentContext' => [
                'customer_id' => $request->customer_id,
                'alternative_id' => $request->alternative_id,
                'product_id' => $pid,
                'product_list_id' => $request->product_list_id,
            ],
        ]);
    }
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx|max:51200',
            'customer_id' => 'required|exists:new_leads,id',
            'alternative_id' => 'required|integer|exists:lead_alternative_adds,id',
            'product_id' => 'required|integer',
            'stage' => 'required|string|max:100',
        ]);

        $file = $request->file('file');

        $originalName = preg_replace(
            '/[^a-zA-Z0-9\-_\.]/',
            '_',
            $file->getClientOriginalName()
        );

        $extension = strtolower($file->getClientOriginalExtension());
        $filename = time() . '_' . uniqid() . '_' . $originalName;

        $path = $file->storeAs('uploads/customers', $filename, 'local');

        if (!$path || !Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Datei konnte nicht gespeichert werden.',
            ], 500);
        }

        $image = Image::create([
            'customer_id' => $request->customer_id,
            'alternative_id' => $request->alternative_id,
            'article_group' => $request->product_id,
            'image' => $filename,
            'image_name' => pathinfo($originalName, PATHINFO_FILENAME),
            'file_type' => $extension,
            'stage' => $request->stage,
            'status' => $request->status ?? 'document',
            'created_by' => auth()->user()->name ?? null, // employee_id in your app
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Datei erfolgreich hochgeladen.',
            'image' => [
                'id' => $image->id,
                'image_id' => $image->id,
                'file_name' => $filename,
                'image_name' => $image->image_name,
                'file_type' => $image->file_type,
                'stage' => $image->stage,
                'created_by' => $image->created_by,
                'created_at' => optional($image->created_at)->format('d.m.Y H:i'),
            ],
        ]);
    }

public function secureDownload($id)
{
    $image = Image::findOrFail($id);

    // Optional: check access control
    if (!auth()->check()) {
        abort(403, 'Unauthorized');
    }

    $path = 'uploads/customers/' . $image->image;

    if (!Storage::disk('local')->exists($path)) {
        abort(404, 'Datei nicht gefunden');
    }

    return Storage::disk('local')->download($path, $image->image_name . '.' . $image->file_type);
}

 
public function getByFilter(Request $request)
{
    $query = Image::query();

    if ($request->filled('customer_id')) {
        $query->where('customer_id', $request->customer_id);
    }
    if ($request->filled('alternative_id')) {
        $query->where('alternative_id', $request->alternative_id);
    }
    if ($request->filled('stage')) {
        $query->where('stage', $request->stage);
    }
    if ($request->filled('keyword')) {
        $query->where(function($q) use ($request) {
            $q->where('image_name', 'like', '%' . $request->keyword . '%')
              ->orWhere('file_type', 'like', '%' . $request->keyword . '%');
        });
    }

    $images = $query->orderBy('created_at', 'desc')->get();

    return response()->json($images);
}



public function delete($id)
{
    $image = Image::findOrFail($id);
    $path = 'uploads/' . $image->image;

    if (Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
    }

    $image->delete();

    return response()->json(['success' => true, 'message' => 'Datei wurde gelöscht.']);
}

public function download($id)
{
    $image = Image::findOrFail($id);
    
    // The exact path where your files are actually saved
    $path = 'uploads/customers/' . $image->image;

    // Look for the file in the private 'local' disk (storage/app/)
    if (!Storage::disk('local')->exists($path)) {
        abort(404, 'Datei nicht gefunden.');
    }

    // Return the file for download, using its original name and extension
    return Storage::disk('local')->download(
        $path, 
        $image->image_name . '.' . $image->file_type
    );
}


public function rename(Request $request)
{
    $request->validate([
        'id' => 'required|exists:images,id',
        'image_name' => 'required|string|max:255',
        'stage' => 'nullable|string', // ✅ Add this
    ]);

    $image = Image::findOrFail($request->id);

    $image->update([
        'image_name' => $request->image_name,
        'stage' => $request->stage ?? $image->stage, // ✅ Update stage if provided
        'update_by'  => auth()->user()->name,
    ]);

    return response()->json(['success' => true, 'message' => 'Details wurden aktualisiert.']);
}

    public function saveScreenshot(Request $request)
{
    try {
        $validated = $request->validate([
            'alternative_id' => 'required|exists:lead_alternative_adds,id',
            'status'         => 'nullable|string',
            'stage'          => 'nullable|string',
            'image_name'     => 'nullable|string|max:255',

            // New fields from JS
            'mode'           => 'nullable|string|max:50',
            'zoom'           => 'nullable|integer|min:1|max:21',
            'lat'            => 'required|numeric',
            'lng'            => 'required|numeric',
            'address'        => 'nullable|string|max:500',
        ]);

        $alternative = LeadAlternativeAdd::findOrFail($validated['alternative_id']);

        if (!$alternative->lead_id) {
            return response()->json([
                'success' => false,
                'message' => 'Customer ID konnte nicht ermittelt werden.',
            ], 422);
        }

        $googleKey = config('services.google.maps_key');

        if (!$googleKey) {
            return response()->json([
                'success' => false,
                'message' => 'Google Maps Key fehlt. Bitte GOOGLE_MAPS_KEY in .env prüfen.',
            ], 422);
        }

        $alternativeId = (int) $validated['alternative_id'];
        $lat = (float) $validated['lat'];
        $lng = (float) $validated['lng'];

        $mode = $validated['mode'] ?? 'satellite';
        $zoom = (int) ($validated['zoom'] ?? 20);

        $location = $lat . ',' . $lng;

        /*
        |--------------------------------------------------------------------------
        | Build Google URL in Laravel
        |--------------------------------------------------------------------------
        | Do not trust/send full image_url from JS.
        */
        if ($mode === 'streetview') {
            $googleUrl = 'https://maps.googleapis.com/maps/api/streetview?' . http_build_query([
                'size'     => '1200x720',
                'location' => $location,
                'fov'      => 55,
                'heading'  => 0,
                'pitch'    => 0,
                'source'   => 'outdoor',
                'key'      => $googleKey,
            ]);
        } else {
            $mapType = in_array($mode, ['satellite', 'hybrid', 'roadmap', 'terrain'], true)
                ? $mode
                : 'satellite';

            $googleUrl = 'https://maps.googleapis.com/maps/api/staticmap?' . http_build_query([
                'center'  => $location,
                'zoom'    => $zoom,
                'size'    => '1200x720',
                'scale'   => 2,
                'maptype' => $mapType,
                'key'     => $googleKey,
            ]) . '&markers=' . urlencode('color:red|' . $location);
        }

        \Log::info('Google screenshot request started', [
            'alternative_id' => $alternativeId,
            'mode' => $mode,
            'lat' => $lat,
            'lng' => $lng,
            'url_without_key' => preg_replace('/key=[^&]+/', 'key=***', $googleUrl),
        ]);

        $response = Http::timeout(30)
            ->retry(2, 500)
            ->withoutVerifying()
            ->get($googleUrl);

        $contentType = $response->header('Content-Type', '');
        $bodyPreview = mb_substr($response->body(), 0, 800);

        if (!$response->successful()) {
            \Log::error('Google screenshot download failed', [
                'status' => $response->status(),
                'content_type' => $contentType,
                'body_preview' => $bodyPreview,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Google konnte das Screenshot-Bild nicht liefern. Bitte Maps Static API / Street View Static API und API-Key Einschränkungen prüfen.',
                'google_status' => $response->status(),
                'google_content_type' => $contentType,
                'google_error' => $bodyPreview,
            ], 422);
        }

        if (!str_starts_with((string) $contentType, 'image/')) {
            \Log::error('Google screenshot response is not an image', [
                'status' => $response->status(),
                'content_type' => $contentType,
                'body_preview' => $bodyPreview,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Google hat kein Bild zurückgegeben. Bitte API-Key, Billing und aktivierte APIs prüfen.',
                'google_status' => $response->status(),
                'google_content_type' => $contentType,
                'google_error' => $bodyPreview,
            ], 422);
        }

        $extension = str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg')
            ? 'jpg'
            : 'png';

        $filename = 'screenshot_' . time() . '_' . uniqid() . '.' . $extension;
        $path = 'uploads/customers/' . $filename;

        Storage::disk('local')->put($path, $response->body());

        if (!Storage::disk('local')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'Screenshot konnte nicht gespeichert werden.',
            ], 500);
        }

        $image = Image::create([
            'customer_id'     => $alternative->lead_id,
            'alternative_id'  => $alternativeId,
            'image_name'      => $request->input('image_name', 'Map Screenshot'),
            'image'           => $filename,
            'file_type'       => $extension,
            'stage'           => $request->input('stage', 'screenshot') ?: 'screenshot',
            'status'          => $request->input('status', 'screenshot') ?: 'screenshot',
            'created_by'      => auth()->user()->name ?? 'system',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Screenshot wurde erfolgreich gespeichert.',
            'image' => [
                'id' => $image->id,
                'image' => $image->image,
                'image_name' => $image->image_name,
                'file_type' => $image->file_type,
                'url' => route('secure.image', $image->id),
                'download' => route('image.secure.download', $image->id),
                'created_at' => optional($image->created_at)->format('d.m.Y H:i'),
            ],
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validierung fehlgeschlagen.',
            'errors' => $e->errors(),
        ], 422);

    } catch (\Throwable $e) {
        \Log::error('Screenshot Save Error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Screenshot konnte nicht gespeichert werden.',
            'error' => $e->getMessage(),
        ], 500);
    }
}
    public function loadScreenshot($alternativeId)
    {
        $images = Image::where('alternative_id', $alternativeId)
            ->whereNull('deleted_at')
            ->where('status', 'screenshot')
            ->latest()
            ->get(['id', 'image', 'image_name', 'file_type', 'created_at'])
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                    'image_name' => $image->image_name ?? 'Screenshot',
                    'file_type' => $image->file_type ?? 'png',
                    'url' => route('secure.image', $image->id),
                    'download' => route('image.secure.download', $image->id),
                    'created_at' => optional($image->created_at)->format('d.m.Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'images' => $images,
        ]);
    }
    public function deleteScreenshot(Request $request)
    {
        try {
            // 1. Wir validieren, dass die ID mitgeschickt wird
            $request->validate([
                'id' => 'required|exists:images,id',
            ]);

            // 2. Das Bild direkt über das Eloquent Model anhand der ID holen
            $image = Image::find($request->input('id'));

            if (!$image) {
                return response()->json(['success' => false, 'message' => 'Bild nicht gefunden.'], 404);
            }

            // 3. Den Dateipfad für den Storage definieren
            $path = 'uploads/customers/' . $image->image;

            // 4. Physisch aus dem privaten Storage löschen
            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            // 5. Den Datenbank-Eintrag entfernen
            $image->delete();

            return response()->json([
                'success' => true,
                'message' => 'Screenshot wurde erfolgreich gelöscht.'
            ]);

        } catch (\Throwable $e) {
            \Log::error('Screenshot Delete Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen des Screenshots.'
            ], 500);
        }
    }
public function secureDownloadScreenshot($id)
{
    // Z2-W0-8 · Kriterium C: erst prüfen, dann laden. Vorher stand `findOrFail` VOR der
    // Auth-Prüfung — ein nicht angemeldeter Aufruf löste damit eine Datenbankabfrage aus und
    // unterschied über den Statuscode zwischen „gibt es" (403) und „gibt es nicht" (404).
    // Die Reihenfolge ist die ganze Änderung; geladen wird dasselbe.
    if (!auth()->check()) {
        abort(403);
    }

    $image = Image::findOrFail($id);

    $path = 'uploads/customers/' . $image->image;

    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }

    return Storage::disk('local')->download($path, $image->image_name . '.' . $image->file_type);
}

public function secureImage($id)
{
    $img = Image::findOrFail($id);
    $path = 'uploads/customers/' . $img->image;

    if (!Storage::disk('local')->exists($path)) {
        abort(404);
    }

    $mime = Storage::disk('local')->mimeType($path);
    $content = Storage::disk('local')->get($path);

    return response($content, 200)
        ->header('Content-Type', $mime)
        ->header('Content-Disposition', 'inline');
}

    public function secureImageByFilename($filename)
    {
        if (!auth()->check()) {
            abort(403);
        }

        $filename = basename(urldecode($filename));

        $image = Image::where('image', $filename)
            ->whereNull('deleted_at')
            ->firstOrFail();

        $path = 'uploads/customers/' . $image->image;

        if (!Storage::disk('local')->exists($path)) {
            \Log::warning('Secure image file not found', [
                'image_id' => $image->id,
                'filename' => $image->image,
                'path' => $path,
                'storage_path' => storage_path('app/' . $path),
            ]);

            abort(404, 'Bilddatei nicht gefunden.');
        }

        $mime = Storage::disk('local')->mimeType($path) ?: 'image/' . ($image->file_type ?: 'png');
        $content = Storage::disk('local')->get($path);

        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $image->image . '"')
            ->header('Cache-Control', 'private, max-age=3600');
    }
    public function uploadScreenshot(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpg,jpeg,png,webp|max:25600',
                'alternative_id' => 'required|exists:lead_alternative_adds,id',
                'image_name' => 'nullable|string|max:255',
            ]);

            $alternative = LeadAlternativeAdd::findOrFail($request->alternative_id);

            if (!$alternative->lead_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer ID konnte nicht ermittelt werden.',
                ], 422);
            }

            $file = $request->file('file');

            $originalName = preg_replace(
                '/[^a-zA-Z0-9\-_\.]/',
                '_',
                $file->getClientOriginalName()
            );

            $filename = 'screenshot_upload_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs('uploads/customers', $filename, 'local');

            if (!$path || !Storage::disk('local')->exists($path)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Screenshot konnte nicht gespeichert werden.',
                ], 500);
            }

            $image = Image::create([
                'customer_id' => $alternative->lead_id,
                'alternative_id' => $request->alternative_id,
                'image_name' => $request->input('image_name') ?: pathinfo($originalName, PATHINFO_FILENAME),
                'image' => $filename,
                'file_type' => strtolower($file->getClientOriginalExtension()),
                'stage' => 'screenshot',
                'status' => 'screenshot',
                'created_by' => auth()->user()->name ?? 'system',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Screenshot wurde erfolgreich hochgeladen.',
                'image' => $image,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierung fehlgeschlagen.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            \Log::error('Manual Screenshot Upload Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Hochladen des Screenshots.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
