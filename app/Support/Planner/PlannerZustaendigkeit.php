<?php

namespace App\Support\Planner;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Z2-W0-5 · Zuständigkeitsbindung für `api/planner/*` — **ein** Baustein, vier Anschlussstellen.
 *
 * Vor diesem Baustein prüfte keiner der 20 `api/planner`-Endpunkte, ob der angemeldete
 * Mitarbeiter für den angefragten Mitarbeiter, Kunden, Plan oder Auftragspunkt überhaupt
 * zuständig ist — `Authenticate:sanctum` allein, keine Policy, kein Gate, kein globaler Scope.
 * Und jedes Konto mit Passwort bekommt einen Token mit allen Abilities, `sanctum.stateful`
 * schließt `ticket.test` ein: jede eingeloggte Browser-Sitzung erreicht dieselben Endpunkte
 * per Cookie, ganz ohne Token. Der Radius war also nicht „die Mobil-App", sondern „jedes Konto".
 *
 * ## Zwei Achsen, und sie sind absichtlich verschieden hart
 *
 * **Sehen** (`darfMitarbeiterSehen`) folgt dem Rechte-Schalter aus W0-7. Yamas Entscheidung vom
 * 21.08. lautet „alle Rechte für alle"; steht der Schalter auf `true`, sieht heute jeder jeden
 * Tagesbericht. Das Tor wird trotzdem gebaut und mit Schalter `false` geprüft, damit es den Tag
 * überlebt, an dem differenziert wird (Y-9).
 *
 * **Zuständig sein** (`istZustaendigFuer*`) folgt ihm **nicht**. Das Entscheidungsblatt trennt
 * „Sehen" von „Fälschen": ein Schalter, der Lesetore öffnet, darf keine Schreibpfade in fremde
 * Kundenakten und fremde Pläne öffnen. Diese Prüfungen rufen `hasPermission()` deshalb nie auf —
 * sie fragen die Zuordnung in `planner_item_employees` und sonst nichts. `isSuperAdmin()` bleibt
 * als zweite Achse, weil Disposition ohne sie nicht arbeiten kann; der Schalter macht niemanden
 * zum Admin (`User::hasPermission()` sagt das ausdrücklich).
 *
 * ## Eine gemessene Eigenheit, die man kennen muss
 *
 * Das Rechte-Item `Planner` **existiert heute nicht** — `user_rolls.item_id` führt 14 Items
 * (Administrator, Customer, Email, Employee, Finance, Inquiry, Invoice, Organization, Partner,
 * Problem, Product, Programmer, Super, Users), keines davon `Planner`. Mit Schalter `false`
 * fällt die Sehen-Regel damit heute auf „selbst oder Admin" zusammen. Das ist gewollt und die
 * sichere Seite; legt jemand das Item an, wirkt die Regel ohne weitere Änderung. Gemessen am
 * 21.08., nicht angenommen.
 *
 * ## Arbeitsannahme, ausdrücklich als solche gekennzeichnet
 *
 * Stufe 1 setzt „selbst ODER Admin ODER `Planner`-Leserecht". Die im Code vorhandene
 * Vorgesetztenkette (`resolveReviewer`) ist **Stufe 2** und hängt an **Y-9** — ob Disponenten
 * ohne Vorgesetztenverhältnis fremde Tage sehen dürfen, ist eine Rechte-Fachentscheidung und
 * wird hier nicht still getroffen.
 */
trait PlannerZustaendigkeit
{
    /**
     * Mitarbeiter-Kennung des angemeldeten Kontos.
     *
     * In diesem Projekt trägt `users.name` die `employees.id` — eine Eigenheit des Bestands,
     * die `User::employeeId()` bereits festhält. Die vorhandene Kette `employee_id ?? employee->id
     * ?? name` wird beibehalten, damit dieser Baustein dieselbe Antwort gibt wie die sechs
     * `authEmployeeId()` in den Controllern; eine zweite Wahrheit über die eigene Kennung wäre
     * genau der Fehler, den dieser Auftrag vermeiden soll.
     */
    protected function pzMitarbeiterId(): ?int
    {
        $user = auth()->user();

        $id = $user?->employee_id ?? $user?->employee?->id ?? $user?->name ?? null;

        return is_numeric($id) ? (int) $id : null;
    }

    protected function pzIstAdmin(): bool
    {
        return (bool) (auth()->user()?->isSuperAdmin() ?? false);
    }

    /** SEHEN — folgt dem Rechte-Schalter (W0-7 / Y-9). */
    protected function darfMitarbeiterSehen(int $employeeId): bool
    {
        $eigene = $this->pzMitarbeiterId();

        if ($eigene !== null && $eigene === $employeeId) {
            return true;
        }

        if ($this->pzIstAdmin()) {
            return true;
        }

        return (bool) (auth()->user()?->hasPermission('Planner', 'read') ?? false);
    }

