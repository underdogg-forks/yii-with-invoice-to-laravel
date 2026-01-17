<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\QuoteService;
use App\Repositories\QuoteRepository;
use App\DTOs\QuoteDTO;
use App\Models\Quote;
use App\Models\QuoteStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\MocksRepositories;
use Mockery;

class QuoteServiceTest extends TestCase
{
    use RefreshDatabase, MocksRepositories;

    private QuoteService $service;
    private $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRepository = Mockery::mock(QuoteRepository::class);
        $this->service = new QuoteService($this->mockRepository);
    }

    #[Test]
    public function it_creates_quote_from_dto(): void
    {
        /* Arrange */
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
        $this->mockRepositoryCreate($this->mockRepository, $expectedQuote);

        /* Act */
        $result = $this->service->create($dto);

        /* Assert */
        $this->assertInstanceOf(Quote::class, $result);
        $this->assertEquals('Q-2026-001', $result->quote_number);
        $this->assertEquals(1210.00, $result->total);
    }

    #[Test]
    public function it_retrieves_quote_by_id(): void
    {
        /* Arrange */
        $quoteId = 1;
        $expectedQuote = new Quote(['id' => $quoteId]);
        
        $this->mockRepositoryFindById($this->mockRepository, $quoteId, $expectedQuote);

        /* Act */
        $result = $this->service->getById($quoteId);

        /* Assert */
        $this->assertInstanceOf(Quote::class, $result);
        $this->assertEquals($quoteId, $result->id);
    }

    #[Test]
    public function it_updates_existing_quote(): void
    {
        /* Arrange */
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
        $this->mockRepositoryUpdate($this->mockRepository, $expectedQuote);

        /* Act */
        $result = $this->service->update($dto);

        /* Assert */
        $this->assertInstanceOf(Quote::class, $result);
    }

    #[Test]
    public function it_deletes_quote_by_id(): void
    {
        /* Arrange */
        $quoteId = 1;
        $this->mockRepositoryDelete($this->mockRepository, $quoteId, true);

        /* Act */
        $result = $this->service->delete($quoteId);

        /* Assert */
        $this->assertTrue($result);
    }

    #[Test]
    public function it_retrieves_quotes_with_multiple_filters(): void
    {
        /* Arrange */
        $status = 'draft';
        $clientId = 1;
        $search = 'Q-2026';

        $this->mockRepository->shouldReceive('getAll')
            ->with($status, $clientId, $search)
            ->once()
            ->andReturn(collect([]));

        /* Act */
        $result = $this->service->getAll($status, $clientId, $search);

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    #[Test]
    public function it_sends_quote_and_updates_status(): void
    {
        /* Arrange */
        QuoteStatus::factory()->create(['code' => 'sent']);
        $quote = Quote::factory()->create();

        $this->mockRepositoryFindById($this->mockRepository, $quote->id, $quote);
        $this->mockRepositoryUpdate($this->mockRepository, $quote);

        /* Act */
        $result = $this->service->send($quote->id);

        /* Assert */
        $this->assertInstanceOf(Quote::class, $result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
