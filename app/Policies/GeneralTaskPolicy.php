<?php

namespace App\Policies;

use App\Models\GeneralTask;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralTaskPolicy
{
    use HandlesAuthorization;

    /**
     * Admins duerfen alles (is_admin-Bypass, analog zum reparierten Rechte-Muster aus Paket 1).
     * null = regulaer weiterpruefen.
     */
    public function before(User $user): ?bool
    {
        return $user->is_admin ? true : null;
    }

    public function update(User $user, GeneralTask $task): bool
    {
        return $this->owns($user, $task);
    }

    public function delete(User $user, GeneralTask $task): bool
    {
        return $this->owns($user, $task);
    }

    public function restore(User $user, GeneralTask $task): bool
    {
        return $this->owns($user, $task);
    }

    /**
     * Eigentuemer (created_by) ODER zugewiesener Mitarbeiter (assignee) darf die Aufgabe verwalten.
     * users.name speichert die Mitarbeiter-Id -> User::employeeId().
     */
    protected function owns(User $user, GeneralTask $task): bool
    {
        $employeeId = $user->employeeId();

        if ($employeeId === null) {
            return false;
        }

        if ((int) $task->created_by === $employeeId) {
            return true;
        }

        return $task->assignees()->where('employees.id', $employeeId)->exists();
    }
}
