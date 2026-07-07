<?php

namespace App\Services\Energie;

use App\Services\Energie\Contracts\SizingBattery;
use App\Services\Energie\Contracts\SizingInverter;
use App\Services\Energie\Contracts\SizingModule;
use Illuminate\Support\Str;

/**
 * Detaillierte Wechselrichter-Auslegungs-Engine (reine, testbare Service-Klasse).
 *
 * Portiert/abgeglichen mit dem Referenz-Engineering (SOLAR MASTER Playground):
 * Spannungsfenster (n_min/n_max + MPP-Tracking), Strangstrom in drei Dimensionen,
 * zweistufige Strangsicherung, DC-Überdimensionierung, Ebenen-Toleranz je MPPT,
 * vollständige Netzregeln (VDE-AR-N 4105/4110, NA-Schutz, §14a), WR-Betriebstemperatur,
 * Batterie-Kompatibilität (Hybrid) und WR-Vorschlag-Ranking. Jede Regel liefert eine
 * Ampel (gruen/gelb/rot) mit Wert, Grenzwert und NORMBEZUG fürs Protokoll.
 *
 * HAFTUNG: Vorauslegung auf Basis hinterlegter Datenblattwerte. Endauslegung gegen aktuelle
 * Normfassung + Original-Datenblätter prüfen; Freigabe durch qualifizierte Elektrofachkraft.
 */
class InverterSizingService
{
    public const STC_TEMP_C = 25.0;

    public const DEFAULT_TMIN_C = -10.0;

    public const DEFAULT_TMAX_CELL_C = 70.0;

    public const SPANNUNG_SICHERHEIT_PCT = 2.0;

    public const STROM_BEMESSUNG_FAKTOR = 1.25;

    public const SICHERUNG_FAKTOR = 1.25;

    public const PARALLEL_STRING_SICHERUNG_AB = 3;

    public const UEBERDIM_GELB_AB = 1.2;

    public const UEBERDIM_GRUEN_AB = 0.9;

    public const UEBERDIM_IDEAL = 1.1;

    public const ISC_ALPHA_FALLBACK_PCT_K = 0.05;

    public const SCHIEFLAST_GRENZE_VA = 4600;

    public const SYMMETRIE_HINWEIS_VA = 2300;

    public const MS_GRENZE_VA = 135000;

    public const NA_SCHUTZ_ZENTRAL_VA = 30000;

    public const WR_TAMB_MIN_DEFAULT_C = -15.0;

    public const WR_TAMB_MAX_DEFAULT_C = 40.0;

    public const EBENE_AZIMUT_GRUEN_GRAD = 10.0;

    public const EBENE_AZIMUT_GELB_GRAD = 25.0;

    public const EBENE_NEIGUNG_GRUEN_GRAD = 10.0;

    public const EBENE_NEIGUNG_GELB_GRAD = 15.0;

    public const NORM_VERSION_NETZ = 'VDE-AR-N 4105:2018-11';

    /** Ausrichtungs-Label → Azimut (Süd = 0°, Konvention PVGIS). */
    private const AZIMUT = [
        'süd' => 0, 'sued' => 0, 's' => 0,
        'südwest' => 45, 'suedwest' => 45, 'sw' => 45,
        'südost' => -45, 'suedost' => -45, 'so' => -45,
        'west' => 90, 'w' => 90, 'ost' => -90, 'o' => -90,
        'nordwest' => 135, 'nw' => 135, 'nordost' => -135, 'no' => -135,
        'nord' => 180, 'n' => 180,
    ];

    // ════════════════════════ Temperaturkorrektur ════════════════════════

    public function vocAt(SizingModule $m, float $t): float
    {
        return $m->voc_v * (1 + $m->tk_voc_pct_k / 100 * ($t - self::STC_TEMP_C));
    }

    public function vmppAt(SizingModule $m, float $t): float
    {
        return $m->vmpp_v * (1 + $this->effektiverTkVmpp($m) / 100 * ($t - self::STC_TEMP_C));
    }

    public function iscAt(SizingModule $m, float $t): float
    {
        return $m->isc_a * (1 + ($m->tk_isc_pct_k ?? self::ISC_ALPHA_FALLBACK_PCT_K) / 100 * ($t - self::STC_TEMP_C));
    }

    /** Effektiver TK Vmpp: Datenblatt bevorzugt, sonst konservativ β_Vmpp ≈ γ_Pmpp − α_Isc. */
    public function effektiverTkVmpp(SizingModule $m): float
    {
        if ($m->tk_vmpp_pct_k !== null) {
            return (float) $m->tk_vmpp_pct_k;
        }
        $alpha = $m->tk_isc_pct_k !== null ? (float) $m->tk_isc_pct_k : self::ISC_ALPHA_FALLBACK_PCT_K;

        return (float) $m->tk_pmpp_pct_k - $alpha;
    }

