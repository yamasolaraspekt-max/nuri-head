<?php

namespace App\Policies;

use App\Models\DealMeasurement;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * S-1a — Ownership der Deal-Measurement-Schreibfläche (Bestandslücke, siehe docs/sicherheits-backlog.md).
 *
 * Modell (b+ „Deal-Zuständigkeit"): schreiben darf, wer am Aufmaß/Deal fachlich beteiligt ist —
 * Ersteller (created_by) ODER zugewiesener Techniker (responsible_employee_id) ODER Deal-Zuständiger
 * (deals.employee_id) ODER Super-Admin. Portal-Hart-Deny ab Tag eins: ohne Employee-Kontext → immer deny.
 *
 * Altdaten-Übergang: Aufmaße ohne ableitbaren Owner (Waisen) werden NICHT hart blockiert (echte
 * Bestandsdaten), sondern „weich" erlaubt + strukturiert geloggt/gezählt. Nach einem waisenfreien
 * Zeitraum schaltet config('features.deal_measurement_orphan_hard_deny') auf hartes Deny um.
 */
class DealMeasurementPolicy
{
    use HandlesAuthorization;

    public const ORPHAN_COUNTER = 'deal_measurement_orphan_write_count';

    /** Schreiben/Ändern eines Aufmaßes (Items, Material, Details, HK-Übernahme). */
    public function write(User $user, DealMeasurement $measurement): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        $emp = $user->employeeId();
        if ($emp === null) {
            return false; // Portal-Hart-Deny: kein Employee-Kontext
        }

        $owners = array_values(array_filter([
            $measurement->created_by,
            $measurement->responsible_employee_id,
            $measurement->deal?->employee_id,
            // Erweiterungspunkt (Vertretung/Team/Filiale): weitere berechtigte Employee-IDs hier andocken.
        ], fn ($v) => $v !== null && $v !== ''));

        if ($owners === []) {
            // Waise: weiches Deny (Übergang) — loggen + zählen, erlauben; im harten Modus verweigern.
            $this->logOrphan($measurement, $emp);

            return ! (bool) config('features.deal_measurement_orphan_hard_deny', false);
        }

        return in_array((string) $emp, array_map('strval', $owners), true);
    }

    private function logOrphan(DealMeasurement $measurement, int $employeeId): void
    {
        Log::warning('deal_measurement_orphan_write', [
            'measurement_id' => $measurement->id,
            'employee_id' => $employeeId,
            'pfad' => optional(request()->route())->getName() ?? request()->path(),
        ]);

        Cache::put(self::ORPHAN_COUNTER, (int) Cache::get(self::ORPHAN_COUNTER, 0) + 1);
    }
}
