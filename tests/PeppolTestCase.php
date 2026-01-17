<?php

namespace Tests;

use Tests\TestCase as BaseTestCase;

/**
 * Base test case for Peppol tests with fixture loading support
 * 
 * Provides centralized fixture management for Peppol-related tests.
 * Fixtures are stored in tests/Fixtures/Peppol/ directory.
 */
abstract class PeppolTestCase extends BaseTestCase
{
    /**
     * Load a fixture from the Peppol fixtures directory
     *
     * @param string $provider Provider name (e.g., 'storecove', 'einvoicing_be')
     * @param string|null $key Optional dot-notation key to retrieve specific data
     * @return mixed
     */
    protected function loadFixture(string $provider, ?string $key = null): mixed
    {
        $fixturePath = __DIR__ . "/Fixtures/Peppol/{$provider}.php";
        
        if (!file_exists($fixturePath)) {
            throw new \RuntimeException("Fixture file not found: {$fixturePath}");
        }
        
        $fixture = require $fixturePath;
        
        if ($key === null) {
            return $fixture;
        }
        
        return $this->getArrayValue($fixture, $key);
    }
    
    /**
     * Get a value from an array using dot notation
     *
     * @param array $array
     * @param string $key
     * @return mixed
     */
    private function getArrayValue(array $array, string $key): mixed
    {
        $keys = explode('.', $key);
        $value = $array;
        
        foreach ($keys as $segment) {
            if (!isset($value[$segment])) {
                throw new \RuntimeException("Key '{$key}' not found in fixture");
            }
            $value = $value[$segment];
        }
        
        return $value;
    }
}
