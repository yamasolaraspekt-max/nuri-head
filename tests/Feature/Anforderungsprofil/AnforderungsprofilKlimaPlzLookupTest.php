<?php

namespace Tests\Feature\Anforderungsprofil;

use App\Models\Anforderungsprofil;
use App\Models\AnforderungsprofilWert;
use App\Models\KlimaPlz;
use App\Services\Anforderungsprofil\AnforderungsprofilService;
use Database\Factories\AnforderungsprofilFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnforderungsprofilKlimaPlzLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_norm_aussentemp_wird_aus_klima_plz_ausgelesen(): void
    {
        KlimaPlz::create([
            'plz' => '10115',
            'ort' => 'Berlin',
            'lat' => 52.5200,
            'lon' => 13.4050,
            'nat_c' => -10.8,
            'temp_mittel_c' => 9.3,
            'hgt15_kd' => 3100,
            'vollbenutz_h' => 1700,
            'hoehe_m' => 34,
        ]);

        $werte = [
            ['schluessel' => 'standort_plz', 'wert' => '10115', 'datenlage' => 'gemessen', 'quelle' => 'Kunde', 'erfassungsweg' => 'manuell'],
        ];

        $profil = (new AnforderungsprofilService())->anlegen(AnforderungsprofilFactory::objektAnker(), 'Klimatest', $werte);

        $this->assertSame('entwurf', $profil->status);
        $this->assertSame('-10.8', $profil->werte()->where('schluessel', 'norm_aussentemp_c')->first()->wert);
        $this->assertSame(-10.8, (float) $profil->werte()->where('schluessel', 'norm_aussentemp_c')->first()->wert_num);
        $this->assertSame('berechnet', $profil->werte()->where('schluessel', 'norm_aussentemp_c')->first()->datenlage);
        $this->assertSame('klima_plz 10115', $profil->werte()->where('schluessel', 'norm_aussentemp_c')->first()->quelle);
    }
}