    // ════════════════════════ Spannungsfenster (Regeln 1–4) ════════════════════════

    public function spannungsfenster(SizingModule $m, SizingInverter $wr, array $opt = []): array
    {
        $tmin = $opt['tmin_c'] ?? self::DEFAULT_TMIN_C;
        $tmax = $opt['tmax_cell_c'] ?? self::DEFAULT_TMAX_CELL_C;
        $margin = ($opt['sicherheitsabstand'] ?? false) ? self::SPANNUNG_SICHERHEIT_PCT : 0.0;

        $vocTmin = $this->vocAt($m, $tmin);
        $vmppTmax = $this->vmppAt($m, $tmax);
        $vmppTmin = $this->vmppAt($m, $tmin);

        $grenzen = array_filter([
            (int) $wr->u_dc_max_v,
            $wr->u_dc_betrieb_max_v !== null ? (int) $wr->u_dc_betrieb_max_v : null,
            (int) $m->u_sys_max_v,
        ], fn ($v) => $v !== null && $v > 0);
        $limit = $grenzen ? (int) min($grenzen) : (int) $wr->u_dc_max_v;
        $effLimit = $limit * (1 - $margin / 100);
        $nMaxSpannung = (int) floor($effLimit / $vocTmin);

        $nMinMppt = (int) ceil($wr->u_mppt_min_v / $vmppTmax);
        $nMinStart = (int) floor($wr->u_dc_start_v / $vmppTmax) + 1;
        $nMin = max($nMinMppt, $nMinStart);
        $nMaxMppt = (int) floor($wr->u_mppt_max_v / $vmppTmin);

        return [
            'tmin_c' => $tmin,
            'tmax_cell_c' => $tmax,
            'voc_tmin_v' => round($vocTmin, 2),
            'vmpp_tmax_v' => round($vmppTmax, 2),
            'vmpp_tmin_v' => round($vmppTmin, 2),
            'tk_vmpp_effektiv_pct_k' => round($this->effektiverTkVmpp($m), 4),
            'spannungslimit_v' => $limit,
            'n_min' => $nMin,
            'n_max' => $nMaxSpannung,
            'n_max_mppt_tracking' => $nMaxMppt,
            'gueltig' => $nMin <= $nMaxSpannung,
            'empfohlen_min' => $nMin,
            'empfohlen_max' => min($nMaxSpannung, $nMaxMppt > 0 ? $nMaxMppt : $nMaxSpannung),
        ];
    }

    // ════════════════════════ String-Prüfung (Regeln 1,2,3,5,6) ════════════════════════

    public function pruefeString(SizingModule $m, SizingInverter $wr, int $nModule, int $parallelStrings = 1, array $opt = []): array
    {
        $f = $this->spannungsfenster($m, $wr, $opt);
        $regeln = [];

        $uString = $nModule * $f['voc_tmin_v'];
        $limit = $f['spannungslimit_v'];
        $status1 = $uString > $limit ? 'rot' : ($nModule > $f['n_max'] ? 'gelb' : 'gruen');
        $regeln[] = $this->regel(1, 'Max. Strangspannung (kalt)', 'DIN EN IEC 62548 / VDE 0100-712',
            $status1, "n×Voc({$f['tmin_c']} °C) = ".round($uString, 1)." V ≤ {$limit} V", round($uString, 1), $limit);

        $uMpptHeiss = $nModule * $f['vmpp_tmax_v'];
        $status2 = ($uMpptHeiss < $wr->u_mppt_min_v || $uMpptHeiss <= $wr->u_dc_start_v) ? 'rot' : 'gruen';
        $regeln[] = $this->regel(2, 'Unteres MPP-Fenster (heiß)', 'WR-Datenblatt (DIN EN 50524)',
            $status2, "n×Vmpp({$f['tmax_cell_c']} °C) = ".round($uMpptHeiss, 1)." V ≥ {$wr->u_mppt_min_v} V (MPP) und > {$wr->u_dc_start_v} V (Start)", round($uMpptHeiss, 1), (int) $wr->u_mppt_min_v);

        $uMpptKalt = $nModule * $f['vmpp_tmin_v'];
        $status3 = $uMpptKalt > $wr->u_mppt_max_v ? 'gelb' : 'gruen';
        $regeln[] = $this->regel(3, 'Oberes MPP-Fenster (kalt)', 'WR-Datenblatt',
            $status3, "n×Vmpp({$f['tmin_c']} °C) = ".round($uMpptKalt, 1)." V ≤ {$wr->u_mppt_max_v} V", round($uMpptKalt, 1), (int) $wr->u_mppt_max_v);

        foreach ($this->stromregeln($m, $wr, $parallelStrings) as $r) {
            $regeln[] = $r;
        }
        $regeln[] = $this->strangsicherung($m, $parallelStrings);

        return [
            'n_module' => $nModule,
            'parallel_strings' => $parallelStrings,
            'u_string_kalt_v' => round($uString, 1),
            'u_mppt_heiss_v' => round($uMpptHeiss, 1),
            'u_mppt_kalt_v' => round($uMpptKalt, 1),
            'im_fenster' => $nModule >= $f['n_min'] && $nModule <= $f['n_max'],
            'regeln' => $regeln,
            'ampel' => $this->gesamtAmpel($regeln),
        ];
    }

