<?php

namespace Tests\Unit\Services\Peppol;

use App\Enums\PeppolProvider;
use App\Services\Http\ApiClient;
use App\Services\Peppol\EInvoicingBeClient;
use App\Services\Peppol\LetsPeppolClient;
use App\Services\Peppol\PeppolProviderFactory;
use App\Services\Peppol\PeppyrusClient;
use App\Services\Peppol\StoreCoveClient;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PeppolProviderFactory::class)]
class PeppolProviderFactoryTest extends TestCase
{
    private ApiClient $mockApiClient;
    private PeppolProviderFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockApiClient = Mockery::mock(ApiClient::class);
        $this->factory = new PeppolProviderFactory($this->mockApiClient);
    }

    #[Test]
    public function it_creates_storecove_client(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->create(PeppolProvider::STORECOVE);

        /* Assert */
        $this->assertInstanceOf(StoreCoveClient::class, $client);
    }

    #[Test]
    public function it_creates_letspeppol_client(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->create(PeppolProvider::LETSPEPPOL);

        /* Assert */
        $this->assertInstanceOf(LetsPeppolClient::class, $client);
    }

    #[Test]
    public function it_creates_peppyrus_client(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->create(PeppolProvider::PEPPYRUS);

        /* Assert */
        $this->assertInstanceOf(PeppyrusClient::class, $client);
    }

    #[Test]
    public function it_creates_einvoicing_be_client(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->create(PeppolProvider::EINVOICING_BE);

        /* Assert */
        $this->assertInstanceOf(EInvoicingBeClient::class, $client);
    }

    #[Test]
    public function it_creates_client_from_string(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->createFromString('storecove');

        /* Assert */
        $this->assertInstanceOf(StoreCoveClient::class, $client);
    }

    #[Test]
    public function it_creates_letspeppol_client_from_string(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->createFromString('letspeppol');

        /* Assert */
        $this->assertInstanceOf(LetsPeppolClient::class, $client);
    }

    #[Test]
    public function it_creates_peppyrus_client_from_string(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->createFromString('peppyrus');

        /* Assert */
        $this->assertInstanceOf(PeppyrusClient::class, $client);
    }

    #[Test]
    public function it_creates_einvoicing_be_client_from_string(): void
    {
        /* Arrange */
        $this->mockApiClient->shouldReceive('__clone')->once()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->once();
        $this->mockApiClient->shouldReceive('setHeaders')->once();

        /* Act */
        $client = $this->factory->createFromString('einvoicing_be');

        /* Assert */
        $this->assertInstanceOf(EInvoicingBeClient::class, $client);
    }

    #[Test]
    public function it_throws_exception_for_invalid_provider_string(): void
    {
        /* Act & Assert */
        $this->expectException(\ValueError::class);
        $this->factory->createFromString('invalid-provider');
    }

    #[Test]
    public function it_clones_api_client_for_each_provider(): void
    {
        /* Arrange */
        // Each call to create() should clone the API client
        $this->mockApiClient->shouldReceive('__clone')->twice()->andReturnSelf();
        $this->mockApiClient->shouldReceive('setBaseUrl')->twice();
        $this->mockApiClient->shouldReceive('setHeaders')->twice();

        /* Act */
        $client1 = $this->factory->create(PeppolProvider::STORECOVE);
        $client2 = $this->factory->create(PeppolProvider::LETSPEPPOL);

        /* Assert */
        $this->assertNotSame($client1->getApiClient(), $client2->getApiClient());
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
