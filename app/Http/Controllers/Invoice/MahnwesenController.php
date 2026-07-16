<?php

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Mahnwesen — Welle A1, Paket 2 (2026-07-16). Muster: hausverwaltung DunningController.
 *
 * Automatisierungs-Prinzip (CLAUDE.md): Mahnen ist eine Fach-/Rechtsentscheidung →
 * das System schlägt vor (Kandidaten + Stufe), der Mensch bestätigt je Lauf.
 * Es wird NIE automatisch gemahnt. Regeln: config/mahnwesen.php (EINE Wahrheit).
 * Schreibt AUSSCHLIESSLICH in die eigenen dunning_*-Tabellen (Dauerdirektive),
 * append-only: kein Edit, Korrektur = neuer Lauf.
 */
class MahnwesenController extends Controller
{
    public function index(Request $request)
    {
        if (!Schema::hasTable('dunning_items')) {
            return view('admin.invoices.mahnwesen', [
                'migrationFehlt' => true,
                'kandidaten' => [], 'gesperrt' => [], 'runs' => collect(), 'stufen' => config('mahnwesen.stufen'),
            ]);
        }

        [$kandidaten, $gesperrt] = $this->ermittleKandidaten();

        $runs = DB::table('dunning_runs')
            ->orderByDesc('run_date')->orderByDesc('id')
            ->limit(20)
            ->get()
            ->map(function ($run) {
                $run->items = DB::table('dunning_items')->where('dunning_run_id', $run->id)->get();
                return $run;
            });

        return view('admin.invoices.mahnwesen', [
            'migrationFehlt' => false,
            'kandidaten' => $kandidaten,
            'gesperrt'   => $gesperrt,
            'runs'       => $runs,
            'stufen'     => config('mahnwesen.stufen'),
        ]);
    }

