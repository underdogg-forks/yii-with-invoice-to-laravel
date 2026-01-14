<?php
/**
 * @var object|null $clientPeppol
 * @var int $client_id
 * @var string $actionRoute
 */
ob_start();
?>
<h1><?= $clientPeppol ? 'Edit' : 'Add' ?> Client Peppol</h1>

<form method="POST" action="<?= $actionRoute ?>">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    
    <div class="form-group">
        <label for="endpointid">Endpoint ID (Email)</label>
        <input type="email" id="endpointid" name="endpointid" value="<?= htmlspecialchars($clientPeppol->endpointid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="endpointid_schemeid">Endpoint ID Scheme ID</label>
        <input type="text" id="endpointid_schemeid" name="endpointid_schemeid" maxlength="4" value="<?= htmlspecialchars($clientPeppol->endpointid_schemeid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="identificationid">Identification ID</label>
        <input type="text" id="identificationid" name="identificationid" maxlength="100" value="<?= htmlspecialchars($clientPeppol->identificationid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="identificationid_schemeid">Identification ID Scheme ID</label>
        <input type="text" id="identificationid_schemeid" name="identificationid_schemeid" maxlength="4" value="<?= htmlspecialchars($clientPeppol->identificationid_schemeid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="taxschemecompanyid">Tax Scheme Company ID</label>
        <input type="text" id="taxschemecompanyid" name="taxschemecompanyid" maxlength="100" value="<?= htmlspecialchars($clientPeppol->taxschemecompanyid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="taxschemeid">Tax Scheme ID</label>
        <input type="text" id="taxschemeid" name="taxschemeid" maxlength="7" value="<?= htmlspecialchars($clientPeppol->taxschemeid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="legal_entity_registration_name">Legal Entity Registration Name</label>
        <input type="text" id="legal_entity_registration_name" name="legal_entity_registration_name" maxlength="100" value="<?= htmlspecialchars($clientPeppol->legal_entity_registration_name ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="legal_entity_companyid">Legal Entity Company ID</label>
        <input type="text" id="legal_entity_companyid" name="legal_entity_companyid" maxlength="100" value="<?= htmlspecialchars($clientPeppol->legal_entity_companyid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="legal_entity_companyid_schemeid">Legal Entity Company ID Scheme ID</label>
        <input type="text" id="legal_entity_companyid_schemeid" name="legal_entity_companyid_schemeid" maxlength="5" value="<?= htmlspecialchars($clientPeppol->legal_entity_companyid_schemeid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="legal_entity_company_legal_form">Legal Entity Company Legal Form</label>
        <input type="text" id="legal_entity_company_legal_form" name="legal_entity_company_legal_form" maxlength="50" value="<?= htmlspecialchars($clientPeppol->legal_entity_company_legal_form ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="financial_institution_branchid">Financial Institution Branch ID</label>
        <input type="text" id="financial_institution_branchid" name="financial_institution_branchid" maxlength="20" value="<?= htmlspecialchars($clientPeppol->financial_institution_branchid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="accounting_cost">Accounting Cost</label>
        <input type="text" id="accounting_cost" name="accounting_cost" maxlength="30" value="<?= htmlspecialchars($clientPeppol->accounting_cost ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="supplier_assigned_accountid">Supplier Assigned Account ID</label>
        <input type="text" id="supplier_assigned_accountid" name="supplier_assigned_accountid" maxlength="20" value="<?= htmlspecialchars($clientPeppol->supplier_assigned_accountid ?? '') ?>" required>
    </div>
    
    <div class="form-group">
        <label for="buyer_reference">Buyer Reference</label>
        <input type="text" id="buyer_reference" name="buyer_reference" maxlength="20" value="<?= htmlspecialchars($clientPeppol->buyer_reference ?? '') ?>" required>
    </div>
    
    <button type="submit" class="btn btn-success">Save</button>
    <a href="/clientpeppol" class="btn btn-secondary">Cancel</a>
</form>

<?php
$content = ob_get_clean();
$title = $clientPeppol ? 'Edit Client Peppol' : 'Add Client Peppol';
include __DIR__ . '/../layout.php';
?>
