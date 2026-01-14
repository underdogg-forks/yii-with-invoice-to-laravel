<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_variable_applicabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_variable_id')->constrained()->onDelete('cascade');
            $table->string('applicable_type'); // invoice, quote, sales_order, email, etc.
            $table->timestamps();
            
            $table->unique(['template_variable_id', 'applicable_type'], 'template_var_applicability_unique');
            $table->index('template_variable_id');
            $table->index('applicable_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_variable_applicabilities');
    }
};
