<?php

namespace App\Http\Controllers\Customer\Deal;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Auftragseingang & Auftragsbestätigung — Welle A2 (2026-07-16).
 * Spec: docs/planner-spec-auftragseingang-ab.md. Generator-Durchgang.
 *
 * Auftragseingang = Lese-Fläche auf deals (Filter wie deal.all.list: SoftDeletes + Junk raus).
 * AB = append-only Snapshot in order_confirmations; die Kette wird NIE beschrieben.
 * Summen kommen aus offer_details (total_net/tax_rate/total_gross) — nicht neu gerechnet.
 */
class AuftragseingangController extends Controller
{
    public function index(Request $request)
    {
        $days = in_array((int) $request->get('days'), [7, 30, 90, 365], true) ? (int) $request->get('days') : 30;
        $seit = Carbon::today()->subDays($days);

        $deals = Deal::query()
            ->with(['customer:id,firma,name,lastname', 'folder:id,name,offer_id'])
            ->whereNotIn('status', ['Junk'])
            ->where('created_at', '>=', $seit)
            ->orderByDesc('created_at')
            ->limit(500)
            ->get();

        $abTabelle = Schema::hasTable('order_confirmations');
        $abs = $abTabelle
            ? DB::table('order_confirmations')->whereIn('deal_id', $deals->pluck('id'))->orderByDesc('id')->get()->groupBy('deal_id')
            : collect();

        $abHistorie = $abTabelle
            ? DB::table('order_confirmations')->orderByDesc('id')->limit(30)->get()
            : collect();

        $rows = $deals->map(function (Deal $deal) use ($abs) {
            $cust = $deal->customer;
            $kunde = trim((string) ($cust->firma ?? '')) !== ''
                ? $cust->firma
                : trim(($cust->name ?? '') . ' ' . ($cust->lastname ?? ''));
            $letzteAb = optional($abs->get($deal->id))->first();

            return [
                'deal'   => $deal,
                'kunde'  => $kunde !== '' ? $kunde : '— kein Kunde verknüpft —',
                'ab'     => $letzteAb,
            ];
        });

        return view('admin.deal.auftragseingang', [
            'rows'       => $rows,
            'days'       => $days,
            'abTabelle'  => $abTabelle,
            'abHistorie' => $abHistorie,
            'summe'      => $deals->sum(fn ($d) => (float) $d->price),
        ]);
    }

    /** AB erzeugen — EIN Insert in order_confirmations, kein Schreibzugriff auf die Kette. */
    public function abErzeugen(Request $request, int $dealId)
    {
        abort_unless(Schema::hasTable('order_confirmations'), 400, 'AB-Tabelle fehlt (php artisan migrate).');

        $deal = Deal::query()->with(['customer', 'folder.detail'])->whereNotIn('status', ['Junk'])->findOrFail($dealId);

        $cust = $deal->customer;
        $kunde = trim((string) ($cust->firma ?? '')) !== ''
            ? (string) $cust->firma
            : trim(($cust->name ?? '') . ' ' . ($cust->lastname ?? ''));

        $detail = $deal->folder->detail ?? null;
        $positions = $detail ? $this->positionenAusSnapshot($detail) : [];
        $ohneSnapshot = empty($positions);

        $id = DB::table('order_confirmations')->insertGetId([
            'deal_id'        => $deal->id,
            'ab_no'          => $deal->order_number ?: null,
            'recipient_name' => $kunde !== '' ? $kunde : '— kein Kunde verknüpft —',
            'positions'      => json_encode($positions),
            'total_net'      => $detail->total_net ?? null,
            'tax_rate'       => $detail->tax_rate ?? null,
            'total_gross'    => $detail->total_gross ?? null,
            'ohne_snapshot'  => $ohneSnapshot,
            'freitext'       => trim((string) $request->input('freitext', '')) ?: null,
            'created_by'     => Auth::id(),
            'created_at'     => now(), 'updated_at' => now(),
        ]);

        return redirect()->route('deal.auftragseingang.ab', $id);
    }

    /** Druckansicht — rendert ausschließlich den eingefrorenen AB-Stand. */
    public function ab(int $confirmationId)
    {
        $ab = DB::table('order_confirmations')->where('id', $confirmationId)->first();
        abort_if(!$ab, 404);

        $deal = Deal::with(['customer'])->find($ab->deal_id);

        DB::table('order_confirmations')->where('id', $confirmationId)->whereNull('printed_at')->update(['printed_at' => now()]);

        return view('admin.deal.ab_druck', [
            'ab'        => $ab,
            'deal'      => $deal,
            'positions' => json_decode($ab->positions ?? '[]', true) ?: [],
        ]);
    }

    /**
     * Positionsliste aus dem Angebots-Snapshot — tolerant wie InvoiceController::decodeOfferSections
     * (dieselben Aktiv-Filter), aber nur Anzeige-Felder (Titel, Menge, Einzelpreis, Zeilensumme).
     * Bevorzugt den eingefrorenen Snapshot (angebot_snapshot_sections), sonst sections.
     */
    private function positionenAusSnapshot($detail): array
    {
        $raw = $detail->angebot_snapshot_sections ?? $detail->sections ?? [];
        if (is_string($raw) && trim($raw) !== '') {
            $raw = json_decode($raw, true);
        }
        if (!is_array($raw)) {
            return [];
        }

        $rows = [];
        foreach ($raw as $section) {
            if (!is_array($section)) { continue; }
            foreach ((array) ($section['items'] ?? []) as $item) {
                if (!is_array($item)) { continue; }
                if (($item['active'] ?? true) === false) { continue; }
                if (($item['status'] ?? 'normal') === 'inactive') { continue; }

                $qty = (float) ($item['qty'] ?? 1);
                $price = (float) ($item['price'] ?? $item['unit_price'] ?? 0);
                $rows[] = [
                    'titel'  => trim(strip_tags((string) ($item['title'] ?? $item['name'] ?? 'Position'))),
                    'menge'  => $qty,
                    'einzel' => $price,
                    'summe'  => round($qty * $price, 2),
                ];
            }
        }

        return $rows;
    }
}
