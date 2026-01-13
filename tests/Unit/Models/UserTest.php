<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\RecoveryCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function it_hashes_password_on_creation(): void
    {
        // Arrange
        $password = 'secret-password';

        // Act
        $user = User::factory()->create([
            'password' => $password,
        ]);

        // Assert
        $this->assertNotEquals($password, $user->password);
        $this->assertTrue(password_verify($password, $user->password));
    }

    public function it_generates_recovery_codes(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $codes = $user->generateRecoveryCodes(8);

        // Assert
        $this->assertCount(8, $codes);
        $this->assertCount(8, $user->recoveryCodes);
        foreach ($codes as $code) {
            $this->assertEquals(10, strlen($code));
        }
    }

    public function it_verifies_valid_recovery_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $codes = $user->generateRecoveryCodes();
        $validCode = $codes[0];

        // Act
        $result = $user->verifyRecoveryCode($validCode);

        // Assert
        $this->assertTrue($result);
    }

    public function it_rejects_invalid_recovery_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $user->generateRecoveryCodes();

        // Act
        $result = $user->verifyRecoveryCode('INVALIDCODE');

        // Assert
        $this->assertFalse($result);
    }

    public function it_marks_recovery_code_as_used(): void
    {
        // Arrange
        $user = User::factory()->create();
        $codes = $user->generateRecoveryCodes();
        $code = $codes[0];

        // Act
        $user->verifyRecoveryCode($code);

        // Assert
        $recoveryCode = $user->recoveryCodes()->where('code', hash('sha256', $code))->first();
        $this->assertNotNull($recoveryCode->used_at);
    }

    public function it_cannot_reuse_recovery_code(): void
    {
        // Arrange
        $user = User::factory()->create();
        $codes = $user->generateRecoveryCodes();
        $code = $codes[0];
        $user->verifyRecoveryCode($code);

        // Act
        $result = $user->verifyRecoveryCode($code);

        // Assert
        $this->assertFalse($result);
    }

    public function it_checks_if_two_factor_is_enabled(): void
    {
        // Arrange & Act
        $userWithout2FA = User::factory()->create();
        $userWith2FA = User::factory()->withTwoFactor()->create();

        // Assert
        $this->assertFalse($userWithout2FA->hasTwoFactorEnabled());
        $this->assertTrue($userWith2FA->hasTwoFactorEnabled());
    }

    public function it_has_recovery_codes_relationship(): void
    {
        // Arrange
        $user = User::factory()->create();
        RecoveryCode::factory()->count(3)->create(['user_id' => $user->id]);

        // Act
        $recoveryCodes = $user->recoveryCodes;

        // Assert
        $this->assertCount(3, $recoveryCodes);
        $this->assertInstanceOf(RecoveryCode::class, $recoveryCodes->first());
    }
}
