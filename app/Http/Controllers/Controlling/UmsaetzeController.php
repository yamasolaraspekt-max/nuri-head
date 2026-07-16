<?php

namespace App\Http\Controllers\Controlling;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * Umsätze — Welle B1, Paket 3 (2026-07-16). Erste Controlling-Fläche.
 *
 * REINE LESE-FLÄCHE: Monatsraster des gewählten Jahres aus der EINZIGEN Umsatz-Wahrheit
 * invoices (DAUERDIREKTIVE): Umsatz = ausgestellte echte Rechnungen (sent/paid/overdue,
 * Typ nicht Gutschrift/Storno) nach issue_date. Vorjahres-Spalte zum Vergleich.
 *
 * BEWUSSTE GRENZE (Operanden-Gate): Gutschriften laufen NACHRICHTLICH in eigener Spalte
 * mit und werden NICHT still verrechnet — das Vorzeichen der Gutschrift-Beträge im
 * Altbestand ist ungeprüft; eine Verrechnungs-Regel braucht erst Yamas Daten-Entscheid.
 * 4 Aggregat-Queries pro Aufruf (Index issue_date/status/type vorhanden), kein Cache nötig.
 */
class UmsaetzeController extends Controller
{
    public function index(Request $request)
    {
        $jahr = (int) $request->input('jahr', now()->year);
        $jahr = max(2000, min(2100, $jahr));

        $ohneZahlungSql = "'" . implode("','", Invoice::TYPEN_OHNE_ZAHLUNG) . "'";
        $istEchteRechnung = "LOWER(TRIM(COALESCE(type, ''))) NOT IN ($ohneZahlungSql)";
        $istAusgestellt = "LOWER(COALESCE(status, '')) IN ('sent', 'paid', 'overdue')";

        $monatsWerte = fn (int $j) => Invoice::query()
            ->whereRaw($istEchteRechnung)
            ->whereRaw($istAusgestellt)
            ->where('issue_date', '>=', sprintf('%04d-01-01', $j))
            ->where('issue_date', '<', sprintf('%04d-01-01', $j + 1))
            ->selectRaw("
                MONTH(issue_date) AS monat,
                COUNT(*) AS anzahl,
                SUM(COALESCE(total_amount, 0)) AS summe,
                SUM(CASE WHEN LOWER(COALESCE(status, '')) = 'paid' THEN COALESCE(total_amount, 0) ELSE 0 END) AS bezahlt
            ")
            ->groupBy('monat')
            ->get()
            ->keyBy('monat');

        $aktuell = $monatsWerte($jahr);
        $vorjahr = $monatsWerte($jahr - 1);

        // Gutschriften/Storni nachrichtlich je Monat (eigene Query, eigene Spalte — keine Verrechnung).
        $gutschriften = Invoice::query()
            ->whereRaw("LOWER(TRIM(COALESCE(type, ''))) IN ($ohneZahlungSql)")
            ->where('issue_date', '>=', sprintf('%04d-01-01', $jahr))
            ->where('issue_date', '<', sprintf('%04d-01-01', $jahr + 1))
            ->selectRaw('MONTH(issue_date) AS monat, COUNT(*) AS anzahl, SUM(COALESCE(total_amount, 0)) AS summe')
            ->groupBy('monat')
            ->get()
            ->keyBy('monat');

        $monate = [];
        for ($m = 1; $m <= 12; $m++) {
            $a = $aktuell->get($m);
            $v = $vorjahr->get($m);
            $g = $gutschriften->get($m);
            $summe = (float) ($a->summe ?? 0);
            $summeVorjahr = (float) ($v->summe ?? 0);
            $monate[$m] = [
                'anzahl' => (int) ($a->anzahl ?? 0),
                'summe' => $summe,
                'bezahlt' => (float) ($a->bezahlt ?? 0),
                'vorjahr' => $summeVorjahr,
                'delta_prozent' => $summeVorjahr > 0 ? (($summe - $summeVorjahr) / $summeVorjahr) * 100 : null,
                'gutschriften_anzahl' => (int) ($g->anzahl ?? 0),
                'gutschriften_summe' => (float) ($g->summe ?? 0),
            ];
        }

        $grenzen = Invoice::query()->whereNotNull('issue_date')
            ->selectRaw('YEAR(MIN(issue_date)) AS min_jahr, YEAR(MAX(issue_date)) AS max_jahr')->first();
        $jahre = range((int) ($grenzen->max_jahr ?? now()->year), (int) ($grenzen->min_jahr ?? now()->year));

        $summeJahr = array_sum(array_column($monate, 'summe'));
        $summeVorjahr = array_sum(array_column($monate, 'vorjahr'));

        return view('admin.controlling.umsaetze', [
            'monate' => $monate,
            'jahr' => $jahr,
            'jahre' => $jahre,
            'kpi' => [
                'summe' => $summeJahr,
                'bezahlt' => array_sum(array_column($monate, 'bezahlt')),
                'anzahl' => array_sum(array_column($monate, 'anzahl')),
                'vorjahr' => $summeVorjahr,
                'delta_prozent' => $summeVorjahr > 0 ? (($summeJahr - $summeVorjahr) / $summeVorjahr) * 100 : null,
                'gutschriften_summe' => array_sum(array_column($monate, 'gutschriften_summe')),
            ],
        ]);
    }
}
