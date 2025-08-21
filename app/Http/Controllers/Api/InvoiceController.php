<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInvoiceRequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $invoices = $this->invoiceService->getAll($request);

        return InvoiceResource::collection($invoices);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $this->invoiceService->create($request->validated());

        return redirect()->route('dashboard')
            ->with('success', 'Factura creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'items', 'tax', 'company']);
        return new InvoiceResource($invoice);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        $this->invoiceService->update($invoice, $request->validated());

        return redirect()->route('dashboard', [], 303)
            ->with('success', 'Factura actualizada  exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
