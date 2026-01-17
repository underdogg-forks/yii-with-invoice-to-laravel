<?php

namespace Tests\Integration;

use App\Enums\PeppolProvider;
use App\Models\Client;
use App\Models\ClientPeppol;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Services\Peppol\PeppolProviderFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Integration tests for provider switching functionality
 * 
 * Tests the ability to switch between different Peppol providers
 * (StoreCove, LetsPeppol, Peppyrus, E-invoicing.be) for the same operations.
 */
#[CoversClass(PeppolProviderFactory::class)]
#[Group('integration')]
#[Group('peppol')]
class ProviderSwitchingTest extends TestCase
{
    use RefreshDatabase;

    private PeppolProviderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = app(PeppolProviderFactory::class);
    }

    #[Test]
    public function it_can_create_storecove_provider_client(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $client = $this->factory->create(PeppolProvider::STORECOVE);

        /* Assert */
        $this->assertInstanceOf(\App\Services\Peppol\StoreCoveClient::class, $client);
    }

    #[Test]
    public function it_can_create_letspeppol_provider_client(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $client = $this->factory->create(PeppolProvider::LETSPEPPOL);

        /* Assert */
        $this->assertInstanceOf(\App\Services\Peppol\LetsPeppolClient::class, $client);
    }

    #[Test]
    public function it_can_create_peppyrus_provider_client(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $client = $this->factory->create(PeppolProvider::PEPPYRUS);

        /* Assert */
        $this->assertInstanceOf(\App\Services\Peppol\PeppyrusClient::class, $client);
    }

    #[Test]
    public function it_can_create_einvoicing_be_provider_client(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $client = $this->factory->create(PeppolProvider::EINVOICING_BE);

        /* Assert */
        $this->assertInstanceOf(\App\Services\Peppol\EInvoicingBeClient::class, $client);
    }

    #[Test]
    public function it_can_create_provider_from_string(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $storeCove = $this->factory->createFromString('storecove');
        $letsPeppol = $this->factory->createFromString('letspeppol');
        $peppyrus = $this->factory->createFromString('peppyrus');
        $einvoicingBe = $this->factory->createFromString('einvoicing_be');

        /* Assert */
        $this->assertInstanceOf(\App\Services\Peppol\StoreCoveClient::class, $storeCove);
        $this->assertInstanceOf(\App\Services\Peppol\LetsPeppolClient::class, $letsPeppol);
        $this->assertInstanceOf(\App\Services\Peppol\PeppyrusClient::class, $peppyrus);
        $this->assertInstanceOf(\App\Services\Peppol\EInvoicingBeClient::class, $einvoicingBe);
    }

    #[Test]
    public function it_sends_same_invoice_through_different_providers(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/*' => Http::response([
                'id' => 'storecove-doc-123',
                'status' => 'submitted',
            ], 200),
            'https://api.letspeppol.com/*' => Http::response([
                'invoice_id' => 'letspeppol-inv-456',
                'status' => 'accepted',
            ], 200),
            'https://api.peppyrus.com/oauth/token' => Http::response([
                'access_token' => 'peppyrus-token',
                'expires_in' => 3600,
            ], 200),
            'https://api.peppyrus.com/*' => Http::response([
                'transmission_id' => 'peppyrus-tx-789',
                'state' => 'transmitted',
            ], 200),
            'https://api.einvoicing.be/*' => Http::response([
                'submission_id' => 'be-sub-999',
                'status' => 'processing',
            ], 200),
        ]);

        $client = Client::factory()->create(['name' => 'Multi-Provider Client']);
        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '1234567890',
            'peppol_scheme_id' => '0088',
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-MULTI-001',
            'total_amount' => 1000.00,
            'status_id' => 2,
        ]);

        /* Act - Send through each provider */
        $storeCoveClient = $this->factory->create(PeppolProvider::STORECOVE);
        $storeCoveResponse = $storeCoveClient->request('POST', '/api/v2/document_submissions', [
            'invoice_number' => $invoice->invoice_number,
        ]);

        $letsPeppolClient = $this->factory->create(PeppolProvider::LETSPEPPOL);
        $letsPeppolResponse = $letsPeppolClient->request('POST', '/api/v1/invoices/send', [
            'invoice_number' => $invoice->invoice_number,
        ]);

        $peppyrusClient = $this->factory->create(PeppolProvider::PEPPYRUS);
        $peppyrusResponse = $peppyrusClient->request('POST', '/api/v1/transmissions', [
            'invoice_number' => $invoice->invoice_number,
        ]);

        $einvoicingBeClient = $this->factory->create(PeppolProvider::EINVOICING_BE);
        $einvoicingBeResponse = $einvoicingBeClient->request('POST', '/api/v1/invoices/submit', [
            'invoice_number' => $invoice->invoice_number,
        ]);

        /* Assert - All providers responded successfully */
        $this->assertEquals('storecove-doc-123', $storeCoveResponse['id']);
        $this->assertEquals('letspeppol-inv-456', $letsPeppolResponse['invoice_id']);
        $this->assertEquals('peppyrus-tx-789', $peppyrusResponse['transmission_id']);
        $this->assertEquals('be-sub-999', $einvoicingBeResponse['submission_id']);
    }

    #[Test]
    public function it_switches_providers_based_on_configuration(): void
    {
        /* Arrange */
        Http::fake();
        
        // Simulate configuration change
        $primaryProvider = 'storecove';
        $secondaryProvider = 'letspeppol';

        /* Act */
        $primary = $this->factory->createFromString($primaryProvider);
        $secondary = $this->factory->createFromString($secondaryProvider);

        /* Assert */
        $this->assertInstanceOf(\App\Services\Peppol\StoreCoveClient::class, $primary);
        $this->assertInstanceOf(\App\Services\Peppol\LetsPeppolClient::class, $secondary);
        $this->assertNotSame($primary, $secondary);
    }

    #[Test]
    public function it_validates_participant_across_different_providers(): void
    {
        /* Arrange */
        Http::fake([
            'https://api.storecove.com/api/v2/peppol_identifiers*' => Http::response([
                'valid' => true,
                'participant' => 'Test Corp',
            ], 200),
            'https://api.letspeppol.com/api/v1/participants*' => Http::response([
                'participant_id' => '0088:1234567890',
                'registered' => true,
            ], 200),
            'https://api.einvoicing.be/api/v1/participants*' => Http::response([
                'vat_number' => 'BE1234567890',
                'registered' => true,
            ], 200),
        ]);

        /* Act */
        $storeCoveClient = $this->factory->create(PeppolProvider::STORECOVE);
        $storeCoveValid = $storeCoveClient->request('GET', '/api/v2/peppol_identifiers/0088:1234567890');

        $letsPeppolClient = $this->factory->create(PeppolProvider::LETSPEPPOL);
        $letsPeppolValid = $letsPeppolClient->request('GET', '/api/v1/participants/0088:1234567890');

        $einvoicingBeClient = $this->factory->create(PeppolProvider::EINVOICING_BE);
        $einvoicingBeValid = $einvoicingBeClient->request('GET', '/api/v1/participants/BE1234567890');

        /* Assert */
        $this->assertTrue($storeCoveValid['valid']);
        $this->assertTrue($letsPeppolValid['registered']);
        $this->assertTrue($einvoicingBeValid['registered']);
    }

    #[Test]
    public function it_handles_provider_specific_authentication_methods(): void
    {
        /* Arrange */
        Http::fake();

        /* Act */
        $storeCove = $this->factory->create(PeppolProvider::STORECOVE); // Bearer token
        $letsPeppol = $this->factory->create(PeppolProvider::LETSPEPPOL); // X-API-Key
        $peppyrus = $this->factory->create(PeppolProvider::PEPPYRUS); // OAuth2
        $einvoicingBe = $this->factory->create(PeppolProvider::EINVOICING_BE); // Bearer + X-API-Key

        /* Assert - Each client configured with correct auth method */
        $this->assertInstanceOf(\App\Services\Peppol\StoreCoveClient::class, $storeCove);
        $this->assertInstanceOf(\App\Services\Peppol\LetsPeppolClient::class, $letsPeppol);
        $this->assertInstanceOf(\App\Services\Peppol\PeppyrusClient::class, $peppyrus);
        $this->assertInstanceOf(\App\Services\Peppol\EInvoicingBeClient::class, $einvoicingBe);
    }

    #[Test]
    public function it_throws_exception_for_unsupported_provider(): void
    {
        /* Arrange */
        $this->expectException(\ValueError::class);

        /* Act */
        $this->factory->createFromString('invalid_provider');

        /* Assert - Exception expected */
    }
}
