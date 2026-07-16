<?php

namespace App\Services\Sperre;

use App\Models\Employee;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

/**
 * BearbeitungsSperre — EINE systemweite Sperr-Mechanik (Yama-Entscheid 2026-07-16:
 * Sperre JE DOKUMENT, nicht je Kunde).
 *
 * Herausgelöst aus dem bewährten Angebots-Muster (OfferDocumentController presence/lock):
 * - Presence-Liste je Dokument (Cache, 3 Min TTL, Einträge älter 2 Min werden bereinigt)
 * - weiche Exklusiv-Sperre: der erste aktive Nutzer hält sie, Ping verlängert sie,
 *   nach 2 Min ohne Ping (Browser zu, Feierabend) fällt sie automatisch und geht
 *   an den nächsten aktiven Nutzer
 * - kein DB-Schema, kein harter Deadlock möglich — Cache-basiert wie das Original.
 *
 * Verwendung: @include('admin.layouts.partials.bearbeitungs-sperre', ['bereich' => 'rechnung', 'sperrId' => $invoice->id])
 * Bereichs-Begriffe (klein, eindeutig): rechnung · auftrag · grundriss · hausplan · materialliste …
 * Die Angebots-Mappe behält ihre bestehende Implementierung (offer_lock:*) — Ablösung = eigener Posten.
 */
class BearbeitungsSperreService
{
    private const PRESENCE_TTL_MIN = 3;
    private const STALE_MIN = 2;

    /** Heartbeat: Presence eintragen, Sperre holen/verlängern. */
    public function ping(object $user, string $bereich, int|string $id): array
    {
        [$presenceKey, $lockKey] = $this->keys($bereich, $id);
        $me = $this->resolveUser($user);

        $users = $this->cleanUsers(Cache::get($presenceKey, []));
        $users[$user->id] = $me;
        Cache::put($presenceKey, $users, now()->addMinutes(self::PRESENCE_TTL_MIN));

        $lock = $this->freshLock($lockKey, $users);

        if (!$lock || (int) ($lock['id'] ?? 0) === (int) $user->id) {
            Cache::put($lockKey, $me, now()->addMinutes(self::PRESENCE_TTL_MIN));
            $lock = $me;
        }

        return $this->zustand($users, $lock, $user);
    }

    /** Nur lesen (kein Presence-Eintrag) — z. B. für Anzeigen auf Übersichts-Flächen. */
    public function status(object $user, string $bereich, int|string $id): array
    {
        [$presenceKey, $lockKey] = $this->keys($bereich, $id);

        $users = $this->cleanUsers(Cache::get($presenceKey, []));
        Cache::put($presenceKey, $users, now()->addMinutes(self::PRESENCE_TTL_MIN));

        return $this->zustand($users, $this->freshLock($lockKey, $users), $user);
    }

    /** Fläche verlassen: Presence austragen; hielt man die Sperre, wird sie frei. */
    public function leave(object $user, string $bereich, int|string $id): array
    {
        [$presenceKey, $lockKey] = $this->keys($bereich, $id);

        $users = $this->cleanUsers(Cache::get($presenceKey, []));
        unset($users[$user->id]);
        Cache::put($presenceKey, $users, now()->addMinutes(self::PRESENCE_TTL_MIN));

        $lock = Cache::get($lockKey);
        if ($lock && (int) ($lock['id'] ?? 0) === (int) $user->id) {
            Cache::forget($lockKey);
            $lock = null;
        }

        return $this->zustand($users, $this->freshLock($lockKey, $users), $user);
    }

    /** @return array{0:string,1:string} */
    private function keys(string $bereich, int|string $id): array
    {
        if (!preg_match('/^[a-z0-9_-]{2,40}$/', $bereich)) {
            throw new InvalidArgumentException("Ungültiger Sperr-Bereich '{$bereich}' (klein, a-z0-9_-).");
        }
        $id = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $id);

        return ["sperre_presence:{$bereich}_{$id}", "sperre_lock:{$bereich}_{$id}"];
    }

    /** Sperre lesen und verfallene/verwaiste Sperren räumen (Original-Semantik: 2-Min-Stale + Owner offline). */
    private function freshLock(string $lockKey, array $users): ?array
    {
        $lock = Cache::get($lockKey);

        if ($lock && ($lock['last_seen'] ?? 0) < now()->subMinutes(self::STALE_MIN)->timestamp) {
            Cache::forget($lockKey);
            $lock = null;
        }
        if ($lock && !isset($users[(int) ($lock['id'] ?? 0)])) {
            Cache::forget($lockKey);
            $lock = null;
        }

        return $lock;
    }

    private function cleanUsers(array $users): array
    {
        return array_filter($users, fn ($row) => ($row['last_seen'] ?? 0) >= now()->subMinutes(self::STALE_MIN)->timestamp);
    }

    private function resolveUser(object $user): array
    {
        $employeeId = is_numeric($user->name ?? null) ? (int) $user->name : null;
        $employee = $employeeId ? Employee::find($employeeId) : null;

        $fullName = $employee
            ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''))
            : trim(($user->firstname ?? '') . ' ' . ($user->lastname ?? ''));

        return [
            'id' => $user->id,
            'name' => $fullName !== '' ? $fullName : 'Unbekannt',
            'last_seen' => now()->timestamp,
        ];
    }

    private function zustand(array $users, ?array $lock, object $user): array
    {
        return [
            'users' => array_values($users),
            'locked' => (bool) $lock,
            'locked_by_other' => $lock && (int) ($lock['id'] ?? 0) !== (int) $user->id,
            'lock_user' => $lock,
        ];
    }
}
