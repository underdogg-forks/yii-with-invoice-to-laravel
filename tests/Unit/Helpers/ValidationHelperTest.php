<?php

namespace Tests\Unit\Helpers;

use App\Services\Helpers\ValidationHelper;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ValidationHelperTest extends TestCase
{
    protected ValidationHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new ValidationHelper();
    }

    #[Test]
    public function it_validates_german_vat_numbers_correctly(): void
    {
        /* Arrange */
        $validVat = 'DE123456789';
        $invalidShortVat = 'DE12345';
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validateVatNumber($validVat, 'DE'));
        $this->assertFalse($this->helper->validateVatNumber($invalidShortVat, 'DE'));
    }

    #[Test]
    public function it_validates_austrian_vat_numbers_correctly(): void
    {
        /* Arrange */
        $validVat = 'ATU12345678';
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validateVatNumber($validVat, 'AT'));
    }

    #[Test]
    public function it_validates_dutch_vat_numbers_correctly(): void
    {
        /* Arrange */
        $validVat = 'NL123456789B12';
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validateVatNumber($validVat, 'NL'));
    }

    #[Test]
    public function it_rejects_invalid_vat_number_formats(): void
    {
        /* Arrange */
        $invalidVat = 'INVALID';
        
        /* Act & Assert */
        $this->assertFalse($this->helper->validateVatNumber($invalidVat, 'DE'));
    }

    #[Test]
    public function it_validates_german_iban_correctly(): void
    {
        /* Arrange */
        $validIban = 'DE89370400440532013000';
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validateIban($validIban));
    }

    #[Test]
    public function it_validates_uk_iban_correctly(): void
    {
        /* Arrange */
        $validIban = 'GB82WEST12345698765432';
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validateIban($validIban));
    }

    #[Test]
    public function it_rejects_invalid_iban_formats(): void
    {
        /* Arrange */
        $invalidIban = 'INVALID';
        $tooShortIban = 'DE12345';
        
        /* Act & Assert */
        $this->assertFalse($this->helper->validateIban($invalidIban));
        $this->assertFalse($this->helper->validateIban($tooShortIban));
    }

    #[Test]
    public function it_validates_international_phone_number_formats(): void
    {
        /* Arrange */
        $dutchPhoneWithPlus = '+31612345678';
        $dutchPhoneWithZeros = '0031612345678';
        $genericPhone = '1234567890';
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validatePhoneNumber($dutchPhoneWithPlus));
        $this->assertTrue($this->helper->validatePhoneNumber($dutchPhoneWithZeros));
        $this->assertTrue($this->helper->validatePhoneNumber($genericPhone));
    }

    #[Test]
    public function it_rejects_invalid_phone_numbers(): void
    {
        /* Arrange */
        $tooShort = '123';
        $nonNumeric = 'abc';
        
        /* Act & Assert */
        $this->assertFalse($this->helper->validatePhoneNumber($tooShort));
        $this->assertFalse($this->helper->validatePhoneNumber($nonNumeric));
    }

    #[Test]
    public function it_detects_common_disposable_email_providers(): void
    {
        /* Arrange */
        $tempmail = 'test@tempmail.com';
        $mailinator = 'user@mailinator.com';
        
        /* Act & Assert */
        $this->assertTrue($this->helper->isDisposableEmail($tempmail));
        $this->assertTrue($this->helper->isDisposableEmail($mailinator));
    }

    #[Test]
    public function it_accepts_legitimate_email_providers(): void
    {
        /* Arrange */
        $gmail = 'user@gmail.com';
        $businessEmail = 'business@company.com';
        
        /* Act & Assert */
        $this->assertFalse($this->helper->isDisposableEmail($gmail));
        $this->assertFalse($this->helper->isDisposableEmail($businessEmail));
    }

    #[Test]
    public function it_validates_positive_number_business_rule(): void
    {
        /* Arrange */
        $positive = 10;
        $zero = 0;
        $negative = -5;
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validateBusinessRule('positive', $positive));
        $this->assertFalse($this->helper->validateBusinessRule('positive', $zero));
        $this->assertFalse($this->helper->validateBusinessRule('positive', $negative));
    }

    #[Test]
    public function it_validates_non_negative_number_business_rule(): void
    {
        /* Arrange */
        $zero = 0;
        $positive = 10;
        $negative = -1;
        
        /* Act & Assert */
        $this->assertTrue($this->helper->validateBusinessRule('non_negative', $zero));
        $this->assertTrue($this->helper->validateBusinessRule('non_negative', $positive));
        $this->assertFalse($this->helper->validateBusinessRule('non_negative', $negative));
    }
}
