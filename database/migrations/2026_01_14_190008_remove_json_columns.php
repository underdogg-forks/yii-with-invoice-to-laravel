<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_variables', function (Blueprint $table) {
            // Remove applicable_to JSON column (replaced with template_variable_applicabilities table)
            if (Schema::hasColumn('template_variables', 'applicable_to')) {
                $table->dropColumn('applicable_to');
            }
        });

        Schema::table('reports', function (Blueprint $table) {
            // Remove parameters JSON column (replaced with report_parameters table)
            if (Schema::hasColumn('reports', 'parameters')) {
                $table->dropColumn('parameters');
            }
        });
    }

    public function down(): void
    {
        Schema::table('template_variables', function (Blueprint $table) {
            // Restore applicable_to JSON column
            $table->json('applicable_to')->nullable();
        });

        Schema::table('reports', function (Blueprint $table) {
            // Restore parameters JSON column
            $table->json('parameters')->nullable();
        });
    }
};