    /** Regel 5: Betrieb (Impp), Kurzschluss (Isc) und Bemessung (1,25×Isc) — nicht vermischen. */
    public function stromregeln(SizingModule $m, SizingInverter $wr, int $parallelStrings): array
    {
        $regeln = [];

        if ($wr->i_dc_max_string_a !== null) {
            $s = $m->impp_a <= $wr->i_dc_max_string_a ? 'gruen' : 'rot';
            $regeln[] = $this->regel(5, 'Betriebsstrom je String (Impp)', 'DIN EN IEC 62548',
                $s, "Impp = {$m->impp_a} A ≤ {$wr->i_dc_max_string_a} A", (float) $m->impp_a, (float) $wr->i_dc_max_string_a);
        }

        $sumImpp = $parallelStrings * $m->impp_a;
        $sMppt = $sumImpp <= $wr->i_dc_max_mppt_a ? 'gruen' : 'rot';
        $regeln[] = $this->regel(5, 'Betriebsstrom je MPPT (Σ Impp)', 'DIN EN IEC 62548',
            $sMppt, 'Σ Impp = '.round($sumImpp, 2)." A ≤ {$wr->i_dc_max_mppt_a} A", round($sumImpp, 2), (float) $wr->i_dc_max_mppt_a);

        if ($wr->i_sc_max_mppt_a !== null) {
            $sumIsc = $parallelStrings * $m->isc_a;
            $sSc = $sumIsc <= $wr->i_sc_max_mppt_a ? 'gruen' : 'rot';
            $regeln[] = $this->regel(5, 'Kurzschlussstrom je MPPT (Σ Isc)', 'DIN EN IEC 62548',
                $sSc, 'Σ Isc = '.round($sumIsc, 2)." A ≤ {$wr->i_sc_max_mppt_a} A", round($sumIsc, 2), (float) $wr->i_sc_max_mppt_a);
        }

        $iBemessung = self::STROM_BEMESSUNG_FAKTOR * $m->isc_a;
        $regeln[] = $this->regel(5, 'Bemessungsstrom Leitung (1,25 × Isc)', 'DIN EN IEC 62548',
            'gruen', 'I_Bemessung = '.round($iBemessung, 2).' A (Leitungs-/Sicherungsdimensionierung)', round($iBemessung, 2), null);

        return $regeln;
    }

    /** Regel 6: Strangsicherung ab ≥ 3 parallelen Strings (zweistufige 1,25er-Bemessung). */
    public function strangsicherung(SizingModule $m, int $parallelStrings): array
    {
        if ($parallelStrings < self::PARALLEL_STRING_SICHERUNG_AB) {
            return $this->regel(6, 'Strangsicherung', 'DIN EN IEC 62548',
                'gruen', "Bei {$parallelStrings} parallelen Strings keine Strangsicherung erforderlich (< ".self::PARALLEL_STRING_SICHERUNG_AB.').', $parallelStrings, null);
        }

        $inMin = self::SICHERUNG_FAKTOR * self::STROM_BEMESSUNG_FAKTOR * $m->isc_a;
        if ($m->sicherung_max_a === null) {
            return $this->regel(6, 'Strangsicherung', 'DIN EN IEC 62548',
                'gelb', 'Sicherung erforderlich ('.round($inMin, 2).' A), Modul-Maximalsicherung im Datenblatt prüfen.', round($inMin, 2), null);
        }
        $status = $inMin <= $m->sicherung_max_a ? 'gruen' : 'rot';

        return $this->regel(6, 'Strangsicherung', 'DIN EN IEC 62548',
            $status, 'In ≥ '.round($inMin, 2)." A ≤ Modul-Max {$m->sicherung_max_a} A", round($inMin, 2), (float) $m->sicherung_max_a);
    }

    // ════════════════════════ Überdimensionierung (Regel 7) ════════════════════════

    public function ueberdimensionierung(SizingModule $m, SizingInverter $wr, int $moduleGesamt): array
    {
        return $this->ueberdimensionierungAusLeistung($moduleGesamt * $m->pmpp_wp, $wr);
    }

