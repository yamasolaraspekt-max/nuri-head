<?php

namespace Tests\Feature\Planner;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Z2-W0-3 — die Mitarbeiter-Kennung kommt aus der Sitzung, nicht aus dem Request.
 *
 * **Die Lücke, die das schließt:** `resolveEmployeeId()` nahm `employee_id` aus Request oder Query
 * und fiel nur bei `<= 0` auf den eigenen zurück; `location()` nahm sie sogar direkt aus dem
 * validierten Rumpf. Jeder eingeloggte Nutzer konnte damit Anwesenheits- und **Standortdaten
 * fremder Mitarbeiter** lesen und schreiben — Spur A, Datenschutz, betriebsratsrelevant.
 *
 * **Gemessen, nicht angenommen:** `users` hat keine `employee_id`-Spalte; `authEmployeeId()`
 * (`:18-23`) fällt auf `$user->employee?->id ?? $user->name` zurück und wandelt, wenn numerisch.
 * Deshalb trägt der Nutzer hier einen Zahlennamen — dieselbe Bauart wie im Vorbild
 * `CustomerPermissionGateTest`.
 *
 * Läuft ausschließlich gegen `ticket_testing` (`phpunit.xml` erzwingt `DB_DATABASE`).
 */
class PlannerAttendanceBindungTest extends TestCase
{
    use DatabaseTransactions;

    private const EIGEN = 4711;
    private const FREMD = 9999;

    private function nutzer(int $employeeId): User
    {
        return User::factory()->create([
            'password' => 'password',
            'name' => (string) $employeeId,   // authEmployeeId() liest den Namen, wenn numerisch
        ]);
    }

    /**
     * Ein Plan zum Anfassen. **Die Fremdschlüssel sind gemessen, nicht geraten:**
     * `planner_plans.customer_id` zeigt auf `new_leads`, `project_id` auf `lead_product_lists`
     * (information_schema). Deshalb werden hier vorhandene Kennungen genommen statt Nullen —
     * mit 0 scheitert der Einfügevorgang an der Fremdschlüsselbedingung, nicht an der Sache,
     * die dieser Test prüfen soll.
     */
    private function plan(): int
    {
        // `ticket_testing` ist leer — die Sätze werden hier angelegt und mit der Transaktion
        // wieder zurückgerollt. Gemessen: `new_leads` hat 53 Spalten und genau EINE Pflichtspalte
        // ohne Vorgabe (`customer_type`); `project_id` ist NULL-fähig, `customer_id` nicht.
        $kunde = (int) DB::table('new_leads')->insertGetId([
            'customer_type' => 'private', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return (int) DB::table('planner_plans')->insertGetId([
            'account_id' => null, 'customer_id' => $kunde, 'project_id' => null,
            'stage' => 'planung', 'title' => 'Z2-W0-3 Probe', 'status' => 'draft',
            'created_by' => 0, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    /** Kriterium A — der Schreibpfad weist eine fremde Kennung ausdrücklich ab. */
    public function test_location_mit_fremder_employee_id_wird_abgewiesen(): void
    {
        $u = $this->nutzer(self::EIGEN);
        $plan = $this->plan();

        $this->actingAs($u)
            ->post("/planner/plans/{$plan}/attendance/location", [
                'employee_id' => self::FREMD, 'lat' => 51.1, 'lng' => 6.9,
            ])
            ->assertForbidden();
    }

    /** Kriterium C — die eigene Kennung geht unverändert durch (kein Verlust). */
    public function test_location_mit_eigener_employee_id_geht_durch(): void
    {
        $u = $this->nutzer(self::EIGEN);
        $plan = $this->plan();

        $this->assertNotSame(403, $this->actingAs($u)
            ->post("/planner/plans/{$plan}/attendance/location", [
                'employee_id' => self::EIGEN, 'lat' => 51.1, 'lng' => 6.9,
            ])->getStatusCode());
    }

    /**
     * Kriterium B — der LESEpfad liefert keine fremden Daten.
     *
     * **Der Auftrag nennt „`day`/`report`" — für `day` trägt das nicht, und das ist eine
     * Berichtigung, keine Auslassung.** Gemessen: `resolveEmployeeId($request)` wird von zehn
     * Methoden gerufen (`checkIn`, `checkOut`, `travelStart`, `location`, `arrived`, `workStart`,
     * `workEnd`, `pauseStart`, `pauseEnd`, `report`) — **`day` ist nicht darunter**. `day` liest
     * die Mitarbeiter DES PLANS (`employeeIdsForPlan`) und ignoriert die Kennung im Request
     * vollständig; eine Bindung dort wäre wirkungslos.
     *
     * *(`day` hat eine eigene, andere Lücke: es liefert die Anwesenheit ALLER Mitarbeiter des
     * Plans an jeden Eingeloggten, weil keine Plan-Zugehörigkeit geprüft wird. Das ist das
     * Routen-Gate Z2-W0-5 und ausdrücklich NICHT Gegenstand dieses Auftrags — gemeldet, nicht
     * mitgebaut.)*
     *
     * Geprüft wird deshalb `report`, das die Kennung tatsächlich aus dem Request nahm.
     */
    public function test_report_mit_fremder_employee_id_liefert_die_eigenen_daten(): void
    {
        $u = $this->nutzer(self::EIGEN);
        $plan = $this->plan();
        foreach ([self::EIGEN, self::FREMD] as $mitarbeiter) {
            DB::table('attendances')->insert([
                'planner_plan_id' => $plan, 'employee_id' => $mitarbeiter,
                'date' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now(),
            ]);
        }

        $rumpf = $this->actingAs($u)
            ->get("/planner/plans/{$plan}/attendance/report?employee_id=" . self::FREMD)
            ->getContent() ?: '';

        $this->assertStringContainsString((string) self::EIGEN, $rumpf, 'der eigene Mitarbeiter muss geliefert werden');
        $this->assertStringNotContainsString((string) self::FREMD, $rumpf, 'der fremde darf NICHT geliefert werden');
    }
}
