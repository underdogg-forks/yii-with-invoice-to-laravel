<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(UserResource::class)]
class UserResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_users(): void
    {
        /* Arrange */
        $payload = [
            'login' => '::test_user::',
            'email' => 'testuser@example.com',
            'password' => 'password123',
        ];
        $user = User::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListUsers::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$user]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_user(): void
    {
        /* Arrange */
        $payload = [
            'login' => 'newuser',
            'email' => 'newuser@example.com',
            'password' => 'SecurePassword123',
            'password_confirmation' => 'SecurePassword123',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(UserResource\Pages\CreateUser::class)
            ->fillForm($payload)
            ->call('create');

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', [
            'login' => 'newuser',
            'email' => 'newuser@example.com',
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_user(): void
    {
        /* Arrange */
        $user = User::factory()->create([
            'login' => 'olduser',
            'email' => 'olduser@example.com',
        ]);

        $payload = [
            'login' => 'updateduser',
            'email' => 'updateduser@example.com',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(UserResource\Pages\EditUser::class, [
                'record' => $user->id,
            ])
            ->fillForm($payload)
            ->call('save');

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('users', array_merge($payload, [
            'id' => $user->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_user(): void
    {
        /* Arrange */
        $user = User::factory()->create();

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(UserResource\Pages\EditUser::class, [
                'record' => $user->id,
            ])
            ->callAction(DeleteAction::class);

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing login and email
            'password' => 'password123',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(UserResource\Pages\CreateUser::class)
            ->fillForm($payload)
            ->call('create');

        /* Assert */
        $component
            ->assertHasFormErrors(['login', 'email']);
    }
    #endregion
}
