<?php

namespace Tests\Integration;

use App\Models\Client;
use App\Models\ClientPeppol;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PeppolService;
use App\Services\StoreCoveService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PeppolService::class)]
#[CoversClass(StoreCoveService::class)]
class PeppolWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Tenant $tenant;
    private PeppolService $peppolService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->user = User::factory()->create();
        $this->user->tenants()->attach($this->tenant);
        
        $this->peppolService = app(PeppolService::class);
    }

    #region Integration Tests - Client Peppol Lookup
    /**
     * Scenario 1: Looking up a client's Peppol ID in Filament admin panel
     */
    #[Test]
    #[Group('integration')]
    public function it_retrieves_client_peppol_information_through_filament(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => 'ACME Corporation',
            'email' => 'billing@acme.com',
        ]);

        $clientPeppol = ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '9482348239847000',
            'peppol_scheme_id' => '0088',
            'peppol_participant_name' => 'ACME Corporation B.V.',
        ]);

        /* Act */
        $retrievedClient = Client::with('peppol')->find($client->id);

        /* Assert */
        $this->assertNotNull($retrievedClient);
        $this->assertNotNull($retrievedClient->peppol);
        $this->assertEquals('9482348239847000', $retrievedClient->peppol->peppol_endpoint_id);
        $this->assertEquals('0088', $retrievedClient->peppol->peppol_scheme_id);
        $this->assertEquals('ACME Corporation B.V.', $retrievedClient->peppol->peppol_participant_name);
    }

    #[Test]
    #[Group('integration')]
    public function it_handles_client_without_peppol_configuration(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => 'Non-Peppol Client',
            'email' => 'contact@nonpeppol.com',
        ]);

        /* Act */
        $retrievedClient = Client::with('peppol')->find($client->id);

        /* Assert */
        $this->assertNotNull($retrievedClient);
        $this->assertNull($retrievedClient->peppol);
    }

    #[Test]
    #[Group('integration')]
    public function it_validates_peppol_endpoint_format(): void
    {
        /* Arrange */
        $client = Client::factory()->create(['name' => 'Test Client']);

        /* Act */
        $validPeppol = ClientPeppol::create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '0106:87654321',
            'peppol_scheme_id' => '0106',
            'peppol_participant_name' => 'Valid Corp',
        ]);

        /* Assert */
        $this->assertNotNull($validPeppol);
        $this->assertDatabaseHas('client_peppol', [
            'client_id' => $client->id,
            'peppol_endpoint_id' => '0106:87654321',
        ]);
    }
    #endregion

    #region Integration Tests - Invoice Creation and Peppol Transmission
    /**
     * Scenario 2: Creating invoice with multiple products and sending via Peppol
     */
    #[Test]
    #[Group('integration')]
    public function it_creates_invoice_with_multiple_products_and_sends_via_peppol(): void
    {
        /* Arrange */
        Http::fake([
            '*/document_submissions' => Http::response([
                'id' => 'doc-12345',
                'guid' => 'guid-67890',
                'status' => 'submitted',
            ], 200),
        ]);

        $client = Client::factory()->create(['name' => 'Test Corp']);
        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '9876543210',
            'peppol_scheme_id' => '0088',
        ]);

        $product1 = Product::factory()->create([
            'product_name' => 'Widget A',
            'code' => 'WDG-A',
            'price' => 100.00,
        ]);

        $product2 = Product::factory()->create([
            'product_name' => 'Widget B',
            'code' => 'WDG-B',
            'price' => 150.00,
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-2024-001',
            'total_amount' => 250.00,
            'status_id' => 2, // Not draft
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => 100.00,
            'total' => 100.00,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 150.00,
            'total' => 150.00,
        ]);

        /* Act */
        $response = $this->peppolService->sendInvoice($invoice);

        /* Assert */
        $this->assertTrue($response['success']);
        $this->assertEquals('doc-12345', $response['document_id']);
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'invoice_number' => 'INV-2024-001',
        ]);
        $this->assertDatabaseCount('invoice_items', 2);
    }

    #[Test]
    #[Group('integration')]
    public function it_validates_invoice_has_required_fields_before_sending(): void
    {
        /* Arrange */
        $client = Client::factory()->create(['name' => 'Test Client']);
        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '9876543210',
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => null, // Missing required field
            'total_amount' => 100.00,
        ]);

        /* Act */
        $canSend = $this->peppolService->canSendInvoice($invoice);

        /* Assert */
        $this->assertFalse($canSend);
    }

    #[Test]
    #[Group('integration')]
    public function it_prevents_sending_draft_invoice_via_peppol(): void
    {
        /* Arrange */
        $client = Client::factory()->create(['name' => 'Draft Client']);
        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '9876543210',
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'DRAFT-001',
            'total_amount' => 100.00,
            'status_id' => 1, // Draft status
        ]);

        /* Act */
        $canSend = $this->peppolService->canSendInvoice($invoice);

        /* Assert */
        $this->assertFalse($canSend);
    }

    #[Test]
    #[Group('integration')]
    public function it_throws_exception_when_client_has_no_peppol_endpoint(): void
    {
        /* Arrange */
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Client does not have a Peppol endpoint ID configured');

        $client = Client::factory()->create(['name' => 'No Peppol Client']);
        // No ClientPeppol record created

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-001',
            'total_amount' => 100.00,
            'status_id' => 2,
        ]);

        /* Act */
        $this->peppolService->sendInvoice($invoice);

        /* Assert - Exception expected */
    }

    #[Test]
    #[Group('integration')]
    public function it_successfully_sends_invoice_with_tax_and_discount(): void
    {
        /* Arrange */
        Http::fake([
            '*/document_submissions' => Http::response([
                'id' => 'doc-tax-123',
                'guid' => 'guid-tax-456',
                'status' => 'submitted',
            ], 200),
        ]);

        $client = Client::factory()->create(['name' => 'Tax Client']);
        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '1234567890',
            'peppol_scheme_id' => '0088',
        ]);

        $product = Product::factory()->create([
            'product_name' => 'Taxable Product',
            'price' => 100.00,
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-TAX-001',
            'subtotal' => 100.00,
            'tax_amount' => 21.00,
            'discount_amount' => 10.00,
            'total_amount' => 111.00,
            'status_id' => 2,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
            'tax_rate' => 21.00,
            'discount' => 10.00,
            'total' => 111.00,
        ]);

        /* Act */
        $response = $this->peppolService->sendInvoice($invoice);

        /* Assert */
        $this->assertTrue($response['success']);
        $this->assertEquals('doc-tax-123', $response['document_id']);
    }
    #endregion

    #region Integration Tests - Invoice Tracking via Peppol
    /**
     * Scenario 3: Tracking invoice delivery status through Peppol network
     */
    #[Test]
    #[Group('integration')]
    public function it_tracks_invoice_delivery_status_via_peppol(): void
    {
        /* Arrange */
        Http::fake([
            '*/document_submissions/doc-track-123' => Http::response([
                'id' => 'doc-track-123',
                'status' => 'delivered',
                'deliveredAt' => '2024-01-15T10:30:00Z',
                'receivedBy' => '9876543210',
            ], 200),
        ]);

        $storeCoveService = app(StoreCoveService::class);
        $documentId = 'doc-track-123';

        /* Act */
        $status = $storeCoveService->getDocumentStatus($documentId);

        /* Assert */
        $this->assertIsArray($status);
        $this->assertEquals('delivered', $status['status']);
        $this->assertEquals('9876543210', $status['receivedBy']);
        $this->assertArrayHasKey('deliveredAt', $status);
    }

    #[Test]
    #[Group('integration')]
    public function it_tracks_pending_invoice_status(): void
    {
        /* Arrange */
        Http::fake([
            '*/document_submissions/doc-pending-456' => Http::response([
                'id' => 'doc-pending-456',
                'status' => 'processing',
                'submittedAt' => '2024-01-15T09:00:00Z',
            ], 200),
        ]);

        $storeCoveService = app(StoreCoveService::class);

        /* Act */
        $status = $storeCoveService->getDocumentStatus('doc-pending-456');

        /* Assert */
        $this->assertEquals('processing', $status['status']);
        $this->assertArrayHasKey('submittedAt', $status);
    }

    #[Test]
    #[Group('integration')]
    public function it_handles_failed_delivery_status(): void
    {
        /* Arrange */
        Http::fake([
            '*/document_submissions/doc-failed-789' => Http::response([
                'id' => 'doc-failed-789',
                'status' => 'failed',
                'error' => 'Recipient endpoint not reachable',
                'failedAt' => '2024-01-15T11:00:00Z',
            ], 200),
        ]);

        $storeCoveService = app(StoreCoveService::class);

        /* Act */
        $status = $storeCoveService->getDocumentStatus('doc-failed-789');

        /* Assert */
        $this->assertEquals('failed', $status['status']);
        $this->assertArrayHasKey('error', $status);
        $this->assertEquals('Recipient endpoint not reachable', $status['error']);
    }

    #[Test]
    #[Group('integration')]
    public function it_throws_exception_for_invalid_document_id(): void
    {
        /* Arrange */
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to get document status from StoreCove');

        Http::fake([
            '*/document_submissions/invalid-doc' => Http::response(null, 404),
        ]);

        $storeCoveService = app(StoreCoveService::class);

        /* Act */
        $storeCoveService->getDocumentStatus('invalid-doc');

        /* Assert - Exception expected */
    }

    #[Test]
    #[Group('integration')]
    public function it_validates_recipient_received_invoice(): void
    {
        /* Arrange */
        Http::fake([
            '*/document_submissions' => Http::response([
                'id' => 'doc-received-111',
                'guid' => 'guid-received-222',
                'status' => 'submitted',
            ], 200),
            '*/document_submissions/doc-received-111' => Http::response([
                'id' => 'doc-received-111',
                'status' => 'delivered',
                'deliveredAt' => '2024-01-15T12:00:00Z',
                'receivedBy' => '9876543210',
                'acknowledgedAt' => '2024-01-15T12:05:00Z',
            ], 200),
        ]);

        $client = Client::factory()->create(['name' => 'Acknowledged Client']);
        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '9876543210',
        ]);

        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-ACK-001',
            'total_amount' => 500.00,
            'status_id' => 2,
        ]);

        $storeCoveService = app(StoreCoveService::class);

        /* Act */
        $sendResponse = $this->peppolService->sendInvoice($invoice);
        $statusResponse = $storeCoveService->getDocumentStatus($sendResponse['document_id']);

        /* Assert */
        $this->assertEquals('delivered', $statusResponse['status']);
        $this->assertArrayHasKey('acknowledgedAt', $statusResponse);
        $this->assertEquals('9876543210', $statusResponse['receivedBy']);
    }

    #[Test]
    #[Group('integration')]
    public function it_validates_peppol_endpoint_before_sending(): void
    {
        /* Arrange */
        Http::fake([
            '*/peppol_identifiers*' => Http::response([
                'valid' => true,
                'participant' => 'Test Corporation',
            ], 200),
        ]);

        $storeCoveService = app(StoreCoveService::class);

        /* Act */
        $isValid = $storeCoveService->validateEndpoint('1234567890', '0088');

        /* Assert */
        $this->assertTrue($isValid);
    }

    #[Test]
    #[Group('integration')]
    public function it_detects_invalid_peppol_endpoint(): void
    {
        /* Arrange */
        Http::fake([
            '*/peppol_identifiers*' => Http::response(null, 404),
        ]);

        $storeCoveService = app(StoreCoveService::class);

        /* Act */
        $isValid = $storeCoveService->validateEndpoint('invalid-endpoint', '0088');

        /* Assert */
        $this->assertFalse($isValid);
    }
    #endregion

    #region Integration Tests - Complete End-to-End Workflow
    #[Test]
    #[Group('integration')]
    public function it_completes_full_peppol_workflow_from_invoice_creation_to_delivery_confirmation(): void
    {
        /* Arrange */
        Http::fake([
            '*/document_submissions' => Http::response([
                'id' => 'doc-e2e-999',
                'guid' => 'guid-e2e-888',
                'status' => 'submitted',
            ], 200),
            '*/document_submissions/doc-e2e-999' => Http::sequence()
                ->push(['id' => 'doc-e2e-999', 'status' => 'processing'], 200)
                ->push(['id' => 'doc-e2e-999', 'status' => 'sent'], 200)
                ->push([
                    'id' => 'doc-e2e-999',
                    'status' => 'delivered',
                    'deliveredAt' => '2024-01-15T14:00:00Z',
                    'receivedBy' => '5555555555',
                ], 200),
        ]);

        // Step 1: Client with Peppol configuration
        $client = Client::factory()->create([
            'name' => 'E2E Test Corporation',
            'email' => 'billing@e2e.com',
        ]);

        ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'peppol_endpoint_id' => '5555555555',
            'peppol_scheme_id' => '0088',
            'peppol_participant_name' => 'E2E Corp B.V.',
        ]);

        // Step 2: Create products
        $product1 = Product::factory()->create([
            'product_name' => 'Consulting Services',
            'code' => 'CONS-001',
            'price' => 500.00,
        ]);

        $product2 = Product::factory()->create([
            'product_name' => 'Development Services',
            'code' => 'DEV-001',
            'price' => 1500.00,
        ]);

        // Step 3: Create invoice with items
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-E2E-2024-001',
            'subtotal' => 2000.00,
            'tax_amount' => 420.00,
            'total_amount' => 2420.00,
            'status_id' => 2, // Finalized
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => 500.00,
            'total' => 500.00,
        ]);

        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 1500.00,
            'total' => 1500.00,
        ]);

        $storeCoveService = app(StoreCoveService::class);

        /* Act - Step 4: Verify client Peppol info is accessible */
        $clientWithPeppol = Client::with('peppol')->find($client->id);
        
        /* Assert Step 4 */
        $this->assertNotNull($clientWithPeppol->peppol);
        $this->assertEquals('5555555555', $clientWithPeppol->peppol->peppol_endpoint_id);

        /* Act - Step 5: Send invoice via Peppol */
        $sendResponse = $this->peppolService->sendInvoice($invoice);
        
        /* Assert Step 5 */
        $this->assertTrue($sendResponse['success']);
        $this->assertNotNull($sendResponse['document_id']);

        /* Act - Step 6: Track delivery status (polling simulation) */
        $status1 = $storeCoveService->getDocumentStatus('doc-e2e-999'); // processing
        $status2 = $storeCoveService->getDocumentStatus('doc-e2e-999'); // sent
        $status3 = $storeCoveService->getDocumentStatus('doc-e2e-999'); // delivered
        
        /* Assert Step 6 - Final delivery confirmation */
        $this->assertEquals('processing', $status1['status']);
        $this->assertEquals('sent', $status2['status']);
        $this->assertEquals('delivered', $status3['status']);
        $this->assertEquals('5555555555', $status3['receivedBy']);
        $this->assertArrayHasKey('deliveredAt', $status3);
    }
    #endregion
}
