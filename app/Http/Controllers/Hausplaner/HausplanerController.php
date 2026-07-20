<?php

namespace App\Http\Controllers\Hausplaner;

use App\Domain\Hausplaner\Actions\ErmittleUebernahmeStatus;
use App\Domain\Hausplaner\Actions\ErstelleLeeresSzenenDokument;
use App\Domain\Hausplaner\Actions\SpeichereHausplanerDokument;
use App\Domain\Hausplaner\Actions\StelleSnapshotWieder;
use App\Domain\Hausplaner\Actions\UebernehmeSzeneInAuslegung;
use App\Domain\Hausplaner\Models\HausplanerCatalogItem;
use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Domain\Hausplaner\Models\HausplanerSnapshot;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hausplaner\SpeichereHausplanerDokumentRequest;
use App\Models\LeadAlternativeAdd;
use App\Services\Geometrie\GeometrieUngueltigException;
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
    public function seite(LeadAlternativeAdd $objekt)
    {
        $dokument = $this->dokumentFuer($objekt);

        return view('admin.hausplaner.objekt', [
            'objekt' => $objekt,
            'dokument' => $dokument,
            'uebernahme' => app(ErmittleUebernahmeStatus::class)->ausfuehren($objekt, $dokument),
        ]);
    }

    /**
     * W-A — expliziter Nutzer-Auslöser „In Auslegung übernehmen" (Operanden-Gate: Fachentscheidung,
     * Vorschlag + Bestätigung, KEIN Automatismus). Dünne Hülle: EIN Action-Aufruf, Antwort-Mapping.
     * Doppel-Submit ist per Bestand idempotent (gleicher Szenen-Hash ⇒ status 'unveraendert', keine
     * Doppel-Version — UebernehmeSzeneInAuslegung). Liest die Szene nur; legt KEIN Dokument an.
     */
    public function uebernehmen(Request $request, LeadAlternativeAdd $objekt)
    {
        try {
            $ergebnis = app(UebernehmeSzeneInAuslegung::class)
                ->ausfuehren($objekt, optional($request->user())->id);
        } catch (GeometrieUngueltigException $e) {
            return redirect()->route('hausplaner.objekt.seite', $objekt)->with('hausplaner_uebernahme', [
                'typ' => 'fehler',
                'text' => 'Übernahme abgelehnt: die Szene enthält ungültige Geometrie ('.$e->getMessage().'). Es wurde nichts geschrieben.',
            ]);
        }

        if ($ergebnis['status'] === 'keine_szene') {
            // Kante (Spec §4): kein Dokument am Objekt ⇒ 422, nichts geschrieben (Button ist im UI deaktiviert).
            return response()->json([
                'status' => 'keine_szene',
                'message' => 'Für dieses Objekt existiert noch keine Hausplaner-Szene — es wurde nichts übernommen.',
            ], 422);
        }

        $flash = match ($ergebnis['status']) {
            'kein_raum' => [
                'typ' => 'warnung',
                'text' => 'Die Szene enthält keine geschlossenen Räume — es wurde keine Version erzeugt.',
            ],
            'unveraendert' => [
                'typ' => 'info',
                'text' => 'Diese Szene ist bereits übernommen (Version '.$ergebnis['version'].') — keine neue Version erzeugt.',
            ],
            default => [
                'typ' => 'erfolg',
                'text' => $ergebnis['raeume'].' '.($ergebnis['raeume'] === 1 ? 'Raum' : 'Räume')
                    .' übernommen, Version '.$ergebnis['version'].'.',
            ],
        };

        return redirect()->route('hausplaner.objekt.seite', $objekt)->with('hausplaner_uebernahme', $flash);
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

    public function speichern(SpeichereHausplanerDokumentRequest $request, LeadAlternativeAdd $objekt): JsonResponse
    {
        $dokument = $this->dokumentFuer($objekt);

        $ergebnis = app(SpeichereHausplanerDokument::class)->ausfuehren(
            $dokument,
            (int) $request->validated('base_revision'),
            $request->scene(),
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
