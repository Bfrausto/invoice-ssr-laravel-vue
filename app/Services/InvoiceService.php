<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(function ($item) {
                return $item['quantity'] * $item['price'];
            });

            $tax = isset($data['tax_id']) ? Tax::find($data['tax_id']) : null;
            $taxAmount = $tax ? $subtotal * ($tax->rate / 100) : 0;
            $total = $subtotal + $taxAmount;

            $invoice = Invoice::create([
                'company_id' => $data['company_id'],
                'client_id' => $data['client_id'],
                'due_date' => $data['due_date'],
                'tax_id' => $data['tax_id'] ?? null,
                'issue_date' => now(),
                'folio' => (Invoice::max('folio') ?? 0) + 1,
                'subtotal' => $subtotal,
                'total_taxes' => $taxAmount,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $subtotal = collect($data['items'])->sum(fn($item) => $item['quantity'] * $item['price']);
            $tax = isset($data['tax_id']) ? Tax::find($data['tax_id']) : null;
            $taxAmount = $tax ? $subtotal * ($tax->rate / 100) : 0;
            $total = $subtotal + $taxAmount;

            $invoice->update([
                'client_id' => $data['client_id'],
                'due_date' => $data['due_date'],
                'tax_id' => $data['tax_id'] ?? null,
                'subtotal' => $subtotal,
                'total_taxes' => $taxAmount,
                'total' => $total,
                'status' => $data['status'] ?? $invoice->status,
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->items()->delete();
            foreach ($data['items'] as $item) {
                $invoice->items()->create([
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['quantity'] * $item['price'],
                ]);
            }

            return $invoice->load(['client', 'items', 'tax', 'company']);
        });
    }

    public function getAll(Request $request): LengthAwarePaginator
    {
        $query = Invoice::query();

        $query->when($request->input('status'), function ($q, $status) {
            return $q->where('status', $status);
        });

        $query->with(['client', 'tax']);

        return $query->latest()->paginate(15);
    }

}
