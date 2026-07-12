<?php

namespace App\Services\Offer;

use App\Models\Anforderungsprofil;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use App\Models\ProductFormula;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Paket 1 (Bereich 2) — Angebotsreife je Gewerkzeile (`lead_product_lists`), READ-ONLY, on-the-fly.
 *
 * Berechnet die Angebotsreife AUS DEN QUELLTABELLEN (new_leads / lead_alternative_adds /
 * lead_product_lists / product_formulas + LeadProductChecklistValue / phase_sections / master_sets /
 * anforderungsprofile). Es wird NICHTS persistiert und KEINE zweite Statuswahrheit erzeugt —
 * die führenden Wahrheiten bleiben die Quelltabellen.
 *
 * Prozent = 100 × Σ(gewicht·erfüllungsgrad der PFLICHT-Kriterien) / Σ(gewicht der PFLICHT-Kriterien).
 * Jeder Erfüllungsgrad kommt aus einem echten Feld-/Service-Check (nicht kosmetisch); das
 * Formular-Kriterium ist graduell (Fill-Ratio). Angebotsfähig ⇔ alle PFLICHT-Kriterien voll erfüllt.
 *
 * Gewerk-variabel: `kriterienKatalog()` verzweigt je Gewerk (Paket 1 = nur WP article_group=2);
 * PV/Service ergänzen später eigene Kataloge in gleicher Struktur.
 *
 * Konservative, rückbaubare Annahmen (Paket 1):
 * - „technische Auslegung": erfüllt, wenn ein aktives Anforderungsprofil am Objekt/Gewerk einen
 *   Heizlast-Wert trägt; sonst offen (der Auslegung→sections-Adapter ist noch nicht gebaut = Paket 3).
 * - „Preisgrundlage": erfüllt, wenn für das Gewerk Katalog-Basis existiert (master_sets/products) —
 *   der harte Preis-Schutz sitzt in P1-a beim Speichern (hier nur Vorhandensein geprüft).
 * - „Angebotsentwurf" ist OPTIONAL (Statusanzeige), keine Reife-Vorbedingung.
 */
class OfferReadinessService
{
    private const GEWERK_WP = 2;

    /** Heizlast-Ergebnis-Schlüssel, die „technische Auslegung vorhanden" belegen. */
    private const AUSLEGUNG_KEYS = ['phi_hl_kw', 'standardheizlast_kw', 'auslegungsheizlast_kw'];

    /** @return array<string,mixed> */
    public function fuerId(int $leadProductListId): array
    {
        $lpl = LeadProductList::with(['customer', 'alternative', 'articleGroup', 'checklistValues'])
            ->find($leadProductListId);

        if (! $lpl) {
            return $this->leererBericht($leadProductListId);
        }

        return $this->fuer($lpl);
    }

