<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Quote <?= htmlspecialchars($quote->quote_number) ?></h2>
        
        <p>Dear <?= htmlspecialchars($quote->client->name) ?>,</p>
        
        <p>Please find attached quote <strong><?= htmlspecialchars($quote->quote_number) ?></strong> 
           dated <?= htmlspecialchars($quote->quote_date?->format('Y-m-d')) ?>.</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 5px 0;"><strong>Quote Number:</strong> <?= htmlspecialchars($quote->quote_number) ?></p>
            <p style="margin: 5px 0;"><strong>Date:</strong> <?= htmlspecialchars($quote->quote_date?->format('Y-m-d')) ?></p>
            <p style="margin: 5px 0;"><strong>Valid Until:</strong> <?= htmlspecialchars($quote->expires_at?->format('Y-m-d')) ?></p>
            <p style="margin: 5px 0;"><strong>Total Amount:</strong> <?= htmlspecialchars($quote->currency) ?> <?= number_format($quote->total_amount, 2) ?></p>
        </div>
        
        <p>This quote is valid until <?= htmlspecialchars($quote->expires_at?->format('Y-m-d')) ?>. 
           If you have any questions or would like to proceed, please contact us.</p>
        
        <p>Best regards,<br>
        <?= htmlspecialchars(config('app.name')) ?></p>
    </div>
</body>
</html>
