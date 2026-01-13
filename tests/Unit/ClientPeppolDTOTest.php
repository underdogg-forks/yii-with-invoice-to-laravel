<?php

namespace Tests\Unit;

use App\DTOs\ClientPeppolDTO;
use PHPUnit\Framework\TestCase;

class ClientPeppolDTOTest extends TestCase
{
    public function test_can_create_client_peppol_dto(): void
    {
        $dto = new ClientPeppolDTO(
            id: 1,
            client_id: 1,
            endpointid: 'test@example.com',
            buyer_reference: 'REF-001'
        );

        $this->assertEquals(1, $dto->id);
        $this->assertEquals(1, $dto->client_id);
        $this->assertEquals('test@example.com', $dto->endpointid);
        $this->assertEquals('REF-001', $dto->buyer_reference);
    }

    public function test_can_convert_dto_to_array(): void
    {
        $dto = new ClientPeppolDTO(
            id: 1,
            client_id: 1,
            endpointid: 'test@example.com'
        );

        $array = $dto->toArray();

        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('client_id', $array);
        $this->assertArrayHasKey('endpointid', $array);
    }
}
