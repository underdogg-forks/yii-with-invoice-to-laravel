<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('rate', 5, 2);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('family_id')->nullable()->constrained('product_families')->onDelete('set null');
            $table->foreignId('unit_id')->nullable()->constrained('units')->onDelete('set null');
            $table->foreignId('tax_rate_id')->nullable()->constrained('tax_rates')->onDelete('set null');
            
            $table->string('product_sku', 100)->nullable()->unique();
            $table->string('product_name', 255);
            $table->text('product_description')->nullable();
            $table->decimal('product_price', 20, 2);
            $table->decimal('purchase_price', 20, 2)->default(0.00);
            
            $table->boolean('is_sold_as_service')->default(false);
            $table->string('product_tariff', 100)->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            $table->index('product_sku');
            $table->index('product_name');
        });

        Schema::create('product_families', function (Blueprint $table) {
            $table->id();
            $table->string('family_name', 100);
            $table->text('family_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
        Schema::dropIfExists('product_families');
        Schema::dropIfExists('tax_rates');
    }
};
