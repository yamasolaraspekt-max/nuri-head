<?php

namespace App\Services\Energie\Contracts;

/**
 * Marker-Contract für Batteriespeicher in der Auslegungs-Engine.
 *
 * @property int|null $u_min_v
 * @property int|null $u_max_v
 * @property float|null $p_lade_max_kw
 * @property float|null $i_max_a
 */
interface SizingBattery {}
