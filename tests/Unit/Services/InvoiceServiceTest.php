<?php

namespace Tests\Unit\Services;

use App\Models\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Services\InvoiceService;
use App\Models\Tax;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceService $invoiceService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->invoiceService = new InvoiceService();
    }

    /**
     * @test
     */
    public function it_creates_an_invoice_with_discounts_and_calculates_totals_correctly(): void
    {
        $company = Company::factory()->create();
        $client = Client::factory()->create();
        $tax = Tax::factory()->create(['rate' => 16.00]);
        $invoiceData = [
            'company_id' => $company->id,
            'client_id' => $client->id,
            'series' => 'TEST',
            'folio' => 1,
            'due_date' => now()->addMonth()->toDateString(),
            'currency' => 'MXN',
            'status' => 'saved',
            'tax_id' => $tax->id,
            'global_discount' => 10,
            'items' => [
                ['description' => 'Item con 20% desc.', 'quantity' => 1, 'price' => 1000, 'discount' => 20],
                ['description' => 'Item sin desc.', 'quantity' => 2, 'price' => 250, 'discount' => 0],
            ],
        ];

        $createdInvoice = $this->invoiceService->create($invoiceData);

        $this->assertDatabaseHas('invoices', [
            'id' => $createdInvoice->id,
            'folio' => 1,
            'subtotal' => 1300,
            'global_discount' => 10,
            'total_taxes' => 187.20,
            'total' => 1357.20,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $createdInvoice->id,
            'description' => 'Item con 20% desc.',
            'discount' => 20,
            'total' => 800,
        ]);

        $this->assertDatabaseCount('invoice_items', 2);
    }
}
