<?php

namespace App\Services\Energie\Contracts;

/**
 * Marker-Contract für Wechselrichter in der Auslegungs-Engine.
 *
 * @property int $u_dc_max_v
 * @property int|null $u_dc_betrieb_max_v
 * @property int $u_dc_start_v
 * @property int $u_mppt_min_v
 * @property int $u_mppt_max_v
 * @property int $anzahl_mppt
 * @property float $i_dc_max_mppt_a
 * @property float|null $i_dc_max_string_a
 * @property float|null $i_sc_max_mppt_a
 * @property int $p_ac_nenn_w
 * @property int $p_ac_max_va
 * @property int $p_dc_max_w
 * @property float|null $max_dc_ac_ratio
 * @property int|null $max_array_wp
 * @property int|string $phasen
 * @property bool $ist_hybrid
 * @property bool $na_schutz_integriert
 * @property bool $vde4105_konform
 * @property string|null $wirkleistungsbegrenzung
 * @property bool $steuerbar_14a
 * @property string|null $schnittstelle
 * @property int|null $temp_betrieb_min_c
 * @property int|null $temp_betrieb_max_c
 * @property int|null $temp_derating_ab_c
 * @property int|null $u_bat_min_v
 * @property int|null $u_bat_max_v
 * @property int|null $p_bat_lade_max_w
 * @property float|null $i_bat_max_a
 * @property bool $eps_faehig
 * @property float|null $wirkungsgrad_euro_pct
 */
interface SizingInverter {}
