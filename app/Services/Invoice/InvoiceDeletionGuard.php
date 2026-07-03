<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceFile;
use Illuminate\Support\Facades\Schema;

class InvoiceDeletionGuard
{
    public function canDeleteInvoice(Invoice $invoice): GuardResult
    {
        if (!empty($invoice->invoice_no)) {
            return GuardResult::blocked(
                'INVOICE_ISSUED',
                'Diese Rechnung hat bereits eine Rechnungsnummer und darf nicht geloescht werden. Bitte Storno/Gutschrift verwenden.'
            );
        }

        if ((string) $invoice->status !== 'draft') {
            return GuardResult::blocked(
                'INVOICE_STATUS_LOCKED',
                'Nur Rechnungsentwuerfe duerfen geloescht werden.'
            );
        }

        if (round((float) ($invoice->paid_amount ?? 0), 2) > 0) {
            return GuardResult::blocked(
                'INVOICE_HAS_PAYMENTS',
                'Diese Rechnung enthaelt Zahlungswerte und darf nicht geloescht werden.'
            );
        }

        if ($invoice->exists && $this->hasInvoicePayments($invoice)) {
            return GuardResult::blocked(
                'INVOICE_HAS_PAYMENTS',
                'Diese Rechnung enthaelt Zahlungen und darf nicht geloescht werden.'
            );
        }

        return GuardResult::allowed();
    }

    public function canDeleteFile(InvoiceFile $file): GuardResult
    {
        $invoice = $file->invoice;

        if (!$invoice) {
            return GuardResult::allowed();
        }

        $result = $this->canDeleteInvoice($invoice);
        if ($result->allowed) {
            return $result;
        }

        return GuardResult::blocked(
            'FILE_LOCKED_ISSUED_INVOICE',
            'Dateien ausgestellter oder bezahlter Rechnungen duerfen nicht geloescht werden.'
        );
    }

    private function hasInvoicePayments(Invoice $invoice): bool
    {
        if (!Schema::hasTable('invoice_payments')) {
            return false;
        }

        return (bool) $invoice->getConnection()
            ->table('invoice_payments')
            ->where('invoice_id', $invoice->id)
            ->whereNull('deleted_at')
            ->exists();
    }
}

class GuardResult
{
    private function __construct(
        public readonly bool $allowed,
        public readonly string $code,
        public readonly string $message
    ) {
    }

    public static function allowed(): self
    {
        return new self(true, 'OK', '');
    }

    public static function blocked(string $code, string $message): self
    {
        return new self(false, $code, $message);
    }
}
