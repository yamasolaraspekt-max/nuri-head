<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * Ausgangsrechnungen — Welle B1, Paket 1 (2026-07-16).
 *
 * REINE LESE-FLÄCHE: das kaufmännische REGISTER der ausgestellten Rechnungen je Zeitraum
 * (Hauptjob: Nachweis/Kontrolle je Monat/Jahr mit Summen). Bewusst KEINE zweite operative
 * Rechnungsliste — Arbeiten an Rechnungen bleibt auf admin.invoices.index; jede Zeile
 * verlinkt dorthin. Entwürfe sind kein Ausgang (draft raus); stornierte bleiben sichtbar
 * gekennzeichnet (Register-Vollständigkeit), zählen aber nicht in die Rechnungssumme.
 * Gutschriften/Stornorechnungen laufen nachrichtlich mit (keine stille Verrechnung).
 * invoices bleibt die einzige Umsatz-Wahrheit; kein neues Schema, keine Schreiboperation.
 */
class AusgangsrechnungenController extends Controller
{
    private const PER_PAGE = 50;

    public function index(Request $request)
    {
        $jahr = (int) $request->input('jahr', now()->year);
        $jahr = max(2000, min(2100, $jahr));
        $monat = $request->filled('monat') ? max(1, min(12, (int) $request->input('monat'))) : null;

        // Zeitraum als issue_date-Range (Index-freundlich, kein YEAR() auf der Spalte).
        $von = $monat !== null
            ? sprintf('%04d-%02d-01', $jahr, $monat)
            : sprintf('%04d-01-01', $jahr);
        $bis = $monat !== null
            ? date('Y-m-d', strtotime($von . ' +1 month'))
            : sprintf('%04d-01-01', $jahr + 1);

        $ohneZahlungSql = "'" . implode("','", Invoice::TYPEN_OHNE_ZAHLUNG) . "'";
        $istEchteRechnung = "LOWER(TRIM(COALESCE(type, ''))) NOT IN ($ohneZahlungSql)";
        $istAusgestellt = "LOWER(COALESCE(status, '')) IN ('sent', 'paid', 'overdue')";

        $basis = Invoice::query()
            ->whereRaw("LOWER(COALESCE(status, '')) <> 'draft'")
            ->where('issue_date', '>=', $von)
            ->where('issue_date', '<', $bis);

        // Summen als EINE Aggregat-Query (nicht über die Collection der Seite).
        $agg = (clone $basis)->selectRaw("
            COUNT(*) AS anzahl,
            SUM(CASE WHEN $istEchteRechnung AND $istAusgestellt THEN COALESCE(total_amount, 0) ELSE 0 END) AS summe_rechnungen,
            SUM(CASE WHEN $istEchteRechnung AND $istAusgestellt THEN COALESCE(total_amount, 0) - COALESCE(paid_amount, 0) ELSE 0 END) AS summe_offen,
            SUM(CASE WHEN $istEchteRechnung AND $istAusgestellt THEN 1 ELSE 0 END) AS anzahl_rechnungen,
            SUM(CASE WHEN NOT ($istEchteRechnung) THEN COALESCE(total_amount, 0) ELSE 0 END) AS summe_gutschriften,
            SUM(CASE WHEN NOT ($istEchteRechnung) THEN 1 ELSE 0 END) AS anzahl_gutschriften,
            SUM(CASE WHEN LOWER(COALESCE(status, '')) = 'cancelled' THEN 1 ELSE 0 END) AS anzahl_storniert
        ")->first();

        $invoices = (clone $basis)
            ->select(['id', 'invoice_no', 'type', 'status', 'issue_date', 'due_date', 'total_amount', 'paid_amount', 'customer_id'])
            ->with(['customer:id,name,lastname,firma'])
            ->orderBy('issue_date')
            ->orderBy('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // Jahres-Auswahl aus dem Bestand (eine Min/Max-Query statt DISTINCT über alles).
        $grenzen = Invoice::query()->whereNotNull('issue_date')
            ->selectRaw('YEAR(MIN(issue_date)) AS min_jahr, YEAR(MAX(issue_date)) AS max_jahr')->first();
        $jahre = range((int) ($grenzen->max_jahr ?? now()->year), (int) ($grenzen->min_jahr ?? now()->year));

        return view('admin.invoices.ausgangsrechnungen', [
            'invoices' => $invoices,
            'agg' => $agg,
            'jahr' => $jahr,
            'monat' => $monat,
            'jahre' => $jahre,
            'typenOhneZahlung' => Invoice::TYPEN_OHNE_ZAHLUNG,
        ]);
    }
}
