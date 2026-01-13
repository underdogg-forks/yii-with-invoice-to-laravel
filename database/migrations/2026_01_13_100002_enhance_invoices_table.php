<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update invoices table with full structure
        Schema::table('invoices', function (Blueprint $table) {
            // Drop existing columns if needed
            // Add all invoice fields
            $table->string('number', 100)->nullable()->after('id');
            $table->foreignId('client_id')->after('number')->constrained()->onDelete('cascade');
            $table->foreignId('group_id')->after('client_id')->constrained('invoice_groups')->onDelete('cascade');
            $table->foreignId('status_id')->after('group_id')->default(1)->constrained('invoice_statuses');
            
            // Dates
            $table->date('date_created')->nullable();
            $table->date('date_modified')->nullable();
            $table->date('date_supplied')->nullable();
            $table->date('date_due')->nullable();
            $table->date('date_tax_point')->nullable();
            $table->date('date_paid_off')->nullable();
            
            // Related entities
            $table->unsignedBigInteger('quote_id')->nullable();
            $table->unsignedBigInteger('so_id')->nullable(); // Sales Order
            $table->unsignedBigInteger('creditinvoice_parent_id')->nullable();
            $table->unsignedBigInteger('delivery_id')->nullable();
            $table->unsignedBigInteger('delivery_location_id')->nullable();
            $table->unsignedBigInteger('postal_address_id')->nullable();
            $table->unsignedBigInteger('contract_id')->nullable();
            
            // Financial fields
            $table->decimal('discount_amount', 20, 2)->default(0.00);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            
            // Security and access
            $table->string('url_key', 32)->nullable()->unique();
            $table->string('password', 90)->nullable();
            
            // Additional fields
            $table->integer('payment_method')->default(0);
            $table->text('terms')->nullable();
            $table->text('note')->nullable();
            $table->string('document_description', 32)->nullable();
            $table->string('stand_in_code', 3)->nullable();
            $table->boolean('is_read_only')->default(false);
            
            // Indexes
            $table->index('number');
            $table->index('date_created');
            $table->index('date_due');
            $table->index(['client_id', 'status_id']);
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'number', 'group_id', 'status_id',
                'date_created', 'date_modified', 'date_supplied', 'date_due',
                'date_tax_point', 'date_paid_off',
                'quote_id', 'so_id', 'creditinvoice_parent_id',
                'delivery_id', 'delivery_location_id', 'postal_address_id', 'contract_id',
                'discount_amount', 'discount_percent',
                'url_key', 'password', 'payment_method',
                'terms', 'note', 'document_description', 'stand_in_code', 'is_read_only'
            ]);
        });
    }
};
