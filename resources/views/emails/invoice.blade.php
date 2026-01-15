<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4;">
    <div style="max-width: 600px; margin: 20px auto; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <!-- Header -->
        <div style="background-color: #2c3e50; color: white; padding: 30px 20px; text-align: center;">
            <h1 style="margin: 0; font-size: 28px;">Invoice</h1>
            <p style="margin: 10px 0 0 0; font-size: 18px; opacity: 0.9;">{{ $invoice->invoice_number }}</p>
        </div>
        
        <!-- Content -->
        <div style="padding: 30px 20px;">
            <p style="margin-top: 0;">Dear {{ $invoice->client->name ?? 'Valued Customer' }},</p>
            
            <p>Please find attached invoice <strong>{{ $invoice->invoice_number }}</strong> 
               dated {{ $invoice->invoice_date_created?->format('d M Y') ?? date('d M Y') }}.</p>
            
            <!-- Invoice Details Box -->
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 6px; margin: 25px 0; border-left: 4px solid #2c3e50;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #555;">Invoice Number:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #555;">Date:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ $invoice->invoice_date_created?->format('d M Y') ?? date('d M Y') }}</td>
                    </tr>
                    @if($invoice->invoice_date_due)
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; color: #555;">Due Date:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ $invoice->invoice_date_due->format('d M Y') }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 2px solid #dee2e6;">
                        <td style="padding: 12px 0 0 0; font-weight: bold; font-size: 16px; color: #2c3e50;">Total Amount:</td>
                        <td style="padding: 12px 0 0 0; text-align: right; font-weight: bold; font-size: 16px; color: #2c3e50;">
                            {{ $invoice->invoice_currency ?? '$' }} {{ number_format($invoice->invoice_total ?? 0, 2) }}
                        </td>
                    </tr>
                </table>
            </div>
            
            @if($invoice->invoice_notes)
            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <p style="margin: 0; color: #856404;"><strong>Note:</strong></p>
                <p style="margin: 5px 0 0 0; color: #856404;">{{ $invoice->invoice_notes }}</p>
            </div>
            @endif
            
            <p>If you have any questions regarding this invoice, please don't hesitate to contact us.</p>
            
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
