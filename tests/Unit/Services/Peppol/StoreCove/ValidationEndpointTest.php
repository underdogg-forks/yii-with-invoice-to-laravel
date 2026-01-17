<?php

namespace Tests\Unit\Services\Peppol\StoreCove;

use App\Enums\HttpMethod;
use App\Services\Peppol\StoreCove\ValidationEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeStoreCoveClient;
use Tests\PeppolTestCase;

#[CoversClass(ValidationEndpoint::class)]
class ValidationEndpointTest extends PeppolTestCase
{
    private FakeStoreCoveClient $fakeClient;
    private ValidationEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeStoreCoveClient();
        $this->endpoint = new ValidationEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_validates_document(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'validation.valid_document');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateDocument($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/validation/document'));
    }

    #[Test]
    public function it_validates_participant(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'validation.valid_participant');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateParticipant($fixture['participant_id'], $fixture['document_type']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, '/api/v2/validation/participant'));
    }

    #[Test]
    public function it_validates_syntax(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'validation.valid_syntax');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateSyntax($fixture['xml']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/validation/syntax'));
    }

    #[Test]
    public function it_returns_validation_errors(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'validation.invalid_document');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateDocument($fixture['request']);

        /* Assert */
        $this->assertFalse($response['valid']);
        $this->assertNotEmpty($response['errors']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/v2/validation/document'));
    }

    #[Test]
    public function it_handles_invalid_participant(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('storecove', 'validation.invalid_participant');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateParticipant($fixture['participant_id'], $fixture['document_type']);

        /* Assert */
        $this->assertFalse($response['valid']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, '/api/v2/validation/participant'));
    }
}
