<?php

namespace Tests\Unit;

use App\Enums\ResumeSection;
use App\Rules\PartialDate;
use PHPUnit\Framework\TestCase;

class ResumeRulesTest extends TestCase
{
    public function test_partial_dates_compare_years_as_whole_ranges(): void
    {
        $this->assertTrue(PartialDate::isBefore('2020', '2020-03'));
        $this->assertTrue(PartialDate::isBefore('2020-03', '2020'));
        $this->assertTrue(PartialDate::isBefore('2020-03', '2020-03'));
        $this->assertFalse(PartialDate::isBefore('2021-01', '2020'));
        $this->assertFalse(PartialDate::isBefore('2020-05', '2020-04'));
    }

    public function test_section_order_is_normalised(): void
    {
        $this->assertSame(
            ['skills', 'summary', 'experience', 'internship', 'organization', 'education', 'projects', 'certifications', 'languages'],
            ResumeSection::normalizeOrder(['skills', 'unknown', 'skills', 42]),
        );

        $this->assertSame(ResumeSection::defaultOrder(), ResumeSection::normalizeOrder(null));
    }
}
