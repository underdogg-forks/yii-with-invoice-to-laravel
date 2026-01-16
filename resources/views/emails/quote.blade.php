<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote {{ $quote->quote_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 20px auto; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #17a2b8; color: white; padding: 30px 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px;">Quote</h1>
            <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">{{ $quote->quote_number }}</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px 20px;">
            <p style="margin-top: 0;">Dear {{ $quote->client->name ?? 'Valued Customer' }},</p>
            
            <p>Thank you for your interest. Please find attached quote <strong>{{ $quote->quote_number }}</strong> 
               dated {{ $quote->quote_date?->format('d M Y') ?? date('d M Y') }}.</p>
            
            <!-- Quote Details Box -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 25px 0; border-left: 4px solid #17a2b8;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #555;">Quote Number:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ $quote->quote_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #555;">Date:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ $quote->quote_date?->format('d M Y') ?? date('d M Y') }}</td>
                    </tr>
                    @if($quote->quote_expires_at)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #555;">Valid Until:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ $quote->quote_expires_at->format('d M Y') }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 2px solid #dee2e6;">
                        <td style="padding: 12px 0 0 0; font-weight: bold; font-size: 16px; color: #17a2b8;">Total Amount:</td>
                        <td style="padding: 12px 0 0 0; text-align: right; font-weight: bold; font-size: 16px; color: #17a2b8;">
                            {{ $quote->quote_currency ?? '$' }} {{ number_format($quote->quote_total ?? 0, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if($quote->quote_notes)
            <div style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; color: #0c5460;"><strong>Note:</strong></p>
                <p style="margin: 5px 0 0 0; color: #0c5460;">{{ $quote->quote_notes }}</p>
            </div>
            @endif
            
            <p>This quote is valid for the period specified above. If you have any questions or would like to proceed, please let us know.</p>
            
            <p style="margin-bottom: 0;">Best regards,<br>
            <strong>{{ config('app.name') }}</strong></p>
        </div>
        
        <!-- Footer -->
        <div style="background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;">
            <p style="margin: 0; font-size: 12px; color: #6c757d;">
                This is an automated email. Please do not reply directly to this message.
            </p>
            @if(config('peppol.supplier.email'))
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #6c757d;">
                Contact us: {{ config('peppol.supplier.email') }}
            </p>
            @endif
        </div>
    </div>
</body>
</html>
