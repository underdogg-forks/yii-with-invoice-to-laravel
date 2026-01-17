<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\InvoiceEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeLetsPeppolClient;
use Tests\PeppolTestCase;

#[CoversClass(InvoiceEndpoint::class)]
class InvoiceEndpointTest extends PeppolTestCase
{
    private FakeLetsPeppolClient $fakeClient;
    private InvoiceEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeLetsPeppolClient();
        $this->endpoint = new InvoiceEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_sends_invoice(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'invoice_submission.basic');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->sendInvoice($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/invoices'));
    }

    #[Test]
    public function it_gets_invoice_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'invoice_status.delivered');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getInvoiceStatus($fixture['invoice_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/invoices/{$fixture['invoice_id']}/status"));
    }

    #[Test]
    public function it_cancels_invoice(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'invoice_status.cancelled');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->cancelInvoice($fixture['invoice_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, "/v1/invoices/{$fixture['invoice_id']}/cancel"));
    }

    #[Test]
    public function it_sends_invoice_with_metadata(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'invoice_submission.with_metadata');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->sendInvoice($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/invoices'));
    }

    #[Test]
    public function it_handles_pending_invoice_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'invoice_status.pending');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getInvoiceStatus($fixture['invoice_id']);

        /* Assert */
        $this->assertEquals('pending', $response['status']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/v1/invoices/{$fixture['invoice_id']}/status"));
    }
}
