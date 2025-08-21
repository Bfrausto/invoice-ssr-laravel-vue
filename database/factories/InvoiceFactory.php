<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Tax;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */

class InvoiceFactory extends Factory
{
    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 100, 5000);

        return [
            'series' => 'F',
            'folio' => $this->faker->unique()->numberBetween(1, 1000),
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => $subtotal,
            'total_taxes' => 0,
            'total' => $subtotal,
            'status' => 'draft',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Invoice $invoice) {
            $items = InvoiceItem::factory()->count(rand(1, 5))->make();
            $subtotal = 0;
            foreach ($items as $item) {
                $item->invoice_id = $invoice->id;
                $subtotal += $item->quantity * $item->unit_price;
                $item->total = $item->quantity * $item->unit_price;
                $item->save();
            }

            $tax = Tax::where('rate', 16.00)->first() ?? Tax::factory()->create();
            $taxAmount = $subtotal * ($tax->rate / 100);

            $invoice->update([
                'subtotal' => $subtotal,
                'total' => $subtotal + $taxAmount,
                'tax_id' => $tax->id
            ]);
        });
    }
}
