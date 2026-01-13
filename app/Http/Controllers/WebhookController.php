<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle StoreCove webhook
     */
    public function storecove(Request $request)
    {
        try {
            $payload = $request->all();
            
            Log::info('StoreCove webhook received', ['payload' => $payload]);
            
            // Verify webhook signature if configured
            if (config('storecove.webhook_secret')) {
                $signature = $request->header('X-StoreCove-Signature');
                if (!$this->verifyStoreCoveSignature($payload, $signature)) {
                    Log::warning('Invalid StoreCove webhook signature');
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            }
            
            // Process webhook event
            $eventType = $payload['event_type'] ?? null;
            
            switch ($eventType) {
                case 'invoice.delivered':
                    $this->handleInvoiceDelivered($payload);
                    break;
                    
                case 'invoice.failed':
                    $this->handleInvoiceFailed($payload);
                    break;
                    
                case 'invoice.read':
                    $this->handleInvoiceRead($payload);
                    break;
                    
                default:
                    Log::info('Unknown StoreCove event type', ['event_type' => $eventType]);
            }
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('StoreCove webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal error'], 500);
        }
    }

    /**
     * Verify StoreCove webhook signature
     */
    private function verifyStoreCoveSignature(array $payload, ?string $signature): bool
    {
        if (!$signature) {
            return false;
        }
        
        $secret = config('storecove.webhook_secret');
        $expectedSignature = hash_hmac('sha256', json_encode($payload), $secret);
        
        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle invoice delivered event
     */
    private function handleInvoiceDelivered(array $payload): void
    {
        $documentId = $payload['document_id'] ?? null;
        
        if ($documentId) {
            // Update invoice status to delivered
            Log::info('Invoice delivered', ['document_id' => $documentId]);
            
            // TODO: Update invoice model with delivery status
            // $invoice = Invoice::where('peppol_document_id', $documentId)->first();
            // if ($invoice) {
            //     $invoice->update(['peppol_status' => 'delivered', 'delivered_at' => now()]);
            // }
        }
    }

    /**
     * Handle invoice failed event
     */
    private function handleInvoiceFailed(array $payload): void
    {
        $documentId = $payload['document_id'] ?? null;
        $error = $payload['error'] ?? 'Unknown error';
        
        if ($documentId) {
            Log::error('Invoice delivery failed', [
                'document_id' => $documentId,
                'error' => $error
            ]);
            
            // TODO: Update invoice model with failure status
            // $invoice = Invoice::where('peppol_document_id', $documentId)->first();
            // if ($invoice) {
            //     $invoice->update([
            //         'peppol_status' => 'failed',
            //         'peppol_error' => $error,
            //         'failed_at' => now()
            //     ]);
            // }
        }
    }

    /**
     * Handle invoice read event
     */
    private function handleInvoiceRead(array $payload): void
    {
        $documentId = $payload['document_id'] ?? null;
        
        if ($documentId) {
            Log::info('Invoice read by recipient', ['document_id' => $documentId]);
            
            // TODO: Update invoice model with read status
            // $invoice = Invoice::where('peppol_document_id', $documentId)->first();
            // if ($invoice) {
            //     $invoice->update(['peppol_status' => 'read', 'read_at' => now()]);
            // }
        }
    }
}
