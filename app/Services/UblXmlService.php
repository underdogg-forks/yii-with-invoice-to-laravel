<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use DOMDocument;
use DOMElement;

class UblXmlService
{
    protected DOMDocument $dom;
    protected string $cbc = 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2';
    protected string $cac = 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2';
    protected string $invoice_ns = 'urn:oasis:names:specification:ubl:schema:xsd:Invoice-2';

    /**
     * Generate UBL 2.1 XML for an invoice (Peppol BIS 3.0 compliant)
     *
     * @param Invoice $invoice
     * @return string XML content
     */
    public function generateInvoiceXml(Invoice $invoice): string
    {
        $invoice->load([
            'client',
            'client.clientPeppol',
            'items',
            'items.product',
            'amounts',
            'numbering',
            'status'
        ]);

        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = true;

        // Root Invoice element
        $invoiceElement = $this->dom->createElementNS($this->invoice_ns, 'Invoice');
        $invoiceElement->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:cbc',
            $this->cbc
        );
        $invoiceElement->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:cac',
            $this->cac
        );
        $this->dom->appendChild($invoiceElement);

        // UBL Version
        $this->addElement($invoiceElement, 'cbc:UBLVersionID', '2.1', $this->cbc);
        $this->addElement($invoiceElement, 'cbc:CustomizationID', 'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0', $this->cbc);
        $this->addElement($invoiceElement, 'cbc:ProfileID', 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', $this->cbc);

        // Invoice identification
        $this->addElement($invoiceElement, 'cbc:ID', $invoice->invoice_number, $this->cbc);
        $this->addElement($invoiceElement, 'cbc:IssueDate', $invoice->date_invoice?->format('Y-m-d') ?? date('Y-m-d'), $this->cbc);
        
        // Invoice type code (380 = Commercial invoice)
        $this->addElement($invoiceElement, 'cbc:InvoiceTypeCode', '380', $this->cbc);
        
        // Document currency
        $this->addElement($invoiceElement, 'cbc:DocumentCurrencyCode', $invoice->currency ?? 'EUR', $this->cbc);

        // Buyer reference (if available)
        if ($invoice->client->clientPeppol?->buyer_reference) {
            $this->addElement($invoiceElement, 'cbc:BuyerReference', $invoice->client->clientPeppol->buyer_reference, $this->cbc);
        }

        // Accounting supplier party (seller)
        $this->addAccountingSupplierParty($invoiceElement);

        // Accounting customer party (buyer)
        $this->addAccountingCustomerParty($invoiceElement, $invoice);

        // Tax total
        $this->addTaxTotal($invoiceElement, $invoice);

        // Legal monetary total
        $this->addLegalMonetaryTotal($invoiceElement, $invoice);

        // Invoice lines
        $this->addInvoiceLines($invoiceElement, $invoice);

        return $this->dom->saveXML();
    }

    /**
     * Add accounting supplier party (seller)
     *
     * @param DOMElement $parent
     */
    protected function addAccountingSupplierParty(DOMElement $parent): void
    {
        $supplier = $this->dom->createElementNS($this->cac, 'cac:AccountingSupplierParty');
        $party = $this->dom->createElementNS($this->cac, 'cac:Party');

        // Endpoint ID
        $endpointId = $this->dom->createElementNS($this->cbc, 'cbc:EndpointID', config('peppol.supplier.endpoint_id', ''));
        $endpointId->setAttribute('schemeID', config('peppol.supplier.scheme_id', '0088'));
        $party->appendChild($endpointId);

        // Party name
        $partyName = $this->dom->createElementNS($this->cac, 'cac:PartyName');
        $this->addElement($partyName, 'cbc:Name', config('app.name', 'Company Name'), $this->cbc);
        $party->appendChild($partyName);

        // Postal address
        $postalAddress = $this->dom->createElementNS($this->cac, 'cac:PostalAddress');
        $this->addElement($postalAddress, 'cbc:StreetName', config('peppol.supplier.address.street', ''), $this->cbc);
        $this->addElement($postalAddress, 'cbc:CityName', config('peppol.supplier.address.city', ''), $this->cbc);
        $this->addElement($postalAddress, 'cbc:PostalZone', config('peppol.supplier.address.postal_code', ''), $this->cbc);
        
        $country = $this->dom->createElementNS($this->cac, 'cac:Country');
        $this->addElement($country, 'cbc:IdentificationCode', config('peppol.supplier.address.country_code', 'NL'), $this->cbc);
        $postalAddress->appendChild($country);
        $party->appendChild($postalAddress);

        // Party tax scheme
        $partyTaxScheme = $this->dom->createElementNS($this->cac, 'cac:PartyTaxScheme');
        $this->addElement($partyTaxScheme, 'cbc:CompanyID', config('peppol.supplier.vat_number', ''), $this->cbc);
        
        $taxScheme = $this->dom->createElementNS($this->cac, 'cac:TaxScheme');
        $this->addElement($taxScheme, 'cbc:ID', 'VAT', $this->cbc);
        $partyTaxScheme->appendChild($taxScheme);
        $party->appendChild($partyTaxScheme);

        // Party legal entity
        $partyLegalEntity = $this->dom->createElementNS($this->cac, 'cac:PartyLegalEntity');
        $this->addElement($partyLegalEntity, 'cbc:RegistrationName', config('app.name', 'Company Name'), $this->cbc);
        $party->appendChild($partyLegalEntity);

        $supplier->appendChild($party);
        $parent->appendChild($supplier);
    }

    /**
     * Add accounting customer party (buyer)
     *
     * @param DOMElement $parent
     * @param Invoice $invoice
     */
    protected function addAccountingCustomerParty(DOMElement $parent, Invoice $invoice): void
    {
        $customer = $this->dom->createElementNS($this->cac, 'cac:AccountingCustomerParty');
        $party = $this->dom->createElementNS($this->cac, 'cac:Party');

        $clientPeppol = $invoice->client->clientPeppol;

        // Endpoint ID
        if ($clientPeppol && $clientPeppol->peppol_client_electronic_address) {
            $endpointId = $this->dom->createElementNS($this->cbc, 'cbc:EndpointID', $clientPeppol->peppol_client_electronic_address);
            $endpointId->setAttribute('schemeID', $clientPeppol->peppol_client_electronic_address_scheme ?? '0088');
            $party->appendChild($endpointId);
        }

        // Party name
        $partyName = $this->dom->createElementNS($this->cac, 'cac:PartyName');
        $this->addElement($partyName, 'cbc:Name', $invoice->client->client_name, $this->cbc);
        $party->appendChild($partyName);

        // Postal address
        $postalAddress = $this->dom->createElementNS($this->cac, 'cac:PostalAddress');
        $this->addElement($postalAddress, 'cbc:StreetName', $invoice->client->client_address_1 ?? '', $this->cbc);
        $this->addElement($postalAddress, 'cbc:CityName', $invoice->client->client_city ?? '', $this->cbc);
        $this->addElement($postalAddress, 'cbc:PostalZone', $invoice->client->client_postal_code ?? '', $this->cbc);
        
        $country = $this->dom->createElementNS($this->cac, 'cac:Country');
        $this->addElement($country, 'cbc:IdentificationCode', $invoice->client->client_country ?? 'NL', $this->cbc);
        $postalAddress->appendChild($country);
        $party->appendChild($postalAddress);

        // Party legal entity
        $partyLegalEntity = $this->dom->createElementNS($this->cac, 'cac:PartyLegalEntity');
        $this->addElement($partyLegalEntity, 'cbc:RegistrationName', $invoice->client->client_name, $this->cbc);
        $party->appendChild($partyLegalEntity);

        $customer->appendChild($party);
        $parent->appendChild($customer);
    }

    /**
     * Add tax total
     *
     * @param DOMElement $parent
     * @param Invoice $invoice
     */
    protected function addTaxTotal(DOMElement $parent, Invoice $invoice): void
    {
        $taxTotal = $this->dom->createElementNS($this->cac, 'cac:TaxTotal');
        
        $taxAmount = $this->dom->createElementNS($this->cbc, 'cbc:TaxAmount', number_format($invoice->total_tax_amount ?? 0, 2, '.', ''));
        $taxAmount->setAttribute('currencyID', $invoice->currency ?? 'EUR');
        $taxTotal->appendChild($taxAmount);

        // Tax subtotal
        $taxSubtotal = $this->dom->createElementNS($this->cac, 'cac:TaxSubtotal');
        
        $taxableAmount = $this->dom->createElementNS($this->cbc, 'cbc:TaxableAmount', number_format($invoice->subtotal ?? 0, 2, '.', ''));
        $taxableAmount->setAttribute('currencyID', $invoice->currency ?? 'EUR');
        $taxSubtotal->appendChild($taxableAmount);
        
        $taxAmountSub = $this->dom->createElementNS($this->cbc, 'cbc:TaxAmount', number_format($invoice->total_tax_amount ?? 0, 2, '.', ''));
        $taxAmountSub->setAttribute('currencyID', $invoice->currency ?? 'EUR');
        $taxSubtotal->appendChild($taxAmountSub);

        $taxCategory = $this->dom->createElementNS($this->cac, 'cac:TaxCategory');
        $this->addElement($taxCategory, 'cbc:ID', 'S', $this->cbc); // S = Standard rate
        $this->addElement($taxCategory, 'cbc:Percent', '21.00', $this->cbc); // Default VAT rate
        
        $taxScheme = $this->dom->createElementNS($this->cac, 'cac:TaxScheme');
        $this->addElement($taxScheme, 'cbc:ID', 'VAT', $this->cbc);
        $taxCategory->appendChild($taxScheme);
        
        $taxSubtotal->appendChild($taxCategory);
        $taxTotal->appendChild($taxSubtotal);

        $parent->appendChild($taxTotal);
    }

    /**
     * Add legal monetary total
     *
     * @param DOMElement $parent
     * @param Invoice $invoice
     */
    protected function addLegalMonetaryTotal(DOMElement $parent, Invoice $invoice): void
    {
        $monetaryTotal = $this->dom->createElementNS($this->cac, 'cac:LegalMonetaryTotal');

        $lineExtensionAmount = $this->dom->createElementNS($this->cbc, 'cbc:LineExtensionAmount', number_format($invoice->subtotal ?? 0, 2, '.', ''));
        $lineExtensionAmount->setAttribute('currencyID', $invoice->currency ?? 'EUR');
        $monetaryTotal->appendChild($lineExtensionAmount);

        $taxExclusiveAmount = $this->dom->createElementNS($this->cbc, 'cbc:TaxExclusiveAmount', number_format($invoice->subtotal ?? 0, 2, '.', ''));
        $taxExclusiveAmount->setAttribute('currencyID', $invoice->currency ?? 'EUR');
        $monetaryTotal->appendChild($taxExclusiveAmount);

        $taxInclusiveAmount = $this->dom->createElementNS($this->cbc, 'cbc:TaxInclusiveAmount', number_format($invoice->total_amount ?? 0, 2, '.', ''));
        $taxInclusiveAmount->setAttribute('currencyID', $invoice->currency ?? 'EUR');
        $monetaryTotal->appendChild($taxInclusiveAmount);

        $payableAmount = $this->dom->createElementNS($this->cbc, 'cbc:PayableAmount', number_format($invoice->balance ?? $invoice->total_amount ?? 0, 2, '.', ''));
        $payableAmount->setAttribute('currencyID', $invoice->currency ?? 'EUR');
        $monetaryTotal->appendChild($payableAmount);

        $parent->appendChild($monetaryTotal);
    }

    /**
     * Add invoice lines
     *
     * @param DOMElement $parent
     * @param Invoice $invoice
     */
    protected function addInvoiceLines(DOMElement $parent, Invoice $invoice): void
    {
        foreach ($invoice->items as $index => $item) {
            $this->addInvoiceLine($parent, $item, $index + 1);
        }
    }

    /**
     * Add single invoice line
     *
     * @param DOMElement $parent
     * @param InvoiceItem $item
     * @param int $lineNumber
     */
    protected function addInvoiceLine(DOMElement $parent, InvoiceItem $item, int $lineNumber): void
    {
        $invoiceLine = $this->dom->createElementNS($this->cac, 'cac:InvoiceLine');

        $this->addElement($invoiceLine, 'cbc:ID', (string)$lineNumber, $this->cbc);

        $quantity = $this->dom->createElementNS($this->cbc, 'cbc:InvoicedQuantity', (string)($item->quantity ?? 1));
        $quantity->setAttribute('unitCode', $item->product?->unit_code ?? 'C62'); // C62 = piece
        $invoiceLine->appendChild($quantity);

        $lineExtensionAmount = $this->dom->createElementNS($this->cbc, 'cbc:LineExtensionAmount', number_format($item->item_subtotal ?? 0, 2, '.', ''));
        $lineExtensionAmount->setAttribute('currencyID', $item->invoice->currency ?? 'EUR');
        $invoiceLine->appendChild($lineExtensionAmount);

        // Item
        $itemElement = $this->dom->createElementNS($this->cac, 'cac:Item');
        $this->addElement($itemElement, 'cbc:Name', $item->item_name, $this->cbc);
        
        if ($item->item_description) {
            $this->addElement($itemElement, 'cbc:Description', $item->item_description, $this->cbc);
        }

        // Tax category
        $classifiedTaxCategory = $this->dom->createElementNS($this->cac, 'cac:ClassifiedTaxCategory');
        $this->addElement($classifiedTaxCategory, 'cbc:ID', 'S', $this->cbc);
        $this->addElement($classifiedTaxCategory, 'cbc:Percent', '21.00', $this->cbc);
        
        $taxScheme = $this->dom->createElementNS($this->cac, 'cac:TaxScheme');
        $this->addElement($taxScheme, 'cbc:ID', 'VAT', $this->cbc);
        $classifiedTaxCategory->appendChild($taxScheme);
        $itemElement->appendChild($classifiedTaxCategory);

        $invoiceLine->appendChild($itemElement);

        // Price
        $price = $this->dom->createElementNS($this->cac, 'cac:Price');
        $priceAmount = $this->dom->createElementNS($this->cbc, 'cbc:PriceAmount', number_format($item->item_price ?? 0, 2, '.', ''));
        $priceAmount->setAttribute('currencyID', $item->invoice->currency ?? 'EUR');
        $price->appendChild($priceAmount);
        $invoiceLine->appendChild($price);

        $parent->appendChild($invoiceLine);
    }

    /**
     * Helper method to add element with namespace
     *
     * @param DOMElement $parent
     * @param string $name
     * @param string $value
     * @param string $namespace
     */
    protected function addElement(DOMElement $parent, string $name, string $value, string $namespace): void
    {
        $element = $this->dom->createElementNS($namespace, $name, htmlspecialchars($value, ENT_XML1, 'UTF-8'));
        $parent->appendChild($element);
    }

    /**
     * Validate UBL XML against schema
     *
     * @param string $xml
     * @return bool
     */
    public function validateXml(string $xml): bool
    {
        $dom = new DOMDocument();
        $dom->loadXML($xml);

        // Basic validation - check for required elements
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('cbc', $this->cbc);
        $xpath->registerNamespace('cac', $this->cac);

        $requiredElements = [
            '//cbc:ID',
            '//cbc:IssueDate',
            '//cac:AccountingSupplierParty',
            '//cac:AccountingCustomerParty',
            '//cac:LegalMonetaryTotal',
        ];

        foreach ($requiredElements as $element) {
            if ($xpath->query($element)->length === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Save XML to storage
     *
     * @param string $content
     * @param string $filename
     * @param string $disk
     * @return string Path to saved file
     */
    public function saveXml(string $content, string $filename, string $disk = 'local'): string
    {
        $path = "ubl/{$filename}";
        \Storage::disk($disk)->put($path, $content);
        return $path;
    }
}
