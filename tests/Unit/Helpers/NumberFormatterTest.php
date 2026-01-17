<?php

namespace Tests\Unit\Helpers;

use App\Services\Helpers\NumberFormatter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NumberFormatterTest extends TestCase
{
    protected NumberFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = new NumberFormatter();
    }

    #[Test]
    public function it_formats_numbers_with_correct_decimal_places(): void
    {
        /* Arrange */
        $number = 1234.567;
        $decimals = 2;

        /* Act */
        $result = $this->formatter->formatNumber($number, $decimals);

        /* Assert */
        $this->assertIsString($result);
        // The result should be "1,234.57" or "1.234,57" depending on locale
        $this->assertMatchesRegularExpression('/1[.,]234[.,]57/', $result);
    }

    #[Test]
    public function it_formats_percentages_with_decimal_precision(): void
    {
        /* Arrange */
        $value = 25.5;
        $decimals = 1;

        /* Act */
        $result = $this->formatter->formatPercentage($value, $decimals);

        /* Assert */
        $this->assertEquals('25.5%', $result);
    }

    #[Test]
    public function it_formats_percentages_with_zero_decimals(): void
    {
        /* Arrange */
        $value = 50.0;
        $decimals = 0;

        /* Act */
        $result = $this->formatter->formatPercentage($value, $decimals);

        /* Assert */
        $this->assertEquals('50%', $result);
    }

    #[Test]
    public function it_formats_file_sizes_in_kilobytes(): void
    {
        /* Arrange */
        $bytes = 1024; // 1 KB

        /* Act */
        $result = $this->formatter->formatFileSize($bytes);

        /* Assert */
        $this->assertEquals('1.00 KB', $result);
    }

    #[Test]
    public function it_formats_file_sizes_in_megabytes(): void
    {
        /* Arrange */
        $bytes = 1024 * 1024; // 1 MB

        /* Act */
        $result = $this->formatter->formatFileSize($bytes);

        /* Assert */
        $this->assertEquals('1.00 MB', $result);
    }

    #[Test]
    public function it_formats_file_sizes_in_gigabytes(): void
    {
        /* Arrange */
        $bytes = 1024 * 1024 * 1024; // 1 GB

        /* Act */
        $result = $this->formatter->formatFileSize($bytes);

        /* Assert */
        $this->assertEquals('1.00 GB', $result);
    }

    #[Test]
    public function it_formats_ordinal_numbers_correctly(): void
    {
        /* Assert - Testing multiple cases */
        $this->assertEquals('1st', $this->formatter->formatOrdinal(1));
        $this->assertEquals('2nd', $this->formatter->formatOrdinal(2));
        $this->assertEquals('3rd', $this->formatter->formatOrdinal(3));
        $this->assertEquals('4th', $this->formatter->formatOrdinal(4));
        $this->assertEquals('11th', $this->formatter->formatOrdinal(11));
        $this->assertEquals('12th', $this->formatter->formatOrdinal(12));
        $this->assertEquals('13th', $this->formatter->formatOrdinal(13));
        $this->assertEquals('21st', $this->formatter->formatOrdinal(21));
        $this->assertEquals('22nd', $this->formatter->formatOrdinal(22));
        $this->assertEquals('23rd', $this->formatter->formatOrdinal(23));
    }

    #[Test]
    public function it_parses_us_formatted_numbers(): void
    {
        /* Arrange */
        $formatted = '1,234.56';

        /* Act */
        $result = $this->formatter->parseNumber($formatted);

        /* Assert */
        $this->assertEquals(1234.56, $result);
    }

    #[Test]
    public function it_parses_european_formatted_numbers(): void
    {
        /* Arrange */
        $formatted = '1.234,56';

        /* Act */
        $result = $this->formatter->parseNumber($formatted);

        /* Assert */
        $this->assertEquals(1234.56, $result);
    }

    #[Test]
    public function it_parses_numbers_without_decimals(): void
    {
        /* Arrange */
        $formatted = '1,234';

        /* Act */
        $result = $this->formatter->parseNumber($formatted);

        /* Assert */
        $this->assertEquals(1234.0, $result);
    }

    #[Test]
    public function it_handles_zero_bytes_file_size(): void
    {
        /* Arrange */
        $bytes = 0;

        /* Act */
        $result = $this->formatter->formatFileSize($bytes);

        /* Assert */
        $this->assertEquals('0.00 B', $result);
    }
}
