<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage-users',
            'manage-clients',
            'manage-invoices',
            'manage-peppol',
            'manage-products',
            'manage-quotes',
            'manage-settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions to roles
        $superAdmin->syncPermissions($permissions);
        $admin->syncPermissions(['manage-clients', 'manage-invoices', 'manage-peppol', 'manage-products', 'manage-quotes']);
        $accountant->syncPermissions(['manage-invoices', 'manage-clients', 'manage-peppol']);
        $user->syncPermissions([]);

        // Create admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'login' => 'admin',
                'password' => 'password', // Will be hashed by User model
                'tfa_enabled' => false,
            ]
        );
        $adminUser->assignRole('super-admin');

        // Create test users
        $testUser = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'login' => 'testuser',
                'password' => 'password',
                'tfa_enabled' => false,
            ]
        );
        $testUser->assignRole('user');

        // Create additional random users
        User::factory()->count(5)->create()->each(function ($randomUser) {
            $randomUser->assignRole('user');
        });
    }
}
