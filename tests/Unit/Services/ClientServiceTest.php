<?php

namespace Tests\Unit\Services;

use App\DTOs\ClientDTO;
use App\Models\Client;
use App\Services\ClientService;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeClientRepository;
use Tests\TestCase;

class ClientServiceTest extends TestCase
{
    private FakeClientRepository $repository;
    private ClientService $service;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FakeClientRepository();
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
        
        /* Act */
        $result = $this->service->create($dto);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertNotNull($result->client_id);
        $this->assertEquals('John', $result->client_name);
        $this->assertEquals('Doe', $result->client_surname);
        $this->assertEquals('john@example.com', $result->client_email);
    }
    
    #[Test]
    public function it_retrieves_client_by_id(): void
    {
        /* Arrange */
        $client = new Client([
            'client_name' => 'Jane',
            'client_email' => 'jane@example.com'
        ]);
        $client->client_id = 1;
        $this->repository->add($client);
        
        /* Act */
        $result = $this->service->findById(1);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(1, $result->client_id);
        $this->assertEquals('Jane', $result->client_name);
    }
    
    #[Test]
    public function it_updates_existing_client(): void
    {
        /* Arrange */
        $client = new Client([
            'client_name' => 'John',
            'client_email' => 'john@example.com'
        ]);
        $client->client_id = 1;
        $this->repository->add($client);
        
        $dto = new ClientDTO(
            client_name: 'Jane',
            client_email: 'jane@example.com'
        );
        
        /* Act */
        $result = $this->service->update(1, $dto);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals('Jane', $result->client_name);
        $this->assertEquals('jane@example.com', $result->client_email);
    }
    
    #[Test]
    public function it_deletes_client_by_id(): void
    {
        /* Arrange */
        $client = new Client(['client_name' => 'John']);
        $client->client_id = 1;
        $this->repository->add($client);
        
        /* Act */
        $result = $this->service->delete(1);
        
        /* Assert */
        $this->assertTrue($result);
        $this->assertNull($this->repository->findById(1));
    }
    
    #[Test]
    public function it_searches_clients_by_query_string(): void
    {
        /* Arrange */
        $client1 = new Client(['client_name' => 'John Doe', 'client_email' => 'john@example.com']);
        $client1->client_id = 1;
        $client2 = new Client(['client_name' => 'Jane Smith', 'client_email' => 'jane@example.com']);
        $client2->client_id = 2;
        $this->repository->add($client1);
        $this->repository->add($client2);
        
        /* Act */
        $result = $this->service->search('john');
        
        /* Assert */
        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result->first()->client_name);
    }
    
    #[Test]
    public function it_retrieves_only_active_clients(): void
    {
        /* Arrange */
        $activeClient = new Client(['client_name' => 'Active', 'client_active' => true]);
        $activeClient->client_id = 1;
        $inactiveClient = new Client(['client_name' => 'Inactive', 'client_active' => false]);
        $inactiveClient->client_id = 2;
        $this->repository->add($activeClient);
        $this->repository->add($inactiveClient);
        
        /* Act */
        $result = $this->service->getActive();
        
        /* Assert */
        $this->assertCount(1, $result);
        $this->assertEquals('Active', $result->first()->client_name);
    }
    
    #[Test]
    public function it_retrieves_clients_by_group(): void
    {
        /* Arrange */
        $corporateClient = new Client(['client_name' => 'Corp', 'client_group' => 'corporate']);
        $corporateClient->client_id = 1;
        $retailClient = new Client(['client_name' => 'Retail', 'client_group' => 'retail']);
        $retailClient->client_id = 2;
        $this->repository->add($corporateClient);
        $this->repository->add($retailClient);
        
        /* Act */
        $result = $this->service->getByGroup('corporate');
        
        /* Assert */
        $this->assertCount(1, $result);
        $this->assertEquals('Corp', $result->first()->client_name);
    }
    
    #[Test]
    public function it_restores_soft_deleted_client(): void
    {
        /* Arrange */
        $client = new Client(['client_name' => 'Restored']);
        $client->client_id = 1;
        $this->repository->add($client);
        
        /* Act */
        $result = $this->service->restore(1);
        
        /* Assert */
        $this->assertInstanceOf(Client::class, $result);
        $this->assertEquals(1, $result->client_id);
    }
    
    #[Test]
    public function it_permanently_deletes_client(): void
    {
        /* Arrange */
        $client = new Client(['client_name' => 'ToDelete']);
        $client->client_id = 1;
        $this->repository->add($client);
        
        /* Act */
        $result = $this->service->forceDelete(1);
        
        /* Assert */
        $this->assertTrue($result);
        $this->assertNull($this->repository->findById(1));
    }
    
    #[Test]
    public function it_retrieves_all_clients(): void
    {
        /* Arrange */
        $client1 = new Client(['client_name' => 'Client 1']);
        $client1->client_id = 1;
        $client2 = new Client(['client_name' => 'Client 2']);
        $client2->client_id = 2;
        $this->repository->add($client1);
        $this->repository->add($client2);
        
        /* Act */
        $result = $this->service->getAll();
        
        /* Assert */
        $this->assertCount(2, $result);
    }
}
