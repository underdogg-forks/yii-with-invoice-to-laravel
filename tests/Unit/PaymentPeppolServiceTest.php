<?php

namespace Tests\Unit;

use App\DTOs\PaymentPeppolDTO;
use App\Models\PaymentPeppol;
use App\Repositories\PaymentPeppolRepository;
use App\Services\PaymentPeppolService;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PaymentPeppolService::class)]
class PaymentPeppolServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_gets_payment_peppol_by_id(): void
    {
        /* Arrange */
        $repository = Mockery::mock(PaymentPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(new PaymentPeppol(['id' => 1]));

        $service = new PaymentPeppolService($repository);

        /* Act */
        $result = $service->getById(1);

        /* Assert */
        $this->assertInstanceOf(PaymentPeppol::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_gets_payment_peppol_by_invoice_id(): void
    {
        /* Arrange */
        $repository = Mockery::mock(PaymentPeppolRepository::class);
        $repository->shouldReceive('findByInvoiceId')
            ->once()
            ->with(1)
            ->andReturn(collect([new PaymentPeppol(['inv_id' => 1])]));

        $service = new PaymentPeppolService($repository);

        /* Act */
        $result = $service->getByInvoiceId(1);

        /* Assert */
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_creates_payment_peppol(): void
    {
        /* Arrange */
        $dto = new PaymentPeppolDTO(
            inv_id: 1,
            provider: 'StoreCove'
        );

        $repository = Mockery::mock(PaymentPeppolRepository::class);
        $repository->shouldReceive('create')
            ->once()
            ->andReturn(new PaymentPeppol(['id' => 1]));

        $service = new PaymentPeppolService($repository);

        /* Act */
        $result = $service->create($dto);

        /* Assert */
        $this->assertInstanceOf(PaymentPeppol::class, $result);
    }

    #[Test]
    public function it_updates_payment_peppol(): void
    {
        /* Arrange */
        $paymentPeppol = new PaymentPeppol(['id' => 1]);
        $dto = new PaymentPeppolDTO(
            id: 1,
            inv_id: 1,
            provider: 'Ecosio'
        );
        
        $repository = Mockery::mock(PaymentPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($paymentPeppol);
        $repository->shouldReceive('update')
            ->once()
            ->andReturn(true);

        $service = new PaymentPeppolService($repository);

        /* Act */
        $result = $service->update(1, $dto);

        /* Assert */
        $this->assertTrue($result);
    }

    #[Test]
    public function it_deletes_payment_peppol(): void
    {
        /* Arrange */
        $paymentPeppol = new PaymentPeppol(['id' => 1]);
        
        $repository = Mockery::mock(PaymentPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($paymentPeppol);
        $repository->shouldReceive('delete')
            ->once()
            ->with($paymentPeppol)
            ->andReturn(true);

        $service = new PaymentPeppolService($repository);

        /* Act */
        $result = $service->delete(1);

        /* Assert */
        $this->assertTrue($result);
    }

    #[Test]
    public function it_returns_false_when_updating_non_existent_payment_peppol(): void
    {
        /* Arrange */
        $dto = new PaymentPeppolDTO(
            id: 999,
            inv_id: 1,
            provider: 'StoreCove'
        );
        
        $repository = Mockery::mock(PaymentPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new PaymentPeppolService($repository);

        /* Act */
        $result = $service->update(999, $dto);

        /* Assert */
        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_false_when_deleting_non_existent_payment_peppol(): void
    {
        /* Arrange */
        $repository = Mockery::mock(PaymentPeppolRepository::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(999)
            ->andReturn(null);

        $service = new PaymentPeppolService($repository);

        /* Act */
        $result = $service->delete(999);

        /* Assert */
        $this->assertFalse($result);
    }
}
