<?php

namespace App\Services\Anforderungsprofil;

use App\Models\Anforderungsprofil;
use App\Models\AnforderungsprofilWert;
use App\Services\Klima\KlimaPlzService;
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
            $this->werteSchreiben($profil, $this->werteErgaenzen($werte));

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
            // Stufe 3b P0 (Auflage B) — optimistische Nebenläufigkeits-Kontrolle der LINEAREN Versionskette:
            // die Zeilen dieser Verankerung innerhalb der Transaktion exklusiv sperren und den maßgeblichen
            // Ketten-Kopf frisch lesen. Zwei gleichzeitige neueVersion()-Aufrufe serialisieren am Lock;
            // der zweite sieht dann eine höhere Version als seine Basis → seine Basis ist STALE.
            // Zielverhalten (Yama/Planner): stale wird als Konflikt ABGELEHNT, NICHT als Folgeversion aus
            // alten Daten angehängt (kein automatisches Rebase/Branching). So bleibt der erste erfolgreiche
            // Save vollständig erhalten und es entsteht keine zweite Wahrheit.
            $gesperrt = Anforderungsprofil::query()
                ->where('verankerbar_type', $basis->verankerbar_type)
                ->where('verankerbar_id', $basis->verankerbar_id)
                ->lockForUpdate()
                ->get();
            $maxVersion = (int) $gesperrt->max('version');

            if ((int) $basis->version < $maxVersion) {
                // Eine neuere Version existiert bereits (Kopf verschoben) → übergebene Basis ist veraltet.
                throw new StaleProfilVersionException((int) $basis->version, $maxVersion);
            }

            // Basis ist der aktuelle Kopf; frisch gesperrte Zeile ist die Kopierquelle.
            $quelle = $gesperrt->firstWhere('id', $basis->getKey()) ?? $basis;

            $neu = new Anforderungsprofil([
                'verankerbar_type' => $quelle->verankerbar_type,
                'verankerbar_id' => $quelle->verankerbar_id,
                'version' => $maxVersion + 1,
                'status' => Anforderungsprofil::STATUS_ENTWURF,
                'bezeichnung' => $quelle->bezeichnung,
                'created_by' => $erfasserId ?? $quelle->created_by,
                'gebaeude_geometrie' => $quelle->gebaeude_geometrie, // B2a-3: Geometrie ist Teil der Version (Reproduzierbarkeit)
            ]);
            $neu->save();

            $werte = [];
            foreach ($quelle->werte()->get() as $w) {
                $werte[$w->schluessel] = [
                    'schluessel' => $w->schluessel, 'wert' => $w->wert, 'wert_num' => $w->wert_num,
                    'einheit' => $w->einheit, 'datenlage' => $w->datenlage,
                    'quelle' => $w->quelle, 'erfassungsweg' => $w->erfassungsweg,
                ];
            }
            foreach ($geaenderteWerte as $g) {
                $werte[$g['schluessel']] = array_merge($werte[$g['schluessel']] ?? [], $g);
            }
            $this->werteSchreiben($neu, $this->werteErgaenzen(array_values($werte)));

            $quelle->status = Anforderungsprofil::STATUS_ABGELOEST;
            $quelle->abgeloest_durch_id = $neu->getKey();
            $quelle->save();

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

    /** Ergänzt fehlende Werte aus Hilfsdiensten, z. B. Klima lookup per PLZ. */
    private function werteErgaenzen(array $werte): array
    {
        $hatNormAussentemp = false;
        $standortPlz = null;

        foreach ($werte as $wert) {
            if (($wert['schluessel'] ?? '') === 'norm_aussentemp_c') {
                $hatNormAussentemp = true;
            }
            if (($wert['schluessel'] ?? '') === 'standort_plz') {
                $standortPlz = trim((string) ($wert['wert'] ?? ''));
            }
        }

        if ($hatNormAussentemp || $standortPlz === null || $standortPlz === '') {
            return $werte;
        }

        $klimaWert = (new KlimaPlzService)->getNormAussentempForPlz($standortPlz);
        if ($klimaWert === null) {
            return $werte;
        }

        $werte[] = [
            'schluessel' => 'norm_aussentemp_c',
            'wert' => (string) $klimaWert,
            'wert_num' => $klimaWert,
            'datenlage' => 'berechnet',
            'quelle' => sprintf('klima_plz %s', $standortPlz),
            'erfassungsweg' => 'import',
        ];

        return $werte;
    }
}
