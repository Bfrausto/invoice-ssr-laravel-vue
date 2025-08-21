<?php

namespace App\Http\Controllers;

use App\Http\Resources\InvoiceResource;
use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Tax;
use Inertia\Inertia;

class InvoicePageController extends Controller
{
    public function create()
    {
        return Inertia::render('Invoices/Form', [
            'formData' => $this->getFormData(),
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['client', 'items', 'tax', 'company']);

        return Inertia::render('Invoices/Form', [
            'formData' => $this->getFormData(),
            'invoice' => new InvoiceResource($invoice)
        ]);
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        return [
            'clients' => Client::all(['id', 'name']),
            'companies' => Company::all(['id', 'name']),
            'taxes' => Tax::all(['id', 'name', 'rate']),
            'currencies' => config('invoicing.currencies'),
            'exchangeRate' => config('invoicing.exchange_rate_usd_to_mxn')
        ];
    }
}
