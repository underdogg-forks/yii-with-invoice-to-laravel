<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_peppol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inv_id')->nullable()->constrained('invoices')->onDelete('cascade');
            $table->integer('auto_reference')->nullable();
            $table->string('provider', 20)->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_peppol');
    }
};
