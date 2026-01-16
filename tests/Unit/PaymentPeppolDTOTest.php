<?php

namespace Tests\Unit;

use App\DTOs\PaymentPeppolDTO;
use App\Models\PaymentPeppol;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PaymentPeppolDTO::class)]
class PaymentPeppolDTOTest extends TestCase
{
    #[Test]
    public function it_creates_payment_peppol_dto(): void
    {
        /* Arrange */
        $expectedId = 1;
        $expectedInvId = 1;
        $expectedAutoReference = 123456;
        $expectedProvider = 'StoreCove';

        /* Act */
        $dto = new PaymentPeppolDTO(
            id: $expectedId,
            inv_id: $expectedInvId,
            auto_reference: $expectedAutoReference,
            provider: $expectedProvider
        );

        /* Assert */
        $this->assertEquals($expectedId, $dto->id);
        $this->assertEquals($expectedInvId, $dto->inv_id);
        $this->assertEquals($expectedAutoReference, $dto->auto_reference);
        $this->assertEquals($expectedProvider, $dto->provider);
    }

    #[Test]
    public function it_converts_dto_to_array(): void
    {
        /* Arrange */
        $dto = new PaymentPeppolDTO(
            id: 1,
            inv_id: 1,
            auto_reference: 123456,
            provider: 'StoreCove'
        );

        /* Act */
        $array = $dto->toArray();

        /* Assert */
        $this->assertIsArray($array);
        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('inv_id', $array);
        $this->assertArrayHasKey('auto_reference', $array);
        $this->assertArrayHasKey('provider', $array);
    }

    #[Test]
    public function it_creates_dto_from_model(): void
    {
        /* Arrange */
        $model = new PaymentPeppol([
            'id' => 1,
            'inv_id' => 1,
            'auto_reference' => 123456,
            'provider' => 'Ecosio',
        ]);

        /* Act */
        $dto = PaymentPeppolDTO::fromModel($model);

        /* Assert */
        $this->assertInstanceOf(PaymentPeppolDTO::class, $dto);
        $this->assertEquals($model->id, $dto->id);
        $this->assertEquals($model->inv_id, $dto->inv_id);
        $this->assertEquals($model->auto_reference, $dto->auto_reference);
        $this->assertEquals($model->provider, $dto->provider);
    }
}
