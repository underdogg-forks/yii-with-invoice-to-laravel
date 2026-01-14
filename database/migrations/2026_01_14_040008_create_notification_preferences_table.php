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
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('channel', ['email', 'database', 'push'])->index();
            $table->string('type'); // notification type
            $table->boolean('is_enabled')->default(true);
            $table->enum('frequency', ['immediate', 'daily', 'weekly'])->default('immediate');
            $table->timestamps();

            $table->unique(['user_id', 'channel', 'type']);
            $table->index(['user_id', 'is_enabled']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
