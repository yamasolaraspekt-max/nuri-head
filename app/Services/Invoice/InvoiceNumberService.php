<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

/**
 * S1-01 — Die EINZIGE führende Vergabestelle für Rechnungsnummern.
 *
 * Regeln:
 *  - Nummer entsteht ausschließlich für ausgestellte Rechnungen (Invoice::ISSUED_STATUSES).
 *  - Drafts erhalten KEINE Nummer.
 *  - Vergabe ist transaktional + zeilengesperrt (lockForUpdate auf die Sequenzzeile)
 *    -> keine Doubletten bei parallelen Requests.
 *  - Idempotent: eine bereits vergebene Nummer wird nie überschrieben.
 *  - Format: RE-yy#### (yy = 2-stelliges Ausstellungsjahr, 4-stellige laufende Nummer).
 *  - Startwert wird lazy aus dem Bestand (nur Muster RE-yy####) abgeleitet; Alt-/Fremdformate
 *    (z. B. RE-2026-#####, TST-…) werden ignoriert und bleiben unverändert.
 *
 * `reserve()` setzt nur das Attribut `invoice_no` (kein $invoice->save()), damit es
 * gefahrlos aus dem Model-`saving`-Hook heraus aufgerufen werden kann (keine Rekursion).
 */
class InvoiceNumberService
{
    public const TYPE = 'invoice';
    public const PREFIX = 'RE-';

    /**
     * Reserviert (falls nötig) eine Nummer und setzt sie am Invoice-Attribut.
     * Gibt die (neue oder bestehende) Nummer zurück, oder null, wenn keine Vergabe erfolgt
     * (Draft / nicht ausgestellt).
     */
    public function reserve(Invoice $invoice): ?string
    {
        // Idempotenz: nie eine vorhandene Nummer verändern.
        if (!empty($invoice->invoice_no)) {
            return $invoice->invoice_no;
        }

        // Nur ausgestellte Rechnungen erhalten eine Nummer (D1-A: sent/paid/overdue).
        if (!$invoice->isIssuedStatus($invoice->status)) {
            return null;
        }

        $yy = now()->format('y');
        $year = (int) now()->format('Y');

        return DB::transaction(function () use ($invoice, $yy, $year) {
            // Sequenzzeile sicherstellen (lazy seed aus Bestand). insertOrIgnore verhindert
            // Race beim Erstanlegen; unique(type, year) garantiert Einzigartigkeit.
            if (!DB::table('invoice_number_ranges')->where('type', self::TYPE)->where('year', $year)->exists()) {
                DB::table('invoice_number_ranges')->insertOrIgnore([
                    'type' => self::TYPE,
                    'prefix' => self::PREFIX,
                    'year' => $year,
                    'current_number' => $this->deriveSeed($yy),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $range = DB::table('invoice_number_ranges')
                ->where('type', self::TYPE)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();

            $next = ((int) $range->current_number) + 1;
            $number = self::PREFIX . $yy . str_pad((string) $next, 4, '0', STR_PAD_LEFT);

            DB::table('invoice_number_ranges')->where('id', $range->id)->update([
                'current_number' => $next,
                'updated_by' => $this->actorId(),
                'updated_at' => now(),
            ]);

            $invoice->invoice_no = $number;

            return $number;
        });
    }

    /**
     * Startwert je Jahr = höchste bereits vergebene Nummer im Muster RE-yy####.
     * Fremdformate (RE-2026-#####, TST-…) matchen nicht und werden ignoriert.
     */
    private function deriveSeed(string $yy): int
    {
        $max = 0;
        $pattern = '/^' . preg_quote(self::PREFIX . $yy, '/') . '(\d{4,})$/';

        DB::table('invoices')
            ->where('invoice_no', 'like', self::PREFIX . $yy . '%')
            ->pluck('invoice_no')
            ->each(function ($no) use (&$max, $pattern) {
                if (preg_match($pattern, (string) $no, $m)) {
                    $max = max($max, (int) $m[1]);
                }
            });

        return $max;
    }

    private function actorId(): ?int
    {
        $user = auth()->user();

        return $user?->employee_id ?? $user?->id ?? null;
    }
}