    /** Wie ueberdimensionierung(), aber aus der Gesamt-DC-Leistung (für gemischte Module je Dach). */
    public function ueberdimensionierungAusLeistung(float $pDc, SizingInverter $wr): array
    {
        $ratio = $wr->p_ac_nenn_w > 0 ? $pDc / $wr->p_ac_nenn_w : 0.0;

        $maxRatio = $wr->max_dc_ac_ratio;
        if ($maxRatio === null) {
            $maxRatio = ($wr->max_array_wp && $wr->p_ac_nenn_w > 0)
                ? $wr->max_array_wp / $wr->p_ac_nenn_w
                : ($wr->p_ac_nenn_w > 0 ? $wr->p_dc_max_w / $wr->p_ac_nenn_w : 1.5);
        }

        if ($pDc > $wr->p_dc_max_w || $ratio > $maxRatio) {
            $status = 'rot';
            $text = 'P_DC = '.round($pDc)." W > Gerätegrenze {$wr->p_dc_max_w} W bzw. Ratio ".round($ratio, 2).' > '.round($maxRatio, 2);
        } elseif ($ratio > self::UEBERDIM_GELB_AB) {
            $status = 'gelb';
            $text = 'Ratio '.round($ratio, 2).' > '.self::UEBERDIM_GELB_AB.' (erhöhte Überdimensionierung — bei Ost/West/flach vertretbar, Clipping prüfen)';
        } elseif ($ratio < self::UEBERDIM_GRUEN_AB) {
            $status = 'gelb';
            $text = 'Ratio '.round($ratio, 2).' < '.self::UEBERDIM_GRUEN_AB.' (WR unterdimensioniert genutzt)';
        } else {
            $status = 'gruen';
            $text = 'Ratio '.round($ratio, 2).' im Empfehlungsbereich 0,9–1,2';
        }

        $r = $this->regel(7, 'DC-Überdimensionierung', 'WR-Datenblatt', $status, $text, round($ratio, 3), round((float) $maxRatio, 3));
        $r['p_dc_w'] = (int) $pDc;
        $r['ratio'] = round($ratio, 3);

        return $r;
    }

    // ════════════════════════ Regel 8: Ausrichtung/Neigung je MPPT ════════════════════════

    public function azimutDifferenz(float $a, float $b): float
    {
        $d = abs($a - $b);

        return $d > 180 ? 360 - $d : $d;
    }

    public function ebeneKompatibel(float $az1, float $ne1, float $az2, float $ne2): string
    {
        $dAz = $this->azimutDifferenz($az1, $az2);
        $dNe = abs($ne1 - $ne2);
        if ($dAz > self::EBENE_AZIMUT_GELB_GRAD || $dNe > self::EBENE_NEIGUNG_GELB_GRAD) {
            return 'rot';
        }
        if ($dAz > self::EBENE_AZIMUT_GRUEN_GRAD || $dNe > self::EBENE_NEIGUNG_GRUEN_GRAD) {
            return 'gelb';
        }

        return 'gruen';
    }

    public function ausrichtungMppt(array $strings): array
    {
        if (count($strings) < 2) {
            return $this->regel(8, 'Ausrichtung/Neigung je MPPT', 'Auslegungsregel (Mismatch)',
                'gruen', 'Ein String je MPPT — keine Ebenenmischung.', count($strings), null);
        }
        $ref = $strings[0];
        $status = 'gruen';
        foreach (array_slice($strings, 1) as $s) {
            $a = $this->ebeneKompatibel((float) ($ref['azimut'] ?? 0), (float) ($ref['neigung'] ?? 0), (float) ($s['azimut'] ?? 0), (float) ($s['neigung'] ?? 0));
            if ($a === 'rot') {
                $status = 'rot';
                break;
            }
            if ($a === 'gelb') {
                $status = 'gelb';
            }
        }
        $text = $status === 'gruen' ? 'Alle Strings dieses MPPT in gemeinsamer Ebene (Toleranz eingehalten).'
            : ($status === 'gelb' ? 'Leichte Ebenenmischung am MPPT — Mismatch-Hinweis.' : 'Unterschiedliche Ausrichtungen/Neigungen an einem MPPT — Ertragsverlust (ein MPPT = eine Ebene).');

        return $this->regel(8, 'Ausrichtung/Neigung je MPPT', 'Auslegungsregel (Mismatch)', $status, $text, count($strings), null);
    }

    // ════════════════════════ Regel 9: AC-/Netzanschluss (VDE-AR-N 4105/4110) ════════════════════════

