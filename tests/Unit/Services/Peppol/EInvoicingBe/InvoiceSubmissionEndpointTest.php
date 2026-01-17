<?php

namespace Tests\Unit\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBe\InvoiceSubmissionEndpoint;
use App\Services\Peppol\EInvoicingBeClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(InvoiceSubmissionEndpoint::class)]
class InvoiceSubmissionEndpointTest extends TestCase
{
    private EInvoicingBeClient $mockClient;
    private InvoiceSubmissionEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockClient = Mockery::mock(EInvoicingBeClient::class);
        $this->endpoint = new InvoiceSubmissionEndpoint($this->mockClient);
    }

    #[Test]
    public function it_submits_invoice(): void
    {
        /* Arrange */
        $invoiceData = [
            'document' => '<Invoice/>',
            'vat_number' => 'BE0123456789',
        ];
        
        $expectedResponse = [
            'submission_id' => 'sub-be-123',
            'status' => 'accepted',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/invoices/submit', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->submitInvoice($invoiceData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_gets_submission_status(): void
    {
        /* Arrange */
        $submissionId = 'sub-be-456';
        $expectedResponse = [
            'submission_id' => $submissionId,
            'status' => 'processed',
            'processed_at' => '2024-01-15T10:30:00Z',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/invoices/submissions/{$submissionId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getSubmissionStatus($submissionId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_cancels_submission(): void
    {
        /* Arrange */
        $submissionId = 'sub-be-789';
        $expectedResponse = [
            'submission_id' => $submissionId,
            'status' => 'cancelled',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, "/api/v1/invoices/submissions/{$submissionId}/cancel")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->cancelSubmission($submissionId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function it_submits_invoice_with_belgian_vat(): void
    {
        /* Arrange */
        $invoiceData = [
            'document' => '<Invoice/>',
            'vat_number' => 'BE0987654321',
            'belgian_specific_data' => ['structured_communication' => '+++123/4567/89012+++'],
        ];
        
        $expectedResponse = ['submission_id' => 'sub-be-vat'];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::POST->value, '/api/v1/invoices/submit', $invoiceData)
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->submitInvoice($invoiceData);

        /* Assert */
        $this->assertEquals('sub-be-vat', $response['submission_id']);
    }

    #[Test]
    public function it_handles_pending_submission_status(): void
    {
        /* Arrange */
        $submissionId = 'sub-be-pending';
        $expectedResponse = [
            'submission_id' => $submissionId,
            'status' => 'pending',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/invoices/submissions/{$submissionId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getSubmissionStatus($submissionId);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
    }

    #[Test]
    public function it_handles_rejected_submission_status(): void
    {
        /* Arrange */
        $submissionId = 'sub-be-rejected';
        $expectedResponse = [
            'submission_id' => $submissionId,
            'status' => 'rejected',
            'reason' => 'Invalid VAT number format',
        ];
        
        $this->mockClient->shouldReceive('request')
            ->once()
            ->with(HttpMethod::GET->value, "/api/v1/invoices/submissions/{$submissionId}/status")
            ->andReturn($expectedResponse);

        /* Act */
        $response = $this->endpoint->getSubmissionStatus($submissionId);

        /* Assert */
        $this->assertEquals('rejected', $response['status']);
        $this->assertNotEmpty($response['reason']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
