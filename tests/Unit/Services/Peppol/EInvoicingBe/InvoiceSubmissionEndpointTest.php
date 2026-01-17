<?php

namespace Tests\Unit\Services\Peppol\EInvoicingBe;

use App\Enums\HttpMethod;
use App\Services\Peppol\EInvoicingBe\InvoiceSubmissionEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeEInvoicingBeClient;
use Tests\PeppolTestCase;

#[CoversClass(InvoiceSubmissionEndpoint::class)]
class InvoiceSubmissionEndpointTest extends PeppolTestCase
{
    private FakeEInvoicingBeClient $fakeClient;
    private InvoiceSubmissionEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeEInvoicingBeClient();
        $this->endpoint = new InvoiceSubmissionEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_submits_invoice(): void
    {
        /* Arrange */
        $invoiceData = $this->loadFixture('einvoicing_be', 'invoice_submission.request');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'invoice_submission.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->submitInvoice($invoiceData);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v1/invoices/submit'));
    }

    #[Test]
    public function it_gets_submission_status(): void
    {
        /* Arrange */
        $submissionId = $this->loadFixture('einvoicing_be', 'submission_status.processed.submission_id');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'submission_status.processed.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->getSubmissionStatus($submissionId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/invoices/submissions/{$submissionId}/status"));
    }

    #[Test]
    public function it_cancels_submission(): void
    {
        /* Arrange */
        $submissionId = $this->loadFixture('einvoicing_be', 'submission_cancel.submission_id');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'submission_cancel.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->cancelSubmission($submissionId);

        /* Assert */
        $this->assertEquals($expectedResponse, $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, "/api/v1/invoices/submissions/{$submissionId}/cancel"));
    }

    #[Test]
    public function it_submits_invoice_with_belgian_vat(): void
    {
        /* Arrange */
        $invoiceData = $this->loadFixture('einvoicing_be', 'invoice_submission.with_belgian_vat.request');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'invoice_submission.with_belgian_vat.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->submitInvoice($invoiceData);

        /* Assert */
        $this->assertEquals('sub-be-vat', $response['submission_id']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v1/invoices/submit'));
    }

    #[Test]
    public function it_handles_pending_submission_status(): void
    {
        /* Arrange */
        $submissionId = $this->loadFixture('einvoicing_be', 'submission_status.pending.submission_id');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'submission_status.pending.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->getSubmissionStatus($submissionId);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/invoices/submissions/{$submissionId}/status"));
    }

    #[Test]
    public function it_handles_rejected_submission_status(): void
    {
        /* Arrange */
        $submissionId = $this->loadFixture('einvoicing_be', 'submission_status.rejected.submission_id');
        $expectedResponse = $this->loadFixture('einvoicing_be', 'submission_status.rejected.response');
        
        $this->fakeClient->addResponse($expectedResponse);

        /* Act */
        $response = $this->endpoint->getSubmissionStatus($submissionId);

        /* Assert */
        $this->assertEquals('rejected', $response['status']);
        $this->assertNotEmpty($response['reason']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v1/invoices/submissions/{$submissionId}/status"));
    }
}
