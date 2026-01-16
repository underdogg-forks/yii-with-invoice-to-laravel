<?php

namespace Tests\Unit\Services;

use App\Models\Invoice;
use App\Services\UblXmlService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(UblXmlService::class)]
class UblXmlServiceTest extends TestCase
{
    use RefreshDatabase;

    private UblXmlService $ublXmlService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->ublXmlService = app(UblXmlService::class);
    }

    #[Test]
    public function it_generates_ubl_xml_for_invoice(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        /* Act */
        $xml = $this->ublXmlService->generateInvoiceXml($invoice);

        /* Assert */
        $this->assertNotEmpty($xml);
        $this->assertStringContainsString('<?xml', $xml);
        $this->assertStringContainsString('Invoice', $xml);
    }

    #[Test]
    public function it_includes_required_ubl_elements(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        /* Act */
        $xml = $this->ublXmlService->generateInvoiceXml($invoice);

        /* Assert */
        $this->assertStringContainsString('ID', $xml);
        $this->assertStringContainsString('IssueDate', $xml);
        $this->assertStringContainsString('AccountingSupplierParty', $xml);
        $this->assertStringContainsString('AccountingCustomerParty', $xml);
        $this->assertStringContainsString('InvoiceLine', $xml);
        $this->assertStringContainsString('TaxTotal', $xml);
        $this->assertStringContainsString('LegalMonetaryTotal', $xml);
    }

    #[Test]
    public function it_validates_generated_xml(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        /* Act */
        $xml = $this->ublXmlService->generateInvoiceXml($invoice);
        $isValid = $this->ublXmlService->validateXml($xml);

        /* Assert */
        $this->assertTrue($isValid);
    }

    #[Test]
    public function it_saves_xml_to_storage(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        /* Act */
        $path = $this->ublXmlService->saveInvoiceXml($invoice);

        /* Assert */
        $this->assertNotEmpty($path);
        $this->assertFileExists(storage_path('app/' . $path));
    }

    #[Test]
    public function it_includes_peppol_namespaces(): void
    {
        /* Arrange */
        $invoice = Invoice::factory()->create();

        /* Act */
        $xml = $this->ublXmlService->generateInvoiceXml($invoice);

        /* Assert */
        $this->assertStringContainsString('urn:oasis:names:specification:ubl:schema:xsd:Invoice-2', $xml);
        $this->assertStringContainsString('urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2', $xml);
        $this->assertStringContainsString('urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2', $xml);
    }

    #[Test]
    public function it_handles_missing_invoice(): void
    {
        /* Arrange */
        $this->expectException(\Exception::class);

        /* Act */
        $this->ublXmlService->generateInvoiceXml(null);
    }
}
