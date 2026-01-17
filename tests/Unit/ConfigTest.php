<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class ConfigTest extends TestCase
{
    #[Test]
    public function it_loads_peppol_configuration(): void
    {
        /* Act */
        $config = config('peppol');

        /* Assert */
        $this->assertIsArray($config);
        $this->assertNotEmpty($config);
    }

    #[Test]
    public function it_has_supplier_configuration(): void
    {
        /* Act */
        $supplier = config('peppol.supplier');

        /* Assert */
        $this->assertIsArray($supplier);
        $this->assertArrayHasKey('endpoint_id', $supplier);
        $this->assertArrayHasKey('scheme_id', $supplier);
        $this->assertArrayHasKey('vat_number', $supplier);
        $this->assertArrayHasKey('address', $supplier);
    }

    #[Test]
    public function it_has_supplier_address_configuration(): void
    {
        /* Act */
        $address = config('peppol.supplier.address');

        /* Assert */
        $this->assertIsArray($address);
        $this->assertArrayHasKey('street', $address);
        $this->assertArrayHasKey('city', $address);
        $this->assertArrayHasKey('postal_code', $address);
        $this->assertArrayHasKey('country_code', $address);
    }

    #[Test]
    public function it_has_service_providers_configuration(): void
    {
        /* Act */
        $providers = config('peppol.service_providers');

        /* Assert */
        $this->assertIsArray($providers);
        $this->assertArrayHasKey('storecove', $providers);
    }

    #[Test]
    public function it_has_storecove_provider_configuration(): void
    {
        /* Act */
        $storecove = config('peppol.service_providers.storecove');

        /* Assert */
        $this->assertIsArray($storecove);
        $this->assertArrayHasKey('enabled', $storecove);
        $this->assertArrayHasKey('api_key', $storecove);
        $this->assertArrayHasKey('endpoint', $storecove);
    }

    #[Test]
    public function it_has_tax_configuration(): void
    {
        /* Act */
        $tax = config('peppol.tax');

        /* Assert */
        $this->assertIsArray($tax);
        $this->assertArrayHasKey('default_rate', $tax);
        $this->assertArrayHasKey('default_scheme', $tax);
    }

    #[Test]
    public function it_has_default_currency_configuration(): void
    {
        /* Act */
        $currency = config('peppol.default_currency');

        /* Assert */
        $this->assertIsString($currency);
        $this->assertEquals('EUR', $currency);
    }

    #[Test]
    public function it_uses_default_values_when_env_not_set(): void
    {
        /* Act */
        $schemeId = config('peppol.supplier.scheme_id');
        $currency = config('peppol.default_currency');
        $taxRate = config('peppol.tax.default_rate');
        $taxScheme = config('peppol.tax.default_scheme');
        $countryCode = config('peppol.supplier.address.country_code');
        $storecoveEnabled = config('peppol.service_providers.storecove.enabled');

        /* Assert */
        $this->assertEquals('0088', $schemeId);
        $this->assertEquals('EUR', $currency);
        $this->assertEquals(21.00, $taxRate);
        $this->assertEquals('VAT', $taxScheme);
        $this->assertEquals('NL', $countryCode);
        $this->assertFalse($storecoveEnabled);
    }

    #[Test]
    public function it_has_storecove_default_endpoint(): void
    {
        /* Act */
        $endpoint = config('peppol.service_providers.storecove.endpoint');

        /* Assert */
        $this->assertEquals('https://api.storecove.com/api/v2', $endpoint);
    }

    #[Test]
    public function it_returns_empty_strings_for_unset_credentials(): void
    {
        /* Act */
        $endpointId = config('peppol.supplier.endpoint_id');
        $vatNumber = config('peppol.supplier.vat_number');
        $apiKey = config('peppol.service_providers.storecove.api_key');

        /* Assert */
        $this->assertEquals('', $endpointId);
        $this->assertEquals('', $vatNumber);
        $this->assertEquals('', $apiKey);
    }
}
