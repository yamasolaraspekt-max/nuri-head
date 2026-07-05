<?php

namespace App\Services\Anforderungsprofil;

use App\Models\Anforderungsprofil;
use App\Models\AnforderungsprofilWert;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * B2a-2 — Operationen am Anforderungsprofil: anlegen · aktivieren (Ein-aktiv-Invariante) ·
 * neueVersion (append-only, atomare Werte-Kopie). Registry-/Whitelist-Validierung sitzt in den Models
 * (greift also auch ohne diesen Service). Alle mutierenden Operationen laufen in einer Transaktion.
 */
class AnforderungsprofilService
{
    /**
     * Legt Version 1 (status=entwurf) an und schreibt die Werte (Registry-validiert).
     *
     * @param  array<int, array<string, mixed>>  $werte  je: schluessel, wert, [wert_num], [einheit], datenlage, [quelle], [erfassungsweg]
     */
    public function anlegen(Model $verankerbar, ?string $bezeichnung, array $werte, ?int $erfasserId = null): Anforderungsprofil
    {
        return DB::transaction(function () use ($verankerbar, $bezeichnung, $werte, $erfasserId) {
            $profil = new Anforderungsprofil([
                'verankerbar_type' => $verankerbar->getMorphClass(),
                'verankerbar_id' => $verankerbar->getKey(),
                'version' => 1,
                'status' => Anforderungsprofil::STATUS_ENTWURF,
                'bezeichnung' => $bezeichnung,
                'created_by' => $erfasserId,
            ]);
            $profil->save();
            $this->werteSchreiben($profil, $werte);

            return $profil->load('werte');
        });
    }

    /** Aktiviert genau dieses Profil; alle anderen aktiven derselben Verankerung werden abgelöst (Ein-aktiv). */
    public function aktivieren(Anforderungsprofil $profil): Anforderungsprofil
    {
        return DB::transaction(function () use ($profil) {
            Anforderungsprofil::query()
                ->where('verankerbar_type', $profil->verankerbar_type)
                ->where('verankerbar_id', $profil->verankerbar_id)
                ->whereKeyNot($profil->getKey())
                ->where('status', Anforderungsprofil::STATUS_AKTIV)
                ->update(['status' => Anforderungsprofil::STATUS_ABGELOEST]);

            $profil->status = Anforderungsprofil::STATUS_AKTIV;
            $profil->save();

            return $profil;
        });
    }

    /**
     * Version n+1 als Kopie (append-only): kopiert alle Werte atomar, version+1, Basis wird abgelöst
     * und verweist per abgeloest_durch_id auf die neue (Entwurf-)Version.
     *
     * @param  array<int, array<string, mixed>>  $geaenderteWerte  überschreibt/ergänzt je schluessel
     */
    public function neueVersion(Anforderungsprofil $basis, array $geaenderteWerte = [], ?int $erfasserId = null): Anforderungsprofil
    {
        return DB::transaction(function () use ($basis, $geaenderteWerte, $erfasserId) {
            $neu = new Anforderungsprofil([
                'verankerbar_type' => $basis->verankerbar_type,
                'verankerbar_id' => $basis->verankerbar_id,
                'version' => $basis->version + 1,
                'status' => Anforderungsprofil::STATUS_ENTWURF,
                'bezeichnung' => $basis->bezeichnung,
                'created_by' => $erfasserId ?? $basis->created_by,
                'gebaeude_geometrie' => $basis->gebaeude_geometrie, // B2a-3: Geometrie ist Teil der Version (Reproduzierbarkeit)
            ]);
            $neu->save();

            $werte = [];
            foreach ($basis->werte()->get() as $w) {
                $werte[$w->schluessel] = [
                    'schluessel' => $w->schluessel, 'wert' => $w->wert, 'wert_num' => $w->wert_num,
                    'einheit' => $w->einheit, 'datenlage' => $w->datenlage,
                    'quelle' => $w->quelle, 'erfassungsweg' => $w->erfassungsweg,
                ];
            }
            foreach ($geaenderteWerte as $g) {
                $werte[$g['schluessel']] = array_merge($werte[$g['schluessel']] ?? [], $g);
            }
            $this->werteSchreiben($neu, array_values($werte));

            $basis->status = Anforderungsprofil::STATUS_ABGELOEST;
            $basis->abgeloest_durch_id = $neu->getKey();
            $basis->save();

            return $neu->load('werte');
        });
    }

    /** @param  array<int, array<string, mixed>>  $werte */
    private function werteSchreiben(Anforderungsprofil $profil, array $werte): void
    {
        foreach ($werte as $w) {
            $einheit = $w['einheit']
                ?? (SchluesselRegistry::istErlaubt((string) ($w['schluessel'] ?? '')) ? SchluesselRegistry::definition($w['schluessel'])['einheit'] : null);

            $wert = new AnforderungsprofilWert([
                'schluessel' => $w['schluessel'],
                'wert' => (string) $w['wert'],
                'wert_num' => $w['wert_num'] ?? null,
                'einheit' => $einheit,
                'datenlage' => $w['datenlage'],
                'quelle' => $w['quelle'] ?? null,
                'erfassungsweg' => $w['erfassungsweg'] ?? null,
            ]);
            $wert->anforderungsprofil_id = $profil->getKey();
            $wert->save();
        }
    }
}
