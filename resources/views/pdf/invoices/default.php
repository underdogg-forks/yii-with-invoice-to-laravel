<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #333; margin: 0; font-size: 24px; }
        .info-section { margin-bottom: 20px; }
        .info-section h3 { color: #666; font-size: 14px; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; }
        .two-column { width: 100%; }
        .two-column td { vertical-align: top; width: 50%; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th { background-color: #f5f5f5; padding: 8px; text-align: left; border-bottom: 2px solid #ddd; }
        .items-table td { padding: 8px; border-bottom: 1px solid #eee; }
        .items-table tr:last-child td { border-bottom: 2px solid #ddd; }
        .totals { margin-top: 20px; }
        .totals table { width: 300px; float: right; }
        .totals table td { padding: 5px; }
        .totals table td:first-child { text-align: right; font-weight: bold; }
        .totals table td:last-child { text-align: right; }
        .total-row { font-size: 14px; background-color: #f5f5f5; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p><strong>Invoice #<?php echo htmlspecialchars($invoice->invoice_number); ?></strong></p>
        <p>Date: <?php echo $invoice->date_invoice ? $invoice->date_invoice->format('d-m-Y') : date('d-m-Y'); ?></p>
    </div>

    <table class="two-column">
        <tr>
            <td>
                <div class="info-section">
                    <h3>From:</h3>
                    <p><strong><?php echo htmlspecialchars(config('app.name', 'Company Name')); ?></strong></p>
                    <p><?php echo htmlspecialchars(config('peppol.supplier.address.street', '')); ?></p>
                    <p><?php echo htmlspecialchars(config('peppol.supplier.address.postal_code', '') . ' ' . config('peppol.supplier.address.city', '')); ?></p>
                    <p><?php echo htmlspecialchars(config('peppol.supplier.address.country_code', 'NL')); ?></p>
                    <p>VAT: <?php echo htmlspecialchars(config('peppol.supplier.vat_number', '')); ?></p>
                </div>
            </td>
            <td>
                <div class="info-section">
                    <h3>Bill To:</h3>
                    <p><strong><?php echo htmlspecialchars($invoice->client->client_name); ?></strong></p>
                    <?php if ($invoice->client->client_address_1): ?>
                    <p><?php echo htmlspecialchars($invoice->client->client_address_1); ?></p>
                    <?php endif; ?>
                    <?php if ($invoice->client->client_postal_code || $invoice->client->client_city): ?>
                    <p><?php echo htmlspecialchars(($invoice->client->client_postal_code ?? '') . ' ' . ($invoice->client->client_city ?? '')); ?></p>
                    <?php endif; ?>
                    <?php if ($invoice->client->client_country): ?>
                    <p><?php echo htmlspecialchars($invoice->client->client_country); ?></p>
                    <?php endif; ?>
                    <?php if ($invoice->client->client_vat_id): ?>
                    <p>VAT: <?php echo htmlspecialchars($invoice->client->client_vat_id); ?></p>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Description</th>
                <th style="text-align: right;">Quantity</th>
                <th style="text-align: right;">Price</th>
                <th style="text-align: right;">Tax</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($invoice->items as $index => $item): ?>
            <tr>
                <td><?php echo $index + 1; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($item->item_name); ?></strong>
                    <?php if ($item->item_description): ?>
                    <br><small><?php echo htmlspecialchars($item->item_description); ?></small>
                    <?php endif; ?>
                </td>
                <td style="text-align: right;"><?php echo number_format($item->quantity ?? 1, 2); ?></td>
                <td style="text-align: right;"><?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($item->item_price ?? 0, 2); ?></td>
                <td style="text-align: right;"><?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($item->item_tax_total ?? 0, 2); ?></td>
                <td style="text-align: right;"><?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($item->item_total ?? 0, 2); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td><?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($invoice->subtotal ?? 0, 2); ?></td>
            </tr>
            <?php if ($invoice->total_discount): ?>
            <tr>
                <td>Discount:</td>
                <td>-<?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($invoice->total_discount, 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Tax:</td>
                <td><?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($invoice->total_tax_amount ?? 0, 2); ?></td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td><?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($invoice->total_amount ?? 0, 2); ?></td>
            </tr>
            <?php if ($invoice->paid): ?>
            <tr>
                <td>Paid:</td>
                <td>-<?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($invoice->paid, 2); ?></td>
            </tr>
            <tr style="background-color: #ffe; font-weight: bold;">
                <td>Balance Due:</td>
                <td><?php echo htmlspecialchars($invoice->currency ?? 'EUR'); ?> <?php echo number_format($invoice->balance ?? 0, 2); ?></td>
            </tr>
            <?php endif; ?>
        </table>
        <div style="clear: both;"></div>
    </div>

    <?php if ($invoice->terms): ?>
    <div class="info-section" style="margin-top: 40px;">
        <h3>Terms & Conditions:</h3>
        <p><?php echo nl2br(htmlspecialchars($invoice->terms)); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Generated on <?php echo date('d-m-Y H:i'); ?></p>
    </div>
</body>
</html>
