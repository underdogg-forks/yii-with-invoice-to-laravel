<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\ClientPeppolResource;
use App\Filament\Resources\ClientPeppolResource\Pages\ListClientPeppols;
use App\Models\Client;
use App\Models\ClientPeppol;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(ClientPeppolResource::class)]
class ClientPeppolResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_client_peppols(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'endpointid' => 'test@example.com',
            'endpointid_schemeid' => '0088',
            'buyer_reference' => 'REF-001',
        ];
        $clientPeppol = ClientPeppol::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClientPeppols::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$clientPeppol]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_client_peppol(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => '::client_name::',
        ]);

        $payload = [
            'client_id' => $client->id,
            'endpointid' => 'newtest@example.com',
            'endpointid_schemeid' => '0088',
            'buyer_reference' => 'NEW-REF',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClientPeppols::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('client_peppol', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_client_peppol(): void
    {
        /* Arrange */
        $client = Client::factory()->create([
            'name' => '::client_name::',
        ]);

        $clientPeppol = ClientPeppol::factory()->create([
            'client_id' => $client->id,
            'endpointid' => 'old@example.com',
            'buyer_reference' => 'OLD-REF',
        ]);

        $payload = [
            'endpointid' => 'updated@example.com',
            'buyer_reference' => 'UPDATED-REF',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClientPeppols::class)
            ->mountAction(TestAction::make('edit')->table($clientPeppol))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('client_peppol', array_merge($payload, [
            'id' => $clientPeppol->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_client_peppol(): void
    {
        /* Arrange */
        $client = Client::factory()->create();
        $clientPeppol = ClientPeppol::factory()->create([
            'client_id' => $client->id,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClientPeppols::class)
            ->mountAction(TestAction::make('delete')->table($clientPeppol))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('client_peppol', [
            'id' => $clientPeppol->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing client_id
            'endpointid' => 'test@example.com',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListClientPeppols::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['client_id']);
    }
    #endregion
}
