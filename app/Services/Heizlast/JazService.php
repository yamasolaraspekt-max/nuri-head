<?php

namespace App\Services\Heizlast;

/**
 * JAZ/COP-Richtwerte, Auslegungs-Vorlauftemperatur, Stromverbrauch der WP.
 * JAZ = Richtwert (to_verify); für Förderung nach VDI 4650 mit Gerätedaten rechnen.
 */
class JazService
{
    public function vorlaufTemp(HeizlastEingabe $e): float
    {
        return $e->vorlauf_c ?? HeizlastKonstanten::vorlauf($e->heizsystem)['temp'];
    }

    public function klasse(HeizlastEingabe $e): string
    {
        return HeizlastKonstanten::vorlauf($e->heizsystem)['klasse'];
    }

    public function jaz(HeizlastEingabe $e): float
    {
        return HeizlastKonstanten::jaz($e->wp_typ, $this->klasse($e));
    }

    /** Stromverbrauch WP (kWh/a). WW nur, wenn über WP versorgt. */
    public function stromverbrauch(HeizlastEingabe $e, float $qHeizKwh, float $qWwKwh): float
    {
        $jaz = $this->jaz($e);
        $waerme = $qHeizKwh + ($e->ww_mit_wp ? $qWwKwh : 0.0);

        return $jaz > 0 ? $waerme / $jaz : 0.0;
    }
}
