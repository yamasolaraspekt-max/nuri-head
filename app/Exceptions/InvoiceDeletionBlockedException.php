<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * S1-02: geworfen, wenn eine Rechnung oder Rechnungsdatei nicht geloescht
 * werden darf. Finale Belege werden spaeter ueber Storno korrigiert.
 */
class InvoiceDeletionBlockedException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly string $blockCode = 'INVOICE_DELETE_BLOCKED'
    ) {
        parent::__construct($message);
    }

    public function blockCode(): string
    {
        return $this->blockCode;
    }
}
