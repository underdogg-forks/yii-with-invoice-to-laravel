<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->morphs('communicable'); // communicable_type, communicable_id
            $table->string('type'); // phone, mobile, fax, email, website, skype, whatsapp - cast to CommunicationTypeEnum
            $table->string('value');
            $table->string('label')->nullable(); // "Work", "Home", "Mobile", etc.
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->timestamps();
            
            $table->index(['communicable_type', 'communicable_id']);
            $table->index(['communicable_type', 'communicable_id', 'type']);
            $table->index(['communicable_type', 'communicable_id', 'is_primary']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
