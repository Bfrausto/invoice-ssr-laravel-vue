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
        $formData = [
            'clients' => Client::all(['id', 'name']),
            'companies' => Company::all(['id', 'name']),
            'taxes' => Tax::query()->get(['id', 'name', 'rate']),
        ];


        return Inertia::render('Invoices/Form', [
            'formData' => $formData
        ]);
    }

    public function edit(Invoice $invoice)
    {
        $formData = [
            'clients'   => Client::all(['id', 'name']),
            'companies' => Company::all(['id', 'name']),
            'taxes'     => Tax::all(['id', 'name', 'rate']),
        ];
        $invoice->load(['client', 'items', 'tax', 'company']);

        return Inertia::render('Invoices/Form', [
            'formData' => $formData,
            'invoice' => new InvoiceResource($invoice)
        ]);
    }
}
