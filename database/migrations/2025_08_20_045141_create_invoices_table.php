<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('tax_id')->nullable()->constrained();
            $table->string('series')->default('F');
            $table->unsignedInteger('folio');
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('currency', 3)->default('MXN');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total_taxes', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('status')->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
