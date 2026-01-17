<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\DocumentsEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeStoreCoveClient;
use Tests\PeppolTestCase;

#[CoversClass(DocumentsEndpoint::class)]
class DocumentsEndpointTest extends PeppolTestCase
{
    private FakeStoreCoveClient $fakeClient;
    private DocumentsEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeStoreCoveClient();
        $this->endpoint = new DocumentsEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_submits_document_successfully(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'document_submission.basic');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->submitDocument($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/document_submissions'));
    }

    #[Test]
    public function it_gets_document_status(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'document_status.delivered');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDocumentStatus($fixture['document_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/document_submissions/{$fixture['document_id']}"));
    }

    #[Test]
    public function it_gets_full_document(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'document_retrieval.full_document');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getDocument($fixture['document_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/v2/document_submissions/{$fixture['document_id']}/document"));
    }

    #[Test]
    public function it_cancels_document(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'document_status.cancelled');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->cancelDocument($fixture['document_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::DELETE->value, "/api/v2/document_submissions/{$fixture['document_id']}"));
    }

    #[Test]
    public function it_submits_document_with_routing_information(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'document_submission.with_routing');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->submitDocument($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/document_submissions'));
    }

    #[Test]
    public function it_handles_document_with_attachments(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'document_submission.with_attachments');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->submitDocument($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/document_submissions'));
    }
}
