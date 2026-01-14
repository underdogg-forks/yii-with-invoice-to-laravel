<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('key'); // parameter name
            $table->text('value'); // parameter value (can store JSON string if needed)
            $table->string('type')->default('string'); // string, number, date, boolean, array
            $table->timestamps();
            
            $table->unique(['report_id', 'key']);
            $table->index('report_id');
            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_parameters');
    }
};
