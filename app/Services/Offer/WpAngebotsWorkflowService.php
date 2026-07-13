<?php

namespace App\Services\Offer;

use App\Models\LeadProductList;
use Throwable;

/**
 * Paket 4a (Bereich 2) — READ-ONLY WP-Angebotsworkflow-Cockpit.
 *
 * Bündelt die drei bestehenden read-only Bausteine on-the-fly:
 *  - OfferReadinessService   (Angebotsreife: Prozent/Status/Aufgaben/Blocker)
 *  - AuslegungVorschlagService (Auslegung vorhanden? Ampel/Aufgaben)
 *  - WpKatalogMatchingService  (Preisfähigkeit: Schnittmenge Specs↔Preis-Set)
 *
 * Erzeugt daraus ein 5-Schritt-Prozessmodell (Bedarf · Reife · Auslegung · Preisfähigkeit · Angebot),
 * einen Fortschritt (85 % Technik + 15 % Preis, GETRENNT ausgewiesen) und die nächste sinnvolle Aktion.
 *
 * Verbindlich: KEIN Schreiben, KEINE Persistenz, KEINE zweite Wahrheit, KEIN Preisanker, KEIN component_id.
 * Nur WP (article_groups.id=2). Alles read-only aus den Quell-Services.
 */
class WpAngebotsWorkflowService
{
    private const GEWERK_WP = 2;

    public function __construct(
        private OfferReadinessService $reifeService,
        private AuslegungVorschlagService $auslegungService,
        private WpKatalogMatchingService $matchingService,
    ) {
    }

    /** @return array<string,mixed> */
    public function fuerId(int $leadProductListId): array
    {
        $lpl = LeadProductList::with(['customer', 'alternative', 'articleGroup'])->find($leadProductListId);

        if (! $lpl) {
            return $this->nichtAnwendbar($leadProductListId, 'Vorgang nicht gefunden.');
        }
        if ((int) $lpl->product_id !== self::GEWERK_WP) {
            return $this->nichtAnwendbar($leadProductListId, 'Das Cockpit ist nur für Wärmepumpe (Gewerk WP) verfügbar.');
        }

        try {
            return $this->cockpit($lpl);
        } catch (Throwable $e) {
            return $this->nichtAnwendbar($leadProductListId, 'Workflow-Status konnte nicht ermittelt werden.');
        }
    }

    /** @return array<string,mixed> */
    private function cockpit(LeadProductList $lpl): array
    {
        $reife = $this->reifeService->fuerId((int) $lpl->id);
        $auslegung = $this->auslegungService->fuerId((int) $lpl->id);
        $matching = $this->matchingService->fuerId((int) $lpl->id);

        $formularErfuellt = $this->kriteriumErfuellt($reife, 'wp_formular_ausgefuellt')
            || $this->kriteriumErfuellt($reife, 'wp_formular_vorhanden');
        $auslegungVorhanden = (bool) ($auslegung['auslegung_vorhanden'] ?? false);
        $preisMoeglich = (int) ($matching['schnittmenge'] ?? 0) > 0;
        $angebotsfaehig = (bool) ($reife['angebotsfaehig'] ?? false);
        $reifeStatus = (string) ($reife['status'] ?? 'unvollstaendig');

        $schritte = [
            $this->schritt('bedarf', 'Bedarf', 'technik', '#reife',
                $formularErfuellt ? 'erledigt' : 'offen',
                $formularErfuellt ? 'WP-Formular ausgefüllt.' : 'WP-Bedarfsformular noch offen.'),
            $this->schritt('reife', 'Angebotsreife', 'technik', '#reife',
                $this->reifeSchrittStatus($reifeStatus),
                $reife['hinweis'] ?? '—'),
            $this->schritt('auslegung', 'Auslegung', 'technik', '#auslegung',
                $auslegungVorhanden ? 'erledigt' : 'offen',
                $auslegung['kurzergebnis'] ?? '—', $auslegung['gesamt_ampel'] ?? 'grau'),
            $this->schritt('preis', 'Preisfähigkeit', 'kaufmaennisch', '#preis',
                $preisMoeglich ? 'erledigt' : 'blockiert',
                $matching['banner'] ?? 'Preisfähigkeit unklar.'),
            $this->schritt('angebot', 'Angebot', 'ergebnis', null,
                $angebotsfaehig ? ($preisMoeglich ? 'bereit' : 'technisch_bereit') : 'gesperrt',
                $angebotsfaehig
                    ? ($preisMoeglich ? 'Angebot kann erstellt werden.' : 'Technisch bereit — kaufmännisch (Preis) noch offen.')
                    : 'Angebot gesperrt: offene Pflichtpunkte.'),
        ];

        $technik = (int) ($reife['percent'] ?? 0);
        $preis = $preisMoeglich ? 100 : 0;
        $gesamt = (int) round(0.85 * $technik + 0.15 * $preis);

        return [
            'anwendbar' => true,
            'lead_product_list_id' => (int) $lpl->id,
            'gewerk' => (string) ($lpl->articleGroup->article_group ?? 'Wärmepumpe'),
            'gewerk_id' => self::GEWERK_WP,
            'vorgang_label' => 'Vorgang #'.(int) $lpl->id,       // PII-arm: nur Vorgangsnummer, kein Kundenname
            'schritte' => $schritte,
            'technik_prozent' => $technik,
            'preis_prozent' => $preis,
            'preis_moeglich' => $preisMoeglich,
            'gesamt_prozent' => $gesamt,
            'aufgaben' => [
                'pflicht_gesamt' => (int) ($reife['aufgaben']['pflicht_gesamt'] ?? 0),
                'erledigt' => (int) ($reife['aufgaben']['erledigt'] ?? 0),
                'offen' => (int) ($reife['aufgaben']['offen'] ?? 0),
                'blockierend' => (int) ($reife['aufgaben']['blockierend'] ?? 0),
            ],
            'blocker_offen' => array_values(array_map('strval', $reife['blocker_offen'] ?? [])),
            'angebotsfaehig' => $angebotsfaehig,
            'naechste_aktion' => $this->naechsteAktion($schritte, $angebotsfaehig, $preisMoeglich, $reife),
            'panels' => [
                'reife' => 'offers.angebotsreife.panel',
                'auslegung' => 'offers.auslegung.vorschau',
                'preis' => 'offers.wp-katalog-matching',
            ],
            'hinweis' => 'Read-only Cockpit. On-the-fly aus bestehenden Services; keine Speicherung, kein Preisanker.',
        ];
    }