    /** Mahnlauf ausführen — nur für die vom Menschen bestätigten Rechnungen. */
    public function execute(Request $request)
    {
        abort_unless(Schema::hasTable('dunning_items'), 400, 'Mahn-Tabellen fehlen (Migration ausführen).');

        $ids = array_map('intval', (array) $request->input('invoice_ids', []));
        if (empty($ids)) {
            return redirect()->route('admin.invoices.mahnwesen')->with('error', 'Keine Rechnung ausgewählt.');
        }

        // Stufe/Beträge werden SERVERSEITIG neu ermittelt (nie dem Formular vertrauen).
        [$kandidaten] = $this->ermittleKandidaten();
        $auswahl = array_values(array_filter($kandidaten, fn ($k) => in_array($k['invoice']->id, $ids, true)));

        if (empty($auswahl)) {
            return redirect()->route('admin.invoices.mahnwesen')->with('error', 'Auswahl enthält keine gültigen Mahn-Kandidaten (Stand neu geprüft).');
        }

        $stufen = config('mahnwesen.stufen');
        $fees = [];
        foreach ($stufen as $lvl => $s) { $fees[(string) $lvl] = $s['gebuehr']; }

        DB::transaction(function () use ($auswahl, $stufen, $fees) {
            $today = Carbon::today();

            $runId = DB::table('dunning_runs')->insertGetId([
                'run_date' => $today->toDateString(),
                'fees' => json_encode($fees),
                'items_count' => 0,
                'total_amount' => 0,
                'executed_by' => Auth::id(),
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $sum = 0.0; $count = 0;
            foreach ($auswahl as $k) {
                $stufe = $stufen[$k['next_level']];
                $fee = (float) $stufe['gebuehr'];
                $totalDue = round($k['open'] + $fee, 2);

                DB::table('dunning_items')->insert([
                    'dunning_run_id' => $runId,
                    'invoice_id' => $k['invoice']->id,
                    'customer_id' => $k['invoice']->customer_id,
                    'recipient_name' => $k['recipient'],
                    'level' => $k['next_level'],
                    'days_overdue' => $k['days_overdue'],
                    'open_amount' => $k['open'],
                    'fee' => $fee,
                    'interest' => 0,
                    'total_due' => $totalDue,
                    'pay_until' => $today->copy()->addDays((int) $stufe['frist_tage'])->toDateString(),
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                $sum += $totalDue; $count++;
            }

            DB::table('dunning_runs')->where('id', $runId)->update([
                'items_count' => $count, 'total_amount' => round($sum, 2), 'updated_at' => now(),
            ]);
        });

        return redirect()->route('admin.invoices.mahnwesen')->with('success', count($auswahl) . ' Mahnschreiben erzeugt — bitte drucken/versenden.');
    }

    /** Druckansicht eines Mahnschreibens. */
    public function brief(int $itemId)
    {
        $item = DB::table('dunning_items')->where('id', $itemId)->first();
        abort_if(!$item, 404);

        $invoice = Invoice::with('customer')->find($item->invoice_id);
        $stufe = config('mahnwesen.stufen')[$item->level] ?? ['titel' => 'Mahnung', 'frist_tage' => 14];

        DB::table('dunning_items')->where('id', $itemId)->whereNull('printed_at')->update(['printed_at' => now()]);

        return view('admin.invoices.mahnbrief', [
            'item' => $item,
            'invoice' => $invoice,
            'stufe' => $stufe,
        ]);
    }

    /**
     * Kandidaten nach den Standard-Regeln (config/mahnwesen.php):
     * offen > 0, fällig + Karenz überschritten, Mindestabstand zur letzten Stufe gewahrt.
     * Rechnungen OHNE due_date werden NICHT gemahnt (Operanden-Gate: keine erfundene Frist)
     * — sie erscheinen auf der OP-Fläche als „Ohne Zahlungsziel".
     * Rückgabe: [kandidaten, gesperrt] — gesperrt = Stufe 3 erreicht (Inkasso prüfen) oder Abstand läuft.
     */
    private function ermittleKandidaten(): array
    {
        $karenz = (int) config('mahnwesen.karenz_tage', 7);
        $abstand = (int) config('mahnwesen.stufen_abstand_tage', 14);
        $maxStufe = max(array_keys(config('mahnwesen.stufen')));
        $today = Carbon::today();

        $invoices = Invoice::query()
            ->with(['customer:id,firma,name,lastname,street,postcode,city'])
            ->whereRaw("LOWER(COALESCE(status, '')) NOT IN ('draft', 'cancelled')")
            ->whereRaw('(COALESCE(total_amount, 0) - COALESCE(paid_amount, 0)) > 0.009')
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $today->copy()->subDays($karenz))
            ->orderBy('due_date')
            ->get();

        $letzte = DB::table('dunning_items')
            ->join('dunning_runs', 'dunning_runs.id', '=', 'dunning_items.dunning_run_id')
            ->whereIn('dunning_items.invoice_id', $invoices->pluck('id'))
            ->select('dunning_items.invoice_id', 'dunning_items.level', 'dunning_runs.run_date')
            ->orderBy('dunning_items.level')
            ->get()
            ->groupBy('invoice_id')
            ->map(fn ($items) => $items->sortByDesc('level')->first());

        $kandidaten = []; $gesperrt = [];

        foreach ($invoices as $invoice) {
            $open = $invoice->open_amount;
            $daysOverdue = (int) Carbon::parse($invoice->due_date)->startOfDay()->diffInDays($today, false);
            $last = $letzte->get($invoice->id);
            $cust = $invoice->customer;
            $recipient = trim(($cust->firma ?? '') !== '' && ($cust->firma ?? null)
                ? $cust->firma
                : trim(($cust->name ?? '') . ' ' . ($cust->lastname ?? ''))) ?: '— kein Kunde verknüpft —';

            $basis = [
                'invoice' => $invoice, 'open' => $open, 'days_overdue' => max(0, $daysOverdue),
                'recipient' => $recipient, 'last_level' => $last->level ?? null, 'last_date' => $last->run_date ?? null,
            ];

            if ($last && (int) $last->level >= $maxStufe) {
                $gesperrt[] = $basis + ['grund' => 'Letzte Mahnstufe erreicht — Inkasso/gerichtliches Mahnverfahren prüfen'];
                continue;
            }
            if ($last && Carbon::parse($last->run_date)->addDays($abstand)->greaterThan($today)) {
                $gesperrt[] = $basis + ['grund' => 'Frist der letzten Stufe läuft noch (Abstand ' . $abstand . ' Tage)'];
                continue;
            }

            $kandidaten[] = $basis + ['next_level' => $last ? ((int) $last->level + 1) : 1];
        }

        return [$kandidaten, $gesperrt];
    }
}
