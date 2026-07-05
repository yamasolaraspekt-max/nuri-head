<?php

namespace Tests\Feature\Anforderungsprofil;

use App\Models\Anforderungsprofil;
use App\Models\AnforderungsprofilWert;
use App\Models\Employee;
use App\Models\LeadAlternativeAdd;
use App\Services\Anforderungsprofil\AnforderungsprofilService;
use Database\Factories\AnforderungsprofilFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

/**
 * B2a-2 — Anforderungsprofil (Framework Phase 1): Verankerung (Whitelist + exists),
 * Quellen-Pflicht je Wert (Registry), append-only Versionierung, Ein-aktiv-Invariante.
 */
class AnforderungsprofilTest extends TestCase
{
    use RefreshDatabase;

    private function service(): AnforderungsprofilService
    {
        return new AnforderungsprofilService;
    }

    /** @return array<int, array<string, mixed>> */
    private function beispielWerte(): array
    {
        return [
            ['schluessel' => 'phi_hl_kw', 'wert' => '9.6', 'wert_num' => 9.6, 'datenlage' => 'berechnet', 'quelle' => 'HeizlastRechner'],
            ['schluessel' => 'vorlauf_c', 'wert' => '55', 'wert_num' => 55, 'datenlage' => 'geschaetzt', 'quelle' => 'Annahme'],
        ];
    }

    public function test_anlegen_mit_werten_traegt_datenlage_je_wert(): void
    {
        $anker = AnforderungsprofilFactory::objektAnker();
        $profil = $this->service()->anlegen($anker, 'Bestand', $this->beispielWerte(), erfasserId: null);

        $this->assertSame(1, $profil->version);
        $this->assertSame(Anforderungsprofil::STATUS_ENTWURF, $profil->status);
        $this->assertSame(LeadAlternativeAdd::class, $profil->verankerbar_type);
        $this->assertCount(2, $profil->werte);
        // Quellen-Pflicht: jeder Wert trägt eine erlaubte datenlage
        foreach ($profil->werte as $w) {
            $this->assertContains($w->datenlage, ['gemessen', 'berechnet', 'geschaetzt']);
        }
        $this->assertSame('kW', $profil->werte->firstWhere('schluessel', 'phi_hl_kw')->einheit);
    }

    public function test_verankerung_whitelist_lehnt_fremden_typ_ab(): void
    {
        $this->expectException(RuntimeException::class);
        // Employee ist ein existierendes Model, aber NICHT in ERLAUBTE_ANKER (customer/fremd = zu unspezifisch)
        (new Anforderungsprofil([
            'verankerbar_type' => Employee::class, 'verankerbar_id' => 1,
            'version' => 1, 'status' => 'entwurf',
        ]))->save();
    }

    public function test_verankerbar_id_muss_existieren(): void
    {
        $this->expectException(RuntimeException::class);
        // erlaubter Typ, aber die id existiert nicht -> exists-Check schlägt zu (DB erzwingt Morph-FK nicht)
        (new Anforderungsprofil([
            'verankerbar_type' => LeadAlternativeAdd::class, 'verankerbar_id' => 999999,
            'version' => 1, 'status' => 'entwurf',
        ]))->save();
    }

    public function test_wert_unbekannter_schluessel_wirft(): void
    {
        $profil = $this->service()->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', $this->beispielWerte());

        $this->expectException(RuntimeException::class);
        (new AnforderungsprofilWert([
            'anforderungsprofil_id' => $profil->id, 'schluessel' => 'quatsch_key',
            'wert' => '1', 'datenlage' => 'berechnet',
        ]))->save();
    }

    public function test_wert_ungueltige_datenlage_wirft(): void
    {
        $profil = $this->service()->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', $this->beispielWerte());

        $this->expectException(RuntimeException::class);
        (new AnforderungsprofilWert([
            'anforderungsprofil_id' => $profil->id, 'schluessel' => 'phi_hl_kw',
            'wert' => '9.6', 'wert_num' => 9.6, 'datenlage' => 'geraten',
        ]))->save();
    }

