<?php

namespace Database\Factories;

use App\Models\ClientPeppol;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientPeppolFactory extends Factory
{
    protected $model = ClientPeppol::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'endpointid' => fake()->email(),
            'endpointid_schemeid' => fake()->lexify('????'),
            'identificationid' => fake()->numerify('##########'),
            'identificationid_schemeid' => fake()->lexify('????'),
            'taxschemecompanyid' => fake()->numerify('##########'),
            'taxschemeid' => fake()->lexify('???????'),
            'legal_entity_registration_name' => fake()->company(),
            'legal_entity_companyid' => fake()->numerify('##########'),
            'legal_entity_companyid_schemeid' => fake()->lexify('?????'),
            'legal_entity_company_legal_form' => fake()->word(),
            'financial_institution_branchid' => fake()->numerify('##########'),
            'accounting_cost' => fake()->numerify('ACC-####'),
            'supplier_assigned_accountid' => fake()->numerify('SUP-####'),
            'buyer_reference' => fake()->numerify('BUY-####'),
        ];
    }
}
