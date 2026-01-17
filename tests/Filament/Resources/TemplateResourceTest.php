<?php

namespace Tests\Filament\Resources;

use App\Enums\TemplateCategoryEnum;
use App\Filament\Resources\TemplateResource;
use App\Filament\Resources\TemplateResource\Pages\ListTemplates;
use App\Models\Template;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(TemplateResource::class)]
class TemplateResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_templates(): void
    {
        /* Arrange */
        $payload = [
            'name' => '::test_template::',
            'subject' => 'Test Subject',
            'content' => 'Test content here',
            'category' => TemplateCategoryEnum::CUSTOM->value,
            'is_default' => false,
            'is_active' => true,
        ];
        $template = Template::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTemplates::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$template]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_template(): void
    {
        /* Arrange */
        $payload = [
            'name' => 'New Template',
            'subject' => 'New Email Subject',
            'content' => '<p>This is new template content</p>',
            'category' => TemplateCategoryEnum::STANDARD->value,
            'is_default' => true,
            'is_active' => true,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTemplates::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('templates', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_template(): void
    {
        /* Arrange */
        $template = Template::factory()->create([
            'name' => 'Old Template',
            'subject' => 'Old Subject',
            'content' => '<p>Old content</p>',
        ]);

        $payload = [
            'name' => 'Updated Template',
            'subject' => 'Updated Subject',
            'content' => '<p>Updated content</p>',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTemplates::class)
            ->mountAction(TestAction::make('edit')->table($template))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('templates', array_merge($payload, [
            'id' => $template->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_template(): void
    {
        /* Arrange */
        $template = Template::factory()->create([
            'is_default' => false,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTemplates::class)
            ->mountAction(TestAction::make('delete')->table($template))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('templates', [
            'id' => $template->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing name, content, and category
            'subject' => 'Test Subject',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListTemplates::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['name', 'content', 'category']);
    }
    #endregion
}
