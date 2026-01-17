<?php

namespace App\Services\Peppol;

use App\Enums\PeppolProvider;
use InvalidArgumentException;

/**
 * Factory for creating Peppol provider clients
 * 
 * Instantiates the correct provider client based on the provider enum.
 */
class PeppolProviderFactory
{
    public function __construct(
        private ApiClient $apiClient
    ) {}

    /**
     * Create a provider client instance
     */
    public function create(PeppolProvider $provider): StoreCoveClient|LetsPeppolClient|PeppyrusClient|EInvoicingBeClient
    {
        return match ($provider) {
            PeppolProvider::STORECOVE => new StoreCoveClient(clone $this->apiClient),
            PeppolProvider::LETSPEPPOL => new LetsPeppolClient(clone $this->apiClient),
            PeppolProvider::PEPPYRUS => new PeppyrusClient(clone $this->apiClient),
            PeppolProvider::EINVOICING_BE => new EInvoicingBeClient(clone $this->apiClient),
            default => throw new InvalidArgumentException("Unsupported provider: {$provider->value}"),
        };
    }

    /**
     * Create a provider client from string
     */
    public function createFromString(string $providerName): StoreCoveClient|LetsPeppolClient|PeppyrusClient|EInvoicingBeClient
    {
        $provider = PeppolProvider::from($providerName);
        return $this->create($provider);
    }
}
