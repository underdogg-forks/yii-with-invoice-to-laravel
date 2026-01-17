<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\RecoveryCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_hashes_password_on_creation(): void
    {
        /* Arrange */
        $password = 'secret-password';

        /* Act */
        $user = User::factory()->create([
            'password' => $password,
        ]);

        /* Assert */
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(password_verify($password, $user->password));
    }

    #[Test]
    public function it_generates_recovery_codes(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        $expectedCount = 8;

        /* Act */
        $codes = $user->generateRecoveryCodes($expectedCount);

        /* Assert */
        $this->assertCount($expectedCount, $codes);
        $this->assertCount($expectedCount, $user->recoveryCodes);
        foreach ($codes as $code) {
            $this->assertEquals(10, strlen($code));
        }
    }

    #[Test]
    public function it_verifies_valid_recovery_code(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        $codes = $user->generateRecoveryCodes();
        $validCode = $codes[0];

        /* Act */
        $result = $user->verifyRecoveryCode($validCode);

        /* Assert */
        $this->assertTrue($result);
    }

    #[Test]
    public function it_rejects_invalid_recovery_code(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        $user->generateRecoveryCodes();
        $invalidCode = 'INVALIDCODE';

        /* Act */
        $result = $user->verifyRecoveryCode($invalidCode);

        /* Assert */
        $this->assertFalse($result);
    }

    #[Test]
    public function it_marks_recovery_code_as_used_after_verification(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        $codes = $user->generateRecoveryCodes();
        $code = $codes[0];

        /* Act */
        $user->verifyRecoveryCode($code);

        /* Assert */
        $recoveryCode = $user->recoveryCodes()->where('code', hash('sha256', $code))->first();
        $this->assertNotNull($recoveryCode, 'Recovery code should exist in database');
        $this->assertNotNull($recoveryCode->used_at, 'Recovery code should be marked as used');
    }

    #[Test]
    public function it_prevents_reuse_of_recovery_code(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        $codes = $user->generateRecoveryCodes();
        $code = $codes[0];
        $user->verifyRecoveryCode($code);

        /* Act */
        $result = $user->verifyRecoveryCode($code);

        /* Assert */
        $this->assertFalse($result, 'Used recovery code should not be accepted again');
    }

    #[Test]
    public function it_correctly_identifies_two_factor_authentication_status(): void
    {
        /* Arrange */
        $userWithout2FA = User::factory()->create();
        $userWith2FA = User::factory()->withTwoFactor()->create();

        /* Assert */
        $this->assertFalse($userWithout2FA->hasTwoFactorEnabled());
        $this->assertTrue($userWith2FA->hasTwoFactorEnabled());
    }

    #[Test]
    public function it_has_recovery_codes_relationship(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        RecoveryCode::factory()->count(3)->create(['user_id' => $user->id]);

        /* Act */
        $recoveryCodes = $user->recoveryCodes;

        /* Assert */
        $this->assertCount(3, $recoveryCodes);
        $this->assertInstanceOf(RecoveryCode::class, $recoveryCodes->first());
    }
}
