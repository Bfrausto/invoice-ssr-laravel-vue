<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('global_discount', 5)->default(0);
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->decimal('discount', 5)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('global_discount');
        });

        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
