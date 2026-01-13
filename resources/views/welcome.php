<?php
ob_start();
?>
<h1>Laravel Invoice - Peppol Application</h1>
<p>Welcome to the Laravel 12 Invoice application with Peppol support.</p>

<h2>Peppol Features</h2>
<ul>
    <li><a href="/clientpeppol" class="btn btn-primary">Client Peppol Management</a></li>
    <li><a href="/paymentpeppol" class="btn btn-primary">Payment Peppol Management</a></li>
    <li><a href="/unitpeppol" class="btn btn-primary">Unit Peppol Management</a></li>
</ul>

<h2>About Peppol</h2>
<p>This application implements the Peppol (Pan-European Public Procurement OnLine) standard for electronic invoicing and procurement.</p>

<h3>Key Features:</h3>
<ul>
    <li>Client Peppol: Manage client-specific Peppol configuration</li>
    <li>Payment Peppol: Handle Peppol payment information</li>
    <li>Unit Peppol: Manage unit of measure codes for Peppol</li>
</ul>

<?php
$content = ob_get_clean();
$title = 'Welcome - Laravel Invoice Peppol';
include __DIR__ . '/layout.php';
?>
