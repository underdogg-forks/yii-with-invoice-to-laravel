<?php

namespace Tests\Unit;

use App\DTOs\ClientPeppolDTO;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClientPeppolDTO::class)]
class ClientPeppolDTOTest extends TestCase
{
    #[Test]
    public function it_creates_client_peppol_dto(): void
    {
        /* Arrange */
        $expectedId = 1;
        $expectedClientId = 1;
        $expectedEndpointId = 'test@example.com';
        $expectedBuyerReference = 'REF-001';

        /* Act */
        $dto = new ClientPeppolDTO(
            id: $expectedId,
            client_id: $expectedClientId,
            endpointid: $expectedEndpointId,
            buyer_reference: $expectedBuyerReference
        );

        /* Assert */
        $this->assertEquals($expectedId, $dto->id);
        $this->assertEquals($expectedClientId, $dto->client_id);
        $this->assertEquals($expectedEndpointId, $dto->endpointid);
        $this->assertEquals($expectedBuyerReference, $dto->buyer_reference);
    }

    #[Test]
    public function it_converts_dto_to_array(): void
    {
        /* Arrange */
        $dto = new ClientPeppolDTO(
            id: 1,
            client_id: 1,
            endpointid: 'test@example.com'
        );

        /* Act */
        $array = $dto->toArray();

        /* Assert */
        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('client_id', $array);
        $this->assertArrayHasKey('endpointid', $array);
    }
}
