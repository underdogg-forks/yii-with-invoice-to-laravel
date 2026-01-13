<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_fields', function (Blueprint $table) {
            $table->id();
            $table->string('table_name', 50)->comment('client, invoice, quote, product');
            $table->string('label', 100);
            $table->string('type', 20)->comment('text, textarea, checkbox, select, date, number');
            $table->string('location', 50)->default('client');
            $table->integer('order')->default(0);
            $table->boolean('required')->default(false);
            $table->text('default_value')->nullable();
            $table->text('select_options')->nullable()->comment('JSON array for select type');
            $table->timestamps();
            
            $table->index(['table_name', 'order']);
        });

        Schema::create('client_custom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('custom_field_id')->constrained('custom_fields')->onDelete('cascade');
            $table->text('value')->nullable();
            
            $table->unique(['client_id', 'custom_field_id']);
        });

        Schema::create('product_client', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->unique(['product_id', 'client_id']);
            $table->index('is_default');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_client');
        Schema::dropIfExists('client_custom');
        Schema::dropIfExists('custom_fields');
    }
};
