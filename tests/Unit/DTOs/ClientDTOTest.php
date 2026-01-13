<?php

namespace Tests\Unit\DTOs;

use App\DTOs\ClientDTO;
use Tests\TestCase;

class ClientDTOTest extends TestCase
{
    public function it_creates_dto_from_array(): void
    {
        // Arrange
        $data = [
            'client_name' => 'John',
            'client_surname' => 'Doe',
            'client_email' => 'john@example.com',
        ];
        
        // Act
        $dto = new ClientDTO(...$data);
        
        // Assert
        $this->assertEquals('John', $dto->client_name);
        $this->assertEquals('Doe', $dto->client_surname);
        $this->assertEquals('john@example.com', $dto->client_email);
    }
    
    public function it_converts_dto_to_array(): void
    {
        // Arrange
        $dto = new ClientDTO(
            client_name: 'John',
            client_surname: 'Doe',
            client_email: 'john@example.com'
        );
        
        // Act
        $array = $dto->toArray();
        
        // Assert
        $this->assertArrayHasKey('client_name', $array);
        $this->assertEquals('John', $array['client_name']);
    }
    
    public function it_handles_optional_fields(): void
    {
        // Arrange & Act
        $dto = new ClientDTO(
            client_name: 'John',
            client_email: 'john@example.com'
        );
        
        // Assert
        $this->assertNull($dto->client_surname);
        $this->assertNull($dto->client_phone);
    }
    
    public function it_handles_boolean_active_field(): void
    {
        // Arrange & Act
        $dto = new ClientDTO(
            client_name: 'John',
            client_email: 'john@example.com',
            client_active: true
        );
        
        // Assert
        $this->assertTrue($dto->client_active);
    }
    
    public function it_handles_all_address_fields(): void
    {
        // Arrange & Act
        $dto = new ClientDTO(
            client_name: 'John',
            client_email: 'john@example.com',
            client_address_1: 'Street 1',
            client_city: 'Amsterdam',
            client_zip: '1000 AB',
            client_country: 'Netherlands'
        );
        
        // Assert
        $this->assertEquals('Street 1', $dto->client_address_1);
        $this->assertEquals('Amsterdam', $dto->client_city);
        $this->assertEquals('1000 AB', $dto->client_zip);
    }
    
    public function it_handles_vat_and_tax_code(): void
    {
        // Arrange & Act
        $dto = new ClientDTO(
            client_name: 'John',
            client_email: 'john@example.com',
            client_vat_id: 'NL123456789B01',
            client_tax_code: 'TAX123'
        );
        
        // Assert
        $this->assertEquals('NL123456789B01', $dto->client_vat_id);
        $this->assertEquals('TAX123', $dto->client_tax_code);
    }
}
