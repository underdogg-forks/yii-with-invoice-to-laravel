<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Invoice <?= htmlspecialchars($invoice->invoice_number) ?></h2>
        
        <p>Dear <?= htmlspecialchars($invoice->client->name) ?>,</p>
        
        <p>Please find attached invoice <strong><?= htmlspecialchars($invoice->invoice_number) ?></strong> 
           dated <?= htmlspecialchars($invoice->invoice_date?->format('Y-m-d')) ?>.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Invoice Number:</strong> <?= htmlspecialchars($invoice->invoice_number) ?></p>
            <p style="margin: 5px 0;"><strong>Date:</strong> <?= htmlspecialchars($invoice->invoice_date?->format('Y-m-d')) ?></p>
            <p style="margin: 5px 0;"><strong>Due Date:</strong> <?= htmlspecialchars($invoice->due_date?->format('Y-m-d')) ?></p>
            <p style="margin: 5px 0;"><strong>Total Amount:</strong> <?= htmlspecialchars($invoice->currency) ?> <?= number_format($invoice->total_amount, 2) ?></p>
        </div>
        
        <p>If you have any questions regarding this invoice, please don't hesitate to contact us.</p>
        
        <p>Best regards,<br>
        <?= htmlspecialchars(config('app.name')) ?></p>
    </div>
</body>
</html>
