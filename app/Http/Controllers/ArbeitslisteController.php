<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Invoice;
use App\Models\PersonalTask;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

/**
 * Arbeitsliste — Inbox „Was braucht mich jetzt?".
 *
 * Verwandelt die bestehenden Backend-Ableitungen in sichtbare To-dos (server-gerendert, SSR):
 *  1) Ueberfaellige Rechnungen  (A5 unpaid + A3 due_date, Invoice-Scopes = EINE Wahrheit)
 *  2) Offene Angebots-Aufgaben  (A1 Follow-ups auf personal_tasks, type=follow_up)
 *  3) Auftraege ohne Rechnung   (deals status='deal' ohne verknuepfte invoices)
 *
 * Jede Kategorie ist eigenstaendig defensiv: fehlt eine Tabelle/Spalte oder wirft eine Query,
 * traegt der Controller einen Fehler-Zustand an die View statt 500 (try/catch je Kategorie).
 * N+1 ist durch Eager-Loading (with('customer')) je Kategorie vermieden.
 */
class ArbeitslisteController extends Controller
{
    /** Limit je Kategorie — die Inbox bleibt scanbar, kein Endlos-Scroll. */
    private const LIMIT = 50;

    public function index(): View
    {
        $overdueInvoices = $this->category(fn () => $this->overdueInvoices());
        $followUpTasks   = $this->category(fn () => $this->followUpTasks());
        $dealsNoInvoice  = $this->category(fn () => $this->dealsWithoutInvoice());

        $categories = [$overdueInvoices, $followUpTasks, $dealsNoInvoice];

        // Jede Sektion traegt ihren EIGENEN Zustand (data|empty|error, siehe _section.blade.php).
        // Der globale $viewState wird daraus ABGELEITET und rollt nur den GESAMT-Fall auf — ein
        // Teilausfall kippt NICHT die ganze Seite (graceful degradation):
        //   error -> ALLE Kategorien gescheitert (Gesamtausfall -> ein globaler role="alert").
        //   empty -> alle ok UND alle leer (ein globaler, positiver role="status").
        //   data  -> sonst: jede Sektion rendert ihren eigenen Zustand (befuellt/leer/Fehler),
        //            ein einzelner Kategorie-Fehler bleibt auf seine Sektion begrenzt.
        // 'loading' existiert im Enum fuer kuenftige Async-Nutzung; SSR liefert stets data/empty/error.
        $allErrored = collect($categories)->every(fn ($c) => $c['ok'] === false);
        $anyErrored = collect($categories)->contains(fn ($c) => $c['ok'] === false);
        $anyItems   = collect($categories)->contains(fn ($c) => ! empty($c['items']));
        $viewState = $allErrored ? 'error' : ((! $anyErrored && ! $anyItems) ? 'empty' : 'data');

        return view('admin.arbeitsliste.index', [
            'viewState'       => $viewState,
            'overdueInvoices' => $overdueInvoices,
            'followUpTasks'   => $followUpTasks,
            'dealsNoInvoice'  => $dealsNoInvoice,
        ]);
    }

    /**
     * Fuehrt eine Kategorie-Query aus und kapselt sie: bei Erfolg ['ok'=>true,'items'=>[...]],
     * bei Fehler ['ok'=>false,'error'=>Meldung] — nie eine Exception nach aussen (kein 500).
     */
    private function category(callable $fn): array
    {
        try {
            return ['ok' => true, 'items' => $fn()];
        } catch (\Throwable $e) {
            report($e);

            return ['ok' => false, 'items' => [], 'error' => 'Diese Liste konnte gerade nicht geladen werden.'];
        }
    }

    /**
     * 1) Ueberfaellige Rechnungen: unpaid() (A5) + gesetztes, in der Vergangenheit liegendes due_date (A3).
     *    Betrag = open_amount-Accessor. Kunde eager geladen (kein N+1). Aeltester zuerst.
     */
    private function overdueInvoices(): array
    {
        if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices', 'due_date')) {
            return [];
        }

