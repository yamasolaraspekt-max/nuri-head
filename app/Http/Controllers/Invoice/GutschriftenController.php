<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

/**
 * Gutschriften — Welle B1, Paket 2 (2026-07-16).
 *
 * REINE LESE-FLÄCHE auf invoices: alle Gutschriften und Stornorechnungen
 * (Invoice::TYPEN_OHNE_ZAHLUNG, Bestand teils kapitalisiert ⇒ Vergleich case-insensitiv).
 * Erstellt wird eine Gutschrift/Storno weiterhin am Rechnungs-Workflow (S1-04) —
 * diese Fläche ist das Register dazu. Ehrlichkeits-Grenze: das Schema kennt KEINE
 * Verknüpfung zur Ursprungsrechnung; es wird deshalb keine „Bezug"-Spalte erfunden.
 * Kein neues Schema, keine Schreiboperation.
 */
class GutschriftenController extends Controller
{
    private const PER_PAGE = 50;

    public function index(Request $request)
    {
        $jahr = $request->input('jahr', (string) now()->year); // 'alle' = ohne Zeitraum
        $jahrInt = ctype_digit((string) $jahr) ? max(2000, min(2100, (int) $jahr)) : null;

        $ohneZahlungSql = "'" . implode("','", Invoice::TYPEN_OHNE_ZAHLUNG) . "'";

        $basis = Invoice::query()
            ->whereRaw("LOWER(TRIM(COALESCE(type, ''))) IN ($ohneZahlungSql)");

        if ($jahrInt !== null) {
            $basis->where('issue_date', '>=', sprintf('%04d-01-01', $jahrInt))
                ->where('issue_date', '<', sprintf('%04d-01-01', $jahrInt + 1));
        }

        // Summen je Typ als EINE Gruppen-Query.
        $jeTyp = (clone $basis)
            ->selectRaw("LOWER(TRIM(COALESCE(type, ''))) AS typ, COUNT(*) AS anzahl, SUM(COALESCE(total_amount, 0)) AS summe")
            ->groupBy('typ')
            ->get()
            ->keyBy('typ');

        $gutschriften = (clone $basis)
            ->select(['id', 'invoice_no', 'type', 'status', 'issue_date', 'total_amount', 'customer_id'])
            ->with(['customer:id,name,lastname,firma'])
            ->orderByDesc('issue_date')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        $grenzen = Invoice::query()
            ->whereRaw("LOWER(TRIM(COALESCE(type, ''))) IN ($ohneZahlungSql)")
            ->whereNotNull('issue_date')
            ->selectRaw('YEAR(MIN(issue_date)) AS min_jahr, YEAR(MAX(issue_date)) AS max_jahr')->first();
        $jahre = $grenzen->max_jahr !== null
            ? range((int) $grenzen->max_jahr, (int) $grenzen->min_jahr)
            : [now()->year];

        return view('admin.invoices.gutschriften', [
            'gutschriften' => $gutschriften,
            'jeTyp' => $jeTyp,
            'jahr' => $jahrInt !== null ? (string) $jahrInt : 'alle',
            'jahre' => $jahre,
        ]);
    }
}