    /** @return array<string,mixed> */
    public function fuer(LeadProductList $lpl): array
    {
        $kriterien = $this->kriterienKatalog($lpl);

        // Erfüllungsgrad je Kriterium (0..1) aus echten Quellen.
        foreach ($kriterien as &$k) {
            $grad = (float) ($k['grad'])($lpl);
            $k['grad'] = max(0.0, min(1.0, $grad));
            $k['erfuellt'] = $k['grad'] >= 0.999;
            unset($k['eval']);
        }
        unset($k);

        $pflicht = array_values(array_filter($kriterien, fn ($k) => $k['gruppe'] !== 'optional'));
        $hart = array_values(array_filter($kriterien, fn ($k) => $k['gruppe'] === 'blocker'));
        $optional = array_values(array_filter($kriterien, fn ($k) => $k['gruppe'] === 'optional'));

        // Prozent nur über PFLICHT-Kriterien (100 % = angebotsfähig).
        $gewichtSum = array_sum(array_map(fn ($k) => $k['gewicht'], $pflicht));
        $erfuelltSum = array_sum(array_map(fn ($k) => $k['gewicht'] * $k['grad'], $pflicht));
        $percent = $gewichtSum > 0 ? (int) round(100 * $erfuelltSum / $gewichtSum) : 0;

        $offeneHart = array_values(array_filter($hart, fn ($k) => ! $k['erfuellt']));
        $offenePflicht = array_values(array_filter($pflicht, fn ($k) => ! $k['erfuellt']));
        $formularOffen = $this->offenNach($pflicht, 'wp_formular_ausgefuellt');
        $technikOffen = $this->offenNach($pflicht, 'technische_auslegung');

        $angebotsfaehig = count($offenePflicht) === 0;

        if (! $angebotsfaehig && count($offeneHart) > 0) {
            $status = 'blockiert';
        } elseif ($formularOffen) {
            $status = 'unvollstaendig';
        } elseif ($technikOffen) {
            $status = 'in_pruefung';
        } elseif ($angebotsfaehig) {
            $status = 'angebotsfaehig';
        } else {
            $status = 'unvollstaendig';
        }

        // Nächster Schritt: erstes offenes PFLICHT-Kriterium in Katalog-Reihenfolge (blocker zuerst).
        $naechster = null;
        foreach ($pflicht as $k) {
            if (! $k['erfuellt']) {
                $naechster = $k['label'];
                break;
            }
        }

        $hinweis = $angebotsfaehig
            ? 'Angebot kann erstellt werden.'
            : (count($offeneHart) > 0
                ? 'Blockierende Pflichtpunkte offen: '.implode(', ', array_map(fn ($k) => $k['label'], $offeneHart))
                : 'Noch offen: '.implode(', ', array_map(fn ($k) => $k['label'], $offenePflicht)));

        return [
            'lead_product_list_id' => (int) $lpl->id,
            'gewerk' => (string) ($lpl->articleGroup->article_group ?? 'Unbekannt'),
            'gewerk_id' => (int) $lpl->product_id,
            'percent' => $percent,
            'status' => $status,
            'angebotsfaehig' => $angebotsfaehig,
            'aufgaben' => [
                'pflicht_gesamt' => count($pflicht),
                'erledigt' => count($pflicht) - count($offenePflicht),
                'offen' => count($offenePflicht),
                'blockierend' => count($offeneHart),
                'optional' => count($optional),
            ],
            'naechster_schritt' => $naechster,
            'blocker_offen' => array_map(fn ($k) => $k['label'], $offeneHart),
            'hinweis' => $hinweis,
            'kriterien' => array_map(fn ($k) => [
                'key' => $k['key'],
                'label' => $k['label'],
                'phase' => $k['phase'],
                'gruppe' => $k['gruppe'], // blocker | pflicht | optional
                'gewicht' => $k['gewicht'],
                'grad' => round($k['grad'], 2),
                'erfuellt' => $k['erfuellt'],
            ], $kriterien),
        ];
    }

    /**
     * Kriterien-Katalog je Gewerk. Paket 1: nur WP (article_group=2).
     * Gruppen: 'blocker' (harter Show-Stopper), 'pflicht' (nötig für volle Reife, weich),
     * 'optional' (Anzeige, nicht reife-relevant).
     *
     * @return array<int,array<string,mixed>>
     */
    private function kriterienKatalog(LeadProductList $lpl): array
    {
        if ((int) $lpl->product_id !== self::GEWERK_WP) {
            // Paket 1 deckt nur WP ab; für andere Gewerke ein minimaler generischer Kern (erweiterbar).
            return $this->generischerKatalog();
        }

        return [
            $this->krit('kunde_vorhanden', 'Kunde vorhanden (Name + Kontakt)', 'kunde', 'blocker', 1, fn ($l) => $this->gradKunde($l)),
            $this->krit('objekt_adresse', 'Objekt / Adresse vorhanden', 'objekt', 'blocker', 1, fn ($l) => $this->gradObjektAdresse($l)),
            $this->krit('gewerk_wp', 'Gewerk Wärmepumpe zugeordnet', 'gewerk', 'blocker', 1, fn ($l) => (int) $l->product_id === self::GEWERK_WP ? 1.0 : 0.0),
            $this->krit('bedarf_erfasst', 'Bedarf / Ziel erfasst', 'bedarf', 'blocker', 1, fn ($l) => $this->gradBedarf($l)),
            $this->krit('dienstleistung', 'Dienstleistung / Phase zugeordnet', 'dienstleistung', 'blocker', 1, fn ($l) => $this->vorhanden($l->service_id)),
            $this->krit('zustaendigkeit', 'Abteilung + Innendienst zugeordnet', 'zustaendigkeit', 'blocker', 1, fn ($l) => $this->gradZustaendigkeit($l)),
            $this->krit('wp_formular_vorhanden', 'WP-Bedarfsformular vorhanden', 'formular', 'blocker', 1, fn ($l) => $this->gradFormularVorhanden()),
            $this->krit('preisgrundlage', 'Preisgrundlage (Katalog) vorhanden', 'preis', 'blocker', 1, fn ($l) => $this->gradPreisgrundlage()),
            $this->krit('wp_formular_ausgefuellt', 'WP-Bedarfsformular vollständig ausgefüllt', 'formular', 'pflicht', 2, fn ($l) => $this->gradFormularAusgefuellt($l)),
            $this->krit('technische_auslegung', 'Technische Auslegung (Heizlast) vorhanden', 'technik', 'pflicht', 1, fn ($l) => $this->gradTechnischeAuslegung($l)),
            $this->krit('angebotsentwurf', 'Angebotsentwurf vorhanden', 'angebot', 'optional', 1, fn ($l) => $this->gradAngebotsentwurf($l)),
            $this->krit('vor_ort_termin', 'Vor-Ort-Aufnahme (angebotsbereit-Flag)', 'objekt', 'optional', 1, fn ($l) => $this->truthy($l->alternative->ready_for_offer ?? null)),
        ];
    }

