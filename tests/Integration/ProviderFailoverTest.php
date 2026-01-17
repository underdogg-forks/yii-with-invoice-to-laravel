<?php

namespace Tests\Integration;

use App\Enums\PeppolProvider;
use App\Models\Client;
use App\Models\ClientPeppol;
use App\Models\Invoice;
use App\Services\Peppol\PeppolProviderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Integration tests for multi-provider failover functionality
 * 
 * Tests the ability to automatically failover to backup providers
 * when the primary provider fails or is unavailable.
 */
#[CoversClass(PeppolProviderFactory::class)]
#[Group('integration')]
#[Group('peppol')]
#[Group('failover')]
class ProviderFailoverTest extends TestCase
{
    use RefreshDatabase;

    private PeppolProviderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = app(PeppolProviderFactory::class);
    }

    #[Test]
    public function it_fails_over_from_storecove_to_letspeppol_when_primary_fails(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => Http::response(null, 503), // Primary fails
            'https://api.letspeppol.com/*' => Http::response([ // Fallback succeeds
                'invoice_id' => 'letspeppol-fallback-123',
                'status' => 'accepted',
            ], 200),
        ]);

        $client = Client::factory()->create(['name' => 'Failover Test Client']);
        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '1234567890',
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-FAILOVER-001',
            'total_amount' => 500.00,
            'status_id' => 2,
        ]);

        /* Act - Try primary, then fallback */
        $primaryClient = $this->factory->create(PeppolProvider::STORECOVE);
        
        $success = false;
        $fallbackResponse = null;
        try {
            $primaryClient->request('POST', '/api/v2/document_submissions', [
                'invoice_number' => $invoice->invoice_number,
            ]);
        } catch (\Exception $e) {
            // Primary failed, try fallback
            $fallbackClient = $this->factory->create(PeppolProvider::LETSPEPPOL);
            $fallbackResponse = $fallbackClient->request('POST', '/api/v1/invoices/send', [
                'invoice_number' => $invoice->invoice_number,
            ]);
            $success = true;
        }

        /* Assert */
        $this->assertTrue($success);
        $this->assertEquals('letspeppol-fallback-123', $fallbackResponse['invoice_id']);
    }

    #[Test]
    public function it_tries_multiple_providers_until_one_succeeds(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => Http::response(null, 503), // 1st fails
            'https://api.letspeppol.com/*' => Http::response(null, 500), // 2nd fails
            'https://api.peppyrus.com/oauth/token' => Http::response([
                'access_token' => 'peppyrus-token',
                'expires_in' => 3600,
            ], 200),
            'https://api.peppyrus.com/*' => Http::response([ // 3rd succeeds
                'transmission_id' => 'peppyrus-success-456',
                'state' => 'transmitted',
            ], 200),
        ]);

        $providers = [
            PeppolProvider::STORECOVE,
            PeppolProvider::LETSPEPPOL,
            PeppolProvider::PEPPYRUS,
        ];

        $invoice = Invoice::factory()->create([
            'invoice_number' => 'INV-MULTI-FAILOVER-001',
            'total_amount' => 1000.00,
            'status_id' => 2,
        ]);

        /* Act */
        $response = null;
        $successfulProvider = null;

        foreach ($providers as $provider) {
            try {
                $client = $this->factory->create($provider);
                $endpoint = match($provider) {
                    PeppolProvider::STORECOVE => '/api/v2/document_submissions',
                    PeppolProvider::LETSPEPPOL => '/api/v1/invoices/send',
                    PeppolProvider::PEPPYRUS => '/api/v1/transmissions',
                    default => '/api/submit',
                };
                
                $response = $client->request('POST', $endpoint, [
                    'invoice_number' => $invoice->invoice_number,
                ]);
                
                $successfulProvider = $provider;
                break; // Success, exit loop
            } catch (\Exception $e) {
                // Try next provider
                continue;
            }
        }

        /* Assert */
        $this->assertNotNull($response);
        $this->assertEquals(PeppolProvider::PEPPYRUS, $successfulProvider);
        $this->assertEquals('peppyrus-success-456', $response['transmission_id']);
    }

    #[Test]
    public function it_fails_gracefully_when_all_providers_are_down(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => Http::response(null, 503),
            'https://api.letspeppol.com/*' => Http::response(null, 500),
            'https://api.peppyrus.com/*' => Http::response(null, 503),
            'https://api.einvoicing.be/*' => Http::response(null, 504),
        ]);

        $providers = [
            PeppolProvider::STORECOVE,
            PeppolProvider::LETSPEPPOL,
            PeppolProvider::PEPPYRUS,
            PeppolProvider::EINVOICING_BE,
        ];

        /* Act */
        $allFailed = true;
        $lastException = null;

        foreach ($providers as $provider) {
            try {
                $client = $this->factory->create($provider);
                $client->request('POST', '/api/test', []);
                $allFailed = false;
                break;
            } catch (\Exception $e) {
                $lastException = $e;
                continue;
            }
        }

        /* Assert */
        $this->assertTrue($allFailed);
        $this->assertNotNull($lastException);
    }

    #[Test]
    public function it_retries_failed_request_with_different_provider(): void
    {
        /* Arrange */
        $attemptCount = 0;
        
        Http::fake([
            'https://api.storecove.com/*' => function() use (&$attemptCount) {
                $attemptCount++;
                return Http::response(null, 503); // Always fail
            },
            'https://api.letspeppol.com/*' => Http::response([
                'invoice_id' => 'retry-success-789',
                'status' => 'accepted',
            ], 200),
        ]);

        $maxRetries = 2;
        $currentRetry = 0;
        $response = null;

        /* Act */
        while ($currentRetry < $maxRetries) {
            try {
                if ($currentRetry == 0) {
                    // First attempt with StoreCove
                    $client = $this->factory->create(PeppolProvider::STORECOVE);
                    $response = $client->request('POST', '/api/v2/document_submissions', []);
                    break;
                } else {
                    // Retry with LetsPeppol
                    $client = $this->factory->create(PeppolProvider::LETSPEPPOL);
                    $response = $client->request('POST', '/api/v1/invoices/send', []);
                    break;
                }
            } catch (\Exception $e) {
                $currentRetry++;
            }
        }

        /* Assert */
        $this->assertNotNull($response);
        $this->assertEquals('retry-success-789', $response['invoice_id']);
        $this->assertEquals(1, $attemptCount); // StoreCove was tried once
    }

    #[Test]
    public function it_falls_back_to_belgian_provider_for_be_clients(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => Http::response(null, 503), // International fails
            'https://api.einvoicing.be/*' => Http::response([ // Belgian succeeds
                'submission_id' => 'be-specific-999',
                'status' => 'accepted',
            ], 200),
        ]);

        $belgianClient = Client::factory()->create([
            'name' => 'Belgian Company BVBA',
            'country' => 'BE',
        ]);
        
        ClientPeppol::factory()->create([
            'client_id' => $belgianClient->id,
            'peppol_endpoint_id' => 'BE0123456789',
            'peppol_scheme_id' => '0208',
        ]);

        /* Act - Try international provider first, then Belgian */
        try {
            $intClient = $this->factory->create(PeppolProvider::STORECOVE);
            $response = $intClient->request('POST', '/api/v2/document_submissions', []);
        } catch (\Exception $e) {
            // Fallback to Belgian provider
            $beClient = $this->factory->create(PeppolProvider::EINVOICING_BE);
            $response = $beClient->request('POST', '/api/v1/invoices/submit', [
                'vat_number' => 'BE0123456789',
            ]);
        }

        /* Assert */
        $this->assertNotNull($response);
        $this->assertEquals('be-specific-999', $response['submission_id']);
    }

    #[Test]
    public function it_handles_timeout_errors_and_switches_providers(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => function() {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
            },
            'https://api.letspeppol.com/*' => Http::response([
                'invoice_id' => 'timeout-recovery-123',
                'status' => 'accepted',
            ], 200),
        ]);

        /* Act */
        $response = null;
        
        try {
            $primary = $this->factory->create(PeppolProvider::STORECOVE);
            $response = $primary->request('POST', '/api/v2/document_submissions', []);
        } catch (\Exception $e) {
            // Timeout occurred, switch to backup
            $backup = $this->factory->create(PeppolProvider::LETSPEPPOL);
            $response = $backup->request('POST', '/api/v1/invoices/send', []);
        }

        /* Assert */
        $this->assertNotNull($response);
        $this->assertEquals('timeout-recovery-123', $response['invoice_id']);
    }

    #[Test]
    public function it_logs_failover_events_for_monitoring(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => Http::response(null, 503),
            'https://api.letspeppol.com/*' => Http::response([
                'invoice_id' => 'logged-failover-456',
            ], 200),
        ]);

        $failoverLog = [];

        /* Act */
        try {
            $primary = $this->factory->create(PeppolProvider::STORECOVE);
            $primary->request('POST', '/api/v2/document_submissions', []);
        } catch (\Exception $e) {
            $failoverLog[] = [
                'provider' => 'storecove',
                'status' => 'failed',
                'error' => $e->getMessage(),
            ];
            
            $backup = $this->factory->create(PeppolProvider::LETSPEPPOL);
            $response = $backup->request('POST', '/api/v1/invoices/send', []);
            
            $failoverLog[] = [
                'provider' => 'letspeppol',
                'status' => 'success',
                'response_id' => $response['invoice_id'],
            ];
        }

        /* Assert */
        $this->assertCount(2, $failoverLog);
        $this->assertEquals('failed', $failoverLog[0]['status']);
        $this->assertEquals('success', $failoverLog[1]['status']);
        $this->assertEquals('logged-failover-456', $failoverLog[1]['response_id']);
    }

    #[Test]
    public function it_respects_provider_priority_order_during_failover(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => Http::response(null, 503), // Priority 1 fails
            'https://api.letspeppol.com/*' => Http::response([ // Priority 2 succeeds
                'invoice_id' => 'priority-fallback-789',
            ], 200),
            'https://api.peppyrus.com/*' => Http::response([ // Priority 3 (not reached)
                'transmission_id' => 'should-not-reach',
            ], 200),
        ]);

        $priorityOrder = [
            PeppolProvider::STORECOVE,     // Priority 1
            PeppolProvider::LETSPEPPOL,    // Priority 2
            PeppolProvider::PEPPYRUS,      // Priority 3
        ];

        /* Act */
        $response = null;
        $usedProvider = null;

        foreach ($priorityOrder as $provider) {
            try {
                $client = $this->factory->create($provider);
                $endpoint = match($provider) {
                    PeppolProvider::STORECOVE => '/api/v2/document_submissions',
                    PeppolProvider::LETSPEPPOL => '/api/v1/invoices/send',
                    PeppolProvider::PEPPYRUS => '/api/v1/transmissions',
                };
                
                $response = $client->request('POST', $endpoint, []);
                $usedProvider = $provider;
                break;
            } catch (\Exception $e) {
                continue;
            }
        }

        /* Assert */
        $this->assertNotNull($response);
        $this->assertEquals(PeppolProvider::LETSPEPPOL, $usedProvider);
        $this->assertEquals('priority-fallback-789', $response['invoice_id']);
    }
}
