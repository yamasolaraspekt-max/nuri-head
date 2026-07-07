<?php

namespace App\Services\Heizlast;

/**
 * Warmwasser: Jahresenergie, Leistungszuschlag, Komfort-/Speicherempfehlung (DIN EN 12831-3).
 */
class WarmwasserService
{
    /** Effektiver Komfort: Badewanne erzwingt mindestens „normal". */
    public function effektiverKomfort(HeizlastEingabe $e): string
    {
        $komfort = $e->ww_komfort ?: 'normal';
        if ($e->badewanne_vorhanden && $komfort === 'sparsam') {
            $komfort = 'normal';
        }

        return $komfort;
    }

    public function empfohlenerKomfort(HeizlastEingabe $e): string
    {
        return $e->badewanne_vorhanden ? 'hoch' : $this->effektiverKomfort($e);
    }

    /** Jahres-WW-Energiebedarf des Gebäudes (kWh). */
    public function qWwKwh(HeizlastEingabe $e): float
    {
        return $e->personen_im_haushalt * HeizlastKonstanten::wwKwhPa($this->effektiverKomfort($e));
    }

    /** Leistungszuschlag WW zur Heizlast (kW); 0 wenn WW nicht über WP. */
    public function phiWwKw(HeizlastEingabe $e): float
    {
        return $e->ww_mit_wp ? $e->personen_im_haushalt * HeizlastKonstanten::WW_KW_PRO_PERSON : 0.0;
    }

    /** Speicherempfehlung (Liter). */
    public function speicherLiter(HeizlastEingabe $e): int
    {
        return $e->personen_im_haushalt * ($e->badewanne_vorhanden ? 90 : 50);
    }

    /**
     * @return array<string, mixed>
     */
    public function ergebnis(HeizlastEingabe $e): array
    {
        return [
            'komfort' => $this->effektiverKomfort($e),
            'empfohlener_komfort' => $this->empfohlenerKomfort($e),
            'ww_kwh_pa_pro_person' => HeizlastKonstanten::wwKwhPa($this->effektiverKomfort($e)),
            'q_ww_kwh' => round($this->qWwKwh($e)),
            'phi_ww_kw' => round($this->phiWwKw($e), 2),
            'speicher_liter' => $this->speicherLiter($e),
            'speicher_hinweis' => 'WP-tauglicher Speicher mit großer Wärmetauscherfläche empfehlen'
                .($e->badewanne_vorhanden ? ' (Badewanne → höhere Zapfspitze)' : ''),
        ];
    }
}