    /** @return array<int,array<string,mixed>> */
    private function generischerKatalog(): array
    {
        return [
            $this->krit('kunde_vorhanden', 'Kunde vorhanden (Name + Kontakt)', 'kunde', 'blocker', 1, fn ($l) => $this->gradKunde($l)),
            $this->krit('objekt_adresse', 'Objekt / Adresse vorhanden', 'objekt', 'blocker', 1, fn ($l) => $this->gradObjektAdresse($l)),
            $this->krit('gewerk_zugeordnet', 'Gewerk zugeordnet', 'gewerk', 'blocker', 1, fn ($l) => $this->vorhanden($l->product_id)),
            $this->krit('dienstleistung', 'Dienstleistung / Phase zugeordnet', 'dienstleistung', 'blocker', 1, fn ($l) => $this->vorhanden($l->service_id)),
            $this->krit('zustaendigkeit', 'Abteilung + Innendienst zugeordnet', 'zustaendigkeit', 'blocker', 1, fn ($l) => $this->gradZustaendigkeit($l)),
        ];
    }

    /**
     * @param  callable  $eval  (LeadProductList) => float 0..1
     * @return array<string,mixed>
     */
    private function krit(string $key, string $label, string $phase, string $gruppe, int $gewicht, callable $eval): array
    {
        return ['key' => $key, 'label' => $label, 'phase' => $phase, 'gruppe' => $gruppe, 'gewicht' => $gewicht, 'grad' => $eval];
    }

    // ---- Erfüllungsgrad-Checks (read-only, defensiv) ----

    private function gradKunde(LeadProductList $lpl): float
    {
        $c = $lpl->customer;
        if (! $c) {
            return 0.0;
        }
        $name = $this->nichtLeer($c->name) || $this->nichtLeer($c->lastname) || $this->nichtLeer($c->firma);
        $kontakt = $this->nichtLeer($c->phone) || $this->nichtLeer($c->telephone) || $this->nichtLeer($c->email);

        return $name && $kontakt ? 1.0 : 0.0;
    }

    private function gradObjektAdresse(LeadProductList $lpl): float
    {
        $a = $lpl->alternative;
        if (! $a) {
            return 0.0;
        }
        $strukturiert = $this->nichtLeer($a->street) && $this->nichtLeer($a->postcode) && $this->nichtLeer($a->city);

        return ($strukturiert || $this->nichtLeer($a->full_address)) ? 1.0 : 0.0;
    }

    private function gradBedarf(LeadProductList $lpl): float
    {
        return ($this->nichtLeer($lpl->interest) || $this->nichtLeer($lpl->alternative->objective ?? null)) ? 1.0 : 0.0;
    }

    private function gradZustaendigkeit(LeadProductList $lpl): float
    {
        // Reuse der InquiryVerification-Regel: Abteilung + Innendienst vorhanden.
        return ($this->vorhanden($lpl->department_id) >= 1.0 && $this->vorhanden($lpl->employee_id) >= 1.0) ? 1.0 : 0.0;
    }

    private function gradFormularVorhanden(): float
    {
        return $this->wpFormular() ? 1.0 : 0.0;
    }

