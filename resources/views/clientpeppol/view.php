<?php
/**
 * @var object $clientPeppol
 */
ob_start();
?>
<h1>View Client Peppol</h1>

<table>
    <tr>
        <th>ID</th>
        <td><?= htmlspecialchars($clientPeppol->id) ?></td>
    </tr>
    <tr>
        <th>Client ID</th>
        <td><?= htmlspecialchars($clientPeppol->client_id) ?></td>
    </tr>
    <tr>
        <th>Endpoint ID</th>
        <td><?= htmlspecialchars($clientPeppol->endpointid) ?></td>
    </tr>
    <tr>
        <th>Endpoint ID Scheme ID</th>
        <td><?= htmlspecialchars($clientPeppol->endpointid_schemeid) ?></td>
    </tr>
    <tr>
        <th>Identification ID</th>
        <td><?= htmlspecialchars($clientPeppol->identificationid) ?></td>
    </tr>
    <tr>
        <th>Identification ID Scheme ID</th>
        <td><?= htmlspecialchars($clientPeppol->identificationid_schemeid) ?></td>
    </tr>
    <tr>
        <th>Tax Scheme Company ID</th>
        <td><?= htmlspecialchars($clientPeppol->taxschemecompanyid) ?></td>
    </tr>
    <tr>
        <th>Tax Scheme ID</th>
        <td><?= htmlspecialchars($clientPeppol->taxschemeid) ?></td>
    </tr>
    <tr>
        <th>Legal Entity Registration Name</th>
        <td><?= htmlspecialchars($clientPeppol->legal_entity_registration_name) ?></td>
    </tr>
    <tr>
        <th>Legal Entity Company ID</th>
        <td><?= htmlspecialchars($clientPeppol->legal_entity_companyid) ?></td>
    </tr>
    <tr>
        <th>Legal Entity Company ID Scheme ID</th>
        <td><?= htmlspecialchars($clientPeppol->legal_entity_companyid_schemeid) ?></td>
    </tr>
    <tr>
        <th>Legal Entity Company Legal Form</th>
        <td><?= htmlspecialchars($clientPeppol->legal_entity_company_legal_form) ?></td>
    </tr>
    <tr>
        <th>Financial Institution Branch ID</th>
        <td><?= htmlspecialchars($clientPeppol->financial_institution_branchid) ?></td>
    </tr>
    <tr>
        <th>Accounting Cost</th>
        <td><?= htmlspecialchars($clientPeppol->accounting_cost) ?></td>
    </tr>
    <tr>
        <th>Supplier Assigned Account ID</th>
        <td><?= htmlspecialchars($clientPeppol->supplier_assigned_accountid) ?></td>
    </tr>
    <tr>
        <th>Buyer Reference</th>
        <td><?= htmlspecialchars($clientPeppol->buyer_reference) ?></td>
    </tr>
</table>

<a href="/clientpeppol/edit/<?= $clientPeppol->id ?>" class="btn btn-primary">Edit</a>
<a href="/clientpeppol" class="btn btn-secondary">Back to List</a>

<?php
$content = ob_get_clean();
$title = 'View Client Peppol';
include __DIR__ . '/../layout.php';
?>
