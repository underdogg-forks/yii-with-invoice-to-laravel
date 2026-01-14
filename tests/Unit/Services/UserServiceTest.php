<?php

namespace Tests\Unit\Services;

use App\Services\UserService;
use App\Repositories\UserRepository;
use App\DTOs\UserDTO;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function it_creates_user_with_hashed_password(): void
    {
        // Arrange
        $repository = Mockery::mock(UserRepository::class);
        $google2fa = Mockery::mock(Google2FA::class);
        
        $dto = new UserDTO(
            login: 'testuser',
            email: 'test@example.com',
            password: 'plain-password'
        );

        $repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($data) {
                return isset($data['password']) 
                    && $data['password'] !== 'plain-password'
                    && strlen($data['password']) > 30; // Hashed password is longer
            }))
            ->andReturn(new User(['id' => 1]));

        $service = new UserService($repository, $google2fa);

        // Act
        $result = $service->create($dto);

        // Assert
        $this->assertInstanceOf(User::class, $result);
    }

    public function it_gets_user_by_id(): void
    {
        // Arrange
        $repository = Mockery::mock(UserRepository::class);
        $google2fa = Mockery::mock(Google2FA::class);
        
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn(new User(['id' => 1]));

        $service = new UserService($repository, $google2fa);

        // Act
        $result = $service->getById(1);

        // Assert
        $this->assertInstanceOf(User::class, $result);
    }

    public function it_gets_user_by_email(): void
    {
        // Arrange
        $repository = Mockery::mock(UserRepository::class);
        $google2fa = Mockery::mock(Google2FA::class);
        
        $repository->shouldReceive('findByEmail')
            ->once()
            ->with('test@example.com')
            ->andReturn(new User(['email' => 'test@example.com']));

        $service = new UserService($repository, $google2fa);

        // Act
        $result = $service->getByEmail('test@example.com');

        // Assert
        $this->assertInstanceOf(User::class, $result);
    }

    public function it_deletes_user(): void
    {
        // Arrange
        $user = new User(['id' => 1]);
        $repository = Mockery::mock(UserRepository::class);
        $google2fa = Mockery::mock(Google2FA::class);
        
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($user);
        
        $repository->shouldReceive('delete')
            ->once()
            ->with($user)
            ->andReturn(true);

        $service = new UserService($repository, $google2fa);

        // Act
        $result = $service->delete(1);

        // Assert
        $this->assertTrue($result);
    }
}
