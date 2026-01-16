<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\CustomFieldResource;
use App\Filament\Resources\CustomFieldResource\Pages\ListCustomFields;
use App\Models\CustomField;
use Filament\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(CustomFieldResource::class)]
class CustomFieldResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_custom_fields(): void
    {
        /* Arrange */
        $payload = [
            'table_name' => CustomField::LOCATION_CLIENT,
            'label' => '::test_field::',
            'type' => CustomField::TYPE_TEXT,
            'order' => 1,
            'required' => false,
        ];
        $customField = CustomField::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListCustomFields::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$customField]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_custom_field(): void
    {
        /* Arrange */
        $payload = [
            'table_name' => CustomField::LOCATION_INVOICE,
            'label' => 'New Custom Field',
            'type' => CustomField::TYPE_TEXTAREA,
            'order' => 5,
            'required' => true,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(CustomFieldResource\Pages\CreateCustomField::class)
            ->fillForm($payload)
            ->call('create');

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('custom_fields', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_custom_field(): void
    {
        /* Arrange */
        $customField = CustomField::factory()->create([
            'table_name' => CustomField::LOCATION_CLIENT,
            'label' => 'Old Label',
            'type' => CustomField::TYPE_TEXT,
        ]);

        $payload = [
            'label' => 'Updated Label',
            'type' => CustomField::TYPE_SELECT,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(CustomFieldResource\Pages\EditCustomField::class, [
                'record' => $customField->id,
            ])
            ->fillForm($payload)
            ->call('save');

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('custom_fields', array_merge($payload, [
            'id' => $customField->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_custom_field(): void
    {
        /* Arrange */
        $customField = CustomField::factory()->create();

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(CustomFieldResource\Pages\EditCustomField::class, [
                'record' => $customField->id,
            ])
            ->callAction(DeleteAction::class);

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('custom_fields', [
            'id' => $customField->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing table_name, label, and type
            'order' => 1,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(CustomFieldResource\Pages\CreateCustomField::class)
            ->fillForm($payload)
            ->call('create');

        /* Assert */
        $component
            ->assertHasFormErrors(['table_name', 'label', 'type']);
    }
    #endregion
}
