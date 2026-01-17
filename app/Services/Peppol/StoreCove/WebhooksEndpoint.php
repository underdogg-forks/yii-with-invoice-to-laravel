<?php

namespace App\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCoveClient;

/**
 * StoreCove Webhooks Endpoint
 * 
 * Manages webhook subscriptions for event notifications.
 */
class WebhooksEndpoint
{
    public function __construct(
        private StoreCoveClient $client
    ) {}

    /**
     * Create a webhook subscription
     *
     * @param array $webhookData Webhook configuration (url, events, etc.)
     * @return array Created webhook with ID
     */
    public function createWebhook(array $webhookData): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            '/api/v2/webhooks',
            $webhookData
        );
    }

    /**
     * Get webhook details
     *
     * @param string $webhookId Webhook identifier
     * @return array Webhook configuration
     */
    public function getWebhook(string $webhookId): array
    {
        return $this->client->request(
            HttpMethod::GET->value,
            "/api/v2/webhooks/{$webhookId}"
        );
    }

    /**
     * Delete a webhook subscription
     *
     * @param string $webhookId Webhook identifier
     * @return array Deletion confirmation
     */
    public function deleteWebhook(string $webhookId): array
    {
        return $this->client->request(
            HttpMethod::DELETE->value,
            "/api/v2/webhooks/{$webhookId}"
        );
    }

    /**
     * Test webhook delivery
     *
     * @param string $webhookId Webhook identifier
     * @return array Test result
     */
    public function testWebhook(string $webhookId): array
    {
        return $this->client->request(
            HttpMethod::POST->value,
            "/api/v2/webhooks/{$webhookId}/test"
        );
    }
}
