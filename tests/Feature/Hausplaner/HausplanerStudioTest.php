<?php

namespace Tests\Feature\Hausplaner;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HausplanerStudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_studio_liefert_gueltige_persistenzfreie_scratch_szene(): void
    {
        $admin = User::factory()->create(['password' => 'password', 'is_admin' => 1]);

        $response = $this->actingAs($admin)->get('/admin/hausplaner/studio');

        $response->assertOk();
        $html = $response->getContent();
        $this->assertIsString($html);
        $this->assertMatchesRegularExpression(
            '/<script type="application\/json" id="hausplaner-scene">(?<scene>.*?)<\/script>/s',
            $html,
        );

        preg_match(
            '/<script type="application\/json" id="hausplaner-scene">(?<scene>.*?)<\/script>/s',
            $html,
            $treffer,
        );
        $scene = json_decode($treffer['scene'], true, 512, JSON_THROW_ON_ERROR);

        $this->assertIsInt($scene['projectId']);
        $this->assertSame(999999999, $scene['projectId'], 'Scratch-ID muss positiv und bewusst absurd hoch bleiben.');
        $this->assertSame(1, $scene['schemaVersion'], 'Studio muss den v1-zu-v2-Migrationspfad weiter ausueben.');
        $this->assertStringNotContainsString('data-speichern-url', $html, 'Studio darf keinen Persistenzpfad erhalten.');
    }
}
