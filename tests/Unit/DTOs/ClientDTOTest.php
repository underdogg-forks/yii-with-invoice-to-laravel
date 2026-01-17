<?php

namespace Tests\Unit\DTOs;

use App\DTOs\ClientDTO;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ClientDTOTest extends TestCase
{
    #[Test]
    public function it_creates_dto_with_required_fields(): void
    {
        /* Arrange */
        $data = [
            'client_name' => 'John',
            'client_surname' => 'Doe',
            'client_email' => 'john@example.com',
        ];
        
        /* Act */
        $dto = new ClientDTO(...$data);
        
        /* Assert */
        $this->assertEquals('John', $dto->client_name);
        $this->assertEquals('Doe', $dto->client_surname);
        $this->assertEquals('john@example.com', $dto->client_email);
    }
    
    #[Test]
    public function it_converts_dto_to_array_preserving_all_fields(): void
    {
        /* Arrange */
        $dto = new ClientDTO(
            client_name: 'John',
            client_surname: 'Doe',
            client_email: 'john@example.com'
        );
        
        /* Act */
        $array = $dto->toArray();
        
        /* Assert */
        $this->assertIsArray($array);
        $this->assertArrayHasKey('client_name', $array);
        $this->assertArrayHasKey('client_surname', $array);
        $this->assertArrayHasKey('client_email', $array);
        $this->assertEquals('John', $array['client_name']);
        $this->assertEquals('Doe', $array['client_surname']);
        $this->assertEquals('john@example.com', $array['client_email']);
    }
    
    #[Test]
    public function it_handles_optional_fields_as_null(): void
    {
        /* Arrange & Act */
        $dto = new ClientDTO(
            client_name: 'John',
            client_email: 'john@example.com'
        );
        
        /* Assert */
        $this->assertNull($dto->client_surname);
        $this->assertNull($dto->client_phone);
        $this->assertNull($dto->client_address_1);
    }
    
    #[Test]
    public function it_handles_boolean_active_field_correctly(): void
    {
        /* Arrange */
        $dtoActive = new ClientDTO(
            client_name: 'John',
            client_email: 'john@example.com',
            client_active: true
        );
        
        $dtoInactive = new ClientDTO(
            client_name: 'Jane',
            client_email: 'jane@example.com',
            client_active: false
        );
        
        /* Assert */
        $this->assertTrue($dtoActive->client_active);
        $this->assertFalse($dtoInactive->client_active);
    }
    
    #[Test]
    public function it_handles_complete_address_information(): void
    {
        /* Arrange */
        $dto = new ClientDTO(
            client_name: 'John',
            client_email: 'john@example.com',
            client_address_1: 'Street 1',
            client_address_2: 'Apartment 4B',
            client_city: 'Amsterdam',
            client_zip: '1000 AB',
            client_country: 'Netherlands'
        );
        
        /* Assert */
        $this->assertEquals('Street 1', $dto->client_address_1);
        $this->assertEquals('Apartment 4B', $dto->client_address_2);
        $this->assertEquals('Amsterdam', $dto->client_city);
        $this->assertEquals('1000 AB', $dto->client_zip);
        $this->assertEquals('Netherlands', $dto->client_country);
    }
    
    #[Test]
    public function it_handles_vat_id_and_tax_code_for_business_clients(): void
    {
        /* Arrange */
        $dto = new ClientDTO(
            client_name: 'Acme Corp',
            client_email: 'info@acme.com',
            client_vat_id: 'NL123456789B01',
            client_tax_code: 'TAX123'
        );
        
        /* Assert */
        $this->assertEquals('NL123456789B01', $dto->client_vat_id);
        $this->assertEquals('TAX123', $dto->client_tax_code);
    }
}
