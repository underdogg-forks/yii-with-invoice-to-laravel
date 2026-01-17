<?php

namespace Tests\Filament\Resources;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Models\Product;
use Filament\Tables\Testing\TestsActions as TestAction;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Filament\AbstractCompanyPanelTestCase;

#[CoversClass(ProductResource::class)]
class ProductResourceTest extends AbstractCompanyPanelTestCase
{
    #region smoke
    #[Test]
    #[Group('smoke')]
    public function it_lists_products(): void
    {
        /* Arrange */
        $payload = [
            'product_name' => '::product_name::',
            'product_sku' => 'SKU-001',
            'product_price' => 99.99,
        ];
        $product = Product::factory()->create($payload);

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListProducts::class);

        /* Assert */
        $component
            ->assertSuccessful()
            ->assertCanSeeTableRecords([$product]);
    }
    #endregion

    #region crud
    #[Test]
    #[Group('crud')]
    public function it_creates_a_product(): void
    {
        /* Arrange */
        $payload = [
            'product_name' => '::product_name::',
            'product_sku' => 'SKU-NEW-001',
            'product_price' => 149.99,
            'product_description' => 'A new product description',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListProducts::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('products', [
            'product_name' => $payload['product_name'],
            'product_sku' => $payload['product_sku'],
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_updates_a_product(): void
    {
        /* Arrange */
        $product = Product::factory()->create([
            'product_name' => 'Old Product Name',
            'product_price' => 99.99,
        ]);

        $payload = [
            'product_name' => 'Updated Product Name',
            'product_price' => 199.99,
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListProducts::class)
            ->mountAction(TestAction::make('edit')->table($product))
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('products', array_merge($payload, [
            'id' => $product->id,
        ]));
    }

    #[Test]
    #[Group('crud')]
    public function it_deletes_a_product(): void
    {
        /* Arrange */
        $product = Product::factory()->create();

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListProducts::class)
            ->mountAction(TestAction::make('delete')->table($product))
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    #[Test]
    #[Group('crud')]
    public function it_validates_required_fields(): void
    {
        /* Arrange */
        $payload = [
            // Missing product_name
            'product_sku' => 'SKU-TEST',
        ];

        /* Act */
        $component = Livewire::actingAs($this->user)
            ->test(ListProducts::class)
            ->mountAction('create')
            ->fillForm($payload)
            ->callMountedAction();

        /* Assert */
        $component
            ->assertHasFormErrors(['product_name']);
    }
    #endregion
}
