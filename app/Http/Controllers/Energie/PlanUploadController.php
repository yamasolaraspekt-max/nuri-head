<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Jobs\PlanKlassifizieren;
use App\Models\HeizlastProjekt;
use App\Models\PlanUpload;
use App\Services\Import\DateiSignatur;
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
            'datei' => [
                'required', 'file', 'max:51200',
                function (string $attr, mixed $value, callable $fail) {
                    if (! in_array(strtolower($value->getClientOriginalExtension()), self::ENDUNGEN, true)) {
                        $fail('Dateityp nicht erlaubt (DWG, DXF, PDF, PNG, JPG, TIFF).');
                    }
                },
                // AUF-88-P1 / K-01 — die Signaturprüfung läuft jetzt VOR dem Speichern, nicht erst
                // im Job danach. §3 des Master-Prompts verbietet, sich allein auf die Endung zu
                // verlassen. `DateiSignatur` ist dieselbe Erkennung, die `PlanKlassifizieren`
                // bereits benutzt — eine Wahrheit, zwei Aufrufstellen.
                function (string $attr, mixed $value, callable $fail) {
                    $endung = strtolower($value->getClientOriginalExtension());
                    $kopf = file_get_contents($value->getRealPath(), false, null, 0, 8);
                    if ($kopf !== false && ! DateiSignatur::passtZuEndung($kopf, $endung)) {
                        $fail('Der Dateiinhalt passt nicht zur Endung — die Datei könnte umbenannt oder beschädigt sein.');
                    }
                },
            ],
            'heizlast_projekt_id' => ['nullable', Rule::exists('heizlast_projekte', 'id')],
            // AUF-88-P1 / K-02 — der Hausplaner-Projektbezug. `Rule::exists` prüft nur, dass die
            // Zeile existiert (Existenz), nicht, dass der Nutzer sie benutzen darf (Zugehörigkeit)
            // — das Ownership-Gate steht darum als eigener Schritt unten, nicht im Regelwerk hier.
            'lead_alternative_add_id' => ['nullable', Rule::exists('lead_alternative_adds', 'id')],
        ]);

        // Ownership-Gate (Bauordnung ticket: keine ID aus dem Request ohne Gate). Dieselbe
        // Berechtigung, mit der die Objektseite selbst gegated ist (`permission:Hausplaner,update`
        // an `hausplaner.objekt.*`) — ein fremdes Projekt zuzuweisen verlangt dieselbe Berechtigung,
        // die das Objekt auch sonst zu ändern erlaubt.
        if ($request->filled('lead_alternative_add_id') && ! $request->user()->hasPermission('Hausplaner', 'update')) {
            abort(403, 'Keine Berechtigung, eine Referenzunterlage einem Hausplaner-Objekt zuzuordnen.');
        }

        $datei = $request->file('datei');
        $upload = PlanUpload::create([
            'user_id' => $request->user()->id, // Besitzer-Bindung (Sicherheitsschuld A-3d)
            'heizlast_projekt_id' => $request->integer('heizlast_projekt_id') ?: null,
            'lead_alternative_add_id' => $request->integer('lead_alternative_add_id') ?: null,
            'original_name' => $datei->getClientOriginalName(),
            'pfad' => $datei->store('plan-uploads'),
            'mime' => $datei->getMimeType() ?? $datei->getClientMimeType(),
            'groesse_bytes' => $datei->getSize(),
            'status' => 'neu',
        ]);

        PlanKlassifizieren::dispatch($upload);

        // Dieselbe Form wie `status()` und der Seitenaufruf — die Insel kann sie ohne zweites
        // Feld direkt in `aktualisiereUnterlage` ablegen, plus `id`/`message` fürs Polling.
        return response()->json(array_merge($upload->alsUnterlage(), [
            'id' => $upload->id, 'message' => 'Hochgeladen — wird klassifiziert.',
        ]));
    }

    /**
     * AUF-88-P1 / K-04 — die Kalibrierung speichern. `massstab_mm_pro_einheit` existiert als Feld
     * seit der ersten Migration (A-3c), aber es gab nie einen Schreibweg dafür — die Insel
     * berechnet den Wert (zwei Punkte + bekannte Länge), dieser Endpunkt hält ihn fest.
     *
     * Kein zweiter Ort für die Rechnung: der Wert kommt fertig gerechnet an, dieselbe Regel wie
     * bei `data-objektkopf` — der Server nimmt entgegen, was die Insel rechnet, er rechnet nicht
     * nach.
     */
    public function massstab(Request $request, PlanUpload $planUpload): JsonResponse
    {
        abort_unless($planUpload->user_id === auth()->id(), 403);

        $daten = $request->validate([
            'massstab_mm_pro_einheit' => ['required', 'numeric', 'gt:0'],
        ]);

        $planUpload->update($daten);

        return response()->json(['massstab_mm_pro_einheit' => $planUpload->massstab_mm_pro_einheit]);
    }

    /**
     * AUF-88-P1 — der aktuelle Stand für die Insel, zum Nachfragen nach dem Hochladen. Die
     * Klassifikation läuft über die Queue (`PlanKlassifizieren::dispatch`); dieser Endpunkt ist,
     * womit die Insel nachfragt, ob sie schon fertig ist — dieselbe Form wie beim Seitenaufruf
     * (`PlanUpload::alsUnterlage()`), eine Wahrheit für beide Aufrufstellen.
     */
    public function status(PlanUpload $planUpload): JsonResponse
    {
        abort_unless($planUpload->user_id === auth()->id(), 403);

        return response()->json($planUpload->alsUnterlage());
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
