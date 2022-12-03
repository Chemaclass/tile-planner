<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Models;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use TilePlanner\TilePlanner\Models\TileCounter;

final class TileCounterTest extends TestCase
{
    protected function setUp(): void
    {
        $reflection = new ReflectionClass(TileCounter::class);
        $reflection->setStaticPropertyValue('numberCounter', 0);
    }

    public function test_counter_is_0_when_not_incremented(): void
    {
        $this->assertEquals(0, TileCounter::current());
    }

    public function test_counter_is_1_after_incrementing_once(): void
    {
        TileCounter::next();

        $this->assertEquals(1, TileCounter::current());
    }

    public function test_counter_is_5_after_incrementing_five_times(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            TileCounter::next();
        }

        $this->assertEquals(5, TileCounter::current());
    }
}