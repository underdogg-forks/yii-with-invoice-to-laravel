<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales Order</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Sales Order <?= htmlspecialchars($salesOrder->so_number) ?></h2>
        
        <p>Dear <?= htmlspecialchars($salesOrder->client->name) ?>,</p>
        
        <p>Please find attached sales order <strong><?= htmlspecialchars($salesOrder->so_number) ?></strong> 
           dated <?= htmlspecialchars($salesOrder->so_date?->format('Y-m-d')) ?>.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>SO Number:</strong> <?= htmlspecialchars($salesOrder->so_number) ?></p>
            <p style="margin: 5px 0;"><strong>Date:</strong> <?= htmlspecialchars($salesOrder->so_date?->format('Y-m-d')) ?></p>
            <p style="margin: 5px 0;"><strong>Status:</strong> <?= htmlspecialchars($salesOrder->status?->name ?? 'Pending') ?></p>
            <p style="margin: 5px 0;"><strong>Total Amount:</strong> <?= htmlspecialchars($salesOrder->currency) ?> <?= number_format($salesOrder->total_amount, 2) ?></p>
        </div>
        
        <p>We will process your order and keep you updated on the progress.</p>
        
        <p>If you have any questions regarding this sales order, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <?= htmlspecialchars(config('app.name')) ?></p>
    </div>
</body>
</html>
