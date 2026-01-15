<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Remove address-related columns (moved to addresses table)
            if (Schema::hasColumn('clients', 'street_line_1')) {
                $table->dropColumn([
                    'street_line_1',
                    'street_line_2',
                    'city',
                    'state',
                    'zip_code',
                    'country',
                ]);
            }
            
            // Remove communication-related columns (moved to communications table)
            if (Schema::hasColumn('clients', 'phone')) {
                $table->dropColumn([
                    'phone',
                    'mobile',
                    'fax',
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Restore address columns
            $table->string('street_line_1')->nullable();
            $table->string('street_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('country')->nullable();
            
            // Restore communication columns
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('fax')->nullable();
        });
    }
};
