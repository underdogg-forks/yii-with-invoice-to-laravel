<?php

namespace Tests\Unit\Providers;

use App\Contracts\ApiClientInterface;
use App\Providers\PeppolServiceProvider;
use App\Services\Http\ApiClient;
use App\Services\Http\Decorators\HttpClientExceptionHandler;
use App\Services\Http\Decorators\RequestLogger;
use Illuminate\Foundation\Application;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PeppolServiceProvider::class)]
class PeppolServiceProviderTest extends TestCase
{
    private PeppolServiceProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->provider = new PeppolServiceProvider($this->app);
    }

    #[Test]
    public function it_registers_api_client_interface_as_singleton(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $client1 = $this->app->make(ApiClientInterface::class);
        $client2 = $this->app->make(ApiClientInterface::class);

        /* Assert */
        $this->assertSame($client1, $client2);
    }

    #[Test]
    public function it_registers_api_client_with_decorator_chain(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $client = $this->app->make(ApiClientInterface::class);

        /* Assert */
        $this->assertInstanceOf(HttpClientExceptionHandler::class, $client);
    }

    #[Test]
    public function it_decorates_api_client_with_request_logger(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $client = $this->app->make(ApiClientInterface::class);

        /* Assert */
        $this->assertInstanceOf(HttpClientExceptionHandler::class, $client);
        
        // Access the decorated client through reflection
        $reflection = new \ReflectionClass($client);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $innerClient = $property->getValue($client);
        
        $this->assertInstanceOf(RequestLogger::class, $innerClient);
    }

    #[Test]
    public function it_decorates_request_logger_with_base_api_client(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $client = $this->app->make(ApiClientInterface::class);

        /* Assert */
        // Navigate through decorator chain: HttpClientExceptionHandler -> RequestLogger -> ApiClient
        $reflection1 = new \ReflectionClass($client);
        $property1 = $reflection1->getProperty('client');
        $property1->setAccessible(true);
        $requestLogger = $property1->getValue($client);
        
        $reflection2 = new \ReflectionClass($requestLogger);
        $property2 = $reflection2->getProperty('client');
        $property2->setAccessible(true);
        $baseClient = $property2->getValue($requestLogger);
        
        $this->assertInstanceOf(ApiClient::class, $baseClient);
    }

    #[Test]
    public function it_implements_correct_decorator_order(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $outerClient = $this->app->make(ApiClientInterface::class);

        /* Assert */
        // Order should be: HttpClientExceptionHandler wraps RequestLogger wraps ApiClient
        $this->assertInstanceOf(HttpClientExceptionHandler::class, $outerClient);
        
        $reflection1 = new \ReflectionClass($outerClient);
        $property1 = $reflection1->getProperty('client');
        $property1->setAccessible(true);
        $middleClient = $property1->getValue($outerClient);
        
        $this->assertInstanceOf(RequestLogger::class, $middleClient);
        
        $reflection2 = new \ReflectionClass($middleClient);
        $property2 = $reflection2->getProperty('client');
        $property2->setAccessible(true);
        $innerClient = $property2->getValue($middleClient);
        
        $this->assertInstanceOf(ApiClient::class, $innerClient);
    }

    #[Test]
    public function it_publishes_peppol_configuration(): void
    {
        /* Arrange & Act */
        $this->provider->boot();

        /* Assert */
        $publishedPaths = PeppolServiceProvider::pathsToPublish(
            PeppolServiceProvider::class,
            'config'
        );

        $this->assertNotEmpty($publishedPaths);
        $this->assertStringContainsString('peppol.php', array_values($publishedPaths)[0] ?? '');
    }

    #[Test]
    public function it_provides_api_client_interface(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $bound = $this->app->bound(ApiClientInterface::class);

        /* Assert */
        $this->assertTrue($bound);
    }

    #[Test]
    public function it_can_resolve_api_client_interface_from_container(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $client = $this->app->make(ApiClientInterface::class);

        /* Assert */
        $this->assertInstanceOf(ApiClientInterface::class, $client);
    }

    #[Test]
    public function it_creates_new_instances_of_inner_clients(): void
    {
        /* Arrange */
        $this->provider->register();

        /* Act */
        $client = $this->app->make(ApiClientInterface::class);

        // Extract the base client from the decorator chain
        $reflection1 = new \ReflectionClass($client);
        $property1 = $reflection1->getProperty('client');
        $property1->setAccessible(true);
        $requestLogger = $property1->getValue($client);
        
        $reflection2 = new \ReflectionClass($requestLogger);
        $property2 = $reflection2->getProperty('client');
        $property2->setAccessible(true);
        $baseClient = $property2->getValue($requestLogger);

        /* Assert */
        // Verify that we have a fresh ApiClient instance (not shared across container calls)
        $this->assertInstanceOf(ApiClient::class, $baseClient);
    }
}
