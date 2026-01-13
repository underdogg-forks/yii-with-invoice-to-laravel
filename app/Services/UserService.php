<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\DTOs\UserDTO;
use App\Models\User;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepository $repository,
        private Google2FA $google2fa
    ) {}

    public function getById(int $id): ?User
    {
        return $this->repository->find($id);
    }

    public function getByEmail(string $email): ?User
    {
        return $this->repository->findByEmail($email);
    }

    public function getAll()
    {
        return $this->repository->all();
    }

    public function create(UserDTO $dto): User
    {
        $data = $dto->toArray();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $this->repository->create($data);
    }

    public function update(int $id, UserDTO $dto): bool
    {
        $user = $this->repository->find($id);
        
        if (!$user) {
            return false;
        }

        $data = $dto->toArray();
        
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        return $this->repository->update($user, $data);
    }

    public function delete(int $id): bool
    {
        $user = $this->repository->find($id);
        
        if (!$user) {
            return false;
        }

        return $this->repository->delete($user);
    }

    /**
     * Enable 2FA for a user and return secret + QR code
     */
    public function enable2FA(User $user): array
    {
        $secret = $this->google2fa->generateSecretKey();
        
        $user->update([
            'totp_secret' => $secret,
            'tfa_enabled' => true,
        ]);

        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        $recoveryCodes = $user->generateRecoveryCodes();

        return [
            'secret' => $secret,
            'qr_code_url' => $qrCodeUrl,
            'recovery_codes' => $recoveryCodes,
        ];
    }

    /**
     * Disable 2FA for a user
     */
    public function disable2FA(User $user): void
    {
        $user->update([
            'totp_secret' => null,
            'tfa_enabled' => false,
        ]);

        $user->recoveryCodes()->delete();
    }

    /**
     * Verify 2FA code
     */
    public function verify2FACode(User $user, string $code): bool
    {
        if (!$user->hasTwoFactorEnabled()) {
            return false;
        }

        return $this->google2fa->verifyKey($user->totp_secret, $code);
    }
}
