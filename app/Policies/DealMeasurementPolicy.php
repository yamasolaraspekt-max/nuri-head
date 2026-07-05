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

    // ----- S-1b-2: enge Abilities (Übergangs-Soft-Deny je Ability) -------------------------------

    /** Disposition (Techniker zuweisen): Deal-Zuständiger ∨ Ersteller ∨ Admin — NICHT der zugewiesene Techniker. */
    public function assign(User $user, DealMeasurement $measurement): bool
    {
        return $this->ability($user, $measurement, $this->creatorOrDealOwnerIds($measurement), 'assign_denied', 'features.deal_measurement_assign_hard_deny');
    }

    /** Entsperren (hebt den 423-Schutz auf): engster Kreis — Deal-Zuständiger ∨ Admin. */
    public function unlock(User $user, DealMeasurement $measurement): bool
    {
        return $this->ability($user, $measurement, $this->dealOwnerIds($measurement), 'unlock_denied', 'features.deal_measurement_unlock_hard_deny');
    }

    /** Löschen (soft): Ersteller ∨ Deal-Zuständiger ∨ Admin. */
    public function delete(User $user, DealMeasurement $measurement): bool
    {
        return $this->ability($user, $measurement, $this->creatorOrDealOwnerIds($measurement), 'delete_denied', 'features.deal_measurement_delete_hard_deny');
    }

    /**
     * Strikt erlaubt → true. Nicht strikt, aber im breiteren write-Kreis → Übergangs-Soft-Deny
     * (loggen+zählen, erlauben; im harten Modus verweigern). Sonst (unbeteiligt/Portal) → hart deny.
     *
     * @param  array<int, mixed>  $strictOwners
     */
    private function ability(User $user, DealMeasurement $measurement, array $strictOwners, string $deniedKey, string $hardFlag): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        $emp = $user->employeeId();
        if ($emp === null) {
            return false;
        }
        $e = (string) $emp;

        if (in_array($e, array_map('strval', $strictOwners), true)) {
            return true;
        }

        if (in_array($e, array_map('strval', $this->writeOwnerIds($measurement)), true)) {
            $this->logAbilitySoftDeny($deniedKey, $measurement, $emp);

            return ! (bool) config($hardFlag, false);
        }

        return false;
    }

    /** @return array<int, mixed> voller write-Kreis (b+): Ersteller, zugewiesener Techniker, Deal-Zuständiger. */
    private function writeOwnerIds(DealMeasurement $m): array
    {
        return array_values(array_filter([$m->created_by, $m->responsible_employee_id, $m->deal?->employee_id], fn ($v) => $v !== null && $v !== ''));
    }

    /** @return array<int, mixed> Ersteller ∨ Deal-Zuständiger (ohne den zugewiesenen Techniker). */
    private function creatorOrDealOwnerIds(DealMeasurement $m): array
    {
        return array_values(array_filter([$m->created_by, $m->deal?->employee_id], fn ($v) => $v !== null && $v !== ''));
    }

    /** @return array<int, mixed> nur Deal-Zuständiger. */
    private function dealOwnerIds(DealMeasurement $m): array
    {
        return array_values(array_filter([$m->deal?->employee_id], fn ($v) => $v !== null && $v !== ''));
    }

    private function logAbilitySoftDeny(string $key, DealMeasurement $m, int $emp): void
    {
        Log::warning('deal_measurement_ability_soft_deny', [
            'ability' => $key,
            'measurement_id' => $m->id,
            'employee_id' => $emp,
            'pfad' => optional(request()->route())->getName() ?? request()->path(),
        ]);

        Cache::put($key.'_count', (int) Cache::get($key.'_count', 0) + 1);
    }
}
