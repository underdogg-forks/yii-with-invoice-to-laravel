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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // create, update, delete, view, login, logout
            $table->string('model_type')->nullable(); // Invoice, Client, etc.
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('method'); // GET, POST, PUT, DELETE
            $table->string('url');
            $table->ipAddress('ip_address');
            $table->text('user_agent')->nullable();
            $table->json('request_data')->nullable();
            $table->json('response_data')->nullable();
            $table->integer('status_code')->nullable();
            $table->decimal('execution_time', 8, 2)->nullable(); // milliseconds
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'created_at']);
            $table->index(['model_type', 'model_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
