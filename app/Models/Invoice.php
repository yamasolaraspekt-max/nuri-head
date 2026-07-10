<?php

namespace App\Models;

use App\Exceptions\InvoiceStatusTransitionException;
use App\Exceptions\InvoiceDeletionBlockedException;
use App\Services\Invoice\InvoiceDeletionGuard;
use App\Services\Invoice\InvoiceNumberService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Invoice extends Model
{
    use SoftDeletes;

    public const STATUS = ['draft', 'sent', 'paid', 'overdue', 'cancelled'];

    /** S1-01 (D1-A): Status, die als „ausgestellt/final" gelten und eine Nummer erhalten. */
    public const ISSUED_STATUSES = ['sent', 'paid', 'overdue'];

    /**
     * Zahlungsziel-Default (Kalendertage) fuer die due_date-Ableitung; hier zentral anpassbar.
     * Explizit gesetztes due_date bleibt unangetastet.
     */
    public const ZAHLUNGSZIEL_TAGE = 14;

    /**
     * A5: Typen OHNE Kunden-Zahlungsziel — EINE Wahrheit fuer die Ausnahme-Menge.
     * Gutschrift/Stornorechnung: Geld fliesst zum Kunden / Rechnung aufgehoben — es gibt
     * kein Kunden-Zahlungsziel, sie werden daher NIE als „bezahlt" klassifiziert (kein
     * due_date-Ableiten in A3, kein isFullyPaid()/scopePaid() in A5). Vergleich case-insensitiv
     * (Typ-Strings im Bestand sind kapitalisiert, z.B. 'Gutschrift'), Liste hier in lowercase.
     */
    public const TYPEN_OHNE_ZAHLUNG = ['gutschrift', 'stornorechnung'];

    protected static function booted(): void
    {
        // S1-01: EINZIGE Vergabestelle. Beim Speichern einer ausgestellten Rechnung
        // ohne Nummer wird sie über den InvoiceNumberService reserviert (idempotent).
        static::saving(function (self $invoice): void {
            if (empty($invoice->invoice_no) && $invoice->isIssuedStatus($invoice->status)) {
                app(InvoiceNumberService::class)->reserve($invoice);
            }
        });

        // A3: EINZIGE Ableitungsregel fuer das Zahlungsziel. Ist due_date leer und ein
        // issue_date vorhanden, wird due_date = issue_date + ZAHLUNGSZIEL_TAGE (Kalendertage,
        // KEINE Werktags-/Feiertagslogik). Ein explizit gesetztes due_date wird NIEMALS
        // ueberschrieben (Operanden-Gate) — idempotent auf create UND update.
        // A3-DELTA: Gutschrift/Stornorechnung tragen kein Kunden-Zahlungsziel (Geld fliesst
        // zum Kunden / Rechnung aufgehoben) -> keine due_date-Ableitung.
        // BEWUSSTE GoBD-Entscheidung: greift NUR bei leerem due_date. Wird das issue_date
        // nachtraeglich geaendert, wird die Faelligkeit NICHT neu abgeleitet — eine einmal
        // gesetzte Faelligkeit bleibt stabil (kein rueckwirkendes Verschieben von Zahlungszielen).
        static::saving(function (self $invoice): void {
            if (empty($invoice->due_date)
                && !empty($invoice->issue_date)
                && !in_array(mb_strtolower(trim((string) $invoice->type)), self::TYPEN_OHNE_ZAHLUNG, true)
            ) {
                $invoice->due_date = Carbon::parse($invoice->issue_date)
                    ->addDays(self::ZAHLUNGSZIEL_TAGE);
            }
        });

        // S1-01: sent/paid/overdue -> draft ist gesperrt (Rücknahme nur via Storno, S1-04).
        static::updating(function (self $invoice): void {
            if ($invoice->isDirty('status')
                && (string) $invoice->status === 'draft'
                && $invoice->isIssuedStatus((string) $invoice->getOriginal('status'))
            ) {
                throw new InvoiceStatusTransitionException(
                    'Eine ausgestellte Rechnung kann nicht nach Entwurf zurückgesetzt werden. Bitte stornieren.'
                );
            }
        });

        // S1-02: Defense-in-depth gegen direkte $invoice->delete()-Aufrufe.
        static::deleting(function (self $invoice): void {
            $result = app(InvoiceDeletionGuard::class)->canDeleteInvoice($invoice);

            if (!$result->allowed) {
                throw new InvoiceDeletionBlockedException($result->message, $result->code);
            }
        });
    }

    /** True, wenn der (angegebene oder eigene) Status als ausgestellt/final gilt. */
    public function isIssuedStatus(?string $status = null): bool
    {
        return in_array($status ?? (string) $this->status, self::ISSUED_STATUSES, true);
    }

    protected $fillable = [
        'account_id',
        'customer_id',
        'object_id',
        'deal_id',
        'offer_detail_id',
        'invoice_no',
        'type',
        'status',
        'issue_date',
        'due_date',
        'service_from',
        'service_to',
        'currency',
        'subtotal',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'paid_amount',
        'paid_at',
        'deal_limit_amount',
        'deal_remaining_before',
        'deal_remaining_after',
        'payment_note',
        'notes',
        'created_by',
        'updated_by',
        'source_offer_detail_id',
        'source_offer_items_hash',
        'source_offer_synced_at',
        'source_offer_updated_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'service_from' => 'date',
        'service_to' => 'date',

        'subtotal' => 'decimal:2',
        'tax_rate' => 'decimal:3',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'deal_limit_amount' => 'decimal:2',
        'deal_remaining_before' => 'decimal:2',
        'deal_remaining_after' => 'decimal:2',
        'paid_at' => 'datetime',
        'source_offer_synced_at' => 'datetime',
        'source_offer_updated_at' => 'datetime',
    ];

    protected $appends = [
        'open_amount',
    ];

    public function getOpenAmountAttribute(): float
    {
        return max(0, round(((float) $this->total_amount - (float) $this->paid_amount), 2));
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function object()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'object_id');
    }

    public function deal()
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function offerDetail()
    {
        return $this->belongsTo(OfferDetail::class, 'offer_detail_id');
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id')->orderBy('sort_order')->orderBy('id');
    }

    public function files()
    {
        return $this->hasMany(InvoiceFile::class, 'invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(Employee::class, 'updated_by');
    }

    public function histories()
    {
        return $this->hasMany(InvoiceHistory::class, 'invoice_id')->latest();
    }

    public function scopeForAccount($q, ?int $accountId)
    {
        return $accountId === null ? $q : $q->where('account_id', $accountId);
    }

    /**
     * A5 — EINZIGE (betrags-basierte) „voll bezahlt"-Wahrheit: KEIN gespeichertes Bool-Feld,
     * sondern abgeleitet aus paid_amount/total_amount. Regel: NICHT ausgenommener Typ UND
     * paid_amount >= total_amount, decimal-EXAKT via bccomp (kein Float-Drift). Konsistent zum
     * bestehenden open_amount-Accessor: fuer nicht-ausgenommene Typen gilt open_amount == 0 ⇔ voll bezahlt.
     * Kanten: total_amount = 0 -> bezahlt (paid >= 0); Ueberzahlung paid > total -> bezahlt (>=);
     * Gutschrift/Stornorechnung -> nie bezahlt (Typ-Gate, TYPEN_OHNE_ZAHLUNG).
     */
    public function isFullyPaid(): bool
    {
        if (in_array(mb_strtolower(trim((string) $this->type)), self::TYPEN_OHNE_ZAHLUNG, true)) {
            return false;
        }

        return bccomp((string) $this->paid_amount, (string) $this->total_amount, 2) >= 0;
    }

    /** A5 — dieselbe Regel serverseitig/SQL-filterbar (nicht ausgenommen UND paid >= total). */
    public function scopePaid($q)
    {
        return $q
            ->whereColumn('paid_amount', '>=', 'total_amount')
            ->whereNotIn(DB::raw('LOWER(type)'), self::TYPEN_OHNE_ZAHLUNG);
    }

    /** A5 — Gegenstueck: nicht ausgenommen UND paid < total. */
    public function scopeUnpaid($q)
    {
        return $q
            ->whereColumn('paid_amount', '<', 'total_amount')
            ->whereNotIn(DB::raw('LOWER(type)'), self::TYPEN_OHNE_ZAHLUNG);
    }

    /**
     * A5 — delegiert auf die EINE „bezahlt"-Wahrheit (betrags-basiert). Die fruehere
     * status-basierte Variante (return $this->status === 'paid') ist damit abgeloest; sie hatte
     * 0 Aufrufer, die Umstellung ist regressionsfrei. Der status='paid'-Filter in den Dashboards
     * (A5b) bleibt bewusst unberuehrt.
     */
    public function isPaid(): bool
    {
        return $this->isFullyPaid();
    }

    public function recalcTotals(): self
    {
        $this->loadMissing('items:id,invoice_id,line_total');

        $subtotal = round((float) $this->items->sum('line_total'), 2);
        $taxRate = (float) ($this->tax_rate ?? 0);
        $taxAmount = round($subtotal * ($taxRate / 100), 2);
        $total = round($subtotal + $taxAmount, 2);

        $this->forceFill([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $total,
        ])->save();

        return $this;
    }

}