    public function test_wert_num_pflicht_wirft(): void
    {
        $profil = $this->service()->anlegen(AnforderungsprofilFactory::objektAnker(), 'x', $this->beispielWerte());

        $this->expectException(RuntimeException::class);
        // phi_hl_kw ist wert_num_pflicht in der Registry -> null muss scheitern
        (new AnforderungsprofilWert([
            'anforderungsprofil_id' => $profil->id, 'schluessel' => 'phi_hl_kw',
            'wert' => '9.6', 'wert_num' => null, 'datenlage' => 'berechnet',
        ]))->save();
    }

    public function test_versionieren_kopiert_werte_append_only(): void
    {
        $anker = AnforderungsprofilFactory::objektAnker();
        $v1 = $this->service()->anlegen($anker, 'Bestand', $this->beispielWerte());

        $v2 = $this->service()->neueVersion($v1, [
            ['schluessel' => 'phi_hl_kw', 'wert' => '8.0', 'wert_num' => 8.0, 'datenlage' => 'berechnet', 'quelle' => 'HeizlastRechner'],
        ]);

        // n+1, gleiche Verankerung, Werte kopiert + geändert
        $this->assertSame(2, $v2->version);
        $this->assertSame($v1->verankerbar_id, $v2->verankerbar_id);
        $this->assertCount(2, $v2->werte); // phi_hl_kw (geändert) + vorlauf_c (kopiert)
        $this->assertSame('8.0000', $v2->werte->firstWhere('schluessel', 'phi_hl_kw')->wert_num);
        $this->assertNotNull($v2->werte->firstWhere('schluessel', 'vorlauf_c'));

        // Basis unveränderlich + verkettet
        $v1->refresh();
        $this->assertSame(Anforderungsprofil::STATUS_ABGELOEST, $v1->status);
        $this->assertSame($v2->id, $v1->abgeloest_durch_id);
        $this->assertSame('9.6000', $v1->werte()->where('schluessel', 'phi_hl_kw')->first()->wert_num); // v1-Wert unangetastet
    }

    public function test_ein_aktiv_invariante_je_verankerung(): void
    {
        $anker = AnforderungsprofilFactory::objektAnker();
        $svc = $this->service();

        $p1 = $svc->anlegen($anker, 'A', $this->beispielWerte());
        $svc->aktivieren($p1);
        $p2 = $svc->anlegen($anker, 'B', $this->beispielWerte());
        $svc->aktivieren($p2);

        $aktiv = Anforderungsprofil::query()
            ->where('verankerbar_type', $anker->getMorphClass())
            ->where('verankerbar_id', $anker->getKey())
            ->aktiv()->count();

        $this->assertSame(1, $aktiv);
        $this->assertSame(Anforderungsprofil::STATUS_ABGELOEST, $p1->refresh()->status);
        $this->assertSame(Anforderungsprofil::STATUS_AKTIV, $p2->refresh()->status);
    }

    public function test_ein_aktiv_haelt_bei_doppelaufruf(): void
    {
        $anker = AnforderungsprofilFactory::objektAnker();
        $svc = $this->service();

        $p1 = $svc->anlegen($anker, 'A', $this->beispielWerte());
        $p2 = $svc->anlegen($anker, 'B', $this->beispielWerte());

        $svc->aktivieren($p1);
        $svc->aktivieren($p2);
        $svc->aktivieren($p2); // Doppel-Aufruf idempotent

        $aktiv = Anforderungsprofil::query()
            ->where('verankerbar_type', $anker->getMorphClass())
            ->where('verankerbar_id', $anker->getKey())
            ->aktiv()->count();

        $this->assertSame(1, $aktiv);
        $this->assertSame(Anforderungsprofil::STATUS_AKTIV, $p2->refresh()->status);
    }
}
