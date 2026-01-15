<?php

namespace Tests\Unit\Helpers;

use App\Services\Helpers\NumberFormatter;
use Tests\TestCase;

class NumberFormatterTest extends TestCase
{
    protected NumberFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = new NumberFormatter();
    }

    public function it_formats_numbers(): void
    {
        // Act
        $result = $this->formatter->formatNumber(1234.567, 2);

        // Assert
        $this->assertIsString($result);
        $this->assertStringContainsString('1', $result);
        $this->assertStringContainsString('234', $result);
    }

    public function it_formats_percentages(): void
    {
        // Act
        $result = $this->formatter->formatPercentage(25.5, 1);

        // Assert
        $this->assertEquals('25.5%', $result);
    }

    public function it_formats_file_sizes(): void
    {
        // Arrange & Act
        $bytes = $this->formatter->formatFileSize(1024);
        $kilobytes = $this->formatter->formatFileSize(1024 * 1024);
        $megabytes = $this->formatter->formatFileSize(1024 * 1024 * 1024);

        // Assert
        $this->assertStringContainsString('KB', $bytes);
        $this->assertStringContainsString('MB', $kilobytes);
        $this->assertStringContainsString('GB', $megabytes);
    }

    public function it_formats_ordinal_numbers(): void
    {
        // Act & Assert
        $this->assertEquals('1st', $this->formatter->formatOrdinal(1));
        $this->assertEquals('2nd', $this->formatter->formatOrdinal(2));
        $this->assertEquals('3rd', $this->formatter->formatOrdinal(3));
        $this->assertEquals('4th', $this->formatter->formatOrdinal(4));
        $this->assertEquals('11th', $this->formatter->formatOrdinal(11));
        $this->assertEquals('21st', $this->formatter->formatOrdinal(21));
    }

    public function it_parses_formatted_numbers(): void
    {
        // Act & Assert
        $this->assertEquals(1234.56, $this->formatter->parseNumber('1,234.56'));
        $this->assertEquals(1234.56, $this->formatter->parseNumber('1.234,56'));
        $this->assertEquals(1234, $this->formatter->parseNumber('1,234'));
    }
}
