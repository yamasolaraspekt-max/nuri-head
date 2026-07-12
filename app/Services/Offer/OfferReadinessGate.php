<?php

namespace App\Services\Offer;

use App\Models\LeadProductList;

/**
 * Paket 2a (Bereich 2) — zentrales Angebotsreife-GATE für die Erstellpfade.
 *
 * READ-ONLY: liest ausschließlich {@see OfferReadinessService}. Es wird NICHTS geschrieben,
 * NICHTS persistiert und KEINE zweite Statuswahrheit erzeugt — die Reife wird on-the-fly aus den
 * Quelltabellen berechnet. Ein einziger Guard/Resolver für alle Erstellpfade (keine drei Logiken).
 *
 * Regel (Yama-Entscheidungen Paket 2a, 2026-07-12):
 *  1. Sperrt NUR, wenn offene BLOCKER existieren (nicht bei bloßer Unvollständigkeit / !angebotsfaehig).
 *  2. Gilt zunächst NUR für WP (product_id = 2). Non-WP wird unverändert durchgelassen.
 *  3. KEIN Override (kommt als eigenes Paket 2b). Kein status_msg-Audit-Ersatz.
 *  4. Kein auflösbarer LeadProductList ⇒ durchlassen (Legacy-/ungegateter Fall, nur im Bericht
 *     dokumentiert — kein Marker in der DB, kein erfundener Reifewert).
 */
class OfferReadinessGate
{
    /** WP-Gewerk (article_groups.id). */
    public const GEWERK_WP = 2;

    public function __construct(private OfferReadinessService $service)
    {
    }

    /**
     * Prüft die Angebotsreife für die Kunde/Objekt/Gewerk-Kombination.
     *
     * @return array{blocker: array<int,string>, hinweis: string}|null
     *   null  = Anlage erlaubt (Non-WP · kein LPL · keine offenen Blocker)
     *   array = gesperrt: Blocker-Labels (KEINE PII) + verständlicher Hinweis
     */
    public function pruefe(int $customerId, ?int $alternativeId, int $productId): ?array
    {
        // Entscheidung 2: Gate nur für WP — alles andere unverändert durchlassen.
        if ($productId !== self::GEWERK_WP) {
            return null;
        }

        $lplId = $this->leadProductListId($customerId, $alternativeId, $productId);

        // Entscheidung 4: kein LeadProductList ⇒ nicht berechenbar ⇒ durchlassen (Legacy-Fall).
        if ($lplId === null) {
            return null;
        }

        $reife = $this->service->fuerId($lplId);

        // blocker_offen ist eine Liste offener Blocker-Labels (leer = keine Blocker).
        $blocker = array_values(array_filter(
            array_map(fn ($l) => trim((string) $l), $reife['blocker_offen'] ?? []),
            fn ($l) => $l !== ''
        ));

        // Entscheidung 1: nur bei offenen Blockern sperren.
        if ($blocker === []) {
            return null;
        }

        return [
            'blocker' => $blocker,
            'hinweis' => 'Angebot kann nicht erstellt werden — offene Pflichtpunkte (Blocker): '
                .implode(', ', $blocker).'.',
        ];
    }

    /**
     * Löst die Gewerkzeile (lead_product_lists) zur Kombination auf.
     * Deterministisch (kleinste id), damit das Gate reproduzierbar greift.
     */
    private function leadProductListId(int $customerId, ?int $alternativeId, int $productId): ?int
    {
        $id = LeadProductList::query()
            ->where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->when($alternativeId !== null, fn ($q) => $q->where('alternative_id', $alternativeId))
            ->orderBy('id')
            ->value('id');

        return $id !== null ? (int) $id : null;
    }
}
