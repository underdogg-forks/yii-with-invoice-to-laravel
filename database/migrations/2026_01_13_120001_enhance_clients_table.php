<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Remove old simple fields if they exist
            if (Schema::hasColumn('clients', 'address')) {
                $table->dropColumn('address');
            }
            
            // Add enhanced client fields
            $table->string('surname', 151)->nullable()->after('name');
            $table->string('full_name', 151)->nullable()->after('surname');
            $table->string('mobile', 20)->nullable()->after('email');
            $table->string('title', 10)->nullable()->after('full_name');
            $table->string('group', 50)->nullable()->after('title');
            $table->string('frequency', 15)->nullable()->after('group');
            $table->string('number', 12)->nullable()->unique()->after('frequency');
            
            // Address fields
            $table->string('address_1', 100)->nullable()->after('number');
            $table->string('address_2', 100)->nullable()->after('address_1');
            $table->string('building_number', 10)->nullable()->after('address_2');
            $table->string('city', 100)->nullable()->after('building_number');
            $table->string('state', 30)->nullable()->after('city');
            $table->string('zip', 10)->nullable()->after('state');
            $table->string('country', 30)->nullable()->after('zip');
            
            // Contact fields
            $table->string('fax', 20)->nullable()->after('phone');
            $table->string('web', 50)->nullable()->after('fax');
            
            // Tax fields
            $table->string('vat_id', 30)->nullable()->after('web');
            $table->string('tax_code', 20)->nullable()->after('vat_id');
            
            // Other fields
            $table->string('language', 151)->nullable()->after('tax_code');
            $table->boolean('active')->default(true)->after('language');
            
            // Swiss specific fields
            $table->string('avs', 16)->nullable()->after('active');
            $table->string('insured_number', 151)->nullable()->after('avs');
            $table->string('veka', 30)->nullable()->after('insured_number');
            
            // Personal information
            $table->date('birthdate')->nullable()->after('veka');
            $table->integer('age')->default(0)->after('birthdate');
            $table->tinyInteger('gender')->default(0)->comment('0=unknown, 1=male, 2=female')->after('age');
            
            // References
            $table->unsignedBigInteger('postal_address_id')->nullable()->after('gender');
            
            // Soft deletes
            $table->softDeletes()->after('updated_at');
            
            // Indexes
            $table->index('active');
            $table->index('group');
            $table->index(['name', 'surname']);
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'surname', 'full_name', 'mobile', 'title', 'group', 'frequency', 'number',
                'address_1', 'address_2', 'building_number', 'city', 'state', 'zip', 'country',
                'fax', 'web', 'vat_id', 'tax_code', 'language', 'active',
                'avs', 'insured_number', 'veka', 'birthdate', 'age', 'gender', 'postal_address_id'
            ]);
            
            // Re-add simple address field
            $table->string('address')->nullable()->after('email');
        });
    }
};
