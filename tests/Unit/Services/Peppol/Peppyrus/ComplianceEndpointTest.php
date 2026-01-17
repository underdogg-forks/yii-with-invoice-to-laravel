<?php

namespace Tests\Unit\Services\Peppol\Peppyrus;

use App\Enums\HttpMethod;
use App\Services\Peppol\Peppyrus\ComplianceEndpoint;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakePeppyrusClient;
use Tests\PeppolTestCase;

#[CoversClass(ComplianceEndpoint::class)]
class ComplianceEndpointTest extends PeppolTestCase
{
    private FakePeppyrusClient $fakeClient;
    private ComplianceEndpoint $endpoint;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeClient = new FakePeppyrusClient();
        $this->endpoint = new ComplianceEndpoint($this->fakeClient);
    }

    #[Test]
    public function it_validates_compliance(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'compliance_validation.compliant');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateCompliance($fixture['request']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($response['compliant']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/compliance/validate'));
    }

    #[Test]
    public function it_gets_validation_report(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'validation_report.passed');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getValidationReport($fixture['validation_id']);

        /* Assert */
        $this->assertEquals($fixture['response'], $response);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/compliance/reports/{$fixture['validation_id']}"));
    }

    #[Test]
    public function it_returns_compliance_violations(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'compliance_validation.non_compliant');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->validateCompliance($fixture['request']);

        /* Assert */
        $this->assertFalse($response['compliant']);
        $this->assertNotEmpty($response['errors']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::POST->value, '/api/compliance/validate'));
    }

    #[Test]
    public function it_gets_detailed_validation_report(): void
    {
        /* Arrange */
        $fixture = $this->loadFixture('peppyrus', 'validation_report.failed');
        $this->fakeClient->addResponse($fixture['response']);

        /* Act */
        $response = $this->endpoint->getValidationReport($fixture['validation_id']);

        /* Assert */
        $this->assertFalse($response['compliant']);
        $this->assertEquals(3, $response['rules_failed']);
        $this->assertNotEmpty($response['failed_rules']);
        $this->assertTrue($this->fakeClient->hasRequest(HttpMethod::GET->value, "/api/compliance/reports/{$fixture['validation_id']}"));
    }
}
