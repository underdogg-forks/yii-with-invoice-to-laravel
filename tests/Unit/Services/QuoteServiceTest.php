<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\QuoteService;
use App\Models\Quote;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeQuoteRepository;

class QuoteServiceTest extends TestCase
{
    private QuoteService $service;
    private FakeQuoteRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FakeQuoteRepository();
        $this->service = new QuoteService($this->repository);
    }

    #[Test]
    public function it_retrieves_quote_by_id(): void
    {
        /* Arrange */
        $quote = new Quote([
            'client_id' => 1,
            'quote_number' => 'Q-2026-001',
            'total' => 1210.00
        ]);
        $quote->id = 1;
        $this->repository->add($quote);

        /* Act */
        $result = $this->service->getById(1);

        /* Assert */
        $this->assertInstanceOf(Quote::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('Q-2026-001', $result->quote_number);
    }

    #[Test]
    public function it_deletes_quote_by_id(): void
    {
        /* Arrange */
        $quote = new Quote(['quote_number' => 'Q-2026-001']);
        $quote->id = 1;
        $this->repository->add($quote);

        /* Act */
        $result = $this->service->delete(1);

        /* Assert */
        $this->assertTrue($result);
        $this->assertNull($this->repository->findById(1));
    }

    #[Test]
    public function it_retrieves_all_quotes(): void
    {
        /* Arrange */
        $quote1 = new Quote(['quote_number' => 'Q-2026-001']);
        $quote1->id = 1;
        $quote2 = new Quote(['quote_number' => 'Q-2026-002']);
        $quote2->id = 2;
        $this->repository->add($quote1);
        $this->repository->add($quote2);

        /* Act */
        $result = $this->service->getAll();

        /* Assert */
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_searches_quotes_by_number(): void
    {
        /* Arrange */
        $quote1 = new Quote(['quote_number' => 'Q-2026-001']);
        $quote1->id = 1;
        $quote2 = new Quote(['quote_number' => 'Q-2026-002']);
        $quote2->id = 2;
        $this->repository->add($quote1);
        $this->repository->add($quote2);

        /* Act */
        $result = $this->repository->search('Q-2026-001');

        /* Assert */
        $this->assertCount(1, $result);
        $this->assertEquals('Q-2026-001', $result->first()->quote_number);
    }
}
