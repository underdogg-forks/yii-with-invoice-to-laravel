<?php

namespace Tests\Unit\Services;

use App\DTOs\ClientDTO;
use App\Models\Client;
use App\Repositories\ClientRepository;
use App\Services\ClientService;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\MocksRepositories;
use Tests\TestCase;

class ClientServiceTest extends TestCase
{
    use MocksRepositories;

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
        
        $this->mockRepositoryCreate($this->repository, $client);
        
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
        
        $this->mockRepositoryFindById($this->repository, $clientId, $client);
        
        /* Act */
        $result = $this->service->getById($clientId);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals($clientId, $result->client_id);
    }
    
    #[Test]
    public function it_updates_existing_client(): void
    {
        /* Arrange */
        $dto = new ClientDTO(
            client_id: 1,
            client_name: 'Jane',
            client_email: 'jane@example.com'
        );
        
        $client = new Client();
        $client->client_id = 1;
        $client->client_name = 'Jane';
        
        $this->mockRepositoryUpdate($this->repository, $client);
        
        /* Act */
        $result = $this->service->update($dto);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals('Jane', $result->client_name);
    }
    
    #[Test]
    public function it_deletes_client_by_id(): void
    {
        /* Arrange */
        $clientId = 1;
        $this->mockRepositoryDelete($this->repository, $clientId, true);
        
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
        $clients = collect([new Client(), new Client()]);
        
        $this->mockRepositorySearch($this->repository, $query, $clients);
        
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
        
        $clients = collect([$activeClient1, $activeClient2]);
        
        $this->mockRepositoryGetActive($this->repository, $clients);
        
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
        $clients = collect([$client]);
        
        $this->mockRepositoryGetByGroup($this->repository, $group, $clients);
        
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
        $this->mockRepositoryRestore($this->repository, $clientId, true);
        
        /* Act */
        $result = $this->service->restore($clientId);
        
        /* Assert */
        $this->assertTrue($result);
    }
    
    #[Test]
    public function it_permanently_deletes_client(): void
    {
        /* Arrange */
        $clientId = 1;
        $this->mockRepositoryForceDelete($this->repository, $clientId, true);
        
        /* Act */
        $result = $this->service->forceDelete($clientId);
        
        /* Assert */
        $this->assertTrue($result);
    }
    
    #[Test]
    public function it_retrieves_all_clients_including_trashed(): void
    {
        /* Arrange */
        $client1 = new Client();
        $client2 = new Client();
        $clients = collect([$client1, $client2]);
        
        $this->mockRepositoryGetAllWithTrashed($this->repository, $clients);
        
        /* Act */
        $result = $this->service->getAllWithTrashed();
        
        /* Assert */
        $this->assertCount(2, $result);
    }
}
