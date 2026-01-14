<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_peppol', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('endpointid', 100)->default('');
            $table->string('endpointid_schemeid', 4)->default('');
            $table->string('identificationid', 100)->default('');
            $table->string('identificationid_schemeid', 4)->default('');
            $table->string('taxschemecompanyid', 100)->default('');
            $table->string('taxschemeid', 7)->default('');
            $table->string('legal_entity_registration_name', 100)->default('');
            $table->string('legal_entity_companyid', 100)->default('');
            $table->string('legal_entity_companyid_schemeid', 5)->default('');
            $table->string('legal_entity_company_legal_form', 50)->default('');
            $table->string('financial_institution_branchid', 20)->default('');
            $table->string('accounting_cost', 30)->default('');
            $table->string('supplier_assigned_accountid', 20)->default('');
            $table->string('buyer_reference', 20)->default('');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_peppol');
    }
};