    public function netzregeln(SizingInverter $wr, array $opt = []): array
    {
        $neuanlage = $opt['neuanlage'] ?? true;
        $summe = (int) ($opt['summe_p_ac_max_va'] ?? $wr->p_ac_max_va);
        $regeln = [];

        if ((int) $wr->phasen === 1) {
            if ($wr->p_ac_max_va > self::SCHIEFLAST_GRENZE_VA) {
                $regeln[] = $this->regel(9, 'Schieflast (1-phasig)', self::NORM_VERSION_NETZ, 'rot',
                    "1-phasig {$wr->p_ac_max_va} VA > ".self::SCHIEFLAST_GRENZE_VA.' VA → 3-phasig zwingend', (int) $wr->p_ac_max_va, self::SCHIEFLAST_GRENZE_VA);
            } elseif ($wr->p_ac_max_va > self::SYMMETRIE_HINWEIS_VA) {
                $regeln[] = $this->regel(9, 'Schieflast (1-phasig)', self::NORM_VERSION_NETZ, 'gelb',
                    "1-phasig {$wr->p_ac_max_va} VA nahe Schieflastgrenze — 3-phasig empfohlen", (int) $wr->p_ac_max_va, self::SCHIEFLAST_GRENZE_VA);
            } else {
                $regeln[] = $this->regel(9, 'Schieflast (1-phasig)', self::NORM_VERSION_NETZ, 'gruen',
                    "1-phasig {$wr->p_ac_max_va} VA ≤ ".self::SCHIEFLAST_GRENZE_VA.' VA', (int) $wr->p_ac_max_va, self::SCHIEFLAST_GRENZE_VA);
            }
        } else {
            $regeln[] = $this->regel(9, 'Einspeisung 3-phasig', self::NORM_VERSION_NETZ, 'gruen',
                '3-phasig — symmetrische Einspeisung bevorzugt.', 3, null);
        }

        if (! $wr->na_schutz_integriert && ! ($opt['externer_na_schutz'] ?? false)) {
            $regeln[] = $this->regel(9, 'NA-Schutz', self::NORM_VERSION_NETZ, 'rot', 'Kein integrierter NA-Schutz und kein externer nachgewiesen.', null, null);
        } elseif ($summe > self::NA_SCHUTZ_ZENTRAL_VA) {
            $regeln[] = $this->regel(9, 'NA-Schutz', self::NORM_VERSION_NETZ, 'gelb', 'Σ '.$summe.' VA > '.self::NA_SCHUTZ_ZENTRAL_VA.' VA → zentraler NA-Schutz erforderlich.', $summe, self::NA_SCHUTZ_ZENTRAL_VA);
        } else {
            $regeln[] = $this->regel(9, 'NA-Schutz', self::NORM_VERSION_NETZ, 'gruen', 'Integrierter NA-Schutz ausreichend.', null, null);
        }

        $regeln[] = $this->regel(9, 'VDE-AR-N 4105-Konformität (Blindleistung)', self::NORM_VERSION_NETZ,
            $wr->vde4105_konform ? 'gruen' : 'rot',
            $wr->vde4105_konform ? 'Herstellerkonformität 4105 bestätigt (inkl. Blindleistung/cos φ).' : 'Keine 4105-Konformität hinterlegt.', null, null);

        $wlb = $wr->wirkleistungsbegrenzung;
        if ($wlb === 'keine' && ! $wr->steuerbar_14a) {
            $regeln[] = $this->regel(9, 'Einspeisemanagement / §14a', '§14a EnWG / Solarspitzengesetz', 'rot', 'Kein Einspeisemanagement (keine Begrenzung, nicht §14a-steuerbar).', null, null);
        } elseif (in_array($wlb, ['60%', '70%'], true) && $neuanlage) {
            $regeln[] = $this->regel(9, 'Einspeisemanagement / §14a', '§14a EnWG / Solarspitzengesetz', 'gelb', "Alt-Begrenzung {$wlb} bei Neuanlage — §14a-Steuerbarkeit prüfen.", null, null);
        } else {
            $regeln[] = $this->regel(9, 'Einspeisemanagement / §14a', '§14a EnWG / Solarspitzengesetz', 'gruen',
                $wr->steuerbar_14a ? ('§14a-steuerbar ('.($wr->schnittstelle ?: 'Schnittstelle?').').') : ('Begrenzung '.$wlb.'.'), null, null);
        }

        if ($summe > self::MS_GRENZE_VA) {
            $regeln[] = $this->regel(9, 'Netzebene (4110-Prüfung)', 'VDE-AR-N 4110', 'gelb', 'Σ '.$summe.' VA > '.self::MS_GRENZE_VA.' VA → Mittelspannungsanschluss (4110) prüfen.', $summe, self::MS_GRENZE_VA);
        }

        return $regeln;
    }

    // ════════════════════════ Regel 10: WR-Betriebstemperatur ════════════════════════