    /**
     * ZUSTÄNDIG für einen Auftragspunkt — Zuordnung in `planner_item_employees`.
     *
     * Dasselbe `whereExists`, das `completeItemWithReport()` seit jeher vorbildlich fährt
     * (`PlannerEmployeeApiController.php:1726-1731`). Es wird hier nicht neu erfunden, sondern
     * an den einen Ort gehoben, an dem die vier Anschlussstellen es teilen können.
     */
    protected function istZustaendigFuerItem(int $itemId): bool
    {
        if ($this->pzIstAdmin()) {
            return true;
        }

        $eigene = $this->pzMitarbeiterId();

        if ($eigene === null || $itemId <= 0 || !Schema::hasTable('planner_item_employees')) {
            return false;
        }

        return DB::table('planner_item_employees')
            ->where('planner_item_id', $itemId)
            ->where('employee_id', $eigene)
            ->exists();
    }

    /** ZUSTÄNDIG für einen Plan — mindestens ein mir zugeordneter Punkt darin. */
    protected function istZustaendigFuerPlan(int $planId): bool
    {
        if ($this->pzIstAdmin()) {
            return true;
        }

        $eigene = $this->pzMitarbeiterId();

        if ($eigene === null || $planId <= 0
            || !Schema::hasTable('planner_items') || !Schema::hasTable('planner_item_employees')) {
            return false;
        }

        return DB::table('planner_items as pi')
            ->join('planner_item_employees as pie', 'pie.planner_item_id', '=', 'pi.id')
            ->where('pi.plan_id', $planId)
            ->where('pie.employee_id', $eigene)
            ->exists();
    }

    /**
     * ZUSTÄNDIG für eine Kundenakte — über einen mir zugeordneten Punkt.
     *
     * Der Kunde hängt im Bestand auf zwei Wegen am Plan: direkt (`planner_plans.customer_id`)
     * und über das Projekt (`planner_plans.project_id` → `lead_product_lists.customer_id`).
     * Beide zählen, sonst sperrt der Baustein Mitarbeiter aus ihren eigenen Akten aus — genau
     * die Fehlsperre, die der Rückweg des Auftrags benennt.
     */
    protected function istZustaendigFuerKunde(int $customerId): bool
    {
        if ($this->pzIstAdmin()) {
            return true;
        }

        $eigene = $this->pzMitarbeiterId();

        if ($eigene === null || $customerId <= 0
            || !Schema::hasTable('planner_plans') || !Schema::hasTable('planner_items')
            || !Schema::hasTable('planner_item_employees')) {
            return false;
        }

        $abfrage = DB::table('planner_items as pi')
            ->join('planner_plans as pp', 'pp.id', '=', 'pi.plan_id')
            ->join('planner_item_employees as pie', 'pie.planner_item_id', '=', 'pi.id')
            ->where('pie.employee_id', $eigene);

        if (Schema::hasTable('lead_product_lists')) {
            $abfrage->leftJoin('lead_product_lists as lpl', 'lpl.id', '=', 'pp.project_id')
                ->where(function ($w) use ($customerId) {
                    $w->where('pp.customer_id', $customerId)
                        ->orWhere('lpl.customer_id', $customerId);
                });
        } else {
            $abfrage->where('pp.customer_id', $customerId);
        }

        return $abfrage->exists();
    }

    // ---------------------------------------------------------------------
    // Fordernde Formen — 403, damit der Aufrufer nicht vergessen kann zu prüfen.
    //
    // 403 und nicht 404: die Nachvollzugs-Matrix des Auftrags nennt für alle vier Stellen
    // ausdrücklich 403, und anders als bei einer stillgelegten Fläche (Z2-W0-10) ist die
    // Existenz dieser Endpunkte kein Geheimnis — sie stehen im Nuriva-Vertrag. Preisgegeben
    // wird nur, dass es die Route gibt; die Daten dahinter bleiben zu.
    // ---------------------------------------------------------------------

    protected function verlangeMitarbeiterSicht(int $employeeId): void
    {
        abort_unless($this->darfMitarbeiterSehen($employeeId), 403, 'Kein Zugriff auf diesen Mitarbeiter.');
    }

    protected function verlangeZustaendigkeitFuerItem(int $itemId): void
    {
        abort_unless($this->istZustaendigFuerItem($itemId), 403, 'Kein Zugriff auf diesen Auftragspunkt.');
    }

    protected function verlangeZustaendigkeitFuerPlan(int $planId): void
    {
        abort_unless($this->istZustaendigFuerPlan($planId), 403, 'Kein Zugriff auf diesen Plan.');
    }

    protected function verlangeZustaendigkeitFuerKunde(int $customerId): void
    {
        abort_unless($this->istZustaendigFuerKunde($customerId), 403, 'Kein Zugriff auf diese Kundenakte.');
    }
}
