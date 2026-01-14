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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['profit_analysis', 'sales_summary', 'inventory_report', 'custom'])->index();
            $table->text('description')->nullable();
            $table->json('parameters')->nullable(); // date ranges, filters, etc.
            $table->string('file_path')->nullable();
            $table->foreignId('generated_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('generated_at')->nullable()->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'generated_at']);
            $table->index('generated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
