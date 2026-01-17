<?php

namespace App\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCoveClient;

/**
 * StoreCove Documents Endpoint
 * 
 * Handles document submission, retrieval, status checking, and cancellation.
 */
class DocumentsEndpoint
{
    public function __construct(
        private StoreCoveClient $client
    ) {}

    /**
     * Submit a document to StoreCove
     *
     * @param array $documentData Document data including UBL XML and metadata
     * @return array Response with document ID and submission status
     */
    public function submitDocument(array $documentData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v2/document_submissions',
            $documentData
        );
    }

    /**
     * Get document status
     *
     * @param string $documentId Document identifier
     * @return array Document status information
     */
    public function getDocumentStatus(string $documentId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/document_submissions/{$documentId}"
        );
    }

    /**
     * Get full document details
     *
     * @param string $documentId Document identifier
     * @return array Complete document data
     */
    public function getDocument(string $documentId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/document_submissions/{$documentId}/document"
        );
    }

    /**
     * Cancel a submitted document
     *
     * @param string $documentId Document identifier
     * @return array Cancellation confirmation
     */
    public function cancelDocument(string $documentId): array
    {
        return $this->client->request(
            HttpMethod::DELETE->value,
            "/api/v2/document_submissions/{$documentId}"
        );
    }
}