    // ---- Ableitungen ----

    /** @param array<string,mixed> $reife */
    private function kriteriumErfuellt(array $reife, string $key): bool
    {
        foreach (($reife['kriterien'] ?? []) as $k) {
            if (($k['key'] ?? null) === $key) {
                return (bool) ($k['erfuellt'] ?? false);
            }
        }

        return false;
    }

    private function reifeSchrittStatus(string $reifeStatus): string
    {
        return match ($reifeStatus) {
            'angebotsfaehig' => 'erledigt',
            'blockiert' => 'blockiert',
            default => 'offen',
        };
    }

    /**
     * @param  array<int,array<string,mixed>>  $schritte
     * @param  array<string,mixed>  $reife
     * @return array<string,mixed>
     */
    private function naechsteAktion(array $schritte, bool $angebotsfaehig, bool $preisMoeglich, array $reife): array
    {
        $erledigt = ['erledigt', 'bereit', 'technisch_bereit'];
        foreach ($schritte as $s) {
            if (in_array($s['status'], $erledigt, true)) {
                continue;
            }
            // Erste nicht-erledigte Etappe bestimmt die nächste Aktion.
            return match ($s['key']) {
                'bedarf' => ['label' => 'Bedarf ausfüllen', 'anker' => '#reife', 'angebot_link' => false, 'disabled' => false],
                'reife' => ['label' => $reife['naechster_schritt'] ?? 'Offene Pflichtpunkte schließen', 'anker' => '#reife', 'angebot_link' => false, 'disabled' => false],
                'auslegung' => ['label' => 'Auslegung prüfen / rechnen', 'anker' => '#auslegung', 'angebot_link' => false, 'disabled' => false],
                'preis' => ['label' => 'Preisfähigkeit klären (OMD/IDS-Anbindung offen)', 'anker' => '#preis', 'angebot_link' => false, 'disabled' => true],
                default => ['label' => 'Weiter', 'anker' => null, 'angebot_link' => false, 'disabled' => true],
            };
        }

        // Alle technischen Etappen erledigt → Angebot.
        return [
            'label' => 'Angebot erstellen',
            'anker' => null,
            'angebot_link' => $angebotsfaehig,
            'disabled' => ! $angebotsfaehig,
            'hinweis' => $preisMoeglich ? null : 'Positionen bleiben zunächst preislos (Preisanbindung offen).',
        ];
    }

    /** @return array<string,mixed> */
    private function schritt(string $key, string $label, string $zone, ?string $anker, string $status, string $detail, string $ampel = 'grau'): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'zone' => $zone,           // technik | kaufmaennisch | ergebnis
            'anker' => $anker,
            'status' => $status,       // erledigt | offen | blockiert | bereit | technisch_bereit | gesperrt
            'detail' => $detail,
            'ampel' => $ampel,
        ];
    }

    /** @return array<string,mixed> */
    private function nichtAnwendbar(int $id, string $grund): array
    {
        return [
            'anwendbar' => false,
            'lead_product_list_id' => $id,
            'gewerk' => 'Unbekannt',
            'gewerk_id' => 0,
            'vorgang_label' => 'Vorgang #'.$id,
            'schritte' => [],
            'technik_prozent' => 0,
            'preis_prozent' => 0,
            'preis_moeglich' => false,
            'gesamt_prozent' => 0,
            'aufgaben' => ['pflicht_gesamt' => 0, 'erledigt' => 0, 'offen' => 0, 'blockierend' => 0],
            'blocker_offen' => [],
            'angebotsfaehig' => false,
            'naechste_aktion' => ['label' => $grund, 'anker' => null, 'angebot_link' => false, 'disabled' => true],
            'panels' => [],
            'hinweis' => $grund,
        ];
    }
}
