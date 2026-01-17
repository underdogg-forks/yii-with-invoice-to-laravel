<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\InvoiceEndpoint;
use App\Services\Peppol\LetsPeppolClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(InvoiceEndpoint::class)]
class InvoiceEndpointTest extends TestCase
{
    private LetsPeppolClient $mockClient;
    private InvoiceEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(LetsPeppolClient::class);
        $this->endpoint = new InvoiceEndpoint($this->mockClient);
    }

    #[Test]
    public function it_sends_invoice(): void
    {
        /* Arrange */
        $invoiceData = [
            'document' => '<Invoice/>',
            'recipient' => '0088:1234567890123',
        ];
        
        $expectedResponse = ['invoice_id' => 'inv-123', 'status' => 'sent'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/invoices', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->sendInvoice($invoiceData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_invoice_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-456';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'delivered',
            'updated_at' => '2024-01-15T10:30:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/invoices/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getInvoiceStatus($invoiceId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_cancels_invoice(): void
    {
        /* Arrange */
        $invoiceId = 'inv-789';
        $expectedResponse = [
            'invoice_id' => $invoiceId,
            'status' => 'cancelled',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, "/v1/invoices/{$invoiceId}/cancel")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->cancelInvoice($invoiceId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_sends_invoice_with_metadata(): void
    {
        /* Arrange */
        $invoiceData = [
            'document' => '<Invoice/>',
            'recipient' => '0088:9876543210987',
            'metadata' => ['reference' => 'PO-12345'],
        ];
        
        $expectedResponse = ['invoice_id' => 'inv-meta', 'status' => 'sent'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/v1/invoices', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->sendInvoice($invoiceData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_handles_pending_invoice_status(): void
    {
        /* Arrange */
        $invoiceId = 'inv-pending';
        $expectedResponse = ['invoice_id' => $invoiceId, 'status' => 'pending'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/v1/invoices/{$invoiceId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getInvoiceStatus($invoiceId);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
