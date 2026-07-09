<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * MASTER-01 P1-IDOR Bündel-1 (Owner-Check) — PersonalNoteController: nur der Eigentümer
 * (personal_notes.user_id = Employee-ID = users.name) darf seine Notiz ändern/löschen.
 * Vorher: jeder Eingeloggte konnte fremde Notizen per ID mutieren.
 */
class PersonalNoteOwnerGateTest extends TestCase
{
    use DatabaseTransactions;

    /** User, dessen name die Employee-ID trägt (User::employeeId() = (int) name). */
    private function userMitEmployee(int $empId): User
    {
        return User::factory()->create(['password' => 'password', 'name' => (string) $empId, 'is_admin' => false]);
    }

    private function note(int $ownerEmpId): int
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $id = DB::table('personal_notes')->insertGetId([
            'user_id' => $ownerEmpId, 'category_id' => 1, 'title' => 'geheim', 'note' => 'x',
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        return $id;
    }

    public function test_fremder_kann_notiz_nicht_aendern_oder_loeschen(): void
    {
        $owner = $this->userMitEmployee(1001);
        $fremder = $this->userMitEmployee(2002);
        $noteId = $this->note(1001);

        $this->actingAs($fremder)->put("/notes_done/{$noteId}", ['is_done' => 1])->assertForbidden();
        $this->actingAs($fremder)->put("/note_change_color/{$noteId}", ['color' => 'red'])->assertForbidden();
        $this->actingAs($fremder)->delete("/notes_delete/{$noteId}")->assertForbidden();

        // Notiz unverändert (nicht done, nicht gelöscht).
        $row = DB::table('personal_notes')->find($noteId);
        $this->assertNull($row->deleted_at ?? null);
    }

    public function test_eigentuemer_darf(): void
    {
        $owner = $this->userMitEmployee(1001);
        $noteId = $this->note(1001);
        $res = $this->actingAs($owner)->put("/notes_done/{$noteId}", ['is_done' => 1]);
        $this->assertNotSame(403, $res->getStatusCode());
    }
}
