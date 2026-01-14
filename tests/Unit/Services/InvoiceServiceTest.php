<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\InvoiceService;
use App\Repositories\InvoiceRepository;
use App\DTOs\InvoiceDTO;
use App\Models\Invoice;
use Mockery;

class InvoiceServiceTest extends TestCase
{
    private InvoiceService $service;
    private $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = Mockery::mock(InvoiceRepository::class);
        $this->service = new InvoiceService($this->repositoryMock);
    }

    public function it_creates_invoice_with_valid_data(): void
    {
        // Arrange
        $dto = new InvoiceDTO(
            client_id: 1,
            date_invoice: '2026-01-01',
            date_due: '2026-01-31'
        );
        
        $invoice = new Invoice();
        $invoice->id = 1;
        
        $this->repositoryMock
            ->shouldReceive('create')
            ->once()
            ->andReturn($invoice);

        // Act
        $result = $this->service->create($dto);

        // Assert
        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals(1, $result->id);
    }

    public function it_updates_invoice_with_valid_data(): void
    {
        // Arrange
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

        // Act
        $result = $this->service->update($dto);

        // Assert
        $this->assertInstanceOf(Invoice::class, $result);
    }

    public function it_deletes_invoice_by_id(): void
    {
        // Arrange
        $invoiceId = 1;
        
        $this->repositoryMock
            ->shouldReceive('delete')
            ->once()
            ->with($invoiceId)
            ->andReturn(true);

        // Act
        $result = $this->service->delete($invoiceId);

        // Assert
        $this->assertTrue($result);
    }

    public function it_finds_invoice_by_id(): void
    {
        // Arrange
        $invoiceId = 1;
        $invoice = new Invoice();
        $invoice->id = $invoiceId;
        
        $this->repositoryMock
            ->shouldReceive('findById')
            ->once()
            ->with($invoiceId)
            ->andReturn($invoice);

        // Act
        $result = $this->service->findById($invoiceId);

        // Assert
        $this->assertInstanceOf(Invoice::class, $result);
        $this->assertEquals($invoiceId, $result->id);
    }

    public function it_gets_all_invoices(): void
    {
        // Arrange
        $invoices = collect([new Invoice(), new Invoice()]);
        
        $this->repositoryMock
            ->shouldReceive('getAll')
            ->once()
            ->andReturn($invoices);

        // Act
        $result = $this->service->getAll();

        // Assert
        $this->assertCount(2, $result);
    }

    public function it_changes_invoice_status(): void
    {
        // Arrange
        $invoiceId = 1;
        $statusId = 2;
        $invoice = new Invoice();
        $invoice->id = $invoiceId;
        
        $this->repositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($invoiceId, ['status_id' => $statusId])
            ->andReturn($invoice);

        // Act
        $result = $this->service->changeStatus($invoiceId, $statusId);

        // Assert
        $this->assertInstanceOf(Invoice::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
