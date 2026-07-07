<?php

namespace App\Services\Energie\Contracts;

/**
 * Marker-Contract für Wärmepumpen-Kennlinie in der Auslegungs-Engine.
 *
 * Wird vom wberechnung-WpKennlinieService per Property-Zugriff konsumiert
 * ($wp->leistungskurve, $wp->heizleistung_*, $wp->cop_*, $wp->aussen_heizen_min_c).
 * Property-Namen = Spaltennamen von product_heat_pump_specs (1:1), hersteller/modell
 * aus products via Join.
 *
 * @property int|null $id
 * @property array|null $leistungskurve 3-Ebenen-Kennlinie, JSON-Schema exakt:
 *                                      {"35":[[t_C,p_kW,cop],…],"45":[…],"55":[…]} — Ebenen W35/W45/W55, je Zeile [Außentemp, Leistung_kW, COP]
 * @property string|null $kurve_semantik Semantik der Leistungsdaten ('en14511_nenn' | 'volllast_max' | null)
 * @property float|null $heizleistung_am7_w35_kw
 * @property float|null $heizleistung_a2_w35_kw
 * @property float|null $heizleistung_a7_w35_kw
 * @property float|null $heizleistung_am7_w55_kw
 * @property float|null $cop_am7_w35
 * @property float|null $cop_a2_w35
 * @property float|null $cop_a7_w35
 * @property float|null $cop_am7_w55
 * @property float|null $scop_35
 * @property float|null $scop_55
 * @property float|null $max_vorlauf_c
 * @property float|null $aussen_heizen_min_c
 * @property float|null $modulation_min_kw
 * @property float|null $modulation_max_kw
 * @property string|null $geraetetyp
 * @property string|null $serie
 * @property string|null $kaeltemittel
 * @property string|null $hersteller aus products/brands via Join
 * @property string|null $modell aus products via Join
 */
interface WpKennlinie {}
