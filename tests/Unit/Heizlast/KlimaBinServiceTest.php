<?php

namespace Tests\Unit\Heizlast;

use App\Services\Heizlast\KlimaBinService;
use PHPUnit\Framework\TestCase;

class KlimaBinServiceTest extends TestCase
{
    public function test_bins_decken_ein_jahr_ab(): void
    {
        $p = (new KlimaBinService)->profil('80331'); // Süddeutschland

        $this->assertSame('sued', $p['zone']);
        $this->assertSame(-14.0, $p['theta_e']);
        $this->assertSame(15.0, $p['heizgrenze_c']);
        $this->assertEqualsWithDelta(8760, array_sum(array_map(fn ($b) => $b['h'], $p['bins'])), 1.0);
    }

    public function test_saisongewichte_summieren_je_bin_zu_eins(): void
    {
        $p = (new KlimaBinService)->profil('10115');

        foreach ($p['bins'] as $b) {
            $this->assertEqualsWithDelta(1.0, array_sum($b['saison']), 1e-6);
        }
    }

    public function test_kalte_bins_sind_winterlastig(): void
    {
        $p = (new KlimaBinService)->profil('60311'); // Mitte

        $kalt = collect($p['bins'])->first(fn ($b) => abs($b['t'] + 10.0) < 0.01);
        $this->assertNotNull($kalt);
        $this->assertGreaterThan(0.5, $kalt['saison']['winter']);
        $this->assertGreaterThan($kalt['saison']['sommer'], $kalt['saison']['winter']);
    }

    public function test_plz_mapping_trennt_klimazonen(): void
    {
        $s = new KlimaBinService;

        $this->assertSame('sued', $s->profil('80000')['zone']);
        $this->assertSame('nordost', $s->profil('20000')['zone']);
        $this->assertSame('west', $s->profil('50000')['zone']);
        $this->assertSame('mitte', $s->profil('60000')['zone']);
    }
}