        return Invoice::query()
            ->unpaid()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::today())
            ->with('customer')
            ->orderBy('due_date')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (Invoice $inv) => [
                'title'  => $inv->invoice_no ?: ('Rechnung #' . $inv->id),
                'meta'   => $this->customerName($inv->customer) . ' · fällig ' . $this->date($inv->due_date),
                'amount' => $this->money($inv->open_amount),
                'href'   => $this->safeRoute('admin.invoices.show', ['invoice' => $inv->id]),
            ])
            ->all();
    }

    /**
     * 2) Offene Angebots-Aufgaben (A1): personal_tasks type=follow_up, source_type=lead_product_list,
     *    task_status NICHT in (completed,done,cancelled), nicht soft-geloescht. Kunde eager geladen.
     */
    private function followUpTasks(): array
    {
        if (!Schema::hasTable('personal_tasks') || !Schema::hasColumn('personal_tasks', 'source_type')) {
            return [];
        }

        return PersonalTask::query()
            ->where('type', 'follow_up')
            ->where('source_type', 'lead_product_list')
            ->whereNotIn('task_status', ['completed', 'done', 'cancelled'])
            ->with('customer')
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (PersonalTask $task) => [
                'title'  => $task->task_title ?: 'Angebots-Aufgabe',
                'meta'   => $this->customerName($task->customer),
                'amount' => null,
                'href'   => $task->customer_id
                    ? $this->safeRoute('new.lead.profile', ['id' => $task->customer_id])
                    : '#',
            ])
            ->all();
    }

    /**
     * 3) Auftraege ohne Rechnung: deals status='deal' ohne (nicht-geloeschte) invoices.deal_id.
     *    whereNotExists statt Left-Join-Null = eine Query, kein N+1; Kunde eager geladen.
     */
    private function dealsWithoutInvoice(): array
    {
        if (!Schema::hasTable('deals') || !Schema::hasTable('invoices') || !Schema::hasColumn('invoices', 'deal_id')) {
            return [];
        }

        $invoicesHaveSoftDelete = Schema::hasColumn('invoices', 'deleted_at');

        return Deal::query()
            ->where('status', 'deal')
            ->whereNotExists(function ($q) use ($invoicesHaveSoftDelete) {
                $q->selectRaw('1')
                    ->from('invoices')
                    ->whereColumn('invoices.deal_id', 'deals.id');
                if ($invoicesHaveSoftDelete) {
                    $q->whereNull('invoices.deleted_at');
                }
            })
            ->with('customer')
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get()
            ->map(fn (Deal $deal) => [
                'title'  => $deal->order_number ?: ('Auftrag #' . $deal->id),
                'meta'   => $this->customerName($deal->customer)
                    . ($deal->price ? ' · ' . $this->money($deal->price) : ''),
                'amount' => null,
                'href'   => $this->safeRoute('admin.invoices.index', ['deal_id' => $deal->id]),
            ])
            ->all();
    }

    /** Kundenname aus NewLeads (display_name-Accessor); leer -> Platzhalter. */
    private function customerName($customer): string
    {
        if (!$customer) {
            return 'Ohne Kunde';
        }

        $name = trim((string) ($customer->display_name ?? ''));

        return $name !== '' ? $name : ('Kunde #' . $customer->id);
    }

    /** Betrag deutsch, tabellarisch dargestellt (Ausrichtung via CSS tabular-nums). */
    private function money($value): string
    {
        return number_format((float) $value, 2, ',', '.') . ' €';
    }

    private function date($date): string
    {
        if (!$date) {
            return '—';
        }

        return $date instanceof \DateTimeInterface
            ? Carbon::instance($date)->format('d.m.Y')
            : Carbon::parse($date)->format('d.m.Y');
    }

    /** Route-Name defensiv aufloesen (fehlt der Name -> '#', kein Fehler). */
    private function safeRoute(string $name, array $params = []): string
    {
        try {
            return Route::has($name) ? route($name, $params) : '#';
        } catch (\Throwable $e) {
            return '#';
        }
    }
}
