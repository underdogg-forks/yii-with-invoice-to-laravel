<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class ConfigTest extends TestCase
{
    #[Test]
    public function it_loads_peppol_configuration_with_all_required_keys(): void
    {
        /* Act */
        $config = config('peppol');

        /* Assert */
        $this->assertIsArray($config);
        $this->assertArrayHasKey('supplier', $config);
        $this->assertArrayHasKey('service_providers', $config);
        $this->assertArrayHasKey('tax', $config);
        $this->assertArrayHasKey('default_currency', $config);
    }

    #[Test]
    public function it_has_valid_supplier_configuration_structure(): void
    {
        /* Act */
        $supplier = config('peppol.supplier');

        /* Assert */
        $this->assertIsArray($supplier);
        $this->assertArrayHasKey('endpoint_id', $supplier);
        $this->assertArrayHasKey('scheme_id', $supplier);
        $this->assertArrayHasKey('vat_number', $supplier);
        $this->assertArrayHasKey('address', $supplier);
        
        // Validate data types
        $this->assertIsString($supplier['endpoint_id']);
        $this->assertIsString($supplier['scheme_id']);
        $this->assertIsString($supplier['vat_number']);
        $this->assertIsArray($supplier['address']);
    }

    #[Test]
    public function it_has_complete_supplier_address_configuration(): void
    {
        /* Act */
        $address = config('peppol.supplier.address');

        /* Assert */
        $this->assertIsArray($address);
        $this->assertArrayHasKey('street', $address);
        $this->assertArrayHasKey('city', $address);
        $this->assertArrayHasKey('postal_code', $address);
        $this->assertArrayHasKey('country_code', $address);
        
        // Validate all address fields are strings
        $this->assertIsString($address['street']);
        $this->assertIsString($address['city']);
        $this->assertIsString($address['postal_code']);
        $this->assertIsString($address['country_code']);
    }

    #[Test]
    public function it_has_service_providers_with_storecove(): void
    {
        /* Act */
        $providers = config('peppol.service_providers');

        /* Assert */
        $this->assertIsArray($providers);
        $this->assertArrayHasKey('storecove', $providers);
        $this->assertIsArray($providers['storecove']);
    }

    #[Test]
    public function it_has_valid_storecove_provider_configuration(): void
    {
        /* Act */
        $storecove = config('peppol.service_providers.storecove');

        /* Assert */
        $this->assertIsArray($storecove);
        $this->assertArrayHasKey('enabled', $storecove);
        $this->assertArrayHasKey('api_key', $storecove);
        $this->assertArrayHasKey('endpoint', $storecove);
        
        // Validate data types
        $this->assertIsBool($storecove['enabled']);
        $this->assertIsString($storecove['api_key']);
        $this->assertIsString($storecove['endpoint']);
    }

    #[Test]
    public function it_has_valid_tax_configuration(): void
    {
        /* Act */
        $tax = config('peppol.tax');

        /* Assert */
        $this->assertIsArray($tax);
        $this->assertArrayHasKey('default_rate', $tax);
        $this->assertArrayHasKey('default_scheme', $tax);
        
        // Validate data types and values
        $this->assertIsFloat($tax['default_rate']);
        $this->assertIsString($tax['default_scheme']);
        $this->assertGreaterThanOrEqual(0, $tax['default_rate']);
        $this->assertLessThanOrEqual(100, $tax['default_rate']);
    }

    #[Test]
    public function it_has_valid_default_currency_configuration(): void
    {
        /* Act */
        $currency = config('peppol.default_currency');

        /* Assert */
        $this->assertIsString($currency);
        $this->assertNotEmpty($currency);
        $this->assertEquals('EUR', $currency);
        // Validate ISO 4217 currency code format (3 letters)
        $this->assertMatchesRegularExpression('/^[A-Z]{3}$/', $currency);
    }

    #[Test]
    public function it_uses_sensible_default_values_when_env_not_set(): void
    {
        /* Act */
        $schemeId = config('peppol.supplier.scheme_id');
        $currency = config('peppol.default_currency');
        $taxRate = config('peppol.tax.default_rate');
        $taxScheme = config('peppol.tax.default_scheme');
        $countryCode = config('peppol.supplier.address.country_code');
        $storecoveEnabled = config('peppol.service_providers.storecove.enabled');

        /* Assert - Verify all defaults are appropriate for business use */
        $this->assertEquals('0088', $schemeId, 'Default scheme should be GLN (0088)');
        $this->assertEquals('EUR', $currency, 'Default currency should be EUR');
        $this->assertEquals(21.00, $taxRate, 'Default tax rate should be 21% (Netherlands standard VAT)');
        $this->assertEquals('VAT', $taxScheme, 'Default tax scheme should be VAT');
        $this->assertEquals('NL', $countryCode, 'Default country should be NL (Netherlands)');
        $this->assertFalse($storecoveEnabled, 'StoreCove should be disabled by default for security');
    }

    #[Test]
    public function it_has_valid_storecove_default_endpoint_url(): void
    {
        /* Act */
        $endpoint = config('peppol.service_providers.storecove.endpoint');

        /* Assert */
        $this->assertIsString($endpoint);
        $this->assertEquals('https://api.storecove.com/api/v2', $endpoint);
        $this->assertStringStartsWith('https://', $endpoint, 'Endpoint must use HTTPS for security');
        $this->assertStringContainsString('storecove.com', $endpoint);
    }

    #[Test]
    public function it_returns_empty_strings_for_unset_sensitive_credentials(): void
    {
        /* Act */
        $endpointId = config('peppol.supplier.endpoint_id');
        $vatNumber = config('peppol.supplier.vat_number');
        $apiKey = config('peppol.service_providers.storecove.api_key');

        /* Assert - These should be empty when not configured for security */
        $this->assertIsString($endpointId);
        $this->assertIsString($vatNumber);
        $this->assertIsString($apiKey);
        $this->assertEquals('', $endpointId, 'Endpoint ID should not have a default value');
        $this->assertEquals('', $vatNumber, 'VAT number should not have a default value');
        $this->assertEquals('', $apiKey, 'API key should not have a default value for security');
    }

    #[Test]
    public function it_validates_scheme_id_format(): void
    {
        /* Act */
        $schemeId = config('peppol.supplier.scheme_id');

        /* Assert */
        $this->assertIsString($schemeId);
        $this->assertEquals(4, strlen($schemeId), 'Scheme ID must be exactly 4 characters');
        $this->assertMatchesRegularExpression('/^\d{4}$/', $schemeId, 'Scheme ID must be 4 digits');
    }

    #[Test]
    public function it_validates_country_code_format(): void
    {
        /* Act */
        $countryCode = config('peppol.supplier.address.country_code');

        /* Assert */
        $this->assertIsString($countryCode);
        $this->assertEquals(2, strlen($countryCode), 'Country code must be exactly 2 characters');
        $this->assertMatchesRegularExpression('/^[A-Z]{2}$/', $countryCode, 'Country code must be 2 uppercase letters (ISO 3166-1 alpha-2)');
    }
}
