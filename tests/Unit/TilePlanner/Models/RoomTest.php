<?php

declare(strict_types=1);

namespace TilePlannerTests\Unit\TilePlanner\Models;

use TilePlanner\TilePlanner\Models\Room;
use PHPUnit\Framework\TestCase;

final class RoomTest extends TestCase
{
    public function test_room_parameters_with_integer(): void
    {
        $room = Room::create(10,20);

        self::assertEquals(10, $room->getWidth());
        self::assertEquals(20, $room->getDepth());
        self::assertEquals(200, $room->getSize());
    }

    public function test_room_size_with_float_value(): void
    {
        $room = Room::create(5, 2.5);

        self::assertEquals(12.5, $room->getSize());
    }

    public function test_room_size_with_float_value_and_rounded_result(): void
    {
        $room = Room::create(3.5, 2.25);

        self::assertEquals(7.88, $room->getSize());
    }
}