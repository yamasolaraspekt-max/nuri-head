<?php

namespace App\Services\Offer;

use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use Throwable;

/**
 * AP-3a (Bereich 2, Kapitel 2) — READ-ONLY Konfigurationsprojekt-Sicht je OBJEKT.
 *
 * Aggregiert alle aktivierten Gewerke/Module eines Objekts (`lead_alternative_adds` →
 * n×`lead_product_lists`) aus VORHANDENEN read-only Bausteinen:
 *  - OfferReadinessService (Reife je Modul, inkl. AP-1 heizlast_belastbarkeit)
 *  - AuslegungVorschlagService (nur WP, Vorschau: auslegung_vorhanden/verbindlich)
 *
 * Verbindliche Grenzen (Yama-Freigabe AP-3a):
 *  - Objekt klammert (Weiche 5); `lead_product_lists` = Module; `lead_stages`/status = Modulstatus.
 *  - KEINE neue Tabelle, KEIN Schreibpfad, KEIN offer_details-Write, KEINE Preis-/Kataloglogik.
 *  - PV/Speicher/Wallbox: nur Platzhalter `verdrahtet=false` — KEINE erfundenen Zahlen,
 *    KEIN Aufruf von PvProjektService (in ticket gebrochen).
 *  - PII-arm: DTO trägt KEINE Kundendaten; Objekt-Label = „Objekt #id".
 *  - offer_details.sections bleibt unberührt (einzige Angebotspositions-Wahrheit).
 */
class KonfigurationsprojektService
{
    private const GEWERK_WP = 2;

    public function __construct(
        private OfferReadinessService $reifeService,
        private AuslegungVorschlagService $auslegungService,
    ) {}

    /** @return array<string,mixed> */
    public function fuerObjekt(int $alternativeId): array
    {
        try {
            $objekt = LeadAlternativeAdd::with(['leadProductLists.articleGroup'])->find($alternativeId);

            if (! $objekt) {
                return $this->nichtAnwendbar($alternativeId, 'Objekt nicht gefunden.');
            }

            $module = $objekt->leadProductLists
                ->sortByDesc(fn (LeadProductList $lpl) => (int) $lpl->product_id === self::GEWERK_WP ? 1 : 0)
                ->map(fn (LeadProductList $lpl) => $this->modul($lpl))
                ->values()
                ->all();

            $verdrahtet = count(array_filter($module, fn ($m) => $m['verdrahtet']));

            return [
                'anwendbar' => true,
                'vorgang_label' => 'Objekt #'.$objekt->id,   // PII-arm: keine Adresse/kein Name
                'alternative_id' => (int) $objekt->id,
                'gewerke_gesamt' => count($module),
                'gewerke_verdrahtet' => $verdrahtet,
                'module' => $module,
                'abhaengigkeiten' => $this->abhaengigkeiten($module),
                'hinweis' => count($module) === 0
                    ? 'Für dieses Objekt ist noch kein Gewerk/Modul aktiviert.'
                    : 'Read-only Objekt-Sicht. Keine Speicherung, keine Angebotsübernahme, kein Preisanker.',
            ];
        } catch (Throwable $e) {
            return $this->nichtAnwendbar($alternativeId, 'Konfigurationssicht nicht lesbar.');
        }
    }

    /** @return array<string,mixed> */
    private function modul(LeadProductList $lpl): array
    {
        $istWp = (int) $lpl->product_id === self::GEWERK_WP;
        $reife = $this->reifeService->fuer($lpl);

        // Technik = Reife (on-the-fly). Preisfähigkeit heute generell blockiert (OMD/IDS inert).
        $technikProzent = (int) ($reife['percent'] ?? 0);

        $modul = [
            'lead_product_list_id' => (int) $lpl->id,
            'gewerk_id' => (int) $lpl->product_id,
            'gewerk' => (string) ($lpl->articleGroup->article_group ?? 'Unbekannt'),
            'ist_wp' => $istWp,
            'verdrahtet' => $istWp,                       // nur WP hat eine echte Auslegungs-Naht
            'status' => (string) ($lpl->status ?? '—'),
            'phase' => $lpl->stage !== null ? (string) $lpl->stage : null,
            'reife_prozent' => $technikProzent,
            'reife_status' => (string) ($reife['status'] ?? 'unvollstaendig'),
            'technik_prozent' => $technikProzent,
            'preis_moeglich' => false,                    // OMD/IDS offen (systemweit, s. P3-d2)
            'preis_hinweis' => 'OMD/IDS offen',
            'heizlast_belastbarkeit' => $istWp ? ($reife['heizlast_belastbarkeit'] ?? null) : null,
            'auslegung_vorhanden' => false,
            'auslegung_verbindlich' => false,
            'naechste_aktion' => $reife['naechster_schritt'] ?? null,
            'blocker_offen' => $reife['blocker_offen'] ?? [],
            'hinweis' => $istWp ? '' : 'Fachmodul noch nicht verdrahtet — Auslegung folgt (AP-2/PV-Slices).',
        ];

        if ($istWp) {
            $auslegung = $this->auslegungService->fuer($lpl);
            $modul['auslegung_vorhanden'] = (bool) ($auslegung['auslegung_vorhanden'] ?? false);
            $modul['auslegung_verbindlich'] = (bool) ($auslegung['verbindlich'] ?? false);
        }

        return $modul;
    }

    /**
     * Read-only Fach-Abhängigkeits-Hinweise (keine Erzwingung, keine Berechnung).
     *
     * @param  array<int,array<string,mixed>>  $module
     * @return array<int,string>
     */
    private function abhaengigkeiten(array $module): array
    {
        if (count($module) === 0) {
            return [];
        }
        $hatWp = (bool) array_filter($module, fn ($m) => $m['ist_wp']);
        $hatWeitere = count($module) > (int) $hatWp;

        $hinweise = [];
        if ($hatWp) {
            $hinweise[] = 'Wärmepumpe ↔ Photovoltaik: Kopplung (Eigenstrom) prüfen.';
            $hinweise[] = 'Gebäudehülle (Fenster/Fassade/Dach) → Heizlast → WP-Größe: Änderungen wirken auf die Auslegung.';
        }
        if ($hatWeitere) {
            $hinweise[] = 'PV → Speicher → Wallbox: bauen fachlich aufeinander auf (erst PV-Auslegung, dann Speicher/Wallbox).';
        }

        return $hinweise;
    }

    /** @return array<string,mixed> */
    private function nichtAnwendbar(int $alternativeId, string $grund): array
    {
        return [
            'anwendbar' => false,
            'vorgang_label' => 'Objekt #'.$alternativeId,
            'alternative_id' => $alternativeId,
            'gewerke_gesamt' => 0,
            'gewerke_verdrahtet' => 0,
            'module' => [],
            'abhaengigkeiten' => [],
            'hinweis' => $grund,
        ];
    }
}
