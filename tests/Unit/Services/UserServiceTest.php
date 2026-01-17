<?php

namespace Tests\Unit\Services;

use App\Services\UserService;
use App\Repositories\UserRepository;
use App\DTOs\UserDTO;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use PHPUnit\Framework\Attributes\Test;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_user_with_securely_hashed_password(): void
    {
        /* Arrange */
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
                // Verify password is hashed (not plain text)
                return isset($data['password']) 
                    && $data['password'] !== 'plain-password'
                    && strlen($data['password']) > 30; // Hashed password is longer
            }))
            ->andReturn(new User(['id' => 1]));

        $service = new UserService($repository, $google2fa);

        /* Act */
        $result = $service->create($dto);

        /* Assert */
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
    }

    #[Test]
    public function it_retrieves_user_by_id(): void
    {
        /* Arrange */
        $userId = 1;
        $repository = Mockery::mock(UserRepository::class);
        $google2fa = Mockery::mock(Google2FA::class);
        
        $repository->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn(new User(['id' => $userId]));

        $service = new UserService($repository, $google2fa);

        /* Act */
        $result = $service->getById($userId);

        /* Assert */
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($userId, $result->id);
    }

    #[Test]
    public function it_retrieves_user_by_email_address(): void
    {
        /* Arrange */
        $email = 'test@example.com';
        $repository = Mockery::mock(UserRepository::class);
        $google2fa = Mockery::mock(Google2FA::class);
        
        $repository->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn(new User(['email' => $email]));

        $service = new UserService($repository, $google2fa);

        /* Act */
        $result = $service->getByEmail($email);

        /* Assert */
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->email);
    }

    #[Test]
    public function it_deletes_user_by_id(): void
    {
        /* Arrange */
        $userId = 1;
        $user = new User(['id' => $userId]);
        $repository = Mockery::mock(UserRepository::class);
        $google2fa = Mockery::mock(Google2FA::class);
        
        $repository->shouldReceive('find')
            ->once()
            ->with($userId)
            ->andReturn($user);
        
        $repository->shouldReceive('delete')
            ->once()
            ->with($user)
            ->andReturn(true);

        $service = new UserService($repository, $google2fa);

        /* Act */
        $result = $service->delete($userId);

        /* Assert */
        $this->assertTrue($result);
    }
}
