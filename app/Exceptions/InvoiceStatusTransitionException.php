<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * S1-01: geworfen, wenn eine ausgestellte Rechnung (sent/paid/overdue)
 * unzulässig nach `draft` zurückgesetzt werden soll. Rücknahme läuft
 * ausschließlich über Storno/Gutschrift (S1-04), nie über einen Statuswechsel.
 */
class InvoiceStatusTransitionException extends RuntimeException
{
}
