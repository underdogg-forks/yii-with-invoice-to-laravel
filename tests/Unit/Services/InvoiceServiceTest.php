<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\InvoiceService;
use App\Repositories\InvoiceRepository;
use App\DTOs\InvoiceDTO;
use App\Models\Invoice;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\MocksRepositories;
use Mockery;

class InvoiceServiceTest extends TestCase
{
    use MocksRepositories;

    private InvoiceService $service;
    private $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(InvoiceRepository::class);
        $this->service = new InvoiceService($this->repositoryMock);
    }

    #[Test]
    public function it_creates_invoice_from_dto(): void
    {
        /* Arrange */
        $dto = new InvoiceDTO(
            client_id: 1,
            date_invoice: '2026-01-01',
            date_due: '2026-01-31'
        );
        
        $invoice = new Invoice();
        $invoice->id = 1;
        $invoice->client_id = 1;
        
        $this->mockRepositoryCreate($this->repositoryMock, $invoice);

        /* Act */
        $result = $this->service->create($dto);

        /* Assert */
        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals(1, $result->client_id);
    }

    #[Test]
    public function it_updates_existing_invoice(): void
    {
        /* Arrange */
        $dto = new InvoiceDTO(
            id: 1,
            client_id: 1,
            date_invoice: '2026-01-01',
            date_due: '2026-01-31'
        );
        
        $invoice = new Invoice();
        $invoice->id = 1;
        
        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(1, Mockery::type('array'))
            ->andReturn($invoice);

        /* Act */
        $result = $this->service->update($dto);

        /* Assert */
        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_deletes_invoice_by_id(): void
    {
        /* Arrange */
        $invoiceId = 1;
        $this->mockRepositoryDelete($this->repositoryMock, $invoiceId, true);

        /* Act */
        $result = $this->service->delete($invoiceId);

        /* Assert */
        $this->assertTrue($result);
    }

    #[Test]
    public function it_retrieves_invoice_by_id(): void
    {
        /* Arrange */
        $invoiceId = 1;
        $invoice = new Invoice();
        $invoice->id = $invoiceId;
        
        $this->mockRepositoryFindById($this->repositoryMock, $invoiceId, $invoice);

        /* Act */
        $result = $this->service->findById($invoiceId);

        /* Assert */
        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals($invoiceId, $result->id);
    }

    #[Test]
    public function it_retrieves_all_invoices(): void
    {
        /* Arrange */
        $invoice1 = new Invoice();
        $invoice1->id = 1;
        $invoice2 = new Invoice();
        $invoice2->id = 2;
        $invoices = collect([$invoice1, $invoice2]);
        
        $this->repositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($invoices);

        /* Act */
        $result = $this->service->getAll();

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    #[Test]
    public function it_changes_invoice_status(): void
    {
        /* Arrange */
        $invoiceId = 1;
        $statusId = 2;
        $invoice = new Invoice();
        $invoice->id = $invoiceId;
        $invoice->status_id = $statusId;
        
        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($invoiceId, ['status_id' => $statusId])
            ->andReturn($invoice);

        /* Act */
        $result = $this->service->changeStatus($invoiceId, $statusId);

        /* Assert */
        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals($statusId, $result->status_id);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
