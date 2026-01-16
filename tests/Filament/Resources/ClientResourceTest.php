<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\ClientResource;
use App\Filament\Resources\ClientResource\Pages\ListClients;
use App\Models\Client;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(ClientResource::class)]
class ClientResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_clients(): void
    {
        /* Arrange */
        $payload = [
            'name' => '::client_name::',
            'email' => 'test@example.com',
            'active' => true,
        ];
        $client = Client::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClients::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$client]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_client(): void
    {
        /* Arrange */
        $payload = [
            'name' => '::client_name::',
            'surname' => '::client_surname::',
            'email' => 'newclient@example.com',
            'phone' => '+1234567890',
            'active' => true,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClients::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', [
            'name' => $payload['name'],
            'email' => $payload['email'],
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_client(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $payload = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClients::class)
            ->mountAction(TestAction::make('edit')->table($client))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('clients', array_merge($payload, [
            'id' => $client->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_client(): void
    {
        /* Arrange */
        $client = Client::factory()->create();

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClients::class)
            ->mountAction(TestAction::make('delete')->table($client))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertSoftDeleted('clients', [
            'id' => $client->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing name
            'email' => 'test@example.com',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClients::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['name']);
    }
    #endregion
}
