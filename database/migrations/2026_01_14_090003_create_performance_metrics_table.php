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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('url');
            $table->string('method');
            $table->decimal('execution_time', 8, 2); // milliseconds
            $table->integer('query_count');
            $table->decimal('query_time', 8, 2); // milliseconds
            $table->integer('memory_usage'); // bytes
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('created_at');
            
            $table->index(['url', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
