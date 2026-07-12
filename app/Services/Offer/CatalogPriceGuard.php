<?php

namespace App\Services\Offer;

use App\Models\MasterSetComponent;

/**
 * P1-a Preis-Integrität — Server-Pricing-Schutz für den Angebots-Speicherpfad.
 *
 * Erzwingt für `sections`-Positionsknoten mit stabiler Katalog-Referenz
 * (`component_id` → `MasterSetComponent`) den Katalog-EK (`purchase_price`) und
 * -VK (`unit_price`); ein abweichender Browser-Payload-Preis wird verworfen.
 * Knoten ohne verifizierbaren Anker werden als `preis_quelle='manuell'`
 * markiert (Preis bleibt unverändert). Ein gesetztes `component_id` ohne
 * DB-Treffer wird als `preis_quelle='katalog_fehlt'` markiert — der vorhandene
 * Preis bleibt erhalten, es wird NIE ein stiller 0-Preis gesetzt.
 *
 * Rein additiv: schreibt nur `ek`/`price`/`purchase_price`/`unit_price` (aus dem
 * Katalog), die Preiseinheits-Divisoren `price_unit_value`/`cost_price_unit_value`
 * (auf 1 normalisiert, B1-Schutz) sowie den Marker `preis_quelle` in den JSON-Knoten.
 * Kein UI, keine Migration, kein DB-Schreibzugriff. Abschaltbar via config('offer.enforce_catalog_pricing').
 *
 * Scope P1-a (bewusst eng): NUR `component_id`. `product_id`-Reprice,
 * `sub_*`-ID-Verlust, GK/Wagnis und Engine 2 sind ausdrücklich NICHT Teil.
 */
class CatalogPriceGuard
{
    /** Knoten-Keys, unter denen Kind-Positionen hängen (Spiegel von OfferController::offerNodeChildren). */
    private const CHILD_KEYS = ['subItems', 'sub_items', 'children', 'components', 'items'];

    /** Kinds ohne Preis (Spiegel von OfferController::offerLineTotals). */
    private const NON_PRICE_KINDS = ['note', 'text', 'heading', 'headline', 'description'];

    /** VK-Preis-Keys in Lese-Reihenfolge (Spiegel von OfferController::offerNodeVkPrice). */
    private const VK_KEYS = ['price', 'unit_price', 'sale_price', 'vk', 'rate', 'total_price'];

    /** EK-Preis-Keys in Lese-Reihenfolge (Spiegel von OfferController::offerNodeEkPrice). */
    private const EK_KEYS = ['ek', 'cost', 'buying_price', 'purchase_price', 'cost_price'];

    /** VK-Preiseinheit-Divisor-Keys (Spiegel von OfferController::offerPriceUnitValue, VK). */
    private const VK_UNIT_KEYS = ['price_unit_value', 'sale_price_unit_value', 'vk_price_unit_value'];

    /** EK-Preiseinheit-Divisor-Keys (Spiegel von OfferController::offerPriceUnitValue, 'cost'). */
    private const EK_UNIT_KEYS = ['cost_price_unit_value', 'ek_price_unit_value', 'purchase_price_unit_value', 'price_unit_value'];

    public const SRC_VERIFIED  = 'katalog_verifiziert';
    public const SRC_CORRECTED = 'katalog_korrigiert';
    public const SRC_MISSING   = 'katalog_fehlt';
    public const SRC_MANUAL    = 'manuell';

    /**
     * Katalog-Preise laden (gebündelt, ein Query) und auf die Sektionen anwenden.
     *
     * @param  array<int,mixed>  $sections
     * @return array<int,mixed>
     */
    public function apply(array $sections): array
    {
        $ids = [];
        $this->collectComponentIds($sections, $ids);

        $catalog = [];
        if (!empty($ids)) {
            $catalog = MasterSetComponent::query()
                ->whereIn('id', array_values(array_unique($ids)))
                ->get(['id', 'purchase_price', 'unit_price'])
                ->reduce(function (array $carry, MasterSetComponent $component): array {
                    $carry[(int) $component->id] = [
                        'ek' => round((float) $component->purchase_price, 2),
                        'vk' => round((float) $component->unit_price, 2),
                    ];

                    return $carry;
                }, []);
        }

        return $this->applyWithCatalog($sections, $catalog);
    }

    /**
     * Reine Logik (DB-frei, isoliert testbar): wendet einen bereits geladenen
     * Katalog auf die Sektionen an.
     *
     * @param  array<int,mixed>  $sections
     * @param  array<int,array{ek:float,vk:float}>  $catalog  [component_id => ['ek'=>.., 'vk'=>..]]
     * @return array<int,mixed>
     */
    public function applyWithCatalog(array $sections, array $catalog): array
    {
        foreach ($sections as $sIdx => $section) {
            if (!is_array($section) || empty($section['items']) || !is_array($section['items'])) {
                continue;
            }

            foreach ($section['items'] as $iIdx => $item) {
                if (is_array($item)) {
                    $sections[$sIdx]['items'][$iIdx] = $this->guardNode($item, $catalog);
                }
            }
        }

        return $sections;
    }

