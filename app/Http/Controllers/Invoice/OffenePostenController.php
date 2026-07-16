<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Offene Posten — Welle A1, Paket 1 (2026-07-16).
 *
 * REINE LESE-FLÄCHE auf der führenden invoices-Schiene (Umsatzdefinition:
 * docs/accounting/umsatzdefinition.md). Kein neues Schema, keine Schreiboperation,
 * keine FiBu-Abhängigkeit — offen = total_amount - paid_amount (Invoice::open_amount).
 * Entwürfe und stornierte/abgebrochene Rechnungen zählen nicht als Forderung.
 */
class OffenePostenController extends Controller
{
    /** Fälligkeitsraster in Tagen (Obergrenzen der Spalten). */
    private const RASTER = [30, 60, 90];

    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->with(['customer:id,name'])
            ->whereRaw("LOWER(COALESCE(status, '')) NOT IN ('draft', 'cancelled')")
            ->whereRaw('(COALESCE(total_amount, 0) - COALESCE(paid_amount, 0)) > 0.009')
            ->orderByRaw('due_date IS NULL, due_date ASC')
            ->get();

        $today = Carbon::today();

        $buckets = [
            'nicht_faellig' => ['label' => 'Nicht fällig',        'sum' => 0.0, 'count' => 0, 'tone' => 'info'],
            'b30'           => ['label' => '1–30 Tage',           'sum' => 0.0, 'count' => 0, 'tone' => 'warning'],
            'b60'           => ['label' => '31–60 Tage',          'sum' => 0.0, 'count' => 0, 'tone' => 'warning'],
            'b90'           => ['label' => '61–90 Tage',          'sum' => 0.0, 'count' => 0, 'tone' => 'danger'],
            'ueber90'       => ['label' => 'Über 90 Tage',        'sum' => 0.0, 'count' => 0, 'tone' => 'danger'],
            'ohne_ziel'     => ['label' => 'Ohne Zahlungsziel',   'sum' => 0.0, 'count' => 0, 'tone' => 'info'],
        ];

        $rows = [];
        $sumOpen = 0.0;

        foreach ($invoices as $invoice) {
            $open = $invoice->open_amount;

            // Überfälligkeit: >0 = Tage überfällig, <=0 = noch nicht fällig, null = kein Zahlungsziel gesetzt.
            // Operanden-Gate: ein fehlendes due_date wird NICHT erfunden, sondern als eigene Spalte gezeigt.
            $daysOverdue = null;
            if ($invoice->due_date) {
                $daysOverdue = Carbon::parse($invoice->due_date)->startOfDay()->diffInDays($today, false);
            }

            $bucketKey = 'ohne_ziel';
            if ($daysOverdue !== null) {
                if ($daysOverdue <= 0) {
                    $bucketKey = 'nicht_faellig';
                } elseif ($daysOverdue <= self::RASTER[0]) {
                    $bucketKey = 'b30';
                } elseif ($daysOverdue <= self::RASTER[1]) {
                    $bucketKey = 'b60';
                } elseif ($daysOverdue <= self::RASTER[2]) {
                    $bucketKey = 'b90';
                } else {
                    $bucketKey = 'ueber90';
                }
            }

            $buckets[$bucketKey]['sum'] += $open;
            $buckets[$bucketKey]['count']++;
            $sumOpen += $open;

            $rows[] = [
                'invoice'      => $invoice,
                'open'         => $open,
                'days_overdue' => $daysOverdue,
                'bucket'       => $bucketKey,
            ];
        }

        // Überfälligste zuerst, dann bald fällige, „ohne Zahlungsziel" ans Ende.
        usort($rows, function (array $a, array $b) {
            if (($a['days_overdue'] === null) !== ($b['days_overdue'] === null)) {
                return $a['days_overdue'] === null ? 1 : -1;
            }
            return ($b['days_overdue'] ?? 0) <=> ($a['days_overdue'] ?? 0);
        });

        return view('admin.invoices.offene_posten', [
            'rows'    => $rows,
            'buckets' => $buckets,
            'sumOpen' => $sumOpen,
        ]);
    }
}
