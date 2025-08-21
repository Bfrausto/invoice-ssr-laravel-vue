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
            $subtotal = 0;

            $itemsWithTotals = collect($data['items'])->map(function ($item) use (&$subtotal) {
                $itemTotal = $item['quantity'] * $item['price'];
                $discountAmount = $itemTotal * (($item['discount'] ?? 0) / 100);
                $finalItemTotal = $itemTotal - $discountAmount;

                $subtotal += $finalItemTotal;

                return array_merge($item, ['total' => $finalItemTotal]);
            });

            $globalDiscountAmount = $subtotal * (($data['global_discount'] ?? 0) / 100);
            $subtotalAfterDiscount = $subtotal - $globalDiscountAmount;

            $tax = isset($data['tax_id']) ? Tax::find($data['tax_id']) : null;
            $taxAmount = $tax ? $subtotalAfterDiscount * ($tax->rate / 100) : 0;

            $total = $subtotalAfterDiscount + $taxAmount;


            $invoice = Invoice::create([
                'company_id' => $data['company_id'],
                'client_id' => $data['client_id'],
                'due_date' => $data['due_date'],
                'tax_id' => $data['tax_id'] ?? null,
                'issue_date' => now(),
                'folio' => (Invoice::max('folio') ?? 0) + 1,
                'subtotal' => $subtotal,
                'global_discount' => $data['global_discount'] ?? 0,
                'total_taxes' => $taxAmount,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
                'currency' => $data['currency'],
            ]);

            $invoice->items()->createMany($itemsWithTotals->toArray());


            return $invoice;
        });
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        return DB::transaction(function () use ($invoice, $data) {
            $subtotal = 0;

            $itemsWithTotals = collect($data['items'])->map(function ($item) use (&$subtotal) {
                $itemTotal = $item['quantity'] * $item['price'];
                $discountAmount = $itemTotal * (($item['discount'] ?? 0) / 100);
                $finalItemTotal = $itemTotal - $discountAmount;

                $subtotal += $finalItemTotal;

                return array_merge($item, ['total' => $finalItemTotal]);
            });

            $globalDiscountAmount = $subtotal * (($data['global_discount'] ?? 0) / 100);
            $subtotalAfterDiscount = $subtotal - $globalDiscountAmount;

            $tax = isset($data['tax_id']) ? Tax::find($data['tax_id']) : null;
            $taxAmount = $tax ? $subtotalAfterDiscount * ($tax->rate / 100) : 0;

            $total = $subtotalAfterDiscount + $taxAmount;


            $invoice->update([
                'client_id' => $data['client_id'],
                'due_date' => $data['due_date'],
                'tax_id' => $data['tax_id'] ?? null,
                'subtotal' => $subtotal,
                'global_discount' => $data['global_discount'] ?? 0,
                'total_taxes' => $taxAmount,
                'total' => $total,
                'status' => $data['status'] ?? $invoice->status,
                'notes' => $data['notes'] ?? null,
                'currency' => $data['currency'] ?? $invoice->currency,
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($itemsWithTotals->toArray());


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