    public function temperaturBereich(SizingInverter $wr, array $opt = []): array
    {
        $tmin = $opt['tamb_min_c'] ?? self::WR_TAMB_MIN_DEFAULT_C;
        $tmax = $opt['tamb_max_c'] ?? self::WR_TAMB_MAX_DEFAULT_C;

        if ($wr->temp_betrieb_min_c === null || $wr->temp_betrieb_max_c === null) {
            return $this->regel(10, 'WR-Betriebstemperatur', 'WR-Datenblatt', 'gelb', 'Betriebstemperaturbereich im Datenblatt nicht hinterlegt.', null, null);
        }
        if ($tmax > $wr->temp_betrieb_max_c || $tmin < $wr->temp_betrieb_min_c) {
            return $this->regel(10, 'WR-Betriebstemperatur', 'WR-Datenblatt', 'rot',
                "Standort {$tmin}…{$tmax} °C außerhalb {$wr->temp_betrieb_min_c}…{$wr->temp_betrieb_max_c} °C", $tmax, (int) $wr->temp_betrieb_max_c);
        }
        if ($wr->temp_derating_ab_c !== null && $tmax > $wr->temp_derating_ab_c) {
            return $this->regel(10, 'WR-Betriebstemperatur', 'WR-Datenblatt', 'gelb',
                "Standort-Tmax {$tmax} °C > Derating-Schwelle {$wr->temp_derating_ab_c} °C → Leistungsabminderung bei Hitze", $tmax, (int) $wr->temp_derating_ab_c);
        }

        return $this->regel(10, 'WR-Betriebstemperatur', 'WR-Datenblatt', 'gruen', "Standort {$tmin}…{$tmax} °C im Betriebsbereich, kein Derating.", $tmax, (int) $wr->temp_betrieb_max_c);
    }

    // ════════════════════════ Regel 11: Batterie-Kompatibilität (Hybrid) ════════════════════════

    public function batterieKompatibilitaet(SizingInverter $wr, ?SizingBattery $bat, array $opt = []): array
    {
        if ($bat === null) {
            return $this->regel(11, 'Batterie-Kompatibilität', 'WR-/Batterie-Datenblatt', 'gruen', 'Keine Batterie gewählt.', null, null);
        }
        if (! $wr->ist_hybrid) {
            return $this->regel(11, 'Batterie-Kompatibilität', 'WR-/Batterie-Datenblatt', 'rot', 'WR ist kein Hybrid — keine DC-Batterieführung.', null, null);
        }
        if ($wr->u_bat_min_v !== null && $wr->u_bat_max_v !== null && $bat->u_min_v !== null && $bat->u_max_v !== null
            && ($bat->u_min_v < $wr->u_bat_min_v || $bat->u_max_v > $wr->u_bat_max_v)) {
            return $this->regel(11, 'Batterie-Spannungsfenster', 'WR-/Batterie-Datenblatt', 'rot',
                "Batterie {$bat->u_min_v}…{$bat->u_max_v} V nicht in WR {$wr->u_bat_min_v}…{$wr->u_bat_max_v} V", null, null);
        }
        if ($wr->p_bat_lade_max_w !== null && $bat->p_lade_max_kw !== null && $bat->p_lade_max_kw * 1000 > $wr->p_bat_lade_max_w) {
            return $this->regel(11, 'Batterie-Ladeleistung', 'WR-/Batterie-Datenblatt', 'rot', 'Batterie-Ladeleistung > WR-Grenze.', null, null);
        }
        if ($wr->i_bat_max_a !== null && $bat->i_max_a !== null && $bat->i_max_a > $wr->i_bat_max_a) {
            return $this->regel(11, 'Batterie-Strom', 'WR-/Batterie-Datenblatt', 'rot', 'Batteriestrom > WR-Grenze.', null, null);
        }
        if (($opt['eps_gefordert'] ?? false) && ! $wr->eps_faehig) {
            return $this->regel(11, 'Notstrom (EPS)', 'WR-Datenblatt', 'rot', 'EPS gefordert, WR nicht ersatzstromfähig.', null, null);
        }
        if (($opt['gelistet'] ?? null) === false) {
            return $this->regel(11, 'Batterie-Kompatibilität', 'Herstellerfreigabe', 'rot', 'WR↔Batterie nicht in Kompatibilitätsliste.', null, null);
        }

        return $this->regel(11, 'Batterie-Kompatibilität', 'WR-/Batterie-Datenblatt', 'gruen', 'Batterie elektrisch + gelistet kompatibel.', null, null);
    }

    // ════════════════════════ Gesamtbewertung + WR-Vorschlag ════════════════════════

