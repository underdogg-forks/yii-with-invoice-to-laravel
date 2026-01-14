<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            // Add type and category columns (string, not enum column type)
            $table->string('type')->default('invoice')->after('name');
            $table->string('category')->default('financial')->after('type');
            
            $table->index('type');
            $table->index('category');
            $table->index(['type', 'category']);
        });

        Schema::table('reports', function (Blueprint $table) {
            // Add type column (string, not enum column type)
            $table->string('type')->default('profit')->after('name');
            
            $table->index('type');
        });

        Schema::table('template_variables', function (Blueprint $table) {
            // Add type column (string, not enum column type)
            $table->string('type')->default('string')->after('name');
            
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropColumn(['type', 'category']);
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn('type');
        });

        Schema::table('template_variables', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