    /**
     * @param  array<string,mixed>  $node
     * @param  array<int,array{ek:float,vk:float}>  $catalog
     * @return array<string,mixed>
     */
    private function guardNode(array $node, array $catalog): array
    {
        $componentId = $this->componentId($node);

        if ($componentId !== null) {
            if (array_key_exists($componentId, $catalog)) {
                $ek = $catalog[$componentId]['ek'];
                $vk = $catalog[$componentId]['vk'];

                // B1: Die tatsächliche Zeilensumme ist (qty / price_unit_value) * price.
                // Der Preiseinheits-Divisor ist ein frei editierbares Payload-Feld und
                // muss für Katalog-Knoten mit-kontrolliert werden, sonst bleibt die
                // Formel trotz erzwungenem Preis manipulierbar. Katalog-Preise sind
                // Stück-Preise (Divisor 1, wie beim Hydrieren) → auf 1 normalisieren.
                $corrected = $this->pricesDiffer($node, $ek, $vk)
                    || abs($this->priceUnit($node, self::VK_UNIT_KEYS) - 1.0) > 0.001
                    || abs($this->priceUnit($node, self::EK_UNIT_KEYS) - 1.0) > 0.001;

                // Katalog gewinnt — Payload-Preis + Preiseinheit werden verworfen.
                $node['ek'] = $ek;
                $node['purchase_price'] = $ek;
                $node['price'] = $vk;
                $node['unit_price'] = $vk;
                $node['price_unit_value'] = 1.0;
                $node['cost_price_unit_value'] = 1.0;

                // 'verifiziert' nur, wenn die GESAMTE Preisformel serverseitig gesetzt
                // ist (Preis + EK + beide Divisoren); jede Payload-Abweichung → 'korrigiert'.
                $node['preis_quelle'] = $corrected ? self::SRC_CORRECTED : self::SRC_VERIFIED;
            } else {
                // component_id gesetzt, aber Komponente fehlt/gelöscht:
                // Preis NICHT anfassen (kein stiller 0), nur markieren.
                $node['preis_quelle'] = self::SRC_MISSING;
            }
        } elseif ($this->isPricedLeaf($node)) {
            // Position ohne sicheren Anker → markieren, Preis unverändert.
            $node['preis_quelle'] = self::SRC_MANUAL;
        }

        foreach ($this->childKeys($node) as $key) {
            foreach ($node[$key] as $cIdx => $child) {
                if (is_array($child)) {
                    $node[$key][$cIdx] = $this->guardNode($child, $catalog);
                }
            }
        }

        return $node;
    }

    /**
     * @param  array<int,mixed>  $sections
     * @param  array<int,int>  $ids
     */
    private function collectComponentIds(array $sections, array &$ids): void
    {
        foreach ($sections as $section) {
            if (!is_array($section) || empty($section['items']) || !is_array($section['items'])) {
                continue;
            }

            foreach ($section['items'] as $item) {
                if (is_array($item)) {
                    $this->collectFromNode($item, $ids);
                }
            }
        }
    }

    /**
     * @param  array<string,mixed>  $node
     * @param  array<int,int>  $ids
     */
    private function collectFromNode(array $node, array &$ids): void
    {
        $componentId = $this->componentId($node);
        if ($componentId !== null) {
            $ids[] = $componentId;
        }

        foreach ($this->childKeys($node) as $key) {
            foreach ($node[$key] as $child) {
                if (is_array($child)) {
                    $this->collectFromNode($child, $ids);
                }
            }
        }
    }

    /**
     * @param  array<string,mixed>  $node
     */
    private function componentId(array $node): ?int
    {
        foreach (['component_id', 'master_set_component_id'] as $key) {
            if (isset($node[$key]) && is_numeric($node[$key]) && (int) $node[$key] > 0) {
                return (int) $node[$key];
            }
        }

        return null;
    }

    /**
     * @param  array<string,mixed>  $node
     * @return array<int,string>
     */
    private function childKeys(array $node): array
    {
        $keys = [];
        foreach (self::CHILD_KEYS as $key) {
            if (!empty($node[$key]) && is_array($node[$key])) {
                $keys[] = $key;
            }
        }

        return $keys;
    }

    /**
     * Ein Preis tragender Blattknoten (kein Container, kein Text/Notiz-Kind).
     *
     * @param  array<string,mixed>  $node
     */
    private function isPricedLeaf(array $node): bool
    {
        if (!empty($this->childKeys($node))) {
            return false;
        }

        $kind = strtolower(trim((string) ($node['kind'] ?? $node['type'] ?? $node['item_type'] ?? $node['category'] ?? '')));

        return !in_array($kind, self::NON_PRICE_KINDS, true);
    }

    /**
     * @param  array<string,mixed>  $node
     */
    private function pricesDiffer(array $node, float $ek, float $vk): bool
    {
        return abs($this->firstNumeric($node, self::EK_KEYS) - $ek) > 0.001
            || abs($this->firstNumeric($node, self::VK_KEYS) - $vk) > 0.001;
    }

    /**
     * Preiseinheits-Divisor wie OfferController::offerPriceUnitValue lesen
     * (erster vorhandener Key; Wert ≤ 0 oder fehlend ⇒ 1.0).
     *
     * @param  array<string,mixed>  $node
     * @param  array<int,string>  $keys
     */
    private function priceUnit(array $node, array $keys): float
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $node) && is_numeric($node[$key])) {
                $value = (float) $node[$key];

                return $value > 0 ? $value : 1.0;
            }
        }

        return 1.0;
    }

    /**
     * @param  array<string,mixed>  $node
     * @param  array<int,string>  $keys
     */
    private function firstNumeric(array $node, array $keys): float
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $node) && is_numeric($node[$key])) {
                return (float) $node[$key];
            }
        }

        return 0.0;
    }
}
