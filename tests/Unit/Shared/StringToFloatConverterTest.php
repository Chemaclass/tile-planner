<?php

namespace TilePlannerTests\Unit\Shared;

use TilePlanner\Shared\StringToFloatConverter;
use PHPUnit\Framework\TestCase;

class StringToFloatConverterTest extends TestCase
{
    public function test_string_with_comma_will_be_float(): void
    {
        $converter = new StringToFloatConverter();

        $actual = $converter->toFloat("1,5");

        $this->assertEquals(1.5, $actual);
    }

    public function test_empty_string_will_be_0(): void
    {
        $converter = new StringToFloatConverter();

        $actual = $converter->toFloat("");

        $this->assertEquals(0, $actual);
    }

    public function test_letter_string_will_be_0(): void
    {
        $converter = new StringToFloatConverter();

        $actual = $converter->toFloat("abc");

        $this->assertEquals(0, $actual);
    }
}
