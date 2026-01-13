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
        .totals { margin-top: 20px; }
        .totals table { width: 300px; float: right; }
        .totals table td { padding: 5px; }
        .totals table td:first-child { text-align: right; font-weight: bold; }
        .totals table td:last-child { text-align: right; }
        .total-row { font-size: 14px; background-color: #f5f5f5; }
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #999; }
        .status-badge { display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 11px; font-weight: bold; }
        .status-draft { background-color: #f0f0f0; color: #666; }
        .status-sent { background-color: #e3f2fd; color: #1976d2; }
        .status-approved { background-color: #e8f5e9; color: #388e3c; }
        .status-rejected { background-color: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUOTE</h1>
        <p><strong>Quote #<?php echo htmlspecialchars($quote->quote_number); ?></strong></p>
        <p>Date: <?php echo $quote->date_quote ? $quote->date_quote->format('d-m-Y') : date('d-m-Y'); ?></p>
        <?php if ($quote->date_expires): ?>
        <p><small>Expires: <?php echo $quote->date_expires->format('d-m-Y'); ?></small></p>
        <?php endif; ?>
        <p>
            <span class="status-badge status-<?php echo strtolower($quote->status->status_name ?? 'draft'); ?>">
                <?php echo htmlspecialchars(strtoupper($quote->status->status_name ?? 'DRAFT')); ?>
            </span>
        </p>
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
                </div>
            </td>
            <td>
                <div class="info-section">
                    <h3>Quote For:</h3>
                    <p><strong><?php echo htmlspecialchars($quote->client->client_name); ?></strong></p>
                    <?php if ($quote->client->client_address_1): ?>
                    <p><?php echo htmlspecialchars($quote->client->client_address_1); ?></p>
                    <?php endif; ?>
                    <?php if ($quote->client->client_postal_code || $quote->client->client_city): ?>
                    <p><?php echo htmlspecialchars(($quote->client->client_postal_code ?? '') . ' ' . ($quote->client->client_city ?? '')); ?></p>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td><?php echo htmlspecialchars($quote->currency ?? 'EUR'); ?> <?php echo number_format($quote->subtotal ?? 0, 2); ?></td>
            </tr>
            <?php if ($quote->discount_amount): ?>
            <tr>
                <td>Discount:</td>
                <td>-<?php echo htmlspecialchars($quote->currency ?? 'EUR'); ?> <?php echo number_format($quote->discount_amount, 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Tax:</td>
                <td><?php echo htmlspecialchars($quote->currency ?? 'EUR'); ?> <?php echo number_format($quote->tax_amount ?? 0, 2); ?></td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td><?php echo htmlspecialchars($quote->currency ?? 'EUR'); ?> <?php echo number_format($quote->total_amount ?? 0, 2); ?></td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <?php if ($quote->terms): ?>
    <div class="info-section" style="margin-top: 40px;">
        <h3>Terms & Conditions:</h3>
        <p><?php echo nl2br(htmlspecialchars($quote->terms)); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Generated on <?php echo date('d-m-Y H:i'); ?></p>
        <p><small>This quote is valid until <?php echo $quote->date_expires ? $quote->date_expires->format('d-m-Y') : 'further notice'; ?></small></p>
    </div>
</body>
</html>
