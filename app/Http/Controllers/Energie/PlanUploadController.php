<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Jobs\PlanKlassifizieren;
use App\Models\HeizlastProjekt;
use App\Models\PlanUpload;
use App\Services\Import\ImportServiceClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Plan-Import-Fundament (Transplantat aus wberechnung, A-3a): Datei hochladen,
 * speichern, grob klassifizieren (über die Queue). Ist der Import-Service inaktiv
 * (IMPORT_SERVICE_URL leer), laufen Upload + Klassifikation, nur die Auto-Extraktion
 * ist graceful aus.
 *
 * Sicherheit: ticket hat (noch) keine PlanUpload-Policy — statt Gate/Policy wird streng
 * über auth()->id() gescoped (Besitzer-Bindung). Reines Laravel (jQuery-AJAX-Frontend,
 * KEIN Alpine, CLAUDE.md).
 */
class PlanUploadController extends Controller
{
    /** Erlaubte Endungen (grobe Klassifikation A-3a). */
    private const ENDUNGEN = ['dwg', 'dxf', 'pdf', 'png', 'jpg', 'jpeg', 'tif', 'tiff'];

    public function index(ImportServiceClient $importService): View
    {
        return view('admin.energie.plan_upload', [
            'projekte' => HeizlastProjekt::query()->orderBy('name')->get(['id', 'name']),
            'uploads' => PlanUpload::query()
                ->where('user_id', auth()->id())
                ->with('heizlastProjekt:id,name')
                ->latest()
                ->get(),
            'importAktiv' => $importService->aktiv(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'datei' => ['required', 'file', 'max:51200', function (string $attr, mixed $value, callable $fail) {
                if (! in_array(strtolower($value->getClientOriginalExtension()), self::ENDUNGEN, true)) {
                    $fail('Dateityp nicht erlaubt (DWG, DXF, PDF, PNG, JPG, TIFF).');
                }
            }],
            'heizlast_projekt_id' => ['nullable', Rule::exists('heizlast_projekte', 'id')],
        ]);

        $datei = $request->file('datei');
        $upload = PlanUpload::create([
            'user_id' => $request->user()->id, // Besitzer-Bindung (Sicherheitsschuld A-3d)
            'heizlast_projekt_id' => $request->integer('heizlast_projekt_id') ?: null,
            'original_name' => $datei->getClientOriginalName(),
            'pfad' => $datei->store('plan-uploads'),
            'mime' => $datei->getMimeType() ?? $datei->getClientMimeType(),
            'groesse_bytes' => $datei->getSize(),
            'status' => 'neu',
        ]);

        PlanKlassifizieren::dispatch($upload);

        return response()->json(['id' => $upload->id, 'message' => 'Hochgeladen — wird klassifiziert.']);
    }

    public function destroy(PlanUpload $planUpload): JsonResponse
    {
        abort_unless($planUpload->user_id === auth()->id(), 403);

        Storage::delete($planUpload->pfad);
        if ($abgeleitet = data_get($planUpload->meta, 'bild_pfad')) {
            Storage::delete($abgeleitet); // abgeleitetes Raster-PDF-PNG (A-3d-2)
        }
        $planUpload->delete();

        return response()->json(['message' => 'Gelöscht.']);
    }

    /**
     * Liefert das Rasterbild eines Uploads als Underlay-Quelle (A-3d-1).
     * Sicherheit: nur der Besitzer (auth()->id()-Scope), Ziel ausschließlich über die Upload-ID
     * (Route-Model-Binding) → gespeicherter Pfad — NIE ein Pfad-Parameter vom Client. Nur echte
     * Bilder (typ=bild + verifizierter Bildinhalt); Content-Type aus dem geprüften Bildtyp.
     */
    public function bild(PlanUpload $planUpload): Response
    {
        abort_unless($planUpload->user_id === auth()->id(), 403);

        // Bild-Upload → Original; Raster-PDF (A-3d-2) → abgeleitetes PNG aus meta.bild_pfad.
        // Beides ausschließlich über den DB-Datensatz, nie über einen Client-Pfad.
        $pfad = $planUpload->typ === 'bild'
            ? $planUpload->pfad
            : (string) data_get($planUpload->meta, 'bild_pfad');
        abort_unless($pfad !== '' && Storage::exists($pfad), 404);

        $inhalt = (string) Storage::get($pfad);
        $info = @getimagesizefromstring($inhalt);
        abort_if($info === false, 404);

        return response($inhalt, 200)
            ->header('Content-Type', $info['mime'])
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('Cache-Control', 'private, max-age=300');
    }
}
