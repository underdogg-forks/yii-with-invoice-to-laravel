<?php

namespace Tests\Unit\Services;

use App\DTOs\ClientDTO;
use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
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
    
    #[Test]
    public function it_creates_client_from_dto(): void
    {
        /* Arrange */
        $dto = new ClientDTO(
            client_name: 'John',
            client_surname: 'Doe',
            client_email: 'john@example.com'
        );
        
        $client = new Client();
        $client->client_id = 1;
        
        $this->repository->shouldReceive('create')
            ->once()
            ->andReturn($client);
        
        /* Act */
        $result = $this->service->create($dto);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(1, $result->client_id);
    }
    
    #[Test]
    public function it_retrieves_client_by_id(): void
    {
        /* Arrange */
        $clientId = 1;
        $client = new Client();
        $client->client_id = $clientId;
        
        $this->repository->shouldReceive('findById')
            ->with($clientId)
            ->once()
            ->andReturn($client);
        
        /* Act */
        $result = $this->service->findById($clientId);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($clientId, $result->client_id);
    }
    
    #[Test]
    public function it_updates_existing_client(): void
    {
        /* Arrange */
        $clientId = 1;
        $dto = new ClientDTO(
            client_name: 'Jane',
            client_email: 'jane@example.com'
        );
        
        $client = new Client();
        $client->client_id = 1;
        $client->client_name = 'Jane';
        
        $this->repository->shouldReceive('findById')
            ->with($clientId)
            ->once()
            ->andReturn($client);
        
        $this->repository->shouldReceive('update')
            ->once()
            ->andReturn($client);
        
        /* Act */
        $result = $this->service->update($clientId, $dto);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals('Jane', $result->client_name);
    }
    
    #[Test]
    public function it_deletes_client_by_id(): void
    {
        /* Arrange */
        $clientId = 1;
        $client = new Client();
        $client->client_id = $clientId;
        
        $this->repository->shouldReceive('findById')
            ->with($clientId)
            ->once()
            ->andReturn($client);
        
        $this->repository->shouldReceive('delete')
            ->with($client)
            ->once()
            ->andReturn(true);
        
        /* Act */
        $result = $this->service->delete($clientId);
        
        /* Assert */
        $this->assertTrue($result);
    }
    
    #[Test]
    public function it_searches_clients_by_query_string(): void
    {
        /* Arrange */
        $query = 'john';
        $clients = new \Illuminate\Database\Eloquent\Collection([new Client(), new Client()]);
        
        $this->repository->shouldReceive('search')
            ->with($query)
            ->once()
            ->andReturn($clients);
        
        /* Act */
        $result = $this->service->search($query);
        
        /* Assert */
        $this->assertCount(2, $result);
    }
    
    #[Test]
    public function it_retrieves_only_active_clients(): void
    {
        /* Arrange */
        $activeClient1 = new Client();
        $activeClient1->client_active = true;
        $activeClient2 = new Client();
        $activeClient2->client_active = true;
        
        $clients = new \Illuminate\Database\Eloquent\Collection([$activeClient1, $activeClient2]);
        
        $this->repository->shouldReceive('findActive')
            ->once()
            ->andReturn($clients);
        
        /* Act */
        $result = $this->service->getActive();
        
        /* Assert */
        $this->assertCount(2, $result);
    }
    
    #[Test]
    public function it_retrieves_clients_by_group(): void
    {
        /* Arrange */
        $group = 'corporate';
        $client = new Client();
        $clients = new \Illuminate\Database\Eloquent\Collection([$client]);
        
        $this->repository->shouldReceive('findByGroup')
            ->with($group)
            ->once()
            ->andReturn($clients);
        
        /* Act */
        $result = $this->service->getByGroup($group);
        
        /* Assert */
        $this->assertCount(1, $result);
    }
    
    #[Test]
    public function it_restores_soft_deleted_client(): void
    {
        /* Arrange */
        $clientId = 1;
        $client = new Client();
        $client->client_id = $clientId;
        
        $this->repository->shouldReceive('restore')
            ->with($clientId)
            ->once()
            ->andReturn($client);
        
        /* Act */
        $result = $this->service->restore($clientId);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
    }
    
    #[Test]
    public function it_permanently_deletes_client(): void
    {
        /* Arrange */
        $clientId = 1;
        $client = new Client();
        $client->client_id = $clientId;
        
        $this->repository->shouldReceive('findById')
            ->with($clientId, true)
            ->once()
            ->andReturn($client);
        
        $this->repository->shouldReceive('forceDelete')
            ->with($client)
            ->once()
            ->andReturn(true);
        
        /* Act */
        $result = $this->service->forceDelete($clientId);
        
        /* Assert */
        $this->assertTrue($result);
    }
    
    #[Test]
    public function it_retrieves_all_clients(): void
    {
        /* Arrange */
        $client1 = new Client();
        $client2 = new Client();
        $clients = new \Illuminate\Database\Eloquent\Collection([$client1, $client2]);
        
        $this->repository->shouldReceive('all')
            ->once()
            ->andReturn($clients);
        
        /* Act */
        $result = $this->service->getAll();
        
        /* Assert */
        $this->assertCount(2, $result);
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
