<?php

namespace Tests\Unit\Helpers;

use App\Services\Helpers\DateHelper;
use Carbon\Carbon;
use Tests\TestCase;

class DateHelperTest extends TestCase
{
    protected DateHelper $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new DateHelper();
    }

    public function it_adds_business_days(): void
    {
        // Arrange
        $startDate = Carbon::create(2024, 1, 8); // Monday

        // Act
        $result = $this->helper->addBusinessDays($startDate, 5);

        // Assert
        $this->assertEquals(Carbon::create(2024, 1, 15), $result); // Next Monday (skips weekend)
    }

    public function it_calculates_business_days_between_dates(): void
    {
        // Arrange
        $start = Carbon::create(2024, 1, 8); // Monday
        $end = Carbon::create(2024, 1, 12); // Friday

        // Act
        $days = $this->helper->getBusinessDaysBetween($start, $end);

        // Assert
        $this->assertEquals(5, $days); // Mon-Fri = 5 business days
    }

    public function it_identifies_business_days(): void
    {
        // Arrange
        $monday = Carbon::create(2024, 1, 8);
        $saturday = Carbon::create(2024, 1, 13);
        $sunday = Carbon::create(2024, 1, 14);

        // Act & Assert
        $this->assertTrue($this->helper->isBusinessDay($monday));
        $this->assertFalse($this->helper->isBusinessDay($saturday));
        $this->assertFalse($this->helper->isBusinessDay($sunday));
    }

    public function it_parses_flexible_dates(): void
    {
        // Act & Assert
        $this->assertEquals(
            Carbon::today()->format('Y-m-d'),
            $this->helper->parseFlexibleDate('today')->format('Y-m-d')
        );
        
        $this->assertEquals(
            Carbon::tomorrow()->format('Y-m-d'),
            $this->helper->parseFlexibleDate('tomorrow')->format('Y-m-d')
        );
        
        $this->assertEquals(
            Carbon::yesterday()->format('Y-m-d'),
            $this->helper->parseFlexibleDate('yesterday')->format('Y-m-d')
        );
    }
}
