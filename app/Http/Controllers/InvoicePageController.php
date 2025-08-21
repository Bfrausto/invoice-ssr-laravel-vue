<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Company;
use App\Models\Tax;
use Inertia\Inertia;

class InvoicePageController extends Controller
{
    public function create()
    {
        $initialData = [
            'clients' => Client::all(['id', 'name']),
            'companies' => Company::all(['id', 'name']),
            'taxes' => Tax::get(['id', 'name', 'rate']),
        ];


        return Inertia::render('Invoices/Create', [
            'initialData' => $initialData
        ]);
    }
}
