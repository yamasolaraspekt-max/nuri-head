<?php

namespace App\Services\Offer;

use App\Models\Anforderungsprofil;
use App\Models\LeadAlternativeAdd;
use App\Models\LeadProductList;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * P3-c-preview (Bereich 2) — READ-ONLY Vorschau: Auslegung → proposed sections (WP).
 *
 * Baut aus dem AKTIVEN Anforderungsprofil (Auslegungs-Wahrheit) eine *Vorschlags*-Struktur in der
 * Form von `offer_details.sections`, damit ein späteres Paket sie übernehmen kann. Es wird
 * NICHTS geschrieben, NICHTS persistiert, KEIN Angebot berührt, KEINE zweite Wahrheit erzeugt.
 *
 * Verbindliche Regeln (Yama-Freigabe P3-c, 2026-07-13):
 *  - Nur WP (article_groups.id=2). `id=16` (=„Tapete") wird NIE verwendet.
 *  - KEIN Preis, KEINE Schätzpreise, KEIN `component_id` (Geräte→Komponente-Zuordnung ist nicht
 *    eindeutig geregelt = spätere P3-d-Fachentscheidung) ⇒ alle Positionen `preis_status='katalog_anker_fehlt'`.
 *  - Das bepreisbare WP-Set (master_sets ag=2) wird NUR als Hinweis gemeldet, nicht zugeordnet.
 *  - Operanden-Gate: fehlender Wert ⇒ Position wird als AUFGABE markiert (`datenlage='fehlt'`),
 *    nie erfunden/geschätzt/gerechnet.
 */
class AuslegungVorschlagService
{
    private const GEWERK_WP = 2;

    /** Schlüssel, deren Vorhandensein „Auslegung liegt vor" belegt. */
    private const HEIZLAST_KEYS = ['phi_hl_kw', 'standardheizlast_kw'];

    /** @return array<string,mixed> */
    public function fuerId(int $leadProductListId): array
    {
        $lpl = LeadProductList::with(['customer', 'alternative'])->find($leadProductListId);

        if (! $lpl) {
            return $this->leererBericht($leadProductListId, 'Vorgang nicht gefunden.');
        }

        return $this->fuer($lpl);
    }

    /** @return array<string,mixed> */
    public function fuer(LeadProductList $lpl): array
    {
        if ((int) $lpl->product_id !== self::GEWERK_WP) {
            return $this->nichtAnwendbar((int) $lpl->id, 'Vorschau nur für Wärmepumpe (Gewerk WP).');
        }

        $werte = $this->werteLesen($lpl);

        $heizlast = $werte['phi_hl_kw'] ?? $werte['standardheizlast_kw'] ?? null;
        $vorlauf = $werte['vorlauf_c'] ?? null;
        $auslegungVorhanden = $heizlast !== null;

        $positionen = [
            $this->positionGeraet($heizlast),
            $this->positionPuffer(),
            $this->positionWarmwasser($werte['phi_ww_kw'] ?? null),
            $this->positionHydraulik($vorlauf, $werte['spreizung_k'] ?? null),
            $this->positionMontage(),
        ];

        $offeneAufgaben = count(array_filter($positionen, fn ($p) => ! empty($p['aufgabe'])));

        return [
            'proposed' => true,
            'anwendbar' => true,
            'gewerk_id' => self::GEWERK_WP,
            'auslegung_vorhanden' => $auslegungVorhanden,
            'kurzergebnis' => $this->kurzergebnis($auslegungVorhanden, $heizlast, $vorlauf),
            'gesamt_ampel' => $auslegungVorhanden ? ($vorlauf ? 'gruen' : 'gelb') : 'grau',
            'offene_aufgaben' => $offeneAufgaben,
            'katalog_hinweis' => $this->katalogHinweis(),
            'foerderung_hinweis' => $this->foerderungHinweis(),
            'sections' => [[
                'title' => 'Wärmepumpe – Auslegung (Vorschlag)',
                'herkunft' => 'auslegung',
                'items' => $positionen,
            ]],
            'hinweis' => 'Read-only Vorschau. Nicht bepreist, keine Angebotsübernahme, keine Speicherung.',
        ];
    }

    // ---- Werte aus aktivem Anforderungsprofil (read-only) ----

    /** @return array<string,array{wert:?string,wert_num:?float,einheit:?string,datenlage:?string}> */
    private function werteLesen(LeadProductList $lpl): array
    {
        try {
            $profil = Anforderungsprofil::query()
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
                ->with('werte')
                ->orderByDesc('version')
                ->first();

            if (! $profil) {
                return [];
            }

            $map = [];
            foreach ($profil->werte as $w) {
                $map[(string) $w->schluessel] = [
                    'wert' => $w->wert,
                    'wert_num' => $w->wert_num !== null ? (float) $w->wert_num : null,
                    'einheit' => $w->einheit,
                    'datenlage' => $w->datenlage,
                ];
            }

            return $map;
        } catch (Throwable $e) {
            return [];
        }
    }

    // ---- Positions-Fabrik ----

    /** @param array{wert:?string,wert_num:?float,einheit:?string,datenlage:?string}|null $heizlast */
    private function positionGeraet(?array $heizlast): array
    {
        if ($heizlast && $heizlast['wert_num'] !== null) {
            return $this->position('wp_geraet', 'Wärmepumpe (Gerät)', 1, 'Stk', [
                'kennzahl' => ['label' => 'Benötigte Heizleistung', 'wert' => $heizlast['wert_num'], 'einheit' => $heizlast['einheit'] ?? 'kW'],
                'datenlage' => $heizlast['datenlage'] ?? 'berechnet',
                'ampel' => 'gruen',
                'bestaetigung' => 'zu_bestaetigen',
                'begruendung' => 'Leistungsbedarf aus Heizlast (Anforderungsprofil). Geräteauswahl offen — spätere P3-b/P3-d-Entscheidung.',
            ]);
        }

        return $this->position('wp_geraet', 'Wärmepumpe (Gerät)', 1, 'Stk', [
            'datenlage' => 'fehlt',
            'ampel' => 'grau',
            'bestaetigung' => 'zu_bestaetigen',
            'aufgabe' => 'Heizlast/Auslegung im Anforderungsprofil fehlt — zuerst Auslegung rechnen.',
        ]);
    }

    private function positionPuffer(): array
    {
        return $this->position('pufferspeicher', 'Pufferspeicher', 1, 'Stk', [
            'datenlage' => 'fehlt',
            'ampel' => 'grau',
            'bestaetigung' => 'zu_bestaetigen',
            'aufgabe' => 'Puffer-Dimensionierung noch nicht verfügbar (P3-f) — manuell festlegen.',
        ]);
    }

    /** @param array{wert:?string,wert_num:?float,einheit:?string,datenlage:?string}|null $phiWw */
    private function positionWarmwasser(?array $phiWw): array
    {
        $extra = $phiWw && $phiWw['wert_num'] !== null
            ? ['kennzahl' => ['label' => 'WW-Leistung', 'wert' => $phiWw['wert_num'], 'einheit' => $phiWw['einheit'] ?? 'kW'], 'datenlage' => $phiWw['datenlage'] ?? 'berechnet', 'ampel' => 'gelb']
            : ['datenlage' => 'fehlt', 'ampel' => 'grau', 'aufgabe' => 'Warmwasserbedarf/Speichergröße noch nicht ermittelt — manuell festlegen.'];

        return $this->position('warmwasserspeicher', 'Warmwasserspeicher', 1, 'Stk', $extra + [
            'bestaetigung' => 'zu_bestaetigen',
        ]);
    }

    /**
     * @param array{wert:?string,wert_num:?float,einheit:?string,datenlage:?string}|null $vorlauf
     * @param array{wert:?string,wert_num:?float,einheit:?string,datenlage:?string}|null $spreizung
     */
    private function positionHydraulik(?array $vorlauf, ?array $spreizung): array
    {
        if ($vorlauf && $vorlauf['wert_num'] !== null) {
            return $this->position('hydraulik_zubehoer', 'Hydraulik / Zubehör', 1, 'Pauschal', [
                'kennzahl' => ['label' => 'Vorlauftemperatur', 'wert' => $vorlauf['wert_num'], 'einheit' => $vorlauf['einheit'] ?? '°C'],
                'datenlage' => $vorlauf['datenlage'] ?? 'berechnet',
                'ampel' => 'gelb',
                'bestaetigung' => 'zu_bestaetigen',
                'begruendung' => 'Vorlauf aus Auslegung; konkrete Hydraulik-Bauteile manuell/über HK-Modul (M5) zu bestimmen.',
            ]);
        }

        return $this->position('hydraulik_zubehoer', 'Hydraulik / Zubehör', 1, 'Pauschal', [
            'datenlage' => 'fehlt',
            'ampel' => 'grau',
            'bestaetigung' => 'zu_bestaetigen',
            'aufgabe' => 'Vorlauf/Hydraulik-Daten fehlen — Auslegung vervollständigen.',
        ]);
    }

    private function positionMontage(): array
    {
        return $this->position('montage_lohn', 'Montage / Lohn', 1, 'Pauschal', [
            'datenlage' => 'fehlt',
            'ampel' => 'grau',
            'bestaetigung' => 'zu_bestaetigen',
            'aufgabe' => 'Montageumfang manuell festlegen (kein automatischer Preis).',
        ]);
    }

    /**
     * @param  array<string,mixed>  $extra
     * @return array<string,mixed>
     */
    private function position(string $klasse, string $titel, float $menge, string $einheit, array $extra): array
    {
        return array_merge([
            'klasse' => $klasse,
            'titel' => $titel,
            'menge' => $menge,
            'einheit' => $einheit,
            'kennzahl' => null,
            'component_id' => null,                 // in P3-c IMMER null (keine eindeutige Zuordnung)
            'preis_status' => 'katalog_anker_fehlt', // nicht bepreist / nicht angebotsfähig als Preisposition
            'datenlage' => 'fehlt',
            'ampel' => 'grau',
            'bestaetigung' => 'zu_bestaetigen',
            'begruendung' => null,
            'aufgabe' => null,
        ], $extra);
    }

    // ---- Hinweise (read-only) ----

    /** @return array<string,mixed> */
    private function katalogHinweis(): array
    {
        try {
            $set = DB::table('master_sets')->where('article_group_id', self::GEWERK_WP)->orderBy('id')->first(['id', 'name']);
            if (! $set) {
                return ['vorhanden' => false, 'text' => 'Kein WP-Set im Katalog gefunden — Positionen bleiben unbepreisst.'];
            }
            $varianten = (int) DB::table('master_set_components')->where('master_set_id', $set->id)->count();

            return [
                'vorhanden' => true,
                'set_id' => (int) $set->id,
                'text' => 'Bepreisbares WP-Set vorhanden ('.$varianten.' Varianten). Zuordnung Gerät→Komponente offen (P3-d) — im Preview wird kein Anker gesetzt.',
            ];
        } catch (Throwable $e) {
            return ['vorhanden' => false, 'text' => 'Katalog-Status nicht lesbar.'];
        }
    }

    /** @return array<string,mixed> */
    private function foerderungHinweis(): array
    {
        return [
            'typ' => 'hinweis',
            'text' => 'Förderung (KfW/BEG) separat prüfen — keine Preisposition im Angebot.',
        ];
    }

    private function kurzergebnis(bool $vorhanden, ?array $heizlast, ?array $vorlauf): string
    {
        if (! $vorhanden) {
            return 'Noch keine Auslegung — Heizlast im Anforderungsprofil fehlt. Zuerst Auslegung rechnen.';
        }

        $teile = ['Heizlast '.$this->fmt($heizlast).' kW'];
        if ($vorlauf && $vorlauf['wert_num'] !== null) {
            $teile[] = 'Vorlauf '.$this->fmt($vorlauf).' °C';
        }

        return 'WP-Auslegung: '.implode(', ', $teile).'. Positionen als Vorschlag, nicht bepreist.';
    }

    private function fmt(?array $wert): string
    {
        return $wert && $wert['wert_num'] !== null ? rtrim(rtrim(number_format($wert['wert_num'], 2, ',', '.'), '0'), ',') : '—';
    }

    /** @return array<string,mixed> */
    private function nichtAnwendbar(int $id, string $grund): array
    {
        return [
            'proposed' => true,
            'anwendbar' => false,
            'gewerk_id' => 0,
            'auslegung_vorhanden' => false,
            'kurzergebnis' => $grund,
            'gesamt_ampel' => 'grau',
            'offene_aufgaben' => 0,
            'katalog_hinweis' => ['vorhanden' => false, 'text' => ''],
            'foerderung_hinweis' => ['typ' => 'hinweis', 'text' => ''],
            'sections' => [],
            'hinweis' => $grund,
        ];
    }

    /** @return array<string,mixed> */
    private function leererBericht(int $id, string $grund): array
    {
        return $this->nichtAnwendbar($id, $grund);
    }
}
