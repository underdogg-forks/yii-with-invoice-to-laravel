<?php

namespace Tests\Filament\Resources;

use App\Enums\ReportTypeEnum;
use App\Filament\Resources\ReportResource;
use App\Filament\Resources\ReportResource\Pages\ListReports;
use App\Models\Report;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(ReportResource::class)]
class ReportResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_reports(): void
    {
        /* Arrange */
        $payload = [
            'name' => '::test_report::',
            'type' => ReportTypeEnum::SALES->value,
            'description' => 'Test report description',
            'generated_by' => $this->user->id,
        ];
        $report = Report::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListReports::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$report]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_report(): void
    {
        /* Arrange */
        $payload = [
            'name' => 'New Report',
            'type' => ReportTypeEnum::PROFIT->value,
            'description' => 'New profit report',
            'generated_by' => $this->user->id,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListReports::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('reports', $payload);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_report(): void
    {
        /* Arrange */
        $report = Report::factory()->create([
            'name' => 'Old Report',
            'type' => ReportTypeEnum::SALES->value,
            'description' => 'Old description',
            'generated_by' => $this->user->id,
        ]);

        $payload = [
            'name' => 'Updated Report',
            'type' => ReportTypeEnum::TAX->value,
            'description' => 'Updated description',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListReports::class)
            ->mountAction(TestAction::make('edit')->table($report))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('reports', array_merge($payload, [
            'id' => $report->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_report(): void
    {
        /* Arrange */
        $report = Report::factory()->create([
            'generated_by' => $this->user->id,
        ]);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListReports::class)
            ->mountAction(TestAction::make('delete')->table($report))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('reports', [
            'id' => $report->id,
            'deleted_at' => null,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing name, type, and generated_by
            'description' => 'Test description',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListReports::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['name', 'type', 'generated_by']);
    }
    #endregion
}
