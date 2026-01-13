<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\QuoteService;
use App\Repositories\QuoteRepository;
use App\DTOs\QuoteDTO;
use App\Models\Quote;
use App\Models\QuoteStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class QuoteServiceTest extends TestCase
{
    use RefreshDatabase;

    private QuoteService $service;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = Mockery::mock(QuoteRepository::class);
        $this->service = new QuoteService($this->mockRepository);
    }

    public function it_creates_quote_with_dto(): void
    {
        // Arrange
        $dto = new QuoteDTO(
            client_id: 1,
            quote_number: 'Q-2026-001',
            quote_date: '2026-01-13',
            expiry_date: '2026-02-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 0,
            total: 1210.00
        );

        $expectedQuote = new Quote($dto->toArray());
        $this->mockRepository->shouldReceive('create')
            ->once()
            ->andReturn($expectedQuote);

        // Act
        $result = $this->service->create($dto);

        // Assert
        $this->assertInstanceOf(Quote::class, $result);
    }

    public function it_gets_quote_by_id(): void
    {
        // Arrange
        $quoteId = 1;
        $expectedQuote = new Quote(['id' => $quoteId]);
        $this->mockRepository->shouldReceive('findById')
            ->with($quoteId)
            ->once()
            ->andReturn($expectedQuote);

        // Act
        $result = $this->service->getById($quoteId);

        // Assert
        $this->assertInstanceOf(Quote::class, $result);
        $this->assertEquals($quoteId, $result->id);
    }

    public function it_updates_quote_with_dto(): void
    {
        // Arrange
        $dto = new QuoteDTO(
            id: 1,
            client_id: 1,
            quote_number: 'Q-2026-001',
            quote_date: '2026-01-13',
            expiry_date: '2026-02-13',
            subtotal: 1000.00,
            tax_total: 210.00,
            discount_total: 0,
            total: 1210.00
        );

        $expectedQuote = new Quote($dto->toArray());
        $this->mockRepository->shouldReceive('update')
            ->once()
            ->andReturn($expectedQuote);

        // Act
        $result = $this->service->update($dto);

        // Assert
        $this->assertInstanceOf(Quote::class, $result);
    }

    public function it_deletes_quote(): void
    {
        // Arrange
        $quoteId = 1;
        $this->mockRepository->shouldReceive('delete')
            ->with($quoteId)
            ->once()
            ->andReturn(true);

        // Act
        $result = $this->service->delete($quoteId);

        // Assert
        $this->assertTrue($result);
    }

    public function it_gets_all_quotes_with_filters(): void
    {
        // Arrange
        $status = 'draft';
        $clientId = 1;
        $search = 'Q-2026';

        $this->mockRepository->shouldReceive('getAll')
            ->with($status, $clientId, $search)
            ->once()
            ->andReturn(collect([]));

        // Act
        $result = $this->service->getAll($status, $clientId, $search);

        // Assert
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function it_sends_quote(): void
    {
        // Arrange
        QuoteStatus::factory()->create(['code' => 'sent']);
        $quote = Quote::factory()->create();

        $this->mockRepository->shouldReceive('findById')
            ->with($quote->id)
            ->once()
            ->andReturn($quote);

        $this->mockRepository->shouldReceive('update')
            ->once()
            ->andReturn($quote);

        // Act
        $result = $this->service->send($quote->id);

        // Assert
        $this->assertInstanceOf(Quote::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
