<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfController extends Controller
{
    public function store(Invoice $invoice)
    {
        $invoice->load(['client', 'items', 'company']);
        $pdf = PDF::loadView('invoices.pdf', [
            'invoice' => $invoice,
        ]);
        return $pdf->download('invoice-' . $invoice->id . '.pdf');
    }
}
