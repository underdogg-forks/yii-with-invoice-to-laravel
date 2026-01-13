<?php

namespace Tests\Unit\Services;

use App\DTOs\ClientDTO;
use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Mockery;
use Tests\TestCase;

class ClientServiceTest extends TestCase
{
    private ClientRepository $repository;
    private ClientService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = Mockery::mock(ClientRepository::class);
        $this->service = new ClientService($this->repository);
    }
    
    public function it_creates_client(): void
    {
        // Arrange
        $dto = new ClientDTO(
            client_name: 'John',
            client_surname: 'Doe',
            client_email: 'john@example.com'
        );
        
        $client = new Client();
        $client->client_id = 1;
        
        $this->repository
            ->shouldReceive('create')
            ->once()
            ->andReturn($client);
        
        // Act
        $result = $this->service->create($dto);
        
        // Assert
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(1, $result->client_id);
    }
    
    public function it_gets_client_by_id(): void
    {
        // Arrange
        $client = new Client();
        $client->client_id = 1;
        
        $this->repository
            ->shouldReceive('findById')
            ->with(1)
            ->once()
            ->andReturn($client);
        
        // Act
        $result = $this->service->getById(1);
        
        // Assert
        $this->assertInstanceOf(Client::class, $result);
    }
    
    public function it_updates_client(): void
    {
        // Arrange
        $dto = new ClientDTO(
            client_id: 1,
            client_name: 'Jane',
            client_email: 'jane@example.com'
        );
        
        $client = new Client();
        $client->client_id = 1;
        
        $this->repository
            ->shouldReceive('update')
            ->once()
            ->andReturn($client);
        
        // Act
        $result = $this->service->update($dto);
        
        // Assert
        $this->assertInstanceOf(Client::class, $result);
    }
    
    public function it_deletes_client(): void
    {
        // Arrange
        $this->repository
            ->shouldReceive('delete')
            ->with(1)
            ->once()
            ->andReturn(true);
        
        // Act
        $result = $this->service->delete(1);
        
        // Assert
        $this->assertTrue($result);
    }
    
    public function it_searches_clients(): void
    {
        // Arrange
        $clients = collect([new Client(), new Client()]);
        
        $this->repository
            ->shouldReceive('search')
            ->with('john', [])
            ->once()
            ->andReturn($clients);
        
        // Act
        $result = $this->service->search('john');
        
        // Assert
        $this->assertCount(2, $result);
    }
    
    public function it_gets_active_clients(): void
    {
        // Arrange
        $clients = collect([new Client(), new Client()]);
        
        $this->repository
            ->shouldReceive('getActive')
            ->once()
            ->andReturn($clients);
        
        // Act
        $result = $this->service->getActive();
        
        // Assert
        $this->assertCount(2, $result);
    }
    
    public function it_gets_clients_by_group(): void
    {
        // Arrange
        $clients = collect([new Client()]);
        
        $this->repository
            ->shouldReceive('getByGroup')
            ->with('corporate')
            ->once()
            ->andReturn($clients);
        
        // Act
        $result = $this->service->getByGroup('corporate');
        
        // Assert
        $this->assertCount(1, $result);
    }
    
    public function it_restores_deleted_client(): void
    {
        // Arrange
        $this->repository
            ->shouldReceive('restore')
            ->with(1)
            ->once()
            ->andReturn(true);
        
        // Act
        $result = $this->service->restore(1);
        
        // Assert
        $this->assertTrue($result);
    }
    
    public function it_force_deletes_client(): void
    {
        // Arrange
        $this->repository
            ->shouldReceive('forceDelete')
            ->with(1)
            ->once()
            ->andReturn(true);
        
        // Act
        $result = $this->service->forceDelete(1);
        
        // Assert
        $this->assertTrue($result);
    }
    
    public function it_gets_all_clients_with_trashed(): void
    {
        // Arrange
        $clients = collect([new Client(), new Client()]);
        
        $this->repository
            ->shouldReceive('getAllWithTrashed')
            ->once()
            ->andReturn($clients);
        
        // Act
        $result = $this->service->getAllWithTrashed();
        
        // Assert
        $this->assertCount(2, $result);
    }
}