    private function gradPreisgrundlage(): float
    {
        try {
            $sets = DB::table('master_sets')->where('article_group_id', self::GEWERK_WP)->exists();
            $prod = DB::table('products')->where('article_group', self::GEWERK_WP)->exists();

            return ($sets || $prod) ? 1.0 : 0.0;
        } catch (Throwable $e) {
            return 0.0;
        }
    }

    private function gradFormularAusgefuellt(LeadProductList $lpl): float
    {
        $formula = $this->wpFormular();
        if (! $formula) {
            return 0.0;
        }
        $required = collect($this->asArray($formula->fields))
            ->filter(fn ($f) => ! empty($f['required']))
            ->map(fn ($f) => $f['key'] ?? $f['name'] ?? null)
            ->filter()
            ->values();

        if ($required->isEmpty()) {
            return 1.0;
        }

        $cv = $lpl->checklistValues->firstWhere('product_formula_id', $formula->id);
        $filled = $cv
            ? collect($this->asArray($cv->filled_values))
                ->filter(fn ($v) => $v !== null && $v !== '' && $v !== [])
                ->keys()
            : collect();

        return $required->intersect($filled)->count() / $required->count();
    }

    private function gradTechnischeAuslegung(LeadProductList $lpl): float
    {
        try {
            $exists = Anforderungsprofil::query()
                ->aktiv()
                ->where(function ($w) use ($lpl) {
                    $w->where(function ($a) use ($lpl) {
                        $a->where('verankerbar_type', LeadAlternativeAdd::class)
                            ->where('verankerbar_id', $lpl->alternative_id);
                    })->orWhere(function ($a) use ($lpl) {
                        $a->where('verankerbar_type', LeadProductList::class)
                            ->where('verankerbar_id', $lpl->id);
                    });
                })
                ->whereHas('werte', fn ($x) => $x->whereIn('schluessel', self::AUSLEGUNG_KEYS))
                ->exists();

            return $exists ? 1.0 : 0.0;
        } catch (Throwable $e) {
            return 0.0; // konservativ: ohne belegte Auslegung = offen
        }
    }

    private function gradAngebotsentwurf(LeadProductList $lpl): float
    {
        try {
            $exists = DB::table('offer_folders')
                ->where('customer_id', $lpl->customer_id)
                ->where('alternative_id', $lpl->alternative_id)
                ->where('product_id', $lpl->product_id)
                ->exists();

            return $exists ? 1.0 : 0.0;
        } catch (Throwable $e) {
            return 0.0;
        }
    }

    // ---- Helfer ----

    private function wpFormular(): ?ProductFormula
    {
        return ProductFormula::where('product_id', self::GEWERK_WP)
            ->where('status', 'Published')
            ->orderBy('id')
            ->first();
    }

    private function vorhanden($value): float
    {
        return ($value !== null && $value !== '' && $value !== 0 && $value !== '0') ? 1.0 : 0.0;
    }

    private function truthy($value): float
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 1.0 : 0.0;
    }

    private function nichtLeer($value): bool
    {
        return $value !== null && trim((string) $value) !== '';
    }

    /** @param array<int,array<string,mixed>> $pflicht */
    private function offenNach(array $pflicht, string $key): bool
    {
        foreach ($pflicht as $k) {
            if ($k['key'] === $key) {
                return ! $k['erfuellt'];
            }
        }

        return false;
    }

    /** @return array<mixed> */
    private function asArray($raw): array
    {
        if (is_array($raw)) {
            return $raw;
        }
        if (is_string($raw) && $raw !== '') {
            $d = json_decode($raw, true);

            return is_array($d) ? $d : [];
        }

        return [];
    }

    /** @return array<string,mixed> */
    private function leererBericht(int $id): array
    {
        return [
            'lead_product_list_id' => $id,
            'gewerk' => 'Unbekannt',
            'gewerk_id' => 0,
            'percent' => 0,
            'status' => 'unvollstaendig',
            'angebotsfaehig' => false,
            'aufgaben' => ['pflicht_gesamt' => 0, 'erledigt' => 0, 'offen' => 0, 'blockierend' => 0, 'optional' => 0],
            'naechster_schritt' => null,
            'blocker_offen' => [],
            'hinweis' => 'Vorgang nicht gefunden.',
            'kriterien' => [],
        ];
    }
}
