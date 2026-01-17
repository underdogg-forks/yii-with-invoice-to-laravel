<?php

namespace Tests\Unit\Services\Peppol\LetsPeppol;

use App\Enums\HttpMethod;
use App\Services\Peppol\LetsPeppol\ValidationServiceEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeLetsPeppolClient;
use Tests\PeppolTestCase;

#[CoversClass(ValidationServiceEndpoint::class)]
class ValidationServiceEndpointTest extends PeppolTestCase
{
    private FakeLetsPeppolClient $fakeClient;
    private ValidationServiceEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakeLetsPeppolClient();
        $this->endpoint = new ValidationServiceEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_validates_invoice(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'validation.valid_invoice');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateInvoice($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($response['valid']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/validation/invoice'));
    }

    #[Test]
    public function it_checks_compliance_with_default_specification(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'compliance_check.compliant_default');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->checkCompliance($fixture['xml']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($response['compliant']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/validation/compliance'));
    }

    #[Test]
    public function it_checks_compliance_with_custom_specification(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'compliance_check.compliant_custom');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->checkCompliance($fixture['xml'], $fixture['specification']);

        /* Assert */
        $this->assertEquals($fixture['specification'], $response['specification']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/validation/compliance'));
    }

    #[Test]
    public function it_returns_validation_errors(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'validation.invalid_invoice');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateInvoice($fixture['request']);

        /* Assert */
        $this->assertFalse($response['valid']);
        $this->assertNotEmpty($response['errors']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/validation/invoice'));
    }

    #[Test]
    public function it_returns_compliance_violations(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('letspeppol', 'compliance_check.non_compliant');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->checkCompliance($fixture['xml']);

        /* Assert */
        $this->assertFalse($response['compliant']);
        $this->assertNotEmpty($response['violations']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/v1/validation/compliance'));
    }
}
