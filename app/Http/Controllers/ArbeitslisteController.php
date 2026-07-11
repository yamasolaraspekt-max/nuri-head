<?php

namespace App\Http\Controllers;

use App\Models\Deal;
use App\Models\Invoice;
use App\Models\PersonalTask;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
    /**
     * Sichtbares Limit je Kategorie — die Inbox bleibt scanbar, kein Endlos-Scroll.
     * Geladen wird LIMIT+1 (siehe paginate()): die (LIMIT+1)-te Zeile beweist „es gibt mehr",
     * OHNE ein teures count(*) ueber tausende Zeilen. Gezeigt werden nur LIMIT Zeilen.
     */
    private const LIMIT = 25;

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
     * Fuehrt eine Kategorie-Query aus und kapselt sie: bei Erfolg wird das paginate()-Payload
     * (items + has_more + more_href) um ['ok'=>true] ergaenzt, bei Fehler ['ok'=>false,...] —
     * nie eine Exception nach aussen (kein 500). Der Fehler-Zweig liefert dieselben Schluessel
     * (items/has_more/more_href), damit die View einheitlich darauf zugreift.
     */
    private function category(callable $fn): array
    {
        try {
            return ['ok' => true] + $fn();
        } catch (\Throwable $e) {
            report($e);

            return [
                'ok'        => false,
                'items'     => [],
                'has_more'  => false,
                'more_href' => '#',
                'error'     => 'Diese Liste konnte gerade nicht geladen werden.',
            ];
        }
    }

    /**
     * Kappung MIT „mehr"-Signal ohne count(*): geladen wird LIMIT+1; existiert die (LIMIT+1)-te
     * Zeile, gibt es mehr als das Sicht-Limit -> has_more=true (+ Link zur Vollansicht). Gezeigt
     * werden nur LIMIT gemappte Zeilen. Ein einzelnes SELECT je Sektion, kein Zaehl-Overhead.
     */
    private function paginate(Collection $rows, callable $map, string $moreHref): array
    {
        $hasMore = $rows->count() > self::LIMIT;

        return [
            'items'     => $rows->take(self::LIMIT)->map($map)->values()->all(),
            'has_more'  => $hasMore,
            'more_href' => $hasMore ? $moreHref : '#',
        ];
    }

    /** Leeres Payload (Schema-Guard greift): dieselbe Form wie paginate(), damit die View gleich bleibt. */
    private function emptyPayload(): array
    {
        return ['items' => [], 'has_more' => false, 'more_href' => '#'];
    }

    /**
     * 1) Ueberfaellige Rechnungen: unpaid() (A5) + gesetztes, in der Vergangenheit liegendes due_date (A3).
     *    Betrag = open_amount-Accessor. Kunde eager geladen (kein N+1). Aeltester zuerst.
     */
    private function overdueInvoices(): array
    {
        if (!Schema::hasTable('invoices') || !Schema::hasColumn('invoices', 'due_date')) {
            return $this->emptyPayload();
        }

        // select() nur auf die WIRKLICH gebrauchten Spalten: customer_id (FK fuer with('customer')),
        // paid_amount/total_amount/type (unpaid()-Scope + open_amount-Accessor), invoice_no/id/due_date
        // (Anzeige). Kleinere Zeilen = weniger Transfer; die Query-Zahl bleibt konstant (kein N+1).
        $rows = Invoice::query()
            ->select(['id', 'invoice_no', 'customer_id', 'due_date', 'total_amount', 'paid_amount', 'type'])
            ->unpaid()
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', Carbon::today())
            ->with('customer')
            ->orderBy('due_date')
            ->limit(self::LIMIT + 1)
            ->get();

        return $this->paginate(
            $rows,
            fn (Invoice $inv) => [
                'title'  => $inv->invoice_no ?: ('Rechnung #' . $inv->id),
                'meta'   => $this->customerName($inv->customer) . ' · fällig ' . $this->date($inv->due_date),
                'amount' => $this->money($inv->open_amount),
                'href'   => $this->safeRoute('admin.invoices.show', ['invoice' => $inv->id]),
            ],
            $this->safeRoute('admin.invoices.index'),
        );
    }

    /**
     * 2) Offene Angebots-Aufgaben (A1): personal_tasks type=follow_up, source_type=lead_product_list,
     *    task_status NICHT in (completed,done,cancelled), nicht soft-geloescht. Kunde eager geladen.
     */
    private function followUpTasks(): array
    {
        if (!Schema::hasTable('personal_tasks') || !Schema::hasColumn('personal_tasks', 'source_type')) {
            return $this->emptyPayload();
        }

        // select() nur auf: id (orderBy + Fallback-Titel), task_title (Titel), customer_id (FK fuer
        // with('customer') + href). Die where-Spalten (type/source_type/task_status) muessen NICHT im
        // select stehen. EXPLAIN: ORDER BY id DESC LIMIT n wird per PRIMARY-reverse-Scan + Filter +
        // LIMIT-Early-Termination bedient (kein filesort, bounded); der (type,source_type,source_id)-
        // Index greift hier bewusst NICHT, weil er fuer ORDER BY id DESC einen filesort erzwaenge.
        $rows = PersonalTask::query()
            ->select(['id', 'task_title', 'customer_id'])
            ->where('type', 'follow_up')
            ->where('source_type', 'lead_product_list')
            ->whereNotIn('task_status', ['completed', 'done', 'cancelled'])
            ->with('customer')
            ->orderByDesc('id')
            ->limit(self::LIMIT + 1)
            ->get();

        return $this->paginate(
            $rows,
            fn (PersonalTask $task) => [
                'title'  => $task->task_title ?: 'Angebots-Aufgabe',
                'meta'   => $this->customerName($task->customer),
                'amount' => null,
                'href'   => $task->customer_id
                    ? $this->safeRoute('new.lead.profile', ['id' => $task->customer_id])
                    : '#',
            ],
            '#', // keine dedizierte Follow-up-Vollansicht -> „mehr" wird als Hinweis (ohne Link) gezeigt
        );
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

        // select() nur auf: id (orderBy + whereNotExists-Korrelation + Fallback-Titel), order_number
        // (Titel), customer_id (FK fuer with('customer')), price (Meta), status (where-Klarheit).
        // Neuer Index deals_status_index bedient status='deal' + ORDER BY id DESC (Sekundaerindex
        // fuehrt die PK mit -> reverse ohne filesort); die Subquery nutzt invoices_deal_id_index.
        $rows = Deal::query()
            ->select(['id', 'order_number', 'customer_id', 'price', 'status'])
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
            ->limit(self::LIMIT + 1)
            ->get();

        return $this->paginate(
            $rows,
            fn (Deal $deal) => [
                'title'  => $deal->order_number ?: ('Auftrag #' . $deal->id),
                'meta'   => $this->customerName($deal->customer)
                    . ($deal->price ? ' · ' . $this->money($deal->price) : ''),
                'amount' => null,
                'href'   => $this->safeRoute('admin.invoices.index', ['deal_id' => $deal->id]),
            ],
            '#', // keine dedizierte Auftrags-Vollansicht -> „mehr" als Hinweis (ohne Link)
        );
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
