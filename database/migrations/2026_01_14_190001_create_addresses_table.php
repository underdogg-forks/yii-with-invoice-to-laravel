<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable'); // addressable_type, addressable_id
            $table->string('type'); // postal, visiting, delivery, billing, shipping - cast to AddressTypeEnum
            $table->string('street_line_1');
            $table->string('street_line_2')->nullable();
            $table->string('city');
            $table->string('state_province')->nullable();
            $table->string('postal_code');
            $table->string('country', 2); // ISO 3166-1 alpha-2
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            
            $table->index(['addressable_type', 'addressable_id']);
            $table->index(['addressable_type', 'addressable_id', 'type']);
            $table->index(['addressable_type', 'addressable_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
