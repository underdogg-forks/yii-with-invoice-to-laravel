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
        .status-pending { background-color: #fff3e0; color: #e65100; }
        .status-confirmed { background-color: #e3f2fd; color: #1976d2; }
        .status-completed { background-color: #e8f5e9; color: #388e3c; }
        .status-cancelled { background-color: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <div class="header">
        <h1>SALES ORDER</h1>
        <p><strong>SO #<?php echo htmlspecialchars($salesOrder->so_number); ?></strong></p>
        <p>Date: <?php echo $salesOrder->date_created ? $salesOrder->date_created->format('d-m-Y') : date('d-m-Y'); ?></p>
        <?php if ($salesOrder->quote): ?>
        <p><small>From Quote: <?php echo htmlspecialchars($salesOrder->quote->quote_number); ?></small></p>
        <?php endif; ?>
        <p>
            <span class="status-badge status-<?php echo strtolower($salesOrder->status->status_name ?? 'pending'); ?>">
                <?php echo htmlspecialchars(strtoupper($salesOrder->status->status_name ?? 'PENDING')); ?>
            </span>
        </p>
    </div>

    <table class="two-column">
        <tr>
            <td>
                <div class="info-section">
                    <h3>Seller:</h3>
                    <p><strong><?php echo htmlspecialchars(config('app.name', 'Company Name')); ?></strong></p>
                    <p><?php echo htmlspecialchars(config('peppol.supplier.address.street', '')); ?></p>
                    <p><?php echo htmlspecialchars(config('peppol.supplier.address.postal_code', '') . ' ' . config('peppol.supplier.address.city', '')); ?></p>
                    <p><?php echo htmlspecialchars(config('peppol.supplier.address.country_code', 'NL')); ?></p>
                </div>
            </td>
            <td>
                <div class="info-section">
                    <h3>Customer:</h3>
                    <p><strong><?php echo htmlspecialchars($salesOrder->client->client_name); ?></strong></p>
                    <?php if ($salesOrder->client->client_address_1): ?>
                    <p><?php echo htmlspecialchars($salesOrder->client->client_address_1); ?></p>
                    <?php endif; ?>
                    <?php if ($salesOrder->client->client_postal_code || $salesOrder->client->client_city): ?>
                    <p><?php echo htmlspecialchars(($salesOrder->client->client_postal_code ?? '') . ' ' . ($salesOrder->client->client_city ?? '')); ?></p>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td>Subtotal:</td>
                <td><?php echo htmlspecialchars($salesOrder->currency ?? 'EUR'); ?> <?php echo number_format($salesOrder->subtotal ?? 0, 2); ?></td>
            </tr>
            <?php if ($salesOrder->discount_amount): ?>
            <tr>
                <td>Discount:</td>
                <td>-<?php echo htmlspecialchars($salesOrder->currency ?? 'EUR'); ?> <?php echo number_format($salesOrder->discount_amount, 2); ?></td>
            </tr>
            <?php endif; ?>
            <tr>
                <td>Tax:</td>
                <td><?php echo htmlspecialchars($salesOrder->currency ?? 'EUR'); ?> <?php echo number_format($salesOrder->tax_amount ?? 0, 2); ?></td>
            </tr>
            <tr class="total-row">
                <td>Total:</td>
                <td><?php echo htmlspecialchars($salesOrder->currency ?? 'EUR'); ?> <?php echo number_format($salesOrder->total_amount ?? 0, 2); ?></td>
            </tr>
        </table>
        <div style="clear: both;"></div>
    </div>

    <?php if ($salesOrder->notes): ?>
    <div class="info-section" style="margin-top: 40px;">
        <h3>Notes:</h3>
        <p><?php echo nl2br(htmlspecialchars($salesOrder->notes)); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Generated on <?php echo date('d-m-Y H:i'); ?></p>
    </div>
</body>
</html>
