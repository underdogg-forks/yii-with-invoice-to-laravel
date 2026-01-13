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
        // Create quotes table
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('quote_number', 100)->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('status_id')->default(1)->constrained('quote_statuses')->onDelete('restrict');
            $table->date('quote_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_total', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->string('url_key', 64)->unique()->nullable();
            $table->string('password', 255)->nullable();
            $table->boolean('is_read_only')->default(false);
            
            // Workflow tracking
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('viewed_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('quote_number');
            $table->index('client_id');
            $table->index('status_id');
            $table->index('quote_date');
            $table->index('expiry_date');
            $table->index(['client_id', 'quote_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
