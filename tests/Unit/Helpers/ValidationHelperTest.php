<?php

namespace Tests\Unit\Helpers;

use App\Services\Helpers\ValidationHelper;
use Tests\TestCase;

class ValidationHelperTest extends TestCase
{
    protected ValidationHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new ValidationHelper();
    }

    public function it_validates_vat_numbers(): void
    {
        // Valid VAT numbers
        $this->assertTrue($this->helper->validateVatNumber('DE123456789', 'DE'));
        $this->assertTrue($this->helper->validateVatNumber('ATU12345678', 'AT'));
        $this->assertTrue($this->helper->validateVatNumber('NL123456789B12', 'NL'));

        // Invalid VAT numbers
        $this->assertFalse($this->helper->validateVatNumber('DE12345', 'DE')); // Too short
        $this->assertFalse($this->helper->validateVatNumber('INVALID', 'DE'));
    }

    public function it_validates_iban(): void
    {
        // Valid IBAN (examples)
        $this->assertTrue($this->helper->validateIban('DE89370400440532013000'));
        $this->assertTrue($this->helper->validateIban('GB82WEST12345698765432'));

        // Invalid IBAN
        $this->assertFalse($this->helper->validateIban('INVALID'));
        $this->assertFalse($this->helper->validateIban('DE12345')); // Too short
    }

    public function it_validates_phone_numbers(): void
    {
        // Valid phone numbers
        $this->assertTrue($this->helper->validatePhoneNumber('+31612345678'));
        $this->assertTrue($this->helper->validatePhoneNumber('0031612345678'));
        $this->assertTrue($this->helper->validatePhoneNumber('1234567890'));

        // Invalid phone numbers
        $this->assertFalse($this->helper->validatePhoneNumber('123')); // Too short
        $this->assertFalse($this->helper->validatePhoneNumber('abc'));
    }

    public function it_detects_disposable_emails(): void
    {
        // Disposable emails
        $this->assertTrue($this->helper->isDisposableEmail('test@tempmail.com'));
        $this->assertTrue($this->helper->isDisposableEmail('user@mailinator.com'));

        // Normal emails
        $this->assertFalse($this->helper->isDisposableEmail('user@gmail.com'));
        $this->assertFalse($this->helper->isDisposableEmail('business@company.com'));
    }

    public function it_validates_business_rules(): void
    {
        // Positive rule
        $this->assertTrue($this->helper->validateBusinessRule('positive', 10));
        $this->assertFalse($this->helper->validateBusinessRule('positive', 0));
        $this->assertFalse($this->helper->validateBusinessRule('positive', -5));

        // Non-negative rule
        $this->assertTrue($this->helper->validateBusinessRule('non_negative', 0));
        $this->assertTrue($this->helper->validateBusinessRule('non_negative', 10));
        $this->assertFalse($this->helper->validateBusinessRule('non_negative', -1));
    }
}
