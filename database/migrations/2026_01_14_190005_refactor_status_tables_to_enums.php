<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add status columns to main tables (string, not enum column type)
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('id');
            $table->index('status');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('id');
            $table->index('status');
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->string('status')->default('draft')->after('id');
            $table->index('status');
        });

        // Drop old status tables
        Schema::dropIfExists('invoice_statuses');
        Schema::dropIfExists('quote_statuses');
        Schema::dropIfExists('sales_order_statuses');
    }

    public function down(): void
    {
        // Recreate status tables
        Schema::create('invoice_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('quote_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_order_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('label');
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // Remove status columns from main tables
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
