<?php

namespace Tests\Support;

use Mockery;
use Mockery\MockInterface;

/**
 * Trait for creating repository mocks with common patterns.
 * Reduces duplication across service tests following DRY principle.
 */
trait MocksRepositories
{
    /**
     * Create a repository mock that expects a create call.
     */
    protected function mockRepositoryCreate(MockInterface $repository, object $model, ?array $expectedPayload = null): void
    {
        $expectation = $repository->shouldReceive('create')->once();
        if ($expectedPayload !== null) {
            $expectation->with($expectedPayload);
        }
        $expectation->andReturn($model);
    }

    /**
     * Create a repository mock that expects a findById call.
     */
    protected function mockRepositoryFindById(MockInterface $repository, int $id, ?object $model): void
    {
        $repository->shouldReceive('findById')
            ->with($id)
            ->once()
            ->andReturn($model);
    }

    /**
     * Create a repository mock that expects an update call.
     */
    protected function mockRepositoryUpdate(MockInterface $repository, object $model, ?array $expectedPayload = null): void
    {
        $expectation = $repository->shouldReceive('update')->once();
        if ($expectedPayload !== null) {
            $expectation->with($expectedPayload);
        }
        $expectation->andReturn($model);
    }

    /**
     * Create a repository mock that expects a delete call.
     */
    protected function mockRepositoryDelete(MockInterface $repository, int $id, bool $result = true): void
    {
        $repository->shouldReceive('delete')
            ->with($id)
            ->once()
            ->andReturn($result);
    }

    /**
     * Create a repository mock that expects a search call.
     */
    protected function mockRepositorySearch(MockInterface $repository, string $query, $result, array $filters = []): void
    {
        $repository->shouldReceive('search')
            ->with($query, $filters)
            ->once()
            ->andReturn($result);
    }

    /**
     * Create a repository mock that expects a getActive call.
     */
    protected function mockRepositoryGetActive(MockInterface $repository, $result): void
    {
        $repository->shouldReceive('getActive')
            ->once()
            ->andReturn($result);
    }

    /**
     * Create a repository mock that expects a restore call.
     */
    protected function mockRepositoryRestore(MockInterface $repository, int $id, bool $result = true): void
    {
        $repository->shouldReceive('restore')
            ->with($id)
            ->once()
            ->andReturn($result);
    }

    /**
     * Create a repository mock that expects a forceDelete call.
     */
    protected function mockRepositoryForceDelete(MockInterface $repository, int $id, bool $result = true): void
    {
        $repository->shouldReceive('forceDelete')
            ->with($id)
            ->once()
            ->andReturn($result);
    }

    /**
     * Create a repository mock that expects a getAllWithTrashed call.
     */
    protected function mockRepositoryGetAllWithTrashed(MockInterface $repository, $result): void
    {
        $repository->shouldReceive('getAllWithTrashed')
            ->once()
            ->andReturn($result);
    }

    /**
     * Create a repository mock that expects a getByGroup call.
     */
    protected function mockRepositoryGetByGroup(MockInterface $repository, string $group, $result): void
    {
        $repository->shouldReceive('getByGroup')
            ->with($group)
            ->once()
            ->andReturn($result);
    }
}
