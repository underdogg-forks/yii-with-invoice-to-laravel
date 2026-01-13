<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_numbering', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('identifier_format', 50)->nullable();
            $table->integer('next_id')->default(1);
            $table->integer('left_pad')->default(0);
            $table->timestamps();
        });

        Schema::create('invoice_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('label', 100);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_statuses');
        Schema::dropIfExists('invoice_numbering');
    }
};