    public function bewerteWechselrichter(SizingModule $m, SizingInverter $wr, int $moduleGesamt, array $opt = []): array
    {
        $f = $this->spannungsfenster($m, $wr, $opt);
        $regeln = [];

        if (! $f['gueltig']) {
            $regeln[] = $this->regel(4, 'Gültiges Spannungsfenster', 'Auslegungsregel', 'rot', 'n_min > n_max — kein gültiger String.', $f['n_min'], $f['n_max']);
        } else {
            $k = max(1, (int) ceil($moduleGesamt / max(1, $f['n_max'])));
            $n = max($f['n_min'], min($f['n_max'], intdiv($moduleGesamt, $k) ?: $f['n_min']));
            $regeln = array_merge($regeln, $this->pruefeString($m, $wr, $n, 1, $opt)['regeln']);
        }

        $regeln[] = $this->ueberdimensionierung($m, $wr, $moduleGesamt);
        foreach ($this->netzregeln($wr, $opt) as $r) {
            $regeln[] = $r;
        }
        $regeln[] = $this->temperaturBereich($wr, $opt);
        if (array_key_exists('batterie', $opt) || ($opt['eps_gefordert'] ?? false)) {
            $regeln[] = $this->batterieKompatibilitaet($wr, $opt['batterie'] ?? null, $opt);
        }

        $ratio = $wr->p_ac_nenn_w > 0 ? ($moduleGesamt * $m->pmpp_wp) / $wr->p_ac_nenn_w : 0.0;

        return [
            'inverter_id' => $wr->id ?? null,
            'hersteller' => $wr->hersteller ?? null,
            'modell' => $wr->modell ?? null,
            'ampel' => $this->gesamtAmpel($regeln),
            'ratio' => round($ratio, 3),
            'wirkungsgrad_euro_pct' => $wr->wirkungsgrad_euro_pct,
            'gueltig' => $f['gueltig'],
            'regeln' => $regeln,
        ];
    }

    /**
     * @param  iterable<SizingInverter>  $kandidaten
     */
    public function vorschlagWechselrichter(SizingModule $m, int $moduleGesamt, iterable $kandidaten, array $opt = []): array
    {
        $bewertet = [];
        foreach ($kandidaten as $wr) {
            $b = $this->bewerteWechselrichter($m, $wr, $moduleGesamt, $opt);
            if ($b['ampel'] === 'rot') {
                continue;
            }
            $bewertet[] = $b;
        }

        usort($bewertet, function ($a, $b) {
            $da = abs($a['ratio'] - self::UEBERDIM_IDEAL);
            $db = abs($b['ratio'] - self::UEBERDIM_IDEAL);
            if (abs($da - $db) > 0.001) {
                return $da <=> $db;
            }

            return ($b['wirkungsgrad_euro_pct'] ?? 0) <=> ($a['wirkungsgrad_euro_pct'] ?? 0);
        });

        return $bewertet;
    }

    // ════════════════════════ Adapter: Konfiguration → UI-/Protokoll-Format ════════════════════════

