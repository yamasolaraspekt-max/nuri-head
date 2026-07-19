<?php

namespace App\Http\Controllers\Hausplaner;

use App\Domain\Hausplaner\Actions\ErstelleLeeresSzenenDokument;
use App\Domain\Hausplaner\Actions\SpeichereHausplanerDokument;
use App\Domain\Hausplaner\Actions\StelleSnapshotWieder;
use App\Domain\Hausplaner\Models\HausplanerCatalogItem;
use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Domain\Hausplaner\Models\HausplanerSnapshot;
use App\Http\Controllers\Controller;
use App\Models\LeadAlternativeAdd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Hausplaner (ticket) — dünne HTTP-Hülle, transplantiert aus playground.
 * EINZIGE Anker-Änderung ▲T1: das Modell {project}/Project → {objekt}/LeadAlternativeAdd
 * (Objekt = Gebäudeakte). Fachlogik unverändert in den Domain-Actions (base_revision → 409).
 * Laden = Blade-Datenübergabe (kein Lade-Fetch); Schreiben = web-PUT/POST mit CSRF.
 * Rechte: permission:hausplaner.* an den Routen; {objekt} 404 bei unbekanntem Objekt.
 */
class HausplanerController extends Controller
{
    /** P2: Groessendeckel fuer die Szene (Bytes des JSON). Client-Zod bleibt Struktur-Wahrheit;
     *  der Server gatet Invarianten + GROESSE, spiegelt aber NICHT das Schema (keine zweite Wahrheit). */
    private const MAX_SCENE_BYTES = 2_000_000;

    public function seite(LeadAlternativeAdd $objekt)
    {
        $dokument = $this->dokumentFuer($objekt);

        return view('admin.hausplaner.objekt', [
            'objekt' => $objekt,
            'dokument' => $dokument,
        ]);
    }

    /** Tools-Einstieg: Gebaeude-Auswahl -> persistenter Objekt-Planer (hausplaner.objekt.seite). */
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $objekte = LeadAlternativeAdd::query()
            ->with('lead:id,firma,name,lastname,customer_no')
            ->gebaeudeSuche($q)
            ->orderByDesc('id')
            ->paginate(25)
            ->appends($request->query());

        return view('admin.hausplaner.index', ['objekte' => $objekte, 'q' => $q]);
    }

    public function speichern(Request $request, LeadAlternativeAdd $objekt): JsonResponse
    {
        $daten = $request->validate([
            'base_revision' => ['required', 'integer', 'min:1'],
            'schema_version' => ['required', 'integer', 'in:1,2'],
            'scene' => ['required', 'array'],
            'scene.schemaVersion' => ['required', 'integer', 'in:1,2'], // v1+v2 gueltig; alles andere ⇒ 422 (Kante)
            'scene.units' => ['required', 'in:mm'],
            'scene.levels' => ['required', 'array', 'min:1'],
            'scene.nodes' => ['present', 'array'],
            'scene.roofs' => ['sometimes', 'array'],                    // D-a: Dach-Sammlung strukturell gueltig, wenn vorhanden
        ]);

        // B2-Fix (Planner-Entscheidung): validate() BESCHNEIDET die Szene auf die benannten Schluessel
        // (scene.roofs + kuenftige v3-Felder fielen still weg -> HTTP 200 + Datenverlust). Das Gate oben
        // prueft die Invarianten; PERSISTIERT wird die UNGESCHNITTENE Nutzlast aus input('scene').
        $szene = $request->input('scene');

        // P2: unbegrenzte/eingeschleuste Nutzlast abfangen. Inhaltliche Gueltigkeit bleibt Sache der
        // Client-Zod-Pruefung (fehlerhafter Inhalt ist per Revision/Snapshot heilbar).
        if (strlen((string) json_encode($szene)) > self::MAX_SCENE_BYTES) {
            return response()->json(['message' => 'Die Szene überschreitet die zulässige Größe.'], 422);
        }

        $dokument = $this->dokumentFuer($objekt);

        $ergebnis = app(SpeichereHausplanerDokument::class)->ausfuehren(
            $dokument,
            (int) $daten['base_revision'],
            $szene,
            optional($request->user())->id,
        );

        if (!$ergebnis['ok']) {
            return response()->json([
                'message' => 'Der Plan wurde zwischenzeitlich verändert.',
                'aktuelle_revision' => $ergebnis['revision'],
            ], 409);
        }

        return response()->json([
            'revision' => $ergebnis['revision'],
            'checksum' => $ergebnis['checksum'],
        ]);
    }

    public function snapshotErstellen(Request $request, LeadAlternativeAdd $objekt): JsonResponse
    {
        $daten = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $dokument = $this->dokumentFuer($objekt);

        $snapshot = HausplanerSnapshot::query()->create([
            'hausplaner_document_id' => $dokument->id,
            'revision' => $dokument->revision,
            'scene_json' => $dokument->scene_json,
            'label' => $daten['label'] ?? null,
            'reason' => $daten['reason'] ?? 'manuell',
            'created_by' => optional($request->user())->id,
        ]);

        return response()->json([
            'id' => $snapshot->id,
            'revision' => $snapshot->revision,
            'label' => $snapshot->label,
        ], 201);
    }

    public function snapshotListe(LeadAlternativeAdd $objekt): JsonResponse
    {
        $dokument = $this->dokumentFuer($objekt);

        return response()->json([
            'snapshots' => $dokument->snapshots()
                ->get(['id', 'revision', 'label', 'reason', 'created_by', 'created_at']),
        ]);
    }

    public function wiederherstellen(Request $request, LeadAlternativeAdd $objekt, int $snapshotId): JsonResponse
    {
        $dokument = $this->dokumentFuer($objekt);

        $snapshot = HausplanerSnapshot::query()
            ->where('hausplaner_document_id', $dokument->id)   // Ownership: fremde Snapshot-IDs ⇒ 404
            ->findOrFail($snapshotId);

        $ergebnis = app(StelleSnapshotWieder::class)->ausfuehren($dokument, $snapshot, optional($request->user())->id);

        return response()->json($ergebnis);
    }

    public function katalog(): JsonResponse
    {
        return response()->json([
            'items' => HausplanerCatalogItem::query()->where('aktiv', true)
                ->get(['id', 'category', 'manufacturer', 'model', 'dimensions', 'representation', 'placement', 'spec_ref', 'technical_data']),
        ]);
    }

    /** Genau EIN Dokument je OBJEKT — beim ersten Zugriff mit Standard-Szene angelegt (Anker ▲T1). */
    private function dokumentFuer(LeadAlternativeAdd $objekt): HausplanerDocument
    {
        return HausplanerDocument::query()->where('alternative_id', $objekt->id)->first()
            ?? app(ErstelleLeeresSzenenDokument::class)->ausfuehren((int) $objekt->id, optional(auth()->user())->id);
    }
}
