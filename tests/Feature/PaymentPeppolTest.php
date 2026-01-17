<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\PaymentPeppol;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PaymentPeppol::class)]
class PaymentPeppolTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_payment_peppol(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();
        
        $payload = [
            'inv_id' => $invoice->id,
            'auto_reference' => 123456,
            'provider' => 'StoreCove',
        ];

        /* Act */
        $paymentPeppol = PaymentPeppol::factory()->create($payload);

        /* Assert */
        $this->assertDatabaseHas('payment_peppol', $payload);
        $this->assertNotNull($paymentPeppol->id);
    }

    #[Test]
    public function it_has_invoice_relationship(): void
    {
        /* Arrange */
        $paymentPeppol = PaymentPeppol::factory()->create();

        /* Act */
        $invoice = $paymentPeppol->invoice;

        /* Assert */
        $this->assertInstanceOf(Invoice::class, $invoice);
    }

    #[Test]
    public function it_updates_payment_peppol(): void
    {
        /* Arrange */
        $paymentPeppol = PaymentPeppol::factory()->create([
            'provider' => 'StoreCove',
        ]);

        $payload = ['provider' => 'Ecosio'];

        /* Act */
        $paymentPeppol->update($payload);

        /* Assert */
        $this->assertDatabaseHas('payment_peppol', [
            'id' => $paymentPeppol->id,
            'provider' => 'Ecosio',
        ]);
    }

    #[Test]
    public function it_deletes_payment_peppol(): void
    {
        /* Arrange */
        $paymentPeppol = PaymentPeppol::factory()->create();
        $id = $paymentPeppol->id;

        /* Act */
        $paymentPeppol->delete();

        /* Assert */
        $this->assertDatabaseMissing('payment_peppol', [
            'id' => $id,
        ]);
    }
}
