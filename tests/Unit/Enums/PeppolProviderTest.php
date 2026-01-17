<?php

namespace Tests\Unit\Enums;

use App\Enums\PeppolProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PeppolProvider::class)]
class PeppolProviderTest extends TestCase
{
    #[Test]
    public function it_has_storecove_provider(): void
    {
        /* Arrange & Act */
        $provider = PeppolProvider::STORECOVE;

        /* Assert */
        $this->assertEquals('storecove', $provider->value);
        $this->assertEquals('StoreCove', $provider->getName());
    }

    #[Test]
    public function it_has_letspeppol_provider(): void
    {
        /* Arrange & Act */
        $provider = PeppolProvider::LETSPEPPOL;

        /* Assert */
        $this->assertEquals('letspeppol', $provider->value);
        $this->assertEquals('LetsPeppol', $provider->getName());
    }

    #[Test]
    public function it_has_peppyrus_provider(): void
    {
        /* Arrange & Act */
        $provider = PeppolProvider::PEPPYRUS;

        /* Assert */
        $this->assertEquals('peppyrus', $provider->value);
        $this->assertEquals('Peppyrus', $provider->getName());
    }

    #[Test]
    public function it_has_einvoicing_be_provider(): void
    {
        /* Arrange & Act */
        $provider = PeppolProvider::EINVOICING_BE;

        /* Assert */
        $this->assertEquals('einvoicing_be', $provider->value);
        $this->assertEquals('E-invoicing.be', $provider->getName());
    }

    #[Test]
    public function it_returns_production_base_url_for_storecove(): void
    {
        /* Arrange */
        $provider = PeppolProvider::STORECOVE;

        /* Act */
        $url = $provider->getBaseUrl('production');

        /* Assert */
        $this->assertEquals('https://api.storecove.com/api/v2', $url);
    }

    #[Test]
    public function it_returns_sandbox_base_url_for_storecove(): void
    {
        /* Arrange */
        $provider = PeppolProvider::STORECOVE;

        /* Act */
        $url = $provider->getBaseUrl('sandbox');

        /* Assert */
        $this->assertEquals('https://api-sandbox.storecove.com/api/v2', $url);
    }

    #[Test]
    public function it_returns_base_url_for_letspeppol(): void
    {
        /* Arrange */
        $provider = PeppolProvider::LETSPEPPOL;

        /* Act */
        $url = $provider->getBaseUrl();

        /* Assert */
        $this->assertEquals('https://api.letspeppol.com/v1', $url);
    }

    #[Test]
    public function it_returns_base_url_for_peppyrus(): void
    {
        /* Arrange */
        $provider = PeppolProvider::PEPPYRUS;

        /* Act */
        $url = $provider->getBaseUrl();

        /* Assert */
        $this->assertEquals('https://api.peppyrus.com/v1', $url);
    }

    #[Test]
    public function it_returns_base_url_for_einvoicing_be(): void
    {
        /* Arrange */
        $provider = PeppolProvider::EINVOICING_BE;

        /* Act */
        $url = $provider->getBaseUrl();

        /* Assert */
        $this->assertEquals('https://api.e-invoicing.be/v1', $url);
    }

    #[Test]
    public function it_can_be_created_from_string(): void
    {
        /* Arrange & Act */
        $provider = PeppolProvider::from('storecove');

        /* Assert */
        $this->assertSame(PeppolProvider::STORECOVE, $provider);
    }

    #[Test]
    public function it_has_all_four_providers(): void
    {
        /* Arrange */
        $expectedProviders = ['storecove', 'letspeppol', 'peppyrus', 'einvoicing_be'];

        /* Act */
        $cases = PeppolProvider::cases();
        $actualProviders = array_map(fn($case) => $case->value, $cases);

        /* Assert */
        $this->assertEquals($expectedProviders, $actualProviders);
        $this->assertCount(4, $cases);
    }

    #[Test]
    public function it_defaults_to_production_url_when_environment_not_specified(): void
    {
        /* Arrange */
        $provider = PeppolProvider::STORECOVE;

        /* Act */
        $url = $provider->getBaseUrl();

        /* Assert */
        $this->assertEquals('https://api.storecove.com/api/v2', $url);
    }
}
