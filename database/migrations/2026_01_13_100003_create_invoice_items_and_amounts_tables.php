<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tax_rate_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->decimal('quantity', 20, 2);
            $table->decimal('price', 20, 2);
            $table->decimal('discount_amount', 20, 2)->default(0.00);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            
            $table->integer('order')->default(0);
            $table->string('product_unit', 50)->nullable();
            $table->string('product_sku', 100)->nullable();
            
            $table->timestamps();
            
            $table->index(['invoice_id', 'order']);
        });

        Schema::create('invoice_amounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->unique()->constrained()->onDelete('cascade');
            
            // Calculated amounts
            $table->decimal('item_subtotal', 20, 2)->default(0.00);
            $table->decimal('item_tax_total', 20, 2)->default(0.00);
            $table->decimal('tax_total', 20, 2)->default(0.00);
            $table->decimal('discount', 20, 2)->default(0.00);
            $table->decimal('total', 20, 2)->default(0.00);
            $table->decimal('paid', 20, 2)->default(0.00);
            $table->decimal('balance', 20, 2)->default(0.00);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_amounts');
        Schema::dropIfExists('invoice_items');
    }
};
