<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StoreCoveService
{
    private string $apiKey;
    private string $apiUrl;
    private int $timeout;

    public function __construct()
    {
        $this->apiKey = config('storecove.api_key');
        $this->apiUrl = config('storecove.api_url');
        $this->timeout = config('storecove.timeout', 30);
    }

    /**
     * Send invoice via StoreCove Peppol network
     *
     * @param string $ublXml UBL XML content
     * @param string $recipientEndpoint Recipient's Peppol endpoint ID
     * @param string $recipientScheme Recipient's scheme ID (e.g., '0088' for GLN, '0190' for Dutch CoC)
     * @return array Response from StoreCove API
     * @throws \Exception
     */
    public function sendInvoice(string $ublXml, string $recipientEndpoint, string $recipientScheme = '0088'): array
    {
        $payload = [
            'document' => [
                'documentType' => 'invoice',
                'invoice' => base64_encode($ublXml),
            ],
            'routing' => [
                'eIdentifiers' => [
                    [
                        'scheme' => $recipientScheme,
                        'id' => $recipientEndpoint,
                    ],
                ],
            ],
        ];

        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post($this->apiUrl . '/document_submissions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('StoreCove invoice submission successful', [
                    'document_id' => $data['id'] ?? null,
                    'recipient' => $recipientEndpoint,
                ]);
                
                return [
                    'success' => true,
                    'document_id' => $data['id'] ?? null,
                    'guid' => $data['guid'] ?? null,
                    'response' => $data,
                ];
            }

            $error = $response->json();
            Log::error('StoreCove invoice submission failed', [
                'status' => $response->status(),
                'error' => $error,
                'recipient' => $recipientEndpoint,
            ]);

            throw new \Exception('StoreCove API error: ' . ($error['message'] ?? 'Unknown error'));
        } catch (\Exception $e) {
            Log::error('StoreCove API exception', [
                'error' => $e->getMessage(),
                'recipient' => $recipientEndpoint,
            ]);
            
            throw $e;
        }
    }

    /**
     * Get document status from StoreCove
     *
     * @param string $documentId StoreCove document ID
     * @return array Document status information
     * @throws \Exception
     */
    public function getDocumentStatus(string $documentId): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->apiUrl . '/document_submissions/' . $documentId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to get document status from StoreCove');
        } catch (\Exception $e) {
            Log::error('StoreCove get status exception', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Validate Peppol endpoint
     *
     * @param string $endpointId Peppol endpoint ID
     * @param string $scheme Scheme ID
     * @return bool
     */
    public function validateEndpoint(string $endpointId, string $scheme = '0088'): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept' => 'application/json',
                ])
                ->get($this->apiUrl . '/peppol_identifiers', [
                    'scheme' => $scheme,
                    'identifier' => $endpointId,
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('StoreCove endpoint validation exception', [
                'endpoint' => $endpointId,
                'scheme' => $scheme,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
}
