<?php

namespace App\DTOs;

class ClientPeppolDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $client_id = null,
        public string $accounting_cost = '',
        public string $buyer_reference = '',
        public string $endpointid = '',
        public string $endpointid_schemeid = '',
        public string $financial_institution_branchid = '',
        public string $identificationid = '',
        public string $identificationid_schemeid = '',
        public string $legal_entity_registration_name = '',
        public string $legal_entity_companyid = '',
        public string $legal_entity_companyid_schemeid = '',
        public string $legal_entity_company_legal_form = '',
        public string $taxschemecompanyid = '',
        public string $taxschemeid = '',
        public string $supplier_assigned_accountid = '',
    ) {}

    public static function fromModel($model): self
    {
        return new self(
            id: $model->id,
            client_id: $model->client_id,
            accounting_cost: $model->accounting_cost ?? '',
            buyer_reference: $model->buyer_reference ?? '',
            endpointid: $model->endpointid ?? '',
            endpointid_schemeid: $model->endpointid_schemeid ?? '',
            financial_institution_branchid: $model->financial_institution_branchid ?? '',
            identificationid: $model->identificationid ?? '',
            identificationid_schemeid: $model->identificationid_schemeid ?? '',
            legal_entity_registration_name: $model->legal_entity_registration_name ?? '',
            legal_entity_companyid: $model->legal_entity_companyid ?? '',
            legal_entity_companyid_schemeid: $model->legal_entity_companyid_schemeid ?? '',
            legal_entity_company_legal_form: $model->legal_entity_company_legal_form ?? '',
            taxschemecompanyid: $model->taxschemecompanyid ?? '',
            taxschemeid: $model->taxschemeid ?? '',
            supplier_assigned_accountid: $model->supplier_assigned_accountid ?? '',
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->client_id,
            'accounting_cost' => $this->accounting_cost,
            'buyer_reference' => $this->buyer_reference,
            'endpointid' => $this->endpointid,
            'endpointid_schemeid' => $this->endpointid_schemeid,
            'financial_institution_branchid' => $this->financial_institution_branchid,
            'identificationid' => $this->identificationid,
            'identificationid_schemeid' => $this->identificationid_schemeid,
            'legal_entity_registration_name' => $this->legal_entity_registration_name,
            'legal_entity_companyid' => $this->legal_entity_companyid,
            'legal_entity_companyid_schemeid' => $this->legal_entity_companyid_schemeid,
            'legal_entity_company_legal_form' => $this->legal_entity_company_legal_form,
            'taxschemecompanyid' => $this->taxschemecompanyid,
            'taxschemeid' => $this->taxschemeid,
            'supplier_assigned_accountid' => $this->supplier_assigned_accountid,
        ];
    }
}
