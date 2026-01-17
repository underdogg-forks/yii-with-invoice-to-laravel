<?php

namespace Tests\Filament;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AbstractCompanyPanelTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Tenant $company;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test company/tenant
        $this->company = Tenant::factory()->create([
            'name' => 'Test Company',
            'subdomain' => 'test001',
            'is_active' => true,
        ]);

        // Create a test user
        $this->user = User::factory()->create([
            'login' => 'testuser',
            'email' => 'test@example.com',
        ]);
    }
}
