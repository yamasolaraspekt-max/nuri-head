<?php

namespace App\Services\Heizkoerper;

use App\Models\Accessory;
use App\Models\ValveInsertCompatibility;

/**
 * Ventil-/Zubehör-Kompatibilität nach den D3-Regeln (Bauplan §5) — M3 iv-b.
 * Neubau (kein Port). Voreinstellstufe wird über den abgenommenen HydraulicService berechnet
 * (kvErforderlich/voreinstellung) — NICHT dupliziert. Katalog-Quelle: accessories +
 * valve_insert_compatibility (nur ticket_testing bis M5). Bei leerer Serien-Kompatibilität
 * degradiert der Service sauber zu 'regel-kandidaten'.
 */
class CompatibilityService
{
    public function __construct(private HydraulicService $hydraulic = new HydraulicService) {}

    public function fuerHeizkoerper(RadiatorSituation $s): CompatibilityResult
    {
        $positionen = [];
        $sperren = [];
        $hinweise = [];

        // Datenlage: valve_insert_compatibility (serien-präzise) vs Regel-Kandidaten
        $vic = $this->findValveInsertCompatibility($s);
        $datenqualitaet = $vic ? 'serien-praezise' : 'regel-kandidaten';

        // Kopf-Anschluss-Norm: präzise aus VIC, sonst Bestand, sonst Default M30x1,5
        $kopfNorm = $vic?->kopf_anschluss_norm ?? $s->kopfNormBestand ?? 'M30x1_5';

        if ($s->istVentilHeizkoerper) {
            // §5.1 Ventil-Heizkörper (integriertes Ventilunterteil) → nur Einsatz + Kopf/Adapter
            if ($vic && $vic->einsatz) {
                $positionen[] = $this->pos($vic->einsatz, 'ventileinsatz', '§5.1 Einsatz (serien-präzise aus valve_insert_compatibility)');
            } elseif ($einsatz = $this->pick('ventileinsatz', $s)) {
                $positionen[] = $this->pos($einsatz, 'ventileinsatz', '§5.1 Ventil-HK → Einsatz (Regel-Kandidat)');
            } else {
                $sperren[] = '§5.1/§5.3: kein passender Ventileinsatz gefunden' . ($s->anschlussFuehrung === 'einrohr' ? ' (einrohr-tauglich)' : '');
            }

            $kopf = ($vic && $vic->adapter) ? $vic->adapter : ($this->pick('thermostatkopf', $s, $kopfNorm) ?? $this->pick('thermostatkopf', $s));
            if ($kopf) {
                $positionen[] = $this->pos($kopf, 'thermostatkopf', '§5.1 Kopf/Adapter nach kopf_anschluss_norm=' . $kopfNorm);
            }
        } else {
            // §5.2 Kompakt-/Standard-HK (ohne integr. Ventil) → Ventil + Kopf + Rücklaufverschraubung
            if ($ventil = $this->pick('thermostatventil', $s)) {
                $positionen[] = $this->pos($ventil, 'thermostatventil', '§5.2 Kompakt-HK → Ventil' . ($ventil->voreinstellbar ? ' (voreinstellbar, §5.4)' : ''));
            } else {
                $sperren[] = '§5.2/§5.3: kein passendes Thermostatventil gefunden' . ($s->anschlussFuehrung === 'einrohr' ? ' — Zweirohr-Armatur an Einrohr ist gesperrt (§5.3)' : '');
            }

            if ($kopf = ($this->pick('thermostatkopf', $s, $kopfNorm) ?? $this->pick('thermostatkopf', $s))) {
                $positionen[] = $this->pos($kopf, 'thermostatkopf', '§5.2 Kopf nach kopf_anschluss_norm=' . $kopfNorm);
            }
            if ($rl = $this->pick('ruecklaufverschraubung', $s)) {
                $positionen[] = $this->pos($rl, 'ruecklaufverschraubung', '§5.2 Rücklaufverschraubung');
            }
        }

        // §5.5 Voreinstellstufe über den abgenommenen HydraulicService (nicht duplizieren)
        $voreinstellstufe = null;
        if ($s->heizlastW !== null && $s->heizlastW > 0 && $s->spreizungK !== null && $s->spreizungK > 0) {
            $v = $this->hydraulic->volumenstrom($s->heizlastW, $s->spreizungK);
            $kv = $this->hydraulic->kvErforderlich($v, $s->dpVentilMbar);
            $voreinstellstufe = $this->hydraulic->voreinstellung($kv);
        } else {
            $hinweise[] = '§5.5: Voreinstellstufe erst mit Heizlast + Spreizung berechenbar — sonst am Objekt einregulieren.';
        }

        // §5.6 Altnorm-Kopf (RAV/RAVL) → Austauschempfehlung
        if (in_array($s->kopfNormBestand, ['RAV', 'RAVL'], true)) {
            $hinweise[] = '§5.6: Altnorm-Kopf ' . $s->kopfNormBestand . ' → Austauschempfehlung (Adapter auf M30x1,5 oder Einsatztausch).';
        }

        return new CompatibilityResult($datenqualitaet, $positionen, $voreinstellstufe, $sperren, $hinweise);
    }

    /** Zubehör je Kategorie; §5.3 Einrohr-Filter, §5.4 voreinstellbar bevorzugt. */
    private function pick(string $categoryCode, RadiatorSituation $s, ?string $kopfNorm = null): ?Accessory
    {
        $q = Accessory::query()
            ->where('aktiv', true)
            ->whereHas('category', fn ($c) => $c->where('code', $categoryCode));

        if ($s->anschlussFuehrung === 'einrohr') {
            $q->where('einrohr_tauglich', true); // §5.3 nur einrohr-taugliche Armaturen
        }
        if ($kopfNorm !== null) {
            $q->where('kopf_anschluss_norm', $kopfNorm);
        }

        return $q->orderByDesc('voreinstellbar')->orderBy('id')->first(); // §5.4
    }

    private function findValveInsertCompatibility(RadiatorSituation $s): ?ValveInsertCompatibility
    {
        if (! $s->hkHersteller) {
            return null;
        }

        $q = ValveInsertCompatibility::query()->where('hk_hersteller', $s->hkHersteller);

        if ($s->hkSerie !== null) {
            $q->where(fn ($w) => $w->whereNull('hk_serie')->orWhere('hk_serie', $s->hkSerie));
        }
        if ($s->baujahr !== null) {
            $q->where(fn ($w) => $w->whereNull('baujahr_von')->orWhere('baujahr_von', '<=', $s->baujahr))
                ->where(fn ($w) => $w->whereNull('baujahr_bis')->orWhere('baujahr_bis', '>=', $s->baujahr));
        }

        return $q->with(['einsatz', 'adapter'])->first();
    }

    /** @return array<string, mixed> */
    private function pos(Accessory $a, string $kategorie, string $begruendung): array
    {
        return [
            'kategorie' => $kategorie,
            'accessory_id' => $a->id,
            'hersteller' => $a->hersteller,
            'herst_artikelnr' => $a->herst_artikelnr,
            'name' => $a->name,
            'begruendung' => $begruendung,
        ];
    }
}