    /**
     * Bewertet eine konkrete MPPT-/String-Konfiguration und liefert das Ergebnis im
     * bestehenden UI-/Protokoll-Format (rules mit id/titel/norm/status/wert_text/grenze_text).
     *
     * @param  array{mppts: array<int, array{strings: array<int, array{module_count?:int, ausrichtung?:string, neigung?:float}>}>}  $config
     * @param  array<string, mixed>  $opt
     * @return array<string, mixed>
     */
    public function evaluateKonfiguration(SizingModule $m, SizingInverter $wr, array $config, array $opt = [], ?SizingBattery $bat = null): array
    {
        if ($bat !== null) {
            $opt['batterie'] = $bat;
        }
        $f = $this->spannungsfenster($m, $wr, $opt);

        $mppts = $config['mppts'] ?? [];
        $counts = [];
        $maxParallel = 0;
        foreach ($mppts as $mp) {
            $strings = array_values(array_filter($mp['strings'] ?? [], fn ($s) => (int) ($s['module_count'] ?? 0) > 0));
            $maxParallel = max($maxParallel, count($strings));
            foreach ($strings as $s) {
                $counts[] = (int) $s['module_count'];
            }
        }
        $total = array_sum($counts);
        $nMaxN = $counts ? max($counts) : 0;
        $nMinN = $counts ? min($counts) : 0;

        $regeln = [];
        if (! $f['gueltig']) {
            $regeln[] = $this->regel(4, 'Gültiges Spannungsfenster', 'Auslegungsregel', 'rot', 'n_min > n_max — kein gültiger String für dieses Modul/WR-Paar.', $f['n_min'], $f['n_max']);
        }

        if ($nMaxN > 0) {
            // Regel 1 & 3 am spannungs-kritischsten String (max n), Regel 2 am heiß-kritischsten (min n)
            $uHigh = $nMaxN * $f['voc_tmin_v'];
            $regeln[] = $this->regel(1, 'Max. Strangspannung (kalt)', 'DIN EN IEC 62548 / VDE 0100-712',
                $uHigh > $f['spannungslimit_v'] ? 'rot' : ($nMaxN > $f['n_max'] ? 'gelb' : 'gruen'),
                "n×Voc({$f['tmin_c']} °C) = ".round($uHigh, 1)." V ≤ {$f['spannungslimit_v']} V", round($uHigh, 1), $f['spannungslimit_v']);

            $uLow = $nMinN * $f['vmpp_tmax_v'];
            $regeln[] = $this->regel(2, 'Unteres MPP-Fenster (heiß)', 'WR-Datenblatt (DIN EN 50524)',
                ($uLow < $wr->u_mppt_min_v || $uLow <= $wr->u_dc_start_v) ? 'rot' : 'gruen',
                "n×Vmpp({$f['tmax_cell_c']} °C) = ".round($uLow, 1)." V ≥ {$wr->u_mppt_min_v} V (MPP) und > {$wr->u_dc_start_v} V (Start)", round($uLow, 1), (int) $wr->u_mppt_min_v);

            $uKalt = $nMaxN * $f['vmpp_tmin_v'];
            $regeln[] = $this->regel(3, 'Oberes MPP-Fenster (kalt)', 'WR-Datenblatt',
                $uKalt > $wr->u_mppt_max_v ? 'gelb' : 'gruen',
                "n×Vmpp({$f['tmin_c']} °C) = ".round($uKalt, 1)." V ≤ {$wr->u_mppt_max_v} V", round($uKalt, 1), (int) $wr->u_mppt_max_v);

            foreach ($this->stromregeln($m, $wr, max(1, $maxParallel)) as $r) {
                $regeln[] = $r;
            }
            $regeln[] = $this->strangsicherung($m, max(1, $maxParallel));
        }

        $regeln[] = $this->ueberdimensionierung($m, $wr, $total);

        // Regel 8: je MPPT die Ebenen (Label → Azimut) prüfen, schärfste Ampel übernehmen
        $r8 = null;
        foreach ($mppts as $mp) {
            $strings = array_map(fn ($s) => [
                'azimut' => $this->azimutVonLabel($s['ausrichtung'] ?? ''),
                'neigung' => (float) ($s['neigung'] ?? 0),
            ], array_filter($mp['strings'] ?? [], fn ($s) => (int) ($s['module_count'] ?? 0) > 0));
            if (! $strings) {
                continue;
            }
            $cand = $this->ausrichtungMppt(array_values($strings));
            if ($r8 === null || $this->ampelRang($cand['status']) > $this->ampelRang($r8['status'])) {
                $r8 = $cand;
            }
        }
        if ($r8 !== null) {
            $regeln[] = $r8;
        }

        foreach ($this->netzregeln($wr, $opt) as $r) {
            $regeln[] = $r;
        }
        $regeln[] = $this->temperaturBereich($wr, $opt);
        if ($bat !== null) {
            $regeln[] = $this->batterieKompatibilitaet($wr, $bat, $opt);
        }

        $pDc = $total * $m->pmpp_wp;

        return [
            'gesamt_status' => $this->gesamtAmpel($regeln),
            'n_min' => $f['n_min'],
            'n_max' => $f['n_max'],
            'n_max_mppt_fenster' => $f['n_max_mppt_tracking'],
            'voc_tmin' => $f['voc_tmin_v'],
            'vmpp_tmax' => $f['vmpp_tmax_v'],
            'vmpp_tmin' => $f['vmpp_tmin_v'],
            'p_dc_gesamt_w' => $pDc,
            'dc_ac_ratio' => $wr->p_ac_nenn_w > 0 ? round($pDc / $wr->p_ac_nenn_w, 3) : null,
            't_min_c' => $f['tmin_c'],
            't_zelle_max_c' => $f['tmax_cell_c'],
            'rules' => array_map(fn ($r) => $this->zuUiRegel($r), $regeln),
        ];
    }

    // ════════════════════════ Hilfen ════════════════════════

    private function regel(int $nr, string $name, string $norm, string $status, string $text, $wert = null, $grenze = null): array
    {
        return compact('nr', 'name', 'norm', 'status', 'text', 'wert', 'grenze');
    }

    /** Interne Regel → UI-/Protokoll-Format (id/titel/norm/status/wert_text/grenze_text). */
    private function zuUiRegel(array $r): array
    {
        return [
            'id' => 'r'.$r['nr'].'_'.Str::slug($r['name']),
            'titel' => $r['name'],
            'norm' => $r['norm'],
            'status' => $r['status'],
            'wert_text' => $r['text'],
            'grenze_text' => $r['grenze'] !== null ? 'Grenze: '.$r['grenze'] : '',
        ];
    }

    public function azimutVonLabel(string $label): float
    {
        $key = strtolower(trim($label));

        return (float) (self::AZIMUT[$key] ?? 0);
    }

    /**
     * Interne Regeln (nr/name/norm/status/text) ins UI-/Protokoll-Format mappen.
     *
     * @param  array<int, array<string, mixed>>  $regeln
     * @return array<int, array<string, mixed>>
     */
    public function formatRegeln(array $regeln): array
    {
        return array_map(fn ($r) => $this->zuUiRegel($r), $regeln);
    }

    public function ampelRang(string $status): int
    {
        return ['gruen' => 0, 'gelb' => 1, 'rot' => 2][$status] ?? 0;
    }

    public function gesamtAmpel(array $regeln): string
    {
        $stati = array_column($regeln, 'status');
        if (in_array('rot', $stati, true)) {
            return 'rot';
        }
        if (in_array('gelb', $stati, true)) {
            return 'gelb';
        }

        return 'gruen';
    }
}
