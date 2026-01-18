<?php

namespace Tests\Unit\Services;

use App\Services\UserService;
use App\DTOs\UserDTO;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\FakeUserRepository;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    private FakeUserRepository $repository;
    private UserService $service;
    private $google2fa;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FakeUserRepository();
        $this->google2fa = Mockery::mock(Google2FA::class);
        $this->service = new UserService($this->repository, $this->google2fa);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_user_with_securely_hashed_password(): void
    {
        /* Arrange */
        $plainPassword = 'plain-password';
        $dto = new UserDTO(
            login: 'testuser',
            email: 'test@example.com',
            password: $plainPassword
        );

        /* Act */
        $result = $this->service->create($dto);

        /* Assert */
        $this->assertInstanceOf(User::class, $result);
        $this->assertNotNull($result->id);
        $this->assertEquals('testuser', $result->login);
        $this->assertEquals('test@example.com', $result->email);
        // Verify password was hashed (should not be plain text)
        $this->assertNotEquals($plainPassword, $result->password);
        $this->assertTrue(strlen($result->password ?? '') > 30, 'Hashed password should be longer than plain text');
    }

    #[Test]
    public function it_retrieves_user_by_id(): void
    {
        /* Arrange */
        $user = new User([
            'login' => 'testuser',
            'email' => 'test@example.com'
        ]);
        $user->id = 1;
        $this->repository->add($user);

        /* Act */
        $result = $this->service->getById(1);

        /* Assert */
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals(1, $result->id);
        $this->assertEquals('testuser', $result->login);
    }

    #[Test]
    public function it_retrieves_user_by_email_address(): void
    {
        /* Arrange */
        $email = 'test@example.com';
        $user = new User([
            'login' => 'testuser',
            'email' => $email
        ]);
        $user->id = 1;
        $this->repository->add($user);

        /* Act */
        $result = $this->service->getByEmail($email);

        /* Assert */
        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($email, $result->email);
    }

    #[Test]
    public function it_deletes_user_by_id(): void
    {
        /* Arrange */
        $user = new User([
            'login' => 'testuser',
            'email' => 'test@example.com'
        ]);
        $user->id = 1;
        $this->repository->add($user);

        /* Act */
        $result = $this->service->delete(1);

        /* Assert */
        $this->assertTrue($result);
        $this->assertNull($this->repository->find(1));
    }
}
