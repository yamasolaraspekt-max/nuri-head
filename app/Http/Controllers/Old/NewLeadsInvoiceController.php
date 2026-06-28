<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NewLeads;
use App\Models\Invoice;

class NewLeadsInvoiceController extends Controller
{
    public function panel(Request $request, int $customer, int $alternative, int $product)
    {
        $lead = NewLeads::query()->findOrFail($customer);

        $base = Invoice::query()
            ->with(['items', 'files'])
            ->where('customer_id', $lead->id);

        // Alternative/Objekt-Filter
        if ($alternative > 0) {
            $base->where('object_id', $alternative);
        }

        // Wenn Produkt gewählt:
        // - "Produkt-Rechnungen" = hat mindestens eine Position mit product_id
        // - "Allgemeine Rechnungen" = hat KEINE Position mit product_id (inkl. 0 Positionen)
        if ($product > 0) {
            $productInvoices = (clone $base)
                ->whereHas('items', fn ($iq) => $iq->where('product_id', $product))
                ->orderByDesc('issue_date')
                ->limit(250)
                ->get();

            $generalInvoices = (clone $base)
                ->whereDoesntHave('items', fn ($iq) => $iq->where('product_id', $product))
                ->orderByDesc('issue_date')
                ->limit(250)
                ->get();
        } else {
            // Kein Produkt gewählt -> alles in "Produkt-Rechnungen", "Allgemein" leer
            $productInvoices = (clone $base)
                ->orderByDesc('issue_date')
                ->limit(250)
                ->get();

            $generalInvoices = collect();
        }

        $html = view('admin.new_leads.layouts.invoice', [
            'customer'          => $lead,
            'alternative_id'    => $alternative,
            'product_id'        => $product,
            'product_invoices'  => $productInvoices,
            'general_invoices'  => $generalInvoices,
        ])->render();

        return response()->json(['ok' => true, 'html' => $html]);
    }
}
