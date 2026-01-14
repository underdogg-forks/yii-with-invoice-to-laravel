<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPeppol extends Model
{
    use HasFactory;

    protected $table = 'client_peppol';

    protected $fillable = [
        'client_id',
        'endpointid',
        'endpointid_schemeid',
        'identificationid',
        'identificationid_schemeid',
        'taxschemecompanyid',
        'taxschemeid',
        'legal_entity_registration_name',
        'legal_entity_companyid',
        'legal_entity_companyid_schemeid',
        'legal_entity_company_legal_form',
        'financial_institution_branchid',
        'accounting_cost',
        'supplier_assigned_accountid',
        'buyer_reference',
    ];

    protected $casts = [
        'client_id' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
