<?php

namespace Tests\Feature;

use App\Models\PlanUpload;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * AUF-88-P1 — die erste Zusage zu `PlanUpload` überhaupt (R17, `geerbte_zusagen`: 0 Treffer
 * gemessen 30.07. 06:53). Eine Fläche, die Dateien annimmt, sie speichert und einen Job anstößt,
 * war vollständig unverriegelt — dieser Auftrag verriegelt sie als Erster.
 *
 * Läuft gegen die Test-DB (RefreshDatabase, `phpunit.xml` erzwingt `ticket_testing`); die
 * Arbeits-/Dev-DB `ticket` wird NICHT geschrieben. Muster für Objekt-/Rechte-Fixtures aus
 * `tests/Feature/Hausplaner/UebernahmeKnopfTest.php`.
 */
class PlanUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement("SET SESSION sql_mode=''");
        config(['broadcasting.default' => 'null', 'services.import.url' => '']);
    }

    private function user(): User
    {
        return User::factory()->create(['password' => 'password', 'name' => (string) random_int(1, 9999)]);
    }

    /** @param array<string,int> $flags */
    private function grant(User $u, array $flags): void
    {
        DB::table('user_rolls')->insert(array_merge([
            'user_id' => $u->id, 'item_id' => 'Hausplaner', 'is_read' => 0, 'is_add' => 0, 'is_update' => 0, 'is_delete' => 0,
            'created_at' => now(), 'updated_at' => now(),
        ], $flags));
    }

    /** Ein Hausplaner-Objekt (`LeadAlternativeAdd`) — dieselbe Fixture wie `UebernahmeKnopfTest`. */
    private function objekt(int $seed = 900): int
    {
        $customer = $seed + 1;
        $alt = $seed + 2;
        DB::table('new_leads')->insert(['id' => $customer, 'customer_type' => 'privat', 'name' => 'K', 'lastname' => 'T', 'email' => 'k'.$seed.'@example.com', 'phone' => '0', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('lead_alternative_adds')->insert(['id' => $alt, 'lead_id' => $customer, 'street' => 'Weg 1', 'postcode' => '12345', 'city' => 'S', 'created_at' => now(), 'updated_at' => now()]);

        return $alt;
    }

    // --- K-01: die Signaturprüfung liegt VOR dem Speichern -----------------------------------------

    public function test_eine_umbenannte_datei_wird_abgelehnt_und_liegt_nicht_auf_der_platte(): void
    {
        Storage::fake('local');
        $u = $this->user();

        // .pdf benannt, aber der Inhalt ist kein PDF — `UploadedFile::fake()->create()` erzeugt
        // beliebige Bytes, keine echte Signatur.
        $datei = UploadedFile::fake()->create('grundriss.pdf', 5, 'application/pdf');

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $datei]);

        $antwort->assertStatus(422);
        $this->assertSame(0, PlanUpload::count(), 'trotz Ablehnung wurde ein Datensatz angelegt');
        // Die Wirkung, nicht nur die Meldung (F-06): das Verzeichnis bleibt leer.
        Storage::disk('local')->assertDirectoryEmpty('plan-uploads');
    }

    public function test_eine_echte_pdf_signatur_wird_angenommen(): void
    {
        // Gegenprobe zur Ablehnung oben: dieselbe Endung, jetzt mit echtem Kopf — die Prüfung
        // lehnt nicht pauschal jede PDF-Datei ab.
        Storage::fake('local');
        $u = $this->user();
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $datei]);

        $antwort->assertStatus(200);
        $this->assertSame(1, PlanUpload::count());
        Storage::disk('local')->assertExists(PlanUpload::first()->pfad);
    }

    public function test_ein_bild_mit_pdf_endung_wird_abgelehnt(): void
    {
        // Der Fall, den §3 des Master-Prompts konkret nennt: eine umbenannte Datei.
        Storage::fake('local');
        $u = $this->user();
        $png = UploadedFile::fake()->image('foto.png', 10, 10); // echte PNG-Bytes
        $umbenannt = UploadedFile::fake()->createWithContent('foto.pdf', file_get_contents($png->getRealPath()));

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $umbenannt]);

        $antwort->assertStatus(422);
        $this->assertSame(0, PlanUpload::count());
    }

    public function test_dwg_und_dxf_haben_keine_verlaessliche_signatur_und_werden_ueber_die_endung_angenommen(): void
    {
        // `DateiSignatur::passtZuEndung` prüft DWG/DXF bewusst nicht (keine feste Signatur) —
        // eine Ablehnung ohne verlässliche Prüfung wäre falsche Sicherheit, keine echte.
        Storage::fake('local');
        $u = $this->user();
        $datei = UploadedFile::fake()->create('kabel.dxf', 5, 'application/dxf');

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $datei]);

        $antwort->assertStatus(200);
    }

    // --- K-02: Projektbezug + Ownership-Gate --------------------------------------------------------

    public function test_ein_nutzer_mit_hausplaner_update_darf_das_projekt_zuweisen(): void
    {
        Storage::fake('local');
        $u = $this->user();
        $this->grant($u, ['is_update' => 1]);
        $alt = $this->objekt();
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), [
            'datei' => $datei, 'lead_alternative_add_id' => $alt,
        ]);

        $antwort->assertStatus(200);
        $this->assertSame($alt, PlanUpload::first()->lead_alternative_add_id);
    }

    public function test_ein_nutzer_ohne_hausplaner_update_darf_kein_projekt_zuweisen(): void
    {
        // **Der Kern von K-02, mit einem zweiten Nutzer geprüft, nicht behauptet.** Kein Grant.
        Storage::fake('local');
        $u = $this->user();
        $alt = $this->objekt(910);
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), [
            'datei' => $datei, 'lead_alternative_add_id' => $alt,
        ]);

        $antwort->assertStatus(403);
        // Fail-closed: nicht nur die Zuordnung, der GANZE Upload unterbleibt — kein halb
        // angelegter Datensatz, keine Datei auf der Platte.
        $this->assertSame(0, PlanUpload::count());
    }

    public function test_ein_upload_ohne_projektbezug_bleibt_moeglich_ganz_ohne_grant(): void
    {
        // Gegenprobe: das Gate greift nur, wenn tatsächlich ein Projekt zugewiesen wird — ein
        // Upload für sich allein (Block A der Aufnahme, `heizlast_projekt_id`-Weg) bleibt frei.
        Storage::fake('local');
        $u = $this->user();
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $datei]);

        $antwort->assertStatus(200);
        $this->assertNull(PlanUpload::first()->lead_alternative_add_id);
    }

    public function test_ein_unbekanntes_projekt_wird_abgelehnt(): void
    {
        Storage::fake('local');
        $u = $this->user();
        $this->grant($u, ['is_update' => 1]);
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");

        $antwort = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), [
            'datei' => $datei, 'lead_alternative_add_id' => 999999,
        ]);

        $antwort->assertStatus(422);
    }

    // --- K-04: die Kalibrierung wird gespeichert, nicht neu erfunden --------------------------------

    public function test_der_massstab_wird_gespeichert_und_nur_vom_besitzer(): void
    {
        Storage::fake('local');
        $u = $this->user();
        $fremder = $this->user();
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");
        $id = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $datei])->json('id');

        $this->actingAs($fremder)
            ->putJson(route('energie.plan-upload.massstab', $id), ['massstab_mm_pro_einheit' => 12.5])
            ->assertStatus(403);

        $this->actingAs($u)
            ->putJson(route('energie.plan-upload.massstab', $id), ['massstab_mm_pro_einheit' => 12.5])
            ->assertStatus(200)
            ->assertJson(['massstab_mm_pro_einheit' => 12.5]);

        $this->assertSame(12.5, PlanUpload::find($id)->massstab_mm_pro_einheit);
    }

    public function test_ein_massstab_von_null_oder_darunter_wird_abgelehnt(): void
    {
        // Ein Maßstab von 0 oder negativ ist keine Kalibrierung, sondern eine kaputte Rechnung
        // (Division durch 0 im Aufrufer) — das Feld verlangt `gt:0`, kein `min:0`.
        Storage::fake('local');
        $u = $this->user();
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");
        $id = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $datei])->json('id');

        $this->actingAs($u)->putJson(route('energie.plan-upload.massstab', $id), ['massstab_mm_pro_einheit' => 0])->assertStatus(422);
        $this->actingAs($u)->putJson(route('energie.plan-upload.massstab', $id), ['massstab_mm_pro_einheit' => -3])->assertStatus(422);
    }

    // --- K-06: ohne Import-Dienst bricht nichts -----------------------------------------------------

    public function test_ohne_konfigurierten_import_dienst_bleibt_die_klassifikation_graceful(): void
    {
        // `services.import.url` ist in setUp() bereits leer — `ImportServiceClient::aktiv()` false.
        Storage::fake('local');
        $u = $this->user();
        $datei = UploadedFile::fake()->createWithContent('grundriss.pdf', "%PDF-1.4\n%%EOF");
        $id = $this->actingAs($u)->postJson(route('energie.plan-upload.store'), ['datei' => $datei])->json('id');

        $upload = PlanUpload::find($id);
        (new \App\Jobs\PlanKlassifizieren($upload))->handle(app(\App\Services\Import\ImportServiceClient::class));

        $upload->refresh();
        $this->assertSame('klassifiziert', $upload->status);
        $this->assertSame('pdf', $upload->typ);
        $this->assertNull(data_get($upload->meta, 'bild_pfad'), 'ohne Dienst darf keine Rasterung stattgefunden haben');
    }

    public function test_ein_unbekannter_dateityp_wird_als_fehler_klassifiziert_ohne_absturz(): void
    {
        Storage::fake('local');
        $u = $this->user();
        // .txt ist nicht in ENDUNGEN — das lehnt schon die Validierung ab; dieser Test prüft den
        // Job direkt (Umgehung der Validierung ist genau der Fall, den `PlanKlassifizieren`
        // selbst noch abfangen muss, falls je ein Datensatz ohne Validierung entsteht).
        $upload = PlanUpload::create([
            'user_id' => $u->id, 'original_name' => 'notiz.txt', 'pfad' => 'plan-uploads/x',
            'mime' => 'text/plain', 'groesse_bytes' => 3, 'status' => 'neu',
        ]);

        (new \App\Jobs\PlanKlassifizieren($upload))->handle(app(\App\Services\Import\ImportServiceClient::class));

        $upload->refresh();
        $this->assertSame('fehler', $upload->status);
    }
}
