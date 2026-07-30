<?php

namespace App\Http\Controllers\Hausplaner;

use App\Domain\Hausplaner\Actions\ErmittleUebernahmeStatus;
use App\Domain\Hausplaner\Actions\ErstelleLeeresSzenenDokument;
use App\Domain\Hausplaner\Actions\SpeichereHausplanerDokument;
use App\Domain\Hausplaner\Actions\StelleSnapshotWieder;
use App\Domain\Hausplaner\Actions\UebernehmeSzeneInAuslegung;
use App\Domain\Hausplaner\Models\HausplanerCatalogItem;
use App\Domain\Hausplaner\Models\HausplanerConfiguratorPackage;
use App\Domain\Hausplaner\Models\HausplanerDocument;
use App\Domain\Hausplaner\Models\HausplanerSnapshot;
use App\Http\Controllers\Controller;
use App\Http\Requests\Hausplaner\SpeichereHausplanerDokumentRequest;
use App\Models\LeadAlternativeAdd;
use App\Models\User;
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
    /**
     * AUF-60 — die Aktionen, die `User::hasPermission()` fuer das Item „Hausplaner" kennt.
     * Genau diese vier, nicht mehr: `hasPermission()` bildet auf vier feste Spalten ab und schickt
     * jede unbekannte Aktion in den `default`-Zweig, also auf `is_read`.
     */
    private const HAUSPLANER_AKTIONEN = ['read', 'add', 'update', 'delete'];

    /**
     * AUF-78 — wie viele zuletzt bearbeitete Objekte der Startbildschirm zeigt.
     * **Hart begrenzt, keine Paginierung:** die Fläche zeigt die letzten wenigen, nicht alle.
     */
    private const PROJEKTLISTE_MAX = 6;

    public function seite(Request $request, LeadAlternativeAdd $objekt)
    {
        $dokument = $this->dokumentFuer($objekt);

        return view('admin.hausplaner.objekt', [
            'objekt' => $objekt,
            'dokument' => $dokument,
            'uebernahme' => app(ErmittleUebernahmeStatus::class)->ausfuehren($objekt, $dokument),
            'hpRechte' => $this->hausplanerRechte($request->user()),
            // AUF-78: NUR hier. Die Studio-Route traegt kein Hausplaner-Recht (nur `auth`) —
            // wer die Liste dorthin durchreicht, zeigt sie jedem angemeldeten Nutzer.
            'hpProjekte' => $this->hausplanerProjekte(),
            // AUF-88-P1: die zuletzt hochgeladene Referenzunterlage dieses Objekts, fertig vom
            // Server — dieselbe Naht wie `hpProjekte`, kein Lade-Fetch aus der Insel.
            'hpUnterlage' => $this->hausplanerUnterlage($objekt),
        ]);
    }

    /**
     * AUF-88-P1 / K-05 — die zuletzt hochgeladene Referenzunterlage dieses Objekts, oder `null`.
     *
     * **Nur EIN Datensatz, der neueste.** Mehrere Unterlagen gleichzeitig zu tragen ist außerhalb
     * des Auftrags (`nicht_ziel`: „ein Dialog reicht für einen Weg") — der Nutzer lädt eine neue
     * hoch, wenn die alte nicht mehr passt.
     *
     * **Kein Bildinhalt hier** — nur die URL zum bestehenden `bild()`-Endpunkt, der seinerseits
     * das Ownership-Gate trägt. Diese Methode gibt keine Bilddaten frei, nur Metadaten.
     */
    private function hausplanerUnterlage(LeadAlternativeAdd $objekt): ?array
    {
        $upload = \App\Models\PlanUpload::query()
            ->where('lead_alternative_add_id', $objekt->id)
            ->latest()
            ->first();

        return $upload?->alsUnterlage();
    }

    /**
     * AUF-78 — die zuletzt bearbeiteten Objekte für den Startbildschirm.
     *
     * **Es wird nichts erfunden:** dieselbe Tabelle, die `index()` seit Langem listet, hinter
     * derselben Middleware (`permission:Hausplaner,read`). Ein zweiter Zugriffsweg entsteht nicht.
     *
     * **Drei Entscheidungen, die die Sicherheit dieses Postens tragen:**
     *
     * 1. **Keine Kundendaten.** `index()` lädt `lead` mit, weil die Suchliste den Kundennamen
     *    zeigt. Der Startbildschirm zeigt ihn **nicht** — also wird die Beziehung gar nicht erst
     *    geladen. *Was nicht durchgereicht wird, kann nicht versehentlich sichtbar werden* — und
     *    ohne Beziehung gibt es auch kein N+1.
     * 2. **Nur die vier Felder, die die Fläche anzeigt.** `select()` statt ganzer Modelle.
     * 3. **Harte Obergrenze.** `limit`, keine Paginierung: auch bei 3 000 Objekten sind es sechs.
     *
     * **Ohne `gebaeudeSuche`:** der Scope gibt bei leerem Begriff die Abfrage unverändert zurück
     * (nachgemessen) — er würde hier also nichts tun. Er wird von der Index-Seite mitbenutzt;
     * ihn ohne Not mitzuziehen, bindet zwei Flächen aneinander, die nichts voneinander wollen.
     */
    private function hausplanerProjekte(): array
    {
        return LeadAlternativeAdd::query()
            ->select(['id', 'object_name', 'city', 'updated_at'])
            ->orderByDesc('updated_at')
            ->limit(self::PROJEKTLISTE_MAX)
            ->get()
            ->map(fn ($o) => [
                'id' => (int) $o->id,
                // Ohne Bezeichnung bleibt die Nummer — sie ist das, was der Nutzer im CRM sieht.
                'name' => (string) ($o->object_name ?: 'Objekt #'.$o->id),
                'ort' => (string) ($o->city ?? ''),
                'datum' => optional($o->updated_at)->format('d.m.Y') ?? '',
                // AUF-66 — die Adresse wird HIER erzeugt, nicht in der Insel. `route()` kennt
                // Praefix und Namen; ein in TypeScript zusammengesetzter Pfad waere eine zweite
                // Wahrheit ueber das Routing und braeche beim ersten Praefixwechsel.
                // Dasselbe Recht wie die Liste selbst (`permission:Hausplaner,read`): wer den
                // Eintrag sehen darf, darf die Seite oeffnen — es entsteht kein zweiter Zugang.
                'adresse' => route('hausplaner.objekt.seite', $o->id),
            ])
            ->all();
    }

    /**
     * AUF-64 — die Rechte des angemeldeten Nutzers als Zeichenkette fuer `data-rechte`.
     *
     * **Warum hier und nicht im Blade:** `auth()->user()`, eine Sammlung und vier
     * `hasPermission`-Aufrufe sind Anwendungslogik. Im Blade brauchte das einen `@php`-Block — und
     * genau dessen schliessende Marke hat, gepaart mit der einzeiligen Form beim Uebernahme-Knopf,
     * `objekt/203` zerbrochen (AUF-64). Hier ist die Berechnung ausserdem pruefbar; ein Block im
     * Template ist es nicht.
     *
     * **Diese Methode entscheidet nichts.** Sie fragt `hasPermission` und gibt weiter, was von dort
     * kommt — die Rechte-Wahrheit bleibt der Server (`CheckUserPermission` an jeder Route).
     *
     * **Ohne angemeldeten Nutzer ist das Ergebnis leer** — das Minimum, nie das Maximum. Ein
     * fehlender Nutzer darf nicht „darf alles" bedeuten.
     */
    private function hausplanerRechte(?User $nutzer): string
    {
        if ($nutzer === null) {
            return '';
        }

        return collect(self::HAUSPLANER_AKTIONEN)
            ->filter(fn (string $aktion) => $nutzer->hasPermission('Hausplaner', $aktion))
            ->map(fn (string $aktion) => 'Hausplaner,'.$aktion)
            ->implode(' ');
    }

    // ── AUF-81: Konfigurator-Pakete ─────────────────────────────────────────────────────────────
    /**
     * Ein Paket speichern. **Der Besitzer kommt aus der Sitzung, nie aus der Anfrage** — eine
     * Kennung, die der Aufrufer mitschickt, wäre das Gatter, das man selbst aufsperrt.
     */
    public function paketSpeichern(Request $request): JsonResponse
    {
        $daten = $request->validate([
            'art' => ['required', 'string', 'in:fenster,tuer,treppe,heizkoerper'],
            'titel' => ['required', 'string', 'max:255'],
            'alternative_id' => ['nullable', 'integer'],   // autark erlaubt: kein Gebäude nötig
            'schema_version' => ['nullable', 'integer', 'min:1'],
            'paket' => ['required', 'array'],
        ]);

        $paket = HausplanerConfiguratorPackage::query()->create([
            'user_id' => $request->user()->id,             // aus der Sitzung, nicht aus der Anfrage
            'alternative_id' => $daten['alternative_id'] ?? null,
            'art' => $daten['art'],
            'titel' => $daten['titel'],
            'status' => 'entwurf',
            'schema_version' => $daten['schema_version'] ?? 1,
            'paket' => $daten['paket'],
        ]);

        return response()->json(['id' => $paket->id, 'titel' => $paket->titel], 201);
    }

    /**
     * Die Liste der **eigenen** Pakete, paginiert.
     *
     * **Serverseitig gefiltert:** `vonNutzer` schränkt die Abfrage ein, bevor etwas geladen wird.
     * Eine Liste, die alles lädt und die Hälfte ausblendet, ist bereits geleakt.
     * Paginierung wie in `index()` — dieselbe Mechanik, kein eigenes Blätterwerk.
     */
    public function paketListe(Request $request): JsonResponse
    {
        $seite = HausplanerConfiguratorPackage::query()
            ->vonNutzer($request->user()?->id)
            ->select(['id', 'art', 'titel', 'status', 'alternative_id', 'created_at'])
            ->orderByDesc('created_at')
            ->paginate(25)
            ->appends($request->query());

        return response()->json($seite);
    }

    /**
     * Ein einzelnes Paket — **nur das eigene.**
     *
     * Die Kennung stammt aus der Anfrage und wird deshalb **niemals ohne Eigentumsprüfung**
     * benutzt (Bauordnung `ticket`). Fremd ⇒ 404: der Aufrufer erfährt nicht einmal, dass es
     * existiert.
     */
    public function paketZeigen(Request $request, int $paket): JsonResponse
    {
        $eintrag = HausplanerConfiguratorPackage::query()
            ->vonNutzer($request->user()?->id)
            ->whereKey($paket)
            ->first();

        if ($eintrag === null) {
            return response()->json(['message' => 'Nicht gefunden.'], 404);
        }

        return response()->json($eintrag);
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
