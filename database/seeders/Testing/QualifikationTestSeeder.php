<?php

namespace Database\Seeders\Testing;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Test-Mitarbeiter fuer die B3-Schwellenlogik + Prueferkette.
 * Anforderung der Montage-Taetigkeit = sort_order 3. Qualifiziert = eigener sort_order <= 3.
 *
 *   Pruefer        (sort 3)  -> qualifiziert, Vorgesetzter der beiden Monteure  -> wird Pruefer
 *   MonteurQual    (sort 2)  -> qualifiziert (2 <= 3)                            -> Karte -> done
 *   MonteurUnqual  (sort 5)  -> NICHT qualifiziert (5 > 3)                       -> Karte -> reported + Pruefer
 *   Fremder        (sort 1)  -> ausserhalb der Kette                             -> sieht Fall C NICHT
 *
 * Je Mitarbeiter ein Marker-User (name = employee.id fuer authEmployeeId,
 * E-Mail-Domain @test-harness.local als Teardown-Schluessel). Referenziert die
 * echten position_qualifications; legt KEINE neuen an.
 */
class QualifikationTestSeeder extends Seeder
{
    use HarnessSupport;

    public function run(): void
    {
        $this->guardLocal();

        $qualIdBySort = fn(int $sort) => DB::table('position_qualifications')
            ->where('sort_order', $sort)->orderBy('id')->value('id');

        // Pruefer zuerst (Vorgesetzter der Monteure)
        $reviewerId = $this->upsertId('employees',
            ['name' => 'Pruefer', 'lastname' => self::TAG],
            ['title' => 'Hr', 'qualification_id' => $qualIdBySort(3), 'supervisor' => null, 'status' => 'active']
        );

        $qualMonteurId = $this->upsertId('employees',
            ['name' => 'MonteurQual', 'lastname' => self::TAG],
            ['title' => 'Hr', 'qualification_id' => $qualIdBySort(2), 'supervisor' => $reviewerId, 'status' => 'active']
        );

        $unqualMonteurId = $this->upsertId('employees',
            ['name' => 'MonteurUnqual', 'lastname' => self::TAG],
            ['title' => 'Hr', 'qualification_id' => $qualIdBySort(5), 'supervisor' => $reviewerId, 'status' => 'active']
        );

        $otherId = $this->upsertId('employees',
            ['name' => 'Fremder', 'lastname' => self::TAG],
            ['title' => 'Hr', 'qualification_id' => $qualIdBySort(1), 'supervisor' => null, 'status' => 'active']
        );

        // Marker-User je Mitarbeiter: name = employee.id (authEmployeeId/employeeIdFromUser),
        // is_admin = 0 (damit die Pruefliste-Admin-Verzweigung nicht faelschlich greift).
        foreach ([
            'Pruefer' => $reviewerId,
            'MonteurQual' => $qualMonteurId,
            'MonteurUnqual' => $unqualMonteurId,
            'Fremder' => $otherId,
        ] as $role => $eid) {
            $this->upsertId('users',
                ['email' => 'emp' . $eid . self::USER_DOMAIN],
                ['name' => (string) $eid, 'password' => bcrypt('test-harness'), 'is_admin' => 0]
            );
        }

        $this->command?->info(self::TAG . " Mitarbeiter: reviewer=$reviewerId(sort3) qual=$qualMonteurId(sort2) unqual=$unqualMonteurId(sort5) other=$otherId(sort1)");
        $this->command?->info(self::TAG . ' Marker-User (name=employee.id, ' . self::USER_DOMAIN . ') je Mitarbeiter angelegt.');
    }
}
