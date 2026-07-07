<?php

namespace App\Services\Energie\Contracts;

/**
 * Marker-Contract für PV-Module in der Auslegungs-Engine (Duck-Typing über Properties).
 *
 * @property float $voc_v Leerlaufspannung STC
 * @property float $vmpp_v MPP-Spannung STC
 * @property float $isc_a Kurzschlussstrom STC
 * @property float $impp_a MPP-Strom STC
 * @property int $pmpp_wp Nennleistung STC
 * @property float $tk_voc_pct_k Temperaturkoeffizient Voc (%/K, negativ)
 * @property float|null $tk_isc_pct_k TK Isc (%/K, positiv)
 * @property float|null $tk_pmpp_pct_k TK Pmpp (%/K, negativ)
 * @property float|null $tk_vmpp_pct_k TK Vmpp (%/K) – falls leer abgeleitet
 * @property int $u_sys_max_v Max. Systemspannung Modul
 * @property float|null $sicherung_max_a Max. Strangsicherung Modul
 */
interface SizingModule {}
